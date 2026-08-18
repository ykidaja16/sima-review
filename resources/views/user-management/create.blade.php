@extends('layouts.app')
@section('title', 'Tambah User')
@section('breadcrumb')<span style="color:var(--text-muted);">User Management</span> / <strong>Tambah User</strong>@endsection

@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah User</h2></div>
    <a href="{{ route('user-management.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form action="{{ route('user-management.store') }}" method="POST">
            @csrf

            {{-- Link ke Karyawan --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="bi bi-person-badge" style="color:#3b82f6;"></i>
                    Tautkan ke Karyawan
                    <small style="font-weight:400;text-transform:none;font-size:0.75rem;color:var(--text-muted);">(1 User = 1 Karyawan)</small>
                </label>
                <select name="karyawan_id" class="form-select">
                    <option value="">-- Tidak ditautkan ke karyawan --</option>
                    @foreach($karyawansTanpaAkun as $k)
                    <option value="{{ $k->id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nip }} — {{ $k->nama }} ({{ $k->jabatan->nama ?? '-' }} / {{ $k->divisi->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
                <small style="color:var(--text-muted);font-size:0.75rem;">Pilih karyawan agar user terkait dengan data karyawan (bisa dikosongkan untuk user admin).</small>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Username * <small style="font-weight:400;text-transform:none;">(untuk login)</small></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}" placeholder="Contoh: budi.santoso" autocomplete="off">
                    <small style="color:var(--text-muted);font-size:0.75rem;">Huruf, angka, dash (-), underscore (_).</small>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email <small style="font-weight:400;text-transform:none;">(opsional)</small></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Role *</label>
                <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->nama }} <span style="color:var(--text-muted);">— {{ $r->deskripsi }}</span>
                    </option>
                    @endforeach
                </select>
                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Akun Aktif</span>
                </label>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Min. 8 karakter, huruf + angka">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password">
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">
                <a href="{{ route('user-management.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection
