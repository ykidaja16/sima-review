@extends('layouts.app')
@section('title', 'Master Jabatan')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Master Jabatan</h2><p>Kelola jabatan dan level hierarki</p></div>
    <a href="{{ route('master.jabatan.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Jabatan</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Jabatan</th><th>Level</th><th>Deskripsi</th><th>Status</th><th>Karyawan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($jabatans as $j)
                <tr>
                    <td>{{ $jabatans->firstItem() + $loop->index }}</td>
                    <td style="font-weight:600;">{{ $j->nama }}</td>
                    <td><span class="badge badge-info">Level {{ $j->level }}</span></td>
                    <td style="color:var(--text-muted);font-size:0.85rem;">{{ Str::limit($j->deskripsi, 50) ?? '—' }}</td>
                    <td>
                        @if($j->is_active)
                        <span class="badge badge-success">Aktif</span>
                        @else
                        <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>{{ $j->karyawans->count() }}</td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('master.jabatan.edit', $j) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.jabatan.destroy', $j) }}" method="POST"
                            onsubmit="return confirm('Hapus jabatan {{ $j->nama }}?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data jabatan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($jabatans->hasPages())<div class="card-footer">{{ $jabatans->links() }}</div>@endif
</div>
@endsection
