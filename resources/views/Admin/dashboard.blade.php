@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Floating Background Shapes -->
<div class="admin-floating-shapes">
    <div class="admin-floating-shape"></div>
    <div class="admin-floating-shape"></div>
    <div class="admin-floating-shape"></div>
    <div class="admin-floating-shape"></div>
</div>

<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg bg-gradient-admin text-white position-relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="admin-avatar me-3">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div>
                                    <h1 class="mb-1 fw-bold">Bienvenue, {{ Auth::user()->name }}! 👋</h1>
                                    <p class="mb-0 opacity-90 fs-5">Système de gestion des affectations - ENSA Al Hoceima</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-white bg-opacity-20 px-3 py-2 me-2">
                                    <i class="fas fa-crown me-1"></i>Administrateur
                                </span>
                                <span class="badge bg-white bg-opacity-20 px-3 py-2">
                                    <i class="fas fa-shield-alt me-1"></i>Accès complet
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="admin-stats-widget">
                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="me-4 text-end">
                                        <div class="admin-time">{{ date('H:i') }}</div>
                                        <div class="admin-date">{{ date('l, F j, Y') }}</div>
                                        <div class="admin-status">
                                            <i class="fas fa-circle text-success me-1"></i>Système opérationnel
                                        </div>
                                    </div>
                                    <div class="admin-icon-container">
                                        <i class="fas fa-cogs fa-3x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Animated Background Elements -->
                <div class="admin-bg-decoration"></div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-users text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold stat-users-total">{{ $stats['users']['total'] }}</h3>
                            <p class="text-muted mb-0">Utilisateurs</p>
                            <small class="text-success">
                                <i class="fas fa-arrow-up me-1"></i>
                                <span class="stat-users-active">{{ $stats['users']['active_today'] }}</span> actifs aujourd'hui
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-building text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold stat-departments-total">{{ $stats['departements']['total'] }}</h3>
                            <p class="text-muted mb-0">Départements</p>
                            <small class="text-info">
                                <i class="fas fa-check-circle me-1"></i>
                                <span class="stat-departments-active">{{ $stats['departements']['with_users'] }}</span> avec utilisateurs
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-book text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold stat-ues-total">{{ $stats['system']['total_ues'] }}</h3>
                            <p class="text-muted mb-0">Unités d'Enseignement</p>
                            <small class="text-muted">
                                <i class="fas fa-check-circle me-1"></i>
                                {{ $stats['system']['total_ues'] - $stats['system']['vacant_ues'] }} assignées
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold stat-pending-affectations">{{ $stats['system']['pending_affectations'] }}</h3>
                            <p class="text-muted mb-0">En attente</p>
                            <small class="text-warning">
                                <i class="fas fa-hourglass-half me-1"></i>
                                Affectations à valider
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 h-100 p-3">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                <strong>Créer utilisateur</strong>
                                <small class="d-block text-muted">Ajouter un nouvel utilisateur</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.departements.create') }}" class="btn btn-outline-success w-100 h-100 p-3">
                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                <strong>Créer département</strong>
                                <small class="d-block text-muted">Ajouter un département</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-info w-100 h-100 p-3">
                                <i class="fas fa-book fa-2x mb-2 d-block"></i>
                                <strong>Gérer UEs</strong>
                                <small class="d-block text-muted">Unités d'enseignement</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <a href="{{ route('admin.ues.create') }}" class="btn btn-outline-warning w-100 h-100 p-3">
                                <i class="fas fa-plus fa-2x mb-2 d-block"></i>
                                <strong>Créer UE</strong>
                                <small class="d-block text-muted">Nouvelle unité d'enseignement</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Distribution Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Répartition des utilisateurs</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Exporter</a></li>
                                <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Imprimer</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Activités récentes</h5>
                </div>
                <div class="card-body p-0">
                    <div class="recent-activities-container">
                        @foreach($recentActivities as $activity)
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle p-2">
                                        <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">{{ $activity['message'] }}</p>
                                    <small class="text-muted">{{ $activity['time'] }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-3 text-center">
                        <a href="{{ route('admin.activities') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-history me-1"></i>Voir toutes les activités
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Admin Dashboard Styles */
.bg-gradient-admin {
    background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
    position: relative;
}

.admin-avatar {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.admin-avatar i {
    font-size: 1.8rem;
    color: #60a5fa;
}

.admin-time {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
}

.admin-date {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
}

.admin-status {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
    margin-top: 4px;
}

.admin-icon-container {
    opacity: 0.3;
    transform: rotate(-15deg);
}

.admin-bg-decoration {
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(96, 165, 250, 0.1) 0%, transparent 70%);
    animation: float-welcome 8s ease-in-out infinite;
}

.admin-bg-decoration::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.05) 0%, transparent 50%, rgba(255,255,255,0.02) 100%);
    pointer-events: none;
}

@keyframes float-welcome {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(180deg); }
}

/* Floating geometric shapes */
.admin-floating-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.admin-floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float-shape 15s ease-in-out infinite;
}

.admin-floating-shape:nth-child(1) {
    top: 20%;
    left: 10%;
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #ef4444, #f87171);
    border-radius: 50%;
    animation-delay: 0s;
}

.admin-floating-shape:nth-child(2) {
    top: 60%;
    right: 15%;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #3b82f6, #60a5fa);
    border-radius: 20px;
    animation-delay: 2s;
}

.admin-floating-shape:nth-child(3) {
    bottom: 30%;
    left: 20%;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #10b981, #34d399);
    transform: rotate(45deg);
    animation-delay: 4s;
}

