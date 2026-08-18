@extends('layouts.app')
@section('title', 'Monitoring Tim')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Monitoring Tim</h2>
        <p>Status penilaian seluruh karyawan yang Anda supervisi</p>
    </div>
    @if($periodeAktif)
    <span class="badge badge-success" style="padding:8px 14px;font-size:0.85rem;">
        <i class="bi bi-calendar-check"></i> {{ $periodeAktif->nama }}
    </span>
    @endif
</div>

{{-- Filter Periode --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:200px;">
                <label class="form-label">Filter Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Periode Aktif</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card green">
        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-value">{{ $karyawans->filter(fn($k) => $k->penilaians->isNotEmpty())->count() }}</div>
        <div class="stat-label">Sudah Dinilai</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value">{{ $karyawans->filter(fn($k) => $k->penilaians->isEmpty())->count() }}</div>
        <div class="stat-label">Belum Dinilai</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value">{{ $karyawans->count() }}</div>
        <div class="stat-label">Total Karyawan</div>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Karyawan</th><th>Divisi / Jabatan</th><th>Status</th><th>Nilai</th><th>Kategori</th><th>Tanggal Dinilai</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($karyawans->sortBy(fn($k) => $k->penilaians->isEmpty()) as $i => $k)
                @php
                    $p = $k->penilaians->first();
                    $kat = $p?->kategori;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $k->nama }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $k->nip }}</div>
                    </td>
                    <td>
                        <div style="font-size:0.85rem;">{{ $k->divisi->nama ?? '-' }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $k->jabatan->nama ?? '-' }}</div>
                    </td>
                    <td>
                        @if($p)
                        <span class="badge badge-success"><i class="bi bi-check-circle"></i> Sudah Dinilai</span>
                        @else
                        <span class="badge badge-warning"><i class="bi bi-clock"></i> Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($p)
                        <span class="text-score-{{ $kat?->warna ?? 'secondary' }}" style="font-size:1.1rem;font-weight:700;">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </span>
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($kat)<span class="badge badge-{{ $kat->warna }}">{{ $kat->nama }}</span>@else —@endif
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">
                        {{ $p?->tanggal_penilaian?->format('d M Y') ?? '—' }}
                    </td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('monitoring.individu', ['karyawan_id' => $k->id]) }}" class="btn btn-outline btn-sm" title="Track Record">
                            <i class="bi bi-graph-up"></i>
                        </a>
                        @if(!$p && $periodeAktif && !auth()->user()->isPelaksana())
                        <a href="{{ route('penilaian.create', ['karyawan_id' => $k->id]) }}" class="btn btn-primary btn-sm" title="Input Penilaian">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        @elseif($p)
                        <a href="{{ route('penilaian.show', $p) }}" class="btn btn-info btn-sm" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">Tidak ada data karyawan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
