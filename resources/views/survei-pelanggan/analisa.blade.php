@extends('layouts.app')
@section('title', 'Analisa Data Survei Pelanggan')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Survei Pelanggan</h2>
        <p>Analisa data survei kepuasan pelanggan per semester (6 bulan)</p>
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
    <a href="{{ route('survei-pelanggan.analisa') }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
        <i class="bi bi-bar-chart-line-fill"></i> Analisa Data
    </a>
    <a href="{{ route('survei-pelanggan.grafik') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
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
            <form method="GET" action="{{ route('survei-pelanggan.analisa') }}" style="display:flex;gap:14px;flex-wrap:wrap;align-items:flex-end;">
                <div style="min-width:140px;">
                    <label class="form-label">Tahun</label>
                    <select name="tahun" class="form-select">
                        @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:220px;">
                    <label class="form-label">Semester / Periode</label>
                    <select name="semester" class="form-select">
                        <option value="1" {{ $selectedSemester == 1 ? 'selected' : '' }}>Periode I (Januari – Juni)</option>
                        <option value="2" {{ $selectedSemester == 2 ? 'selected' : '' }}>Periode II (Juli – Desember)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Tampilkan Analisa
                </button>
            </form>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('survei-pelanggan.export-excel', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-success" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
                <a href="{{ route('survei-pelanggan.export-pdf', ['tahun' => $selectedYear, 'semester' => $selectedSemester]) }}"
                   class="btn btn-danger" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Report Container --}}
