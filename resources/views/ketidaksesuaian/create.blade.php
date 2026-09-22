@extends('layouts.app')
@section('title', 'Buat FTKP Baru')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Buat FTKP Baru</h2>
        <p>Formulir Tindakan Korektif dan Perbaikan — Bagian 1: Penggagas</p>
    </div>
    <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

@if(!$karyawan)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Data karyawan Anda tidak ditemukan. Hubungi administrator.
</div>
@else
<form action="{{ route('ketidaksesuaian.store') }}" method="POST">
@csrf
<div class="grid grid-2" style="align-items:start;">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-person-fill" style="color:#3b82f6;margin-right:6px;"></i>BAGIAN 1 – PENGGAGAS</h5>
        </div>
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penggagas</label>
                    <input type="text" class="form-control" value="{{ $karyawan->nama }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Departemen/Bagian</label>
                    <input type="text" class="form-control" value="{{ $karyawan->divisi->nama ?? '-' }}" disabled>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Jenis Ketidaksesuaian *</label>
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                    @foreach($jenisList as $j)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="radio" name="jenis_id" value="{{ $j->id }}" {{ old('jenis_id') == $j->id ? 'checked' : '' }} id="jenis_{{ $j->id }}">
                        <span>{{ $j->nama }}</span>
                    </label>
                    @endforeach
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="radio" name="jenis_id" value="" id="jenis_lainnya_radio" {{ !old('jenis_id') && old('jenis_lainnya') ? 'checked' : '' }}>
                        <span>Lain-lain, sebutkan:</span>
                        <input type="text" name="jenis_lainnya" id="jenis_lainnya_input" class="form-control @error('jenis_lainnya') is-invalid @enderror"
                            style="width:auto;flex:1;" value="{{ old('jenis_lainnya') }}" placeholder="Sebutkan...">
                    </label>
                </div>
                @error('jenis_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                @error('jenis_lainnya')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ditujukan ke Departemen/Divisi *</label>
                <select name="divisi_tujuan_id" class="form-select @error('divisi_tujuan_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Divisi Tujuan --</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ old('divisi_tujuan_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
                @error('divisi_tujuan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Penjelasan Temuan *</label>
                <textarea name="penjelasan_temuan" class="form-control @error('penjelasan_temuan') is-invalid @enderror"
                    rows="5" placeholder="Jelaskan temuan yang timbul atau potensial...">{{ old('penjelasan_temuan') }}</textarea>
                @error('penjelasan_temuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kategori Temuan *</label>
                <div style="display:flex;gap:20px;">
                    @foreach(['ok' => 'OK', 'observasi' => 'Observasi', 'nc' => 'NC (Ketidaksesuaian)'] as $val => $label)
                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                        <input type="radio" name="kategori_temuan" value="{{ $val }}" {{ old('kategori_temuan', 'nc') === $val ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('kategori_temuan')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card-footer" style="display:flex;justify-content:flex-end;gap:8px;">
            <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send-fill"></i> Kirim FTKP
            </button>
        </div>
    </div>

    <div class="card" style="background:linear-gradient(135deg,rgba(37,99,235,0.04),rgba(6,182,212,0.04));border-style:dashed;">
        <div class="card-header">
            <h5><i class="bi bi-info-circle-fill" style="color:#0284c7;margin-right:6px;"></i>Petunjuk Pengisian</h5>
        </div>
        <div class="card-body">
            <ul style="padding-left:16px;font-size:0.875rem;color:var(--text-muted);line-height:1.9;">
                <li>Pilih <strong>jenis ketidaksesuaian</strong> yang sesuai. Jika tidak ada, pilih "Lain-lain".</li>
                <li>Pilih <strong>divisi tujuan</strong> yang harus menangani masalah ini.</li>
                <li>Jelaskan temuan secara detail pada kolom <strong>Penjelasan Temuan</strong>.</li>
                <li>Pilih <strong>kategori temuan</strong>: OK (tidak ada masalah), Observasi (perlu dipantau), atau NC (ketidaksesuaian nyata).</li>
                <li>Setelah FTKP dibuat, pihak divisi terkait akan mengisi tindak lanjut.</li>
                <li>Kemudian diverifikasi oleh Manager dan Kepala Cabang.</li>
            </ul>
            <div style="margin-top:16px;padding:12px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;font-size:0.82rem;color:#92400e;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Nomor FTKP akan digenerate otomatis setelah form dikirim.
            </div>
        </div>
    </div>
</div>
</form>
@endif
@endsection
