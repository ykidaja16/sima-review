<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIMA-REVIEW</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Evaluasi Service Excellent')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 68px;
            --topbar-height: 60px;
            --primary: #1e40af;
            --primary-light: #3b82f6;
            --primary-hover: #1d4ed8;
            --accent: #0891b2;
            --success: #059669;
            --danger: #dc2626;
            --warning: #d97706;
            --info: #0284c7;
            --bg: #f1f5f9;
            --bg-card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --sidebar-bg: #0f172a;
            --sidebar-text: rgba(255,255,255,0.75);
            --sidebar-active: rgba(255,255,255,0.12);
            --sidebar-hover: rgba(255,255,255,0.07);
            --topbar-bg: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.12);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            z-index: 1000;
            overflow: hidden;
        }

        .sidebar.collapsed { width: var(--sidebar-collapsed-width); }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 18px;
            height: var(--topbar-height);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
            text-decoration: none;
        }

        .brand-logo {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(59,130,246,0.35);
        }

        .brand-logo i { color: white; font-size: 17px; }

        .brand-text {
            overflow: hidden;
            transition: var(--transition);
        }

        .brand-text h2 {
            font-size: 0.95rem;
            font-weight: 700;
            color: white;
            white-space: nowrap;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.4);
            white-space: nowrap;
        }

        .sidebar.collapsed .brand-text { width: 0; opacity: 0; }

        /* Sidebar nav */
        .sidebar-nav { flex: 1; overflow-y: auto; overflow-x: hidden; padding: 12px 0; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 2px; }

        .nav-section-title {
            padding: 8px 20px 4px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: rgba(255,255,255,0.25);
            white-space: nowrap;
            overflow: hidden;
            transition: var(--transition);
        }

        .sidebar.collapsed .nav-section-title { opacity: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 18px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 10px;
            margin: 2px 8px;
            transition: var(--transition);
            white-space: nowrap;
            position: relative;
        }

        .nav-item:hover { background: var(--sidebar-hover); color: white; }
        .nav-item.active { background: linear-gradient(135deg, rgba(59,130,246,0.25), rgba(6,182,212,0.15)); color: white; }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -8px; top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%;
            background: linear-gradient(#3b82f6, #06b6d4);
            border-radius: 0 3px 3px 0;
        }

        .nav-item i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-item span {
            overflow: hidden;
            transition: var(--transition);
        }

        .sidebar.collapsed .nav-item span { width: 0; opacity: 0; }
        .sidebar.collapsed .nav-item { padding: 9px 0; margin: 2px; justify-content: center; border-radius: 10px; }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .nav-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%; transform: translateY(-50%);
            background: #1e293b;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
            z-index: 2000;
        }

        .sidebar.collapsed .nav-item:hover::after { opacity: 1; }

        /* Sidebar user bottom */
        .sidebar-user {
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-info { overflow: hidden; transition: var(--transition); }
        .user-info .name { font-size: 0.82rem; font-weight: 600; color: white; white-space: nowrap; }
        .user-info .role { font-size: 0.7rem; color: rgba(255,255,255,0.4); white-space: nowrap; }
        .sidebar.collapsed .user-info { width: 0; opacity: 0; }

        /* ==================== MAIN ==================== */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: var(--transition);
        }

        .main-wrapper.sidebar-collapsed { margin-left: var(--sidebar-collapsed-width); }

        /* ==================== TOPBAR ==================== */
        .topbar {
            height: var(--topbar-height);
            background: var(--topbar-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            position: sticky;
            top: 0; z-index: 100;
            box-shadow: var(--shadow-sm);
            gap: 16px;
        }

        .topbar-toggle {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: none; cursor: pointer;
            border-radius: 8px; color: var(--text-muted);
            transition: var(--transition);
            font-size: 1.1rem;
        }

        .topbar-toggle:hover { background: var(--bg); color: var(--text); }

        .topbar-breadcrumb {
            flex: 1;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .topbar-breadcrumb strong { color: var(--text); }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .topbar-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border: none; background: none; cursor: pointer;
            border-radius: 8px; color: var(--text-muted);
            transition: var(--transition); font-size: 1rem;
            position: relative;
            text-decoration: none;
        }

        .topbar-btn:hover { background: var(--bg); color: var(--text); }

        .topbar-divider { width: 1px; height: 24px; background: var(--border); margin: 0 4px; }

        .topbar-user {
            display: flex; align-items: center; gap: 8px;
            padding: 4px 10px;
            border-radius: 8px; cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: var(--text);
        }

        .topbar-user:hover { background: var(--bg); }

        .topbar-user .name { font-size: 0.82rem; font-weight: 600; }
        .topbar-user .role-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(59,130,246,0.12);
            color: var(--primary-light);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* ==================== CONTENT ==================== */
        .content-area {
            flex: 1;
            padding: 24px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 16px;
        }

        .page-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
        }

        .page-title p {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Cards */
        .card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-header h5 {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
        }

        .card-body { padding: 20px; }
        .card-footer { padding: 12px 20px; border-top: 1px solid var(--border); background: #f8fafc; border-radius: 0 0 var(--radius) var(--radius); }

        /* Stat cards */
        .stat-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 20px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: 0.08;
            transform: translate(20px, -20px);
        }

        .stat-card.blue::before   { background: #3b82f6; }
        .stat-card.green::before  { background: #10b981; }
        .stat-card.orange::before { background: #f59e0b; }
        .stat-card.purple::before { background: #8b5cf6; }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-bottom: 12px;
        }

        .stat-icon.blue   { background: rgba(59,130,246,0.12); color: #3b82f6; }
        .stat-icon.green  { background: rgba(16,185,129,0.12); color: #10b981; }
        .stat-icon.orange { background: rgba(245,158,11,0.12); color: #f59e0b; }
        .stat-icon.purple { background: rgba(139,92,246,0.12); color: #8b5cf6; }

        .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-label { font-size: 0.8rem; color: var(--text-muted); margin-top: 4px; }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            font-size: 0.875rem; font-weight: 500;
            font-family: 'Inter', sans-serif;
            border: none; border-radius: var(--radius-sm);
            cursor: pointer; text-decoration: none;
            transition: var(--transition);
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .btn-success { background: var(--success); color: white; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-info { background: var(--info); color: white; }
        .btn-secondary { background: #64748b; color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
        .btn-outline:hover { background: var(--bg); }
        .btn-sm { padding: 4px 10px; font-size: 0.8rem; }
        .btn-lg { padding: 10px 20px; font-size: 1rem; }

        /* Tables */
        .table-wrapper { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }

        tbody td {
            padding: 12px 16px;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #f8fafc; }

        /* Forms */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: 0.825rem; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-control, .form-select {
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: white;
            transition: var(--transition);
            outline: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { font-size: 0.78rem; color: var(--danger); margin-top: 4px; }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 8px;
            font-size: 0.72rem; font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-success { background: rgba(5,150,105,0.12); color: #059669; }
        .badge-primary { background: rgba(59,130,246,0.12); color: #2563eb; }
        .badge-info    { background: rgba(2,132,199,0.12);  color: #0284c7; }
        .badge-warning { background: rgba(217,119,6,0.12);  color: #d97706; }
        .badge-danger  { background: rgba(220,38,38,0.12);  color: #dc2626; }
        .badge-secondary{ background: rgba(100,116,139,0.12); color: #475569; }

        /* Grid */
        .grid { display: grid; }
        .grid-2 { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .grid-3 { grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, 1fr); gap: 20px; }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 16px;
        }
        .alert-success { background: rgba(5,150,105,0.1); border: 1px solid rgba(5,150,105,0.25); color: #065f46; }
        .alert-danger  { background: rgba(220,38,38,0.1);  border: 1px solid rgba(220,38,38,0.25);  color: #991b1b; }
        .alert-warning { background: rgba(217,119,6,0.1);  border: 1px solid rgba(217,119,6,0.25);  color: #92400e; }
        .alert-info    { background: rgba(2,132,199,0.1);  border: 1px solid rgba(2,132,199,0.25);  color: #075985; }

        /* Pagination */
        .pagination { display: flex; align-items: center; gap: 4px; list-style: none; }
        .pagination .page-item .page-link {
            display: flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
        }
        .pagination .page-item.active .page-link { background: var(--primary); color: white; }
        .pagination .page-item .page-link:hover { background: var(--bg); color: var(--text); }
        .pagination .page-item.disabled .page-link { opacity: 0.4; pointer-events: none; }

        /* Logout form */
        .logout-form button {
            background: none; border: none; width: 100%;
            text-align: left; cursor: pointer; color: inherit;
            font-size: inherit; font-family: inherit; padding: 0;
        }

        /* Utilities */
        .d-flex { display: flex; }
        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .mt-1 { margin-top: 4px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .text-muted { color: var(--text-muted); }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }
        .fs-sm { font-size: 0.8rem; }
        .w-100 { width: 100%; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .main-wrapper { margin-left: 0; }
            .grid-4 { grid-template-columns: repeat(2, 1fr); }
            .grid-3 { grid-template-columns: repeat(2, 1fr); }
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('images/logosima.png') }}" alt="SIMA" style="height:34px;flex-shrink:0;">
            <div class="brand-text">
                <h2>SIMA-REVIEW</h2>
                <span>Service Excellent</span>
            </div>
        </a>

        <nav class="sidebar-nav" id="sidebarNav">

            {{-- Dashboard --}}
            <div class="nav-section-title">Utama</div>
            <a href="{{ route('dashboard') }}"
               class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               data-tooltip="Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>

            {{-- Penilaian --}}
            @if(auth()->user()->hasRole(['super_admin','manager','supervisor']))
            <div class="nav-section-title">Penilaian</div>

            @if(auth()->user()->hasRole(['super_admin','manager']))
            <a href="{{ route('periode.index') }}"
               class="nav-item {{ request()->routeIs('periode.*') ? 'active' : '' }}"
               data-tooltip="Periode">
                <i class="bi bi-calendar3"></i>
                <span>Periode Penilaian</span>
            </a>
            @endif

            <a href="{{ route('penilaian.index') }}"
               class="nav-item {{ request()->routeIs('penilaian.*') ? 'active' : '' }}"
               data-tooltip="Penilaian">
                <i class="bi bi-clipboard2-check"></i>
                <span>Input Penilaian</span>
            </a>
            @endif

            {{-- Pelaksana: lihat nilai sendiri --}}
            @if(auth()->user()->isPelaksana())
            <div class="nav-section-title">Nilai Saya</div>
            <a href="{{ route('monitoring.individu') }}"
               class="nav-item {{ request()->routeIs('monitoring.individu') ? 'active' : '' }}"
               data-tooltip="Nilai Saya">
                <i class="bi bi-person-check"></i>
                <span>Track Record Saya</span>
            </a>
            @endif

            {{-- Monitoring --}}
            @if(auth()->user()->hasRole(['super_admin','manager','supervisor']))
            <div class="nav-section-title">Monitoring</div>
            <a href="{{ route('monitoring.individu') }}"
               class="nav-item {{ request()->routeIs('monitoring.individu') ? 'active' : '' }}"
               data-tooltip="Individu">
                <i class="bi bi-person-lines-fill"></i>
                <span>Track Record Individu</span>
            </a>
            <a href="{{ route('monitoring.tim') }}"
               class="nav-item {{ request()->routeIs('monitoring.tim') ? 'active' : '' }}"
               data-tooltip="Tim">
                <i class="bi bi-people-fill"></i>
                <span>Monitoring Tim</span>
            </a>
            @if(auth()->user()->hasRole(['super_admin','manager']))
            <a href="{{ route('monitoring.divisi') }}"
               class="nav-item {{ request()->routeIs('monitoring.divisi') ? 'active' : '' }}"
               data-tooltip="Divisi">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Perbandingan Divisi</span>
            </a>
            <a href="{{ route('monitoring.ranking') }}"
               class="nav-item {{ request()->routeIs('monitoring.ranking') ? 'active' : '' }}"
               data-tooltip="Ranking">
                <i class="bi bi-trophy-fill"></i>
                <span>Ranking Karyawan</span>
            </a>
            @endif
            @endif

            {{-- Laporan --}}
            @if(auth()->user()->hasRole(['super_admin','manager','supervisor']))
            <a href="{{ route('laporan.index') }}"
               class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
               data-tooltip="Laporan">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Laporan & Export</span>
            </a>
            @endif

            {{-- Master Data --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="nav-section-title">Master Data</div>
            <a href="{{ route('master.divisi.index') }}"
               class="nav-item {{ request()->routeIs('master.divisi.*') ? 'active' : '' }}"
               data-tooltip="Divisi">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Divisi</span>
            </a>
            <a href="{{ route('master.jabatan.index') }}"
               class="nav-item {{ request()->routeIs('master.jabatan.*') ? 'active' : '' }}"
               data-tooltip="Jabatan">
                <i class="bi bi-person-badge-fill"></i>
                <span>Jabatan</span>
            </a>
            <a href="{{ route('master.karyawan.index') }}"
               class="nav-item {{ request()->routeIs('master.karyawan.*') ? 'active' : '' }}"
               data-tooltip="Karyawan">
                <i class="bi bi-people-fill"></i>
                <span>Karyawan</span>
            </a>
            <a href="{{ route('master.parameter-sop.index') }}"
               class="nav-item {{ request()->routeIs('master.parameter-sop.*') ? 'active' : '' }}"
               data-tooltip="Parameter SOP">
                <i class="bi bi-list-check"></i>
                <span>Parameter SOP</span>
            </a>
            <a href="{{ route('master.kategori-nilai.index') }}"
               class="nav-item {{ request()->routeIs('master.kategori-nilai.*') ? 'active' : '' }}"
               data-tooltip="Kategori Nilai">
                <i class="bi bi-tags-fill"></i>
                <span>Kategori Nilai</span>
            </a>

            <div class="nav-section-title">Administrasi</div>
            <a href="{{ route('user-management.index') }}"
               class="nav-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}"
               data-tooltip="User">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('audit-log.index') }}"
               class="nav-item {{ request()->routeIs('audit-log.*') ? 'active' : '' }}"
               data-tooltip="Audit Log">
                <i class="bi bi-clock-history"></i>
                <span>Audit Log</span>
            </a>
            @endif

        </nav>

        <div class="sidebar-user">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ auth()->user()->role_label }}</div>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN WRAPPER ==================== -->
    <div class="main-wrapper" id="mainWrapper">

        <!-- TOPBAR -->
        <header class="topbar">
            <button class="topbar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>

            <div class="topbar-breadcrumb">
                @hasSection('breadcrumb')
                    @yield('breadcrumb')
                @else
                    <strong>Dashboard</strong>
                @endif
            </div>

            <div class="topbar-actions">
                <div class="topbar-divider"></div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <div class="user-avatar" style="width:32px;height:32px;font-size:12px;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="topbar-user">
                            <div>
                                <div class="name">{{ auth()->user()->name }}</div>
                            </div>
                            <span class="role-badge">{{ auth()->user()->role_label }}</span>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="topbar-btn" title="Logout">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        <main class="content-area">

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <div>
                        <strong>Ada kesalahan pada input:</strong>
                        <ul style="margin-top:4px; padding-left:16px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('mainWrapper');
        const toggleBtn = document.getElementById('sidebarToggle');

        const sidebarState = localStorage.getItem('sidebarCollapsed') === 'true';
        if (sidebarState) {
            sidebar.classList.add('collapsed');
            mainWrapper.classList.add('sidebar-collapsed');
        }

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Auto dismiss alerts
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    </script>

    @stack('scripts')
</body>
</html>
