@extends('layouts.app')
@section('title', 'FTKP / Ketidaksesuaian')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>FTKP / Ketidaksesuaian</h2>
        <p>Formulir Tindakan Korektif dan Perbaikan</p>
    </div>
    @if($karyawan)
    <a href="{{ route('ketidaksesuaian.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i> Buat FTKP Baru
    </a>
    @endif
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No. FTKP</th>
                        <th>Tanggal</th>
                        <th>Pelapor</th>
                        <th>Ditujukan Ke</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ketidaksesuaians as $k)
                    <tr>
                        <td>
                            <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:0.82rem;">{{ $k->nomor_ftkp }}</code>
                        </td>
                        <td style="font-size:0.85rem;">{{ $k->tanggal_laporan->format('d/m/Y') }}</td>
                        <td style="font-size:0.85rem;">
                            <div style="font-weight:600;">{{ $k->pelapor->nama }}</div>
                            <div style="color:var(--text-muted);font-size:0.78rem;">{{ $k->divisiPelapor->nama }}</div>
                        </td>
                        <td style="font-size:0.85rem;color:var(--text-muted);">{{ $k->divisiTujuan->nama }}</td>
                        <td>
                            @php
                                $kategoriColor = match($k->kategori_temuan) {
                                    'ok' => 'success',
                                    'observasi' => 'warning',
                                    'nc' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-{{ $kategoriColor }}">{{ $k->kategori_label }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $k->status_color }}">{{ $k->status_label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('ketidaksesuaian.show', $k) }}" class="btn btn-info btn-sm">
                                <i class="bi bi-eye-fill"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center" style="padding:40px;color:var(--text-muted);">
                        <i class="bi bi-exclamation-triangle" style="font-size:2rem;display:block;margin-bottom:8px;"></i>
                        Belum ada data ketidaksesuaian
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ketidaksesuaians->hasPages())
    <div class="card-footer">{{ $ketidaksesuaians->links() }}</div>
    @endif
</div>
@endsection
