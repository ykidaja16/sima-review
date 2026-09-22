@extends('layouts.app')
@section('title', 'Detail FTKP: ' . $ketidaksesuaian->nomor_ftkp)
@section('content')

@php
    $statusColor = $ketidaksesuaian->status_color;
    $statusLabel = $ketidaksesuaian->status_label;
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>FTKP: {{ $ketidaksesuaian->nomor_ftkp }}</h2>
        <p>Dibuat: {{ $ketidaksesuaian->tanggal_laporan->format('d F Y') }} &mdash; <span class="badge badge-{{ $statusColor }}">{{ $statusLabel }}</span></p>
    </div>
    <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

{{-- BAGIAN 1: PENGGAGAS --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(59,130,246,0.08),rgba(6,182,212,0.08));">
        <h5><i class="bi bi-1-circle-fill" style="color:#3b82f6;margin-right:8px;"></i>BAGIAN 1 – PENGGAGAS</h5>
    </div>
    <div class="card-body">
        <div class="grid grid-2" style="margin-bottom:16px;">
            <div>
                <div class="form-label">Penggagas</div>
                <div style="font-weight:600;">{{ $ketidaksesuaian->pelapor->nama }}</div>
            </div>
            <div>
                <div class="form-label">Departemen/Bagian</div>
                <div style="font-weight:600;">{{ $ketidaksesuaian->divisiPelapor->nama }}</div>
            </div>
            <div>
                <div class="form-label">Ditujukan ke Divisi</div>
                <div style="font-weight:600;">{{ $ketidaksesuaian->divisiTujuan->nama }}</div>
            </div>
            <div>
                <div class="form-label">Kategori Temuan</div>
                <span class="badge badge-{{ match($ketidaksesuaian->kategori_temuan) { 'ok' => 'success', 'observasi' => 'warning', default => 'danger' } }}">
                    {{ $ketidaksesuaian->kategori_label }}
                </span>
            </div>
        </div>
        <div class="form-group" style="margin:0;">
            <div class="form-label">Jenis Ketidaksesuaian</div>
            <div>
                @if($ketidaksesuaian->jenis)
                    {{ $ketidaksesuaian->jenis->nama }}
                @elseif($ketidaksesuaian->jenis_lainnya)
                    Lain-lain: {{ $ketidaksesuaian->jenis_lainnya }}
                @else
                    -
                @endif
            </div>
        </div>
        <div class="form-group" style="margin-top:16px;margin-bottom:0;">
            <div class="form-label">Penjelasan Temuan</div>
            <div style="background:#f8fafc;padding:12px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:0.9rem;">{{ $ketidaksesuaian->penjelasan_temuan }}</div>
        </div>
    </div>
</div>

{{-- BAGIAN 2: TINDAKAN --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(5,150,105,0.08),rgba(16,185,129,0.08));">
        <h5><i class="bi bi-2-circle-fill" style="color:#059669;margin-right:8px;"></i>BAGIAN 2 – TINDAKAN</h5>
    </div>
    <div class="card-body">
        @if($ketidaksesuaian->tindaklanjut)
        @php $tl = $ketidaksesuaian->tindaklanjut; @endphp
        <div class="grid grid-2" style="margin-bottom:16px;">
            <div>
                <div class="form-label">Diisi oleh</div>
                <div style="font-weight:600;">{{ $tl->user->name }}</div>
            </div>
            <div>
                <div class="form-label">Perkiraan Tanggal Selesai</div>
                <div style="font-weight:600;">{{ $tl->perkiraan_tanggal_selesai->format('d F Y') }}</div>
            </div>
        </div>
        <div class="grid grid-2">
            <div>
                <div class="form-label">Akar Masalah</div>
                <div style="background:#f8fafc;padding:12px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:0.875rem;">{{ $tl->akar_masalah }}</div>
            </div>
            <div>
                <div class="form-label">Tindakan Korektif</div>
                <div style="background:#f8fafc;padding:12px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:0.875rem;">{{ $tl->tindakan_korektif }}</div>
            </div>
        </div>
        <div style="margin-top:16px;">
            <div class="form-label">Tindakan Pencegahan</div>
            <div style="background:#f8fafc;padding:12px;border-radius:8px;border:1px solid var(--border);white-space:pre-wrap;font-size:0.875rem;">{{ $tl->tindakan_pencegahan }}</div>
        </div>
        @else
        <div style="text-align:center;padding:32px;color:var(--text-muted);">
            <i class="bi bi-hourglass-split" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            Tindak lanjut belum diisi oleh divisi terkait.
        </div>

        @if($bisaTindaklanjut)
        <div style="margin-top:16px;border-top:1px solid var(--border);padding-top:20px;">
            <h6 style="font-weight:700;margin-bottom:16px;color:var(--text);">Isi Tindak Lanjut</h6>
            <form action="{{ route('ketidaksesuaian.tindaklanjut', $ketidaksesuaian) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Akar Masalah / Analisa *</label>
                    <textarea name="akar_masalah" class="form-control @error('akar_masalah') is-invalid @enderror" rows="3" required>{{ old('akar_masalah') }}</textarea>
                    @error('akar_masalah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">Tindakan Korektif *</label>
                        <textarea name="tindakan_korektif" class="form-control @error('tindakan_korektif') is-invalid @enderror" rows="3" required>{{ old('tindakan_korektif') }}</textarea>
                        @error('tindakan_korektif')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tindakan Pencegahan *</label>
                        <textarea name="tindakan_pencegahan" class="form-control @error('tindakan_pencegahan') is-invalid @enderror" rows="3" required>{{ old('tindakan_pencegahan') }}</textarea>
                        @error('tindakan_pencegahan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Perkiraan Tanggal Selesai *</label>
                    <input type="date" name="perkiraan_tanggal_selesai" class="form-control @error('perkiraan_tanggal_selesai') is-invalid @enderror" value="{{ old('perkiraan_tanggal_selesai') }}" required>
                    @error('perkiraan_tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div style="display:flex;justify-content:flex-end;">
                    <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-send-fill"></i> Kirim Tindak Lanjut</button>
                </div>
            </form>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- BAGIAN 3: VERIFIKASI --}}
<div class="card">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(109,40,217,0.08));">
        <h5><i class="bi bi-3-circle-fill" style="color:#7c3aed;margin-right:8px;"></i>BAGIAN 3 – VERIFIKASI</h5>
    </div>
    <div class="card-body">
        <div class="grid grid-2">
            {{-- Verifikasi Manager --}}
            <div style="border:1px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-weight:700;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-person-check-fill" style="color:#0284c7;"></i> Verifikasi Manager
                </div>
                @if($ketidaksesuaian->verifikasiManager)
                @php $vm = $ketidaksesuaian->verifikasiManager; @endphp
                <div style="margin-bottom:8px;">
                    <span class="badge badge-{{ $vm->tindakan_efektif ? 'success' : 'danger' }}">
                        {{ $vm->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif' }}
                    </span>
                </div>
                <div style="font-size:0.85rem;color:var(--text-muted);">Oleh: {{ $vm->verifikator->name }}</div>
                @if($vm->alasan)<div style="font-size:0.875rem;margin-top:8px;">{{ $vm->alasan }}</div>@endif
                @else
                <div style="color:var(--text-muted);font-size:0.875rem;">Menunggu verifikasi manager...</div>
                @if($bisaVerifManager)
                <form action="{{ route('ketidaksesuaian.verifikasi', $ketidaksesuaian) }}" method="POST" style="margin-top:12px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tindakan Efektif?</label>
                        <div style="display:flex;gap:16px;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="tindakan_efektif" value="1" required> Ya
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="tindakan_efektif" value="0"> Tidak
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alasan (jika tidak efektif)</label>
                        <textarea name="alasan" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle-fill"></i> Submit Verifikasi</button>
                </form>
                @endif
                @endif
            </div>

            {{-- Verifikasi Kacab --}}
            <div style="border:1px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-weight:700;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
                    <i class="bi bi-person-check-fill" style="color:#7c3aed;"></i> Verifikasi Kepala Cabang
                </div>
                @if($ketidaksesuaian->verifikasiKacab)
                @php $vk = $ketidaksesuaian->verifikasiKacab; @endphp
                <div style="margin-bottom:8px;">
                    <span class="badge badge-{{ $vk->tindakan_efektif ? 'success' : 'danger' }}">
                        {{ $vk->tindakan_efektif ? 'Tindakan Efektif' : 'Tidak Efektif' }}
                    </span>
                </div>
                <div style="font-size:0.85rem;color:var(--text-muted);">Oleh: {{ $vk->verifikator->name }}</div>
                @if($vk->alasan)<div style="font-size:0.875rem;margin-top:8px;">{{ $vk->alasan }}</div>@endif
                @if($vk->ftkp_baru_no)<div style="font-size:0.85rem;margin-top:8px;color:var(--text-muted);">FTKP Baru: {{ $vk->ftkp_baru_no }}</div>@endif
                @else
                <div style="color:var(--text-muted);font-size:0.875rem;">Menunggu verifikasi kepala cabang...</div>
                @if($bisaVerifKacab)
                <form action="{{ route('ketidaksesuaian.verifikasi', $ketidaksesuaian) }}" method="POST" style="margin-top:12px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Tindakan Efektif?</label>
                        <div style="display:flex;gap:16px;">
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="tindakan_efektif" value="1" required> Ya
                            </label>
                            <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
                                <input type="radio" name="tindakan_efektif" value="0"> Tidak
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alasan</label>
                        <textarea name="alasan" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. FTKP Baru (jika perlu)</label>
                        <input type="text" name="ftkp_baru_no" class="form-control" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-circle-fill"></i> Submit Verifikasi</button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
