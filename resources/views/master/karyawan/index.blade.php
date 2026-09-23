@extends('layouts.app')
@section('title', 'Master Karyawan')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Master Karyawan</h2><p>Kelola data karyawan</p></div>
    <a href="{{ route('master.karyawan.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Karyawan</a>
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:200px;">
                <label class="form-label">Cari Nama / NIP</label>
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nama atau NIP...">
            </div>
            <div style="flex:1;min-width:150px;">
                <label class="form-label">Divisi</label>
                <select name="divisi_id" class="form-select">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ request('divisi_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:120px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
                    <option value="nonaktif" {{ request('status')=='nonaktif'?'selected':'' }}>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('master.karyawan.index') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>NIP</th><th>Nama</th><th>Jabatan</th><th>Divisi</th><th>Atasan</th><th>Akun</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($karyawans as $k)
                <tr>
                    <td>{{ $karyawans->firstItem() + $loop->index }}</td>
                    <td style="font-family:monospace;">{{ $k->nip ?: '—' }}</td>
                    <td style="font-weight:600;">{{ $k->nama }}</td>
                    <td>{{ $k->jabatan->nama ?? '—' }}</td>
                    <td>{{ $k->divisi->nama ?? '—' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ $k->atasan->nama ?? '—' }}</td>
                    <td>
                        @if($k->user)
                        <span class="badge badge-success"><i class="bi bi-person-check"></i> Ya</span>
                        @else
                        <span class="badge badge-secondary">Tidak</span>
                        @endif
                    </td>
                    <td>
                        @if($k->is_active)
                        <span class="badge badge-success">Aktif</span>
                        @else
                        <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('master.karyawan.show', $k) }}" class="btn btn-info btn-sm" title="Detail"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('master.karyawan.edit', $k) }}" class="btn btn-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.karyawan.destroy', $k) }}" method="POST"
                            onsubmit="return confirm('Hapus karyawan {{ $k->nama }}?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data karyawan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($karyawans->hasPages())
    <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:0.8rem;color:var(--text-muted);">Total: {{ $karyawans->total() }} karyawan</div>
        {{ $karyawans->links() }}
    </div>
    @endif
</div>
@endsection
