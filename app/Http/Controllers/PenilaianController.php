<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\ParameterSop;
use App\Models\Penilaian;
use App\Models\PeriodePenilaian;
use App\Services\AuditLogService;
use App\Services\PenilaianService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    public function __construct(protected PenilaianService $service) {}

    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Penilaian::with(['karyawan.divisi', 'evaluator', 'periode'])
            ->orderByDesc('tanggal_penilaian');

        // Filter berdasarkan role
        if ($user->isSupervisor() && $user->karyawan) {
            $query->whereHas('karyawan', fn($q) => $q->where('atasan_id', $user->karyawan->id));
        } elseif ($user->isManager() && $user->karyawan) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $user->karyawan->divisi_id));
        } elseif ($user->isPelaksana() && $user->karyawan) {
            $query->where('karyawan_id', $user->karyawan->id);
        }

        // Filter opsional
        if ($request->filled('periode_id')) $query->where('periode_id', $request->periode_id);
        if ($request->filled('karyawan_id')) $query->where('karyawan_id', $request->karyawan_id);

        $penilaians = $query->paginate(15)->withQueryString();
        $periodes   = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();

        return view('penilaian.index', compact('penilaians', 'periodes'));
    }

    public function create(Request $request): View
    {
        $user = Auth::user();
        $periodeAktif = PeriodePenilaian::aktif()->first();

        if (!$periodeAktif) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Tidak ada periode penilaian yang aktif saat ini.');
        }

        // Ambil karyawan yang bisa dinilai berdasarkan role
        $karyawansQuery = Karyawan::active()->with(['divisi', 'jabatan']);

        if ($user->isSupervisor() && $user->karyawan) {
            $karyawansQuery->where('atasan_id', $user->karyawan->id);
        } elseif ($user->isManager() && $user->karyawan) {
            $karyawansQuery->where('divisi_id', $user->karyawan->divisi_id);
        }

        // Exclude yang sudah dinilai di periode ini
        $karyawansQuery->whereDoesntHave('penilaians', fn($q) => $q->where('periode_id', $periodeAktif->id));

        $karyawans  = $karyawansQuery->orderBy('nama')->get();
        $parameters = ParameterSop::active()->get()->groupBy('kategori');

        return view('penilaian.create', compact('karyawans', 'parameters', 'periodeAktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $parameterIds = ParameterSop::active()->pluck('id')->toArray();

        $rules = [
            'karyawan_id'       => ['required', 'exists:karyawans,id'],
            'periode_id'        => ['required', 'exists:periode_penilaians,id'],
            'tanggal_penilaian' => ['required', 'date'],
            'catatan'           => ['nullable', 'string'],
        ];

        foreach ($parameterIds as $pid) {
            $rules["nilai_{$pid}"] = ['required', 'integer', 'min:1', 'max:4'];
            $rules["catatan_{$pid}"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        // Cek duplikasi
        $exists = Penilaian::where('karyawan_id', $validated['karyawan_id'])
            ->where('periode_id', $validated['periode_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Karyawan ini sudah memiliki penilaian untuk periode yang dipilih.');
        }

        $parameters = ParameterSop::active()->get();
        $details = $parameters->map(fn($p) => [
            'parameter_id' => $p->id,
            'nilai'        => $validated["nilai_{$p->id}"],
            'catatan'      => $validated["catatan_{$p->id}"] ?? null,
            'bobot'        => $p->bobot,
        ])->toArray();

        $this->service->simpanPenilaian(
            [
                'karyawan_id'       => $validated['karyawan_id'],
                'evaluator_id'      => Auth::id(),
                'periode_id'        => $validated['periode_id'],
                'catatan'           => $validated['catatan'] ?? null,
                'tanggal_penilaian' => $validated['tanggal_penilaian'],
            ],
            $details
        );

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    public function show(Penilaian $penilaian): View
    {
        $user = Auth::user();

        // Jika pelaksana, hanya boleh melihat penilaian milik dirinya sendiri
        if ($user->isPelaksana()) {
            if (!$user->karyawan || $penilaian->karyawan_id !== $user->karyawan->id) {
                abort(403, 'Anda tidak memiliki izin untuk melihat detail penilaian karyawan lain.');
            }
        }

        $penilaian->load(['karyawan.divisi', 'karyawan.jabatan', 'evaluator', 'periode', 'details.parameter']);
        return view('penilaian.show', compact('penilaian'));
    }

    public function edit(Penilaian $penilaian): View
    {
        $penilaian->load(['karyawan', 'periode', 'details.parameter']);
        $parameters = ParameterSop::active()->get()->groupBy('kategori');
        $detailsMap = $penilaian->details->keyBy('parameter_id');

        return view('penilaian.edit', compact('penilaian', 'parameters', 'detailsMap'));
    }

    public function update(Request $request, Penilaian $penilaian): RedirectResponse
    {
        $parameterIds = ParameterSop::active()->pluck('id')->toArray();

        $rules = [
            'tanggal_penilaian' => ['required', 'date'],
            'catatan'           => ['nullable', 'string'],
        ];

        foreach ($parameterIds as $pid) {
            $rules["nilai_{$pid}"] = ['required', 'integer', 'min:1', 'max:4'];
            $rules["catatan_{$pid}"] = ['nullable', 'string'];
        }

        $validated = $request->validate($rules);

        $parameters = ParameterSop::active()->get();
        $details = $parameters->map(fn($p) => [
            'parameter_id' => $p->id,
            'nilai'        => $validated["nilai_{$p->id}"],
            'catatan'      => $validated["catatan_{$p->id}"] ?? null,
            'bobot'        => $p->bobot,
        ])->toArray();

        $this->service->updatePenilaian(
            $penilaian,
            [
                'catatan'           => $validated['catatan'] ?? null,
                'tanggal_penilaian' => $validated['tanggal_penilaian'],
            ],
            $details
        );

        return redirect()->route('penilaian.show', $penilaian)
            ->with('success', 'Penilaian berhasil diperbarui.');
    }

    public function destroy(Penilaian $penilaian): RedirectResponse
    {
        AuditLogService::log('DELETE_PENILAIAN', 'Penilaian', $penilaian->id, $penilaian->toArray(), null);
        $penilaian->delete();

        return redirect()->route('penilaian.index')
            ->with('success', 'Penilaian berhasil dihapus.');
    }
}
