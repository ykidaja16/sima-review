@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>User Management</h2>
        <p>Kelola akun pengguna sistem SIMA-REVIEW</p>
    </div>
    <a href="{{ route('user-management.index', ['action' => 'create']) }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah User
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>#</th><th>Username</th><th>Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="font-weight:700;font-family:monospace;">{{ $u->username }}</div>
                        @if($u->id === auth()->id())
                        <span style="font-size:0.68rem;background:rgba(59,130,246,0.1);color:#2563eb;padding:1px 6px;border-radius:4px;font-weight:600;">Anda</span>
                        @endif
                    </td>
                    <td style="font-weight:600;">{{ $u->name }}</td>
                    <td style="color:var(--text-muted);font-size:0.85rem;">{{ $u->email ?? '—' }}</td>
                    <td>
                        <span class="badge badge-{{ match($u->role?->slug) {
                            'super_admin' => 'danger',
                            'manager' => 'primary',
                            'supervisor' => 'warning',
                            default => 'secondary'
                        } }}">{{ $u->role?->nama ?? '—' }}</span>
                    </td>
                    <td>
                        @if($u->is_active)
                        <span class="badge badge-success"><i class="bi bi-check-circle"></i> Aktif</span>
                        @else
                        <span class="badge badge-danger"><i class="bi bi-x-circle"></i> Nonaktif</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('user-management.edit', $u) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('user-management.destroy', $u) }}" method="POST"
                                onsubmit="return confirm('Hapus user {{ $u->username }}?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
