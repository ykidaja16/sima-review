<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan form update data diri dan ganti password.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $user->load(['role', 'karyawan.divisi', 'karyawan.jabatan', 'karyawan.cabang']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Simpan perubahan data diri dan password pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $rules = [
            'name'  => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];

        // Jika user mengisi password baru, maka password saat ini wajib diisi dan diverifikasi
        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'string'];
            $rules['password']         = ['required', 'string', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules, [
            'name.required'             => 'Nama lengkap wajib diisi.',
            'email.email'               => 'Format email tidak valid.',
            'email.unique'              => 'Email ini sudah digunakan oleh akun lain.',
            'current_password.required' => 'Kata sandi saat ini wajib diisi untuk mengganti kata sandi.',
            'password.min'              => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Verifikasi kata sandi saat ini jika user ingin mengganti password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()
                    ->withInput($request->except(['current_password', 'password', 'password_confirmation']))
                    ->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
            }
        }

        $dataLamaUser = $user->only(['name', 'email']);

        // Update User
        $user->name  = $validated['name'];
        $user->email = $validated['email'] ?? null;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update Data Karyawan jika terhubung
        if ($user->karyawan) {
            $karyawan = $user->karyawan;
            $karyawan->nama  = $validated['name'];
            $karyawan->email = $validated['email'] ?? null;
            $karyawan->no_hp = $validated['no_hp'] ?? null;
            $karyawan->save();
        }

        AuditLogService::log(
            'UPDATE_PROFILE',
            'User',
            $user->id,
            $dataLamaUser,
            $user->only(['name', 'email'])
        );

        $pesan = $request->filled('password')
            ? 'Data diri dan kata sandi akun Anda berhasil diperbarui.'
            : 'Data diri Anda berhasil diperbarui.';

        return redirect()->route('profile.edit')->with('success', $pesan);
    }
}
