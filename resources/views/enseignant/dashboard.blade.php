@extends('layouts.enseignant')

@section('title', 'Tableau de bord Enseignant')

@push('styles')
<style>
/* Enhanced gradient styling to match UE view */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.card {
    border-radius: 12px;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
}

/* Welcome section with floating animation */
.bg-gradient-primary {
    position: relative;
    overflow: hidden;
}

.bg-gradient-primary::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(96, 165, 250, 0.1) 0%, transparent 70%);
    animation: float-welcome 8s ease-in-out infinite;
}

.bg-gradient-primary::after {
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
.enseignant-floating-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.enseignant-floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float-shape 15s ease-in-out infinite;
}

.enseignant-floating-shape:nth-child(1) {
    top: 20%;
    left: 10%;
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #60a5fa, #93c5fd);
    border-radius: 50%;
    animation-delay: 0s;
}

.enseignant-floating-shape:nth-child(2) {
    top: 60%;
    right: 15%;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 20px;
    animation-delay: 2s;
}

.enseignant-floating-shape:nth-child(3) {
    bottom: 30%;
    left: 20%;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #3b82f6, #1d4ed8);
    transform: rotate(45deg);
    animation-delay: 4s;
}

.enseignant-floating-shape:nth-child(4) {
    top: 40%;
    left: 60%;
    width: 50px;
    height: 50px;
    background: linear-gradient(45deg, #06b6d4, #0891b2);
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
</style>
@endpush

@section('content')
<!-- Floating Background Shapes -->
<div class="enseignant-floating-shapes">
    <div class="enseignant-floating-shape"></div>
    <div class="enseignant-floating-shape"></div>
    <div class="enseignant-floating-shape"></div>
    <div class="enseignant-floating-shape"></div>
</div>

<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1">Bienvenue, {{ $teacher->name }}! 👋</h2>
                            <p class="mb-0 opacity-75">{{ $departement->name ?? 'Aucun département' }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="me-3">
                                    <small class="d-block opacity-75">{{ date('l, F j, Y') }}</small>
                                    <small class="d-block opacity-75">{{ date('H:i') }}</small>
                                </div>
                                <i class="fas fa-user-circle fa-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <i class="fas fa-book text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ $stats['total_ues'] }}</h3>
                            <p class="text-muted mb-0">Unités d'enseignement</p>
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
                                <i class="fas fa-clock text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ $stats['total_hours'] }}h</h3>
                            <p class="text-muted mb-0">Charge horaire totale</p>
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
                                <i class="fas fa-users text-info fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ $stats['students_count'] }}</h3>
                            <p class="text-muted mb-0">Étudiants</p>
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
                                <i class="fas fa-hourglass-half text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ $stats['pending_assignments'] }}</h3>
                            <p class="text-muted mb-0">Demandes en attente</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Workload Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Répartition de la charge horaire</h5>
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
                    <canvas id="workloadChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Notifications -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('enseignant.notes') }}" class="btn btn-primary">
                            <i class="fas fa-graduation-cap me-2"></i>Gérer les notes
                        </a>
                        <a href="{{ route('enseignant.emploi-du-temps') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Emploi du temps
                        </a>
                        <a href="{{ route('enseignant.ues.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-book me-2"></i>Mes UEs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Notifications récentes</h5>
                </div>
                <div class="card-body p-0">
                    @if($notifications->isNotEmpty())
                        @foreach($notifications as $notification)
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                        <i class="fas fa-bell text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                    <p class="text-muted small mb-0">{{ $notification->message }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-bell-slash text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucune notification</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Teaching Load & Schedule Overview -->
    <div class="row">
        <!-- Upcoming Schedule -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Prochains cours</h5>
                        <a href="{{ route('enseignant.emploi-du-temps') }}" class="btn btn-sm btn-outline-primary">
                            Voir tout
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingSchedule->isNotEmpty())
                        @foreach($upcomingSchedule as $schedule)
                            <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary text-white rounded p-2 text-center" style="min-width: 60px;">
                                        <div class="fw-bold">{{ ucfirst(substr($schedule->jour_semaine, 0, 3)) }}</div>
                                        <small>{{ date('d/m', strtotime('next ' . $schedule->jour_semaine)) }}</small>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ $schedule->uniteEnseignement->code ?? 'N/A' }}</h6>
                                    <p class="text-muted small mb-1">{{ $schedule->uniteEnseignement->nom ?? 'N/A' }}</p>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ substr($schedule->heure_debut, 0, 5) }} - {{ substr($schedule->heure_fin, 0, 5) }}
                                        @if($schedule->salle)
                                            <i class="fas fa-map-marker-alt ms-3 me-1"></i>
                                            {{ $schedule->salle }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4">
                            <i class="fas fa-calendar-times text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucun cours programmé</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Specialties & Profile -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0">Profil enseignant</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <h6 class="text-muted mb-2">Spécialités</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($specialites as $specialite)
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                        {{ trim($specialite) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-primary mb-1">{{ $stats['total_ues'] }}</h4>
                                <small class="text-muted">UEs assignées</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <h4 class="text-success mb-1">{{ $stats['total_hours'] }}h</h4>
                                <small class="text-muted">Charge totale</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Progression charge horaire</span>
                            <span class="small text-muted">{{ round(($stats['total_hours'] / 240) * 100, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-gradient" role="progressbar"
                                 style="width: {{ min(($stats['total_hours'] / 240) * 100, 100) }}%"></div>
                        </div>
                        <small class="text-muted">Objectif: 240h/an</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Workload Chart
    const ctx = document.getElementById('workloadChart').getContext('2d');
    const workloadData = @json($workloadData);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Cours Magistraux (CM)', 'Travaux Dirigés (TD)', 'Travaux Pratiques (TP)'],
            datasets: [{
                data: [workloadData.CM, workloadData.TD, workloadData.TP],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb'
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
});
</script>
@endsection