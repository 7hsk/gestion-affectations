<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Système de Gestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }
        .sidebar {
            background-color: #1a237e;
            min-height: 100vh;
            color: white;
            position: fixed; /* Make sidebar fixed */
            height: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .sidebar-link {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: background-color 0.3s;
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background-color: #303f9f;
        }
        .sidebar-link i {
            margin-right: 10px;
        }
        .content-area {
            padding: 20px;
            margin-left: 250px; /* Match initial sidebar width */
            transition: margin-left 0.3s ease;
            width: calc(100% - 250px); /* Adjust width to account for sidebar */
        }
        .sidebar-brand {
            padding: 10px;
            text-align: center;
            font-size: 1.1rem;
            white-space: nowrap;
            overflow: hidden;
        }

        /* Default (larger) sidebar styles */
        .sidebar {
            width: 250px;
        }
        .sidebar-brand h5 {
            display: block;
        }
        .sidebar-link span {
            display: inline;
        }

        /* Small sidebar styles for screens < 775px */
        @media (max-width: 775px) {
            .sidebar {
                width: 70px;
            }
            .content-area {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            .sidebar-brand h5 {
                display: none;
            }
            .sidebar-link {
                text-align: center;
                padding: 15px 5px;
            }
            .sidebar-link span {
                display: none;
            }
            .sidebar-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <h5>Espace Enseignant</h5>
    </div>
    <hr class="bg-light m-0">
    <nav>
        <a href="{{ route('enseignant.dashboard') }}" class="sidebar-link {{ request()->routeIs('enseignant.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('enseignant.unites') }}" class="sidebar-link {{ request()->routeIs('enseignant.unites') ? 'active' : '' }}">
            <i class="fas fa-book"></i>
            <span>Unités d'enseignement</span>
        </a>

        <a href="{{ route('enseignant.notes') }}" class="sidebar-link {{ request()->routeIs('enseignant.notes') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap"></i>
            <span>Notes</span>
        </a>

        <a href="{{ route('enseignant.emploi-du-temps') }}" class="sidebar-link {{ request()->routeIs('enseignant.emploi-du-temps') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i>
            <span>Emploi du temps</span>
        </a>

        <hr class="bg-light m-0">
        <a href="{{ route('logout') }}" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </nav>
</div>

<!-- Main Content -->
<div class="content-area">
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
                <span class="navbar-brand">
                    @yield('title', 'Tableau de bord Enseignant')
                </span>
            <div class="ms-auto d-flex align-items-center">
                <span class="me-3">{{ Auth::user()->name }}</span>
                <i class="fas fa-user-circle fa-2x"></i>
            </div>
        </div>
    </nav>

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
