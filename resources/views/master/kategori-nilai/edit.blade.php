@extends('layouts.app')
@section('title', 'Edit Kategori Nilai')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Edit Kategori: {{ $kategoriNilai->nama }}</h2></div>
    <a href="{{ route('master.kategori-nilai.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:540px;">
    <div class="card-body">
        <form action="{{ route('master.kategori-nilai.update', $kategoriNilai) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $kategoriNilai->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Nilai Minimum *</label>
                    <input type="number" name="nilai_min" class="form-control" value="{{ old('nilai_min', $kategoriNilai->nilai_min) }}" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai Maximum *</label>
                    <input type="number" name="nilai_max" class="form-control" value="{{ old('nilai_max', $kategoriNilai->nilai_max) }}" min="0" max="100" required>
                </div>
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Warna Badge *</label>
                    <select name="warna" class="form-select" required>
                        <option value="success"  {{ old('warna', $kategoriNilai->warna)=='success'  ?'selected':'' }}>🟢 Hijau (success)</option>
                        <option value="primary"  {{ old('warna', $kategoriNilai->warna)=='primary'  ?'selected':'' }}>🔵 Biru (primary)</option>
                        <option value="warning"  {{ old('warna', $kategoriNilai->warna)=='warning'  ?'selected':'' }}>🟡 Kuning (warning)</option>
                        <option value="danger"   {{ old('warna', $kategoriNilai->warna)=='danger'   ?'selected':'' }}>🔴 Merah (danger)</option>
                        <option value="info"     {{ old('warna', $kategoriNilai->warna)=='info'     ?'selected':'' }}>🔵 Cyan (info)</option>
                        <option value="secondary"{{ old('warna', $kategoriNilai->warna)=='secondary'?'selected':'' }}>⚫ Abu (secondary)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', $kategoriNilai->urutan) }}" min="1">
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.kategori-nilai.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
