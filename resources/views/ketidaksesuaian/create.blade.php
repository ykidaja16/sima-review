@extends('layouts.app')
@section('title', 'Buat FTKP Baru')
@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Buat FTKP Baru</h2>
        <p>Formulir Tindakan Korektif dan Perbaikan — Bagian 1: Penggagas</p>
    </div>
    <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

@if(!$karyawan)
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle-fill"></i>
    Data karyawan Anda tidak ditemukan. Hubungi administrator.
</div>
@else
<form action="{{ route('ketidaksesuaian.store') }}" method="POST">
@csrf
<div class="grid grid-2" style="align-items:start;">
    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-person-fill" style="color:#3b82f6;margin-right:6px;"></i>BAGIAN 1 – PENGGAGAS</h5>
        </div>
        <div class="card-body">
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Penggagas</label>
                    <input type="text" class="form-control" value="{{ $karyawan->nama }}" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Departemen/Bagian</label>
                    <input type="text" class="form-control" value="{{ $karyawan->divisi->nama ?? '-' }}" disabled>
                </div>
            </div>

            @php
                $defaultKategoris = ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
            @endphp
            <div class="form-group">
                <label class="form-label">Jenis Ketidaksesuaian *</label>
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:8px;">
                    @foreach($jenisList as $j)
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="radio" name="jenis_id" value="{{ $j->id }}"
                            data-kategoris="{{ json_encode($j->kategori_temuan_list) }}"
                            {{ old('jenis_id') == $j->id ? 'checked' : '' }}
                            id="jenis_{{ $j->id }}"
                            onchange="updateKategoriTemuanOptions()">
                        <span>{{ $j->nama }}</span>
                    </label>
                    @endforeach
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="radio" name="jenis_id" value="" id="jenis_lainnya_radio"
                            data-kategoris="{{ json_encode($defaultKategoris) }}"
                            {{ !old('jenis_id') && old('jenis_lainnya') ? 'checked' : '' }}
                            onchange="updateKategoriTemuanOptions()">
                        <span>Lain-lain, sebutkan:</span>
                        <input type="text" name="jenis_lainnya" id="jenis_lainnya_input" class="form-control @error('jenis_lainnya') is-invalid @enderror"
                            style="width:auto;flex:1;" value="{{ old('jenis_lainnya') }}" placeholder="Sebutkan..."
                            onfocus="document.getElementById('jenis_lainnya_radio').checked = true; updateKategoriTemuanOptions();">
                    </label>
                </div>
                @error('jenis_id')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
                @error('jenis_lainnya')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Ditujukan ke Departemen/Divisi *</label>
                <select name="divisi_tujuan_id" class="form-select searchable-select @error('divisi_tujuan_id') is-invalid @enderror" data-placeholder="Cari divisi tujuan..." required>
                    <option value="">-- Pilih Divisi Tujuan --</option>
                    @foreach($divisis as $d)
                    <option value="{{ $d->id }}" {{ old('divisi_tujuan_id') == $d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
                    @endforeach
                </select>
                @error('divisi_tujuan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Penjelasan Temuan *</label>
                <textarea name="penjelasan_temuan" class="form-control @error('penjelasan_temuan') is-invalid @enderror"
                    rows="5" placeholder="Jelaskan temuan yang timbul atau potensial...">{{ old('penjelasan_temuan') }}</textarea>
                @error('penjelasan_temuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Kategori Temuan *</label>
                <div id="kategoriTemuanContainer" style="display:flex;gap:16px;flex-wrap:wrap;padding:10px 12px;background:#f8fafc;border:1px solid var(--border);border-radius:8px;">
                    <span style="font-size:0.85rem;color:var(--text-muted);">Pilih Jenis Ketidaksesuaian terlebih dahulu...</span>
                </div>
                @error('kategori_temuan')<div class="invalid-feedback" style="display:block;">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="card-footer" style="display:flex;justify-content:flex-end;gap:8px;">
            <a href="{{ route('ketidaksesuaian.index') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send-fill"></i> Kirim FTKP
            </button>
        </div>
    </div>

    <div class="card" style="background:linear-gradient(135deg,rgba(37,99,235,0.04),rgba(6,182,212,0.04));border-style:dashed;">
        <div class="card-header">
            <h5><i class="bi bi-info-circle-fill" style="color:#0284c7;margin-right:6px;"></i>Petunjuk Pengisian</h5>
        </div>
        <div class="card-body">
            <ul style="padding-left:16px;font-size:0.875rem;color:var(--text-muted);line-height:1.9;">
                <li>Pilih <strong>jenis ketidaksesuaian</strong> yang sesuai. Jika tidak ada, pilih "Lain-lain".</li>
                <li>Pilih <strong>divisi tujuan</strong> yang harus menangani masalah ini.</li>
                <li>Jelaskan temuan secara detail pada kolom <strong>Penjelasan Temuan</strong>.</li>
                <li>Pilih <strong>kategori temuan</strong> sesuai dengan jenis ketidaksesuaian yang dipilih.</li>
                <li>Setelah FTKP dibuat, pihak divisi terkait akan mengisi tindak lanjut.</li>
                <li>Kemudian diverifikasi oleh Manager Penggagas dan Kepala Cabang.</li>
            </ul>
            <div style="margin-top:16px;padding:12px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:8px;font-size:0.82rem;color:#92400e;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                Nomor FTKP akan digenerate otomatis setelah form dikirim.
            </div>
        </div>
    </div>
</div>
</form>

@push('scripts')
<script>
const oldKategoriTemuan = {!! json_encode(old('kategori_temuan', '')) !!};

function updateKategoriTemuanOptions() {
    const container = document.getElementById('kategoriTemuanContainer');
    const checkedRadio = document.querySelector('input[name="jenis_id"]:checked');
    if (!container) return;

    if (!checkedRadio) {
        container.innerHTML = '<span style="font-size:0.85rem;color:var(--text-muted);">Pilih Jenis Ketidaksesuaian terlebih dahulu...</span>';
        return;
    }

    let kategoris = [];
    try {
        kategoris = JSON.parse(checkedRadio.dataset.kategoris || '[]');
    } catch (e) {
        kategoris = ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
    }
    if (!kategoris.length) {
        kategoris = ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
    }

    container.innerHTML = '';
    kategoris.forEach((kat, idx) => {
        const label = document.createElement('label');
        label.style.cssText = 'display:flex;align-items:center;gap:6px;cursor:pointer;font-weight:500;font-size:0.88rem;';

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'kategori_temuan';
        radio.value = kat;
        radio.required = true;
        if ((oldKategoriTemuan && oldKategoriTemuan === kat) || (!oldKategoriTemuan && idx === 0)) {
            radio.checked = true;
        }

        const span = document.createElement('span');
        span.textContent = kat;

        label.appendChild(radio);
        label.appendChild(span);
        container.appendChild(label);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const anyChecked = document.querySelector('input[name="jenis_id"]:checked');
    if (!anyChecked) {
        const firstRadio = document.querySelector('input[name="jenis_id"]');
        if (firstRadio) firstRadio.checked = true;
    }
    updateKategoriTemuanOptions();
});
</script>
@endpush
@endif
@endsection
