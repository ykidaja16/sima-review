@extends('layouts.app')
@section('title', 'Edit Karyawan')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Edit Karyawan: {{ $karyawan->nama }}</h2></div>
    <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:700px;">
    <div class="card-body">
        <form action="{{ route('master.karyawan.update', $karyawan) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">NIP <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem;">(Opsional - kosongkan jika bukan karyawan tetap)</span></label>
                <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $karyawan->nip) }}" placeholder="Kosongkan jika bukan karyawan tetap">
                @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $karyawan->nama) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $karyawan->email) }}">
            </div>
            <div class="form-group">
                <label class="form-label">No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $karyawan->no_hp) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Divisi *</label>
                <select name="divisi_id" class="form-select" required>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ old('divisi_id', $karyawan->divisi_id) == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jabatan *</label>
                <select name="jabatan_id" class="form-select" required>
                    @foreach($jabatans as $j)
                    <option value="{{ $j->id }}" {{ old('jabatan_id', $karyawan->jabatan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Cabang</label>
                <select name="cabang_id" class="form-select @error('cabang_id') is-invalid @enderror">
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangs as $c)
                    <option value="{{ $c->id }}" {{ old('cabang_id', $karyawan->cabang_id) == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->kode }})</option>
                    @endforeach
                </select>
                @error('cabang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Atasan Langsung</label>
                <select name="atasan_id" class="form-select">
                    <option value="">-- Tidak ada --</option>
                    @foreach($atasans as $a)
                    <option value="{{ $a->id }}" {{ old('atasan_id', $karyawan->atasan_id) == $a->id ? 'selected' : '' }}>{{ $a->nip }} — {{ $a->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ $karyawan->is_active ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Karyawan Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
