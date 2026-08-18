<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JabatanController extends Controller
{
    public function index(): View
    {
        $jabatans = Jabatan::orderBy('level')->orderBy('nama')->paginate(15);
        return view('master.jabatan.index', compact('jabatans'));
    }

    public function create(): View
    {
        return view('master.jabatan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'level'     => ['required', 'integer', 'min:1', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $jabatan = Jabatan::create($validated);
        AuditLogService::log('CREATE_JABATAN', 'Jabatan', $jabatan->id, null, $jabatan->toArray());

        return redirect()->route('master.jabatan.index')
            ->with('success', "Jabatan {$jabatan->nama} berhasil ditambahkan.");
    }

    public function edit(Jabatan $jabatan): View
    {
        return view('master.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'level'     => ['required', 'integer', 'min:1', 'max:10'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $dataLama = $jabatan->toArray();
        $jabatan->update($validated);
        AuditLogService::log('UPDATE_JABATAN', 'Jabatan', $jabatan->id, $dataLama, $jabatan->fresh()->toArray());

        return redirect()->route('master.jabatan.index')
            ->with('success', "Jabatan {$jabatan->nama} berhasil diperbarui.");
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        if ($jabatan->karyawans()->count() > 0) {
            return back()->with('error', "Jabatan {$jabatan->nama} tidak dapat dihapus karena masih digunakan.");
        }

        AuditLogService::log('DELETE_JABATAN', 'Jabatan', $jabatan->id, $jabatan->toArray(), null);
        $jabatan->delete();

        return redirect()->route('master.jabatan.index')
            ->with('success', "Jabatan berhasil dihapus.");
    }
}
