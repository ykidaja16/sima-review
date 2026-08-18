@extends('layouts.app')
@section('title', 'Track Record Individu')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Track Record Individu</h2>
        <p>Riwayat penilaian per karyawan lintas periode</p>
    </div>
</div>

{{-- Filter --}}
@if(!auth()->user()->isPelaksana())
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:200px;">
                <label class="form-label">Pilih Karyawan</label>
                <select name="karyawan_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                    <option value="{{ $k->id }}" {{ request('karyawan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nip }} — {{ $k->nama }} ({{ $k->divisi->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
            </div>
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
                @php $avg = $penilaians->avg('nilai_akhir'); @endphp
                <div style="font-size:1.8rem;font-weight:800;color:{{ $avg>=90?'#059669':($avg>=75?'#2563eb':($avg>=60?'#d97706':'#dc2626')) }};">
                    {{ $avg ? number_format($avg, 2) : '—' }}
                </div>
                <div style="font-size:0.75rem;color:var(--text-muted);">Rata-rata</div>
            </div>
            @if($penilaians->count() > 0)
            <div style="width:1px;background:var(--border);"></div>
            <div>
                <div style="font-size:1.8rem;font-weight:800;color:#059669;">{{ number_format($penilaians->max('nilai_akhir'), 1) }}</div>
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
<div class="card">
    <div class="card-header"><h5><i class="bi bi-clock-history" style="color:#0284c7;margin-right:6px;"></i>Riwayat Penilaian</h5></div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Periode</th><th>Tanggal</th><th>Evaluator</th><th>Nilai Akhir</th><th>Kategori</th><th>Catatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($penilaians as $p)
                @php $kat = $p->kategori; @endphp
                <tr>
                    <td style="font-weight:600;">{{ $p->periode->nama ?? '-' }}</td>
                    <td>{{ $p->tanggal_penilaian?->format('d M Y') }}</td>
                    <td>{{ $p->evaluator->name ?? '-' }}</td>
                    <td>
                        <span style="font-size:1.1rem;font-weight:800;color:{{ $kat?->warna=='success'?'#059669':($kat?->warna=='primary'?'#2563eb':($kat?->warna=='warning'?'#d97706':'#dc2626')) }}">
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
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<div class="card">
    <div class="card-body" style="text-align:center;padding:48px;">
        <i class="bi bi-person-x" style="font-size:3rem;color:var(--text-muted);display:block;margin-bottom:12px;"></i>
        <h5>Pilih Karyawan</h5>
        <p style="color:var(--text-muted);margin-top:8px;">Pilih karyawan dari dropdown di atas untuk melihat track record penilaiannya.</p>
    </div>
</div>
@endif

@endsection

@if($penilaians->count() > 1)
@push('scripts')
<script>
const labels  = @json($penilaians->pluck('periode.nama'));
const values  = @json($penilaians->pluck('nilai_akhir'));
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
