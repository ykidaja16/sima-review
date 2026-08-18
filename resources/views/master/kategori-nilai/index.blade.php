@extends('layouts.app')
@section('title', 'Kategori Nilai')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Kategori Nilai</h2><p>Kelola kategori predikat penilaian</p></div>
    <a href="{{ route('master.kategori-nilai.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Kategori</a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Nama Kategori</th><th>Range Nilai</th><th>Warna</th><th>Urutan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($kategoris as $k)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight:600;">
                        <span class="badge badge-{{ $k->warna }}" style="font-size:0.9rem;padding:4px 12px;">{{ $k->nama }}</span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-weight:700;color:#059669;">{{ $k->nilai_min }}</span>
                            <span style="color:var(--text-muted);">—</span>
                            <span style="font-weight:700;color:#2563eb;">{{ $k->nilai_max }}</span>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:20px;height:20px;border-radius:5px;background:{{ match($k->warna) {
                                'success' => '#059669', 'primary' => '#2563eb',
                                'warning' => '#d97706', 'danger' => '#dc2626', default => '#64748b'
                            } }};"></div>
                            <span style="font-size:0.8rem;color:var(--text-muted);">{{ $k->warna }}</span>
                        </div>
                    </td>
                    <td>{{ $k->urutan }}</td>
                    <td style="display:flex;gap:4px;">
                        <a href="{{ route('master.kategori-nilai.edit', $k) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.kategori-nilai.destroy', $k) }}" method="POST"
                            onsubmit="return confirm('Hapus kategori {{ $k->nama }}?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada kategori nilai</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
