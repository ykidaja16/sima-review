@extends('layouts.app')

@section('title', 'Dashboard Super Admin')

@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Dashboard</h2>
        <p>Selamat datang, {{ auth()->user()->name }} — Ringkasan evaluasi SIMA-REVIEW</p>
    </div>
    @if($periodeAktif)
    <div style="display:flex;align-items:center;gap:8px;background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);padding:8px 14px;border-radius:10px;">
        <i class="bi bi-calendar-check" style="color:#059669;"></i>
        <div>
            <div style="font-size:0.75rem;color:#64748b;font-weight:600;">PERIODE AKTIF</div>
            <div style="font-size:0.85rem;font-weight:700;color:#065f46;">{{ $periodeAktif->nama }}</div>
        </div>
    </div>
    @else
    <div style="background:rgba(217,119,6,0.08);border:1px solid rgba(217,119,6,0.2);padding:8px 14px;border-radius:10px;">
        <span style="color:#92400e;font-size:0.875rem;"><i class="bi bi-exclamation-triangle"></i> Tidak ada periode aktif</span>
    </div>
    @endif
</div>

{{-- STAT CARDS --}}
<div class="grid grid-4" style="margin-bottom:24px;">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div class="stat-value">{{ number_format($stats['total_karyawan']) }}</div>
        <div class="stat-label">Total Karyawan Aktif</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="bi bi-diagram-3-fill"></i></div>
        <div class="stat-value">{{ number_format($stats['total_divisi']) }}</div>
        <div class="stat-label">Divisi Aktif</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-clipboard2-check-fill"></i></div>
        <div class="stat-value">{{ number_format($stats['total_penilaian']) }}</div>
        <div class="stat-label">Total Penilaian</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="bi bi-trophy-fill"></i></div>
        <div class="stat-value">{{ $ranking->first() ? number_format($ranking->first()->avg_nilai ?? 0, 1) : '-' }}</div>
        <div class="stat-label">Nilai Tertinggi Periode Ini</div>
    </div>
</div>

{{-- ROW 2: Chart + Ranking --}}
<div class="grid grid-2" style="margin-bottom:24px;">

    {{-- Chart Penilaian per Divisi --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-bar-chart-fill" style="color:#3b82f6;margin-right:6px;"></i>Progres Penilaian per Divisi</h5>
            @if($periodeAktif)
            <span class="badge badge-success">{{ $periodeAktif->nama }}</span>
            @endif
        </div>
        <div class="card-body">
            <canvas id="divisiChart" height="220"></canvas>
        </div>
    </div>

    {{-- Ranking Top 5 --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-trophy-fill" style="color:#f59e0b;margin-right:6px;"></i>Top 5 Karyawan Periode Ini</h5>
            <a href="{{ route('monitoring.ranking') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($ranking as $i => $k)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);">
                <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;
                    background:{{ $i==0 ? 'linear-gradient(135deg,#f59e0b,#ef4444)' : ($i==1?'linear-gradient(135deg,#94a3b8,#64748b)':($i==2?'linear-gradient(135deg,#92400e,#d97706)':'var(--bg)')) }};
                    color:{{ $i<3 ? 'white' : 'var(--text-muted)' }};">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.875rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $k->nama }}</div>
                    <div style="font-size:0.75rem;color:var(--text-muted);">{{ $k->divisi->nama ?? '-' }} • {{ $k->jabatan->nama ?? '-' }}</div>
                </div>
                <div style="font-size:1.1rem;font-weight:700;color:{{ $k->avg_nilai>=90?'#059669':($k->avg_nilai>=75?'#2563eb':($k->avg_nilai>=60?'#d97706':'#dc2626')) }};">
                    {{ number_format($k->avg_nilai ?? 0, 1) }}
                </div>
            </div>
            @empty
            <div style="padding:32px;text-align:center;color:var(--text-muted);">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                Belum ada data penilaian periode ini
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Penilaian Terbaru --}}
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-clock-history" style="color:#0284c7;margin-right:6px;"></i>Penilaian Terbaru</h5>
        <a href="{{ route('penilaian.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Karyawan</th>
                    <th>Divisi</th>
                    <th>Periode</th>
                    <th>Evaluator</th>
                    <th>Tanggal</th>
                    <th>Nilai Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPenilaian as $p)
                @php $kategori = $p->kategori; @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $p->karyawan->nama ?? '-' }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $p->karyawan->nip ?? '' }}</div>
                    </td>
                    <td>{{ $p->karyawan->divisi->nama ?? '-' }}</td>
                    <td>{{ $p->periode->nama ?? '-' }}</td>
                    <td>{{ $p->evaluator->name ?? '-' }}</td>
                    <td>{{ $p->tanggal_penilaian?->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-{{ $kategori?->warna ?? 'secondary' }}" style="font-size:0.8rem;font-weight:700;">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </span>
                        @if($kategori)
                        <div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">{{ $kategori->nama }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('penilaian.show', $p) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data penilaian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('divisiChart');
    const labels = @json($penilaianPerDivisi->pluck('nama'));
    const dinilai = @json($penilaianPerDivisi->pluck('total_penilaian'));
    const total = @json($penilaianPerDivisi->pluck('total_karyawan'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Sudah Dinilai',
                    data: dinilai,
                    backgroundColor: 'rgba(59,130,246,0.8)',
                    borderRadius: 6,
                },
                {
                    label: 'Total Karyawan',
                    data: total,
                    backgroundColor: 'rgba(226,232,240,0.8)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 } } }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } },
                    grid: { color: 'rgba(226,232,240,0.5)' }
                },
                x: {
                    ticks: { font: { family: 'Inter', size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
