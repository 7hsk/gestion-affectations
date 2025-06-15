<div class="p-3">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users me-2"></i> Utilisateurs
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.departements.index') }}" class="nav-link text-white {{ request()->routeIs('admin.departements.*') ? 'active' : '' }}">
                <i class="fas fa-building me-2"></i> Départements
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.filieres.index') }}" class="nav-link text-white {{ request()->routeIs('admin.filieres.*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap me-2"></i> Filières
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.ues.index') }}" class="nav-link text-white {{ request()->routeIs('admin.ues.*') ? 'active' : '' }}">
                <i class="fas fa-book me-2"></i> Unités d'Enseignement
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.affectations.index') }}" class="nav-link text-white {{ request()->routeIs('admin.affectations.*') ? 'active' : '' }}">
                <i class="fas fa-tasks me-2"></i> Affectations
            </a>
        </li>
    </ul>
</div> 