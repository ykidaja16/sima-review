@extends('layouts.app')
@section('title', 'Update Data Diri')
@section('breadcrumb')
    <span style="color:var(--text-muted);">Pengaturan</span> / <strong>Update Data Diri</strong>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Update Data Diri</h2>
        <p>Kelola informasi profil pribadi dan kata sandi akun Anda</p>
    </div>
</div>

<form action="{{ route('profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-2" style="gap:24px;align-items:start;">

        {{-- KOLOM KIRI: DATA DIRI & INFORMASI AKUN --}}
        <div>
            {{-- Kartu Profil Header --}}
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body" style="display:flex;align-items:center;gap:18px;padding:20px;">
                    <div class="user-avatar" style="width:64px;height:64px;font-size:24px;border-radius:16px;background:linear-gradient(135deg, #3b82f6, #06b6d4);box-shadow:0 4px 14px rgba(59,130,246,0.35);">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 style="font-size:1.15rem;font-weight:700;margin-bottom:4px;color:var(--text-primary);">{{ $user->name }}</h3>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span class="role-badge" style="background:#e0f2fe;color:#0369a1;font-weight:600;padding:4px 10px;border-radius:6px;font-size:0.75rem;">
                                <i class="bi bi-shield-check" style="margin-right:4px;"></i>{{ $user->role_label }}
                            </span>
                            <span style="font-size:0.8rem;color:var(--text-muted);">
                                <i class="bi bi-person-fill" style="margin-right:2px;"></i>{{ $user->username }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Form Data Diri --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-lines-fill" style="color:#3b82f6;margin-right:8px;"></i>Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    {{-- Info Kepegawaian (Read-only) --}}
                    @if($user->karyawan)
                    <div style="background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:20px;">
                        <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;color:var(--text-muted);letter-spacing:0.5px;margin-bottom:8px;">
                            <i class="bi bi-info-circle-fill" style="color:#3b82f6;margin-right:4px;"></i>Data Kepegawaian
                        </div>
                        <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:10px;font-size:0.85rem;">
                            <div>
                                <span style="color:var(--text-muted);display:block;font-size:0.75rem;">NIP</span>
                                <strong>{{ $user->karyawan->nip ?: '—' }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-muted);display:block;font-size:0.75rem;">Divisi</span>
                                <strong>{{ $user->karyawan->divisi->nama ?? '—' }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-muted);display:block;font-size:0.75rem;">Jabatan</span>
                                <strong>{{ $user->karyawan->jabatan->nama ?? '—' }}</strong>
                            </div>
                            <div>
                                <span style="color:var(--text-muted);display:block;font-size:0.75rem;">Cabang</span>
                                <strong>{{ $user->karyawan->cabang->nama ?? 'Pusat' }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div style="position:relative;">
                            <input type="text" class="form-control" value="{{ $user->username }}" disabled style="background:#f1f5f9;color:#64748b;cursor:not-allowed;padding-right:36px;">
                            <i class="bi bi-lock-fill" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;" title="Username tidak dapat diubah"></i>
                        </div>
                        <small style="color:var(--text-muted);font-size:0.72rem;">Username bersifat permanen untuk login sistem.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nama Lengkap *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap Anda">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" placeholder="email@contoh.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($user->karyawan)
                    <div class="form-group">
                        <label class="form-label">No. Handphone / WhatsApp</label>
                        <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror"
                            value="{{ old('no_hp', $user->karyawan->no_hp) }}" placeholder="Contoh: 08123456789">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: GANTI KATA SANDI --}}
        <div>
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-shield-lock-fill" style="color:#059669;margin-right:8px;"></i>Ganti Kata Sandi Akun</h5>
                </div>
                <div class="card-body">
                    <div style="background:rgba(59,130,246,0.06);border:1px solid rgba(59,130,246,0.2);border-radius:8px;padding:12px 14px;margin-bottom:20px;font-size:0.83rem;color:#1e40af;display:flex;gap:10px;align-items:flex-start;">
                        <i class="bi bi-info-circle-fill" style="font-size:1.1rem;color:#2563eb;flex-shrink:0;margin-top:1px;"></i>
                        <span>Kosongkan kolom kata sandi di bawah jika Anda <strong>tidak ingin mengubah</strong> kata sandi akun saat ini.</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Sandi Saat Ini</label>
                        <div style="position:relative;">
                            <input type="password" name="current_password" id="current_password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                placeholder="Masukkan kata sandi lama Anda" autocomplete="current-password">
                            <button type="button" onclick="togglePass('current_password', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#64748b;cursor:pointer;padding:4px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Kata Sandi Baru</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Minimal 8 karakter" autocomplete="new-password">
                            <button type="button" onclick="togglePass('password', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#64748b;cursor:pointer;padding:4px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Kata Sandi Baru</label>
                        <div style="position:relative;">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control"
                                placeholder="Ketik ulang kata sandi baru" autocomplete="new-password">
                            <button type="button" onclick="togglePass('password_confirmation', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#64748b;cursor:pointer;padding:4px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
                <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg" style="padding:10px 24px;">
                    <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                </button>
            </div>
        </div>

    </div>
</form>

<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    }
</script>
@endsection
