<div class="bg-dark text-white" id="sidebar-wrapper" style="min-height: 100vh; width: 250px;">
    <div class="sidebar-heading text-center py-4">
        <h4>School Admin</h4>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-tachometer-alt me-2"></i> Dashboard
        </a>

        <!-- Users Management -->
        <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-users me-2"></i> Users
        </a>

        <!-- Departments Management -->
        <a href="{{ route('admin.departements.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-building me-2"></i> Departments
        </a>

       <!--
        <a href="{{ route('admin.filieres.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-graduation-cap me-2"></i> Programs
        </a>


        <a href="{{ route('admin.unites-enseignement.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-book me-2"></i> Course Units
        </a>


        <a href="{{ route('admin.affectations.index') }}" class="list-group-item list-group-item-action bg-dark text-white">
            <i class="fas fa-tasks me-2"></i> Assignments-->
        </a>
    </div>
</div>
