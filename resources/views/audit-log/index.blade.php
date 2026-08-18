@extends('layouts.app')
@section('title', 'Audit Log')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Audit Log</h2>
        <p>Riwayat aktivitas seluruh pengguna sistem</p>
    </div>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:180px;">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->username }})</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:2;min-width:200px;">
                <label class="form-label">Cari Aktivitas</label>
                <input type="text" name="aktivitas" class="form-control" value="{{ request('aktivitas') }}" placeholder="Contoh: LOGIN, CREATE_USER...">
            </div>
            <div style="flex:1;min-width:140px;">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
            </div>
            <div style="flex:1;min-width:140px;">
                <label class="form-label">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('audit-log.index') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Waktu</th><th>User</th><th>Role</th><th>Aktivitas</th><th>Model</th><th>Record ID</th><th>IP Address</th></tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="white-space:nowrap;font-size:0.8rem;">
                        <div style="font-weight:600;">{{ $log->created_at->format('d M Y') }}</div>
                        <div style="color:var(--text-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="font-weight:600;">{{ $log->user->name ?? '—' }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">@{{ $log->user->username ?? '' }}</div>
                    </td>
                    <td>
                        @if($log->user)
                        <span class="badge badge-{{ match($log->user->role?->slug) {
                            'super_admin' => 'danger',
                            'manager' => 'primary',
                            'supervisor' => 'warning',
                            default => 'secondary'
                        } }}">{{ $log->user->role?->nama ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeColor = match(true) {
                                str_contains($log->aktivitas, 'LOGIN')  => 'badge-success',
                                str_contains($log->aktivitas, 'LOGOUT') => 'badge-secondary',
                                str_contains($log->aktivitas, 'CREATE') => 'badge-primary',
                                str_contains($log->aktivitas, 'UPDATE') => 'badge-warning',
                                str_contains($log->aktivitas, 'DELETE') => 'badge-danger',
                                default => 'badge-info',
                            };
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $log->aktivitas }}</span>
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ $log->model_type ?? '—' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">#{{ $log->model_id ?? '—' }}</td>
                    <td style="font-size:0.78rem;color:var(--text-muted);font-family:monospace;">{{ $log->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="bi bi-clock-history" style="font-size:2.5rem;display:block;margin-bottom:12px;"></i>
                        Tidak ada log aktivitas untuk filter ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:0.8rem;color:var(--text-muted);">
            Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} log
        </div>
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
