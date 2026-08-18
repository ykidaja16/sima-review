@extends('layouts.app')
@section('title', 'Edit Periode')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Edit Periode: {{ $periode->nama }}</h2></div>
    <a href="{{ route('periode.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form action="{{ route('periode.update', $periode) }}" method="POST">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Periode *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama', $periode->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                        value="{{ old('tanggal_mulai', $periode->tanggal_mulai->format('Y-m-d')) }}" required>
                    @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                        value="{{ old('tanggal_selesai', $periode->tanggal_selesai->format('Y-m-d')) }}" required>
                    @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="draft"   {{ old('status', $periode->status)=='draft'   ? 'selected' : '' }}>Draft</option>
                    <option value="aktif"   {{ old('status', $periode->status)=='aktif'   ? 'selected' : '' }}>Aktif</option>
                    <option value="ditutup" {{ old('status', $periode->status)=='ditutup' ? 'selected' : '' }}>Ditutup</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $periode->keterangan) }}</textarea>
            </div>
            @if($periode->penilaians->count() > 0)
            <div class="alert alert-info" style="margin-bottom:16px;">
                <i class="bi bi-info-circle-fill"></i>
                Periode ini sudah memiliki <strong>{{ $periode->penilaians->count() }} penilaian</strong>. Perubahan tanggal tidak akan mempengaruhi data yang sudah ada.
            </div>
            @endif
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('periode.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update Periode</button>
            </div>
        </form>
    </div>
</div>
@endsection
