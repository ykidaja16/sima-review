<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CabangController extends Controller
{
    public function index(): View
    {
        $cabangs = Cabang::withCount('karyawans')->orderBy('nama')->paginate(15);
        return view('master.cabang.index', compact('cabangs'));
    }

    public function create(): View
    {
        return view('master.cabang.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'kode'     => ['required', 'string', 'max:20', 'unique:cabangs,kode'],
            'alamat'   => ['nullable', 'string'],
            'telepon'  => ['nullable', 'string', 'max:20'],
            'is_active'=> ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $cabang = Cabang::create($validated);
        AuditLogService::log('CREATE_CABANG', 'Cabang', $cabang->id, null, $cabang->toArray());

        return redirect()->route('master.cabang.index')
            ->with('success', "Cabang {$cabang->nama} berhasil ditambahkan.");
    }

    public function edit(Cabang $cabang): View
    {
        return view('master.cabang.edit', compact('cabang'));
    }

    public function update(Request $request, Cabang $cabang): RedirectResponse
    {
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'kode'     => ['required', 'string', 'max:20', 'unique:cabangs,kode,' . $cabang->id],
            'alamat'   => ['nullable', 'string'],
            'telepon'  => ['nullable', 'string', 'max:20'],
            'is_active'=> ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $dataLama = $cabang->toArray();
        $cabang->update($validated);
        AuditLogService::log('UPDATE_CABANG', 'Cabang', $cabang->id, $dataLama, $cabang->fresh()->toArray());

        return redirect()->route('master.cabang.index')
            ->with('success', "Cabang {$cabang->nama} berhasil diperbarui.");
    }

    public function destroy(Cabang $cabang): RedirectResponse
    {
        if ($cabang->karyawans()->exists()) {
            return back()->with('error', "Cabang {$cabang->nama} tidak dapat dihapus karena masih memiliki data karyawan.");
        }

        try {
            DB::transaction(function () use ($cabang) {
                AuditLogService::log('DELETE_CABANG', 'Cabang', $cabang->id, $cabang->toArray(), null);
                $cabang->delete();
            });
            return redirect()->route('master.cabang.index')
                ->with('success', "Cabang berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', "Cabang tidak dapat dihapus karena masih terhubung dengan data lain.");
        }
    }
}
