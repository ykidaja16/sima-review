@extends('layouts.app')
@section('title', 'Monitoring Indikator')
@section('breadcrumb')<span style="color:var(--text-muted);">Monitoring</span> / <strong>Monitoring Indikator</strong>@endsection

@section('content')
@php
    $evaluatedIndikators = $indikators->filter(fn($i) => $i->avg_nilai !== null);
    $terendah = $evaluatedIndikators->first();
    $latestPeriode = $trendPeriodes->last();
    $prevPeriode = $trendPeriodes->count() > 1 ? $trendPeriodes->get($trendPeriodes->count() - 2) : null;
    $deltaTrend = ($latestPeriode && $prevPeriode && $latestPeriode->avg_nilai !== null && $prevPeriode->avg_nilai !== null)
        ? round($latestPeriode->avg_nilai - $prevPeriode->avg_nilai, 2)
        : null;
@endphp

<div class="page-header">
    <div class="page-title">
        <h2>Monitoring Indikator</h2>
        <p>Analisis indikator tersulit dicapai, kendala pencapaian, dan tren penilaian per periode</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('monitoring.indikator.export-excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
        </a>
        <a href="{{ route('monitoring.indikator.export-pdf', request()->query()) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('monitoring.indikator') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:190px;">
                <label class="form-label">Filter Periode</label>
                <select name="periode_id" class="form-select searchable-select" data-placeholder="Semua periode...">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isDirektur() || auth()->user()->isSuperAdmin())
            <div style="flex:1;min-width:180px;">
                <label class="form-label">Filter Cabang</label>
                <select name="cabang_id" class="form-select searchable-select" data-placeholder="Semua cabang...">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                    <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama }} ({{ $c->kode }})</option>
                    @endforeach
                </select>
            </div>
            @endif
            @if(!auth()->user()->isManager())
            <div style="flex:1;min-width:190px;">
                <label class="form-label">Filter Divisi</label>
                <select name="divisi_id" class="form-select searchable-select" data-placeholder="Semua divisi...">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Terapkan</button>
            <a href="{{ route('monitoring.indikator') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

{{-- Ringkasan Statistik --}}
<div class="grid grid-3" style="margin-bottom:20px;">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="bi bi-list-check"></i></div>
        <div class="stat-value">{{ $indikators->count() }}</div>
        <div class="stat-label">Total Indikator Aktif</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon orange"><i class="bi bi-exclamation-triangle-fill"></i></div>
        <div class="stat-value">{{ $terendah ? number_format($terendah->avg_nilai, 2) . ' / 4' : '—' }}</div>
        <div class="stat-label">Rata-rata Indikator Terendah</div>
        @if($terendah)
        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $terendah->nama }}">
            {{ Str::limit($terendah->nama, 48) }}
        </div>
        @endif
    </div>
    <div class="stat-card {{ ($deltaTrend === null || $deltaTrend >= 0) ? 'green' : 'red' }}">
        <div class="stat-icon {{ ($deltaTrend === null || $deltaTrend >= 0) ? 'green' : 'red' }}">
            <i class="bi {{ ($deltaTrend === null || $deltaTrend >= 0) ? 'bi-graph-up-arrow' : 'bi-graph-down-arrow' }}"></i>
        </div>
        <div class="stat-value">
            @if($deltaTrend !== null)
                {{ $deltaTrend >= 0 ? '+' : '' }}{{ number_format($deltaTrend, 2) }}
            @elseif($latestPeriode)
                {{ number_format($latestPeriode->avg_nilai, 2) }}
            @else
                —
            @endif
        </div>
        <div class="stat-label">
            @if($deltaTrend !== null)
                Perubahan dari Periode Sebelumnya
            @else
                Rata-rata Nilai Periode Terakhir
            @endif
        </div>
    </div>
</div>

