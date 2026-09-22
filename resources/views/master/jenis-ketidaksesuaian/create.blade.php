@extends('layouts.app')
@section('title', 'Tambah Jenis Ketidaksesuaian')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah Jenis Ketidaksesuaian</h2></div>
    <a href="{{ route('master.jenis-ketidaksesuaian.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:580px;">
    <div class="card-body">
        <form action="{{ route('master.jenis-ketidaksesuaian.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Jenis *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Contoh: Keluhan Pelanggan" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="form-label" style="margin:0;">Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.jenis-ketidaksesuaian.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
