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
    grid-template-columns: 1fr 240px 300px;
    gap: 0;
    border-bottom: 1px solid var(--border);
    align-items: stretch;
}
.param-row:last-child {
    border-bottom: none;
}
.param-header-grid {
    display: grid;
    grid-template-columns: 1fr 240px 300px;
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
.catatan-col {
    padding: 8px 12px;
    border-left: 1px solid var(--border);
    display: flex;
    align-items: center;
}
@media (max-width: 992px) {
    .param-row, .param-header-grid {
        grid-template-columns: 1fr 200px 220px;
    }
    .skor-grid {
        grid-template-columns: repeat(4, 50px);
    }
}
@media (max-width: 768px) {
    .param-row, .param-header-grid {
        grid-template-columns: 1fr;
    }
    .skor-grid {
        grid-template-columns: repeat(4, 1fr);
        border-left: none;
        border-top: 1px solid var(--border);
        padding: 8px 0;
    }
    .catatan-col {
        border-left: none;
        border-top: 1px solid var(--border);
        padding: 8px 12px 12px;
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

{{-- Rubrik & Panduan Penilaian --}}
<div class="card" style="margin-bottom:16px;background:#f8fafc;border:1px solid #cbd5e1;">
    <div class="card-body" style="padding:16px;">
        <div style="font-weight:700;font-size:0.88rem;margin-bottom:10px;color:var(--text);display:flex;align-items:center;gap:6px;">
            <i class="bi bi-info-circle-fill" style="color:var(--primary);"></i> RUBRIK PENILAIAN (PANDUAN EVALUATOR)
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:10px;font-size:0.8rem;line-height:1.45;">
            <div style="background:white;padding:10px 12px;border-radius:8px;border-left:4px solid #059669;box-shadow:var(--shadow-sm);">
                <strong style="color:#059669;display:block;margin-bottom:2px;">Skor 4 (Sangat Baik):</strong>
                Petugas melakukan indikator secara konsisten, natural, proaktif, dan melampaui ekspektasi standar SOP.
            </div>
            <div style="background:white;padding:10px 12px;border-radius:8px;border-left:4px solid #2563eb;box-shadow:var(--shadow-sm);">
                <strong style="color:#2563eb;display:block;margin-bottom:2px;">Skor 3 (Baik):</strong>
                Petugas melakukan indikator sesuai standar SOP, namun terkadang masih terlihat kurang fleksibel atau perlu sedikit stimulus.
            </div>
            <div style="background:white;padding:10px 12px;border-radius:8px;border-left:4px solid #d97706;box-shadow:var(--shadow-sm);">
                <strong style="color:#d97706;display:block;margin-bottom:2px;">Skor 2 (Cukup):</strong>
                Petugas melakukan indikator hanya jika diingatkan atau hanya terlihat di awal sesi, terdapat beberapa aspek yang terlewat.
            </div>
            <div style="background:white;padding:10px 12px;border-radius:8px;border-left:4px solid #dc2626;box-shadow:var(--shadow-sm);">
                <strong style="color:#dc2626;display:block;margin-bottom:2px;">Skor 1 (Kurang Baik):</strong>
                Petugas gagal menunjukkan indikator tersebut atau perilakunya kontradiktif dengan prinsip pelayanan prima.
            </div>
        </div>
        <div style="margin-top:12px;padding-top:10px;border-top:1px dashed var(--border);display:flex;gap:10px;flex-wrap:wrap;align-items:center;font-size:0.78rem;">
            <span style="font-weight:700;color:var(--text);">Predikat Nilai:</span>
            <span class="badge badge-success" style="font-size:0.76rem;padding:4px 8px;">90% - 100% : Sangat Kompeten (Role Model)</span>
            <span class="badge badge-primary" style="font-size:0.76rem;padding:4px 8px;">75% - 89% : Kompeten (Standar Instansi)</span>
            <span class="badge badge-warning" style="font-size:0.76rem;padding:4px 8px;">60% - 74% : Cukup Kompeten (Perlu Pemantauan)</span>
            <span class="badge badge-danger" style="font-size:0.76rem;padding:4px 8px;">&lt; 60% : Belum Kompeten (Remedial/Coaching)</span>
        </div>
    </div>
</div>

@foreach($parameters as $kategoriNama => $params)
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(30,64,175,0.06),rgba(6,182,212,0.04));">
        <h5 style="font-size:0.9rem;font-weight:700;">
            <i class="bi bi-check2-square" style="color:#0284c7;margin-right:8px;"></i>
            {{ $kategoriNama }}
        </h5>
        <span class="badge badge-info" style="font-size:0.72rem;">{{ $params->count() }} Indikator</span>
    </div>

    {{-- Header --}}
    <div class="param-header-grid">
        <div style="padding:10px 16px;font-size:0.74rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">DIMENSI &amp; INDIKATOR PENILAIAN</div>
        <div class="skor-grid">
            <div class="skor-col-4" style="padding:8px 0;font-size:0.78rem;font-weight:700;">4</div>
            <div class="skor-col-3" style="padding:8px 0;font-size:0.78rem;font-weight:700;">3</div>
            <div class="skor-col-2" style="padding:8px 0;font-size:0.78rem;font-weight:700;">2</div>
            <div class="skor-col-1" style="padding:8px 0;font-size:0.78rem;font-weight:700;">1</div>
        </div>
        <div style="padding:10px 14px;font-size:0.74rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;border-left:1px solid var(--border);">CATATAN PENGAMATAN</div>
    </div>

    @foreach($params as $param)
    @php
        $nilaiLama = old('nilai_' . $param->id, $detailsMap[$param->id]->nilai ?? 3);
        $catatanLama = old('catatan_' . $param->id, $detailsMap[$param->id]->catatan ?? '');
    @endphp
    <div class="param-row">
        <div style="padding:12px 16px;">
            <div style="font-size:0.875rem;font-weight:600;color:var(--text);line-height:1.45;">{{ $param->nama }}</div>
            @if($param->deskripsi)
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:3px;">{{ $param->deskripsi }}</div>
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
        <div class="catatan-col">
            <input type="text"
                name="catatan_{{ $param->id }}"
                class="form-control"
                style="font-size:0.83rem;padding:6px 10px;"
                value="{{ $catatanLama }}"
                placeholder="Catatan pengamatan (opsional)">
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

    let color = '#dc2626', kat = 'Belum Kompeten';
    if (pct >= 90)      { color = '#059669'; kat = 'Sangat Kompeten'; }
    else if (pct >= 75) { color = '#2563eb'; kat = 'Kompeten'; }
    else if (pct >= 60) { color = '#d97706'; kat = 'Cukup Kompeten'; }

    document.getElementById('previewNilai').style.color = color;
    document.getElementById('previewKategori').textContent = kat;
    document.getElementById('previewKategori').style.color = color;
}

recalculate();
</script>
@endpush
