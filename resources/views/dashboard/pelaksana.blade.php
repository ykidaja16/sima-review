@extends('layouts.app')
@section('title', 'Dashboard — Nilai Saya')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Nilai Saya</h2>
        <p>Track record evaluasi Service Excellent Anda</p>
    </div>
</div>

@if($karyawan)
{{-- Profil Info --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="display:flex;align-items:center;gap:20px;">
        <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:white;flex-shrink:0;">
            {{ substr($karyawan->nama, 0, 1) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:1.1rem;font-weight:700;">{{ $karyawan->nama }}</div>
            <div style="font-size:0.85rem;color:var(--text-muted);">{{ $karyawan->nip }} • {{ $karyawan->jabatan->nama ?? '-' }} • {{ $karyawan->divisi->nama ?? '-' }}</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:0.75rem;color:var(--text-muted);margin-bottom:2px;">Rata-rata Semua Periode</div>
            <div style="font-size:2rem;font-weight:800;color:{{ $rataRata>=90?'#059669':($rataRata>=75?'#2563eb':($rataRata>=60?'#d97706':'#dc2626')) }};">
                {{ $rataRata ? number_format($rataRata, 2) : '—' }}
            </div>
        </div>
    </div>
</div>

@if($periodeAktif)
<div class="alert {{ $sudahDinilai ? 'alert-success' : 'alert-warning' }}">
    <i class="bi bi-{{ $sudahDinilai ? 'check-circle-fill' : 'clock-fill' }}"></i>
    @if($sudahDinilai)
        Anda sudah dinilai untuk periode <strong>{{ $periodeAktif->nama }}</strong>.
    @else
        Anda belum dinilai untuk periode <strong>{{ $periodeAktif->nama }}</strong>. Hubungi atasan Anda.
    @endif
</div>
@endif

{{-- Riwayat Penilaian --}}
<div class="grid grid-3">
    @forelse($riwayatPenilaian as $p)
    @php $kat = $p->kategori; @endphp
    <div class="card" style="transition:all 0.25s;cursor:default;">
        <div class="card-header">
            <div>
                <div style="font-size:0.8rem;font-weight:600;color:var(--text-muted);">{{ $p->periode->nama ?? '-' }}</div>
                <div style="font-size:0.78rem;color:var(--text-muted);">{{ $p->tanggal_penilaian?->format('d M Y') }}</div>
            </div>
            @if($kat)
            <span class="badge badge-{{ $kat->warna }}">{{ $kat->nama }}</span>
            @endif
        </div>
        <div class="card-body" style="text-align:center;padding:20px;">
            <div style="font-size:3rem;font-weight:800;color:{{ $kat?->warna=='success'?'#059669':($kat?->warna=='primary'?'#2563eb':($kat?->warna=='warning'?'#d97706':'#dc2626')) }};">
                {{ number_format($p->nilai_akhir ?? 0, 1) }}
            </div>
            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Nilai Akhir</div>
        </div>
        <div class="card-footer" style="font-size:0.78rem;color:var(--text-muted);">
            <i class="bi bi-person"></i> {{ $p->evaluator->name ?? '-' }}
            @if($p->catatan)
            <div style="margin-top:4px;font-style:italic;">"{{ Str::limit($p->catatan, 60) }}"</div>
            @endif
        </div>
    </div>
    @empty
    <div class="card" style="grid-column:1/-1;text-align:center;padding:48px 24px;">
        <i class="bi bi-inbox" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--text-muted);">Belum ada riwayat penilaian</p>
    </div>
    @endforelse
</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px;">
        <i class="bi bi-person-x" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:12px;"></i>
        <h5>Data karyawan tidak ditemukan</h5>
        <p style="color:var(--text-muted);margin-top:8px;">Hubungi administrator untuk menghubungkan akun Anda dengan data karyawan.</p>
    </div>
</div>
@endif

@endsection
