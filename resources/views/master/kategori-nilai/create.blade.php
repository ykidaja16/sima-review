@extends('layouts.app')
@section('title', 'Tambah Kategori Nilai')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah Kategori Nilai</h2></div>
    <a href="{{ route('master.kategori-nilai.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:540px;">
    <div class="card-body">
        <form action="{{ route('master.kategori-nilai.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama') }}" placeholder="Contoh: Sangat Baik" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nilai Minimum *</label>
                    <input type="number" name="nilai_min" class="form-control @error('nilai_min') is-invalid @enderror"
                        value="{{ old('nilai_min', 0) }}" min="0" max="100" required>
                    @error('nilai_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Maximum *</label>
                    <input type="number" name="nilai_max" class="form-control @error('nilai_max') is-invalid @enderror"
                        value="{{ old('nilai_max', 100) }}" min="0" max="100" required>
                    @error('nilai_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Warna Badge *</label>
                    <select name="warna" class="form-select @error('warna') is-invalid @enderror" required>
                        <option value="success" {{ old('warna')=='success'?'selected':'' }}>🟢 Hijau (success)</option>
                        <option value="primary" {{ old('warna')=='primary'?'selected':'' }}>🔵 Biru (primary)</option>
                        <option value="warning" {{ old('warna')=='warning'?'selected':'' }}>🟡 Kuning (warning)</option>
                        <option value="danger"  {{ old('warna')=='danger'?'selected':'' }}>🔴 Merah (danger)</option>
                        <option value="info"    {{ old('warna')=='info'?'selected':'' }}>🔵 Cyan (info)</option>
                        <option value="secondary"{{ old('warna')=='secondary'?'selected':'' }}>⚫ Abu (secondary)</option>
                    </select>
                    @error('warna')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 1) }}" min="1">
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.kategori-nilai.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
