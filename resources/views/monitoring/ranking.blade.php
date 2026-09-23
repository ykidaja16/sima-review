@extends('layouts.app')
@section('title', 'Ranking Karyawan')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Ranking Karyawan</h2>
        <p>Peringkat berdasarkan nilai rata-rata penilaian</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:200px;">
                <label class="form-label">Periode</label>
                <select name="periode_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:160px;">
                <label class="form-label">Filter Divisi</label>
                <select name="divisi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Top 3 Podium --}}
@if($ranking->count() >= 3)
<div style="display:flex;justify-content:center;align-items:flex-end;gap:16px;margin-bottom:28px;padding:20px;">
    {{-- 2nd --}}
    @php $second = $ranking->get(1); @endphp
    <div style="text-align:center;flex:1;max-width:200px;">
        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#94a3b8,#64748b);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:white;margin:0 auto 8px;">
            {{ substr($second->nama, 0, 1) }}
        </div>
        <div style="font-weight:700;font-size:0.9rem;">{{ $second->nama }}</div>
        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $second->divisi->nama ?? '-' }}</div>
        <div style="font-size:1.6rem;font-weight:800;color:#64748b;margin:4px 0;">{{ number_format($second->avg_nilai ?? 0, 1) }}</div>
        <div style="background:linear-gradient(135deg,#94a3b8,#64748b);padding:20px 0 8px;border-radius:8px 8px 0 0;color:white;font-weight:700;font-size:1.2rem;">🥈</div>
    </div>

    {{-- 1st --}}
    @php $first = $ranking->get(0); @endphp
    <div style="text-align:center;flex:1;max-width:220px;transform:scale(1.05);">
        <div style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#ef4444);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:white;margin:0 auto 8px;box-shadow:0 8px 24px rgba(245,158,11,0.4);">
            {{ substr($first->nama, 0, 1) }}
        </div>
        <div style="font-weight:700;font-size:1rem;">{{ $first->nama }}</div>
        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $first->divisi->nama ?? '-' }}</div>
        <div style="font-size:2rem;font-weight:800;color:#d97706;margin:4px 0;">{{ number_format($first->avg_nilai ?? 0, 1) }}</div>
        <div style="background:linear-gradient(135deg,#f59e0b,#d97706);padding:32px 0 8px;border-radius:8px 8px 0 0;color:white;font-weight:700;font-size:1.5rem;">🥇</div>
    </div>

    {{-- 3rd --}}
    @php $third = $ranking->get(2); @endphp
    <div style="text-align:center;flex:1;max-width:200px;">
        <div style="width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#92400e,#d97706);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:white;margin:0 auto 8px;">
            {{ substr($third->nama, 0, 1) }}
        </div>
        <div style="font-weight:700;font-size:0.9rem;">{{ $third->nama }}</div>
        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $third->divisi->nama ?? '-' }}</div>
        <div style="font-size:1.6rem;font-weight:800;color:#92400e;margin:4px 0;">{{ number_format($third->avg_nilai ?? 0, 1) }}</div>
        <div style="background:linear-gradient(135deg,#92400e,#b45309);padding:12px 0 8px;border-radius:8px 8px 0 0;color:white;font-weight:700;font-size:1.2rem;">🥉</div>
    </div>
</div>
@endif

{{-- Tabel lengkap --}}
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Ranking</th><th>Karyawan</th><th>Divisi</th><th>Jabatan</th><th>Rata-rata Nilai</th><th>Jml Periode</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($ranking as $i => $k)
                @php
                    $avg = $k->avg_nilai ?? 0;
                    $colorClass = $avg >= 88 ? 'success' : ($avg >= 63 ? 'primary' : ($avg >= 38 ? 'warning' : 'danger'));
                    $medal = match($i) { 0 => '🥇', 1 => '🥈', 2 => '🥉', default => '' };
                @endphp
                <tr class="{{ $i < 3 ? 'rank-top-3' : '' }}">
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-weight:700;font-size:1rem;">{{ $i + 1 }}</span>
                            @if($medal)<span style="font-size:1.1rem;">{{ $medal }}</span>@endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $k->nama }}</div>
                        @if($k->nip)<div style="font-size:0.75rem;color:var(--text-muted);">{{ $k->nip }}</div>@endif
                    </td>
                    <td>{{ $k->divisi->nama ?? '—' }}</td>
                    <td>{{ $k->jabatan->nama ?? '—' }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="badge badge-{{ $colorClass }}" style="font-size:0.85rem;padding:4px 10px;">
                                {{ number_format($avg, 2) }}
                            </span>
                        </div>
                    </td>
                    <td>{{ $k->penilaians_count ?? 0 }} periode</td>
                    <td>
                        <a href="{{ route('monitoring.individu', ['karyawan_id' => $k->id]) }}" class="btn btn-outline btn-sm">
                            <i class="bi bi-graph-up"></i> Track Record
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data penilaian</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
