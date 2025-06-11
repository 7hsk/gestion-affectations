<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Espace Enseignant</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Stunning Blue Theme for Enseignant */
            --enseignant-primary: #2563eb;
            --enseignant-secondary: #1d4ed8;
            --enseignant-accent: #3b82f6;
            --enseignant-light: #dbeafe;
            --enseignant-dark: #1e3a8a;
            --gradient-primary: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            --gradient-secondary: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            --gradient-accent: linear-gradient(135deg, #60a5fa 0%, #93c5fd 100%);
            --sidebar-gradient: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);
        }

        body {
            overflow-x: hidden;
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 50%, #93c5fd 100%);
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
            box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
        }



        /* Removed heavy sidebar pattern for better performance */

        /* Removed heavy sidebar after animation for better performance */

        /* Removed float animation keyframes for better performance */

        .sidebar-brand {
            padding: 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            overflow: hidden;
            flex-shrink: 0;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-brand:hover {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.1) 100%);
            transform: scale(1.02);
        }

        /* Removed heavy sidebar brand animation for better performance */

        .sidebar-brand h5 {
            margin: 0;
            font-weight: 800;
            font-size: 1.3rem;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .sidebar-brand i {
            background: linear-gradient(45deg, #93c5fd, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 2;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar-brand:hover i {
            transform: scale(1.1) rotate(5deg);
            filter: drop-shadow(0 0 10px rgba(147, 197, 253, 0.5));
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
            background: linear-gradient(135deg, #60a5fa, #93c5fd);
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
            background: linear-gradient(135deg, #93c5fd, #dbeafe);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
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
            box-shadow: 0 5px 20px rgba(37, 99, 235, 0.3);
            transform: translateX(10px);
        }

        .sidebar-link.active {
            background: var(--gradient-primary);
            color: white;
            font-weight: 600;
            box-shadow: 0 5px 20px rgba(37, 99, 235, 0.4);
            transform: translateX(5px);
        }

        .sidebar-link.active::before {
            left: 0;
        }

        /* Removed active before element */

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

        .sidebar-brand i {
            animation: gentleFloat 3s ease-in-out infinite;
        }

        .content-area {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }



        .content-area::before {
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

        .navbar-enseignant {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(30, 58, 138, 0.1);
            border: none;
            position: relative;
            z-index: 10;
        }

        .navbar-enseignant::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
            border-radius: 20px;
            z-index: -1;
        }

        .navbar-brand {
            font-weight: 800;
            background: var(--gradient-secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.5rem;
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
            box-shadow: 0 4px 20px rgba(30, 58, 138, 0.1);
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
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            transition: transform 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .content-area {
                margin-left: 70px;
            }
            .sidebar-brand h5 {
                display: none;
            }
            .sidebar-link span {
                display: none;
            }
            .sidebar-link {
                text-align: center;
                padding: 15px 5px;
            }
            .sidebar-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Enhanced card styles */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(30, 58, 138, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
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
            box-shadow: 0 20px 60px rgba(30, 58, 138, 0.15);
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

        /* Enhanced Button styles */
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
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-primary:hover {
            background: var(--gradient-primary);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.4);
            color: white;
        }

        /* Alert enhancements */
        .alert {
            border: none;
            border-radius: 12px;
            border-left: 4px solid;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            border-left-color: #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(239, 68, 68, 0.1));
            border-left-color: #dc3545;
        }

        .alert-warning {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(253, 126, 20, 0.1));
            border-left-color: #ffc107;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(23, 162, 184, 0.1), rgba(19, 132, 150, 0.1));
            border-left-color: #17a2b8;
        }


    </style>
    @stack('styles')
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-chalkboard-teacher fa-2x mb-2"></i>
        <h5>Espace Enseignant</h5>
    </div>
    <div class="sidebar-nav">
        <div class="nav-main">
            <div class="nav-item {{ request()->routeIs('enseignant.dashboard') ? 'active' : '' }}">
                <a href="{{ route('enseignant.dashboard') }}" class="sidebar-link {{ request()->routeIs('enseignant.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>

            <div class="nav-item {{ request()->routeIs('enseignant.ues.index') ? 'active' : '' }}">
                <a href="{{ route('enseignant.ues.index') }}" class="sidebar-link {{ request()->routeIs('enseignant.ues.index') ? 'active' : '' }}">
                    <i class="fas fa-book"></i>
                    <span>Unités d'Enseignement</span>
                </a>
            </div>

            <div class="nav-item {{ request()->routeIs('enseignant.ue.status') ? 'active' : '' }}">
                <a href="{{ route('enseignant.ue.status') }}" class="sidebar-link {{ request()->routeIs('enseignant.ue.status') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>UE Status</span>
                </a>
            </div>

            <div class="nav-item {{ request()->routeIs('enseignant.notes*') ? 'active' : '' }}">
                <a href="{{ route('enseignant.notes') }}" class="sidebar-link {{ request()->routeIs('enseignant.notes*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Notes</span>
                </a>
            </div>

            <div class="nav-item {{ request()->routeIs('enseignant.emploi-du-temps*') ? 'active' : '' }}">
                <a href="{{ route('enseignant.emploi-du-temps') }}" class="sidebar-link {{ request()->routeIs('enseignant.emploi-du-temps*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Emploi du temps</span>
                </a>
            </div>
        </div>

        <div class="nav-footer">
            <div class="nav-item">
                <a href="#" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
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

<!-- Main Content -->
<div class="content-area" id="contentArea">


    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-enseignant mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">
                @yield('title', 'Espace Enseignant')
            </span>
            <div class="ms-auto">
                <div class="user-info">
                    <div class="text-end me-2">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                </div>
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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreurs de validation:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Content -->
    @yield('content')
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Custom Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
</script>

@stack('scripts')


</body>
</html>
