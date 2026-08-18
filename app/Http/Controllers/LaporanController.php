<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\KategoriNilai;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = Penilaian::with(['karyawan.divisi', 'karyawan.jabatan', 'evaluator', 'periode'])
            ->orderByDesc('nilai_akhir');

        if ($request->filled('periode_id'))  $query->where('periode_id', $request->periode_id);
        if ($request->filled('divisi_id'))   $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
        if ($request->filled('kategori_id')) {
            $k = KategoriNilai::find($request->kategori_id);
            if ($k) $query->whereBetween('nilai_akhir', [$k->nilai_min, $k->nilai_max]);
        }

        return $query;
    }

    public function index(Request $request): View
    {
        $periodes   = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $divisis    = Divisi::active()->orderBy('nama')->get();
        $kategoris  = KategoriNilai::orderBy('urutan')->get();
        $penilaians = $this->buildQuery($request)->get();

        return view('laporan.index', compact('periodes', 'divisis', 'kategoris', 'penilaians'));
    }

    public function exportExcel(Request $request)
    {
        $penilaians = $this->buildQuery($request)->get();
        return Excel::download(
            new \App\Exports\PenilaianExport($penilaians),
            'laporan-penilaian-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $penilaians = $this->buildQuery($request)->get();
        $periode    = $request->periode_id ? PeriodePenilaian::find($request->periode_id) : null;
        $divisi     = $request->divisi_id  ? Divisi::find($request->divisi_id) : null;

        $pdf = Pdf::loadView('laporan.print', compact('penilaians', 'periode', 'divisi'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penilaian-' . now()->format('Y-m-d') . '.pdf');
    }

    public function print(Request $request): View
    {
        $penilaians = $this->buildQuery($request)->get();
        $periode    = $request->periode_id ? PeriodePenilaian::find($request->periode_id) : null;
        $divisi     = $request->divisi_id  ? Divisi::find($request->divisi_id) : null;

        return view('laporan.print', compact('penilaians', 'periode', 'divisi'));
    }
}
