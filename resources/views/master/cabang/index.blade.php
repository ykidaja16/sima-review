@extends('layouts.app')
@section('title', 'Master Cabang')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Master Cabang</h2>
        <p>Kelola daftar cabang perusahaan</p>
    </div>
    <a href="{{ route('master.cabang.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i> Tambah Cabang
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama Cabang</th>
                        <th>Alamat</th>
                        <th>Telepon</th>
                        <th>Karyawan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cabangs as $c)
                    <tr>
                        <td>{{ $cabangs->firstItem() + $loop->index }}</td>
                        <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:0.82rem;">{{ $c->kode }}</code></td>
                        <td><strong>{{ $c->nama }}</strong></td>
                        <td style="color:var(--text-muted);font-size:0.85rem;">{{ $c->alamat ?: '-' }}</td>
                        <td style="color:var(--text-muted);font-size:0.85rem;">{{ $c->telepon ?: '-' }}</td>
                        <td><span class="badge badge-primary">{{ $c->karyawans_count }} karyawan</span></td>
                        <td>
                            @if($c->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('master.cabang.edit', $c) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('master.cabang.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus cabang {{ $c->nama }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center" style="padding:32px;color:var(--text-muted);">
                        <i class="bi bi-building" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data cabang
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($cabangs->hasPages())
    <div class="card-footer">{{ $cabangs->links() }}</div>
    @endif
</div>
@endsection
