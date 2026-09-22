@extends('layouts.app')
@section('title', 'Jenis Ketidaksesuaian')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Jenis Ketidaksesuaian</h2>
        <p>Master jenis/kategori untuk formulir FTKP</p>
    </div>
    <a href="{{ route('master.jenis-ketidaksesuaian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i> Tambah Jenis
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Jenis</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jenis as $j)
                    <tr>
                        <td>{{ $jenis->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $j->nama }}</strong></td>
                        <td style="color:var(--text-muted);font-size:0.85rem;">{{ $j->deskripsi ?: '-' }}</td>
                        <td>
                            @if($j->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('master.jenis-ketidaksesuaian.edit', $j) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('master.jenis-ketidaksesuaian.destroy', $j) }}" method="POST" onsubmit="return confirm('Hapus jenis ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center" style="padding:32px;color:var(--text-muted);">
                        Belum ada data jenis ketidaksesuaian
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($jenis->hasPages())
    <div class="card-footer">{{ $jenis->links() }}</div>
    @endif
</div>
@endsection
