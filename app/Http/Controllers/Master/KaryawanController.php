<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Cabang;
use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KaryawanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Karyawan::with(['divisi', 'jabatan', 'user'])
            ->orderBy('nama');

        if ($request->filled('divisi_id')) {
            $query->where('divisi_id', $request->divisi_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $karyawans = $query->paginate(15)->withQueryString();
        $divisis = Divisi::active()->orderBy('nama')->get();

        return view('master.karyawan.index', compact('karyawans', 'divisis'));
    }

    public function create(): View
    {
        $divisis  = Divisi::active()->orderBy('nama')->get();
        $jabatans = Jabatan::active()->orderBy('nama')->get();
        $atasans  = Karyawan::active()->orderBy('nama')->get();
        $cabangs  = Cabang::active()->orderBy('nama')->get();

        $roles = Role::where('slug', '!=', 'super_admin')->orderByDesc('level')->get();
        return view('master.karyawan.create', compact('divisis', 'jabatans', 'atasans', 'roles', 'cabangs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip'        => ['required', 'string', 'max:30', 'unique:karyawans,nip'],
            'nama'       => ['required', 'string', 'max:150'],
            'email'      => ['nullable', 'email', 'max:150', 'unique:karyawans,email'],
            'no_hp'      => ['nullable', 'string', 'max:20', 'unique:karyawans,no_hp'],
            'divisi_id'  => ['required', 'exists:divisis,id'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'cabang_id'  => ['nullable', 'exists:cabangs,id'],
            'atasan_id'  => ['nullable', 'exists:karyawans,id'],
            'is_active'  => ['boolean'],
            // Akun user (opsional)
            'buat_akun'  => ['boolean'],
            'username'   => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:users,username', 'required_if:buat_akun,1'],
            'role_id'    => ['nullable', 'exists:roles,id', 'required_if:buat_akun,1'],
            'password'   => ['nullable', 'min:8', 'required_if:buat_akun,1'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash (-) dan underscore (_).',
            'email.unique'        => 'Email ini sudah digunakan oleh karyawan lain.',
            'no_hp.unique'        => 'Nomor HP ini sudah digunakan oleh karyawan lain.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $userId = null;

            if ($request->boolean('buat_akun')) {
                $user = User::create([
                    'username' => $validated['username'],
                    'name'     => $validated['nama'],
                    'email'    => $validated['email'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'role_id'  => $validated['role_id'],
                    'is_active'=> true,
                ]);
                $userId = $user->id;
            }

            $karyawan = Karyawan::create([
                'user_id'    => $userId,
                'nip'        => $validated['nip'],
                'nama'       => $validated['nama'],
                'email'      => $validated['email'] ?? null,
                'no_hp'      => $validated['no_hp'] ?? null,
                'divisi_id'  => $validated['divisi_id'],
                'jabatan_id' => $validated['jabatan_id'],
                'cabang_id'  => $validated['cabang_id'] ?? null,
                'atasan_id'  => $validated['atasan_id'] ?? null,
                'is_active'  => $request->boolean('is_active', true),
            ]);

            AuditLogService::log('CREATE_KARYAWAN', 'Karyawan', $karyawan->id, null, $karyawan->toArray());
        });

        return redirect()->route('master.karyawan.index')
            ->with('success', "Karyawan {$validated['nama']} berhasil ditambahkan.");
    }

    public function show(Karyawan $karyawan): View
    {
        $karyawan->load(['divisi', 'jabatan', 'atasan', 'user', 'penilaians.periode', 'penilaians.evaluator']);
        return view('master.karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan): View
    {
        $divisis  = Divisi::active()->orderBy('nama')->get();
        $jabatans = Jabatan::active()->orderBy('nama')->get();
        $atasans  = Karyawan::active()->where('id', '!=', $karyawan->id)->orderBy('nama')->get();
        $cabangs  = Cabang::active()->orderBy('nama')->get();

        return view('master.karyawan.edit', compact('karyawan', 'divisis', 'jabatans', 'atasans', 'cabangs'));
    }

    public function update(Request $request, Karyawan $karyawan): RedirectResponse
    {
        $validated = $request->validate([
            'nip'        => ['required', 'string', 'max:30', 'unique:karyawans,nip,' . $karyawan->id],
            'nama'       => ['required', 'string', 'max:150'],
            'email'      => ['nullable', 'email', 'max:150', 'unique:karyawans,email,' . $karyawan->id],
            'no_hp'      => ['nullable', 'string', 'max:20', 'unique:karyawans,no_hp,' . $karyawan->id],
            'divisi_id'  => ['required', 'exists:divisis,id'],
            'jabatan_id' => ['required', 'exists:jabatans,id'],
            'cabang_id'  => ['nullable', 'exists:cabangs,id'],
            'atasan_id'  => ['nullable', 'exists:karyawans,id'],
            'is_active'  => ['boolean'],
        ], [
            'email.unique' => 'Email ini sudah digunakan oleh karyawan lain.',
            'no_hp.unique' => 'Nomor HP ini sudah digunakan oleh karyawan lain.',
        ]);

        $dataLama = $karyawan->toArray();
        $karyawan->update($validated);

        AuditLogService::log('UPDATE_KARYAWAN', 'Karyawan', $karyawan->id, $dataLama, $karyawan->fresh()->toArray());

        return redirect()->route('master.karyawan.index')
            ->with('success', "Data karyawan {$karyawan->nama} berhasil diperbarui.");
    }

    public function destroy(Karyawan $karyawan): RedirectResponse
    {
        if ($karyawan->penilaians()->exists()) {
            return back()->with('error', "Karyawan {$karyawan->nama} tidak dapat dihapus karena memiliki riwayat penilaian.");
        }

        try {
            DB::transaction(function () use ($karyawan) {
                AuditLogService::log('DELETE_KARYAWAN', 'Karyawan', $karyawan->id, $karyawan->toArray(), null);
                $karyawan->delete();
            });

            return redirect()->route('master.karyawan.index')
                ->with('success', "Karyawan berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->with('error', "Karyawan {$karyawan->nama} tidak dapat dihapus karena masih terhubung dengan data lain.");
        }
    }
}
