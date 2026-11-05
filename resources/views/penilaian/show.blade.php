@extends('layouts.app')
@section('title', 'Detail Penilaian')
@section('breadcrumb')
    <span style="color:var(--text-muted);">
        {{ auth()->user()->isPelaksana() ? 'Track Record Saya' : 'Penilaian' }}
    </span> / <strong>Detail Penilaian</strong>
@endsection

@section('content')
@php $kat = $penilaian->kategori; @endphp

<div class="page-header">
    <div class="page-title">
        <h2>Detail Penilaian</h2>
        <p>{{ $penilaian->karyawan->nama ?? '-' }} — {{ $penilaian->periode->nama ?? '-' }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        @if(!auth()->user()->isPelaksana())
        <a href="{{ route('penilaian.edit', $penilaian) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('penilaian.index') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        @else
        <a href="{{ route('monitoring.individu') }}" class="btn btn-outline">
            <i class="bi bi-arrow-left"></i> Kembali ke Track Record
        </a>
        @endif
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:20px;">
    {{-- Info Karyawan --}}
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-person-badge-fill" style="color:#3b82f6;margin-right:6px;"></i>Informasi Karyawan & Penilaian</h5></div>
        <div class="card-body">
            @php
                $k = $penilaian->karyawan;
                $isOwnPenilaian = auth()->user()->karyawan && $penilaian->karyawan_id === auth()->user()->karyawan->id;
            @endphp
            <table style="width:100%;border-collapse:collapse;">
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;width:40%;">NIP</td><td style="font-weight:600;">{{ $k->nip }}</td></tr>
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Nama Karyawan</td><td style="font-weight:600;">{{ $k->nama }}</td></tr>
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Jabatan</td><td>{{ $k->jabatan->nama ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Divisi</td><td>{{ $k->divisi->nama ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Periode</td><td style="font-weight:600;color:var(--primary);">{{ $penilaian->periode->nama ?? '-' }}</td></tr>
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Tanggal Penilaian</td><td>{{ $penilaian->tanggal_penilaian?->format('d M Y') }}</td></tr>
                @if(!$isOwnPenilaian)
                <tr><td style="padding:8px 0;color:var(--text-muted);font-size:0.85rem;">Evaluator</td><td>{{ $penilaian->evaluator->name ?? '-' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Nilai Akhir --}}
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-star-fill" style="color:#f59e0b;margin-right:6px;"></i>Hasil Akhir</h5></div>
        <div class="card-body" style="text-align:center;padding:28px;">
            <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Nilai Akhir (Skala 100)</div>
            <div class="text-score-{{ $kat?->warna ?? 'secondary' }}" style="font-size:4.5rem;font-weight:800;line-height:1;margin:8px 0;">
                {{ number_format($penilaian->nilai_akhir ?? 0, 2) }}
            </div>
            @if($kat)
            <div>
                <span class="badge badge-{{ $kat->warna }}" style="font-size:1rem;padding:6px 18px;">{{ $kat->nama }}</span>
            </div>
            @endif
            @if($penilaian->catatan)
            <div style="margin-top:16px;padding:12px;background:var(--bg);border-radius:8px;font-size:0.875rem;font-style:italic;color:var(--text);">
                "{{ $penilaian->catatan }}"
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Detail per Parameter --}}
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h5><i class="bi bi-list-check" style="color:#0284c7;margin-right:6px;"></i>Rincian Nilai per Indikator SOP</h5>
        <div style="display:flex;gap:6px;font-size:0.75rem;">
            <span class="badge badge-success">4 = Sangat Baik</span>
            <span class="badge badge-primary">3 = Baik</span>
            <span class="badge badge-warning">2 = Cukup</span>
            <span class="badge badge-danger">1 = Kurang Baik</span>
        </div>
    </div>
    <div class="card-body" style="padding:0;">
        @php $grouped = $penilaian->details->groupBy(fn($d) => $d->parameter?->kategori ?? 'Lainnya'); @endphp
        @foreach($grouped as $kategori => $details)
        <div style="padding:10px 20px;background:#f8fafc;border-bottom:1px solid var(--border);border-top:1px solid var(--border);font-size:0.8rem;font-weight:700;color:var(--text);display:flex;justify-content:space-between;">
            <span><i class="bi bi-folder2-open" style="color:var(--primary);margin-right:6px;"></i>{{ $kategori }}</span>
            <span style="color:var(--text-muted);font-weight:600;font-size:0.75rem;">{{ $details->count() }} Indikator</span>
        </div>
        @foreach($details as $detail)
        @php
            $skor = (int) $detail->nilai;
            $badgeColor = match($skor) {
                4 => 'success',
                3 => 'primary',
                2 => 'warning',
                default => 'danger',
            };
            $skorLabel = match($skor) {
                4 => 'Sangat Baik',
                3 => 'Baik',
                2 => 'Cukup',
                default => 'Kurang Baik',
            };
        @endphp
        <div style="padding:12px 20px;border-bottom:1px solid var(--border);display:grid;grid-template-columns:1fr 200px;gap:16px;align-items:center;">
            <div>
                <div style="font-weight:500;font-size:0.875rem;color:var(--text);">{{ $detail->parameter?->nama ?? 'Parameter ' . $detail->parameter_id }}</div>
                @if($detail->parameter?->deskripsi)
                <div style="font-size:0.73rem;color:var(--text-muted);margin-top:2px;">{{ $detail->parameter->deskripsi }}</div>
                @endif
                @if($detail->catatan)
                <div style="font-size:0.75rem;color:var(--text-muted);font-style:italic;margin-top:4px;">Catatan: {{ $detail->catatan }}</div>
                @endif
            </div>
            <div style="text-align:right;display:flex;align-items:center;justify-content:flex-end;gap:10px;">
                <span class="badge badge-{{ $badgeColor }}" style="padding:4px 10px;border-radius:12px;font-size:0.8rem;">
                    Skor: {{ $skor }} / 4
                </span>
                <span class="text-score-{{ $badgeColor }}" style="font-size:0.8rem;font-weight:600;">
                    {{ $skorLabel }}
                </span>
            </div>
        </div>
        @endforeach
        @endforeach
    </div>
</div>

{{-- Tanggapan Karyawan terhadap Penilaian --}}
<div class="card" style="margin-top:20px;">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h5><i class="bi bi-chat-left-text-fill" style="color:#059669;margin-right:6px;"></i>Tanggapan Karyawan terhadap Penilaian</h5>
        @if($penilaian->tanggal_tanggapan)
        <span style="font-size:0.78rem;color:var(--text-muted);">
            <i class="bi bi-clock"></i> Ditanggapi: {{ $penilaian->tanggal_tanggapan->format('d M Y, H:i') }}
        </span>
        @endif
    </div>
    <div class="card-body">
        @if($penilaian->tanggapan)
        <div style="padding:14px 16px;background:#f8fafc;border-left:4px solid #059669;border-radius:8px;margin-bottom:{{ $isOwnPenilaian ? '16px' : '0' }};">
            <div style="font-size:0.8rem;font-weight:600;color:#059669;margin-bottom:4px;">
                Tanggapan dari {{ $penilaian->karyawan->nama ?? 'Karyawan' }}:
            </div>
            <div style="font-size:0.9rem;color:var(--text);white-space:pre-wrap;">{{ $penilaian->tanggapan }}</div>
        </div>
        @else
        <div style="color:var(--text-muted);font-size:0.875rem;margin-bottom:{{ $isOwnPenilaian ? '16px' : '0' }};">
            <i class="bi bi-info-circle"></i> Belum ada tanggapan dari karyawan yang dinilai.
        </div>
        @endif

        @if($isOwnPenilaian)
        <form action="{{ route('penilaian.tanggapan', $penilaian) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">{{ $penilaian->tanggapan ? 'Perbarui Tanggapan Anda' : 'Berikan Tanggapan Anda terhadap Penilaian Ini' }} *</label>
                <textarea name="tanggapan" class="form-control @error('tanggapan') is-invalid @enderror" rows="3" required placeholder="Tuliskan tanggapan, masukan, atau konfirmasi Anda terkait hasil penilaian ini...">{{ old('tanggapan', $penilaian->tanggapan) }}</textarea>
                @error('tanggapan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-fill"></i> {{ $penilaian->tanggapan ? 'Perbarui Tanggapan' : 'Kirim Tanggapan' }}
                </button>
            </div>
        </form>
        @endif
    </div>
</div>

@endsection
