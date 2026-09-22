@extends('layouts.app')
@section('title', 'Tambah Cabang')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah Cabang</h2></div>
    <a href="{{ route('master.cabang.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:600px;">
    <div class="card-header"><h5><i class="bi bi-building-fill" style="color:#3b82f6;margin-right:6px;"></i>Data Cabang</h5></div>
    <div class="card-body">
        <form action="{{ route('master.cabang.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Cabang *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kode Cabang *</label>
                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode') }}" placeholder="Contoh: KAB, MLG, SBY" required>
                @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" placeholder="Contoh: 0341-123456">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="form-label" style="margin:0;">Cabang Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.cabang.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
