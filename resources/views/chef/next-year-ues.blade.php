@extends('layouts.chef')

@section('title', 'UEs Année Prochaine')

@push('styles')
<style>
.next-year-header {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(243, 156, 18, 0.3);
}

.ue-card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.ue-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.ue-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ue-code {
    font-weight: 700;
    font-size: 1.1rem;
}

.ue-body {
    padding: 1.5rem;
}

.ue-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.ue-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.ue-detail-item {
    text-align: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.ue-detail-label {
    font-size: 0.75rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.ue-detail-value {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1rem;
}

.demand-badge {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
}

.no-demand-badge {
    background: #e9ecef;
    color: #6c757d;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 600;
}

.filter-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.search-box {
    position: relative;
}

.search-box .form-control {
    padding-left: 2.5rem;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-box .form-control:focus {
    border-color: #f39c12;
    box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.15);
}

.search-box .search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
}

.btn-back {
    background: linear-gradient(135deg, #6c757d, #495057);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: linear-gradient(135deg, #495057, #343a40);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
    color: white;
}

.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stats-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 5rem;
    margin-bottom: 2rem;
    opacity: 0.3;
}

.empty-state h4 {
    margin-bottom: 1rem;
    color: #495057;
}

.empty-state p {
    font-size: 1.1rem;
    line-height: 1.6;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="next-year-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">
                    <i class="fas fa-calendar-plus me-3"></i>Unités d'Enseignement - Année {{ $nextYear }}
                </h1>
                <p class="mb-0 opacity-75">
                    Gestion des UEs et demandes pour l'année universitaire prochaine
                </p>
            </div>
            <div>
                <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left me-2"></i>Retour Année Courante
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon text-primary">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stats-number text-primary">{{ $unites->total() }}</div>
                <div class="stats-label">Total UEs</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon text-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-number text-warning">{{ $demandes->count() }}</div>
                <div class="stats-label">Demandes en Attente</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number text-success">
                    {{ $unites->filter(function($ue) { return $ue->affectations->where('validee', 'valide')->count() > 0; })->count() }}
                </div>
                <div class="stats-label">UEs Affectées</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-icon text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stats-number text-danger">
                    {{ $unites->filter(function($ue) { return $ue->affectations->where('validee', 'valide')->count() === 0; })->count() }}
                </div>
                <div class="stats-label">UEs Vacantes</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('chef.unites-enseignement.next-year') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Rechercher par nom ou code..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="filiere_id">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" 
                                        {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="semestre">
                            <option value="">Tous les semestres</option>
                            @foreach($semestres as $semestre)
                                <option value="{{ $semestre }}" 
                                        {{ request('semestre') == $semestre ? 'selected' : '' }}>
                                    {{ $semestre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- UEs List -->
    <div class="row">
        @forelse($unites as $ue)
            <div class="col-lg-6 col-xl-4">
                <div class="card ue-card">
                    <div class="ue-header">
                        <div class="ue-code">{{ $ue->code }}</div>
                        <div>
                            @php
                                $demandesForUE = $demandes->where('unite_enseignement_id', $ue->id);
                            @endphp
                            @if($demandesForUE->count() > 0)
                                <span class="demand-badge">{{ $demandesForUE->count() }} demande(s)</span>
                            @else
                                <span class="no-demand-badge">Aucune demande</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ue-body">
                        <div class="ue-title">{{ $ue->nom }}</div>
                        
                        <div class="ue-details">
                            <div class="ue-detail-item">
                                <div class="ue-detail-label">Filière</div>
                                <div class="ue-detail-value">{{ $ue->filiere->nom }}</div>
                            </div>
                            <div class="ue-detail-item">
                                <div class="ue-detail-label">Semestre</div>
                                <div class="ue-detail-value">{{ $ue->semestre }}</div>
                            </div>
                            <div class="ue-detail-item">
                                <div class="ue-detail-label">CM</div>
                                <div class="ue-detail-value">{{ $ue->heures_cm }}h</div>
                            </div>
                            <div class="ue-detail-item">
                                <div class="ue-detail-label">TD</div>
                                <div class="ue-detail-value">{{ $ue->heures_td }}h</div>
                            </div>
                            <div class="ue-detail-item">
                                <div class="ue-detail-label">TP</div>
                                <div class="ue-detail-value">{{ $ue->heures_tp }}h</div>
                            </div>
                            @if($ue->specialite)
                                <div class="ue-detail-item">
                                    <div class="ue-detail-label">Spécialité</div>
                                    <div class="ue-detail-value">{{ $ue->specialite }}</div>
                                </div>
                            @endif
                        </div>

                        @if($demandesForUE->count() > 0)
                            <div class="mt-3">
                                <h6 class="text-muted mb-2">Demandes reçues:</h6>
                                @foreach($demandesForUE as $demande)
                                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                                        <div>
                                            <strong>{{ $demande->user->name }}</strong>
                                            <small class="text-muted d-block">{{ $demande->type_seance }}</small>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success btn-sm" 
                                                    onclick="approuverDemande({{ $demande->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" 
                                                    onclick="rejeterDemande({{ $demande->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>Aucune UE trouvée</h4>
                    <p>Aucune unité d'enseignement ne correspond aux critères de recherche pour l'année {{ $nextYear }}.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($unites->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $unites->links() }}
        </div>
    @endif
</div>

<script>
function approuverDemande(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) {
        fetch(`/chef/demandes/${demandeId}/approuver`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'approbation');
            }
        });
    }
}

function rejeterDemande(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')) {
        fetch(`/chef/demandes/${demandeId}/rejeter`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du rejet');
            }
        });
    }
}
</script>
@endsection
