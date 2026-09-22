@extends('layouts.app')
@section('title', 'Dashboard Supervisor')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Dashboard Supervisor</h2>
        <p>Status penilaian anggota tim Anda</p>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value">{{ $stats['total_bawahan'] }}</div>
        <div class="stat-label">Anggota Tim</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-value">{{ $stats['sudah_dinilai'] }}</div>
        <div class="stat-label">Sudah Dinilai</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value">{{ $stats['total_bawahan'] - $stats['sudah_dinilai'] }}</div>
        <div class="stat-label">Belum Dinilai</div>
    </div>
</div>

@if($periodeAktif)
<div class="alert alert-info">
    <i class="bi bi-calendar-check-fill"></i>
    <span>Periode aktif: <strong>{{ $periodeAktif->nama }}</strong>
    ({{ $periodeAktif->tanggal_mulai->format('d M') }} — {{ $periodeAktif->tanggal_selesai->format('d M Y') }})</span>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-people" style="color:#3b82f6;margin-right:6px;"></i>Daftar Bawahan</h5>
        @if($periodeAktif)
        <a href="{{ route('penilaian.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Input Penilaian
        </a>
        @endif
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Status</th>
                    <th>Nilai Periode Ini</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bawahans as $k)
                @php $penilaian = $k->penilaians->first(); @endphp
                <tr>
                    <td>{{ $k->nip }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>{{ $k->jabatan->nama ?? '-' }}</td>
                    <td>
                        @if($penilaian)
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> Dinilai</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-clock"></i> Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($penilaian)
                        @php $kat = $penilaian->kategori; @endphp
                        <strong class="badge badge-{{ $kat?->warna ?? 'secondary' }}">{{ number_format($penilaian->nilai_akhir, 2) }}</strong>
                        @else —
                        @endif
                    </td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('monitoring.individu', ['karyawan_id' => $k->id]) }}" class="btn btn-outline btn-sm" title="Track Record">
                            <i class="bi bi-graph-up"></i>
                        </a>
                        @if($penilaian)
                        <a href="{{ route('penilaian.show', $penilaian) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">
                        <i class="bi bi-people" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Tidak ada anggota tim yang terdaftar sebagai bawahan Anda
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