{{-- Grafik Penilaian Per Periode --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h5><i class="bi bi-graph-up" style="color:#3b82f6;margin-right:6px;"></i>Grafik Penilaian Per Periode (Kenaikan / Penurunan)</h5>
        <span style="font-size:0.78rem;color:var(--text-muted);">{{ $trendPeriodes->count() }} Periode Terdata</span>
    </div>
    <div class="card-body">
        @if($trendPeriodes->isNotEmpty())
        <div style="height:260px;position:relative;">
            <canvas id="trendPeriodeChart"></canvas>
        </div>
        @else
        <div style="text-align:center;padding:36px 16px;color:var(--text-muted);">
            <i class="bi bi-bar-chart-line" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
            Belum ada data penilaian pada filter yang dipilih untuk menampilkan grafik tren.
        </div>
        @endif
    </div>
</div>

{{-- Tabel Indikator Terendah + Catatan Kendala --}}
<div class="card">
    <div class="card-header">
        <h5><i class="bi bi-sort-numeric-down" style="color:#dc2626;margin-right:6px;"></i>Indikator dari Nilai Rata-Rata Terendah (Paling Sulit Dicapai)</h5>
        <span style="font-size:0.78rem;color:var(--text-muted);">Diurutkan dari skor rata-rata terendah</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width:45px;">#</th>
                    <th>Indikator &amp; Kategori</th>
                    <th style="width:75px;">Bobot</th>
                    <th style="width:125px;">Rata-rata Skor</th>
                    <th style="width:110px;">Ketercapaian</th>
                    <th style="width:85px;">Jml Evaluasi</th>
                    <th style="min-width:260px;">Alasan / Catatan Kendala Evaluator</th>
                </tr>
            </thead>
            <tbody>
                @forelse($indikators as $idx => $ind)
                @php
                    $avg = $ind->avg_nilai;
                    $pct = $ind->persentase;
                    $badgeClass = $avg === null ? 'secondary' : ($pct >= 85 ? 'success' : ($pct >= 75 ? 'primary' : ($pct >= 60 ? 'warning' : 'danger')));
                @endphp
                <tr>
                    <td style="font-weight:700;color:var(--text-muted);">{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight:600;color:var(--text);line-height:1.4;">{{ $ind->nama }}</div>
                        <div style="font-size:0.74rem;color:var(--text-muted);margin-top:2px;">{{ $ind->kategori }}</div>
                        @if($ind->metode_penilaian_label)
                        <div style="font-size:0.72rem;color:#64748b;margin-top:2px;">Metode: {{ $ind->metode_penilaian_label }}</div>
                        @endif
                    </td>
                    <td><span class="badge badge-info">Bobot {{ $ind->bobot }}</span></td>
                    <td>
                        @if($avg !== null)
                        <span class="text-score-{{ $badgeClass }}" style="font-size:1.05rem;font-weight:800;">
                            {{ number_format($avg, 2) }}
                        </span>
                        <span style="font-size:0.75rem;color:var(--text-muted);">/ 4.00</span>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($pct !== null)
                        <span class="badge badge-{{ $badgeClass }}">{{ number_format($pct, 1) }}%</span>
                        @else
                        <span style="color:var(--text-muted);font-size:0.78rem;">Belum ada data</span>
                        @endif
                    </td>
                    <td style="text-align:center;font-weight:600;">{{ $ind->total_evaluasi }}</td>
                    <td style="font-size:0.79rem;">
                        @if($ind->catatan_list->isNotEmpty())
                            <ul style="padding-left:14px;margin:0;display:flex;flex-direction:column;gap:4px;">
                                @foreach($ind->catatan_list as $c)
                                <li>
                                    <span style="color:var(--text);">{{ $c['catatan'] }}</span>
                                    <span style="color:var(--text-muted);font-size:0.72rem;">({{ $c['karyawan'] }} — Skor {{ $c['nilai'] }})</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color:var(--text-muted);font-style:italic;">Belum ada catatan kendala tertulis</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">Belum ada parameter indikator aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script id="trendChartLabelsJson" type="application/json">{!! json_encode($chartLabels->values()) !!}</script>
<script id="trendChartDataJson" type="application/json">{!! json_encode($chartData->values()) !!}</script>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('trendPeriodeChart');
    if (!canvas) return;
    const labels = JSON.parse(document.getElementById('trendChartLabelsJson').textContent || '[]');
    const data = JSON.parse(document.getElementById('trendChartDataJson').textContent || '[]');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rata-rata Nilai Penilaian',
                data: data,
                borderColor: '#0d5c63',
                backgroundColor: 'rgba(13, 92, 99, 0.12)',
                borderWidth: 3,
                pointBackgroundColor: '#166d3b',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    min: 0,
                    max: 100,
                    ticks: { stepSize: 10 }
                }
            },
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` Rata-rata: ${Number(ctx.parsed.y).toFixed(2)}`
                    }
                }
            }
        }
    });
});
</script>
@endpush
