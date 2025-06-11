<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Coordonnateur de Filière') - ENSA Al Hoceima</title>

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
            /* Stunning Green Theme for Coordonnateur */
            --coord-primary: #059669;
            --coord-secondary: #047857;
            --coord-accent: #10b981;
            --coord-light: #d1fae5;
            --coord-dark: #064e3b;
            --gradient-primary: linear-gradient(135deg, #059669 0%, #10b981 100%);
            --gradient-secondary: linear-gradient(135deg, #047857 0%, #059669 100%);
            --gradient-accent: linear-gradient(135deg, #34d399 0%, #6ee7b7 100%);
            --sidebar-gradient: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Enhanced Sidebar with Purple Gradients */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--sidebar-gradient);
            color: white;
            z-index: 1000;
            overflow: hidden; /* Remove scrolling */
            box-shadow: 0 4px 12px rgba(6, 78, 59, 0.2);
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease, transform 0.3s ease;
        }

        .sidebar.minimized {
            width: 70px;
        }

        .sidebar.minimized .sidebar-header h4,
        .sidebar.minimized .nav-link span {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.minimized .nav-link {
            justify-content: center;
            padding: 0.75rem 0;
        }

        .sidebar.minimized .nav-link i {
            margin-right: 0;
        }

        /* Removed sidebar toggle from layout - will be added to specific views */

        /* Removed heavy sidebar pattern for better performance */

        /* Removed heavy sidebar after animation for better performance */

        /* Removed float animation keyframes for better performance */

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            backdrop-filter: blur(20px);
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.3rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
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

        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 4px;
            height: 0;
            background: linear-gradient(135deg, #059669, #10b981);
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
            background: linear-gradient(135deg, #10b981, #6ee7b7);
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
            z-index: 1;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--gradient-secondary);
            transition: opacity 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: -1;
            opacity: 0;
        }

        .nav-link:hover::after {
            opacity: 1;
        }

        .nav-link:hover {
            color: white;
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(5, 150, 105, 0.3);
        }

        .nav-link.active {
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(5, 150, 105, 0.4);
            transform: translateX(5px);
        }

        .nav-link.active::after {
            opacity: 1;
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

        .sidebar-header h4 {
            animation: gentleFloat 3s ease-in-out infinite;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-header:hover h4 {
            transform: scale(1.05);
            filter: drop-shadow(0 0 10px rgba(5, 150, 105, 0.5));
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            position: relative;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-minimized {
            margin-left: 70px;
        }

        .main-content::before {
            content: '';
            position: fixed;
            top: 0;
            left: 280px;
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
            box-shadow: 0 8px 32px rgba(6, 78, 59, 0.1);
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
            position: relative;
            z-index: 2;
        }

        .page-title {
            margin: 0;
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 1.8rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 4px 20px rgba(6, 78, 59, 0.1);
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
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
            box-shadow: 0 10px 40px rgba(6, 78, 59, 0.1);
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
            box-shadow: 0 20px 60px rgba(6, 78, 59, 0.15);
        }

        .card-header {
            background: var(--gradient-secondary);
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
            background: var(--gradient-primary);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
            color: white;
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            color: white;
        }

        .btn-success {
            background: var(--gradient-secondary);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
            color: white;
        }

        .btn-success:hover {
            background: var(--gradient-secondary);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
            color: white;
        }

        /* Enhanced Notification Badge */
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: var(--gradient-secondary);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);
            animation: pulse-notification 2s infinite;
            border: 2px solid white;
        }

        @keyframes pulse-notification {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4>Coordonnateur de Filière</h4>
            <div class="subtitle">
                @php
                    $filieres = DB::table('coordonnateurs_filieres')
                        ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
                        ->where('coordonnateurs_filieres.user_id', Auth::id())
                        ->pluck('filieres.nom');
                @endphp
                {{ $filieres->implode(', ') ?: 'ENSA Al Hoceima' }}
            </div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-main">
                <div class="nav-item {{ request()->routeIs('coordonnateur.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.dashboard') }}" class="nav-link {{ request()->routeIs('coordonnateur.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de Bord
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('coordonnateur.unites-enseignement*') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.unites-enseignement') }}" class="nav-link {{ request()->routeIs('coordonnateur.unites-enseignement*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        Unités d'Enseignement
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('coordonnateur.vacataires*') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.vacataires') }}" class="nav-link {{ request()->routeIs('coordonnateur.vacataires*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        Vacataires
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('coordonnateur.affectations') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.affectations') }}" class="nav-link {{ request()->routeIs('coordonnateur.affectations') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        Affectations
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('coordonnateur.emplois-du-temps') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.emplois-du-temps') }}" class="nav-link {{ request()->routeIs('coordonnateur.emplois-du-temps') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Emplois du Temps
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('coordonnateur.historique') ? 'active' : '' }}">
                    <a href="{{ route('coordonnateur.historique') }}" class="nav-link {{ request()->routeIs('coordonnateur.historique') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        Historique
                    </a>
                </div>
            </div>

            <div class="nav-footer">
                <div class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <h1 class="page-title">@yield('title', 'Coordonnateur de Filière')</h1>

                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">Coordonnateur de Filière</small>
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

    <!-- Sidebar toggle functionality moved to specific views -->

    @stack('scripts')
</body>
</html>
