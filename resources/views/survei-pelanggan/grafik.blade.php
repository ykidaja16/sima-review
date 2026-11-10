@extends('layouts.app')
@section('title', 'Internal Grafik Survei Pelanggan')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Survei Pelanggan</h2>
        <p>Analisa tingkat kesesuaian dan diagram kartesius (Importance-Performance Analysis)</p>
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
    <a href="{{ route('survei-pelanggan.grafik') }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-diagram-2-fill"></i> Internal Grafik
    </a>
    <a href="{{ route('survei-pelanggan.pendukung') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
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
            <form method="GET" action="{{ route('survei-pelanggan.grafik') }}" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
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
                    <i class="bi bi-funnel"></i> Tampilkan Grafik
                </button>
            </form>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('survei-pelanggan.export-grafik-excel', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-success" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <a href="{{ route('survei-pelanggan.export-grafik-pdf', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-danger" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Report Container --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:24px;">
        {{-- Header Dokumen --}}
        <div style="text-align:center;margin-bottom:24px;border-bottom:2px solid var(--border);padding-bottom:16px;">
            <h4 style="font-weight:800;letter-spacing:0.5px;margin:0 0 6px 0;text-transform:uppercase;">ANALISA SURVEY PELANGGAN INTEREN</h4>
            <div style="font-weight:700;font-size:0.95rem;color:var(--text);margin-bottom:4px;">
                Penilaian Tingkat Kesesuaian dan Rata-Rata Variabel Persepsi dan Harapan pada Pelayanan
            </div>
            <div style="font-size:0.875rem;color:var(--text-muted);margin-bottom:4px;">
                Laboratorium Medis dan Klinik SIMA Malang - Jl. Ciliwung No.51 Malang
            </div>
            <div style="font-weight:700;font-size:0.95rem;color:var(--primary);text-transform:uppercase;margin-top:6px;">
                {{ $semesterLabel }}
            </div>
            <div style="font-size:0.825rem;color:var(--text-muted);margin-top:4px;">
                Jumlah Responden: <strong>{{ $jumlahResponden }}</strong> responden
            </div>
        </div>

        {{-- Side-by-Side: Tabel Penilaian & Diagram Kartesius --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(480px, 1fr));gap:24px;align-items:start;">
            {{-- Kolom Kiri: Tabel --}}
            <div class="table-wrapper" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                    <thead>
                        <tr style="background:#ea580c;color:#ffffff;">
                            <th style="width:38px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">NO.</th>
                            <th style="min-width:220px;text-align:center;border:1px solid #c2410c;padding:10px 8px;font-weight:800;">URAIAN</th>
                            <th style="width:70px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">NILAI KINERJA</th>
                            <th style="width:75px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">NILAI KEPENTINGAN</th>
                            <th style="width:75px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">TINGKAT KESESUAIAN</th>
                            <th style="width:60px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">Sumb X</th>
                            <th style="width:60px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">Sumb Y</th>
                            <th style="width:40px;text-align:center;border:1px solid #c2410c;padding:10px 4px;font-weight:800;">K</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $row)
                        @php
                            $badgeColor = match($row['kuadran']) {
                                'A' => 'badge-danger',
                                'B' => 'badge-success',
                                'C' => 'badge-warning',
                                'D' => 'badge-info',
                                default => 'badge-secondary',
                            };
                        @endphp
                        <tr>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;color:var(--text-muted);">
                                {{ $row['no'] }}
                            </td>
                            <td style="border:1px solid #cbd5e1;padding:8px 10px;line-height:1.35;font-size:0.8rem;color:#1e293b;">
                                {{ $row['uraian'] }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;">
                                {{ number_format($row['nilai_kinerja'], 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;">
                                {{ number_format($row['nilai_kepentingan'], 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;">
                                {{ number_format($row['kesesuaian'], 0, ',', '.') }}%
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;">
                                {{ number_format($row['sumb_x'], 2, ',', '.') }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;font-weight:700;">
                                {{ number_format($row['sumb_y'], 2, ',', '.') }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 4px;">
                                <span class="badge {{ $badgeColor }}" style="font-weight:800;font-size:0.8rem;padding:3px 6px;">
                                    {{ $row['kuadran'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:30px;color:var(--text-muted);border:1px solid #cbd5e1;">
                                Belum ada data survei pada semester ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($items) > 0)
                    <tfoot>
                        <tr style="background:#fef3c7;font-weight:800;border-top:2px solid #b45309;">
                            <td colspan="5" style="border:1px solid #cbd5e1;padding:10px 12px;font-size:0.85rem;color:#92400e;">
                                Rata-rata nilai X dan Y
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:10px 4px;color:#92400e;font-size:0.9rem;">
                                {{ number_format($avgX, 2, ',', '.') }}
                            </td>
                            <td style="text-align:center;border:1px solid #cbd5e1;padding:10px 4px;color:#92400e;font-size:0.9rem;">
                                {{ number_format($avgY, 2, ',', '.') }}
                            </td>
                            <td style="border:1px solid #cbd5e1;background:#fef3c7;"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Kolom Kanan: Diagram Kartesius IPA --}}
            <div style="background:#ffffff;border:1.5px solid var(--border);border-radius:var(--radius-sm);padding:20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;border-bottom:1px solid var(--border);padding-bottom:8px;">
                    <h6 style="margin:0;font-weight:700;color:var(--text);display:flex;align-items:center;gap:6px;">
                        <i class="bi bi-graph-up" style="color:var(--primary);"></i>
                        Diagram Kartesius (IPA Chart)
                    </h6>
                    <span style="font-size:0.75rem;color:var(--text-muted);">
                        Sumbu X: &ge; {{ number_format($avgX, 2) }} | Sumbu Y: &ge; {{ number_format($avgY, 2) }}
                    </span>
                </div>

                {{-- Chart Container --}}
                <div style="position:relative;height:480px;width:100%;">
                    <canvas id="cartesianChart"></canvas>
                </div>

                {{-- Legend Kuadran --}}
                <div style="margin-top:16px;border-top:1px dashed var(--border);padding-top:14px;font-size:0.78rem;line-height:1.5;">
                    <div style="font-weight:700;margin-bottom:6px;color:var(--text-muted);text-transform:uppercase;font-size:0.75rem;">
                        Penjelasan 4 Kuadran IPA:
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:8px;">
                        <div style="background:#fef2f2;border-left:3px solid #dc2626;padding:6px 10px;border-radius:4px;">
                            <strong style="color:#dc2626;">Kuadran A</strong> (Prioritas Utama):<br>
                            <span style="color:var(--text-muted);">Kepentingan tinggi, kinerja masih kurang. Perlu ditingkatkan.</span>
                        </div>
                        <div style="background:#f0fdf4;border-left:3px solid #16a34a;padding:6px 10px;border-radius:4px;">
                            <strong style="color:#16a34a;">Kuadran B</strong> (Pertahankan Prestasi):<br>
                            <span style="color:var(--text-muted);">Kepentingan tinggi dan kinerja sudah sangat baik. Pertahankan!</span>
                        </div>
                        <div style="background:#fffbeb;border-left:3px solid #d97706;padding:6px 10px;border-radius:4px;">
                            <strong style="color:#d97706;">Kuadran C</strong> (Prioritas Rendah):<br>
                            <span style="color:var(--text-muted);">Kepentingan rendah, kinerja biasa. Tidak terlalu mendesak.</span>
                        </div>
                        <div style="background:#eff6ff;border-left:3px solid #2563eb;padding:6px 10px;border-radius:4px;">
                            <strong style="color:#2563eb;">Kuadran D</strong> (Berlebihan):<br>
                            <span style="color:var(--text-muted);">Kinerja sangat tinggi tetapi kepentingan pelanggan rendah.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script id="chartPointsJson" type="application/json">
{!! json_encode($chartPoints) !!}
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawPoints = JSON.parse(document.getElementById('chartPointsJson').textContent || '[]');
    const avgX = {{ $avgX }};
    const avgY = {{ $avgY }};

    if (!rawPoints.length) return;

    // Hitung range batas min/max
    const allX = rawPoints.map(p => p.x).concat([avgX]);
    const allY = rawPoints.map(p => p.y).concat([avgY]);

    const minX = Math.floor(Math.min(...allX) * 10) / 10 - 0.5;
    const maxX = Math.ceil(Math.max(...allX) * 10) / 10 + 0.5;
    const minY = Math.floor(Math.min(...allY) * 10) / 10 - 0.5;
    const maxY = Math.ceil(Math.max(...allY) * 10) / 10 + 0.5;

    // Plugin garis pembatas kuadran (crosshairs di avgX dan avgY)
    const quadrantCrosshairPlugin = {
        id: 'quadrantCrosshair',
        beforeDraw: (chart) => {
            const { ctx, chartArea, scales: { x, y } } = chart;
            if (!chartArea) return;
            const { top, bottom, left, right } = chartArea;

            const xPixel = x.getPixelForValue(avgX);
            const yPixel = y.getPixelForValue(avgY);

            ctx.save();
            ctx.lineWidth = 1.5;
            ctx.strokeStyle = '#1e293b';

            // Garis vertikal sumbu X
            if (xPixel >= left && xPixel <= right) {
                ctx.beginPath();
                ctx.moveTo(xPixel, top);
                ctx.lineTo(xPixel, bottom);
                ctx.stroke();
            }

            // Garis horizontal sumbu Y
            if (yPixel >= top && yPixel <= bottom) {
                ctx.beginPath();
                ctx.moveTo(left, yPixel);
                ctx.lineTo(right, yPixel);
                ctx.stroke();
            }

            // Label Sumbu X & Y di ujung garis
            ctx.font = 'bold 12px Inter, sans-serif';
            ctx.fillStyle = '#0f172a';
            ctx.fillText('Y', xPixel - 5, top - 8);
            ctx.fillText('X', right + 8, yPixel + 4);

            // Label Kuadran
            ctx.font = 'bold 11px Inter, sans-serif';
            ctx.fillStyle = '#94a3b8';
            ctx.fillText('Kuadran A', left + 12, top + 20);
            ctx.fillText('Kuadran B', right - 75, top + 20);
            ctx.fillText('Kuadran C', left + 12, bottom - 12);
            ctx.fillText('Kuadran D', right - 75, bottom - 12);

            ctx.restore();
        }
    };

    // Plugin untuk menampilkan nomor item di dekat titik
    const pointLabelsPlugin = {
        id: 'pointLabels',
        afterDatasetsDraw: (chart) => {
            const { ctx, scales: { x, y } } = chart;
            ctx.save();
            ctx.font = 'bold 11px Inter, sans-serif';
            ctx.fillStyle = '#0f172a';

            rawPoints.forEach((pt, idx) => {
                const xPos = x.getPixelForValue(pt.x);
                const yPos = y.getPixelForValue(pt.y);
                const offsetX = (idx % 2 === 0) ? 8 : -14;
                const offsetY = (idx % 3 === 0) ? -8 : 10;
                ctx.fillText(pt.no, xPos + offsetX, yPos + offsetY);
            });

            ctx.restore();
        }
    };

    const ctx = document.getElementById('cartesianChart').getContext('2d');
    new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Variabel Layanan',
                data: rawPoints.map(p => ({
                    x: p.x,
                    y: p.y,
                    no: p.no,
                    label: p.label,
                    kuadran: p.kuadran
                })),
                backgroundColor: '#ea580c',
                borderColor: '#c2410c',
                borderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 9,
                pointHoverBackgroundColor: '#dc2626',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                    padding: 10,
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 11, family: 'Inter' },
                    callbacks: {
                        label: function(context) {
                            const raw = context.raw;
                            return [
                                `Item #${raw.no}: ${raw.label}`,
                                `Kepuasan / Kinerja (X): ${raw.x.toFixed(2)}`,
                                `Kepentingan / Harapan (Y): ${raw.y.toFixed(2)}`,
                                `Posisi: Kuadran ${raw.kuadran}`
                            ];
                        }
                    }
                }
            },
            scales: {
                x: {
                    min: minX,
                    max: maxX,
                    title: {
                        display: true,
                        text: 'Kepuasan (Kinerja)',
                        font: { family: 'Inter', size: 12, weight: 'bold' },
                        color: '#334155'
                    },
                    grid: { color: 'rgba(203, 213, 225, 0.4)' },
                    ticks: {
                        callback: val => val.toFixed(2),
                        font: { family: 'Inter', size: 10 }
                    }
                },
                y: {
                    min: minY,
                    max: maxY,
                    title: {
                        display: true,
                        text: 'Kepentingan (Harapan)',
                        font: { family: 'Inter', size: 12, weight: 'bold' },
                        color: '#334155'
                    },
                    grid: { color: 'rgba(203, 213, 225, 0.4)' },
                    ticks: {
                        callback: val => val.toFixed(2),
                        font: { family: 'Inter', size: 10 }
                    }
                }
            }
        },
        plugins: [quadrantCrosshairPlugin, pointLabelsPlugin]
    });
});
</script>
@endpush

