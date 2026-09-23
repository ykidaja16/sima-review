@extends('layouts.app')
@section('title', 'Edit User')
@section('breadcrumb')<span style="color:var(--text-muted);">User Management</span> / <strong>Edit: {{ $user->username }}</strong>@endsection

@section('content')
<div class="page-header">
    <div class="page-title"><h2>Edit User: <strong>{{ $user->username }}</strong></h2></div>
    <a href="{{ route('user-management.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form action="{{ route('user-management.update', $user) }}" method="POST">
            @csrf @method('PUT')

            {{-- Karyawan yang terhubung --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-person-badge" style="color:#3b82f6;"></i>
                    Tautkan ke Karyawan
                </label>
                @if($user->karyawan)
                <div style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);border-radius:8px;padding:10px 14px;margin-bottom:10px;font-size:0.875rem;">
                    <i class="bi bi-check-circle-fill" style="color:#059669;"></i>
                    Saat ini terhubung ke: <strong>{{ $user->karyawan->nama }}</strong>
                    {{ $user->karyawan->nip ? '(' . $user->karyawan->nip . ')' : '' }}
                </div>
                @endif
                <select name="karyawan_id" class="form-select">
                    <option value="">-- Tidak ditautkan --</option>
                    @foreach($karyawansTanpaAkun as $k)
                    <option value="{{ $k->id }}"
                        {{ old('karyawan_id', $user->karyawan?->id) == $k->id ? 'selected' : '' }}>
                        {{ $k->nip ? ($k->nip . ' — ') : '' }}{{ $k->nama }} ({{ $k->jabatan->nama ?? '-' }} / {{ $k->divisi->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username', $user->username) }}" required>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ old('role_id', $user->role_id) == $r->id ? 'selected' : '' }}>
                        {{ $r->nama }} — {{ $r->deskripsi }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Akun Aktif</span>
                </label>
            </div>
            <div style="background:var(--bg);border-radius:8px;padding:12px 14px;margin-bottom:16px;">
                <div style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-bottom:10px;">
                    <i class="bi bi-lock"></i> Ganti Password (kosongkan jika tidak ingin mengubah)
                </div>
                <div class="grid grid-2">
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                            placeholder="Password baru">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi baru">
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('user-management.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
