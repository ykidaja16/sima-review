@extends('layouts.app')
@section('title', 'Master Divisi')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Master Divisi</h2>
        <p>Kelola divisi/unit kerja dalam organisasi</p>
    </div>
    <a href="{{ route('master.divisi.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Divisi
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Kode</th><th>Nama Divisi</th><th>Deskripsi</th><th>Status</th><th>Jumlah Karyawan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($divisis as $d)
                <tr>
                    <td>{{ $divisis->firstItem() + $loop->index }}</td>
                    <td><span class="badge badge-primary">{{ $d->kode }}</span></td>
                    <td style="font-weight:600;">{{ $d->nama }}</td>
                    <td style="color:var(--text-muted);font-size:0.85rem;">{{ Str::limit($d->deskripsi, 50) ?? '—' }}</td>
                    <td>
                        @if($d->is_active)
                        <span class="badge badge-success"><i class="bi bi-check-circle"></i> Aktif</span>
                        @else
                        <span class="badge badge-danger"><i class="bi bi-x-circle"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td>{{ $d->karyawans->count() }} karyawan</td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('master.divisi.edit', $d) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.divisi.destroy', $d) }}" method="POST"
                            onsubmit="return confirm('Hapus divisi {{ $d->nama }}?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data divisi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($divisis->hasPages())
    <div class="card-footer">{{ $divisis->links() }}</div>
    @endif
</div>
@endsection
