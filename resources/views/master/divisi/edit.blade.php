@extends('layouts.app')
@section('title', 'Edit Divisi')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Edit Divisi: {{ $divisi->nama }}</h2></div>
    <a href="{{ route('master.divisi.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('master.divisi.update', $divisi) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Divisi *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $divisi->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kode Divisi *</label>
                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode', $divisi->kode) }}" required style="text-transform:uppercase;">
                @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $divisi->deskripsi) }}</textarea>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $divisi->is_active) ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Divisi Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.divisi.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