<div class="card">
    <div class="card-body" style="padding:24px;">
        {{-- Header Dokumen --}}
        <div style="text-align:center;margin-bottom:24px;border-bottom:2px solid var(--border);padding-bottom:16px;">
            <h4 style="font-weight:800;letter-spacing:0.5px;margin:0 0 4px 0;text-transform:uppercase;">ANALISA DATA SURVEY PELANGGAN</h4>
            <h5 style="font-weight:700;margin:0 0 4px 0;color:var(--text-muted);text-transform:uppercase;">LABORATORIUM MEDIS DAN KLINIK SIMA CILIWUNG MALANG</h5>
            <div style="font-weight:700;font-size:0.95rem;color:var(--primary);text-transform:uppercase;">PERIODE BULAN {{ $periodeBulan }}</div>
        </div>

        {{-- Meta & Keterangan --}}
        <div style="margin-bottom:20px;font-size:0.875rem;">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:14px;background:#f8fafc;padding:12px 16px;border-radius:var(--radius-sm);border:1px solid var(--border);">
                <div>
                    <span style="color:var(--text-muted);">Data Survey pelanggan:</span> <strong>{{ $periodeNama }}</strong>
                </div>
                <div>
                    <span style="color:var(--text-muted);">Jumlah responden:</span> <strong style="color:var(--primary);font-size:1rem;">{{ $jumlahResponden }}</strong> responden
                </div>
            </div>

            {{-- Box Keterangan --}}
            <div style="background:#ffffff;border:1px dashed var(--border);border-radius:var(--radius-sm);padding:14px 16px;">
                <div style="font-weight:700;margin-bottom:8px;color:var(--text-muted);font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;">Keterangan Bobot Penilaian:</div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));gap:12px;line-height:1.6;">
                    <div>
                        <div style="font-weight:600;color:#0284c7;font-size:0.825rem;margin-bottom:2px;">Tingkat Kinerja:</div>
                        <ul style="list-style:none;padding:0;margin:0;font-size:0.82rem;color:var(--text);">
                            <li><strong>SP</strong> : Sangat Puas (bobot 4)</li>
                            <li><strong>P</strong> &nbsp;: Puas (bobot 3)</li>
                            <li><strong>TP</strong> : Tidak Puas / Biasa saja (bobot 2)</li>
                            <li><strong>STP</strong>: Sangat Tidak Puas (bobot 1)</li>
                        </ul>
                    </div>
                    <div>
                        <div style="font-weight:600;color:#059669;font-size:0.825rem;margin-bottom:2px;">Tingkat Kepentingan:</div>
                        <ul style="list-style:none;padding:0;margin:0;font-size:0.82rem;color:var(--text);">
                            <li><strong>SP'</strong> : Sangat Penting (bobot 4)</li>
                            <li><strong>P'</strong> &nbsp;: Penting (bobot 3)</li>
                            <li><strong>TP'</strong> : Tidak Penting / Biasa saja (bobot 2)</li>
                            <li><strong>STP'</strong>: Sangat Tidak Penting (bobot 1)</li>
                        </ul>
                    </div>
                    <div style="grid-column:1/-1;border-top:1px solid #f1f5f9;padding-top:8px;font-size:0.82rem;color:var(--text-muted);">
                        <strong>TP %</strong> : Prosentase Tingkat Harapan Pelanggan Yang Terpenuhi = <code>(Nilai Kinerja / Nilai Kepentingan) &times; 100%</code>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Analisa Data --}}
        <div class="table-wrapper" style="overflow-x:auto;">
            <table class="table-analisa" style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                <thead>
                    <tr>
                        <th rowspan="2" style="width:40px;text-align:center;border:1px solid #94a3b8;background:#cbd5e1;font-weight:800;">#</th>
                        <th rowspan="2" style="min-width:300px;text-align:center;border:1px solid #94a3b8;background:#cbd5e1;font-weight:800;">HAL</th>
                        <th colspan="5" style="text-align:center;border:1px solid #94a3b8;background:#e2e8f0;font-weight:800;">Kinerja</th>
                        <th colspan="5" style="text-align:center;border:1px solid #94a3b8;background:#e2e8f0;font-weight:800;">Kepentingan</th>
                        <th rowspan="2" style="width:90px;text-align:center;border:1px solid #94a3b8;background:#cbd5e1;font-weight:800;">TP %</th>
                    </tr>
                    <tr>
                        {{-- Kinerja --}}
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">SP</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">P</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">TP</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">STP</th>
                        <th style="width:75px;text-align:center;border:1px solid #94a3b8;background:#94a3b8;color:#0f172a;font-weight:800;">Nilai</th>
                        {{-- Kepentingan --}}
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">SP'</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">P'</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">TP'</th>
                        <th style="width:45px;text-align:center;border:1px solid #94a3b8;background:#f1f5f9;font-weight:700;">STP'</th>
                        <th style="width:75px;text-align:center;border:1px solid #94a3b8;background:#94a3b8;color:#0f172a;font-weight:800;">Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Header Kategori --}}
                    <tr>
                        <td colspan="13" style="font-weight:800;color:#dc2626;background:#fef2f2;padding:8px 14px;border:1px solid #94a3b8;letter-spacing:0.5px;">
                            KUALITAS LAYANAN
                        </td>
                    </tr>

                    @forelse($analysisData as $row)
                    <tr>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;color:var(--text-muted);font-weight:600;">
                            {{ $row['no'] }}
                        </td>
                        <td style="border:1px solid #cbd5e1;padding:8px 12px;font-size:0.84rem;color:#1e293b;line-height:1.4;">
                            {{ $row['label'] }}
                        </td>
                        {{-- Kinerja counts --}}
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['k_sp'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['k_p'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['k_tp'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['k_stp'] }}</td>
                        <td style="text-align:center;border:1px solid #94a3b8;padding:8px 6px;background:#94a3b8;font-weight:800;color:#0f172a;">
                            {{ number_format($row['k_nilai'], 2, ',', '.') }}
                        </td>
                        {{-- Kepentingan counts --}}
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['i_sp'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['i_p'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['i_tp'] }}</td>
                        <td style="text-align:center;border:1px solid #cbd5e1;padding:8px 6px;">{{ $row['i_stp'] }}</td>
                        <td style="text-align:center;border:1px solid #94a3b8;padding:8px 6px;background:#94a3b8;font-weight:800;color:#0f172a;">
                            {{ number_format($row['i_nilai'], 2, ',', '.') }}
                        </td>
                        {{-- TP % --}}
                        <td style="text-align:center;border:1px solid #94a3b8;padding:8px 6px;background:#22c55e;color:#ffffff;font-weight:800;font-size:0.88rem;">
                            {{ number_format($row['tp_persen'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" style="text-align:center;padding:40px;color:var(--text-muted);border:1px solid #cbd5e1;">
                            Tidak ada data survei untuk semester ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($analysisData) > 0)
                <tfoot>
                    <tr style="background:#f1f5f9;font-weight:800;">
                        <td colspan="2" style="text-align:center;border:1px solid #94a3b8;padding:10px;text-transform:uppercase;">
                            Total / Rata-rata
                        </td>
                        <td colspan="4" style="border:1px solid #cbd5e1;background:#f8fafc;"></td>
                        <td style="text-align:center;border:1px solid #94a3b8;padding:10px 6px;background:#64748b;color:#ffffff;font-size:0.9rem;">
                            {{ number_format($totalNilaiKinerja, 2, ',', '.') }}
                        </td>
                        <td colspan="4" style="border:1px solid #cbd5e1;background:#f8fafc;"></td>
                        <td style="text-align:center;border:1px solid #94a3b8;padding:10px 6px;background:#64748b;color:#ffffff;font-size:0.9rem;">
                            {{ number_format($totalNilaiKepentingan, 2, ',', '.') }}
                        </td>
                        <td style="text-align:center;border:1px solid #94a3b8;padding:10px 6px;background:#16a34a;color:#ffffff;font-size:0.92rem;">
                            {{ number_format($avgTpPersen, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

