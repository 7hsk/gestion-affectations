@extends('layouts.enseignant')

@section('title', 'Mes Unités d\'Enseignement')

@section('content')
<div class="container-fluid">
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Succès!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Erreur!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-1">
                                <i class="fas fa-book me-2"></i>
                                Mes Unités d'Enseignement
                            </h2>
                            <p class="mb-0 opacity-75">Gérez vos affectations et demandes d'UE pour {{ $currentYear }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['approved'] }}</h4>
                                    <small class="text-white-50">Approuvées</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['pending'] }}</h4>
                                    <small class="text-white-50">En attente</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['rejected'] }}</h4>
                                    <small class="text-white-50">Refusées</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['total_hours_approved'] }}</h4>
                                    <small class="text-white-50">Heures totales</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Side: Approved Affectations -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        UEs Approuvées - {{ $currentYear }} ({{ $stats['approved'] }})
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    @if($approvedAffectations->count() > 0)
                        @foreach($approvedAffectations as $affectation)
                            <div class="border-bottom p-3 hover-bg">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-success me-2">{{ $affectation->type_seance }}</span>
                                            <strong class="text-primary">{{ $affectation->uniteEnseignement->code }}</strong>
                                        </div>
                                        <h6 class="mb-1">{{ $affectation->uniteEnseignement->nom }}</h6>
                                        <div class="row text-muted small">
                                            <div class="col-6">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                {{ $affectation->uniteEnseignement->filiere->nom ?? 'Non assignée' }}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $affectation->uniteEnseignement->departement->nom ?? 'Non assigné' }}
                                            </div>
                                        </div>
                                        <div class="row text-muted small mt-1">
                                            <div class="col-6">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $affectation->uniteEnseignement->semestre }}
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $affectation->uniteEnseignement->total_hours }}h
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">
                                            Approuvé le<br>
                                            {{ $affectation->updated_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucune UE approuvée</h6>
                            <p class="text-muted">Vos demandes approuvées apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Side: Pending and Rejected Affectations -->
        <div class="col-lg-6">
            <!-- Pending Affectations -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-half me-2"></i>
                        Demandes en Attente - {{ $nextYear }} ({{ $stats['pending'] }})
                    </h5>
                </div>
                <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                    @if($pendingAffectations->count() > 0)
                        @foreach($pendingAffectations as $affectation)
                            <div class="border-bottom p-3 hover-bg">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-warning me-2">{{ $affectation->type_seance }}</span>
                                            <strong class="text-primary">{{ $affectation->uniteEnseignement->code }}</strong>
                                        </div>
                                        <h6 class="mb-1">{{ $affectation->uniteEnseignement->nom }}</h6>
                                        <div class="text-muted small">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            {{ $affectation->uniteEnseignement->filiere->nom ?? 'Non assignée' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            Demandé le {{ $affectation->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="cancelRequest({{ $affectation->id }})"
                                                title="Annuler la demande">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-hourglass-half fa-2x text-muted mb-2"></i>
                            <h6 class="text-muted">Aucune demande en attente</h6>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Rejected Affectations -->
            @if($rejectedAffectations->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-times-circle me-2"></i>
                            Historique - {{ $nextYear }} ({{ $stats['rejected'] }})
                        </h5>
                    </div>
                    <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                        @foreach($rejectedAffectations as $affectation)
                            <div class="border-bottom p-3">
                                <div class="d-flex align-items-center mb-2">
                                    @if($affectation->validee === 'rejete')
                                        <span class="badge bg-danger me-2">{{ $affectation->type_seance }}</span>
                                    @elseif($affectation->validee === 'annule')
                                        <span class="badge bg-warning me-2">{{ $affectation->type_seance }}</span>
                                    @else
                                        <span class="badge bg-secondary me-2">{{ $affectation->type_seance }}</span>
                                    @endif
                                    <strong class="text-primary">{{ $affectation->uniteEnseignement->code }}</strong>
                                </div>
                                <h6 class="mb-1">{{ $affectation->uniteEnseignement->nom }}</h6>
                                <div class="text-muted small">
                                    @if($affectation->validee === 'rejete')
                                        <i class="fas fa-times me-1 text-danger"></i>
                                        <span class="text-danger">Rejetée</span> le {{ $affectation->date_validation ? $affectation->date_validation->format('d/m/Y') : $affectation->updated_at->format('d/m/Y') }}
                                    @elseif($affectation->validee === 'annule')
                                        <i class="fas fa-ban me-1 text-warning"></i>
                                        <span class="text-warning">Annulée</span> le {{ $affectation->date_validation ? $affectation->date_validation->format('d/m/Y') : $affectation->updated_at->format('d/m/Y') }}
                                    @endif
                                </div>
                                @if($affectation->commentaire)
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-comment me-1"></i>
                                        {{ $affectation->commentaire }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Available UEs Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            UEs Disponibles - {{ $nextYear }} ({{ $stats['available_ues'] }})
                        </h5>
                        <small>Cliquez sur une UE pour faire une demande d'affectation pour l'année prochaine</small>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-body border-bottom bg-light">
                    <form method="GET" action="{{ route('enseignant.ues.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="filiere_filter" class="form-label">Filtrer par Filière</label>
                            <select class="form-select" id="filiere_filter" name="filiere_filter">
                                <option value="">Toutes les filières</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ request('filiere_filter') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="semestre_filter" class="form-label">Filtrer par Semestre</label>
                            <select class="form-select" id="semestre_filter" name="semestre_filter">
                                <option value="">Tous les semestres</option>
                                @foreach($semestres as $semestre)
                                    <option value="{{ $semestre }}" {{ request('semestre_filter') == $semestre ? 'selected' : '' }}>
                                        {{ $semestre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('enseignant.ues.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>

                @if($availableUEs->count() > 0)
                    <div class="card-body">
                        <div class="row">
                            @foreach($availableUEs as $ue)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border hover-shadow cursor-pointer" onclick="showUEDetails({{ $ue->id }})">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-info">{{ $ue->semestre }}</span>
                                                <span class="badge bg-secondary">{{ $ue->niveau }}</span>
                                            </div>
                                            <h6 class="card-title text-primary mb-1">{{ $ue->code }}</h6>
                                            <p class="card-text small mb-2">{{ Str::limit($ue->nom, 40) }}</p>
                                            <div class="text-muted small">
                                                <div><i class="fas fa-graduation-cap me-1"></i>{{ $ue->filiere->nom ?? 'Non assignée' }}</div>
                                                <div><i class="fas fa-clock me-1"></i>{{ $ue->total_hours }}h total</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="card-body text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucune UE disponible</h6>
                        <p class="text-muted">Aucune UE ne correspond aux critères de filtrage sélectionnés.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- UE Details Modal -->
<div class="modal fade" id="ueDetailsModal" tabindex="-1" aria-labelledby="ueDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="ueDetailsModalLabel">
                    <i class="fas fa-book me-2"></i>Détails de l'UE
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="ueDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Fermer
                </button>
                <button type="button" class="btn btn-primary" onclick="showRequestForm()">
                    <i class="fas fa-paper-plane me-1"></i>Faire une demande
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Request Affectation Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="requestModalLabel">
                    <i class="fas fa-paper-plane me-2"></i>Demande d'Affectation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('enseignant.ues.request') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="request_ue_id" name="ue_id">

                    <div class="mb-3">
                        <label class="form-label">UE Sélectionnée</label>
                        <div id="selected_ue_info" class="p-3 bg-light rounded">
                            <!-- UE info will be displayed here -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Types d'Enseignement <span class="text-danger">*</span></label>
                        <small class="text-muted d-block mb-2">Sélectionnez un ou plusieurs types d'enseignement</small>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="checkbox" name="type_seance[]" value="CM" id="type_cm">
                                    <label class="form-check-label w-100" for="type_cm">
                                        <div class="card h-100 border-2">
                                            <div class="card-body text-center p-3">
                                                <i class="fas fa-chalkboard-teacher fa-2x text-primary mb-2"></i>
                                                <h6 class="card-title mb-1">Cours Magistral</h6>
                                                <small class="text-muted">CM</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="checkbox" name="type_seance[]" value="TD" id="type_td">
                                    <label class="form-check-label w-100" for="type_td">
                                        <div class="card h-100 border-2">
                                            <div class="card-body text-center p-3">
                                                <i class="fas fa-users fa-2x text-success mb-2"></i>
                                                <h6 class="card-title mb-1">Travaux Dirigés</h6>
                                                <small class="text-muted">TD</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-check-card">
                                    <input class="form-check-input" type="checkbox" name="type_seance[]" value="TP" id="type_tp">
                                    <label class="form-check-label w-100" for="type_tp">
                                        <div class="card h-100 border-2">
                                            <div class="card-body text-center p-3">
                                                <i class="fas fa-laptop-code fa-2x text-warning mb-2"></i>
                                                <h6 class="card-title mb-1">Travaux Pratiques</h6>
                                                <small class="text-muted">TP</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="type_seance_error" style="display: none;">
                            Veuillez sélectionner au moins un type d'enseignement.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Message (optionnel)</label>
                        <textarea class="form-control" id="message" name="message" rows="3"
                                  placeholder="Ajoutez un message pour justifier votre demande..."></textarea>
                        <small class="text-muted">Maximum 500 caractères</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>Envoyer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Request Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Annuler la demande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir annuler cette demande d'affectation ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    Cette action est irréversible. Vous devrez refaire une nouvelle demande si nécessaire.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Non, garder
                </button>
                <form id="cancelForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Oui, annuler
                    </button>
                </form>
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

.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.hover-bg:hover {
    background-color: rgba(0,123,255,0.05);
}

.cursor-pointer {
    cursor: pointer;
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

.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header.bg-info,
.modal-header.bg-primary,
.modal-header.bg-danger {
    border-bottom: none;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Custom scrollbar */
.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.card-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Checkbox card styling */
.form-check-card {
    position: relative;
}

.form-check-card .form-check-input {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 10;
    transform: scale(1.2);
}

.form-check-card .card {
    cursor: pointer;
    transition: all 0.3s ease;
    border-color: #dee2e6;
}

.form-check-card .card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    transform: translateY(-2px);
}

.form-check-card .form-check-input:checked + .form-check-label .card {
    border-color: #007bff;
    background-color: rgba(0, 123, 255, 0.05);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
}

.form-check-card .form-check-input:checked + .form-check-label .card .text-primary {
    color: #007bff !important;
}

.form-check-card .form-check-input:checked + .form-check-label .card .text-success {
    color: #28a745 !important;
}

.form-check-card .form-check-input:checked + .form-check-label .card .text-warning {
    color: #ffc107 !important;
}
</style>

<script>
let currentUEId = null;

// Show UE details modal
function showUEDetails(ueId) {
    currentUEId = ueId;

    fetch(`/enseignant/ues/${ueId}/details`)
        .then(response => response.json())
        .then(data => {
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">${data.code}</h6>
                        <h5 class="mb-3">${data.nom}</h5>

                        <div class="mb-3">
                            <strong>Informations générales:</strong>
                            <ul class="list-unstyled mt-2">
                                <li><i class="fas fa-calendar text-info me-2"></i>Semestre: ${data.semestre}</li>
                                <li><i class="fas fa-layer-group text-secondary me-2"></i>Niveau: ${data.niveau}</li>
                                <li><i class="fas fa-graduation-cap text-success me-2"></i>Filière: ${data.filiere}</li>
                                <li><i class="fas fa-building text-warning me-2"></i>Département: ${data.departement}</li>
                                <li><i class="fas fa-user-tie text-primary me-2"></i>Responsable: ${data.responsable}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>Configuration des heures:</strong>
                        <div class="row mt-2">
                            <div class="col-4 text-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2">
                                    <h5 class="text-primary mb-0">${data.heures_cm}</h5>
                                    <small class="text-muted">CM</small>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <h5 class="text-success mb-0">${data.heures_td}</h5>
                                    <small class="text-muted">TD</small>
                                </div>
                            </div>
                            <div class="col-4 text-center">
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <h5 class="text-warning mb-0">${data.heures_tp}</h5>
                                    <small class="text-muted">TP</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center">
                            <div class="bg-info bg-opacity-10 rounded p-2">
                                <h4 class="text-info mb-0">${data.total_hours}h</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <strong>Groupes TD:</strong> ${data.groupes_td}
                            </div>
                            <div class="col-6">
                                <strong>Groupes TP:</strong> ${data.groupes_tp}
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('ueDetailsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('ueDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors du chargement des détails de l\'UE');
        });
}

// Show request form
function showRequestForm() {
    if (!currentUEId) return;

    // Hide details modal
    bootstrap.Modal.getInstance(document.getElementById('ueDetailsModal')).hide();

    // Set UE ID in request form
    document.getElementById('request_ue_id').value = currentUEId;

    // Get UE info for display
    fetch(`/enseignant/ues/${currentUEId}/details`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('selected_ue_info').innerHTML = `
                <strong>${data.code} - ${data.nom}</strong><br>
                <small class="text-muted">${data.filiere} • ${data.semestre} • ${data.total_hours}h total</small>
            `;
        });

    // Show request modal
    new bootstrap.Modal(document.getElementById('requestModal')).show();
}

// Cancel request
function cancelRequest(affectationId) {
    document.getElementById('cancelForm').action = `/enseignant/ues/cancel/${affectationId}`;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    // Handle request form submission
    const requestForm = document.querySelector('#requestModal form');
    if (requestForm) {
        requestForm.addEventListener('submit', function(e) {
            // Validate that at least one type seance is selected
            const checkboxes = this.querySelectorAll('input[name="type_seance[]"]:checked');
            const errorDiv = document.getElementById('type_seance_error');

            if (checkboxes.length === 0) {
                e.preventDefault();
                errorDiv.style.display = 'block';
                errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            } else {
                errorDiv.style.display = 'none';
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Envoi en cours...';
            submitBtn.disabled = true;
        });
    }

    // Add click handlers for checkbox cards
    document.querySelectorAll('.form-check-card .card').forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.closest('.form-check-card').querySelector('.form-check-input');
            checkbox.checked = !checkbox.checked;

            // Hide error message if at least one is selected
            const checkedBoxes = document.querySelectorAll('input[name="type_seance[]"]:checked');
            if (checkedBoxes.length > 0) {
                document.getElementById('type_seance_error').style.display = 'none';
            }
        });
    });

    // Handle cancel form submission
    const cancelForm = document.getElementById('cancelForm');
    if (cancelForm) {
        cancelForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Annulation...';
            submitBtn.disabled = true;
        });
    }

    // Dynamic semester filtering based on filière selection
    const filiereSelect = document.getElementById('filiere_filter');
    const semestreSelect = document.getElementById('semestre_filter');

    if (filiereSelect && semestreSelect) {
        filiereSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const filiereName = selectedOption.text;

            // Clear current semester options
            semestreSelect.innerHTML = '<option value="">Tous les semestres</option>';

            let semestres = [];

            // Extract level from filière name
            const levelMatch = filiereName.match(/(\d)$/);
            if (levelMatch) {
                const level = parseInt(levelMatch[1]);
                switch (level) {
                    case 1:
                        semestres = ['S1', 'S2'];
                        break;
                    case 2:
                        semestres = ['S3', 'S4'];
                        break;
                    case 3:
                        semestres = ['S5'];
                        break;
                    default:
                        semestres = ['S1', 'S2', 'S3', 'S4', 'S5'];
                }
            } else {
                // Default all semesters if no level found
                semestres = ['S1', 'S2', 'S3', 'S4', 'S5'];
            }

            // Add semester options
            semestres.forEach(function(semestre) {
                const option = document.createElement('option');
                option.value = semestre;
                option.textContent = semestre;
                semestreSelect.appendChild(option);
            });
        });
    }
});
</script>
@endsection
