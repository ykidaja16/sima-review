<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\KategoriNilai;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KategoriNilaiController extends Controller
{
    public function index(): View
    {
        $kategoris = KategoriNilai::ordered()->get();
        return view('master.kategori-nilai.index', compact('kategoris'));
    }

    public function create(): View
    {
        return view('master.kategori-nilai.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:50'],
            'nilai_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:nilai_min'],
            'warna'     => ['required', 'string', 'in:success,primary,info,warning,danger,secondary'],
            'urutan'    => ['required', 'integer', 'min:0'],
        ]);

        $kategori = KategoriNilai::create($validated);
        AuditLogService::log('CREATE_KATEGORI_NILAI', 'KategoriNilai', $kategori->id, null, $kategori->toArray());

        return redirect()->route('master.kategori-nilai.index')
            ->with('success', "Kategori nilai '{$kategori->nama}' berhasil ditambahkan.");
    }

    public function edit(KategoriNilai $kategoriNilai): View
    {
        return view('master.kategori-nilai.edit', compact('kategoriNilai'));
    }

    public function update(Request $request, KategoriNilai $kategoriNilai): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:50'],
            'nilai_min' => ['required', 'numeric', 'min:0', 'max:100'],
            'nilai_max' => ['required', 'numeric', 'min:0', 'max:100', 'gte:nilai_min'],
            'warna'     => ['required', 'string', 'in:success,primary,info,warning,danger,secondary'],
            'urutan'    => ['required', 'integer', 'min:0'],
        ]);

        $dataLama = $kategoriNilai->toArray();
        $kategoriNilai->update($validated);
        AuditLogService::log('UPDATE_KATEGORI_NILAI', 'KategoriNilai', $kategoriNilai->id, $dataLama, $kategoriNilai->fresh()->toArray());

        return redirect()->route('master.kategori-nilai.index')
            ->with('success', "Kategori nilai berhasil diperbarui.");
    }

    public function destroy(KategoriNilai $kategoriNilai): RedirectResponse
    {
        AuditLogService::log('DELETE_KATEGORI_NILAI', 'KategoriNilai', $kategoriNilai->id, $kategoriNilai->toArray(), null);
        $kategoriNilai->delete();

        return redirect()->route('master.kategori-nilai.index')
            ->with('success', "Kategori nilai berhasil dihapus.");
    }
}
