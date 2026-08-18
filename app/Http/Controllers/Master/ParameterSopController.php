<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ParameterSop;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParameterSopController extends Controller
{
    public function index(): View
    {
        $parameters = ParameterSop::orderBy('urutan')->paginate(20);
        $kategoris = ParameterSop::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
        return view('master.parameter-sop.index', compact('parameters', 'kategoris'));
    }

    public function create(): View
    {
        $kategoriList = ParameterSop::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
        return view('master.parameter-sop.create', compact('kategoriList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kategori'  => ['required', 'string', 'max:100'],
            'nama'      => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'urutan'    => ['required', 'integer', 'min:0'],
            'bobot'     => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $parameter = ParameterSop::create($validated);
        AuditLogService::log('CREATE_PARAMETER_SOP', 'ParameterSop', $parameter->id, null, $parameter->toArray());

        return redirect()->route('master.parameter-sop.index')
            ->with('success', "Parameter SOP '{$parameter->nama}' berhasil ditambahkan.");
    }

    public function edit(ParameterSop $parameterSop): View
    {
        $kategoriList = ParameterSop::select('kategori')->distinct()->orderBy('kategori')->pluck('kategori');
        return view('master.parameter-sop.edit', compact('parameterSop', 'kategoriList'));
    }

    public function update(Request $request, ParameterSop $parameterSop): RedirectResponse
    {
        $validated = $request->validate([
            'kategori'  => ['required', 'string', 'max:100'],
            'nama'      => ['required', 'string', 'max:200'],
            'deskripsi' => ['nullable', 'string'],
            'urutan'    => ['required', 'integer', 'min:0'],
            'bobot'     => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $dataLama = $parameterSop->toArray();
        $parameterSop->update($validated);
        AuditLogService::log('UPDATE_PARAMETER_SOP', 'ParameterSop', $parameterSop->id, $dataLama, $parameterSop->fresh()->toArray());

        return redirect()->route('master.parameter-sop.index')
            ->with('success', "Parameter SOP berhasil diperbarui.");
    }

    public function destroy(ParameterSop $parameterSop): RedirectResponse
    {
        if ($parameterSop->penilaianDetails()->count() > 0) {
            return back()->with('error', "Parameter ini tidak dapat dihapus karena sudah digunakan dalam penilaian.");
        }

        AuditLogService::log('DELETE_PARAMETER_SOP', 'ParameterSop', $parameterSop->id, $parameterSop->toArray(), null);
        $parameterSop->delete();

        return redirect()->route('master.parameter-sop.index')
            ->with('success', "Parameter SOP berhasil dihapus.");
    }
}
