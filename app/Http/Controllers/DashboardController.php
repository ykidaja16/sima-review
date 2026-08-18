<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role?->slug) {
            'super_admin' => $this->dashboardSuperAdmin(),
            'manager'     => $this->dashboardManager(),
            'supervisor'  => $this->dashboardSupervisor(),
            'pelaksana'   => $this->dashboardPelaksana(),
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
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $divisiId = $karyawan?->divisi_id;

        $periodeAktif = PeriodePenilaian::aktif()->first();

        $stats = [
            'total_karyawan_divisi' => Karyawan::active()->where('divisi_id', $divisiId)->count(),
            'sudah_dinilai'         => Penilaian::whereHas('karyawan', fn($q) => $q->where('divisi_id', $divisiId))
                ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
                ->count(),
            'belum_dinilai'         => 0,
            'periode_aktif'         => $periodeAktif,
        ];
        $stats['belum_dinilai'] = $stats['total_karyawan_divisi'] - $stats['sudah_dinilai'];

        $karyawans = Karyawan::active()
            ->where('divisi_id', $divisiId)
            ->with(['jabatan', 'penilaians' => fn($q) => $q->when($periodeAktif, fn($p) => $p->where('periode_id', $periodeAktif->id))])
            ->get();

        return view('dashboard.manager', compact('stats', 'karyawans', 'periodeAktif'));
    }

    private function dashboardSupervisor(): View
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $periodeAktif = PeriodePenilaian::aktif()->first();

        // Supervisor menilai bawahans langsung
        $bawahans = $karyawan
            ? Karyawan::active()
                ->where('atasan_id', $karyawan->id)
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
