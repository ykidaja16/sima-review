@extends('layouts.app')
@section('title', 'FTKP / Ketidaksesuaian')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>FTKP / Ketidaksesuaian</h2>
        <p>Formulir Tindakan Korektif dan Perbaikan</p>
    </div>
    @if($karyawan)
    <a href="{{ route('ketidaksesuaian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i> Buat FTKP Baru
    </a>
    @endif
</div>

{{-- Filter FTKP --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h5><i class="bi bi-funnel-fill" style="color:#3b82f6;margin-right:6px;"></i>Filter Data FTKP</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('ketidaksesuaian.index') }}">
            <div class="grid grid-3" style="margin-bottom:12px;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Divisi Pelapor</label>
                    <select name="divisi_pelapor_id" class="form-select searchable-select" data-placeholder="Cari divisi pelapor...">
                        <option value="">Semua Divisi Pelapor</option>
                        @foreach($divisis as $d)
                        <option value="{{ $d->id }}" {{ request('divisi_pelapor_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Karyawan Pelapor</label>
                    <select name="pelapor_id" class="form-select searchable-select" data-placeholder="Cari nama karyawan pelapor...">
                        <option value="">Semua Pelapor</option>
                        @foreach($pelapors as $pel)
                        <option value="{{ $pel->id }}" {{ request('pelapor_id') == $pel->id ? 'selected' : '' }}>
                            {{ $pel->nama }} ({{ $pel->divisi->nama ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Ditujukan Ke</label>
                    <select name="divisi_tujuan_id" class="form-select searchable-select" data-placeholder="Cari divisi tujuan...">
                        <option value="">Semua Divisi Tujuan</option>
                        @foreach($divisis as $d)
                        <option value="{{ $d->id }}" {{ request('divisi_tujuan_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Kategori</label>
                    <select name="kategori_temuan" class="form-select searchable-select" data-placeholder="Cari kategori temuan...">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriList as $katItem)
                        <option value="{{ $katItem }}" {{ request('kategori_temuan') === $katItem ? 'selected' : '' }}>
                            {{ match(strtolower((string) $katItem)) { 'ok' => 'OK', 'observasi' => 'Observasi', 'nc' => 'NC (Ketidaksesuaian)', default => $katItem } }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Filter Status</label>
                    <select name="status" class="form-select searchable-select" data-placeholder="Cari status...">
                        <option value="">Semua Status</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="verifikasi_manager" {{ request('status') === 'verifikasi_manager' ? 'selected' : '' }}>Verifikasi Manager</option>
                        <option value="verifikasi_kacab" {{ request('status') === 'verifikasi_kacab' ? 'selected' : '' }}>Verifikasi Kacab</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline">Reset</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. FTKP</th>
                        <th>Tanggal</th>
                        <th>Pelapor</th>
                        <th>Ditujukan Ke</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ketidaksesuaians as $k)
                    <tr>
                        <td>
                            <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:0.82rem;">{{ $k->nomor_ftkp }}</code>
                        </td>
                        <td style="font-size:0.85rem;">{{ $k->tanggal_laporan->format('d/m/Y') }}</td>
                        <td style="font-size:0.85rem;">
                            <div style="font-weight:600;">{{ $k->pelapor->nama }}</div>
                            <div style="color:var(--text-muted);font-size:0.78rem;">{{ $k->divisiPelapor->nama }}</div>
                        </td>
                        <td style="font-size:0.85rem;color:var(--text-muted);">{{ $k->divisiTujuan->nama }}</td>
                        <td>
                            <span class="badge badge-{{ $k->kategori_color }}">{{ $k->kategori_label }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $k->status_color }}">{{ $k->status_label }}</span>
                        </td>
                        <td>
                            <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                <a href="{{ route('ketidaksesuaian.show', $k) }}" class="btn btn-info btn-sm" title="Detail FTKP">
                                    <i class="bi bi-eye-fill"></i> Detail
                                </a>
                                <a href="{{ route('ketidaksesuaian.export-pdf', $k) }}" class="btn btn-danger btn-sm" title="Export PDF" target="_blank">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </a>
                                <a href="{{ route('ketidaksesuaian.export-excel', $k) }}" class="btn btn-success btn-sm" title="Export Excel">
                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                </a>
                                @if(auth()->user()?->isSuperAdmin())
                                <form action="{{ route('ketidaksesuaian.destroy', $k) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus FTKP {{ $k->nomor_ftkp }}? Tindakan ini tidak dapat dibatalkan.')"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus FTKP">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:40px;color:var(--text-muted);">
                        <i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data ketidaksesuaian
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ketidaksesuaians->hasPages())
    <div class="card-footer">{{ $ketidaksesuaians->links() }}</div>
    @endif
</div>
@endsection
