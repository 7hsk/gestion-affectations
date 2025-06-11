<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Stunning Red Theme for Admin */
            --admin-primary: #dc2626;
            --admin-secondary: #991b1b;
            --admin-success: #059669;
            --admin-warning: #d97706;
            --admin-danger: #b91c1c;
            --admin-info: #0891b2;
            --admin-dark: #7f1d1d;
            --admin-light: #fef2f2;
            --sidebar-gradient: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #dc2626 100%);
            --gradient-primary: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            --gradient-secondary: linear-gradient(135deg, #f87171 0%, #fca5a5 100%);
            --gradient-accent: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
            --shadow-sm: 0 1px 2px 0 rgb(220 38 38 / 0.1);
            --shadow-md: 0 4px 6px -1px rgb(220 38 38 / 0.15), 0 2px 4px -2px rgb(220 38 38 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(220 38 38 / 0.2), 0 4px 6px -4px rgb(220 38 38 / 0.15);
        }

        body {
            overflow-x: hidden;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 50%, #fecaca 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
        }

        .sidebar {
            background: var(--sidebar-gradient);
            min-height: 100vh;
            color: white;
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 280px;
            box-shadow: 0 4px 12px rgba(127, 29, 29, 0.2);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        /* Sidebar is always visible */

        /* Removed heavy sidebar pattern for better performance */

        /* Removed heavy sidebar after animation for better performance */

        /* Removed float animation keyframes for better performance */

        .sidebar-brand {
            padding: 1.5rem 1.5rem;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            overflow: hidden;
            flex-shrink: 0;
        }

        /* Removed heavy sidebar brand animation for better performance */

        .sidebar-brand i {
            background: linear-gradient(45deg, #fca5a5, #f87171);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.8rem;
            margin-right: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .sidebar-brand span {
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .sidebar-nav {
            padding: 1rem 0;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .nav-main {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .nav-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .nav-item {
            margin: 0.3rem 0;
            position: relative;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 4px;
            height: 0;
            background: linear-gradient(135deg, #ef4444, #f87171);
            border-radius: 0 2px 2px 0;
            transition: height 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            transform: translateY(-50%);
            z-index: 1;
        }

        .nav-item:hover::before {
            height: 60%;
        }

        .nav-item.active::before {
            height: 80%;
            background: linear-gradient(135deg, #f87171, #fca5a5);
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            border-radius: 0 25px 25px 0;
            margin: 0 1rem 0 0;
            position: relative;
            overflow: hidden;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .sidebar-link:hover::before {
            left: 100%;
        }

        .sidebar-link:hover {
            color: white;
            background: var(--gradient-primary);
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.3);
            transform: translateX(10px);
        }

        .sidebar-link.active {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(220, 38, 38, 0.4);
            transform: translateX(5px);
        }

        .sidebar-link.active::before {
            left: 0;
        }

        .sidebar-link i {
            margin-right: 1rem;
            width: 24px;
            font-size: 1.2rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            z-index: 2;
        }

        .sidebar-link:hover i {
            transform: scale(1.2) rotate(5deg);
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .sidebar-link.active i {
            transform: scale(1.1);
            text-shadow: 0 0 8px rgba(255,255,255,0.3);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        .sidebar-brand {
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-brand:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
            transform: scale(1.02);
        }

        .sidebar-brand i {
            animation: gentleFloat 3s ease-in-out infinite;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-brand:hover i {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.5));
        }

        .content-area {
            padding: 24px;
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
        }

        .navbar-admin {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: var(--shadow-md);
            border: none;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .navbar-admin .navbar-brand {
            font-weight: 700;
            color: var(--admin-dark);
            font-size: 1.25rem;
        }

        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.9);
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            border-radius: 16px 16px 0 0 !important;
            background: rgba(255, 255, 255, 0.8) !important;
        }

        /* Enhanced Buttons */
        .btn {
            border-radius: 12px;
            font-weight: 600;
            border: none;
            position: relative;
            overflow: hidden;
        }

        /* Removed heavy button sliding animation for better performance */

        .btn-primary {
            background: var(--gradient-primary);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.4);
            color: white;
        }

        .btn-danger {
            background: var(--gradient-accent);
            box-shadow: 0 4px 15px rgba(185, 28, 28, 0.3);
        }

        .btn-danger:hover {
            background: var(--gradient-accent);
            box-shadow: 0 8px 25px rgba(185, 28, 28, 0.4);
        }

        /* Enhanced Tables */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table th {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
            color: var(--admin-dark);
            border: none;
        }

        .table td {
            border-color: rgba(226, 232, 240, 0.5);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        /* Enhanced Badges */
        .badge {
            border-radius: 8px;
            font-weight: 600;
            padding: 6px 12px;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .content-area {
                margin-left: 80px;
                width: calc(100% - 80px);
                padding: 16px;
            }
            .sidebar-brand span {
                display: none;
            }
            .sidebar-link span {
                display: none;
            }
            .sidebar-link {
                text-align: center;
                padding: 16px 8px;
                justify-content: center;
            }
            .sidebar-link i {
                margin-right: 0;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #94a3b8, #64748b);
        }

        /* Additional Admin Enhancements */
        .admin-card-hover {
            /* Removed heavy transition for better performance */
        }

        .admin-card-hover:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Form Controls */
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid rgba(226, 232, 240, 0.8);
            font-weight: 500;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid rgba(226, 232, 240, 0.8);
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            font-weight: 600;
        }

        /* Enhanced Modals */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            border-radius: 20px 20px 0 0;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        .modal-footer {
            border-radius: 0 0 20px 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.5);
        }

        /* Enhanced Dropdowns */
        .dropdown-menu {
            border-radius: 16px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: rgba(255, 255, 255, 0.95);
        }

        .dropdown-item {
            border-radius: 12px;
            margin: 4px 8px;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, var(--admin-primary), #3b82f6);
            color: white;
        }

        /* Enhanced Alerts */
        .alert {
            border-radius: 16px;
            border: none;
            font-weight: 500;
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(217, 119, 6, 0.1), rgba(245, 158, 11, 0.1));
            color: var(--admin-warning);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
            color: var(--admin-success);
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(239, 68, 68, 0.1));
            color: var(--admin-danger);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(8, 145, 178, 0.1), rgba(6, 182, 212, 0.1));
            color: var(--admin-info);
        }

        /* Enhanced Pagination */
        .pagination {
            border-radius: 12px;
            overflow: hidden;
        }

        .page-link {
            border: none;
            font-weight: 600;
        }

        .page-link:hover {
            background: var(--admin-primary);
            color: white;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--admin-primary), #3b82f6);
            border: none;
        }

        /* Loading States */
        .btn.loading {
            position: relative;
            color: transparent;
        }

        /* Removed heavy loading animation for better performance */

        /* Enhanced Status Indicators */
        .status-indicator {
            position: relative;
            display: inline-block;
        }

        /* Removed heavy pulse animation for better performance */

        /* Sidebar is now always visible - no toggle needed */
    </style>

    @stack('styles')
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Enhanced Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-university me-2"></i>
            <span>Admin Panel</span>
        </div>
        <div class="sidebar-nav">
            <div class="nav-main">
                <div class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i>
                        <span>Utilisateurs</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('admin.departements.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.departements.index') }}" class="sidebar-link {{ request()->routeIs('admin.departements.*') ? 'active' : '' }}">
                        <i class="fas fa-building"></i>
                        <span>Départements</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('admin.ues.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.ues.index') }}" class="sidebar-link {{ request()->routeIs('admin.ues.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>Unités d'Enseignement</span>
                    </a>
                </div>
            </div>

            <div class="nav-footer">
                <div class="nav-item">
                    <a href="{{ route('logout') }}" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>

    <!-- Page Content -->
    <div class="content-area" id="contentArea">

        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-admin mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">
                    @yield('title', 'Admin Panel')
                </span>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">{{ Auth::user()->name }}</span>
                    <i class="fas fa-user-circle fa-2x text-primary"></i>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Content -->
        @yield('content')
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

@include('sweetalert::alert')
@stack('scripts')

<script>
// Admin layout initialized - sidebar is always visible
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔴 ADMIN LAYOUT INITIALIZED - Sidebar always visible');
});
</script>
</body>
</html>
