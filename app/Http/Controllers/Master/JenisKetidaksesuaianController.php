<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\JenisKetidaksesuaian;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JenisKetidaksesuaianController extends Controller
{
    public function index(): View
    {
        $jenis = JenisKetidaksesuaian::orderBy('nama')->paginate(15);
        return view('master.jenis-ketidaksesuaian.index', compact('jenis'));
    }

    public function create(): View
    {
        return view('master.jenis-ketidaksesuaian.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $jenis = JenisKetidaksesuaian::create($validated);
        AuditLogService::log('CREATE_JENIS_KETIDAKSESUAIAN', 'JenisKetidaksesuaian', $jenis->id, null, $jenis->toArray());

        return redirect()->route('master.jenis-ketidaksesuaian.index')
            ->with('success', "Jenis '{$jenis->nama}' berhasil ditambahkan.");
    }

    public function edit(JenisKetidaksesuaian $jenisKetidaksesuaian): View
    {
        return view('master.jenis-ketidaksesuaian.edit', compact('jenisKetidaksesuaian'));
    }

    public function update(Request $request, JenisKetidaksesuaian $jenisKetidaksesuaian): RedirectResponse
    {
        $validated = $request->validate([
            'nama'      => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $dataLama = $jenisKetidaksesuaian->toArray();
        $jenisKetidaksesuaian->update($validated);
        AuditLogService::log('UPDATE_JENIS_KETIDAKSESUAIAN', 'JenisKetidaksesuaian', $jenisKetidaksesuaian->id, $dataLama, $jenisKetidaksesuaian->fresh()->toArray());

        return redirect()->route('master.jenis-ketidaksesuaian.index')
            ->with('success', "Jenis ketidaksesuaian berhasil diperbarui.");
    }

    public function destroy(JenisKetidaksesuaian $jenisKetidaksesuaian): RedirectResponse
    {
        if ($jenisKetidaksesuaian->ketidaksesuaians()->exists()) {
            return back()->with('error', "Jenis ini tidak dapat dihapus karena sudah digunakan.");
        }

        try {
            DB::transaction(function () use ($jenisKetidaksesuaian) {
                AuditLogService::log('DELETE_JENIS_KETIDAKSESUAIAN', 'JenisKetidaksesuaian', $jenisKetidaksesuaian->id, $jenisKetidaksesuaian->toArray(), null);
                $jenisKetidaksesuaian->delete();
            });
            return redirect()->route('master.jenis-ketidaksesuaian.index')
                ->with('success', "Jenis ketidaksesuaian berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal menghapus jenis ketidaksesuaian.");
        }
    }
}
