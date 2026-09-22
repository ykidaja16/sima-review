@extends('layouts.app')
@section('title', 'Dashboard Manager')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Dashboard Manager</h2>
        <p>Monitoring evaluasi divisi Anda</p>
    </div>
    @if($periodeAktif)
    <div style="display:flex;align-items:center;gap:8px;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);padding:8px 14px;border-radius:10px;">
        <i class="bi bi-calendar-check" style="color:#059669;"></i>
        <div>
            <div style="font-size:0.75rem;color:#64748b;font-weight:600;">PERIODE AKTIF</div>
            <div style="font-size:0.85rem;font-weight:700;color:#065f46;">{{ $periodeAktif->nama }}</div>
        </div>
    </div>
    @endif
</div>

<div class="grid grid-3" style="margin-bottom:24px;">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value">{{ $stats['total_karyawan_divisi'] }}</div>
        <div class="stat-label">Karyawan Divisi</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
        <div class="stat-value">{{ $stats['sudah_dinilai'] }}</div>
        <div class="stat-label">Sudah Dinilai</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-hourglass-split"></i></div>
        <div class="stat-value">{{ $stats['belum_dinilai'] }}</div>
        <div class="stat-label">Belum Dinilai</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-people" style="color:#3b82f6;margin-right:6px;"></i>Status Penilaian Karyawan Divisi</h5>
        <a href="{{ route('penilaian.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Input Penilaian
        </a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Status Periode Ini</th>
                    <th>Nilai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karyawans as $k)
                @php $penilaian = $k->penilaians->first(); @endphp
                <tr>
                    <td>{{ $k->nip }}</td>
                    <td>{{ $k->nama }}</td>
                    <td>{{ $k->jabatan->nama ?? '-' }}</td>
                    <td>
                        @if($penilaian)
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> Sudah Dinilai</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-clock"></i> Belum Dinilai</span>
                        @endif
                    </td>
                    <td>
                        @if($penilaian)
                        @php $kat = $penilaian->kategori; @endphp
                        <span class="badge badge-{{ $kat?->warna ?? 'secondary' }}">{{ number_format($penilaian->nilai_akhir, 2) }}</span>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('monitoring.individu', ['karyawan_id' => $k->id]) }}" class="btn btn-outline btn-sm" title="Track Record">
                                <i class="bi bi-graph-up"></i>
                            </a>
                            @if($penilaian)
                            <a href="{{ route('penilaian.show', $penilaian) }}" class="btn btn-info btn-sm" title="Lihat Detail Penilaian">
                                <i class="bi bi-eye"></i>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:28px;color:var(--text-muted);">
                        <i class="bi bi-people" style="font-size:1.8rem;display:block;margin-bottom:6px;"></i>
                        Belum ada anggota staf di divisi Anda
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