.admin-floating-shape:nth-child(4) {
    top: 40%;
    left: 60%;
    width: 50px;
    height: 50px;
    background: linear-gradient(45deg, #f59e0b, #fbbf24);
    border-radius: 50%;
    animation-delay: 6s;
}

@keyframes float-shape {
    0%, 100% {
        transform: translateY(0px) rotate(0deg) scale(1);
        opacity: 0.1;
    }
    25% {
        transform: translateY(-20px) rotate(90deg) scale(1.1);
        opacity: 0.2;
    }
    50% {
        transform: translateY(-40px) rotate(180deg) scale(0.9);
        opacity: 0.15;
    }
    75% {
        transform: translateY(-20px) rotate(270deg) scale(1.05);
        opacity: 0.25;
    }
}

.admin-stat-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(226, 232, 240, 0.3);
}

.admin-stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.stat-icon-container {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.stat-icon-container::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transform: rotate(45deg);
    transition: all 0.6s;
}

.admin-stat-card:hover .stat-icon-container::before {
    animation: shimmer 1.5s ease-in-out;
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #059669, #10b981);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #0891b2, #06b6d4);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #d97706, #f59e0b);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
}

.stat-trend {
    font-size: 0.8rem;
    font-weight: 600;
}

.stat-decoration {
    position: absolute;
    width: 100px;
    height: 100px;
    border-radius: 50%;
    opacity: 0.05;
    top: -20px;
    right: -20px;
}

.stat-decoration-1 { background: #2563eb; }
.stat-decoration-2 { background: #059669; }
.stat-decoration-3 { background: #0891b2; }
.stat-decoration-4 { background: #d97706; }

.card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-outline-primary:hover {
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
}

.btn-outline-success:hover {
    box-shadow: 0 8px 20px rgba(5, 150, 105, 0.3);
}

.btn-outline-info:hover {
    box-shadow: 0 8px 20px rgba(8, 145, 178, 0.3);
}

.btn-outline-warning:hover {
    box-shadow: 0 8px 20px rgba(217, 119, 6, 0.3);
}

/* Enhanced table styles */
.table {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.table th {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.8rem;
    color: #1e293b;
    border: none;
    padding: 16px;
}

.table td {
    border-color: rgba(226, 232, 240, 0.5);
    vertical-align: middle;
    padding: 16px;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background: rgba(59, 130, 246, 0.05);
    transform: scale(1.01);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Enhanced badges */
.badge {
    border-radius: 8px;
    font-weight: 600;
    padding: 8px 12px;
    font-size: 0.75rem;
}

/* Responsive enhancements */
@media (max-width: 768px) {
    .stat-number {
        font-size: 2rem;
    }

    .admin-time {
        font-size: 1.2rem;
    }

    .admin-avatar {
        width: 50px;
        height: 50px;
    }

    .admin-avatar i {
        font-size: 1.5rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Distribution Chart
    const ctx = document.getElementById('userDistributionChart').getContext('2d');
    const userRoles = @json($stats['users']['by_role']);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Administrateurs', 'Chefs de département', 'Coordonnateurs', 'Enseignants', 'Vacataires'],
            datasets: [{
                data: [
                    userRoles.admin,
                    userRoles.chef,
                    userRoles.coordonnateur,
                    userRoles.enseignant,
                    userRoles.vacataire
                ],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#f5576c',
                    '#4facfe'
                ],
                borderWidth: 0,
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Real-time dashboard updates
    function updateDashboardStats() {
        fetch('{{ route("admin.api.dashboard-stats") }}')
            .then(response => response.json())
            .then(data => {
                // Update user statistics
                document.querySelector('.stat-users-total').textContent = data.users.total;
                document.querySelector('.stat-users-active').textContent = data.users.active_today;
                document.querySelector('.stat-departments-total').textContent = data.departements.total;
                document.querySelector('.stat-departments-active').textContent = data.departements.with_users;
                document.querySelector('.stat-ues-total').textContent = data.system.total_ues;
                document.querySelector('.stat-pending-affectations').textContent = data.system.pending_affectations;

                // Update chart data
                if (window.userChart) {
                    window.userChart.data.datasets[0].data = [
                        data.users.by_role.admin,
                        data.users.by_role.chef,
                        data.users.by_role.coordonnateur,
                        data.users.by_role.enseignant,
                        data.users.by_role.vacataire
                    ];
                    window.userChart.update();
                }
            })
            .catch(error => console.error('Error updating dashboard stats:', error));
    }

    function updateRecentActivities() {
        fetch('{{ route("admin.api.recent-activities") }}')
            .then(response => response.json())
            .then(activities => {
                const container = document.querySelector('.recent-activities-container');
                if (container && activities.length > 0) {
                    container.innerHTML = '';
                    activities.forEach(activity => {
                        const activityHtml = `
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="bg-${activity.color} bg-opacity-10 rounded-circle p-2">
                                        <i class="${activity.icon} text-${activity.color}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1">${activity.message}</p>
                                    <small class="text-muted">${activity.time}</small>
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', activityHtml);
                    });
                }
            })
            .catch(error => console.error('Error updating activities:', error));
    }

    // Chart already created above, no need to duplicate

    // Update dashboard every 30 seconds
    setInterval(updateDashboardStats, 30000);

    // Update activities every 60 seconds
    setInterval(updateRecentActivities, 60000);

    // Add loading states
    function showLoading(element) {
        element.classList.add('loading');
    }

    function hideLoading(element) {
        element.classList.remove('loading');
    }
});
</script>
@endsection
