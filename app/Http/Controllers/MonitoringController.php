<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\User;
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
        /** @var User $user */
        $user = Auth::user();
        $isSaya = $user->isPelaksana() || $request->boolean('saya');

        $karyawanId = $isSaya
            ? $user->karyawan?->id
            : ($request->karyawan_id ?: null);

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

        $periodeAktif = PeriodePenilaian::aktif()->first();

        // Karyawan list (hanya yang dinilai)
        $karyawans = collect();
        $divisis   = collect();

        if (!$isSaya) {
            $karyawanQuery = Karyawan::active()
                ->where('is_dinilai', true)
                ->with(['divisi', 'jabatan']);

            if ($user->isSupervisor()) {
                // Supervisor: memonitor bawahan langsung
                $karyawanQuery->where('atasan_id', $user->karyawan?->id);
            } elseif ($user->isManager()) {
                // Manager: memonitor divisi sendiri
                if ($user->karyawan?->divisi_id) {
                    $karyawanQuery->where('divisi_id', $user->karyawan->divisi_id);
                }
            }

            $karyawans = $karyawanQuery->orderBy('nama')->get();

            $divisis = Divisi::active()
                ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
                ->orderBy('nama')
                ->get();
        }

        return view('monitoring.individu', compact(
            'karyawan', 'penilaians', 'chartLabels', 'chartData', 'karyawans', 'divisis', 'periodeAktif', 'isSaya'
        ));
    }

    /**
     * Track record tim/divisi
     */
    public function tim(Request $request): View
    {
        /** @var User $user */
        $user      = Auth::user();
        $periodeId = $request->periode_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $periodeAktif = $periodeId
            ? PeriodePenilaian::find($periodeId)
            : PeriodePenilaian::aktif()->first();

        // Supervisor hanya lihat bawahannya, manager/superadmin lihat divisinya
        $karyawanUser = $user->karyawan;

        if ($user->isSupervisor() && $karyawanUser) {
            // Supervisor: lihat bawahan langsung yang dinilai
            $query = Karyawan::active()
                ->where('is_dinilai', true)
                ->where('atasan_id', $karyawanUser->id);
        } elseif ($user->isManager() && $karyawanUser) {
            // Manager: lihat divisi sendiri yang dinilai
            $query = Karyawan::active()
                ->where('is_dinilai', true)
                ->where('divisi_id', $karyawanUser->divisi_id);
        } else {
            // Super admin / Kacab / Mutu: semua yang dinilai
            $query = Karyawan::active()->where('is_dinilai', true);
        }

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $karyawans = $query->with([
            'jabatan', 'divisi',
            'penilaians' => fn($q) => $q->when(
                $periodeAktif,
                fn($p) => $p->where('periode_id', $periodeAktif->id)
            ),
        ])->orderBy('nama')->get();

        $divisis = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
            ->get();

        return view('monitoring.tim', compact('karyawans', 'periodes', 'periodeAktif', 'divisis'));
    }

    /**
     * Perbandingan antar divisi
     */
    public function divisi(Request $request): View
    {
        $periodeId = $request->periode_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $periodeAktif = $periodeId ? PeriodePenilaian::find($periodeId) : PeriodePenilaian::aktif()->first();

        $divisiStats = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
            ->with(['karyawans' => function ($q) use ($periodeAktif) {
                $q->active()
                  ->where('is_dinilai', true)
                  ->with(['penilaians' => fn($p) => $p->when($periodeAktif, fn($p2) => $p2->where('periode_id', $periodeAktif?->id))]);
            }])
            ->get()
            ->map(function ($d) {
                $karyawans = $d->karyawans;
                $d->total_karyawan = $karyawans->count();
                $penilaians = $karyawans->flatMap(fn($k) => $k->penilaians);
                $d->sudah_dinilai  = $penilaians->count();
                $d->avg_nilai      = $penilaians->isNotEmpty() ? $penilaians->avg('nilai_akhir') : 0;
                $d->max_nilai      = $penilaians->isNotEmpty() ? $penilaians->max('nilai_akhir') : null;
                $d->min_nilai      = $penilaians->isNotEmpty() ? $penilaians->min('nilai_akhir') : null;
                return $d;
            })
            ->sortByDesc('avg_nilai')
            ->values();

        $chartLabels = $divisiStats->pluck('nama');
        $chartData   = $divisiStats->pluck('avg_nilai')->map(fn($v) => round($v, 2));

        return view('monitoring.divisi', compact('divisiStats', 'periodes', 'periodeAktif', 'chartLabels', 'chartData'));
    }

    /**
     * Ranking karyawan berdasarkan rata-rata nilai
     */
    public function ranking(Request $request): View
    {
        $periodeId = $request->periode_id;
        $divisiId  = $request->divisi_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $divisis   = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
            ->get();

        $ranking = Karyawan::active()
            ->where('is_dinilai', true)
            ->whereDoesntHave('user.role', fn($q) => $q->where('slug', 'kacab'))
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
