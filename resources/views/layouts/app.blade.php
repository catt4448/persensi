<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            padding: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand {
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .sidebar-brand:hover {
            color: #fff;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-item {
            margin: 0.25rem 0;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #3498db;
        }

        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #3498db;
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 20px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-top {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-wrapper {
            flex: 1;
            padding: 2rem;
        }

        .footer {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            margin-top: auto;
            border-top: 1px solid #dee2e6;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.show {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-toggle {
            display: none;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .nav-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .nav-item.has-submenu.active .nav-submenu {
            max-height: 500px;
        }

        .nav-submenu .nav-link {
            padding: 0.5rem 1.5rem 0.5rem 3.5rem;
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
        }

        .nav-submenu .nav-link::before {
            content: '└';
            position: absolute;
            left: 2.5rem;
            opacity: 0.5;
        }

        .nav-link.has-submenu {
            position: relative;
        }

        .nav-link.has-submenu::after {
            content: '\f282';
            font-family: 'bootstrap-icons';
            margin-left: auto;
            transition: transform 0.3s;
            font-size: 0.9rem;
        }

        .nav-item.has-submenu.active > .nav-link.has-submenu::after {
            transform: rotate(90deg);
        }

        @media (max-width: 768px) {
            .nav-submenu {
                max-height: 500px !important;
            }
        }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="sidebar-brand">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'admin')
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link" href="{{ route('mahasiswa.index') }}">
                                <i class="bi bi-mortarboard"></i>
                                <span>Data Mahasiswa</span>
                        </a>
                    </li>
                    <li class="nav-item has-submenu {{ request()->routeIs('admin.sesi.*') || request()->routeIs('admin.kehadiran.*') ? 'active' : '' }}">
                        <a class="nav-link has-submenu {{ request()->routeIs('admin.sesi.*') || request()->routeIs('admin.kehadiran.*') ? 'active' : '' }}" 
                           href="{{ route('admin.sesi.index') }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Sesi</span>
                        </a>
                        <ul class="nav-submenu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.sesi.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.sesi.index') }}">
                                    <i class="bi bi-calendar-event"></i>
                                    <span>Daftar Sesi</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.kehadiran.*') ? 'active' : '' }}" 
                                   href="{{ route('admin.sesi.index') }}">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Kehadiran</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                           href="{{ route('admin.profile.edit') }}">
                            <i class="bi bi-gear"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('users.index') }}">
                            <i class="bi bi-people"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                </ul>
            @else
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-calendar-check"></i>
                            <span>Kehadiran Saya</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-person-circle"></i>
                            <span>Profil</span>
                        </a>
                    </li>
                </ul>
            @endif
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <nav class="navbar-top">
            <div class="d-flex align-items-center">
                <button class="btn btn-link sidebar-toggle d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h4 class="mb-0 ms-2">@yield('page-title', 'Dashboard')</h4>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <small class="text-muted">
                        <span class="badge bg-{{ auth()->user()->role === 'admin' ? 'danger' : 'primary' }}">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </small>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Content Wrapper -->
        <main class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <p class="mb-0 text-muted">
                            <i class="bi bi-heart-fill text-danger"></i> Made with Laravel
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('show');
        }

        // Toggle submenu and auto-open if active
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-open submenu if on sesi or kehadiran pages
            const sesiSubmenu = document.querySelector('.nav-item.has-submenu');
            if (sesiSubmenu && sesiSubmenu.classList.contains('active')) {
                sesiSubmenu.classList.add('active');
            }

            // Toggle submenu on click (desktop only)
            const submenuLinks = document.querySelectorAll('.nav-link.has-submenu');
            submenuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (window.innerWidth > 768 && !this.href.includes('#') && !window.location.href.includes('sesi') && !window.location.href.includes('kehadiran')) {
                        e.preventDefault();
                        const navItem = this.closest('.nav-item.has-submenu');
                        navItem.classList.toggle('active');
                    }
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>

