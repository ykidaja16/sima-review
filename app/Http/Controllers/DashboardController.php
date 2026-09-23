<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return match ($user->role?->slug) {
            'super_admin' => $this->dashboardSuperAdmin(),
            'kacab'       => $this->dashboardManager(),
            'manager'     => $this->dashboardManager(),
            'supervisor'  => $this->dashboardSupervisor(),
            'pelaksana'   => $this->dashboardPelaksana(),
            'mutu'        => $this->dashboardSuperAdmin(),
            default       => abort(403),
        };
    }

    private function dashboardSuperAdmin(): View
    {
        $stats = [
            'total_karyawan'  => Karyawan::active()->count(),
            'total_divisi'    => Divisi::active()->count(),
            'total_penilaian' => Penilaian::count(),
            'periode_aktif'   => PeriodePenilaian::aktif()->first(),
        ];

        $periodeAktif = PeriodePenilaian::aktif()->first();

        $penilaianPerDivisi = Divisi::active()
            ->withCount(['karyawans as total_penilaian' => function ($q) use ($periodeAktif) {
                $q->whereHas('penilaians', function ($p) use ($periodeAktif) {
                    if ($periodeAktif) $p->where('periode_id', $periodeAktif->id);
                });
            }])
            ->withCount(['karyawans as total_karyawan'])
            ->get();

        $recentPenilaian = Penilaian::with(['karyawan', 'evaluator', 'periode'])
            ->latest()
            ->take(10)
            ->get();

        $ranking = Karyawan::active()
            ->whereDoesntHave('user.role', fn($q) => $q->where('slug', 'kacab'))
            ->with(['divisi', 'jabatan'])
            ->withAvg(['penilaians as avg_nilai' => function ($q) use ($periodeAktif) {
                if ($periodeAktif) $q->where('periode_id', $periodeAktif->id);
            }], 'nilai_akhir')
            ->orderByDesc('avg_nilai')
            ->take(5)
            ->get();

        return view('dashboard.super-admin', compact('stats', 'penilaianPerDivisi', 'recentPenilaian', 'ranking', 'periodeAktif'));
    }

    private function dashboardManager(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $divisiId = $karyawan?->divisi_id;

        $periodeAktif = PeriodePenilaian::aktif()->first();

        // Karyawan divisi selain diri sendiri (karena penilai tidak menilai diri sendiri)
        $karyawanDivisiQuery = Karyawan::active()
            ->where('divisi_id', $divisiId)
            ->when($karyawan, fn($q) => $q->where('id', '!=', $karyawan->id));

        $totalKaryawanDivisi = $karyawanDivisiQuery->count();

        $sudahDinilai = Penilaian::whereHas('karyawan', function ($q) use ($divisiId, $karyawan) {
            $q->where('divisi_id', $divisiId);
            if ($karyawan) {
                $q->where('id', '!=', $karyawan->id);
            }
        })
        ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
        ->count();

        $stats = [
            'total_karyawan_divisi' => $totalKaryawanDivisi,
            'sudah_dinilai'         => $sudahDinilai,
            'belum_dinilai'         => max(0, $totalKaryawanDivisi - $sudahDinilai),
            'periode_aktif'         => $periodeAktif,
        ];

        $karyawans = $karyawan
            ? Karyawan::active()
                ->where('divisi_id', $divisiId)
                ->where('id', '!=', $karyawan->id)
                ->with(['jabatan', 'penilaians' => fn($q) => $q->when($periodeAktif, fn($p) => $p->where('periode_id', $periodeAktif->id))])
                ->get()
            : collect();

        return view('dashboard.manager', compact('stats', 'karyawans', 'periodeAktif'));
    }

    private function dashboardSupervisor(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $periodeAktif = PeriodePenilaian::aktif()->first();

        // Supervisor memonitor bawahan langsung (tidak termasuk diri sendiri)
        $bawahans = $karyawan
            ? Karyawan::active()
                ->where('atasan_id', $karyawan->id)
                ->where('id', '!=', $karyawan->id)
                ->with(['jabatan', 'penilaians' => fn($q) => $q->when($periodeAktif, fn($p) => $p->where('periode_id', $periodeAktif->id))])
                ->get()
            : collect();

        $stats = [
            'total_bawahan' => $bawahans->count(),
            'sudah_dinilai' => $bawahans->filter(fn($k) => $k->penilaians->isNotEmpty())->count(),
            'periode_aktif' => $periodeAktif,
        ];

        return view('dashboard.supervisor', compact('stats', 'bawahans', 'periodeAktif'));
    }

    private function dashboardPelaksana(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $riwayatPenilaian = $karyawan
            ? Penilaian::with(['periode', 'evaluator', 'details.parameter'])
                ->where('karyawan_id', $karyawan->id)
                ->latest('tanggal_penilaian')
                ->take(6)
                ->get()
            : collect();

        $rataRata = $riwayatPenilaian->avg('nilai_akhir');
        $periodeAktif = PeriodePenilaian::aktif()->first();
        $sudahDinilai = $karyawan && $periodeAktif
            ? Penilaian::where('karyawan_id', $karyawan->id)->where('periode_id', $periodeAktif->id)->exists()
            : false;

        return view('dashboard.pelaksana', compact('karyawan', 'riwayatPenilaian', 'rataRata', 'periodeAktif', 'sudahDinilai'));
    }
}
