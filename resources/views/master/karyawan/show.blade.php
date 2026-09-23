@extends('layouts.app')
@section('title', 'Detail Karyawan')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>{{ $karyawan->nama }}</h2><p>NIP: {{ $karyawan->nip }}</p></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('master.karyawan.edit', $karyawan) }}" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:20px;">
    <div class="card">
        <div class="card-header"><h5>Informasi Karyawan</h5></div>
        <div class="card-body">
            <table style="width:100%;border-collapse:collapse;">
                <tr><td style="padding:6px 0;color:var(--text-muted);width:40%;">NIP</td><td style="font-weight:600;">{{ $karyawan->nip ?: '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Nama</td><td style="font-weight:600;">{{ $karyawan->nama }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Email</td><td>{{ $karyawan->email ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">No. HP</td><td>{{ $karyawan->no_hp ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Jabatan</td><td>{{ $karyawan->jabatan->nama ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Divisi</td><td>{{ $karyawan->divisi->nama ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Atasan</td><td>{{ $karyawan->atasan?->nama ?? '—' }}</td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Status</td><td>
                    @if($karyawan->is_active)<span class="badge badge-success">Aktif</span>
                    @else<span class="badge badge-danger">Nonaktif</span>@endif
                </td></tr>
                <tr><td style="padding:6px 0;color:var(--text-muted);">Akun Login</td><td>
                    @if($karyawan->user)
                    <div style="font-weight:600;">{{ $karyawan->user->email }}</div>
                    <span class="badge badge-primary">{{ $karyawan->user->role_label }}</span>
                    @else
                    <span style="color:var(--text-muted);">Tidak ada akun</span>
                    @endif
                </td></tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Riwayat Penilaian</h5>
            <a href="{{ route('monitoring.individu', ['karyawan_id' => $karyawan->id]) }}" class="btn btn-outline btn-sm">
                <i class="bi bi-graph-up"></i> Track Record
            </a>
        </div>
        <div class="card-body" style="padding:0;max-height:350px;overflow-y:auto;">
            @forelse($karyawan->penilaians as $p)
            @php $kat = $p->kategori; @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid var(--border);">
                <div>
                    <div style="font-size:0.875rem;font-weight:600;">{{ $p->periode->nama ?? '—' }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $p->tanggal_penilaian?->format('d M Y') }} by {{ $p->evaluator->name ?? '—' }}</div>
                </div>
                <div style="text-align:right;">
                    <span class="badge badge-{{ $kat?->warna ?? 'secondary' }}" style="font-size:0.85rem;">{{ number_format($p->nilai_akhir ?? 0, 2) }}</span>
                    @if($kat)<div style="font-size:0.7rem;color:var(--text-muted);">{{ $kat->nama }}</div>@endif
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada penilaian</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
