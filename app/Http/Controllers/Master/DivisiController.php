<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Divisi;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DivisiController extends Controller
{
    public function index(): View
    {
        $divisis = Divisi::orderBy('nama')->paginate(15);
        return view('master.divisi.index', compact('divisis'));
    }

    public function create(): View
    {
        return view('master.divisi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'kode'      => ['required', 'string', 'max:20', 'unique:divisis,kode'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $divisi = Divisi::create($validated);

        AuditLogService::log('CREATE_DIVISI', 'Divisi', $divisi->id, null, $divisi->toArray());

        return redirect()->route('master.divisi.index')
            ->with('success', "Divisi {$divisi->nama} berhasil ditambahkan.");
    }

    public function edit(Divisi $divisi): View
    {
        return view('master.divisi.edit', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'kode'      => ['required', 'string', 'max:20', 'unique:divisis,kode,' . $divisi->id],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $dataLama = $divisi->toArray();
        $divisi->update($validated);

        AuditLogService::log('UPDATE_DIVISI', 'Divisi', $divisi->id, $dataLama, $divisi->fresh()->toArray());

        return redirect()->route('master.divisi.index')
            ->with('success', "Divisi {$divisi->nama} berhasil diperbarui.");
    }

    public function destroy(Divisi $divisi): RedirectResponse
    {
        if ($divisi->karyawans()->exists()) {
            return back()->with('error', "Divisi {$divisi->nama} tidak dapat dihapus karena masih memiliki data karyawan.");
        }

        try {
            DB::transaction(function () use ($divisi) {
                AuditLogService::log('DELETE_DIVISI', 'Divisi', $divisi->id, $divisi->toArray(), null);
                $divisi->delete();
            });

            return redirect()->route('master.divisi.index')
                ->with('success', "Divisi berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', "Divisi {$divisi->nama} tidak dapat dihapus karena masih terhubung dengan data lain.");
        }
    }
}
