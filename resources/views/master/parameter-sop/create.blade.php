@extends('layouts.app')
@section('title', 'Tambah Parameter SOP')
@section('content')
<div class="page-header">
    <div class="page-title"><h2>Tambah Parameter SOP</h2></div>
    <a href="{{ route('master.parameter-sop.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>
<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form action="{{ route('master.parameter-sop.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Parameter *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Kategori *</label>
                <input type="text" name="kategori" class="form-control @error('kategori') is-invalid @enderror"
                    value="{{ old('kategori') }}" list="kategoriList" placeholder="Contoh: Penampilan & Sikap" required>
                <datalist id="kategoriList">
                    @foreach($kategoriList as $kat)
                    <option value="{{ $kat }}">
                    @endforeach
                </datalist>
                @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Bobot * (1-10)</label>
                    <input type="number" name="bobot" class="form-control @error('bobot') is-invalid @enderror"
                        value="{{ old('bobot', 3) }}" min="1" max="10" required>
                    @error('bobot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="urutan" class="form-control" value="{{ old('urutan', 10) }}" min="1">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi / Aspek Penilaian</label>
                <textarea name="deskripsi" class="form-control" rows="2" placeholder="Penjelasan singkat parameter ini">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Metode Penilaian <span style="font-weight:400;color:var(--text-muted);">(Bisa pilih lebih dari 1)</span></label>
                @php
                    $oldMetode = (array) old('metode_penilaian', []);
                    $opsiMetode = $metodeOptions ?? \App\Models\ParameterSop::METODE_OPTIONS;
                @endphp
                <div style="display:flex;gap:18px;flex-wrap:wrap;padding:10px 14px;background:#f8fafc;border:1.5px solid var(--border);border-radius:var(--radius-sm);">
                    @foreach($opsiMetode as $metode)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:0.88rem;color:var(--text);">
                        <input type="checkbox" name="metode_penilaian[]" value="{{ $metode }}"
                            {{ in_array($metode, $oldMetode, true) ? 'checked' : '' }}
                            style="width:16px;height:16px;cursor:pointer;">
                        <span>{{ $metode }}</span>
                    </label>
                    @endforeach
                </div>
                @error('metode_penilaian')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="form-label" style="margin:0;">Parameter Aktif</span>
                </label>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('master.parameter-sop.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
