<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Vacataire') - {{ config('app.name') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Clean Purple Theme for Vacataire */
            --vacataire-primary: #7c3aed;
            --vacataire-secondary: #8b5cf6;
            --vacataire-light: #f3e8ff;
            --vacataire-dark: #581c87;
            --vacataire-hover: #6d28d9;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--vacataire-light);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* Clean Purple Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--vacataire-primary);
            color: white;
            z-index: 1000;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
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

        .sidebar-header {
            padding: 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            overflow: hidden;
            flex-shrink: 0;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 800;
            font-size: 1.3rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            color: white;
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
            background: var(--vacataire-hover);
            transition: left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: -1;
        }

        .nav-link:hover::before {
            left: 0;
        }

        .nav-link:hover {
            color: white;
            transform: translateX(10px);
            box-shadow: 0 5px 20px rgba(124, 58, 237, 0.3);
        }

        .nav-link.active {
            background: var(--vacataire-hover);
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(124, 58, 237, 0.4);
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
            background: linear-gradient(135deg, #7c3aed, #8b5cf6);
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
            background: linear-gradient(135deg, #8b5cf6, #c4b5fd);
        }

        /* Clean Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--vacataire-light);
            position: relative;
            transition: margin-left 0.3s ease;
        }

        .main-content.sidebar-minimized {
            margin-left: 70px;
        }

        /* Clean Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 1.5rem 2rem;
            box-shadow: 0 8px 32px rgba(124, 58, 237, 0.1);
            border-bottom: 1px solid rgba(124, 58, 237, 0.1);
            position: relative;
            z-index: 10;
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
            color: var(--vacataire-primary);
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
            box-shadow: 0 4px 20px rgba(88, 28, 135, 0.1);
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--vacataire-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
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
            box-shadow: 0 10px 40px rgba(88, 28, 135, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(124, 58, 237, 0.1);
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
            box-shadow: 0 20px 60px rgba(88, 28, 135, 0.15);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .card-header {
            background: var(--vacataire-primary);
            color: white;
            border-radius: 20px 20px 0 0 !important;
            border: none;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
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
            background: var(--vacataire-primary);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--vacataire-hover);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
            color: white;
        }

        .btn-warning {
            background: var(--vacataire-secondary);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
            color: white;
            border: none;
        }

        .btn-warning:hover {
            background: var(--vacataire-primary);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
            color: white;
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
            filter: drop-shadow(0 0 10px rgba(124, 58, 237, 0.5));
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
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4>Espace Vacataire</h4>
            <div class="subtitle">ENSA Al Hoceima</div>
        </div>

        <div class="sidebar-nav">
            <div class="nav-main">
                <div class="nav-item {{ request()->routeIs('vacataire.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('vacataire.dashboard') }}" class="nav-link {{ request()->routeIs('vacataire.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tableau de Bord</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('vacataire.unites-enseignement*') ? 'active' : '' }}">
                    <a href="{{ route('vacataire.unites-enseignement') }}" class="nav-link {{ request()->routeIs('vacataire.unites-enseignement*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>
                        <span>Mes UEs</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('vacataire.notes*') ? 'active' : '' }}">
                    <a href="{{ route('vacataire.notes') }}" class="nav-link {{ request()->routeIs('vacataire.notes*') ? 'active' : '' }}">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Gestion Notes</span>
                    </a>
                </div>

                <div class="nav-item {{ request()->routeIs('vacataire.emploi-du-temps*') ? 'active' : '' }}">
                    <a href="{{ route('vacataire.emploi-du-temps') }}" class="nav-link {{ request()->routeIs('vacataire.emploi-du-temps*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Emploi du Temps</span>
                    </a>
                </div>
            </div>

            <div class="nav-footer">
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="page-title">@yield('title', 'Espace Vacataire')</h1>
                <div class="user-info">
                    <div class="text-end me-2">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">Vacataire</small>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
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

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
