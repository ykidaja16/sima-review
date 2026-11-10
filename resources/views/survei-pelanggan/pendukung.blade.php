@extends('layouts.app')
@section('title', 'Data Pendukung Survei Pelanggan')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Survei Pelanggan</h2>
        <p>Data pendukung & profil demografi responden survei kepuasan pelanggan</p>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:0.78rem;color:var(--text-muted);background:var(--bg);padding:5px 10px;border-radius:6px;border:1px solid var(--border);">
            <i class="bi bi-arrow-repeat"></i> Cache 5 menit
        </span>
    </div>
</div>

{{-- Navigation Tabs --}}
<div style="display:flex;gap:8px;margin-bottom:20px;border-bottom:1px solid var(--border);padding-bottom:12px;flex-wrap:wrap;">
    <a href="{{ route('survei-pelanggan.index') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-card-list"></i> Data Survei
    </a>
    @if(auth()->user()->hasRole(['super_admin','kacab','mutu']))
    <a href="{{ route('survei-pelanggan.analisa') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-bar-chart-line-fill"></i> Analisa Data
    </a>
    <a href="{{ route('survei-pelanggan.grafik') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-diagram-2-fill"></i> Internal Grafik
    </a>
    <a href="{{ route('survei-pelanggan.pendukung') }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-pie-chart-fill"></i> Data Pendukung
    </a>
    @endif
</div>

{{-- Error state --}}
@if($error)
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle-fill"></i>
    {{ $error }}
</div>
@endif

