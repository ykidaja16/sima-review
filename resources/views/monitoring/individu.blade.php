@extends('layouts.app')
@section('title', !empty($isSaya) ? 'Track Record Saya' : 'Track Record Individu')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>{{ !empty($isSaya) ? 'Track Record Saya' : 'Track Record Individu' }}</h2>
        <p>{{ !empty($isSaya) ? 'Riwayat hasil penilaian Service Excellence Anda lintas periode' : 'Riwayat penilaian per karyawan lintas periode' }}</p>
    </div>
</div>

{{-- Filter --}}
@if(empty($isSaya))
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('monitoring.individu') }}" id="formTrackRecordIndividu" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Filter Divisi</label>
                <select name="divisi_id" id="filterDivisiTrack" class="form-select searchable-select" data-placeholder="Cari divisi...">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2;min-width:280px;">
                <label class="form-label">Pilih Karyawan Dinilai</label>
                <select name="karyawan_id" id="filterKaryawanTrack" class="form-select searchable-select" data-placeholder="Cari nama atau NIP karyawan...">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                    <option value="{{ $k->id }}"
                        data-divisi="{{ $k->divisi_id }}"
                        {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nip ? ($k->nip . ' — ') : '' }}{{ $k->nama }} ({{ $k->divisi->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Tampilkan</button>
            <a href="{{ route('monitoring.individu') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>
@endif

@if($karyawan)
{{-- Profil card --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div style="width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:white;flex-shrink:0;">
            {{ substr($karyawan->nama, 0, 1) }}
        </div>
        <div style="flex:1;min-width:200px;">
            <div style="font-size:1.15rem;font-weight:700;">{{ $karyawan->nama }}</div>
            <div style="font-size:0.85rem;color:var(--text-muted);">
                NIP: {{ $karyawan->nip }} &nbsp;|&nbsp; {{ $karyawan->jabatan->nama ?? '-' }} &nbsp;|&nbsp; {{ $karyawan->divisi->nama ?? '-' }}
            </div>
            @if($karyawan->atasan)
            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:2px;">
                <i class="bi bi-person-up"></i> Atasan: {{ $karyawan->atasan->nama }}
            </div>
            @endif
        </div>
        <div style="display:flex;gap:16px;text-align:center;">
            <div>
                <div style="font-size:1.8rem;font-weight:800;color:var(--primary);">{{ $penilaians->count() }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);">Total Periode</div>
            </div>
            <div style="width:1px;background:var(--border);"></div>
            <div>
                @php
                    $avg = $penilaians->avg('nilai_akhir');
                    $avgColorClass = $avg>=90 ? 'text-score-success' : ($avg>=75 ? 'text-score-primary' : ($avg>=60 ? 'text-score-warning' : 'text-score-danger'));
                @endphp
                <div class="{{ $avgColorClass }}" style="font-size:1.8rem;font-weight:800;">
                    {{ $avg ? number_format($avg, 2) : '—' }}
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);">Rata-rata</div>
            </div>
            @if($penilaians->count() > 0)
            <div style="width:1px;background:var(--border);"></div>
            <div>
                <div class="text-score-success" style="font-size:1.8rem;font-weight:800;">{{ number_format($penilaians->max('nilai_akhir'), 1) }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted);">Tertinggi</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Chart Trend --}}
@if($penilaians->count() > 1)
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h5><i class="bi bi-graph-up" style="color:#3b82f6;margin-right:6px;"></i>Grafik Trend Nilai</h5></div>
    <div class="card-body"><canvas id="trendChart" height="120"></canvas></div>
</div>
@endif

{{-- Tabel Riwayat --}}
@php
    $canSeeEvaluator = auth()->user()->isKacab() || auth()->user()->isSuperAdmin();
@endphp
<div class="card">
    <div class="card-header"><h5><i class="bi bi-clock-history" style="color:#0284c7;margin-right:6px;"></i>Riwayat Penilaian</h5></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    @if($canSeeEvaluator)
                    <th>Evaluator</th>
                    @endif
                    <th>Nilai Akhir</th>
                    <th>Kategori</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penilaians as $p)
                @php $kat = $p->kategori; @endphp
                <tr>
                    <td style="font-weight:600;">{{ $p->periode->nama ?? '-' }}</td>
                    <td>{{ $p->tanggal_penilaian?->format('d M Y') }}</td>
                    @if($canSeeEvaluator)
                    <td>{{ $p->evaluator->name ?? '-' }}</td>
                    @endif
                    <td>
                        <span class="text-score-{{ $kat?->warna ?? 'secondary' }}" style="font-size:1.1rem;font-weight:800;">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </span>
                    </td>
                    <td>
                        @if($kat)
                        <span class="badge badge-{{ $kat->warna }}">{{ $kat->nama }}</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);max-width:200px;">{{ $p->catatan ? Str::limit($p->catatan, 60) : '—' }}</td>
                    <td>
                        <a href="{{ route('penilaian.show', $p) }}" class="btn btn-info btn-sm"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="{{ $canSeeEvaluator ? 7 : 6 }}" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px 16px;color:var(--text-muted);">
        <i class="bi bi-person-x" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:12px;opacity:0.6;"></i>
        <h5>Tidak Ada Data Karyawan</h5>
        <p style="color:var(--text-muted);margin-top:8px;">Pilih karyawan dari filter di atas atau tidak ada karyawan yang sesuai kriteria.</p>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterDivisiTrack = document.getElementById('filterDivisiTrack');
    const filterKaryawanTrack = document.getElementById('filterKaryawanTrack');

    function syncKaryawanOptions(isUserAction = false) {
        if (!filterKaryawanTrack) return;
        const divId = filterDivisiTrack ? filterDivisiTrack.value : '';

        Array.from(filterKaryawanTrack.options).forEach(opt => {
            if (!opt.value) return;
            const match = !divId || opt.dataset.divisi === divId;
            opt.hidden = !match;
        });

        if (isUserAction && filterKaryawanTrack.selectedOptions[0]?.hidden) {
            filterKaryawanTrack.value = '';
        }

        if (typeof filterKaryawanTrack._refreshSearchableSelect === 'function') {
            filterKaryawanTrack._refreshSearchableSelect();
        }
    }

    if (filterDivisiTrack) filterDivisiTrack.addEventListener('change', () => syncKaryawanOptions(true));

    syncKaryawanOptions(false);

    const formTrack = document.getElementById('formTrackRecordIndividu');
    if (formTrack) {
        formTrack.addEventListener('submit', function() {
            if (typeof filterKaryawanTrack._syncFromInput === 'function') {
                filterKaryawanTrack._syncFromInput();
            }
        });
    }
});
</script>
@endpush

@if($penilaians->count() > 1)
<script id="trendDataJson" type="application/json">
{!! json_encode(['labels' => $penilaians->pluck('periode.nama'), 'values' => $penilaians->pluck('nilai_akhir')]) !!}
</script>
@push('scripts')
<script>
const trendData = JSON.parse(document.getElementById('trendDataJson').textContent || '{}');
const labels  = trendData.labels || [];
const values  = trendData.values || [];
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Nilai Akhir',
            data: values,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            borderWidth: 2.5,
            pointRadius: 5,
            pointBackgroundColor: '#3b82f6',
            tension: 0.3,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 100, ticks: { font: { family: 'Inter', size: 11 } }, grid: { color: 'rgba(226,232,240,0.5)' } },
            x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
        }
    }
});
</script>
@endpush
@endif
