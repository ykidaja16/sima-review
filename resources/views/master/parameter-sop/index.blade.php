@extends('layouts.app')
@section('title', 'Parameter SOP')
@section('content')

<div class="page-header">
    <div class="page-title">
        <h2>Parameter SOP</h2>
        <p>Kelola parameter evaluasi berbobot</p>
    </div>
    <a href="{{ route('master.parameter-sop.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Parameter</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Parameter</th><th>Kategori</th><th>Bobot</th><th>Deskripsi</th><th>Urutan</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($parameters as $p)
                <tr>
                    <td>{{ $parameters->firstItem() + $loop->index }}</td>
                    <td style="font-weight:600;">{{ $p->nama }}</td>
                    <td><span class="badge badge-info">{{ $p->kategori }}</span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="height:6px;background:var(--border);border-radius:3px;width:50px;overflow:hidden;">
                                <div style="height:100%;background:#3b82f6;border-radius:3px;width:{{ min($p->bobot * 10, 100) }}%"></div>
                            </div>
                            <span style="font-weight:700;">{{ $p->bobot }}</span>
                        </div>
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ Str::limit($p->deskripsi, 50) ?? '—' }}</td>
                    <td>{{ $p->urutan }}</td>
                    <td>
                        @if($p->is_active)
                        <span class="badge badge-success">Aktif</span>
                        @else
                        <span class="badge badge-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('master.parameter-sop.edit', $p) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.parameter-sop.destroy', $p) }}" method="POST"
                            onsubmit="return confirm('Hapus parameter {{ $p->nama }}?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada parameter SOP</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($parameters->hasPages())<div class="card-footer">{{ $parameters->links() }}</div>@endif
</div>
@endsection
