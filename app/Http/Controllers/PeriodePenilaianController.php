<?php

namespace App\Http\Controllers;

use App\Models\PeriodePenilaian;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeriodePenilaianController extends Controller
{
    public function index(): View
    {
        $periodes = PeriodePenilaian::withCount('penilaians')
            ->orderByDesc('tanggal_mulai')
            ->paginate(15);
        return view('periode.index', compact('periodes'));
    }

    public function create(): View
    {
        return view('periode.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'            => ['required', 'string', 'max:100'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['nullable', 'in:draft,aktif'],
            'keterangan'      => ['nullable', 'string'],
        ]);

        // Jika set aktif, nonaktifkan yang lain dulu
        if (($validated['status'] ?? 'draft') === 'aktif') {
            PeriodePenilaian::where('status', 'aktif')->update(['status' => 'ditutup']);
        }

        $validated['status'] = $validated['status'] ?? 'draft';
        $periode = PeriodePenilaian::create($validated);
        AuditLogService::log('CREATE_PERIODE', 'PeriodePenilaian', $periode->id, null, $periode->toArray());

        return redirect()->route('periode.index')
            ->with('success', "Periode '{$periode->nama}' berhasil dibuat.");
    }

    public function edit(PeriodePenilaian $periode): View
    {
        $periode->loadCount('penilaians');
        return view('periode.edit', compact('periode'));
    }

    public function update(Request $request, PeriodePenilaian $periode): RedirectResponse
    {
        $validated = $request->validate([
            'nama'            => ['required', 'string', 'max:100'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['nullable', 'in:draft,aktif,ditutup'],
            'keterangan'      => ['nullable', 'string'],
        ]);

        $dataLama = $periode->toArray();
        $periode->update($validated);
        AuditLogService::log('UPDATE_PERIODE', 'PeriodePenilaian', $periode->id, $dataLama, $periode->fresh()->toArray());

        return redirect()->route('periode.index')
            ->with('success', "Periode berhasil diperbarui.");
    }

    public function updateStatus(Request $request, PeriodePenilaian $periode): RedirectResponse
    {
        $request->validate(['status' => ['required', 'in:draft,aktif,ditutup']]);

        if ($request->status === 'aktif') {
            PeriodePenilaian::where('status', 'aktif')->update(['status' => 'ditutup']);
        }

        $dataLama = $periode->toArray();
        $periode->update(['status' => $request->status]);
        AuditLogService::log('UPDATE_STATUS_PERIODE', 'PeriodePenilaian', $periode->id, $dataLama, $periode->fresh()->toArray());

        return back()->with('success', "Status periode diubah menjadi '{$request->status}'.");
    }

    public function destroy(PeriodePenilaian $periode): RedirectResponse
    {
        if ($periode->penilaians()->exists()) {
            return back()->with('error', "Periode tidak dapat dihapus karena sudah memiliki data penilaian.");
        }

        try {
            DB::transaction(function () use ($periode) {
                AuditLogService::log('DELETE_PERIODE', 'PeriodePenilaian', $periode->id, $periode->toArray(), null);
                $periode->delete();
            });

            return redirect()->route('periode.index')
                ->with('success', "Periode berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', "Periode tidak dapat dihapus karena masih terhubung dengan data lain.");
        }
    }
}
