<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SIMA-MONEV</title>
    <meta name="description" content="@yield('meta_description', 'Sistem Evaluasi Service Excellence')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

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

        html, body {
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            margin: 0;
            padding: 0;
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

        .sidebar.collapsed .nav-section-title { opacity: 0; height: 0; padding: 0; margin: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 16px;
            margin: 2px 10px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.07);
            color: white;
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(59,130,246,0.9), rgba(37,99,235,0.9));
            color: white;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }

        .nav-item i {
            font-size: 1.05rem;
            flex-shrink: 0;
            width: 20px;
            text-align: center;
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
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

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
            width: 100%;
            box-sizing: border-box;
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
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
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
            width: 100%;
            box-sizing: border-box;
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

        .card-body { padding: 20px; box-sizing: border-box; }
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
            box-sizing: border-box;
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
        .table-wrapper { overflow-x: auto; width: 100%; }

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
            box-sizing: border-box;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .invalid-feedback { font-size: 0.78rem; color: var(--danger); margin-top: 4px; }

        /* Input Pencarian dengan Icon (Persis seperti referensi) */
        .search-input-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            position: relative;
            min-height: 38px;
            overflow: hidden;
        }
        .search-input-box:focus-within {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
        }
        .search-input-box .search-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px 0 12px;
            color: #00bcd4;
            font-size: 1.05rem;
            pointer-events: none;
            flex-shrink: 0;
        }
        .search-input-box input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            color: var(--text);
            padding: 8px 12px 8px 0;
            width: 100%;
        }
        .search-input-box input::placeholder {
            color: #94a3b8;
        }

        /* Searchable Select / Combobox */
        .ss-wrapper { position: relative; width: 100%; }
        .ss-control-box {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
            position: relative;
            min-height: 38px;
            overflow: hidden;
        }
        .ss-wrapper.open .ss-control-box,
        .ss-control-box:focus-within {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25);
        }
        .ss-icon-search {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 10px 0 12px;
            color: #00bcd4;
            font-size: 1.05rem;
            pointer-events: none;
            flex-shrink: 0;
        }
        .ss-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Inter', sans-serif;
            font-size: 0.88rem;
            color: var(--text);
            padding: 8px 6px 8px 0;
            width: 100%;
            min-width: 60px;
        }
        .ss-input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }
        .ss-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            min-height: 38px;
            align-self: stretch;
            background: #00bcd4;
            border: none;
            color: #0f172a;
            cursor: pointer;
            font-size: 0.75rem;
            transition: background 0.15s ease;
            flex-shrink: 0;
        }
        .ss-toggle-btn:hover {
            background: #00acc1;
        }
        .ss-wrapper.open .ss-toggle-btn i {
            transform: rotate(180deg);
        }
        .ss-dropdown {
            display: none;
            position: absolute;
            top: calc(100% + 7px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 10px;
            box-shadow: 0 12px 28px rgba(0,0,0,0.12), 0 4px 10px rgba(0,0,0,0.06);
            z-index: 1050;
            max-width: 100%;
        }
        .ss-dropdown::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 20px;
            width: 10px;
            height: 10px;
            background: #ffffff;
            border-top: 1px solid var(--border);
            border-left: 1px solid var(--border);
            transform: rotate(45deg);
        }
        .ss-wrapper.open .ss-dropdown { display: block; }
        .ss-list {
            max-height: 240px;
            overflow-y: auto;
            list-style: none;
            margin: 0;
            padding: 6px 0;
        }
        .ss-option {
            padding: 9px 16px;
            font-size: 0.88rem;
            color: var(--text);
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ss-option:hover, .ss-option.focused {
            background: #f0f9ff;
            color: #0284c7;
        }
        .ss-option.selected {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }
        .ss-empty {
            padding: 12px 16px;
            font-size: 0.84rem;
            color: var(--text-muted);
            text-align: center;
        }

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

        /* Grid System */
        .grid { display: grid; width: 100%; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 20px; }
        .grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; }

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

        /* Clean Pagination */
        nav .pagination, ul.pagination {
            display: flex !important;
            padding-left: 0;
            list-style: none;
            gap: 4px;
            margin: 16px 0 0;
            justify-content: flex-end;
            align-items: center;
        }
        .pagination .page-item .page-link,
        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text);
            background: white;
            border: 1px solid var(--border);
            text-decoration: none;
            transition: var(--transition);
        }
        .pagination .page-item.active .page-link,
        .pagination li.active span {
            background: var(--primary) !important;
            color: white !important;
            border-color: var(--primary) !important;
        }
        .pagination .page-item.disabled .page-link,
        .pagination li.disabled span {
            color: var(--text-muted);
            opacity: 0.4;
            pointer-events: none;
            background: var(--bg);
        }
        .pagination svg {
            width: 14px;
            height: 14px;
        }

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
        .fs-sm { font-size: 0.8rem; }
        .w-100 { width: 100%; }

        /* Score colors */
        .text-score-success { color: #059669 !important; }
        .text-score-primary { color: #2563eb !important; }
        .text-score-warning { color: #d97706 !important; }
        .text-score-danger  { color: #dc2626 !important; }
        .text-score-secondary { color: #64748b !important; }
        .bg-score-success { background-color: #059669 !important; }
        .bg-score-primary { background-color: #2563eb !important; }
        .bg-score-warning { background-color: #d97706 !important; }
        .bg-score-danger  { background-color: #dc2626 !important; }

        /* Rank badges & helper */
        .rank-badge-1 { background: linear-gradient(135deg, #f59e0b, #ef4444); color: white; }
        .rank-badge-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
        .rank-badge-3 { background: linear-gradient(135deg, #92400e, #d97706); color: white; }
        .rank-badge-4 { background: var(--bg); color: var(--text-muted); }
        .rank-top-3 { background: rgba(245, 158, 11, 0.03); }
        .d-none { display: none !important; }

        /* ==================== RESPONSIVE DESIGN ==================== */
        /* Sidebar Overlay for Mobile/Tablet */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Tablet & Small Desktop (<= 1024px) */
        @media (max-width: 1024px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
            .grid-3 { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
            .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
            .content-area { padding: 20px 16px; }
            .page-header { margin-bottom: 20px; }
        }

        /* Mobile & Tablet Portrait (<= 768px) */
        @media (max-width: 768px) {
            /* Sidebar off-canvas drawer */
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
                max-width: 85vw;
                box-shadow: 6px 0 24px rgba(0, 0, 0, 0.3);
                z-index: 1050;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .sidebar.mobile-open {
                transform: translateX(0) !important;
            }

            /* Main wrapper full width on mobile */
            .main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
                min-width: 0;
            }

            /* Content area compact padding */
            .content-area {
                padding: 16px 12px;
                width: 100%;
                overflow-x: hidden;
            }

            /* Topbar adjustments */
            .topbar {
                padding: 0 12px;
                gap: 8px;
            }
            .topbar-breadcrumb {
                font-size: 0.8rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 180px;
            }
            .topbar-user .name,
            .topbar-user .role-badge {
                display: none;
            }
            .topbar-divider {
                display: none;
            }

            /* Page header stacks gracefully */
            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 16px;
            }
            .page-header > div:last-child,
            .page-header > a,
            .page-header > button,
            .page-header > .d-flex {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                width: 100%;
            }

            /* Grids flow to single column */
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .grid-3 { grid-template-columns: 1fr; gap: 12px; }
            .grid-2 { grid-template-columns: 1fr; gap: 12px; }

            /* Stat cards on mobile */
            .stat-card {
                padding: 14px;
            }
            .stat-value {
                font-size: 1.45rem;
            }

            /* Tables with smooth touch scrolling */
            .table-wrapper {
                -webkit-overflow-scrolling: touch;
                border-radius: var(--radius-sm);
                position: relative;
            }
            table {
                min-width: 560px;
            }

            /* Cards */
            .card {
                margin-bottom: 16px;
                border-radius: 10px;
            }
            .card-header {
                padding: 12px 14px;
                flex-wrap: wrap;
                gap: 8px;
            }
            .card-body {
                padding: 14px;
            }

            /* Forms & Filters */
            .form-group {
                margin-bottom: 1rem;
            }

            /* Pagination wrap */
            nav .pagination, ul.pagination {
                flex-wrap: wrap;
                justify-content: center;
                gap: 2px;
            }
        }

        /* Small Phones (<= 480px) */
        @media (max-width: 480px) {
            .grid-4 { grid-template-columns: 1fr; }
            .content-area { padding: 12px 8px; }
            .topbar-toggle { width: 38px; height: 38px; font-size: 1.2rem; }
            .topbar-breadcrumb { display: none; }
            .stat-card { padding: 12px; }
            .card-header h5 { font-size: 0.92rem; }
        }
    </style>

    @stack('styles')

    {{-- Fix sidebar flash: apply collapsed state before paint --}}
    <script>
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-init-collapsed');
            }
        })();
    </script>
    <style>
        html.sidebar-init-collapsed .sidebar { width: var(--sidebar-collapsed-width); }
        html.sidebar-init-collapsed .main-wrapper { margin-left: var(--sidebar-collapsed-width); }
    </style>
</head>
<body>

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <img src="{{ asset('images/logosima.png') }}" alt="SIMA" style="height:34px;flex-shrink:0;">
            <div class="brand-text">
                <h2>SIMA-MONEV</h2>
                <span>Service Excellence</span>
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
            @if(auth()->user()->hasRole(['super_admin','direktur','manager','kacab','supervisor']))
            <div class="nav-section-title">Penilaian</div>

            @if(auth()->user()->hasRole(['super_admin','kacab']) && auth()->user()->canAccessMenu('periode_kelola'))
            <a href="{{ route('periode.index') }}"
               class="nav-item {{ request()->routeIs('periode.*') ? 'active' : '' }}"
               data-tooltip="Periode">
                <i class="bi bi-calendar3"></i>
                <span>Periode Penilaian</span>
            </a>
            @endif

            @if(auth()->user()->canAccessMenu('penilaian_input'))
            <a href="{{ route('penilaian.index') }}"
               class="nav-item {{ request()->routeIs('penilaian.*') ? 'active' : '' }}"
               data-tooltip="Penilaian">
                <i class="bi bi-clipboard2-check"></i>
                <span>Input Penilaian</span>
            </a>
            @endif
            @endif

            {{-- Nilai Saya (Pelaksana & Supervisor) --}}
            @if(auth()->user()->isPelaksana() || auth()->user()->isSupervisor())
            <div class="nav-section-title">Nilai Saya</div>
            <a href="{{ route('monitoring.individu', ['saya' => 1]) }}"
               class="nav-item {{ (request()->routeIs('monitoring.individu') && (request()->boolean('saya') || auth()->user()->isPelaksana())) ? 'active' : '' }}"
               data-tooltip="Nilai Saya">
                <i class="bi bi-person-check"></i>
                <span>Track Record Saya</span>
            </a>
            @endif

            {{-- Monitoring: Super Admin tidak memerlukan menu ini sesuai Request #2 --}}
            @if(!auth()->user()->isSuperAdmin() && auth()->user()->hasRole(['direktur','manager','kacab','supervisor','mutu']))
            <div class="nav-section-title">Monitoring</div>

            @if(auth()->user()->canAccessMenu('monitoring_individu'))
            <a href="{{ route('monitoring.individu') }}"
               class="nav-item {{ (request()->routeIs('monitoring.individu') && !request()->boolean('saya') && !auth()->user()->isPelaksana()) ? 'active' : '' }}"
               data-tooltip="Individu">
                <i class="bi bi-person-lines-fill"></i>
                <span>Track Record Individu</span>
            </a>
            @endif

            @if(auth()->user()->canAccessMenu('monitoring_tim'))
            <a href="{{ route('monitoring.tim') }}"
               class="nav-item {{ request()->routeIs('monitoring.tim') ? 'active' : '' }}"
               data-tooltip="Tim">
                <i class="bi bi-people-fill"></i>
                <span>Monitoring Tim</span>
            </a>
            @endif

            @if(auth()->user()->hasRole(['direktur','manager','kacab','mutu']))
            @if(auth()->user()->canAccessMenu('monitoring_divisi'))
            <a href="{{ route('monitoring.divisi') }}"
               class="nav-item {{ request()->routeIs('monitoring.divisi') ? 'active' : '' }}"
               data-tooltip="Divisi">
                <i class="bi bi-bar-chart-fill"></i>
                <span>Perbandingan Divisi</span>
            </a>
            @endif

            @if(auth()->user()->canAccessMenu('monitoring_ranking'))
            <a href="{{ route('monitoring.ranking') }}"
               class="nav-item {{ request()->routeIs('monitoring.ranking') ? 'active' : '' }}"
               data-tooltip="Ranking">
                <i class="bi bi-trophy-fill"></i>
                <span>Ranking Karyawan</span>
            </a>
            @endif
            @endif
            @endif

            {{-- Laporan --}}
            @if(auth()->user()->hasRole(['super_admin','direktur','manager','kacab','supervisor','mutu']) && auth()->user()->canAccessMenu('laporan'))
            <a href="{{ route('laporan.index') }}"
               class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}"
               data-tooltip="Laporan">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Laporan &amp; Export</span>
            </a>
            @endif

            {{-- Ketidaksesuaian: semua role --}}
            @if(auth()->user()->canAccessMenu('ketidaksesuaian'))
            <div class="nav-section-title">Review</div>
            <a href="{{ route('ketidaksesuaian.index') }}"
               class="nav-item {{ request()->routeIs('ketidaksesuaian.*') ? 'active' : '' }}"
               data-tooltip="FTKP">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>FTKP / Ketidaksesuaian</span>
            </a>
            @endif

            {{-- Survei Pelanggan --}}
            @if(auth()->user()->canAccessMenu('survei_data') || auth()->user()->canAccessMenu('survei_analisa'))
            <div class="nav-section-title">Survei Pelanggan</div>
            @if(auth()->user()->canAccessMenu('survei_data'))
            <a href="{{ route('survei-pelanggan.index') }}"
               class="nav-item {{ request()->routeIs('survei-pelanggan.index') ? 'active' : '' }}"
               data-tooltip="Data Survei">
                <i class="bi bi-card-list"></i>
                <span>Data Survei</span>
            </a>
            @endif
            @if(auth()->user()->hasRole(['super_admin', 'direktur', 'kacab', 'mutu']))
            @if(auth()->user()->canAccessMenu('survei_analisa'))
            <a href="{{ route('survei-pelanggan.analisa') }}"
               class="nav-item {{ request()->routeIs('survei-pelanggan.analisa') ? 'active' : '' }}"
               data-tooltip="Analisa Data">
                <i class="bi bi-bar-chart-line-fill"></i>
                <span>Analisa Data</span>
            </a>
            @endif
            @if(auth()->user()->canAccessMenu('survei_grafik'))
            <a href="{{ route('survei-pelanggan.grafik') }}"
               class="nav-item {{ request()->routeIs('survei-pelanggan.grafik') ? 'active' : '' }}"
               data-tooltip="Internal Grafik">
                <i class="bi bi-diagram-2-fill"></i>
                <span>Internal Grafik</span>
            </a>
            @endif
            @if(auth()->user()->canAccessMenu('survei_pendukung'))
            <a href="{{ route('survei-pelanggan.pendukung') }}"
               class="nav-item {{ request()->routeIs('survei-pelanggan.pendukung') ? 'active' : '' }}"
               data-tooltip="Data Pendukung">
                <i class="bi bi-pie-chart-fill"></i>
                <span>Data Pendukung</span>
            </a>
            @endif
            @endif
            @endif

            {{-- Master Data --}}
            @if(auth()->user()->isSuperAdmin())
            <div class="nav-section-title">Master Data</div>
            <a href="{{ route('master.cabang.index') }}"
               class="nav-item {{ request()->routeIs('master.cabang.*') ? 'active' : '' }}"
               data-tooltip="Cabang">
                <i class="bi bi-building-fill"></i>
                <span>Cabang</span>
            </a>
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
            <a href="{{ route('master.jenis-ketidaksesuaian.index') }}"
               class="nav-item {{ request()->routeIs('master.jenis-ketidaksesuaian.*') ? 'active' : '' }}"
               data-tooltip="Jenis FTKP">
                <i class="bi bi-card-checklist"></i>
                <span>Jenis Ketidaksesuaian</span>
            </a>

            <div class="nav-section-title">Administrasi</div>
            <a href="{{ route('user-management.index') }}"
               class="nav-item {{ request()->routeIs('user-management.*') ? 'active' : '' }}"
               data-tooltip="User">
                <i class="bi bi-person-gear"></i>
                <span>User Management</span>
            </a>
            <a href="{{ route('hak-akses.index') }}"
               class="nav-item {{ request()->routeIs('hak-akses.*') ? 'active' : '' }}"
               data-tooltip="Hak Akses">
                <i class="bi bi-shield-lock-fill"></i>
                <span>Hak Akses Management</span>
            </a>
            <a href="{{ route('audit-log.index') }}"
               class="nav-item {{ request()->routeIs('audit-log.*') ? 'active' : '' }}"
               data-tooltip="Audit Log">
                <i class="bi bi-clock-history"></i>
                <span>Audit Log</span>
            </a>
            @endif

            {{-- Pengaturan Akun Saya (Semua Role) --}}
            <div class="nav-section-title">Akun</div>
            <a href="{{ route('profile.edit') }}"
               class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}"
               data-tooltip="Update Data Diri">
                <i class="bi bi-person-gear"></i>
                <span>Update Data Diri</span>
            </a>

        </nav>

        <a href="{{ route('profile.edit') }}" class="sidebar-user" title="Update Data Diri" style="text-decoration:none;color:inherit;cursor:pointer;">
            <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ auth()->user()->role_label }}</div>
            </div>
            <i class="bi bi-pencil-square" style="color:rgba(255,255,255,0.4);margin-left:auto;font-size:0.85rem;" title="Edit Data Diri"></i>
        </a>
    </aside>

    <!-- SIDEBAR OVERLAY FOR MOBILE -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
                    <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:inherit; cursor:pointer;" title="Update Data Diri">
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
                    </a>
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
        // Responsive sidebar & overlay toggle
        const sidebar        = document.getElementById('sidebar');
        const mainWrapper    = document.getElementById('mainWrapper');
        const toggleBtn      = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Check if device is currently mobile/tablet portrait
        const isMobile = () => window.innerWidth <= 768;

        // Init desktop state
        if (!isMobile()) {
            const sidebarState = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarState) {
                sidebar.classList.add('collapsed');
                mainWrapper.classList.add('sidebar-collapsed');
            }
        }
        document.documentElement.classList.remove('sidebar-init-collapsed');

        // Toggle button handler
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (isMobile()) {
                const isOpen = sidebar.classList.toggle('mobile-open');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('active', isOpen);
                document.body.style.overflow = isOpen ? 'hidden' : '';
            } else {
                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });

        // Close mobile sidebar on backdrop click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close mobile sidebar when clicking a nav link
        document.querySelectorAll('#sidebarNav a.nav-item').forEach(link => {
            link.addEventListener('click', () => {
                if (isMobile()) {
                    sidebar.classList.remove('mobile-open');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });

        // Close mobile sidebar on window resize if viewport expands to desktop
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Auto dismiss alerts
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });

        // Initialize Searchable Selects / Combobox
        function initSearchableSelects() {
            document.querySelectorAll('select.searchable-select').forEach(select => {
                if (select.dataset.ssInitialized === 'true') return;
                select.dataset.ssInitialized = 'true';
                select.style.display = 'none';

                const wrapper = document.createElement('div');
                wrapper.className = 'ss-wrapper';
                select.parentNode.insertBefore(wrapper, select);
                wrapper.appendChild(select);

                const controlBox = document.createElement('div');
                controlBox.className = 'ss-control-box';

                const iconSearch = document.createElement('span');
                iconSearch.className = 'ss-icon-search';
                iconSearch.innerHTML = '<i class="bi bi-search"></i>';
                controlBox.appendChild(iconSearch);

                const input = document.createElement('input');
                input.type = 'text';
                input.className = 'ss-input';
                input.placeholder = select.dataset.placeholder || (select.options[0] ? select.options[0].textContent.trim() : 'Ketik untuk mencari...');
                input.autocomplete = 'off';
                input.spellcheck = false;
                controlBox.appendChild(input);

                const toggleBtn = document.createElement('button');
                toggleBtn.type = 'button';
                toggleBtn.className = 'ss-toggle-btn';
                toggleBtn.setAttribute('aria-label', 'Buka pilihan');
                toggleBtn.innerHTML = '<i class="bi bi-caret-down-fill"></i>';
                controlBox.appendChild(toggleBtn);

                wrapper.appendChild(controlBox);

                const dropdown = document.createElement('div');
                dropdown.className = 'ss-dropdown';

                const list = document.createElement('ul');
                list.className = 'ss-list';
                dropdown.appendChild(list);
                wrapper.appendChild(dropdown);

                let activeIndex = -1;

                function findOptionByText(text) {
                    if (!text) return null;
                    const q = text.toLowerCase().trim();
                    if (!q) return null;

                    const validOpts = Array.from(select.options).filter(opt => !opt.hidden && opt.value !== '');
                    // 1. Exact match
                    let match = validOpts.find(opt => opt.textContent.trim().toLowerCase() === q);
                    if (match) return match;
                    // 2. Starts with
                    match = validOpts.find(opt => opt.textContent.trim().toLowerCase().startsWith(q));
                    if (match) return match;
                    // 3. Contains
                    match = validOpts.find(opt => opt.textContent.trim().toLowerCase().includes(q));
                    if (match) return match;
                    return null;
                }

                function updateInputFromSelected() {
                    const selectedOpt = select.options[select.selectedIndex];
                    if (selectedOpt && selectedOpt.value !== '' && !selectedOpt.hidden) {
                        input.value = selectedOpt.textContent.trim();
                    } else {
                        input.value = '';
                    }
                }

                function syncFromInput() {
                    const valText = input.value.trim();
                    if (valText === '') {
                        if (select.value !== '') {
                            select.value = '';
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    } else {
                        const matched = findOptionByText(valText);
                        if (matched) {
                            if (select.value !== matched.value) {
                                select.value = matched.value;
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                            }
                            input.value = matched.textContent.trim();
                        } else {
                            updateInputFromSelected();
                        }
                    }
                }

                function renderOptions(filterText = '') {
                    list.innerHTML = '';
                    const q = filterText.toLowerCase().trim();
                    let visibleCount = 0;
                    activeIndex = -1;

                    Array.from(select.options).forEach((opt, idx) => {
                        if (opt.hidden || (opt.disabled && opt.value !== '')) return;
                        const text = opt.textContent.trim();
                        if (q && !text.toLowerCase().includes(q)) return;

                        visibleCount++;
                        const li = document.createElement('li');
                        li.className = 'ss-option' + (opt.selected ? ' selected' : '');
                        li.textContent = text;
                        li.dataset.value = opt.value;

                        li.addEventListener('click', (e) => {
                            e.stopPropagation();
                            selectOption(opt.value, text);
                        });
                        list.appendChild(li);
                    });

                    if (visibleCount === 0) {
                        const empty = document.createElement('li');
                        empty.className = 'ss-empty';
                        empty.textContent = 'Tidak ditemukan hasil';
                        list.appendChild(empty);
                    }
                }

                function selectOption(val, text) {
                    select.value = val;
                    if (val === '') {
                        input.value = '';
                    } else {
                        input.value = text;
                    }
                    wrapper.classList.remove('open');
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }

                function openDropdown(filter = '') {
                    document.querySelectorAll('.ss-wrapper.open').forEach(w => {
                        if (w !== wrapper) w.classList.remove('open');
                    });
                    wrapper.classList.add('open');
                    renderOptions(filter);
                }

                function closeDropdown() {
                    wrapper.classList.remove('open');
                    syncFromInput();
                }

                input.addEventListener('focus', () => {
                    openDropdown('');
                });

                input.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (!wrapper.classList.contains('open')) {
                        openDropdown('');
                    }
                });

                input.addEventListener('input', () => {
                    if (!wrapper.classList.contains('open')) {
                        wrapper.classList.add('open');
                    }
                    renderOptions(input.value);
                });

                toggleBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (wrapper.classList.contains('open')) {
                        closeDropdown();
                    } else {
                        input.focus();
                        openDropdown('');
                    }
                });

                input.addEventListener('keydown', (e) => {
                    const items = list.querySelectorAll('.ss-option');
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        if (!wrapper.classList.contains('open')) {
                            openDropdown('');
                            return;
                        }
                        if (items.length) {
                            activeIndex = (activeIndex + 1) % items.length;
                            highlightItem(items);
                        }
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        if (!wrapper.classList.contains('open')) return;
                        if (items.length) {
                            activeIndex = (activeIndex - 1 + items.length) % items.length;
                            highlightItem(items);
                        }
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (wrapper.classList.contains('open') && activeIndex >= 0 && items[activeIndex]) {
                            items[activeIndex].click();
                        } else {
                            syncFromInput();
                            wrapper.classList.remove('open');
                            const parentForm = select.closest('form');
                            if (parentForm) {
                                if (typeof parentForm.requestSubmit === 'function') {
                                    parentForm.requestSubmit();
                                } else {
                                    parentForm.submit();
                                }
                            }
                        }
                    } else if (e.key === 'Escape') {
                        closeDropdown();
                    }
                });

                function highlightItem(items) {
                    items.forEach((item, idx) => {
                        if (idx === activeIndex) {
                            item.classList.add('focused');
                            item.scrollIntoView({ block: 'nearest' });
                        } else {
                            item.classList.remove('focused');
                        }
                    });
                }

                select._refreshSearchableSelect = () => {
                    updateInputFromSelected();
                    if (wrapper.classList.contains('open')) {
                        renderOptions(input.value);
                    }
                };

                select._syncFromInput = syncFromInput;

                // Sync select value before parent form submit
                const parentForm = select.closest('form');
                if (parentForm && !parentForm._hasSsSubmitSync) {
                    parentForm._hasSsSubmitSync = true;
                    parentForm.addEventListener('submit', () => {
                        parentForm.querySelectorAll('select.searchable-select').forEach(s => {
                            if (typeof s._syncFromInput === 'function') {
                                s._syncFromInput();
                            }
                        });
                    });
                }

                updateInputFromSelected();
            });
        }

        document.addEventListener('click', (e) => {
            document.querySelectorAll('.ss-wrapper.open').forEach(w => {
                if (!w.contains(e.target)) {
                    w.classList.remove('open');
                    const select = w.querySelector('select');
                    if (select && typeof select._syncFromInput === 'function') {
                        select._syncFromInput();
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', initSearchableSelects);
    </script>

    @stack('scripts')
</body>
</html>
