@extends('layouts.app')
@section('title', 'Laporan & Export')
@section('breadcrumb')<span style="color:var(--text-muted);">Laporan</span> / <strong>Export & Rekap</strong>@endsection

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Laporan & Export</h2>
        <p>Rekapitulasi dan generate laporan penilaian dalam berbagai format</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('laporan.export-excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
        </a>
        <a href="{{ route('laporan.export-pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
        <a href="{{ route('laporan.print', request()->query()) }}" class="btn btn-secondary" target="_blank">
            <i class="bi bi-printer-fill"></i> Print
        </a>
    </div>
</div>

{{-- Filter Form --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h5><i class="bi bi-funnel-fill" style="color:#3b82f6;margin-right:6px;"></i>Filter Data Laporan</h5></div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.index') }}" id="filterForm">
            <div class="grid grid-3">
                <div class="form-group">
                    <label class="form-label">Periode</label>
                    <select name="periode_id" class="form-select">
                        <option value="">Semua Periode</option>
                        @foreach($periodes as $p)
                        <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Divisi</label>
                    <select name="divisi_id" class="form-select">
                        <option value="">Semua Divisi</option>
                        @foreach($divisis as $d)
                        <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori Nilai</label>
                    <select name="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Terapkan Filter
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-outline">Reset Filter</a>
            </div>
        </form>
    </div>
</div>

{{-- Statistik ringkasan --}}
@if($penilaians->isNotEmpty())
<div class="grid grid-4" style="margin-bottom:20px;">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value">{{ $penilaians->count() }}</div>
        <div class="stat-label">Total Penilaian</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="bi bi-trophy-fill"></i></div>
        <div class="stat-value">{{ number_format($penilaians->avg('nilai_akhir') ?? 0, 2) }}</div>
        <div class="stat-label">Rata-rata Nilai</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-arrow-up-circle-fill"></i></div>
        <div class="stat-value">{{ number_format($penilaians->max('nilai_akhir') ?? 0, 2) }}</div>
        <div class="stat-label">Nilai Tertinggi</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="bi bi-arrow-down-circle-fill"></i></div>
        <div class="stat-value">{{ number_format($penilaians->min('nilai_akhir') ?? 0, 2) }}</div>
        <div class="stat-label">Nilai Terendah</div>
    </div>
</div>

{{-- Tabel Data --}}
<div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <h5><i class="bi bi-table" style="color:#059669;margin-right:6px;"></i>Data Penilaian</h5>
        <span class="badge badge-info">{{ $penilaians->count() }} data</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>NIP</th><th>Nama Karyawan</th><th>Jabatan</th><th>Divisi</th><th>Periode</th><th>Evaluator</th><th>Nilai Akhir</th><th>Kategori</th></tr>
            </thead>
            <tbody>
                @foreach($penilaians->sortByDesc('nilai_akhir')->values() as $i => $p)
                @php $kat = $p->kategori; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family:monospace;font-size:0.85rem;">{{ $p->karyawan->nip ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $p->karyawan->nama ?? '-' }}</td>
                    <td style="font-size:0.85rem;">{{ $p->karyawan->jabatan->nama ?? '-' }}</td>
                    <td style="font-size:0.85rem;">{{ $p->karyawan->divisi->nama ?? '-' }}</td>
                    <td style="font-size:0.85rem;">{{ $p->periode->nama ?? '-' }}</td>
                    <td style="font-size:0.85rem;">{{ $p->evaluator->name ?? '-' }}</td>
                    <td>
                        <span class="text-score-{{ $kat?->warna ?? 'secondary' }}" style="font-size:1rem;font-weight:700;">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </span>
                    </td>
                    <td>
                        @if($kat)<span class="badge badge-{{ $kat->warna }}">{{ $kat->nama }}</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:60px;">
        <i class="bi bi-file-earmark-x" style="font-size:3.5rem;color:var(--text-muted);display:block;margin-bottom:16px;"></i>
        <h5 style="color:var(--text-muted);">Belum ada data penilaian untuk filter yang dipilih</h5>
    </div>
</div>
@endif
@endsection
