@extends('layouts.app')
@section('title', 'Daftar Penilaian')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Daftar Penilaian</h2>
        <p>Riwayat penilaian Service Excellent karyawan</p>
    </div>
    @if(!auth()->user()->isPelaksana())
    <a href="{{ route('penilaian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Input Penilaian Baru
    </a>
    @endif
</div>

{{-- Filter --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('penilaian.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Filter Periode</label>
                <select name="periode_id" class="form-select">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-funnel"></i> Filter
            </button>
            <a href="{{ route('penilaian.index') }}" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Karyawan</th>
                    <th>Divisi</th>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th>Evaluator</th>
                    <th>Nilai Akhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penilaians as $p)
                @php $kat = $p->kategori; @endphp
                <tr>
                    <td style="color:var(--text-muted);">{{ $penilaians->firstItem() + $loop->index }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $p->karyawan->nama ?? '-' }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $p->karyawan->nip ?? '' }}</div>
                    </td>
                    <td>{{ $p->karyawan->divisi->nama ?? '-' }}</td>
                    <td>{{ $p->periode->nama ?? '-' }}</td>
                    <td>{{ $p->tanggal_penilaian?->format('d/m/Y') }}</td>
                    <td>{{ $p->evaluator->name ?? '-' }}</td>
                    <td>
                        <div class="badge badge-{{ $kat?->warna ?? 'secondary' }}" style="font-size:0.85rem;font-weight:700;">
                            {{ number_format($p->nilai_akhir ?? 0, 2) }}
                        </div>
                        @if($kat)<div style="font-size:0.7rem;color:var(--text-muted);margin-top:2px;">{{ $kat->nama }}</div>@endif
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ route('penilaian.show', $p) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if(!auth()->user()->isPelaksana())
                            <a href="{{ route('penilaian.edit', $p) }}" class="btn btn-warning btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if(auth()->user()->isSuperAdmin())
                            <form action="{{ route('penilaian.destroy', $p) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus penilaian ini?')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size:2.5rem;display:block;margin-bottom:12px;"></i>
                        Belum ada data penilaian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($penilaians->hasPages())
    <div class="card-footer" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="font-size:0.8rem;color:var(--text-muted);">
            Menampilkan {{ $penilaians->firstItem() }} - {{ $penilaians->lastItem() }} dari {{ $penilaians->total() }} data
        </div>
        {{ $penilaians->links() }}
    </div>
    @endif
</div>
@endsection
