<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with(['role', 'karyawan.divisi', 'karyawan.jabatan'])
            ->orderBy('name')
            ->paginate(15);

        return view('user-management.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderByDesc('level')->get();
        // Karyawan yang belum punya akun user
        $karyawansTanpaAkun = Karyawan::active()
            ->whereNull('user_id')
            ->orderBy('nama')
            ->get();

        return view('user-management.create', compact('roles', 'karyawansTanpaAkun'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username'    => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'name'        => ['required', 'string', 'max:150'],
            'email'       => ['nullable', 'email', 'unique:users,email'],
            'role_id'     => ['required', 'exists:roles,id'],
            'is_active'   => ['boolean'],
            'karyawan_id' => ['nullable', 'exists:karyawans,id'],
            'password'    => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash dan underscore.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'username'  => $validated['username'],
                'name'      => $validated['name'],
                'email'     => $validated['email'] ?? null,
                'role_id'   => $validated['role_id'],
                'is_active' => $request->boolean('is_active', true),
                'password'  => Hash::make($validated['password']),
            ]);

            // Link ke karyawan jika dipilih
            if (!empty($validated['karyawan_id'])) {
                Karyawan::where('id', $validated['karyawan_id'])
                    ->update(['user_id' => $user->id]);
            }

            AuditLogService::log('CREATE_USER', 'User', $user->id, null, [
                'username'    => $user->username,
                'name'        => $user->name,
                'role'        => $user->role?->slug,
                'karyawan_id' => $validated['karyawan_id'] ?? null,
            ]);
        });

        return redirect()->route('user-management.index')
            ->with('success', "User '{$validated['username']}' berhasil dibuat.");
    }

    public function edit(User $user): View
    {
        $roles = Role::orderByDesc('level')->get();
        $karyawansTanpaAkun = Karyawan::active()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')
                  ->orWhere('user_id', $user->id); // termasuk yang sudah linked ke user ini
            })
            ->orderBy('nama')
            ->get();

        return view('user-management.edit', compact('user', 'roles', 'karyawansTanpaAkun'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'username'    => ['required', 'string', 'max:50', 'unique:users,username,' . $user->id, 'alpha_dash'],
            'name'        => ['required', 'string', 'max:150'],
            'email'       => ['nullable', 'email', 'unique:users,email,' . $user->id],
            'role_id'     => ['required', 'exists:roles,id'],
            'is_active'   => ['boolean'],
            'karyawan_id' => ['nullable', 'exists:karyawans,id'],
            'password'    => ['nullable', Password::min(8)->letters()->numbers(), 'confirmed'],
        ]);

        $dataLama = ['username' => $user->username, 'name' => $user->name, 'role' => $user->role?->slug];

        DB::transaction(function () use ($validated, $request, $user) {
            // Unlink karyawan lama jika ada
            Karyawan::where('user_id', $user->id)->update(['user_id' => null]);

            $user->username  = $validated['username'];
            $user->name      = $validated['name'];
            $user->email     = $validated['email'] ?? null;
            $user->role_id   = $validated['role_id'];
            $user->is_active = $request->boolean('is_active');

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            // Link ke karyawan baru
            if (!empty($validated['karyawan_id'])) {
                Karyawan::where('id', $validated['karyawan_id'])
                    ->update(['user_id' => $user->id]);
            }
        });

        AuditLogService::log('UPDATE_USER', 'User', $user->id, $dataLama, [
            'username' => $user->username,
            'name'     => $user->name,
            'role'     => $user->role?->slug,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', "User '{$user->username}' berhasil diperbarui.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        // Unlink karyawan
        Karyawan::where('user_id', $user->id)->update(['user_id' => null]);

        AuditLogService::log('DELETE_USER', 'User', $user->id, [
            'username' => $user->username,
            'name'     => $user->name,
        ], null);

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
