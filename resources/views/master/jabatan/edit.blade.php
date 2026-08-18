@extends('layouts.app')
@section('title', 'Edit Jabatan')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Edit Jabatan: {{ $jabatan->nama }}</h2></div>
    <a href="{{ route('master.jabatan.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:600px;">
    <div class="card-body">
        <form action="{{ route('master.jabatan.update', $jabatan) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Jabatan *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $jabatan->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Level Hierarki *</label>
                <input type="number" name="level" class="form-control @error('level') is-invalid @enderror" value="{{ old('level', $jabatan->level) }}" min="1" max="10" required>
                @error('level')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $jabatan->deskripsi) }}</textarea>
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ $jabatan->is_active ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Jabatan Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.jabatan.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
