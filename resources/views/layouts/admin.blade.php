<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Uyayi Store')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Yellowtail&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Customer theme colors */
            --primary-green: #8b9c41;
            --soft-yellow: #F4E4A6;
            --warm-beige: #F5F0E8;
            --soft-brown: #8B7355;
            --text-dark: #3D3D3D;
            
            /* Admin layout */
            --sidebar-width: 260px;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e5e7eb;
            --text-muted: #6b7280;
            --bg-light: var(--warm-beige);
            --success-green: #10b981;
            --warning-orange: #f59e0b;
        }

        * {
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: calc(100vh - 4px);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            position: fixed;
            left: 0;
            top: 4px;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand .logo {
            width: 36px;
            height: 36px;
            background: var(--primary-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .sidebar-brand .brand-name {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Yellowtail', cursive;
            color: var(--primary-green);
        }

        .sidebar-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-user .avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-green), var(--soft-brown));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .sidebar-user .user-info {
            flex: 1;
        }

        .sidebar-user .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .sidebar-user .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .nav-section {
            padding: 0.5rem 1.5rem;
            margin-top: 0.5rem;
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover {
            background: var(--soft-yellow);
            color: var(--primary-green);
        }

        .sidebar-link.active {
            background: var(--soft-yellow);
            color: var(--primary-green);
            border-left-color: var(--primary-green);
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link .badge {
            margin-left: auto;
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }

        .sidebar-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: none;
        }

        .sidebar-submenu.show {
            display: block;
        }

        .sidebar-submenu .sidebar-link {
            padding-left: 3.25rem;
            font-size: 0.85rem;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Header */
        .top-header {
            background: white;
            border-bottom: 1px solid var(--sidebar-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .breadcrumb-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }

        .breadcrumb-nav a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            color: var(--primary-green);
        }

        .breadcrumb-nav .separator {
            color: var(--text-muted);
        }

        .breadcrumb-nav .current {
            color: var(--text-dark);
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .header-btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-light);
            color: var(--text-muted);
            border: none;
            cursor: pointer;
            position: relative;
        }

        .header-btn-icon:hover {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .header-btn-icon .notification-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }

        .btn-view-shop {
            background: var(--primary-green);
            color: white;
            border-radius: 25px;
        }

        .btn-view-shop:hover {
            background: var(--soft-brown);
            color: white;
        }

        /* Page Content */
        .page-content {
            padding: 1.5rem;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        /* Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--sidebar-border);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-card .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Tabs */
        .nav-tabs-custom {
            border: none;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .nav-tabs-custom .nav-link:hover {
            background: var(--soft-yellow);
            color: var(--text-dark);
        }

        .nav-tabs-custom .nav-link.active {
            background: var(--primary-green);
            color: white;
        }

        .nav-tabs-custom .nav-link .badge {
            margin-left: 0.5rem;
        }

        /* Data Table */
        .data-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--sidebar-border);
            overflow: hidden;
        }

        .data-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: var(--bg-light);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            gap: 0.5rem;
            min-width: 250px;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            flex: 1;
            font-size: 0.875rem;
        }

        .search-box i {
            color: var(--text-muted);
        }

        .filter-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid var(--sidebar-border);
            border-radius: 8px;
            background: white;
            font-size: 0.875rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-btn:hover {
            border-color: var(--primary-green);
            color: var(--primary-green);
        }

        .table-custom {
            margin: 0;
        }

        .table-custom thead th {
            background: var(--bg-light);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 1rem 1.5rem;
            border: none;
        }

        .table-custom tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--sidebar-border);
            font-size: 0.875rem;
        }

        .table-custom tbody tr:hover {
            background: var(--bg-light);
        }

        .customer-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .customer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green), var(--soft-brown));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .customer-name {
            font-weight: 500;
        }

        .order-number {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .status-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .status-processing {
            background: #dbeafe;
            color: #2563eb;
        }

        .status-shipped {
            background: #d1fae5;
            color: #059669;
        }

        .status-completed {
            background: #d1fae5;
            color: #059669;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 500;
            border: 1px solid var(--primary-green);
            background: white;
            color: var(--primary-green);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            background: var(--primary-green);
            color: white;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
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
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--text-dark);
            cursor: pointer;
        }

        /* Customer theme elements */
        .decorative-border {
            height: 4px;
            background: repeating-linear-gradient(
                90deg,
                var(--primary-green) 0px,
                var(--primary-green) 10px,
                var(--soft-yellow) 10px,
                var(--soft-yellow) 20px
            );
        }

        .btn-primary-custom {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background-color: var(--soft-brown);
            border-color: var(--soft-brown);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-primary:hover {
            background-color: var(--soft-brown);
            border-color: var(--soft-brown);
        }

        .btn-outline-primary {
            color: var(--primary-green);
            border-color: var(--primary-green);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
        }

        .eco-badge {
            background-color: var(--primary-green);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Decorative top border -->
    <div class="decorative-border"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo">U</div>
            <span class="brand-name">Uyayi</span>
        </div>

        <div class="sidebar-user">
            <div class="avatar">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <div class="user-role">Administrator</div>
            </div>
            <i class="bi bi-chevron-right text-muted"></i>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Store Management</div>
            </div>

            <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Orders</span>
                @if(isset($pendingOrdersCount) && $pendingOrdersCount > 0)
                    <span class="badge bg-primary">{{ $pendingOrdersCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Products</span>
            </a>

            <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Customers</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Inventory</div>
            </div>

            <a href="{{ route('admin.inventory') }}" class="sidebar-link {{ request()->routeIs('admin.inventory') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i>
                <span>Stock Management</span>
            </a>

            <a href="{{ route('admin.inventory.restocks') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.restocks') ? 'active' : '' }}">
                <i class="bi bi-arrow-repeat"></i>
                <span>Restock History</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Finance</div>
            </div>

            <a href="{{ route('admin.payments') }}" class="sidebar-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i>
                <span>Payments</span>
            </a>

            <a href="{{ route('admin.expenses') }}" class="sidebar-link {{ request()->routeIs('admin.expenses') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i>
                <span>Expenses</span>
            </a>

            <a href="{{ route('admin.reports') }}" class="sidebar-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i>
                <span>Reports</span>
            </a>

            <div class="nav-section">
                <div class="nav-section-title">Settings</div>
            </div>

            <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Store Settings</span>
            </a>

            <a href="{{ route('admin.account') }}" class="sidebar-link {{ request()->routeIs('admin.account') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Account</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--sidebar-border); margin-top: auto;">
            <a href="{{ route('homepage') }}" class="sidebar-link" style="padding: 0.5rem 0;">
                <i class="bi bi-shop"></i>
                <span>Visit Store</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="sidebar-link w-100 text-start border-0 bg-transparent" style="padding: 0.5rem 0;">
                    <i class="bi bi-box-arrow-left"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="bi bi-list"></i>
                </button>
                <nav class="breadcrumb-nav">
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                    <span class="separator">&gt;</span>
                    @yield('breadcrumb', '<span class="current">Dashboard</span>')
                </nav>
            </div>

            <div class="header-actions">
                <button class="header-btn-icon">
                    <i class="bi bi-bell"></i>
                    <span class="notification-dot"></span>
                </button>
                <button class="header-btn-icon">
                    <i class="bi bi-question-circle"></i>
                </button>
                <a href="{{ route('homepage') }}" class="header-btn btn-view-shop">
                    View Shop
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3 mb-0" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3 mb-0" role="alert">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
