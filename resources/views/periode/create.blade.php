@extends('layouts.app')
@section('title', 'Buat Periode Penilaian')
@section('content')

<div class="page-header">
    <div class="page-title"><h2>Buat Periode Penilaian</h2></div>
    <a href="{{ route('periode.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-body">
        <form action="{{ route('periode.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Periode *</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                    value="{{ old('nama') }}" placeholder="Contoh: Periode Januari 2026 / Triwulan I 2026" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="grid grid-2">
                <div class="form-group">
                    <label class="form-label">Tanggal Mulai *</label>
                    <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror"
                        value="{{ old('tanggal_mulai') }}" required id="tglMulai">
                    @error('tanggal_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Tanggal Selesai *</label>
                    <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror"
                        value="{{ old('tanggal_selesai') }}" required id="tglSelesai">
                    @error('tanggal_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status Awal</label>
                <select name="status" class="form-select">
                    <option value="draft" {{ old('status')=='draft'?'selected':'' }}>Draft (belum aktif)</option>
                    <option value="aktif" {{ old('status')=='aktif'?'selected':'' }}>Aktif sekarang</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"
                    placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            </div>

            {{-- Preview durasi --}}
            <div id="durasiInfo" style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:16px;font-size:0.85rem;color:var(--text-muted);display:none;">
                <i class="bi bi-info-circle" style="color:#3b82f6;"></i>
                Durasi: <strong id="durasiText"></strong>
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <a href="{{ route('periode.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Periode</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateDurasi() {
        const mulai = document.getElementById('tglMulai').value;
        const selesai = document.getElementById('tglSelesai').value;
        const info = document.getElementById('durasiInfo');
        if (mulai && selesai) {
            const d1 = new Date(mulai), d2 = new Date(selesai);
            const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
            if (diff > 0) {
                document.getElementById('durasiText').textContent = diff + ' hari';
                info.style.display = 'block';
            }
        }
    }
    document.getElementById('tglMulai').addEventListener('change', updateDurasi);
    document.getElementById('tglSelesai').addEventListener('change', updateDurasi);
</script>
@endsection
