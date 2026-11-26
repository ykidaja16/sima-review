<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\ParameterSop;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Models\PeriodePenilaian;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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
        $cabangs   = Cabang::active()->orderBy('nama')->get();

        if (!$isSaya) {
            $karyawanQuery = Karyawan::active()
                ->where('is_dinilai', true)
                ->with(['divisi', 'jabatan', 'cabang']);

            if ($user->isSupervisor()) {
                // Supervisor: memonitor bawahan langsung
                $karyawanQuery->where('atasan_id', $user->karyawan?->id);
            } elseif ($user->isManager()) {
                // Manager: memonitor divisi sendiri
                if ($user->karyawan?->divisi_id) {
                    $karyawanQuery->where('divisi_id', $user->karyawan->divisi_id);
                }
            } elseif ($user->isKacab() && $user->karyawan?->cabang_id) {
                // Kepala Cabang: memonitor cabang miliknya
                $karyawanQuery->where('cabang_id', $user->karyawan->cabang_id);
            }

            // Direktur & Super Admin: dapat memfilter cabang manapun
            if (($user->isDirektur() || $user->isSuperAdmin()) && $request->filled('cabang_id')) {
                $karyawanQuery->where('cabang_id', $request->cabang_id);
            }

            $karyawans = $karyawanQuery->orderBy('nama')->get();

            $divisis = Divisi::active()
                ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
                ->orderBy('nama')
                ->get();
        }

        return view('monitoring.individu', compact(
            'karyawan', 'penilaians', 'chartLabels', 'chartData', 'karyawans', 'divisis', 'cabangs', 'periodeAktif', 'isSaya'
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

        $cabangs = Cabang::active()->orderBy('nama')->get();

        // Supervisor hanya lihat bawahannya, manager lihat divisinya, kacab lihat cabangnya
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
        } elseif ($user->isKacab() && $karyawanUser?->cabang_id) {
            // Kepala Cabang: khusus cabang miliknya
            $query = Karyawan::active()
                ->where('is_dinilai', true)
                ->where('cabang_id', $karyawanUser->cabang_id);
        } else {
            // Direktur / Super admin / Mutu: semua yang dinilai
            $query = Karyawan::active()->where('is_dinilai', true);
        }

        // Filter cabang untuk Direktur dan Super Admin
        if (($user->isDirektur() || $user->isSuperAdmin()) && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
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
            'jabatan', 'divisi', 'cabang',
            'penilaians' => fn($q) => $q->when(
                $periodeAktif,
                fn($p) => $p->where('periode_id', $periodeAktif->id)
            ),
        ])->orderBy('nama')->get();

        $divisis = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
            ->get();

        return view('monitoring.tim', compact('karyawans', 'periodes', 'periodeAktif', 'divisis', 'cabangs'));
    }

    /**
     * Siapkan data Monitoring Indikator & Tren Penilaian per Periode
     */
    private function buildIndikatorData(Request $request): array
    {
        /** @var User $user */
        $user         = Auth::user();
        $periodeId    = $request->periode_id;
        $divisiId     = $request->divisi_id;
        $periodes     = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $periodeAktif = $periodeId ? PeriodePenilaian::find($periodeId) : null;
        $cabangs      = Cabang::active()->orderBy('nama')->get();

        // Scope cabang & divisi berdasarkan role
        $cabangFilter = null;
        $divisiFilter = $divisiId ? (int) $divisiId : null;

        if ($user->isKacab() && $user->karyawan?->cabang_id) {
            $cabangFilter = $user->karyawan->cabang_id;
        } elseif (($user->isDirektur() || $user->isSuperAdmin()) && $request->filled('cabang_id')) {
            $cabangFilter = (int) $request->cabang_id;
        }

        if ($user->isManager() && $user->karyawan?->divisi_id) {
            $divisiFilter = $user->karyawan->divisi_id;
        }

        $divisis = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true)->when($cabangFilter, fn($c) => $c->where('cabang_id', $cabangFilter)))
            ->orderBy('nama')
            ->get();

        // 1. Analisis Indikator (urut rata-rata terendah ke tertinggi)
        $parameters = ParameterSop::active()->orderBy('urutan')->get();

        $detailsQuery = PenilaianDetail::with(['penilaian.karyawan'])
            ->whereHas('penilaian', function ($q) use ($periodeAktif, $cabangFilter, $divisiFilter) {
                if ($periodeAktif) {
                    $q->where('periode_id', $periodeAktif->id);
                }
                $q->whereHas('karyawan', function ($k) use ($cabangFilter, $divisiFilter) {
                    $k->where('is_dinilai', true)
                      ->when($cabangFilter, fn($c) => $c->where('cabang_id', $cabangFilter))
                      ->when($divisiFilter, fn($d) => $d->where('divisi_id', $divisiFilter));
                });
            })
            ->get()
            ->groupBy('parameter_id');

        $indikators = $parameters->map(function ($param) use ($detailsQuery) {
            $items = $detailsQuery->get($param->id, collect());
            $count = $items->count();
            $avg   = $count > 0 ? round((float) $items->avg('nilai'), 2) : null;
            $pct   = $avg !== null ? round(($avg / 4) * 100, 1) : null;

            $catatanList = $items
                ->filter(fn($d) => trim((string) $d->catatan) !== '')
                ->map(fn($d) => [
                    'catatan'  => trim((string) $d->catatan),
                    'karyawan' => $d->penilaian?->karyawan?->nama ?? '-',
                    'nilai'    => (int) $d->nilai,
                ])
                ->unique('catatan')
                ->take(5)
                ->values();

            $param->total_evaluasi = $count;
            $param->avg_nilai      = $avg;
            $param->persentase     = $pct;
            $param->min_nilai      = $count > 0 ? (int) $items->min('nilai') : null;
            $param->max_nilai      = $count > 0 ? (int) $items->max('nilai') : null;
            $param->catatan_list   = $catatanList;

            return $param;
        })
        ->sortBy(function ($item) {
            // Indikator yang sudah dievaluasi dengan nilai paling rendah tampil paling atas
            return $item->avg_nilai === null ? 999 : $item->avg_nilai;
        })
        ->values();

        // 2. Grafik Penilaian Per Periode (kronologis)
        $trendPeriodes = PeriodePenilaian::orderBy('tanggal_mulai')
            ->get()
            ->map(function ($periode) use ($cabangFilter, $divisiFilter) {
                $penilaians = Penilaian::where('periode_id', $periode->id)
                    ->whereHas('karyawan', function ($k) use ($cabangFilter, $divisiFilter) {
                        $k->where('is_dinilai', true)
                          ->when($cabangFilter, fn($c) => $c->where('cabang_id', $cabangFilter))
                          ->when($divisiFilter, fn($d) => $d->where('divisi_id', $divisiFilter));
                    })
                    ->get();

                $periode->total_penilaian = $penilaians->count();
                $periode->avg_nilai       = $penilaians->isNotEmpty() ? round((float) $penilaians->avg('nilai_akhir'), 2) : null;
                return $periode;
            })
            ->filter(fn($p) => $p->total_penilaian > 0)
            ->values();

        $chartLabels = $trendPeriodes->pluck('nama');
        $chartData   = $trendPeriodes->pluck('avg_nilai');

        $cabangObj = $cabangFilter ? Cabang::find($cabangFilter) : null;
        $divisiObj = $divisiFilter ? Divisi::find($divisiFilter) : null;

        return compact(
            'indikators', 'trendPeriodes', 'chartLabels', 'chartData',
            'periodes', 'periodeAktif', 'divisis', 'cabangs', 'cabangObj', 'divisiObj'
        );
    }

    /**
     * Menu Monitoring Indikator (Kepala Cabang, Manager, Direktur, Super Admin)
     */
    public function indikator(Request $request): View
    {
        return view('monitoring.indikator', $this->buildIndikatorData($request));
    }

    /**
     * Export PDF Monitoring Indikator
     */
    public function exportIndikatorPdf(Request $request)
    {
        $data = $this->buildIndikatorData($request);
        $pdf = Pdf::loadView('monitoring.indikator-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('monitoring-indikator-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Excel Monitoring Indikator
     */
    public function exportIndikatorExcel(Request $request)
    {
        $data = $this->buildIndikatorData($request);
        return Excel::download(
            new \App\Exports\MonitoringIndikatorExport($data),
            'monitoring-indikator-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Ranking karyawan berdasarkan rata-rata nilai
     */
    public function ranking(Request $request): View
    {
        /** @var User $user */
        $user      = Auth::user();
        $periodeId = $request->periode_id;
        $divisiId  = $request->divisi_id;
        $periodes  = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $divisis   = Divisi::active()
            ->whereHas('karyawans', fn($q) => $q->active()->where('is_dinilai', true))
            ->get();
        $cabangs   = Cabang::active()->orderBy('nama')->get();

        $cabangFilter = null;
        if ($user->isKacab() && $user->karyawan?->cabang_id) {
            $cabangFilter = $user->karyawan->cabang_id;
        } elseif (($user->isDirektur() || $user->isSuperAdmin()) && $request->filled('cabang_id')) {
            $cabangFilter = $request->cabang_id;
        }

        $ranking = Karyawan::active()
            ->where('is_dinilai', true)
            ->whereDoesntHave('user.role', fn($q) => $q->whereIn('slug', ['kacab', 'direktur']))
            ->with(['divisi', 'jabatan', 'cabang'])
            ->withAvg(['penilaians as avg_nilai' => function ($q) use ($periodeId) {
                if ($periodeId) $q->where('periode_id', $periodeId);
            }], 'nilai_akhir')
            ->withCount(['penilaians as total_penilaian' => function ($q) use ($periodeId) {
                if ($periodeId) $q->where('periode_id', $periodeId);
            }])
            ->when($cabangFilter, fn($q) => $q->where('cabang_id', $cabangFilter))
            ->when($divisiId, fn($q) => $q->where('divisi_id', $divisiId))
            ->orderByDesc('avg_nilai')
            ->paginate(20)
            ->withQueryString();

        return view('monitoring.ranking', compact('ranking', 'periodes', 'divisis', 'cabangs'));
    }
}
