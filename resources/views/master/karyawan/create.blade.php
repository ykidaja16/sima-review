@extends('layouts.app')
@section('title', 'Tambah Karyawan')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah Karyawan</h2></div>
    <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<form action="{{ route('master.karyawan.store') }}" method="POST">
@csrf
<div class="grid grid-2">
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-person-fill" style="color:#3b82f6;margin-right:6px;"></i>Data Karyawan</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">NIP <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem;">(Opsional - kosongkan jika bukan karyawan tetap)</span></label>
                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip') }}" placeholder="Kosongkan jika bukan karyawan tetap">
                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Divisi *</label>
                <select name="divisi_id" class="form-select @error('divisi_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ old('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
                @error('divisi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Jabatan *</label>
                <select name="jabatan_id" class="form-select @error('jabatan_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($jabatans as $j)
                    <option value="{{ $j->id }}" {{ old('jabatan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama }} (Level {{ $j->level }})</option>
                    @endforeach
                </select>
                @error('jabatan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Cabang</label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangs as $c)
                    <option value="{{ $c->id }}" {{ old('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->kode }})</option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Atasan Langsung</label>
                <select name="atasan_id" class="form-select">
                    <option value="">-- Tidak ada --</option>
                    @foreach($atasans as $a)
                    <option value="{{ $a->id }}" {{ old('atasan_id') == $a->id ? 'selected' : '' }}>{{ $a->nip }} — {{ $a->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="form-label" style="margin:0;">Karyawan Aktif</span>
                </label>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5><i class="bi bi-person-lock" style="color:#059669;margin-right:6px;"></i>Akun Login (Opsional)</h5></div>
        <div class="card-body">
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="buat_akun" id="buatAkunCheck" value="1" {{ old('buat_akun') ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;font-size:0.9rem;">Buat akun login untuk karyawan ini</span>
                </label>
            </div>
            <div id="akunFields" style="display:{{ old('buat_akun') ? 'block' : 'none' }};">
                <div class="form-group">
                    <label class="form-label">Username Login *</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}" placeholder="Contoh: budi.santoso" autocomplete="off">
                    <small style="color:var(--text-muted);font-size:0.75rem;">Huruf, angka, dash (-), underscore (_) saja.</small>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Role *</label>
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        placeholder="Min. 8 karakter">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;">
    <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline">Batal</a>
    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-circle-fill"></i> Simpan Karyawan</button>
</div>
</form>

<script>
    const buatAkunCheck = document.getElementById('buatAkunCheck');
    const akunFields    = document.getElementById('akunFields');

    function toggleAkunFields() {
        akunFields.style.display = buatAkunCheck.checked ? 'block' : 'none';
    }

    buatAkunCheck.addEventListener('change', toggleAkunFields);
    // Inisiasi saat load (jika old value checked)
    toggleAkunFields();
</script>
@endsection
