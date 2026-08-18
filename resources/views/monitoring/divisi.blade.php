@extends('layouts.app')
@section('title', 'Perbandingan Divisi')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Perbandingan Antar Divisi</h2>
        <p>Rata-rata nilai dan progres penilaian per divisi</p>
    </div>
    @if($periodeAktif)
    <span class="badge badge-success" style="padding:8px 14px;font-size:0.85rem;">
        <i class="bi bi-calendar-check"></i> {{ $periodeAktif->nama ?? 'Periode Aktif' }}
    </span>
    @endif
</div>

{{-- Filter Periode --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;align-items:flex-end;">
            <div style="flex:1;max-width:300px;">
                <label class="form-label">Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom:20px;">
    {{-- Chart Rata-rata Nilai --}}
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-bar-chart-fill" style="color:#3b82f6;margin-right:6px;"></i>Rata-rata Nilai per Divisi</h5></div>
        <div class="card-body"><canvas id="nilaiChart" height="260"></canvas></div>
    </div>

    {{-- Chart Progres --}}
    <div class="card">
        <div class="card-header"><h5><i class="bi bi-pie-chart-fill" style="color:#8b5cf6;margin-right:6px;"></i>Progres Penilaian</h5></div>
        <div class="card-body"><canvas id="progresChart" height="260"></canvas></div>
    </div>
</div>

{{-- Tabel Perbandingan --}}
<div class="card">
    <div class="card-header"><h5><i class="bi bi-table" style="color:#059669;margin-right:6px;"></i>Detail per Divisi</h5></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Divisi</th><th>Total Karyawan</th><th>Sudah Dinilai</th><th>Progres</th><th>Rata-rata Nilai</th><th>Tertinggi</th><th>Terendah</th></tr>
            </thead>
            <tbody>
                @forelse($divisiStats as $d)
                @php
                    $pct = $d->total_karyawan > 0 ? round(($d->sudah_dinilai / $d->total_karyawan) * 100) : 0;
                    $avg = $d->avg_nilai ?? 0;
                    $avgClass = $avg >= 88 ? 'success' : ($avg >= 63 ? 'primary' : ($avg >= 38 ? 'warning' : 'danger'));
                    $progressClass = $pct == 100 ? 'bg-score-success' : 'bg-score-primary';
                @endphp
                <tr>
                    <td style="font-weight:600;">{{ $d->nama }}</td>
                    <td>{{ $d->total_karyawan }}</td>
                    <td>{{ $d->sudah_dinilai }}</td>
                    <td>
                        <span class="badge {{ $pct == 100 ? 'badge-success' : 'badge-primary' }}" style="font-size:0.8rem;padding:4px 10px;">
                            {{ $pct }}% Selesai
                        </span>
                    </td>
                    <td>
                        @if($avg > 0)
                        <span class="text-score-{{ $avgClass }}" style="font-size:1.1rem;font-weight:700;">{{ number_format($avg, 2) }}</span>
                        @else <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td class="text-score-success" style="font-weight:600;">{{ $d->max_nilai ? number_format($d->max_nilai, 1) : '—' }}</td>
                    <td class="text-score-danger" style="font-weight:600;">{{ $d->min_nilai ? number_format($d->min_nilai, 1) : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

<script id="divisiChartDataJson" type="application/json">
{!! json_encode([
    'labels' => $divisiStats->pluck('nama'),
    'avgValues' => $divisiStats->pluck('avg_nilai')->map(fn($v) => $v ? round($v, 2) : 0),
    'sudah' => $divisiStats->pluck('sudah_dinilai'),
    'total' => $divisiStats->pluck('total_karyawan'),
]) !!}
</script>

@push('scripts')
<script>
const chartData = JSON.parse(document.getElementById('divisiChartDataJson').textContent || '{}');
const labels    = chartData.labels || [];
const avgValues = chartData.avgValues || [];
const sudah     = chartData.sudah || [];
const total     = chartData.total || [];

// Chart rata-rata nilai
new Chart(document.getElementById('nilaiChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Rata-rata Nilai',
            data: avgValues,
            backgroundColor: avgValues.map(v => v >= 90 ? 'rgba(5,150,105,0.75)' : v >= 75 ? 'rgba(59,130,246,0.75)' : v >= 60 ? 'rgba(217,119,6,0.75)' : 'rgba(220,38,38,0.75)'),
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 100, ticks: { font: { family: 'Inter', size: 11 } }, grid: { color: 'rgba(226,232,240,0.5)' } },
            x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
        }
    }
});

// Chart progres
new Chart(document.getElementById('progresChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Dinilai', data: sudah, backgroundColor: 'rgba(59,130,246,0.75)', borderRadius: 8 },
            { label: 'Belum', data: total.map((t,i) => t - sudah[i]), backgroundColor: 'rgba(226,232,240,0.8)', borderRadius: 8 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 11 } } } },
        scales: {
            x: { stacked: true, ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } },
            y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } }, grid: { color: 'rgba(226,232,240,0.5)' } }
        }
    }
});
</script>
@endpush
