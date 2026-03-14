<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ config('app.name') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --sidebar-text: #cbd5e1;
            --sidebar-heading: #94a3b8;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f1f5f9;
        }

        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            overflow-y: auto;
            z-index: 1040;
            transition: transform 0.3s ease;
        }

        .admin-sidebar .sidebar-brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .admin-sidebar .sidebar-brand h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
        }

        .sidebar-nav {
            padding: 0.75rem 0;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--sidebar-heading);
            padding: 0.75rem 1.25rem 0.5rem;
        }

        .sidebar-nav .nav-link {
            color: var(--sidebar-text);
            padding: 0.55rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            border-radius: 0;
            transition: all 0.15s ease;
        }

        .sidebar-nav .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.2rem;
            width: 1.5rem;
            text-align: center;
        }

        /* Header */
        .admin-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            z-index: 1030;
        }

        /* Main Content */
        .admin-main {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--header-height) + 1.5rem);
            padding-bottom: 2rem;
            min-height: 100vh;
        }

        .admin-main .content-wrapper {
            padding: 0 1.5rem;
        }

        /* Stat Cards */
        .stat-card {
            border: none;
            border-radius: 0.75rem;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Page heading */
        .page-heading {
            margin-bottom: 1.5rem;
        }

        .page-heading h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        /* Card styling */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
        }

        /* Mobile responsive */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            .admin-sidebar.show {
                transform: translateX(0);
            }
            .admin-header {
                left: 0;
            }
            .admin-main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    @include('layouts.sidebar')

    <!-- Header -->
    <header class="admin-header">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-link text-dark d-lg-none p-0" onclick="document.querySelector('.admin-sidebar').classList.toggle('show')">
                <i class='bx bx-menu fs-4'></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.875rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- Domain selector for admin --}}
            @if(isset($domains) && $domains->count() > 1)
            <select id="admin-domain-selector" class="form-select form-select-sm" style="width: 200px;">
                @foreach($domains as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
            @endif

            {{-- User dropdown --}}
            <div class="dropdown">
                <button class="btn btn-link text-dark text-decoration-none dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:32px;height:32px;font-size:0.8rem;">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <span class="d-none d-md-inline" style="font-size:0.875rem;">{{ Auth::user()->name ?? 'Admin' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class='bx bx-log-out me-2'></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="content-wrapper">
            {{-- Page Heading --}}
            @hasSection('page-title')
            <div class="page-heading d-flex justify-content-between align-items-center">
                <div>
                    <h1>@yield('page-title')</h1>
                    @hasSection('page-description')
                    <p class="text-muted mb-0" style="font-size:0.875rem;">@yield('page-description')</p>
                    @endif
                </div>
                <div>
                    @yield('page-actions')
                </div>
            </div>
            @endif

            {{-- Alert Messages --}}
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class='bx bx-check-circle me-2'></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error-circle me-2'></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class='bx bx-error-circle me-2'></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay d-lg-none" onclick="document.querySelector('.admin-sidebar').classList.remove('show')" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1035;"></div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @vite(['resources/js/app.js'])

    <script>
        // Init Select2
        $(document).ready(function() {
            $('.select2').select2({ theme: 'bootstrap-5' });
        });

        // Mobile sidebar overlay
        const sidebar = document.querySelector('.admin-sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        if (sidebar && overlay) {
            const observer = new MutationObserver(() => {
                overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            });
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        }
    </script>
    @stack('scripts')
    @stack('select2-ajax-init')
</body>
</html>
