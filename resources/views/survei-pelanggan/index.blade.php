@extends('layouts.app')
@section('title', 'Survei Pelanggan')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Survei Pelanggan</h2>
        <p>Data survei kepuasan pelanggan — diperbarui otomatis dari Google Sheets</p>
    </div>
    <div style="display:flex;align-items:center;gap:8px;">
        <span style="font-size:0.78rem;color:var(--text-muted);background:var(--bg);padding:5px 10px;border-radius:6px;border:1px solid var(--border);">
            <i class="bi bi-arrow-repeat"></i> Cache 5 menit
        </span>
    </div>
</div>

{{-- Error state --}}
@if($error)
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle-fill"></i>
    {{ $error }}
</div>
@endif

{{-- Search bar --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body" style="padding:12px 16px;">
        <form method="GET" action="{{ route('survei-pelanggan.index') }}" style="display:flex;gap:10px;align-items:center;">
            <div style="flex:1;position:relative;">
                <i class="bi bi-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:0.85rem;"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari di semua kolom..."
                    class="form-control" style="padding-left:32px;">
            </div>
            <button type="submit" class="btn btn-primary">Cari</button>
            @if($search)
            <a href="{{ route('survei-pelanggan.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats + table --}}
<div class="card">
    <div class="card-header" style="justify-content:space-between;">
        <h5><i class="bi bi-star-fill" style="color:#f59e0b;margin-right:6px;"></i>Data Survei</h5>
        <span style="font-size:0.82rem;color:var(--text-muted);">
            Menampilkan
            @if($total > 0)
                {{ (($page-1)*50)+1 }}–{{ min($page*50, $total) }} dari <strong>{{ $total }}</strong> data
            @else
                0 data
            @endif
            @if($search) &nbsp;(filter: "<em>{{ $search }}</em>") @endif
        </span>
    </div>

    @if(empty($headers) && !$error)
    <div class="card-body" style="text-align:center;padding:60px;color:var(--text-muted);">
        <i class="bi bi-table" style="font-size:2.5rem;display:block;margin-bottom:12px;opacity:.4;"></i>
        <p>Belum ada data</p>
    </div>
    @elseif(!empty($headers))
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px;text-align:center;">#</th>
                        @foreach($headers as $h)
                        <th>{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagedRows as $i => $row)
                    <tr>
                        <td style="text-align:center;color:var(--text-muted);font-size:0.8rem;">
                            {{ (($page-1)*50) + $i + 1 }}
                        </td>
                        @foreach($row as $cell)
                        <td style="font-size:0.85rem;white-space:nowrap;max-width:220px;overflow:hidden;text-overflow:ellipsis;" title="{{ $cell }}">
                            {{ $cell ?: '—' }}
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($headers) + 1 }}" class="text-center" style="padding:40px;color:var(--text-muted);">
                            <i class="bi bi-search" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4;"></i>
                            Tidak ada data yang cocok dengan pencarian "{{ $search }}"
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($totalPages > 1)
    <div class="card-footer">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
            <span style="font-size:0.82rem;color:var(--text-muted);">Halaman {{ $page }} dari {{ $totalPages }}</span>
            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                {{-- Prev --}}
                @if($page > 1)
                <a href="{{ route('survei-pelanggan.index', ['page' => $page-1, 'search' => $search]) }}"
                   class="btn btn-outline btn-sm"><i class="bi bi-chevron-left"></i></a>
                @endif

                {{-- Page numbers --}}
                @php
                    $start = max(1, $page - 2);
                    $end   = min($totalPages, $page + 2);
                @endphp
                @if($start > 1)
                    <a href="{{ route('survei-pelanggan.index', ['page' => 1, 'search' => $search]) }}"
                       class="btn btn-outline btn-sm">1</a>
                    @if($start > 2)<span style="padding:4px 6px;color:var(--text-muted);">…</span>@endif
                @endif
                @for($p = $start; $p <= $end; $p++)
                    <a href="{{ route('survei-pelanggan.index', ['page' => $p, 'search' => $search]) }}"
                       class="btn btn-sm {{ $p == $page ? 'btn-primary' : 'btn-outline' }}">{{ $p }}</a>
                @endfor
                @if($end < $totalPages)
                    @if($end < $totalPages - 1)<span style="padding:4px 6px;color:var(--text-muted);">…</span>@endif
                    <a href="{{ route('survei-pelanggan.index', ['page' => $totalPages, 'search' => $search]) }}"
                       class="btn btn-outline btn-sm">{{ $totalPages }}</a>
                @endif

                {{-- Next --}}
                @if($page < $totalPages)
                <a href="{{ route('survei-pelanggan.index', ['page' => $page+1, 'search' => $search]) }}"
                   class="btn btn-outline btn-sm"><i class="bi bi-chevron-right"></i></a>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection
