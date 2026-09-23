@extends('layouts.app')
@section('title', 'Edit Penilaian')
@section('breadcrumb')<span style="color:var(--text-muted);">Penilaian</span> / <strong>Edit Penilaian</strong>@endsection

@push('styles')
<style>
.skor-col-4 { color: #059669; }
.skor-col-3 { color: #2563eb; }
.skor-col-2 { color: #d97706; }
.skor-col-1 { color: #dc2626; }
.param-row {
    display: grid;
    grid-template-columns: 1fr 240px;
    gap: 0;
    border-bottom: 1px solid var(--border);
}
.param-row:last-child {
    border-bottom: none;
}
.param-header-grid {
    display: grid;
    grid-template-columns: 1fr 240px;
    gap: 0;
    border-bottom: 1px solid var(--border);
    background: #f8fafc;
}
.skor-grid {
    display: grid;
    grid-template-columns: repeat(4, 60px);
    text-align: center;
    border-left: 1px solid var(--border);
    align-items: center;
}
@media (max-width: 640px) {
    .param-row, .param-header-grid {
        grid-template-columns: 1fr 160px;
    }
    .skor-grid {
        grid-template-columns: repeat(4, 40px);
    }
}
@media (max-width: 420px) {
    .param-row, .param-header-grid {
        grid-template-columns: 1fr 140px;
    }
    .skor-grid {
        grid-template-columns: repeat(4, 35px);
    }
    .skor-grid input[type="radio"] {
        width: 16px;
        height: 16px;
    }
}
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Edit Penilaian</h2>
        <p>{{ $penilaian->karyawan->nama ?? '-' }} — {{ $penilaian->periode->nama ?? '-' }}</p>
    </div>
    <a href="{{ route('penilaian.show', $penilaian) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<form action="{{ route('penilaian.update', $penilaian) }}" method="POST" id="editForm">
@csrf @method('PUT')

<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h5><i class="bi bi-person-badge-fill" style="color:#3b82f6;margin-right:6px;"></i>Informasi Penilaian</h5></div>
    <div class="card-body">
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Karyawan</label>
                <input type="text" class="form-control" value="{{ $penilaian->karyawan->nama ?? '-' }} ({{ $penilaian->karyawan->nip ?? '-' }})" readonly style="background:var(--bg);">
            </div>
            <div class="form-group">
                <label class="form-label">Periode</label>
                <input type="text" class="form-control" value="{{ $penilaian->periode->nama ?? '-' }}" readonly style="background:var(--bg);">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Penilaian *</label>
                <input type="date" name="tanggal_penilaian" class="form-control @error('tanggal_penilaian') is-invalid @enderror"
                    value="{{ old('tanggal_penilaian', $penilaian->tanggal_penilaian?->format('Y-m-d')) }}" required>
                @error('tanggal_penilaian')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Catatan Umum</label>
            <input type="text" name="catatan" class="form-control" value="{{ old('catatan', $penilaian->catatan) }}" placeholder="Catatan singkat (opsional)">
        </div>
    </div>
</div>

{{-- Legenda Skor --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;align-items:center;">
    <span style="font-size:0.8rem;font-weight:600;color:var(--text-muted);margin-right:4px;">Keterangan Skor:</span>
    <span style="padding:4px 12px;background:#dcfce7;color:#166534;border-radius:20px;font-size:0.8rem;font-weight:700;">4 — Sangat Baik</span>
    <span style="padding:4px 12px;background:#dbeafe;color:#1e40af;border-radius:20px;font-size:0.8rem;font-weight:700;">3 — Baik</span>
    <span style="padding:4px 12px;background:#fef9c3;color:#854d0e;border-radius:20px;font-size:0.8rem;font-weight:700;">2 — Cukup</span>
    <span style="padding:4px 12px;background:#fee2e2;color:#991b1b;border-radius:20px;font-size:0.8rem;font-weight:700;">1 — Kurang Baik</span>
</div>

@foreach($parameters as $kategoriNama => $params)
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(30,64,175,0.06),rgba(6,182,212,0.04));">
        <h5 style="font-size:0.9rem;font-weight:700;">
            <i class="bi bi-check2-square" style="color:#0284c7;margin-right:8px;"></i>
            {{ $kategoriNama }}
        </h5>
        <span class="badge badge-info" style="font-size:0.72rem;">{{ $params->count() }} parameter</span>
    </div>

    {{-- Header --}}
    <div class="param-header-grid">
        <div style="padding:8px 16px;font-size:0.72rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">Indikator Penilaian</div>
        <div class="skor-grid">
            <div class="skor-col-4" style="padding:8px 0;font-size:0.78rem;font-weight:700;">4</div>
            <div class="skor-col-3" style="padding:8px 0;font-size:0.78rem;font-weight:700;">3</div>
            <div class="skor-col-2" style="padding:8px 0;font-size:0.78rem;font-weight:700;">2</div>
            <div class="skor-col-1" style="padding:8px 0;font-size:0.78rem;font-weight:700;">1</div>
        </div>
    </div>

    @foreach($params as $param)
    @php $nilaiLama = old('nilai_' . $param->id, $detailsMap[$param->id]->nilai ?? 3); @endphp
    <div class="param-row">
        <div style="padding:12px 16px;">
            <div style="font-size:0.875rem;font-weight:500;">{{ $param->nama }}</div>
            @if($param->deskripsi)
            <div style="font-size:0.72rem;color:var(--text-muted);margin-top:2px;">{{ $param->deskripsi }}</div>
            @endif
        </div>
        <div class="skor-grid">
            @foreach([4,3,2,1] as $skor)
            <label style="display:flex;justify-content:center;align-items:center;height:100%;cursor:pointer;padding:8px 0;">
                <input type="radio"
                    name="nilai_{{ $param->id }}"
                    value="{{ $skor }}"
                    {{ $nilaiLama == $skor ? 'checked' : '' }}
                    onchange="recalculate()"
                    style="width:18px;height:18px;cursor:pointer;">
            </label>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endforeach

<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="text-align:center;">
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Estimasi Nilai Akhir</div>
                <div id="previewNilai" style="font-size:2.5rem;font-weight:800;color:#2563eb;line-height:1.1;">{{ number_format($penilaian->nilai_akhir ?? 0, 2) }}</div>
                <div id="previewKategori" style="font-size:0.82rem;font-weight:600;color:#2563eb;">
                    {{ $penilaian->kategori?->nama ?? '-' }}
                </div>
            </div>
            <div style="font-size:0.75rem;color:var(--text-muted);max-width:260px;line-height:1.5;">
                Dinilai oleh: <strong>{{ $penilaian->evaluator->name ?? '-' }}</strong>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('penilaian.show', $penilaian) }}" class="btn btn-outline btn-lg">Batal</a>
            <button type="submit" class="btn btn-warning btn-lg">
                <i class="bi bi-check-circle-fill"></i> Update Penilaian
            </button>
        </div>
    </div>
</div>
</form>

{{-- Data JSON terisolasi untuk frontend script --}}
<script id="paramDataJson" type="application/json">
{!! json_encode($parameters->flatten()->map(fn($p) => ['id' => $p->id, 'bobot' => $p->bobot])) !!}
</script>
@endsection

@push('scripts')
<script>
const allParams = JSON.parse(document.getElementById('paramDataJson').textContent || '[]');

function recalculate() {
    let totalBobotMax = 0, totalWeighted = 0;
    allParams.forEach(p => {
        const radio = document.querySelector('input[name="nilai_' + p.id + '"]:checked');
        const skor = radio ? parseInt(radio.value) : 3;
        totalBobotMax += 4 * p.bobot;
        totalWeighted += skor * p.bobot;
    });
    const pct = totalBobotMax > 0 ? (totalWeighted / totalBobotMax * 100) : 0;
    document.getElementById('previewNilai').textContent = pct.toFixed(2);

    let color = '#dc2626', kat = 'Kurang Baik';
    if (pct >= 88)      { color = '#059669'; kat = 'Sangat Baik'; }
    else if (pct >= 63) { color = '#2563eb'; kat = 'Baik'; }
    else if (pct >= 38) { color = '#d97706'; kat = 'Cukup'; }

    document.getElementById('previewNilai').style.color = color;
    document.getElementById('previewKategori').textContent = kat;
    document.getElementById('previewKategori').style.color = color;
}

recalculate();
</script>
@endpush
