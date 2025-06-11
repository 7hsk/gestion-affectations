@extends('layouts.enseignant')

@section('title', 'Statut de mes UEs')

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
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
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

.ue-card {
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.ue-card.cm {
    border-left-color: #667eea;
}

.ue-card.td {
    border-left-color: #28a745;
}

.ue-card.tp {
    border-left-color: #ffc107;
}

.ue-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.type-badge.cm {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.type-badge.td {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.type-badge.tp {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.stats-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.progress {
    height: 8px;
    border-radius: 4px;
}

/* Badge overflow protection */
.badge {
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}

.type-badge {
    white-space: nowrap;
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-1">
                                <i class="fas fa-tasks me-2"></i>
                                Statut de mes UEs
                            </h2>
                            <p class="mb-0 opacity-75">Vue d'ensemble de vos unités d'enseignement assignées pour {{ $currentYear }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['total_ues'] }}</h4>
                                    <small class="text-white-50">UEs assignées</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['total_hours'] }}</h4>
                                    <small class="text-white-50">Heures totales</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['by_type']['CM']['count'] + $stats['by_type']['TD']['count'] + $stats['by_type']['TP']['count'] }}</h4>
                                    <small class="text-white-50">Affectations</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['by_semester']->count() }}</h4>
                                    <small class="text-white-50">Semestres</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-chalkboard-teacher text-primary fa-2x"></i>
                    </div>
                    <h4 class="text-primary">{{ $stats['by_type']['CM']['count'] }}</h4>
                    <p class="mb-1">Cours Magistraux</p>
                    <small class="text-muted">{{ $stats['by_type']['CM']['hours'] }} heures</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-users text-success fa-2x"></i>
                    </div>
                    <h4 class="text-success">{{ $stats['by_type']['TD']['count'] }}</h4>
                    <p class="mb-1">Travaux Dirigés</p>
                    <small class="text-muted">{{ $stats['by_type']['TD']['hours'] }} heures</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-laptop-code text-warning fa-2x"></i>
                    </div>
                    <h4 class="text-warning">{{ $stats['by_type']['TP']['count'] }}</h4>
                    <p class="mb-1">Travaux Pratiques</p>
                    <small class="text-muted">{{ $stats['by_type']['TP']['hours'] }} heures</small>
                </div>
            </div>
        </div>
    </div>

    <!-- UE Cards by Type -->
    @if($assignedUEs->count() > 0)
        <!-- CM Section -->
        @if($uesByType['CM']->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">
                        <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                        Cours Magistraux ({{ $uesByType['CM']->count() }})
                    </h5>
                    <div class="row">
                        @foreach($uesByType['CM'] as $affectation)
                            @php $ue = $affectation->uniteEnseignement; @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card ue-card cm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge type-badge cm text-white" title="Cours Magistral">CM</span>
                                            <span class="badge bg-info" title="Semestre {{ $ue->semestre }}">{{ $ue->semestre }}</span>
                                        </div>
                                        <h6 class="card-title text-primary mb-1">{{ $ue->code }}</h6>
                                        <p class="card-text mb-2">{{ $ue->nom }}</p>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                <strong>Filière:</strong> {{ $ue->filiere->nom ?? 'Non assignée' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-building me-1"></i>
                                                <strong>Département:</strong> {{ $ue->departement->nom ?? 'Non assigné' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-3">
                                            <div class="col-6">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $ue->heures_cm }}h CM
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-layer-group me-1"></i>
                                                {{ $ue->niveau }}
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Assigné le {{ $affectation->created_at->format('d/m/Y') }}
                                            </small>
                                            @if($ue->responsable_id == Auth::id())
                                                <span class="badge bg-warning">Responsable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- TD Section -->
        @if($uesByType['TD']->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">
                        <i class="fas fa-users text-success me-2"></i>
                        Travaux Dirigés ({{ $uesByType['TD']->count() }})
                    </h5>
                    <div class="row">
                        @foreach($uesByType['TD'] as $affectation)
                            @php $ue = $affectation->uniteEnseignement; @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card ue-card td">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge type-badge td text-white" title="Travaux Dirigés">TD</span>
                                            <span class="badge bg-info" title="Semestre {{ $ue->semestre }}">{{ $ue->semestre }}</span>
                                        </div>
                                        <h6 class="card-title text-success mb-1">{{ $ue->code }}</h6>
                                        <p class="card-text mb-2">{{ $ue->nom }}</p>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                <strong>Filière:</strong> {{ $ue->filiere->nom ?? 'Non assignée' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-building me-1"></i>
                                                <strong>Département:</strong> {{ $ue->departement->nom ?? 'Non assigné' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-3">
                                            <div class="col-6">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $ue->heures_td }}h TD
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $ue->groupes_td }} groupes
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Assigné le {{ $affectation->created_at->format('d/m/Y') }}
                                            </small>
                                            @if($ue->responsable_id == Auth::id())
                                                <span class="badge bg-warning">Responsable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- TP Section -->
        @if($uesByType['TP']->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3">
                        <i class="fas fa-laptop-code text-warning me-2"></i>
                        Travaux Pratiques ({{ $uesByType['TP']->count() }})
                    </h5>
                    <div class="row">
                        @foreach($uesByType['TP'] as $affectation)
                            @php $ue = $affectation->uniteEnseignement; @endphp
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card ue-card tp">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge type-badge tp text-white" title="Travaux Pratiques">TP</span>
                                            <span class="badge bg-info" title="Semestre {{ $ue->semestre }}">{{ $ue->semestre }}</span>
                                        </div>
                                        <h6 class="card-title text-warning mb-1">{{ $ue->code }}</h6>
                                        <p class="card-text mb-2">{{ $ue->nom }}</p>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                <strong>Filière:</strong> {{ $ue->filiere->nom ?? 'Non assignée' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-2">
                                            <div class="col-12">
                                                <i class="fas fa-building me-1"></i>
                                                <strong>Département:</strong> {{ $ue->departement->nom ?? 'Non assigné' }}
                                            </div>
                                        </div>

                                        <div class="row text-muted small mb-3">
                                            <div class="col-6">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $ue->heures_tp }}h TP
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-laptop me-1"></i>
                                                {{ $ue->groupes_tp }} groupes
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Assigné le {{ $affectation->created_at->format('d/m/Y') }}
                                            </small>
                                            @if($ue->responsable_id == Auth::id())
                                                <span class="badge bg-warning">Responsable</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- No UEs Assigned -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">Aucune UE assignée</h4>
                        <p class="text-muted mb-4">Vous n'avez actuellement aucune unité d'enseignement assignée pour l'année {{ $currentYear }}.</p>
                        <a href="{{ route('enseignant.ues.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Faire une demande d'affectation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
