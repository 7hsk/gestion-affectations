@extends('layouts.enseignant')

@section('title', 'Statut des UEs')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-chart-line me-2"></i>Statut des Unités d'Enseignement
                            </h1>
                            <p class="mb-0 opacity-75">Vue d'ensemble de vos affectations actuelles et futures</p>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="text-white mb-0">{{ $stats['current_year_count'] }}</h4>
                                    <small class="text-white-50">UEs Actuelles</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-white mb-0">{{ $stats['next_year_count'] }}</h4>
                                    <small class="text-white-50">UEs Futures</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Year Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check me-2"></i>
                            Année Actuelle - {{ $currentYear }}
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('enseignant.emploi-du-temps') }}" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-calendar-alt me-1"></i>Emploi du Temps
                            </a>
                            <a href="{{ route('enseignant.notes') }}" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-clipboard-list me-1"></i>Gestion Notes
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($currentYearUEs->count() > 0)
                        <div class="row">
                            @foreach($currentYearUEs as $affectation)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-success" title="{{ $affectation->type_seance }}">{{ $affectation->type_seance }}</span>
                                                <small class="text-muted">{{ $affectation->uniteEnseignement->total_hours }}h</small>
                                            </div>
                                            <h6 class="card-title text-success">{{ $affectation->uniteEnseignement->code }}</h6>
                                            <p class="card-text small mb-2">{{ Str::limit($affectation->uniteEnseignement->nom, 50) }}</p>
                                            <div class="text-muted small">
                                                <div><i class="fas fa-graduation-cap me-1"></i>{{ $affectation->uniteEnseignement->filiere->nom ?? 'Non assignée' }}</div>
                                                <div><i class="fas fa-building me-1"></i>{{ $affectation->uniteEnseignement->departement->nom ?? 'Non assigné' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-success mb-2">
                                        <i class="fas fa-chart-bar me-1"></i>Statistiques Année Actuelle
                                    </h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>{{ $stats['current_year_count'] }}</strong> UEs assignées
                                        </div>
                                        <div class="col-6">
                                            <strong>{{ $stats['current_year_hours'] }}</strong> heures totales
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucune UE assignée pour l'année actuelle</h6>
                            <p class="text-muted">Vos affectations pour {{ $currentYear }} apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Next Year Section -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Année Prochaine - {{ $nextYear }}
                        </h5>
                        <a href="{{ route('enseignant.ues.index') }}" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-plus me-1"></i>Faire des Demandes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($nextYearUEs->count() > 0)
                        <div class="row">
                            @foreach($nextYearUEs as $affectation)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-primary h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-primary" title="{{ $affectation->type_seance }}">{{ $affectation->type_seance }}</span>
                                                <small class="text-muted">{{ $affectation->uniteEnseignement->total_hours }}h</small>
                                            </div>
                                            <h6 class="card-title text-primary">{{ $affectation->uniteEnseignement->code }}</h6>
                                            <p class="card-text small mb-2">{{ Str::limit($affectation->uniteEnseignement->nom, 50) }}</p>
                                            <div class="text-muted small">
                                                <div><i class="fas fa-graduation-cap me-1"></i>{{ $affectation->uniteEnseignement->filiere->nom ?? 'Non assignée' }}</div>
                                                <div><i class="fas fa-building me-1"></i>{{ $affectation->uniteEnseignement->departement->nom ?? 'Non assigné' }}</div>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="fas fa-check me-1"></i>Approuvée le {{ $affectation->date_validation ? $affectation->date_validation->format('d/m/Y') : $affectation->updated_at->format('d/m/Y') }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-primary mb-2">
                                        <i class="fas fa-chart-bar me-1"></i>Statistiques Année Prochaine
                                    </h6>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>{{ $stats['next_year_count'] }}</strong> UEs approuvées
                                        </div>
                                        <div class="col-6">
                                            <strong>{{ $stats['next_year_hours'] }}</strong> heures prévues
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucune UE approuvée pour l'année prochaine</h6>
                            <p class="text-muted">Vos demandes approuvées pour {{ $nextYear }} apparaîtront ici</p>
                            <a href="{{ route('enseignant.ues.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Faire des Demandes d'Affectation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced styling */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.badge {
    font-weight: 500;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}
</style>
@endsection
