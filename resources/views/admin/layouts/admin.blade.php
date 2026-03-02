<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AI Photo Enhancer — Admin Panel">
    <title>@yield('title', 'Dashboard') — Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.6.0/fonts/remixicon.css" rel="stylesheet">

    <style>
        /* ─────────────────────────────────────────────
           DESIGN TOKENS
        ───────────────────────────────────────────── */
        :root {
            --white: #ffffff;
            --gray-25: #fafafa;
            --gray-50: #f7f8fa;
            --gray-100: #f0f1f5;
            --gray-200: #e4e6ed;
            --gray-300: #d0d4df;
            --gray-400: #9ba3b8;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;

            --indigo-50: #eef2ff;
            --indigo-100: #e0e7ff;
            --indigo-500: #6366f1;
            --indigo-600: #4f46e5;
            --indigo-700: #4338ca;

            --violet-50: #f5f3ff;
            --violet-500: #8b5cf6;

            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-500: #10b981;
            --emerald-600: #059669;

            --amber-50: #fffbeb;
            --amber-100: #fef3c7;
            --amber-500: #f59e0b;
            --amber-600: #d97706;

            --rose-50: #fff1f2;
            --rose-100: #ffe4e6;
            --rose-500: #f43f5e;
            --rose-600: #e11d48;

            --sky-50: #f0f9ff;
            --sky-500: #0ea5e9;

            --sidebar-w: 248px;
            --topbar-h: 58px;
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 14px;
            --radius-xl: 20px;

            --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-sm: 0 1px 4px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.08), 0 4px 8px rgba(0, 0, 0, 0.04);
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-size: 14px;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.55;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 99px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* ─────────────────────────────────────────────
           SIDEBAR
        ───────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 18px 18px 14px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .brand-mark {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--indigo-600) 0%, var(--violet-500) 100%);
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .brand-text strong {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.2px;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--gray-400);
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 10px 10px;
        }

        .nav-section {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            color: var(--gray-400);
            padding: 14px 10px 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            margin-bottom: 1px;
            border-radius: var(--radius-sm);
            color: var(--gray-500);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .nav-link i {
            font-size: 17px;
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background: var(--gray-50);
            color: var(--gray-800);
        }

        .nav-link.active {
            background: var(--indigo-50);
            color: var(--indigo-600);
            font-weight: 600;
        }

        .nav-link.active i {
            color: var(--indigo-500);
        }

        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--gray-100);
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--indigo-500), var(--violet-500));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-info strong {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--gray-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info span {
            font-size: 11px;
            color: var(--gray-400);
        }

        .logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--gray-400);
            font-size: 17px;
            padding: 4px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            transition: all 0.15s;
            flex-shrink: 0;
        }

        .logout-btn:hover {
            color: var(--rose-500);
            background: var(--rose-50);
        }

        /* ─────────────────────────────────────────────
           TOPBAR
        ───────────────────────────────────────────── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 10px;
            z-index: 99;
            overflow: hidden;
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.2px;
        }

        .topbar-sep {
            color: var(--gray-300);
        }

        .topbar-crumb {
            font-size: 13px;
            color: var(--gray-400);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .topbar-crumb a {
            color: var(--gray-400);
            text-decoration: none;
        }

        .topbar-crumb a:hover {
            color: var(--indigo-600);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .topbar-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 99px;
            font-size: 12px;
            color: var(--gray-500);
            font-weight: 500;
        }

        .online-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--emerald-500);
            flex-shrink: 0;
        }

        /* ─────────────────────────────────────────────
           MAIN
        ───────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            flex: 1;
            min-width: 0;
            width: calc(100% - var(--sidebar-w));
            overflow-x: hidden;
        }

        .page-body {
            padding: 24px;
            width: 100%;
            box-sizing: border-box;
        }

        /* ─────────────────────────────────────────────
           PAGE HEADER
        ───────────────────────────────────────────── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
            gap: 16px;
            flex-wrap: wrap;
        }

        .page-header h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.4px;
        }

        .page-header p {
            font-size: 13px;
            color: var(--gray-500);
            margin-top: 3px;
        }

        /* ─────────────────────────────────────────────
           CARDS
        ───────────────────────────────────────────── */
        .card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--gray-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .card-title i {
            font-size: 16px;
            color: var(--indigo-500);
        }

        .card-body {
            padding: 20px;
        }

        /* ─────────────────────────────────────────────
           STAT CARDS
        ───────────────────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(185px, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-1px);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-icon.indigo {
            background: var(--indigo-50);
            color: var(--indigo-600);
        }

        .stat-icon.emerald {
            background: var(--emerald-50);
            color: var(--emerald-600);
        }

        .stat-icon.amber {
            background: var(--amber-50);
            color: var(--amber-600);
        }

        .stat-icon.rose {
            background: var(--rose-50);
            color: var(--rose-600);
        }

        .stat-icon.sky {
            background: var(--sky-50);
            color: var(--sky-500);
        }

        .stat-icon.violet {
            background: var(--violet-50);
            color: var(--violet-500);
        }

        .stat-content {
            flex: 1;
            min-width: 0;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-900);
            letter-spacing: -0.5px;
            line-height: 1;
            margin-bottom: 3px;
        }

        .stat-label {
            font-size: 12px;
            color: var(--gray-400);
            font-weight: 500;
        }

        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
            padding: 2px 6px;
            border-radius: 99px;
        }

        .trend-up {
            background: var(--emerald-50);
            color: var(--emerald-600);
        }

        .trend-down {
            background: var(--rose-50);
            color: var(--rose-600);
        }

        /* ─────────────────────────────────────────────
           TABLES
        ───────────────────────────────────────────── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 10px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--gray-400);
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-25);
            white-space: nowrap;
        }

        tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-600);
            vertical-align: middle;
        }

        tbody tr:hover td {
            background: var(--gray-25);
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* ─────────────────────────────────────────────
           BADGES
        ───────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .badge-indigo {
            background: var(--indigo-50);
            color: var(--indigo-600);
        }

        .badge-emerald {
            background: var(--emerald-50);
            color: var(--emerald-600);
        }

        .badge-amber {
            background: var(--amber-50);
            color: var(--amber-600);
        }

        .badge-rose {
            background: var(--rose-50);
            color: var(--rose-600);
        }

        .badge-sky {
            background: var(--sky-50);
            color: var(--sky-500);
        }

        .badge-gray {
            background: var(--gray-100);
            color: var(--gray-500);
        }

        .badge-violet {
            background: var(--violet-50);
            color: var(--violet-500);
        }

        /* ─────────────────────────────────────────────
           BUTTONS
        ───────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--indigo-600);
            color: white;
            border-color: var(--indigo-600);
            box-shadow: 0 1px 3px rgba(79, 70, 229, 0.25);
        }

        .btn-primary:hover {
            background: var(--indigo-700);
            border-color: var(--indigo-700);
        }

        .btn-white {
            background: var(--white);
            color: var(--gray-700);
            border-color: var(--gray-300);
            box-shadow: var(--shadow-xs);
        }

        .btn-white:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        .btn-danger {
            background: var(--rose-50);
            color: var(--rose-600);
            border-color: var(--rose-100);
        }

        .btn-success {
            background: var(--emerald-50);
            color: var(--emerald-600);
            border-color: var(--emerald-100);
        }

        .btn-warning {
            background: var(--amber-50);
            color: var(--amber-600);
            border-color: var(--amber-100);
        }

        .btn:hover:not(.btn-primary) {
            filter: brightness(0.96);
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-xs {
            padding: 3px 7px;
            font-size: 11.5px;
        }

        /* ─────────────────────────────────────────────
           FORMS
        ───────────────────────────────────────────── */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 8px 12px;
            background: var(--white);
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            color: var(--gray-800);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            outline: none;
            transition: all 0.15s;
            box-shadow: var(--shadow-xs);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--indigo-500);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
        }

        .form-control::placeholder {
            color: var(--gray-400);
        }

        .form-hint {
            font-size: 11.5px;
            color: var(--gray-400);
            margin-top: 4px;
        }

        /* Toggle */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .toggle-row:last-child {
            border-bottom: none;
        }

        .toggle-info strong {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .toggle-info span {
            font-size: 12px;
            color: var(--gray-400);
        }

        .toggle {
            position: relative;
            width: 40px;
            height: 22px;
            flex-shrink: 0;
        }

        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            inset: 0;
            background: var(--gray-200);
            border-radius: 99px;
            cursor: pointer;
            transition: 0.25s;
        }

        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            left: 3px;
            top: 3px;
            background: white;
            border-radius: 50%;
            transition: 0.25s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .toggle input:checked+.toggle-slider {
            background: var(--indigo-500);
        }

        .toggle input:checked+.toggle-slider::before {
            transform: translateX(18px);
        }

        /* ─────────────────────────────────────────────
           ALERTS
        ───────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            margin-bottom: 18px;
            border: 1px solid transparent;
        }

        .alert i {
            font-size: 17px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-success {
            background: var(--emerald-50);
            border-color: var(--emerald-100);
            color: var(--emerald-600);
        }

        .alert-danger {
            background: var(--rose-50);
            border-color: var(--rose-100);
            color: var(--rose-600);
        }

        .alert-warning {
            background: var(--amber-50);
            border-color: var(--amber-100);
            color: var(--amber-600);
        }

        .alert-info {
            background: var(--indigo-50);
            border-color: var(--indigo-100);
            color: var(--indigo-600);
        }

        /* ─────────────────────────────────────────────
           TABS (for settings nav)
        ───────────────────────────────────────────── */
        .settings-tabs {
            display: flex;
            gap: 2px;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 22px;
            background: var(--white);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            padding: 12px 16px 0;
            border: 1px solid var(--gray-200);
            border-bottom: none;
            box-shadow: var(--shadow-sm);
        }

        .settings-tab {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            border-radius: var(--radius-sm) var(--radius-sm) 0 0;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--gray-500);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.15s;
        }

        .settings-tab:hover {
            color: var(--gray-800);
            background: var(--gray-50);
        }

        .settings-tab.active {
            color: var(--indigo-600);
            border-bottom-color: var(--indigo-500);
            background: transparent;
        }

        .settings-tab i {
            font-size: 16px;
        }

        /* ─────────────────────────────────────────────
           PAGINATION
        ───────────────────────────────────────────── */
        .pagination {
            display: flex;
            align-items: center;
            gap: 3px;
            padding: 13px 20px;
            border-top: 1px solid var(--gray-100);
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-500);
            text-decoration: none;
            background: var(--white);
            border: 1px solid var(--gray-200);
            transition: all 0.15s;
        }

        .page-link:hover {
            background: var(--gray-50);
            color: var(--gray-800);
        }

        .page-link.active {
            background: var(--indigo-600);
            border-color: var(--indigo-600);
            color: white;
        }

        /* ─────────────────────────────────────────────
           EMPTY STATE
        ───────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--gray-400);
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 10px;
            display: block;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-500);
            margin-bottom: 4px;
        }

        .empty-state p {
            font-size: 13px;
        }

        /* ─────────────────────────────────────────────
           FILTER BAR
        ───────────────────────────────────────────── */
        .filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-field .form-control,
        .filter-field .form-select {
            width: auto;
            min-width: 150px;
        }

        /* ─────────────────────────────────────────────
           GRIDS
        ───────────────────────────────────────────── */
        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-1 {
            gap: 5px;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-3 {
            gap: 12px;
        }

        .gap-4 {
            gap: 16px;
        }

        .mb-1 {
            margin-bottom: 6px;
        }

        .mb-2 {
            margin-bottom: 12px;
        }

        .mb-3 {
            margin-bottom: 18px;
        }

        .mb-4 {
            margin-bottom: 24px;
        }

        .mt-1 {
            margin-top: 6px;
        }

        .mt-2 {
            margin-top: 12px;
        }

        .mt-3 {
            margin-top: 18px;
        }

        .text-sm {
            font-size: 12.5px;
        }

        .text-xs {
            font-size: 11.5px;
        }

        .text-muted {
            color: var(--gray-400);
        }

        .text-dark {
            color: var(--gray-900);
        }

        .font-semibold {
            font-weight: 600;
        }

        .font-bold {
            font-weight: 700;
        }

        .truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .w-full {
            width: 100%;
        }

        /* divider */
        .divider {
            border: none;
            border-top: 1px solid var(--gray-100);
            margin: 16px 0;
        }

        /* Section card (for settings) */
        .section-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            margin-bottom: 16px;
            overflow: hidden;
        }

        .section-card-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-25);
        }

        .section-card-header h3 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-card-header p {
            font-size: 12px;
            color: var(--gray-400);
            margin-top: 2px;
        }

        .section-card-body {
            padding: 20px;
        }

        @media (max-width: 1200px) {
            .grid-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 1024px) {
            .grid-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .topbar-pill:last-child {
                display: none;
            }
        }

        @media (max-width: 900px) {
            .grid-2 {
                grid-template-columns: minmax(0, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-wrapper {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                left: 0;
            }

            .grid-2,
            .grid-3,
            .grid-4 {
                grid-template-columns: minmax(0, 1fr);
            }

            .page-body {
                padding: 16px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">✨</div>
            <div class="brand-text">
                <strong>AI Photo Enhancer</strong>
                <span>Admin Panel</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Overview</div>
            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ri-home-5-line"></i> Dashboard
            </a>

            <div class="nav-section">Manage</div>

            <a href="{{ route('admin.photos.index') }}"
                class="nav-link {{ request()->routeIs('admin.photos.*') ? 'active' : '' }}">
                <i class="ri-image-2-line"></i> Photos
            </a>
            <a href="{{ route('admin.api-logs.index') }}"
                class="nav-link {{ request()->routeIs('admin.api-logs.*') ? 'active' : '' }}">
                <i class="ri-terminal-box-line"></i> API Logs
            </a>
            <a href="{{ route('admin.features.index') }}"
                class="nav-link {{ request()->routeIs('admin.features.*') ? 'active' : '' }}">
                <i class="ri-magic-line"></i> App Features
            </a>
            <a href="{{ route('admin.ai-test.index') }}"
                class="nav-link {{ request()->routeIs('admin.ai-test.*') ? 'active' : '' }}"
                style="{{ request()->routeIs('admin.ai-test.*') ? '' : '' }}">
                <i class="ri-flask-line"></i> AI Test Lab
            </a>

            <div class="nav-section">Settings</div>
            <a href="{{ route('admin.settings.general') }}"
                class="nav-link {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                <i class="ri-settings-3-line"></i> General
            </a>
            <a href="{{ route('admin.settings.billing') }}"
                class="nav-link {{ request()->routeIs('admin.settings.billing') ? 'active' : '' }}">
                <i class="ri-money-dollar-circle-line"></i> Billing
            </a>
            <a href="{{ route('admin.settings.ai') }}"
                class="nav-link {{ request()->routeIs('admin.settings.ai') ? 'active' : '' }}">
                <i class="ri-robot-2-line"></i> AI Provider
            </a>
            <a href="{{ route('admin.settings.ads') }}"
                class="nav-link {{ request()->routeIs('admin.settings.ads*') ? 'active' : '' }}">
                <i class="ri-advertisement-line"></i> AdMob
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-row">
                <div class="user-avatar">{{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}</div>
                <div class="user-info">
                    <strong>{{ session('admin_name', 'Admin') }}</strong>
                    <span>{{ ucfirst(session('admin_role', 'admin')) }}</span>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <i class="ri-logout-box-r-line"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-wrapper">
        <header class="topbar">
            <div>
                <div class="topbar-title">@yield('title', 'Dashboard')</div>
                <div class="topbar-crumb">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-pill">
                    <span class="online-dot"></span>
                    System Online
                </div>
                <div class="topbar-pill">
                    <i class="ri-calendar-line"></i>
                    {{ now()->format('M d, Y') }}
                </div>
            </div>
        </header>

        <!-- Alerts -->
        <div style="padding: 16px 24px 0;">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="ri-error-warning-line"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <main class="page-body">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>