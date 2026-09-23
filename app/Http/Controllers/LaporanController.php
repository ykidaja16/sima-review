<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\KategoriNilai;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Cek apakah user berhak melihat semua divisi pada laporan & export.
     * Super Admin, Kacab, dan Role Mutu bisa melihat semua divisi.
     */
    private function canViewAllDivisi(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return $user->hasRole(['super_admin', 'kacab', 'mutu']) || $user->isMutu();
    }

    private function buildQuery(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $userKaryawan = $user?->karyawan;

        $query = Penilaian::with(['karyawan.divisi', 'karyawan.jabatan', 'evaluator', 'periode'])
            ->orderByDesc('nilai_akhir');

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        // Filter divisi
        if ($this->canViewAllDivisi()) {
            if ($request->filled('divisi_id')) {
                $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
            }
        } else {
            // Khusus divisi sendiri
            $divisiId = $userKaryawan?->divisi_id;
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $divisiId));
        }

        if ($request->filled('kategori_id')) {
            $k = KategoriNilai::find($request->kategori_id);
            if ($k) {
                $query->whereBetween('nilai_akhir', [$k->nilai_min, $k->nilai_max]);
            }
        }

        return $query;
    }

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        $userKaryawan = $user?->karyawan;
        $canViewAll = $this->canViewAllDivisi();

        $periodes   = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $kategoris  = KategoriNilai::orderBy('urutan')->get();
        $penilaians = $this->buildQuery($request)->get();

        if ($canViewAll) {
            $divisis = Divisi::active()->orderBy('nama')->get();
        } else {
            $divisis = $userKaryawan && $userKaryawan->divisi_id
                ? Divisi::where('id', $userKaryawan->divisi_id)->get()
                : collect();
        }

        return view('laporan.index', compact('periodes', 'divisis', 'kategoris', 'penilaians', 'canViewAll'));
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

        if ($this->canViewAllDivisi()) {
            $divisi = $request->divisi_id ? Divisi::find($request->divisi_id) : null;
        } else {
            /** @var User $user */
            $user = Auth::user();
            $divisi = $user?->karyawan?->divisi;
        }

        $pdf = Pdf::loadView('laporan.print', compact('penilaians', 'periode', 'divisi'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-penilaian-' . now()->format('Y-m-d') . '.pdf');
    }

    public function print(Request $request): View
    {
        $penilaians = $this->buildQuery($request)->get();
        $periode    = $request->periode_id ? PeriodePenilaian::find($request->periode_id) : null;

        if ($this->canViewAllDivisi()) {
            $divisi = $request->divisi_id ? Divisi::find($request->divisi_id) : null;
        } else {
            /** @var User $user */
            $user = Auth::user();
            $divisi = $user?->karyawan?->divisi;
        }

        return view('laporan.print', compact('penilaians', 'periode', 'divisi'));
    }
}

