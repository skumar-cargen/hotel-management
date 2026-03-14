@props([
    'title' => 'Admin',
    'pageTitle' => '',
    'pageDescription' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link href="{{ asset('vendor/fonts/plus-jakarta-sans/plus-jakarta-sans.css') }}" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Boxicons -->
    <link href="{{ asset('assets/css/boxicons.min.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet">
    <!-- DataTables -->
    <link href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/responsive.bootstrap5.min.css') }}" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════
           COLORFUL & VIBRANT ADMIN THEME
           Dubai Apartments — Multi-Domain Hotel Platform
           ═══════════════════════════════════════════════════════ */

        :root {
            /* Layout */
            --sidebar-width: 270px;
            --sidebar-collapsed: 78px;
            --header-height: 64px;

            /* Gradient Colors */
            --grad-start: #667eea;
            --grad-mid: #764ba2;
            --grad-end: #f093fb;

            /* Accent Colors */
            --accent-primary: #667eea;
            --accent-secondary: #764ba2;
            --accent-success: #00c853;
            --accent-warning: #ffab00;
            --accent-danger: #ff5252;
            --accent-info: #00b0ff;
            --accent-pink: #f50057;
            --accent-teal: #1de9b6;
            --accent-orange: #ff6d00;

            /* Backgrounds */
            --bg-body: #f0f2f8;
            --bg-card: #ffffff;
            --bg-header: rgba(255,255,255,0.95);

            /* Text */
            --text-primary: #1a1d29;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;

            /* Shadows */
            --shadow-sm: 0 1px 3px rgba(102,126,234,0.08);
            --shadow-md: 0 4px 16px rgba(102,126,234,0.12);
            --shadow-lg: 0 10px 30px rgba(102,126,234,0.16);
            --shadow-xl: 0 20px 60px rgba(102,126,234,0.2);

            /* Border */
            --border-color: #e5e7eb;
            --border-light: #f3f4f6;

            /* Radius */
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.25rem;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            margin: 0;
            overflow-x: hidden;
        }

        /* ── Scrollbar ──────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c5cae9; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9fa8da; }

        /* ═══════════════════════════════════════════════════════
           SIDEBAR
           ═══════════════════════════════════════════════════════ */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1a1d3a 0%, #2d1b69 40%, #44107a 100%);
            color: rgba(255,255,255,0.85);
            z-index: 1040;
            display: flex;
            flex-direction: column;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .admin-sidebar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(ellipse at 20% 0%, rgba(102,126,234,0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 100%, rgba(240,147,251,0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Collapsed state */
        .sidebar-collapsed .admin-sidebar { width: var(--sidebar-collapsed); }
        .sidebar-collapsed .brand-text,
        .sidebar-collapsed .nav-section-title,
        .sidebar-collapsed .nav-text,
        .sidebar-collapsed .nav-badge,
        .sidebar-collapsed .sidebar-footer .user-info { display: none !important; }
        .sidebar-collapsed .sidebar-brand { justify-content: center; padding: 0; }
        .sidebar-collapsed .brand-logo { margin: 0; }
        .sidebar-collapsed .sidebar-collapse-btn { position: static; margin: 0; }
        .sidebar-collapsed .sidebar-collapse-btn i { transform: rotate(180deg); }
        .sidebar-collapsed .nav-item { justify-content: center; padding: 0.65rem 0; margin: 2px 8px; }
        .sidebar-collapsed .nav-icon { margin: 0; }
        .sidebar-collapsed .sidebar-footer { justify-content: center; }
        .sidebar-collapsed .sidebar-user { justify-content: center; }
        .sidebar-collapsed .user-avatar { margin: 0; }
        .sidebar-collapsed .admin-header { left: var(--sidebar-collapsed); }
        .sidebar-collapsed .admin-main { margin-left: var(--sidebar-collapsed); }

        /* Sidebar tooltips on collapsed */
        .sidebar-collapsed .nav-item { position: relative; }
        .sidebar-collapsed .nav-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%; transform: translateY(-50%);
            background: #1a1d3a;
            color: #fff;
            padding: 0.35rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.78rem;
            font-weight: 600;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s;
            pointer-events: none;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        .sidebar-collapsed .nav-item:hover::after { opacity: 1; visibility: visible; }

        /* Brand */
        .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.15rem;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .brand-logo {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; color: #fff;
            flex-shrink: 0;
            margin-right: 0.75rem;
            box-shadow: 0 4px 15px rgba(102,126,234,0.4);
        }

        .brand-text h4 {
            color: #fff; font-size: 1.05rem; font-weight: 800;
            margin: 0; line-height: 1.2; letter-spacing: -0.02em;
        }
        .brand-text h4 span { color: var(--grad-end); }
        .brand-text p { color: rgba(255,255,255,0.4); font-size: 0.65rem; margin: 0; text-transform: uppercase; letter-spacing: 0.1em; }

        .sidebar-collapse-btn {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            border: none; color: rgba(255,255,255,0.6);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s;
            font-size: 1rem;
        }
        .sidebar-collapse-btn:hover { background: rgba(255,255,255,0.2); color: #fff; }

        /* Nav */
        .sidebar-nav {
            flex: 1; overflow-y: auto; overflow-x: hidden;
            padding: 0.5rem 0;
            position: relative; z-index: 1;
        }

        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 2px; }

        .nav-section { margin-bottom: 0.25rem; }

        .nav-section-title {
            font-size: 0.65rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.3);
            padding: 0.85rem 1.25rem 0.4rem;
        }

        .nav-item {
            display: flex; align-items: center;
            padding: 0.6rem 1.15rem;
            margin: 2px 10px;
            border-radius: var(--radius-md);
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.84rem; font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(102,126,234,0.4), rgba(240,147,251,0.25));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(102,126,234,0.25);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, var(--grad-start), var(--grad-end));
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 34px; height: 34px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            margin-right: 0.75rem;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .nav-item:hover .nav-icon { transform: scale(1.08); }
        .nav-item.active .nav-icon { background: rgba(255,255,255,0.15); }

        .nav-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        .nav-badge {
            margin-left: auto;
            font-size: 0.6rem; font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 1rem;
            color: #fff;
            min-width: 18px; text-align: center;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            flex-shrink: 0;
            position: relative; z-index: 1;
        }

        .sidebar-user {
            display: flex; align-items: center;
            padding: 0.4rem;
            border-radius: var(--radius-md);
        }

        .user-avatar {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem; font-weight: 700;
            flex-shrink: 0;
            margin-right: 0.65rem;
        }

        .user-name {
            color: rgba(255,255,255,0.9); font-size: 0.8rem; font-weight: 600;
            line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            max-width: 140px;
        }
        .user-role {
            color: rgba(255,255,255,0.4); font-size: 0.65rem;
            text-transform: capitalize;
        }

        /* ═══════════════════════════════════════════════════════
           HEADER
           ═══════════════════════════════════════════════════════ */
        .admin-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: var(--bg-header);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1030;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Header Actions */
        .header-actions { display: flex; align-items: center; gap: 0.5rem; }

        .header-btn {
            width: 40px; height: 40px;
            border-radius: var(--radius-md);
            border: none;
            background: transparent;
            color: var(--text-secondary);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .header-btn:hover {
            background: var(--bg-body);
            color: var(--accent-primary);
        }

        .header-btn .notif-badge {
            position: absolute; top: 4px; right: 2px;
            min-width: 18px; height: 18px;
            padding: 0 5px;
            background: var(--accent-danger);
            border-radius: 9px;
            border: 2px solid #fff;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            line-height: 1;
        }

        .header-divider {
            width: 1px; height: 28px;
            background: var(--border-color);
            margin: 0 0.35rem;
        }

        /* Profile Button */
        .header-profile {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.35rem 0.75rem 0.35rem 0.35rem;
            border-radius: 2rem;
            border: 2px solid var(--border-light);
            background: transparent;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            color: var(--text-primary);
        }
        .header-profile:hover { border-color: var(--accent-primary); background: rgba(102,126,234,0.04); }

        .header-profile .profile-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--grad-start), var(--grad-end));
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.78rem; font-weight: 700;
        }
        .header-profile .profile-name {
            font-size: 0.82rem; font-weight: 600;
            max-width: 120px; overflow: hidden;
            text-overflow: ellipsis; white-space: nowrap;
        }
        .header-profile .profile-arrow {
            color: var(--text-muted); font-size: 1.1rem;
            transition: transform 0.2s;
        }
        .header-profile[aria-expanded="true"] .profile-arrow { transform: rotate(180deg); }

        /* Profile Dropdown */
        .profile-dropdown {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 0.5rem;
            min-width: 220px;
            margin-top: 0.5rem !important;
        }
        .profile-dropdown .dropdown-item {
            border-radius: var(--radius-sm);
            padding: 0.6rem 0.85rem;
            font-size: 0.84rem;
            font-weight: 500;
            display: flex; align-items: center; gap: 0.6rem;
            transition: all 0.15s;
        }
        .profile-dropdown .dropdown-item:hover { background: var(--bg-body); }
        .profile-dropdown .dropdown-item i { font-size: 1.1rem; width: 1.2rem; text-align: center; }
        .profile-dropdown .dropdown-divider { margin: 0.35rem 0; border-color: var(--border-light); }

        /* Notification Dropdown */
        .notif-dropdown {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            padding: 0;
            width: 340px;
            margin-top: 0.5rem !important;
        }
        .notif-dropdown .notif-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-light);
            display: flex; justify-content: space-between; align-items: center;
        }
        .notif-dropdown .notif-header h6 { font-size: 0.9rem; font-weight: 700; margin: 0; }
        .notif-dropdown .notif-body { max-height: 320px; overflow-y: auto; padding: 0.5rem; }
        .notif-dropdown .notif-item {
            display: flex; gap: 0.75rem; padding: 0.65rem;
            border-radius: var(--radius-sm); transition: background 0.15s;
        }
        .notif-dropdown .notif-item:hover { background: var(--bg-body); }
        .notif-dropdown .notif-item .notif-icon {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .notif-dropdown .notif-item .notif-text {
            font-size: 0.8rem; color: var(--text-primary); line-height: 1.4;
        }
        .notif-dropdown .notif-item .notif-time {
            font-size: 0.68rem; color: var(--text-muted); margin-top: 2px;
        }
        .notif-dropdown .notif-footer {
            padding: 0.6rem 1rem;
            border-top: 1px solid var(--border-light);
            text-align: center;
        }
        .notif-dropdown .notif-footer a {
            font-size: 0.8rem; font-weight: 600; color: var(--accent-primary);
            text-decoration: none;
        }
        .notif-dropdown .notif-item.notif-read {
            opacity: 0.5;
        }
        .notif-dropdown .notif-item.notif-read .notif-time { color: #22c55e; }
        .notif-mark-read {
            background: none; border: none; cursor: pointer;
            font-size: 0.7rem; font-weight: 600;
            color: var(--accent-primary);
            padding: 0;
        }
        .notif-mark-read:hover { text-decoration: underline; }

        /* ═══════════════════════════════════════════════════════
           MAIN CONTENT
           ═══════════════════════════════════════════════════════ */
        .admin-main {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--header-height) + 1.5rem);
            padding-bottom: 2rem;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-main .content-wrapper { padding: 0 1.5rem; max-width: 1600px; }

        /* Page Heading */
        .page-heading {
            margin-bottom: 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-heading h1 {
            font-size: 1.5rem; font-weight: 800;
            color: var(--text-primary);
            margin: 0; letter-spacing: -0.02em;
        }
        .page-heading p {
            color: var(--text-secondary);
            font-size: 0.85rem; margin: 0.15rem 0 0;
        }

        /* ═══════════════════════════════════════════════════════
           CARDS & COMPONENTS
           ═══════════════════════════════════════════════════════ */
        .card {
            border: none;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            background: var(--bg-card);
            transition: all 0.25s;
            overflow: visible;
        }
        .card:hover { box-shadow: var(--shadow-md); }
        .card .table-responsive { overflow: visible; }
        .card .card-body:has(.dataTables_wrapper) { overflow: visible; }
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-light);
            font-weight: 700;
            padding: 1rem 1.25rem;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102,126,234,0.3);
            transition: all 0.25s;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5b72d8, #7c3aed);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(102,126,234,0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #00c853, #1de9b6);
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,200,83,0.3);
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #00b348, #17d6a5);
            transform: translateY(-1px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff5252, #ff1744);
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffab00, #ff6d00);
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            color: #fff;
        }
        .btn-warning:hover { color: #fff; }

        .btn-outline-primary {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
            border-radius: var(--radius-sm);
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6);
            border-color: transparent;
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid var(--border-light);
            border-radius: var(--radius-sm);
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
        }

        .form-label {
            font-size: 0.82rem; font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.35rem;
        }

        .input-group-text {
            border: 2px solid var(--border-light);
            background: var(--bg-body);
            border-radius: var(--radius-sm);
        }

        /* ═══════════════════════════════════════════════════════
           CLEAN DATATABLES — Notion / Linear / Stripe inspired
           ═══════════════════════════════════════════════════════ */

        .dataTables_wrapper { padding: 0; }

        /* ── Top toolbar ── */
        .dataTables_wrapper .row:first-child {
            padding: 1.15rem 1.5rem;
            align-items: center;
            margin: 0;
            background: transparent;
            border: none;
        }

        /* Entries selector — quiet, minimal */
        .dataTables_wrapper .dataTables_length { font-size: 0.8rem; color: var(--text-muted); }
        .dataTables_wrapper .dataTables_length label {
            display: flex; align-items: center; gap: 0.5rem; margin: 0; font-weight: 500;
        }
        .dataTables_wrapper .dataTables_length select {
            min-width: 56px; border: 1.5px solid var(--border-light); border-radius: 8px;
            padding: 0.4rem 0.55rem; font-size: 0.8rem; background: #fff;
            transition: border-color 0.2s; cursor: pointer; color: var(--text-primary);
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: var(--accent-primary); box-shadow: 0 0 0 3px rgba(102,126,234,0.08); outline: none;
        }

        /* Search — clean pill */
        .dataTables_wrapper .dataTables_filter { font-size: 0.8rem; }
        .dataTables_wrapper .dataTables_filter label {
            display: flex; align-items: center; gap: 0.45rem; margin: 0; font-weight: 500; color: var(--text-muted);
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border-light); border-radius: 10px;
            padding: 0.5rem 1rem; font-size: 0.8rem; min-width: 240px;
            background: #fff; transition: all 0.2s; color: var(--text-primary);
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--accent-primary); background: #fff;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.08); outline: none; min-width: 280px;
        }
        .dataTables_wrapper .dataTables_filter input::placeholder { color: #c4c9d4; }

        /* ── Table ── */
        table.dataTable { border-collapse: collapse !important; margin: 0 !important; width: 100% !important; }

        /* Header — clean, no background noise */
        table.dataTable > thead > tr > th {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #8b92a5;
            background: transparent !important;
            border-top: none !important;
            border-bottom: 1.5px solid #e8ebf0 !important;
            padding: 0.65rem 1rem 0.75rem !important;
            white-space: nowrap;
        }
        table.dataTable > thead > tr > th:first-child { padding-left: 1.5rem !important; }
        table.dataTable > thead > tr > th:last-child { padding-right: 1.5rem !important; }

        /* Sort — barely visible unless active */
        table.dataTable > thead .sorting::after,
        table.dataTable > thead .sorting_asc::after,
        table.dataTable > thead .sorting_desc::after { opacity: 0.2 !important; font-size: 0.55rem !important; }
        table.dataTable > thead .sorting_asc::after,
        table.dataTable > thead .sorting_desc::after { opacity: 0.7 !important; color: var(--accent-primary) !important; }

        /* Body rows — generous spacing, big readable text */
        table.dataTable > tbody > tr { transition: background 0.12s ease; }
        table.dataTable > tbody > tr > td {
            padding: 0.9rem 1rem !important;
            font-size: 0.88rem;
            font-weight: 450;
            color: var(--text-primary);
            border-bottom: 1px solid #f1f3f6 !important;
            vertical-align: middle;
        }
        table.dataTable > tbody > tr > td:first-child {
            padding-left: 1.5rem !important;
            font-weight: 550;
            color: #1a1d2e;
        }
        table.dataTable > tbody > tr > td:last-child { padding-right: 1.5rem !important; }

        /* Hover — single soft tint */
        table.dataTable > tbody > tr:hover { background: #f8f9fc !important; }

        /* No distracting stripes */
        table.dataTable.table-striped > tbody > tr:nth-child(odd) > td,
        table.dataTable.table-striped > tbody > tr:nth-child(even) > td { background: transparent; }
        table.dataTable > tbody > tr.selected > td { background: rgba(102,126,234,0.06) !important; }

        /* Action column alignment */
        table.dataTable > tbody > tr > td:last-child { text-align: center; }

        /* ── Bottom bar ── */
        .dataTables_wrapper .row:last-child {
            padding: 1rem 1.5rem 1.15rem; align-items: center;
            border-top: 1.5px solid #f1f3f6; margin: 0;
        }

        .dataTables_wrapper .dataTables_info {
            font-size: 0.78rem; color: #a0a7b8; font-weight: 500; padding: 0 !important;
        }

        /* Pagination — pill buttons */
        .dataTables_wrapper .dataTables_paginate { padding: 0 !important; }
        .dataTables_wrapper .dataTables_paginate .pagination { margin: 0; gap: 3px; }

        .dataTables_wrapper .dataTables_paginate .page-item .page-link {
            border: none; border-radius: 8px !important;
            padding: 0.38rem 0.7rem; font-size: 0.78rem; font-weight: 600;
            color: #8b92a5; background: transparent; transition: all 0.15s;
            min-width: 34px; text-align: center; line-height: 1.45;
        }
        .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
            background: #f4f5f8; color: var(--text-primary);
        }
        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background: var(--accent-primary); color: #fff;
            box-shadow: 0 1px 4px rgba(102,126,234,0.3);
        }
        .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            color: #d0d4de; background: transparent;
        }
        .dataTables_wrapper .dataTables_paginate .page-item:first-child .page-link,
        .dataTables_wrapper .dataTables_paginate .page-item:last-child .page-link { font-size: 0.95rem; }

        /* Processing overlay */
        .dataTables_wrapper .dataTables_processing {
            background: rgba(255,255,255,0.92) !important; backdrop-filter: blur(6px);
            border: none !important; border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06); padding: 1rem 2rem !important;
            font-size: 0.84rem; font-weight: 600; color: var(--accent-primary); z-index: 10;
        }

        /* Empty state */
        table.dataTable tbody td.dataTables_empty {
            text-align: center; padding: 4rem 1rem !important;
            color: #b5bac8; font-size: 0.88rem; font-weight: 500;
        }

        /* Responsive collapse */
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before {
            background: var(--accent-primary) !important; border: none !important;
            box-shadow: 0 1px 4px rgba(102,126,234,0.25); border-radius: 6px;
        }

        /* Badges in tables — soft pill */
        table.dataTable .badge {
            font-size: 0.68rem; font-weight: 650; padding: 0.28rem 0.6rem;
            border-radius: 6px; letter-spacing: 0.01em;
        }

        /* Mobile stack */
        @media (max-width: 767.98px) {
            .dataTables_wrapper .row:first-child,
            .dataTables_wrapper .row:last-child { flex-direction: column; gap: 0.75rem; text-align: center; }
            .dataTables_wrapper .dataTables_filter input { min-width: 100%; }
            .dataTables_wrapper .dataTables_filter input:focus { min-width: 100%; }
            .dataTables_wrapper .dataTables_paginate .pagination { justify-content: center; }
            table.dataTable > tbody > tr > td { font-size: 0.82rem; padding: 0.75rem 0.8rem !important; }
        }

        /* Badges */
        .badge {
            font-weight: 600;
            border-radius: var(--radius-sm);
            padding: 0.35rem 0.6rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--radius-md);
            font-weight: 500;
        }

        /* Tables */
        .table > thead > tr > th {
            border-bottom: 2px solid var(--border-light);
        }
        .table > tbody > tr > td {
            border-bottom: 1px solid var(--border-light);
        }
        .table > tbody > tr:hover { background: rgba(102,126,234,0.03); }

        /* ── Action Dropdown (DataTable rows) ── */
        .action-dropdown { text-align: center; }
        .btn-action-toggle {
            width: 32px; height: 32px; padding: 0;
            display: inline-flex; align-items: center; justify-content: center;
            border: 1.5px solid transparent; border-radius: 8px;
            background: transparent; color: #8b92a5;
            font-size: 1.15rem; transition: all 0.15s; cursor: pointer;
        }
        .btn-action-toggle:hover, .btn-action-toggle:focus,
        .btn-action-toggle[aria-expanded="true"] {
            background: #f4f5f8; color: var(--text-primary);
            border-color: var(--border-light);
        }
        .action-dropdown .dropdown-menu {
            min-width: 160px; padding: 6px;
            border: 1.5px solid #e8ebf0; border-radius: 10px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08), 0 2px 8px rgba(0,0,0,0.04);
            animation: dropIn 0.12s ease;
            z-index: 1050;
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .action-dropdown .dropdown-item {
            font-size: 0.82rem; font-weight: 500; padding: 0.45rem 0.75rem;
            border-radius: 6px; color: var(--text-primary);
            display: flex; align-items: center; transition: background 0.1s;
        }
        .action-dropdown .dropdown-item:hover {
            background: #f4f5f8;
        }
        .action-dropdown .dropdown-item.text-danger:hover {
            background: rgba(239,68,68,0.06);
        }
        .action-dropdown .dropdown-item i { font-size: 1rem; opacity: 0.65; }
        .action-dropdown .dropdown-divider { margin: 4px 0; border-color: #f1f3f6; }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 1035;
        }

        /* ═══════════════════════════════════════════════════════
           MOBILE RESPONSIVE
           ═══════════════════════════════════════════════════════ */
        @media (max-width: 991.98px) {
            .admin-sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-header { left: 0 !important; }
            .admin-main { margin-left: 0 !important; }
            .sidebar-collapse-btn { display: none !important; }
            .header-profile .profile-name { display: none; }
        }

        @media (max-width: 575.98px) {
            .admin-main .content-wrapper { padding: 0 1rem; }
        }

        /* ═══════════════════════════════════════════════════════
           ANIMATIONS
           ═══════════════════════════════════════════════════════ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .content-wrapper > * { animation: fadeInUp 0.3s ease forwards; }
    </style>
    {{ $styles ?? '' }}
</head>
<body>
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Header -->
    <header class="admin-header" id="adminHeader">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile Toggle -->
            <button class="btn btn-link text-dark d-lg-none p-0" id="mobileSidebarToggle">
                <i class='bx bx-menu fs-4'></i>
            </button>

        </div>

        <div class="header-actions">
            <!-- Notifications -->
            <div class="dropdown" id="notifDropdown"
                 data-bookings="{{ $pendingBookings ?? 0 }}"
                 data-reviews="{{ $pendingReviews ?? 0 }}">
                <button class="header-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <i class='bx bx-bell'></i>
                    <span class="notif-badge" id="notifBadge" style="display:none;"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end notif-dropdown">
                    <div class="notif-header">
                        <h6>Notifications</h6>
                        <button class="notif-mark-read" id="markAllRead" style="display:none;">
                            <i class='bx bx-check-double me-1'></i>Mark all read
                        </button>
                    </div>
                    <div class="notif-body" id="notifBody">
                        @if(($pendingBookings ?? 0) > 0)
                        <a href="{{ route('admin.bookings.index') }}" class="notif-item text-decoration-none" data-notif-key="bookings" data-notif-count="{{ $pendingBookings }}">
                            <div class="notif-icon" style="background:rgba(255,171,0,0.1);color:#ffab00;">
                                <i class='bx bx-calendar-check'></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notif-text"><strong>{{ $pendingBookings }}</strong> pending bookings need review</div>
                                <div class="notif-time">Requires attention</div>
                            </div>
                        </a>
                        @endif
                        @if(($pendingReviews ?? 0) > 0)
                        <a href="{{ route('admin.reviews.index') }}" class="notif-item text-decoration-none" data-notif-key="reviews" data-notif-count="{{ $pendingReviews }}">
                            <div class="notif-icon" style="background:rgba(0,176,255,0.1);color:#00b0ff;">
                                <i class='bx bx-message-dots'></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="notif-text"><strong>{{ $pendingReviews }}</strong> reviews awaiting approval</div>
                                <div class="notif-time">Requires attention</div>
                            </div>
                        </a>
                        @endif
                        <div class="text-center py-4 text-muted" id="notifEmpty" style="display:none;">
                            <i class='bx bx-check-circle fs-2 d-block mb-1 opacity-50'></i>
                            <span style="font-size:0.82rem;">All caught up!</span>
                        </div>
                    </div>
                    <div class="notif-footer">
                        <a href="{{ route('admin.bookings.index') }}">View all activity</a>
                    </div>
                </div>
            </div>

            <div class="header-divider"></div>

            <!-- Profile -->
            <div class="dropdown">
                <button class="header-profile dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="border:2px solid var(--border-light);">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <span class="profile-name d-none d-md-inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <i class='bx bx-chevron-down profile-arrow'></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item" style="color:var(--accent-danger);">
                                <i class='bx bx-log-out'></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main" id="adminMain">
        <div class="content-wrapper">
            {{-- Page Heading --}}
            @if($pageTitle)
            <div class="page-heading">
                <div>
                    <h1>{{ $pageTitle }}</h1>
                    @if($pageDescription)
                    <p>{{ $pageDescription }}</p>
                    @endif
                </div>
                <div>{{ $actions ?? '' }}</div>
            </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.body;
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const mobileToggle = document.getElementById('mobileSidebarToggle');

            // ── Collapsible Sidebar ─────────────────────────
            const COLLAPSED_KEY = 'sidebar_collapsed';
            if (localStorage.getItem(COLLAPSED_KEY) === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            if (collapseBtn) {
                collapseBtn.addEventListener('click', function() {
                    body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(COLLAPSED_KEY, body.classList.contains('sidebar-collapsed'));
                });
            }

            // ── Mobile Sidebar Toggle ───────────────────────
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
                });
            }
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                });
            }

            // ── Select2 Init ────────────────────────────────
            if (typeof $ !== 'undefined') {
                $('.select2').select2({ theme: 'bootstrap-5' });
            }
        });

        // ── SweetAlert2 — Flash Messages ────────────────────
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: @json(session('success')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'rounded-3' }
        });
        @endif

        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: @json(session('error')),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
        });
        @endif

        @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align:left;margin:0;padding-left:1.2em;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        });
        @endif

        // ── SweetAlert2 — Delete Confirmation ───────────────
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-confirm-delete]');
            if (!btn) return;
            e.preventDefault();
            const form = btn.closest('form');
            const message = btn.getAttribute('data-confirm-delete') || 'Are you sure you want to delete this?';
            Swal.fire({
                title: 'Delete Confirmation',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ff5252',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="bx bx-trash me-1"></i> Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: { popup: 'rounded-4', confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
            }).then((result) => {
                if (result.isConfirmed && form) form.submit();
            });
        });

        // ── SweetAlert2 — Generic Confirmation ──────────────
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-confirm]');
            if (!btn) return;
            e.preventDefault();
            const form = btn.closest('form');
            const message = btn.getAttribute('data-confirm') || 'Are you sure?';
            const title = btn.getAttribute('data-confirm-title') || 'Confirm Action';
            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: { popup: 'rounded-4', confirmButton: 'rounded-3', cancelButton: 'rounded-3' }
            }).then((result) => {
                if (result.isConfirmed && form) form.submit();
            });
        });
    </script>
    @stack('select2-ajax-init')
    {{ $scripts ?? '' }}

    <script>
    (function() {
        var wrap = document.getElementById('notifDropdown');
        if (!wrap) return;

        var serverBookings = parseInt(wrap.dataset.bookings) || 0;
        var serverReviews  = parseInt(wrap.dataset.reviews) || 0;
        var badge    = document.getElementById('notifBadge');
        var markAll  = document.getElementById('markAllRead');
        var emptyMsg = document.getElementById('notifEmpty');
        var items    = wrap.querySelectorAll('.notif-item[data-notif-key]');
        var STORAGE_KEY = 'admin_notif_read';

        function getRead() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; }
            catch(e) { return {}; }
        }

        function saveRead(data) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        }

        function render() {
            var read = getRead();
            var unread = 0;

            items.forEach(function(item) {
                var key   = item.dataset.notifKey;
                var count = parseInt(item.dataset.notifCount) || 0;
                var readCount = parseInt(read[key]) || 0;

                // If server count changed (new items arrived), reset read state for this key
                if (count > readCount) {
                    item.classList.remove('notif-read');
                    item.querySelector('.notif-time').textContent = 'Requires attention';
                    unread += count;
                } else {
                    item.classList.add('notif-read');
                    item.querySelector('.notif-time').textContent = 'Read';
                }
            });

            // Badge
            if (unread > 0) {
                badge.textContent = unread > 99 ? '99+' : unread;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }

            // Mark all read button
            markAll.style.display = unread > 0 ? 'inline-flex' : 'none';

            // Empty state
            var totalServer = serverBookings + serverReviews;
            if (totalServer === 0) {
                emptyMsg.style.display = 'block';
            } else {
                emptyMsg.style.display = 'none';
            }
        }

        // Click on individual notification → mark read, then navigate
        items.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var key   = item.dataset.notifKey;
                var count = parseInt(item.dataset.notifCount) || 0;
                var read  = getRead();
                read[key] = count;
                saveRead(read);

                item.classList.add('notif-read');
                item.querySelector('.notif-time').textContent = 'Read';
                render();

                // Navigate after brief visual feedback
                setTimeout(function() {
                    window.location.href = item.getAttribute('href');
                }, 150);
            });
        });

        // Mark all as read
        markAll.addEventListener('click', function() {
            var read = getRead();
            items.forEach(function(item) {
                var key   = item.dataset.notifKey;
                var count = parseInt(item.dataset.notifCount) || 0;
                read[key] = count;
                item.classList.add('notif-read');
                item.querySelector('.notif-time').textContent = 'Read';
            });
            saveRead(read);
            render();
        });

        render();
    })();
    </script>
</body>
</html>