{{-- Filter & Export Card --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:14px;">
            <form method="GET" action="{{ route('survei-pelanggan.pendukung') }}" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
                <div style="min-width:160px;">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:240px;">
                    <label class="form-label">Semester / Periode</label>
                    <select name="semester" class="form-select">
                        <option value="1" {{ $selectedSemester == 1 ? 'selected' : '' }}>Semester 1 (Januari – Juni)</option>
                        <option value="2" {{ $selectedSemester == 2 ? 'selected' : '' }}>Semester 2 (Juli – Desember)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Tampilkan Data
                </button>
            </form>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('survei-pelanggan.export-pendukung-excel', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-success" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <a href="{{ route('survei-pelanggan.export-pendukung-pdf', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-danger" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Styles Scoped untuk Responsivitas & Mencegah Chart Tembus --}}
<style>
.demografi-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
}
@media (max-width: 1024px) {
    .demografi-grid {
        grid-template-columns: 1fr;
    }
}
.demografi-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    display: flex;
    flex-direction: column;
}
.demografi-card-header {
    background: #fed7aa;
    border-bottom: 1.5px solid #ea580c;
    padding: 12px 18px;
}
.demografi-card-header h5 {
    margin: 0;
    font-weight: 800;
    color: #9a3412;
    text-transform: uppercase;
    font-size: 0.92rem;
    display: flex;
    align-items: center;
    gap: 8px;
}
.demografi-card-body {
    padding: 18px 20px;
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex: 1;
    overflow: hidden;
}
@media (max-width: 640px) {
    .demografi-card-body {
        flex-direction: column;
        align-items: stretch;
    }
}
.demografi-table-col {
    flex: 1 1 52%;
    min-width: 0;
    overflow-x: auto;
}
.demografi-chart-col {
    flex: 1 1 48%;
    min-width: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}
.demografi-chart-container {
    position: relative;
    width: 100%;
    max-width: 250px;
    height: 230px;
    margin: 0 auto;
}
.demografi-chart-container canvas {
    max-width: 100% !important;
    max-height: 100% !important;
}
.demografi-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.84rem;
}
.demografi-table th {
    padding: 6px 4px;
    font-weight: 800;
    border-bottom: 2px solid #000;
}
.demografi-table td {
    padding: 5px 4px;
    border-bottom: 1px solid var(--border);
}
.demografi-table tfoot tr {
    border-top: 2px solid #000;
    font-weight: 800;
}
</style>

<div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
    <div>
        <h5 style="margin:0;font-weight:800;color:var(--text);">PROFIL RESPONDEN SURVEI</h5>
        <div style="font-size:0.85rem;color:var(--text-muted);">{{ $semesterLabel }} &bull; Total Responden: <strong>{{ $jumlahResponden }}</strong> orang</div>
    </div>
</div>

{{-- Grid Demografi 2x2 --}}
<div class="demografi-grid">

    {{-- 1. JENIS KELAMIN --}}
    <div class="demografi-card">
        <div class="demografi-card-header">
            <h5>
                <i class="bi bi-gender-ambiguous"></i> Jenis Kelamin
            </h5>
        </div>
        <div class="demografi-card-body">
            <div class="demografi-table-col">
                <table class="demografi-table">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Profil Responden</th>
                            <th style="text-align:center;">Jumlah</th>
                            <th style="text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['gender'] as $g)
                        <tr>
                            <td>{{ $g['label'] }}</td>
                            <td style="text-align:center;font-weight:700;">{{ $g['count'] }}</td>
                            <td style="text-align:right;color:var(--text-muted);font-weight:600;">{{ $g['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="demografi-chart-col">
                <div class="demografi-chart-container">
                    <canvas id="chartGender"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. USIA --}}
    <div class="demografi-card">
        <div class="demografi-card-header">
            <h5>
                <i class="bi bi-clock-history"></i> Usia
            </h5>
        </div>
        <div class="demografi-card-body">
            <div class="demografi-table-col">
                <table class="demografi-table">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Rentang Usia</th>
                            <th style="text-align:center;">Jumlah</th>
                            <th style="text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['usia'] as $u)
                        <tr>
                            <td>{{ $u['label'] }}</td>
                            <td style="text-align:center;font-weight:700;">{{ $u['count'] }}</td>
                            <td style="text-align:right;color:var(--text-muted);font-weight:600;">{{ $u['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="demografi-chart-col">
                <div class="demografi-chart-container">
                    <canvas id="chartUsia"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. PENDIDIKAN --}}
    <div class="demografi-card">
        <div class="demografi-card-header">
            <h5>
                <i class="bi bi-mortarboard-fill"></i> Pendidikan
            </h5>
        </div>
        <div class="demografi-card-body">
            <div class="demografi-table-col">
                <table class="demografi-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;width:28px;">NO</th>
                            <th style="text-align:left;">Pendidikan</th>
                            <th style="text-align:center;">Jumlah</th>
                            <th style="text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['pendidikan'] as $idx => $p)
                        <tr>
                            <td style="text-align:center;color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td>{{ $p['label'] }}</td>
                            <td style="text-align:center;font-weight:700;">{{ $p['count'] }}</td>
                            <td style="text-align:right;color:var(--text-muted);font-weight:600;">{{ $p['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="demografi-chart-col">
                <div class="demografi-chart-container">
                    <canvas id="chartPendidikan"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. PEKERJAAN --}}
    <div class="demografi-card">
        <div class="demografi-card-header">
            <h5>
                <i class="bi bi-briefcase-fill"></i> Pekerjaan
            </h5>
        </div>
        <div class="demografi-card-body">
            <div class="demografi-table-col">
                <table class="demografi-table">
                    <thead>
                        <tr>
                            <th style="text-align:center;width:28px;">NO</th>
                            <th style="text-align:left;">Pekerjaan</th>
                            <th style="text-align:center;">Jumlah</th>
                            <th style="text-align:right;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demografi['pekerjaan'] as $idx => $k)
                        <tr>
                            <td style="text-align:center;color:var(--text-muted);">{{ $idx + 1 }}</td>
                            <td>{{ $k['label'] }}</td>
                            <td style="text-align:center;font-weight:700;">{{ $k['count'] }}</td>
                            <td style="text-align:right;color:var(--text-muted);font-weight:600;">{{ $k['pct'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">Total</td>
                            <td style="text-align:center;">{{ $jumlahResponden }}</td>
                            <td style="text-align:right;">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="demografi-chart-col">
                <div class="demografi-chart-container">
                    <canvas id="chartPekerjaan"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const demografi = @json($demografi);

    const pieOptions = {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: 4
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 10,
                    boxHeight: 10,
                    padding: 6,
                    font: { size: 10, family: 'Inter, sans-serif' }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        const val = ctx.raw || 0;
                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                        const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                        return ` ${ctx.label}: ${val} (${pct}%)`;
                    }
                }
            }
        }
    };

    // 1. Gender Chart
    new Chart(document.getElementById('chartGender'), {
        type: 'pie',
        data: {
            labels: demografi.gender.map(d => d.label),
            datasets: [{
                data: demografi.gender.map(d => d.count),
                backgroundColor: ['#93c5fd', '#86efac'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // 2. Usia Chart
    new Chart(document.getElementById('chartUsia'), {
        type: 'pie',
        data: {
            labels: demografi.usia.map(d => d.label),
            datasets: [{
                data: demografi.usia.map(d => d.count),
                backgroundColor: ['#bfdbfe', '#f97316', '#94a3b8', '#bbf7d0'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // 3. Pendidikan Chart
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'pie',
        data: {
            labels: demografi.pendidikan.map(d => d.label),
            datasets: [{
                data: demografi.pendidikan.map(d => d.count),
                backgroundColor: ['#38bdf8', '#ea580c', '#64748b', '#eab308', '#2563eb', '#22c55e', '#a855f7'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: pieOptions
    });

    // 4. Pekerjaan Chart
    new Chart(document.getElementById('chartPekerjaan'), {
        type: 'pie',
        data: {
            labels: demografi.pekerjaan.map(d => d.label),
            datasets: [{
                data: demografi.pekerjaan.map(d => d.count),
                backgroundColor: ['#60a5fa', '#f97316', '#94a3b8', '#eab308', '#2563eb', '#22c55e'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: pieOptions
    });
});
</script>
@endpush

