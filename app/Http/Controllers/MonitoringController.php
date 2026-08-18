<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Track record individu karyawan
     */
    public function individu(Request $request): View
    {
        $user = Auth::user();
        $karyawanId = $request->karyawan_id ?? $user->karyawan?->id;

        $karyawan  = null;
        $penilaians = collect();
        $chartLabels = collect();
        $chartData   = collect();

        if ($karyawanId) {
            $karyawan = Karyawan::with(['divisi', 'jabatan', 'atasan'])->find($karyawanId);

            if ($karyawan) {
                // Pelaksana hanya bisa lihat data sendiri
                if ($user->isPelaksana() && $karyawan->id !== $user->karyawan?->id) {
                    abort(403);
                }

                $penilaians = Penilaian::with(['periode', 'evaluator', 'details.parameter'])
                    ->where('karyawan_id', $karyawan->id)
                    ->orderBy('tanggal_penilaian')
                    ->get();

                $chartLabels = $penilaians->pluck('periode.nama');
                $chartData   = $penilaians->pluck('nilai_akhir');
            }
        }

        $karyawans = $user->isPelaksana()
            ? collect() // pelaksana tidak perlu list karyawan
            : Karyawan::active()->with('divisi')->orderBy('nama')->get();

        return view('monitoring.individu', compact('karyawan', 'penilaians', 'chartLabels', 'chartData', 'karyawans'));
    }

    /**
     * Track record tim/divisi
     */
    public function tim(Request $request): View
    {
        $user      = Auth::user();
        $periodeId = $request->periode_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $periodeAktif = $periodeId
            ? PeriodePenilaian::find($periodeId)
            : PeriodePenilaian::aktif()->first();

        // Supervisor hanya lihat bawahannya, manager/superadmin lihat divisinya
        $karyawanUser = $user->karyawan;

        if ($user->isSupervisor() && $karyawanUser) {
            // Supervisor: lihat bawahan langsung
            $query = Karyawan::active()
                ->where('atasan_id', $karyawanUser->id);
        } elseif ($user->isManager() && $karyawanUser) {
            // Manager: lihat divisi sendiri
            $query = Karyawan::active()
                ->where('divisi_id', $karyawanUser->divisi_id);
        } else {
            // Super admin: semua, bisa filter divisi
            $query = Karyawan::active();
            if ($request->filled('divisi_id')) {
                $query->where('divisi_id', $request->divisi_id);
            }
        }

        $karyawans = $query->with([
            'jabatan', 'divisi',
            'penilaians' => fn($q) => $q->when(
                $periodeAktif,
                fn($p) => $p->where('periode_id', $periodeAktif->id)
            )->with('kategori'),
        ])->orderBy('nama')->get();

        $divisis = Divisi::active()->get();

        return view('monitoring.tim', compact('karyawans', 'periodes', 'periodeAktif', 'divisis'));
    }

    /**
     * Perbandingan antar divisi
     */
    public function divisi(Request $request): View
    {
        $periodeId = $request->periode_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $periode   = $periodeId ? PeriodePenilaian::find($periodeId) : PeriodePenilaian::aktif()->first();

        $divisis = Divisi::active()
            ->with(['karyawans' => function ($q) use ($periode) {
                $q->active()
                  ->with(['penilaians' => fn($p) => $p->when($periode, fn($p2) => $p2->where('periode_id', $periode?->id))]);
            }])
            ->get()
            ->map(function ($d) {
                $nilaiAll = $d->karyawans->flatMap(fn($k) => $k->penilaians->pluck('nilai_akhir'));
                $d->avg_nilai    = $nilaiAll->avg() ?? 0;
                $d->total_dinilai = $nilaiAll->count();
                return $d;
            })
            ->sortByDesc('avg_nilai');

        $chartLabels = $divisis->pluck('nama');
        $chartData   = $divisis->pluck('avg_nilai')->map(fn($v) => round($v, 2));

        return view('monitoring.divisi', compact('divisis', 'periodes', 'periode', 'chartLabels', 'chartData'));
    }

    /**
     * Ranking karyawan berdasarkan rata-rata nilai
     */
    public function ranking(Request $request): View
    {
        $periodeId = $request->periode_id;
        $divisiId  = $request->divisi_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $divisis   = Divisi::active()->get();

        $ranking = Karyawan::active()
            ->with(['divisi', 'jabatan'])
            ->withAvg(['penilaians as avg_nilai' => function ($q) use ($periodeId) {
                if ($periodeId) $q->where('periode_id', $periodeId);
            }], 'nilai_akhir')
            ->withCount(['penilaians as total_penilaian' => function ($q) use ($periodeId) {
                if ($periodeId) $q->where('periode_id', $periodeId);
            }])
            ->when($divisiId, fn($q) => $q->where('divisi_id', $divisiId))
            ->orderByDesc('avg_nilai')
            ->paginate(20)
            ->withQueryString();

        return view('monitoring.ranking', compact('ranking', 'periodes', 'divisis'));
    }
}
