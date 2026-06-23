<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Paramita'))</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #00AB55;
            --primary-dark: #007B3F;
            --primary-light: #B7F2CC;
            --secondary: #3366FF;
            --info: #00B8D9;
            --success: #36B37E;
            --warning: #FFAB00;
            --error: #FF5630;
            --grey-0: #FFFFFF;
            --grey-100: #F9FAFB;
            --grey-200: #F4F6F8;
            --grey-300: #DFE3E8;
            --grey-400: #C4CDD5;
            --grey-500: #919EAB;
            --grey-600: #637381;
            --grey-700: #454F5B;
            --grey-800: #212B36;
            --grey-900: #161C24;
            --sidebar-width: 260px;
            --header-height: 64px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--grey-100);
            color: var(--grey-800);
            font-size: 0.875rem;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--grey-900);
            color: var(--grey-0);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            padding: 16px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            min-height: var(--header-height);
        }

        .sidebar-logo {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
        }

        .sidebar-logo span {
            color: var(--grey-0);
            font-weight: 300;
            margin-left: 4px;
        }

        .sidebar-section {
            padding: 12px 16px 8px;
        }

        .sidebar-section-title {
            font-size: 0.6875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--grey-500);
            padding: 0 8px;
            margin-bottom: 4px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: var(--grey-400);
            text-decoration: none;
            border-radius: 8px;
            margin: 2px 0;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .sidebar-nav li a:hover {
            background: rgba(255,255,255,0.06);
            color: var(--grey-0);
        }

        .sidebar-nav li a.active {
            background: rgba(0, 171, 85, 0.12);
            color: var(--primary);
        }

        .sidebar-nav li a .icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            position: absolute;
            bottom: 0;
            width: 100%;
            background: var(--grey-900);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: white;
            font-size: 0.875rem;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-weight: 600;
            color: var(--grey-0);
            font-size: 0.875rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user-role {
            font-size: 0.75rem;
            color: var(--grey-500);
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: var(--grey-0);
            border-bottom: 1px solid var(--grey-300);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 999;
        }

        .header-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--grey-800);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: transparent;
            color: var(--grey-600);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .header-btn:hover {
            background: var(--grey-200);
            color: var(--grey-800);
        }

        /* Main Content */
        .main {
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            min-height: 100vh;
        }

        .content {
            padding: 24px;
        }

        /* Cards */
        .card {
            background: var(--grey-0);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid var(--grey-200);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--grey-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--grey-800);
        }

        .card-body {
            padding: 24px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--grey-0);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid var(--grey-200);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: box-shadow 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .stat-icon.green { background: rgba(0, 171, 85, 0.12); color: var(--primary); }
        .stat-icon.blue { background: rgba(51, 102, 255, 0.12); color: var(--secondary); }
        .stat-icon.orange { background: rgba(255, 171, 0, 0.12); color: var(--warning); }
        .stat-icon.red { background: rgba(255, 86, 48, 0.12); color: var(--error); }
        .stat-icon.cyan { background: rgba(0, 184, 217, 0.12); color: var(--info); }

        .stat-content { flex: 1; }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--grey-500);
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--grey-800);
            line-height: 1.2;
        }

        .stat-change {
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 4px;
        }

        .stat-change.up { color: var(--success); }
        .stat-change.down { color: var(--error); }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.4;
        }

        .badge-success { background: rgba(54, 179, 126, 0.12); color: #118D57; }
        .badge-warning { background: rgba(255, 171, 0, 0.12); color: #B7791F; }
        .badge-error { background: rgba(255, 86, 48, 0.12); color: #CD2B20; }
        .badge-info { background: rgba(0, 184, 217, 0.12); color: #006C9C; }
        .badge-secondary { background: var(--grey-200); color: var(--grey-700); }

        /* Table */
        .table-wrapper { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--grey-500);
            border-bottom: 1px solid var(--grey-200);
            background: var(--grey-100);
        }

        table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--grey-200);
            color: var(--grey-700);
        }

        table tbody tr:hover {
            background: var(--grey-100);
        }

        /* Button */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 4px 8px rgba(0, 171, 85, 0.24);
        }

        .btn-secondary {
            background: var(--grey-200);
            color: var(--grey-700);
        }

        .btn-secondary:hover {
            background: var(--grey-300);
        }

        .btn-ghost {
            background: transparent;
            color: var(--grey-600);
        }

        .btn-ghost:hover {
            background: var(--grey-200);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8125rem;
        }

        /* Grid */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .mb-24 { margin-bottom: 24px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-8 { margin-bottom: 8px; }

        /* JSON Template Block */
        .json-block {
            background: var(--grey-900);
            color: var(--success);
            padding: 16px 20px;
            border-radius: 8px;
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: 0.8125rem;
            line-height: 1.6;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--grey-500);
        }

        .empty-state h3 {
            color: var(--grey-700);
            margin-top: 12px;
            font-size: 1rem;
        }

        .empty-state p {
            font-size: 0.875rem;
            margin-top: 4px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .header { left: 0; }
            .main { margin-left: 0; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
            .content { padding: 16px; }
        }

        /* Transitions */
        .fade-in {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--grey-400); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--grey-500); }

        /* Logout button */
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 16px;
            color: var(--grey-400);
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
        }

        .btn-logout:hover {
            background: rgba(255, 86, 48, 0.12);
            color: var(--error);
        }

        /* Section Header */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--grey-800);
        }

        .section-subtitle {
            font-size: 0.875rem;
            color: var(--grey-500);
            margin-top: 2px;
        }

        /* Chart placeholder */
        .chart-placeholder {
            height: 300px;
            background: linear-gradient(135deg, var(--grey-100), var(--grey-200));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--grey-500);
            font-size: 0.875rem;
        }

        /* Status dot */
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }

        .status-dot.green { background: var(--success); }
        .status-dot.red { background: var(--error); }
        .status-dot.orange { background: var(--warning); }

        /* Template Preview Modal */
        .tpl-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }
        .tpl-modal-overlay.open { display: flex; }
        .tpl-modal {
            background: var(--grey-0);
            border-radius: 16px;
            width: 90%;
            max-width: 720px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 24px 48px rgba(0,0,0,0.2);
            animation: modalSlideUp 0.25s ease;
        }
        @keyframes modalSlideUp {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .tpl-modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 20px 24px;
            border-bottom: 1px solid var(--grey-200);
        }
        .tpl-modal-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--grey-800);
        }
        .tpl-modal-close {
            width: 36px; height: 36px;
            border-radius: 8px; border: none;
            background: transparent;
            color: var(--grey-500);
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s ease;
        }
        .tpl-modal-close:hover { background: var(--grey-200); color: var(--grey-800); }
        .tpl-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
        }
        .tpl-modal-desc {
            font-size: 0.875rem;
            color: var(--grey-600);
            margin-bottom: 16px;
        }
        .tpl-modal-json {
            background: var(--grey-900);
            color: var(--success);
            padding: 20px;
            border-radius: 10px;
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: 0.8125rem;
            line-height: 1.7;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: 50vh;
            overflow-y: auto;
        }
        .tpl-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--grey-200);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Clickable template name */
        .tpl-clickable {
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            transition: color 0.15s ease;
        }
        .tpl-clickable:hover {
            color: var(--primary);
            text-decoration: underline;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @php
        $currentUser = auth()->user()->loadMissing('unitKerja:id,nama,kode');
        $role = \App\Helpers\RoleHelper::getPrimaryRoleName($currentUser) ?? 'viewer';
        $unitKerja = session('unit_kerja') ?: ($currentUser->unitKerja ? [
            'id' => $currentUser->unitKerja->id,
            'nama' => $currentUser->unitKerja->nama,
            'kode' => $currentUser->unitKerja->kode,
        ] : null);
        $homeRoute = \App\Helpers\RoleHelper::getHomeRoute($role);
        $roleDashboard = '#';

        if ($homeRoute) {
            $roleDashboard = str_starts_with($homeRoute, '/')
                ? $homeRoute
                : (\Illuminate\Support\Facades\Route::has($homeRoute) ? route($homeRoute) : '#');
        }
    @endphp
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ $roleDashboard }}" class="sidebar-logo">
                P<span>aramita</span>
            </a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Menu</div>
            <ul class="sidebar-nav">
                @yield('sidebar-nav')
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">{{ strtoupper(substr($currentUser->name, 0, 2)) }}</div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ $currentUser->name }}</div>
                    <div class="sidebar-user-role">{{ \App\Helpers\RoleHelper::getLabel($role) }}</div>
                    @if($unitKerja)
                        <div class="sidebar-user-role" title="{{ $unitKerja['kode'] }}" style="color: var(--grey-400);">{{ $unitKerja['nama'] }}</div>
                    @endif
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin-top: 12px;">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Header -->
    <header class="header">
        <h1 class="header-title">@yield('header-title')</h1>
        <div class="header-actions">
            @yield('header-actions')
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="content fade-in">
            @if(session('success'))
                <div style="background: rgba(54, 179, 126, 0.12); border: 1px solid rgba(54, 179, 126, 0.24); border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; color: #118D57; font-weight: 500;">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div style="background: rgba(255, 86, 48, 0.12); border: 1px solid rgba(255, 86, 48, 0.24); border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; color: #CD2B20; font-weight: 500;">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    {{-- Template Preview Modal --}}
    <div id="templateModal" class="tpl-modal-overlay" onclick="if(event.target===this)closeTemplateModal()">
        <div class="tpl-modal">
            <div class="tpl-modal-header">
                <div>
                    <h3 class="tpl-modal-title" id="modalTemplateName"></h3>
                    <div style="display:flex;gap:6px;margin-top:4px;">
                        <span class="badge badge-info" id="modalTemplateVersion"></span>
                        <span class="badge badge-secondary" id="modalTemplateCategory"></span>
                    </div>
                </div>
                <button class="tpl-modal-close" onclick="closeTemplateModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="tpl-modal-body">
                <div class="tpl-modal-desc" id="modalTemplateDesc"></div>
                <div class="tpl-modal-json" id="modalTemplateJson"></div>
            </div>
            <div class="tpl-modal-footer">
                <button class="btn btn-secondary btn-sm" onclick="copyTemplateJson()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copy JSON
                </button>
                <button class="btn btn-ghost btn-sm" onclick="closeTemplateModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
    function openTemplateModal(name, version, category, description, jsonStr) {
        var modal = document.getElementById('templateModal');
        document.getElementById('modalTemplateName').textContent = name;
        document.getElementById('modalTemplateVersion').textContent = 'v' + version;
        document.getElementById('modalTemplateCategory').textContent = category || 'uncategorized';
        document.getElementById('modalTemplateDesc').textContent = description || 'No description';
        try {
            var parsed = JSON.parse(jsonStr);
            document.getElementById('modalTemplateJson').textContent = JSON.stringify(parsed, null, 2);
        } catch(e) {
            document.getElementById('modalTemplateJson').textContent = jsonStr;
        }
        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeTemplateModal() {
        var modal = document.getElementById('templateModal');
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    function copyTemplateJson() {
        var text = document.getElementById('modalTemplateJson').textContent;
        navigator.clipboard.writeText(text).then(function() {
            var btn = document.querySelector('.tpl-modal-footer .btn-secondary');
            if (!btn) return;
            var orig = btn.innerHTML;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Copied!';
            btn.style.color = 'var(--success)';
            setTimeout(function() { btn.innerHTML = orig; btn.style.color = ''; }, 1500);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeTemplateModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-tpl-click]').forEach(function(el) {
            el.addEventListener('click', function() {
                openTemplateModal(
                    this.dataset.tplName || '',
                    this.dataset.tplVersion || '',
                    this.dataset.tplCategory || '',
                    this.dataset.tplDesc || '',
                    this.dataset.tplJson || '{}'
                );
            });
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
