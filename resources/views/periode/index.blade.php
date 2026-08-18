@extends('layouts.app')
@section('title', 'Periode Penilaian')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Periode Penilaian</h2>
        <p>Kelola periode evaluasi dua mingguan</p>
    </div>
    <a href="{{ route('periode.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Buat Periode Baru
    </a>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Periode</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Durasi</th>
                    <th>Status</th>
                    <th>Total Penilaian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $p)
                <tr>
                    <td>{{ $periodes->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $p->nama }}</div>
                        @if($p->keterangan)
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ Str::limit($p->keterangan, 50) }}</div>
                        @endif
                    </td>
                    <td>{{ $p->tanggal_mulai->format('d M Y') }}</td>
                    <td>{{ $p->tanggal_selesai->format('d M Y') }}</td>
                    <td style="color:var(--text-muted);font-size:0.85rem;">{{ $p->tanggal_mulai->diffInDays($p->tanggal_selesai) + 1 }} hari</td>
                    <td>
                        @if($p->status === 'aktif')
                            <span class="badge badge-success"><i class="bi bi-circle-fill" style="font-size:0.55rem;"></i> Aktif</span>
                        @elseif($p->status === 'ditutup')
                            <span class="badge badge-secondary"><i class="bi bi-lock-fill" style="font-size:0.7rem;"></i> Ditutup</span>
                        @else
                            <span class="badge badge-warning"><i class="bi bi-hourglass" style="font-size:0.7rem;"></i> Draft</span>
                        @endif
                    </td>
                    <td>{{ $p->penilaians->count() }} penilaian</td>
                    <td>
                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                            <a href="{{ route('periode.edit', $p) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            {{-- Toggle status --}}
                            @if($p->status !== 'aktif')
                            <form action="{{ route('periode.update-status', $p) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="aktif">
                                <button type="submit" class="btn btn-success btn-sm" title="Aktifkan"
                                    onclick="return confirm('Aktifkan periode ini? Periode aktif lainnya akan dinonaktifkan.')">
                                    <i class="bi bi-play-fill"></i>
                                </button>
                            </form>
                            @endif
                            @if($p->status === 'aktif')
                            <form action="{{ route('periode.update-status', $p) }}" method="POST" style="display:inline;">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="ditutup">
                                <button type="submit" class="btn btn-secondary btn-sm" title="Tutup Periode"
                                    onclick="return confirm('Tutup periode ini? Penilaian tidak bisa dilakukan setelah ditutup.')">
                                    <i class="bi bi-lock-fill"></i>
                                </button>
                            </form>
                            @endif
                            @if($p->penilaians->count() === 0)
                            <form action="{{ route('periode.destroy', $p) }}" method="POST" style="display:inline;"
                                onsubmit="return confirm('Hapus periode {{ $p->nama }}?')">
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
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="bi bi-calendar-x" style="font-size:2.5rem;display:block;margin-bottom:12px;"></i>
                        Belum ada periode penilaian. Buat periode pertama sekarang.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($periodes->hasPages())
    <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:0.8rem;color:var(--text-muted);">Total: {{ $periodes->total() }} periode</div>
        {{ $periodes->links() }}
    </div>
    @endif
</div>
@endsection
