@extends('layouts.app')
@section('title', 'Input Penilaian')
@section('breadcrumb')<span style="color:var(--text-muted);">Penilaian</span> / <strong>Input Penilaian</strong>@endsection

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
        <h2>Input Penilaian</h2>
        <p>Periode: <strong>{{ $periodeAktif->nama }}</strong></p>
    </div>
    <a href="{{ route('penilaian.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<form action="{{ route('penilaian.store') }}" method="POST" id="penilaianForm">
@csrf
<input type="hidden" name="periode_id" value="{{ $periodeAktif->id }}">

{{-- Info Karyawan --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header"><h5><i class="bi bi-person-badge-fill" style="color:#3b82f6;margin-right:6px;"></i>Data Karyawan & Penilaian</h5></div>
    <div class="card-body">
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Filter Divisi</label>
                <select id="filterDivisiSelect" class="form-select searchable-select" data-placeholder="Cari divisi...">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}">{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Cari & Pilih Karyawan Dinilai *</label>
                <select name="karyawan_id" class="form-select searchable-select @error('karyawan_id') is-invalid @enderror" required id="karyawanSelect" data-placeholder="Cari nama karyawan...">
                    <option value="">-- Pilih Karyawan --</option>
                    @foreach($karyawans as $k)
                    <option value="{{ $k->id }}" data-divisi="{{ $k->divisi_id }}" {{ old('karyawan_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama }} — {{ $k->jabatan->nama ?? '-' }} ({{ $k->divisi->nama ?? '-' }})
                    </option>
                    @endforeach
                </select>
                @error('karyawan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @if($karyawans->isEmpty())
                <div style="font-size:0.8rem;color:#d97706;margin-top:4px;">
                    <i class="bi bi-exclamation-triangle"></i> Tidak ada karyawan dari divisi lain yang memenuhi kriteria untuk dinilai atau seluruhnya telah dinilai pada periode ini.
                </div>
                @endif
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Penilaian *</label>
                <input type="date" name="tanggal_penilaian" class="form-control @error('tanggal_penilaian') is-invalid @enderror"
                    value="{{ old('tanggal_penilaian', today()->format('Y-m-d')) }}" required>
                @error('tanggal_penilaian')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
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

{{-- Parameter per Kategori --}}
@foreach($parameters as $kategoriNama => $params)
<div class="card" style="margin-bottom:16px;">
    <div class="card-header" style="background:linear-gradient(135deg,rgba(30,64,175,0.06),rgba(6,182,212,0.04));">
        <h5 style="font-size:0.9rem;font-weight:700;">
            <i class="bi bi-check2-square" style="color:#0284c7;margin-right:8px;"></i>
            {{ $kategoriNama }}
        </h5>
        <span class="badge badge-info" style="font-size:0.72rem;">{{ $params->count() }} Indikator</span>
    </div>

    {{-- Header kolom --}}
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
    <div class="param-row">
        <div style="padding:12px 16px;">
            <div style="font-size:0.875rem;font-weight:600;color:var(--text);line-height:1.45;">{{ $param->nama }}</div>
            @if($param->deskripsi)
            <div style="font-size:0.75rem;color:var(--text-muted);margin-top:3px;">{{ $param->deskripsi }}</div>
            @endif
        </div>
        <div class="skor-grid">
            @foreach([4,3,2,1] as $skor)
            <label style="display:flex;justify-content:center;align-items:center;height:100%;cursor:pointer;padding:8px 0;"
                   title="{{ $skor==4?'Sangat Baik':($skor==3?'Baik':($skor==2?'Cukup':'Kurang Baik')) }}">
                <input type="radio"
                    name="nilai_{{ $param->id }}"
                    value="{{ $skor }}"
                    {{ old('nilai_' . $param->id) == $skor || (!old('nilai_' . $param->id) && $skor == 3) ? 'checked' : '' }}
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
                value="{{ old('catatan_' . $param->id) }}"
                placeholder="Catatan pengamatan (opsional)">
        </div>
    </div>
    @endforeach
</div>
@endforeach

{{-- Catatan Umum Penilaian (Diisi di akhir sebelum Simpan) --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <h5><i class="bi bi-chat-left-text-fill" style="color:#3b82f6;margin-right:6px;"></i>Catatan Umum Penilaian</h5>
    </div>
    <div class="card-body">
        <textarea name="catatan" class="form-control" rows="3" placeholder="Tuliskan catatan umum, evaluasi keseluruhan, atau rekomendasi perbaikan untuk karyawan ini (opsional)...">{{ old('catatan') }}</textarea>
    </div>
</div>

{{-- Ringkasan & Submit --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="text-align:center;">
                <div style="font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);">Estimasi Nilai Akhir</div>
                <div id="previewNilai" style="font-size:2.5rem;font-weight:800;color:#2563eb;line-height:1.1;">75.00</div>
                <div id="previewKategori" style="font-size:0.82rem;font-weight:600;color:#2563eb;">Baik</div>
            </div>
            <div style="font-size:0.78rem;color:var(--text-muted);max-width:280px;line-height:1.5;">
                <i class="bi bi-info-circle" style="color:#0284c7;"></i>
                Nilai akhir dihitung otomatis berdasarkan skor yang dipilih × bobot masing-masing parameter, lalu dikonversi ke skala 0–100.
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('penilaian.index') }}" class="btn btn-outline btn-lg">Batal</a>
            <button type="submit" class="btn btn-primary btn-lg" {{ $karyawans->isEmpty() ? 'disabled' : '' }}>
                <i class="bi bi-check-circle-fill"></i> Simpan Penilaian
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
    const pctStr = pct.toFixed(2);
    document.getElementById('previewNilai').textContent = pctStr;

    let color = '#dc2626', kat = 'Belum Kompeten';
    if (pct >= 90)      { color = '#059669'; kat = 'Sangat Kompeten'; }
    else if (pct >= 75) { color = '#2563eb'; kat = 'Kompeten'; }
    else if (pct >= 60) { color = '#d97706'; kat = 'Cukup Kompeten'; }

    document.getElementById('previewNilai').style.color = color;
    document.getElementById('previewKategori').textContent = kat;
    document.getElementById('previewKategori').style.color = color;
}

// Init on load
recalculate();

const filterDivisiEl = document.getElementById('filterDivisiSelect');
const karyawanSelectEl = document.getElementById('karyawanSelect');
if (filterDivisiEl && karyawanSelectEl) {
    filterDivisiEl.addEventListener('change', function() {
        const divId = this.value;
        Array.from(karyawanSelectEl.options).forEach(opt => {
            if (!opt.value) return;
            const match = !divId || opt.dataset.divisi === divId;
            opt.hidden = !match;
            opt.disabled = !match;
        });
        if (karyawanSelectEl.selectedOptions[0]?.hidden) {
            karyawanSelectEl.value = '';
        }
        if (typeof karyawanSelectEl._refreshSearchableSelect === 'function') {
            karyawanSelectEl._refreshSearchableSelect();
        }
    });
}
</script>
@endpush
