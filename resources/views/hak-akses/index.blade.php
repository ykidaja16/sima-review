@extends('layouts.app')
@section('title', 'Hak Akses Management')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>Hak Akses Management</h2>
        <p>Atur visibilitas dan hak akses menu aplikasi untuk masing-masing role pengguna</p>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px;">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('hak-akses.update') }}">
    @csrf

    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="bi bi-shield-lock-fill" style="color:var(--primary);font-size:1.1rem;"></i>
                <h5 style="margin:0;">Matriks Hak Akses Menu Aplikasi</h5>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-save-fill"></i> Simpan Perubahan Hak Akses
                </button>
            </div>
        </div>

        <div class="card-body" style="padding:0;">
            <div class="table-wrapper" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:2px solid var(--border);">
                            <th style="padding:14px 18px;text-align:left;min-width:260px;font-weight:700;color:var(--text);position:sticky;left:0;background:#f8fafc;z-index:2;box-shadow:2px 0 4px rgba(0,0,0,0.03);">
                                Menu Aplikasi
                            </th>
                            @foreach($roles as $role)
                            <th style="padding:12px 10px;text-align:center;min-width:110px;font-weight:700;">
                                <div style="display:inline-block;padding:4px 8px;border-radius:6px;font-size:0.78rem;background:{{ $role->slug === 'super_admin' ? '#fee2e2;color:#991b1b' : ($role->slug === 'direktur' ? '#fef3c7;color:#92400e' : ($role->slug === 'kacab' ? '#fed7aa;color:#9a3412' : '#e0f2fe;color:#0369a1')) }};">
                                    {{ $role->nama }}
                                </div>
                                <div style="font-size:0.7rem;color:var(--text-muted);font-weight:500;margin-top:4px;">
                                    Level {{ $role->level }}
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($menuCatalog as $group)
                        <tr style="background:#f1f5f9;border-top:1.5px solid var(--border);border-bottom:1.5px solid var(--border);">
                            <td colspan="{{ count($roles) + 1 }}" style="padding:10px 18px;font-weight:800;color:#0f172a;text-transform:uppercase;font-size:0.78rem;letter-spacing:0.5px;">
                                <i class="bi bi-folder2-open" style="margin-right:6px;color:var(--primary);"></i>
                                {{ $group['group'] }}
                            </td>
                        </tr>
                        @foreach($group['menus'] as $menu)
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:12px 18px;position:sticky;left:0;background:#ffffff;z-index:1;box-shadow:2px 0 4px rgba(0,0,0,0.03);">
                                <div style="font-weight:600;color:var(--text);">{{ $menu['title'] }}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:2px;">{{ $menu['desc'] }}</div>
                            </td>
                            @foreach($roles as $role)
                            @php
                                $isChecked = isset($matrix[$role->id][$menu['key']]);
                            @endphp
                            <td style="padding:10px;text-align:center;vertical-align:middle;">
                                <label style="display:inline-flex;align-items:center;justify-content:center;cursor:pointer;margin:0;padding:4px;">
                                    <input type="checkbox"
                                           name="permissions[{{ $role->id }}][{{ $menu['key'] }}]"
                                           value="1"
                                           {{ $isChecked ? 'checked' : '' }}
                                           style="width:18px;height:18px;cursor:pointer;accent-color:var(--primary);">
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer" style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;background:#f8fafc;border-top:1px solid var(--border);flex-wrap:wrap;gap:12px;">
            <div style="font-size:0.8rem;color:var(--text-muted);">
                <i class="bi bi-info-circle"></i> Centang menu yang ingin ditampilkan dan diberikan hak aksesnya untuk role yang bersangkutan.
            </div>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;">
                <i class="bi bi-save-fill"></i> Simpan Perubahan Hak Akses
            </button>
        </div>
    </div>
</form>
@endsection

