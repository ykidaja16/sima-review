<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Divisi;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $query = Penilaian::with(['karyawan.divisi', 'evaluator', 'periode'])
            ->orderByDesc('tanggal_penilaian');

        // Filter berdasarkan role
        if ($user->isSupervisor() && $user->karyawan) {
            // Supervisor melihat penilaian yang dibuat oleh dirinya
            $query->where('evaluator_id', $user->id);
        } elseif ($user->isManager() && $user->karyawan) {
            // Manager melihat penilaian yang dibuat oleh dirinya serta penilaian anggota divisinya
            $query->where(function ($q) use ($user) {
                $q->where('evaluator_id', $user->id)
                    ->orWhereHas('karyawan', fn($k) => $k->where('divisi_id', $user->karyawan->divisi_id));
            });
        } elseif ($user->isPelaksana() && $user->karyawan) {
            $query->where('karyawan_id', $user->karyawan->id);
        } elseif ($user->isKacab() && $user->karyawan?->cabang_id) {
            // Kacab: hanya melihat penilaian di cabangnya
            $query->whereHas('karyawan', fn($k) => $k->where('cabang_id', $user->karyawan->cabang_id));
        }

        // Filter opsional cabang (untuk Direktur dan Super Admin)
        if (($user->isDirektur() || $user->isSuperAdmin()) && $request->filled('cabang_id')) {
            $query->whereHas('karyawan', fn($k) => $k->where('cabang_id', $request->cabang_id));
        }

        // Filter opsional
        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }
        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }
        if ($request->filled('divisi_id')) {
            $query->whereHas('karyawan', fn($q) => $q->where('divisi_id', $request->divisi_id));
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $penilaians = $query->paginate(15)->withQueryString();
        $periodes   = PeriodePenilaian::orderByDesc('tanggal_mulai')->get();
        $divisis    = Divisi::active()->orderBy('nama')->get();
        $cabangs    = Cabang::active()->orderBy('nama')->get();

        return view('penilaian.index', compact('penilaians', 'periodes', 'divisis', 'cabangs'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $periodeAktif = PeriodePenilaian::aktif()->first();

        if (!$periodeAktif) {
            return redirect()->route('penilaian.index')
                ->with('error', 'Tidak ada periode penilaian yang aktif saat ini.');
        }

        // Ambil karyawan yang bisa dinilai (hanya yang diflag wajib dinilai)
        $karyawansQuery = Karyawan::active()
            ->where('is_dinilai', true)
            ->whereDoesntHave('user', fn($u) => $u->where('is_dinilai', false))
            ->with(['divisi', 'jabatan']);
        $userKaryawan = $user->karyawan;

        // Role Kacab dan Direktur tidak perlu dinilai oleh siapapun
        $karyawansQuery->whereDoesntHave('user.role', fn($q) => $q->whereIn('slug', ['kacab', 'direktur']));

        // 1. Penilai tidak menilai dirinya sendiri
        if ($userKaryawan) {
            $karyawansQuery->where('id', '!=', $userKaryawan->id);
        }

        // 2. Jika penilai adalah Supervisor atau Manager:
        //    - Harus menilai karyawan dari divisi lain (bukan dari divisinya sendiri)
        //    - Minimal penilai adalah level atasnya (level penilai > level yang dinilai)
        if (($user->isSupervisor() || $user->isManager()) && $userKaryawan) {
            $penilaiLevel = $userKaryawan->jabatan?->level ?? 0;

            $karyawansQuery->where('divisi_id', '!=', $userKaryawan->divisi_id)
                ->whereHas('jabatan', fn($q) => $q->where('level', '<', $penilaiLevel));
        } elseif (($user->isKacab() || $user->isDirektur()) && $userKaryawan) {
            // Kepala Cabang / Direktur: menilai karyawan di bawah level jabatannya
            $penilaiLevel = $userKaryawan->jabatan?->level ?? 0;
            if ($penilaiLevel > 0) {
                $karyawansQuery->whereHas('jabatan', fn($q) => $q->where('level', '<', $penilaiLevel));
            }
        }

        // Exclude yang sudah dinilai di periode ini
        $karyawansQuery->whereDoesntHave('penilaians', fn($q) => $q->where('periode_id', $periodeAktif->id));

        $karyawans  = $karyawansQuery->orderBy('nama')->get();
        $divisis    = Divisi::active()->orderBy('nama')->get();
        $parameters = ParameterSop::active()->get()->groupBy('kategori');

        return view('penilaian.create', compact('karyawans', 'divisis', 'parameters', 'periodeAktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userKaryawan = $user->karyawan;
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

        // Validasi: tidak boleh menilai diri sendiri
        if ($userKaryawan && $validated['karyawan_id'] == $userKaryawan->id) {
            return back()->with('error', 'Penilai tidak dapat menilai dirinya sendiri.');
        }

        // Validasi: Kepala Cabang (Kacab) tidak dapat dinilai oleh siapapun
        $targetKaryawan = Karyawan::with(['jabatan', 'user.role'])->find($validated['karyawan_id']);
        if ($targetKaryawan?->user?->role?->slug === 'kacab') {
            return back()->with('error', 'Kepala Cabang tidak dapat dinilai oleh siapapun.');
        }

        // Validasi: hanya karyawan yang masuk penilaian (flag is_dinilai = true) yang dapat dinilai
        if (!$targetKaryawan?->is_dinilai || ($targetKaryawan?->user && !$targetKaryawan->user->is_dinilai)) {
            return back()->with('error', 'Karyawan ini tidak termasuk dalam daftar karyawan yang dinilai.');
        }

        // Validasi aturan lintas divisi dan level atas untuk Supervisor & Manager
        if (($user->isSupervisor() || $user->isManager()) && $userKaryawan) {
            $targetKaryawan = Karyawan::with('jabatan')->find($validated['karyawan_id']);
            $penilaiLevel = $userKaryawan->jabatan?->level ?? 0;
            $targetLevel = $targetKaryawan?->jabatan?->level ?? 0;

            if ($targetKaryawan && $targetKaryawan->divisi_id == $userKaryawan->divisi_id) {
                return back()->with('error', 'Penilaian harus dilakukan terhadap karyawan dari divisi lain.');
            }

            if ($targetLevel >= $penilaiLevel) {
                return back()->with('error', 'Penilai harus memiliki level jabatan di atas karyawan yang dinilai.');
            }
        }

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
        /** @var \App\Models\User $user */
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

    public function tanggapan(Request $request, Penilaian $penilaian): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->karyawan || $penilaian->karyawan_id !== $user->karyawan->id) {
            return back()->with('error', 'Hanya karyawan yang dinilai yang dapat memberikan tanggapan.');
        }

        $validated = $request->validate([
            'tanggapan' => ['required', 'string', 'max:2000'],
        ], [
            'tanggapan.required' => 'Tanggapan tidak boleh kosong.',
            'tanggapan.max'      => 'Tanggapan maksimal 2000 karakter.',
        ]);

        $penilaian->update([
            'tanggapan'         => $validated['tanggapan'],
            'tanggal_tanggapan' => now(),
        ]);

        AuditLogService::log('TANGGAPAN_PENILAIAN', 'Penilaian', $penilaian->id, null, [
            'tanggapan' => $validated['tanggapan'],
        ]);

        return redirect()->route('penilaian.show', $penilaian)
            ->with('success', 'Tanggapan Anda berhasil disimpan.');
    }
}
