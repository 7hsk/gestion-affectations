<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chef de Département') - ENSA Al Hoceima</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')

    <style>
        :root {
            /* Enhanced color scheme for Chef de Département */
            --primary-color: #1a1a2e;
            --primary-dark: #16213e;
            --secondary-color: #0f3460;
            --accent-color: #e94560;
            --accent-light: #f39c12;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --light-color: #ecf0f1;
            --dark-color: #1a1a2e;
            --sidebar-width: 280px;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-dark: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            --gradient-accent: linear-gradient(135deg, #e94560 0%, #f39c12 100%);
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Enhanced Sidebar with Perfect Gradients */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--gradient-dark);
            color: white;
            z-index: 1000;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        /* Removed heavy sidebar pattern for better performance */

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        /* Removed heavy sidebar header animation for better performance */

        .sidebar-header h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }

        .sidebar-header .subtitle {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
            margin-top: 0.5rem;
            font-weight: 500;
            position: relative;
            z-index: 2;
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

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 0 25px 25px 0;
            margin-right: 1rem;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link:hover {
            color: white;
            background: var(--gradient-accent);
            box-shadow: 0 5px 20px rgba(233, 69, 96, 0.3);
            transform: translateX(10px);
        }

        .nav-link.active {
            background: var(--gradient-accent);
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(233, 69, 96, 0.4);
            transform: translateX(5px);
        }

        .nav-link.active::before {
            left: 0;
        }

        .nav-link i {
            width: 24px;
            margin-right: 1rem;
            font-size: 1.2rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            z-index: 2;
        }

        .nav-link:hover i {
            transform: scale(1.2) rotate(5deg);
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        .nav-link.active i {
            transform: scale(1.1);
            text-shadow: 0 0 8px rgba(255,255,255,0.3);
        }

        .nav-item {
            position: relative;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 4px;
            height: 0;
            background: linear-gradient(135deg, #e94560, #f06292);
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
            background: linear-gradient(135deg, #f06292, #f8bbd9);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes gentleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        .sidebar-header {
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-header:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
            transform: scale(1.02);
        }

        .sidebar-header i {
            animation: gentleFloat 3s ease-in-out infinite;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-header:hover i {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 0 10px rgba(233, 69, 96, 0.5));
        }

        /* Sidebar minimized styles */
        .sidebar.minimized {
            width: 70px;
        }

        .sidebar.minimized .sidebar-header h4,
        .sidebar.minimized .sidebar-header .subtitle,
        .sidebar.minimized .nav-link span {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.minimized .nav-link {
            justify-content: center;
            padding: 0.75rem 0;
            margin-right: 0;
        }

        .sidebar.minimized .nav-link i {
            margin-right: 0;
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-minimized {
            margin-left: 70px;
        }

        .main-content.sidebar-hidden {
            margin-left: 0;
        }

        .main-content::before {
            content: '';
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="0.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
            z-index: 0;
        }

        /* Enhanced Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            position: relative;
            z-index: 10;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
            z-index: -1;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            margin: 0;
            background: var(--gradient-dark);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 1.8rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            z-index: 2;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
            transition: transform 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        /* Enhanced Content Area */
        .content {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Enhanced Cards */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 100%);
            z-index: -1;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        .card-header {
            background: var(--gradient-dark);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            border: none;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
        }

        .card-header h5 {
            margin: 0;
            font-weight: 700;
            position: relative;
            z-index: 2;
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(44, 62, 80, 0.3);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
        }

        .stats-card.success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
        }

        .stats-card.info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Enhanced Buttons */
        .btn {
            border-radius: 15px;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: none;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: var(--gradient-accent);
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
        }

        .btn-primary:hover {
            background: var(--gradient-accent);
            box-shadow: 0 8px 25px rgba(233, 69, 96, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--accent-color), #c0392b);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #229954);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info-color), #2980b9);
        }

        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr:hover {
            background-color: rgba(44, 62, 80, 0.05);
        }

        /* Badges */
        .badge {
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
        }

        .badge.bg-pending {
            background-color: var(--warning-color) !important;
        }

        .badge.bg-approved {
            background-color: var(--success-color) !important;
        }

        .badge.bg-rejected {
            background-color: var(--accent-color) !important;
        }

        /* Responsive behavior for screens less than 400px */
        @media (max-width: 400px) {
            .sidebar {
                width: 70px !important;
            }

            .sidebar .sidebar-header h4,
            .sidebar .sidebar-header .subtitle,
            .sidebar .nav-link span {
                opacity: 0 !important;
                visibility: hidden !important;
            }

            .sidebar .nav-link {
                justify-content: center !important;
                padding: 0.75rem 0 !important;
                margin-right: 0 !important;
            }

            .sidebar .nav-link i {
                margin-right: 0 !important;
            }

            .main-content {
                margin-left: 70px !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content {
                padding: 1rem;
            }
        }

        /* Enhanced Notifications */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gradient-accent);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(233, 69, 96, 0.4);
            animation: pulse-notification 2s infinite;
            border: 2px solid white;
        }

        @keyframes pulse-notification {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4>Chef de Département</h4>
            <div class="subtitle">{{ Auth::user()->departement->nom ?? 'ENSA Al Hoceima' }}</div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-main">
                <div class="nav-item {{ request()->routeIs('chef.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('chef.dashboard') }}" class="nav-link {{ request()->routeIs('chef.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de Bord
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.unites-enseignement') ? 'active' : '' }}">
                    <a href="{{ route('chef.unites-enseignement') }}" class="nav-link {{ request()->routeIs('chef.unites-enseignement') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        Unités d'Enseignement
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.enseignants') ? 'active' : '' }}">
                    <a href="{{ route('chef.enseignants') }}" class="nav-link {{ request()->routeIs('chef.enseignants') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Enseignants
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.affectations') ? 'active' : '' }}">
                    <a href="{{ route('chef.affectations') }}" class="nav-link {{ request()->routeIs('chef.affectations') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        Affectations
                        @php
                            $pendingCount = \App\Models\Affectation::whereHas('uniteEnseignement', function($query) {
                                $query->where('departement_id', Auth::user()->departement_id);
                            })->where('validee', 'en_attente')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="notification-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.gestion-demandes') ? 'active' : '' }}">
                    <a href="{{ route('chef.gestion-demandes') }}" class="nav-link {{ request()->routeIs('chef.gestion-demandes') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        Gestion des Demandes
                        @php
                            $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);
                            $nextYearPendingCount = \App\Models\Affectation::whereHas('uniteEnseignement', function($query) {
                                $query->where('departement_id', Auth::user()->departement_id);
                            })->where('annee_universitaire', $nextYear)->where('validee', 'en_attente')->count();
                        @endphp
                        @if($nextYearPendingCount > 0)
                            <span class="notification-badge">{{ $nextYearPendingCount }}</span>
                        @endif
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.historique') ? 'active' : '' }}">
                    <a href="{{ route('chef.historique') }}" class="nav-link {{ request()->routeIs('chef.historique') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        Historique
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('chef.rapports') ? 'active' : '' }}">
                    <a href="{{ route('chef.rapports') }}" class="nav-link {{ request()->routeIs('chef.rapports') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        Rapports
                    </a>
                </div>
            </div>

            <div class="nav-footer">
                <div class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <h1 class="page-title">@yield('title', 'Chef de Département')</h1>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">Chef de Département</small>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="content">
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

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
