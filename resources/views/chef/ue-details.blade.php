@extends('layouts.chef')

@section('title', 'Détails UE - ' . $ue->code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Détails de l'UE</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('chef.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('chef.unites-enseignement') }}">Unités d'Enseignement</a></li>
                    <li class="breadcrumb-item active">{{ $ue->code }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            @if($ue->est_vacant)
                <a href="{{ route('chef.ue.affecter', $ue->id) }}" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Affecter
                </a>
            @endif
        </div>
    </div>

    <!-- UE Information Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>{{ $ue->code }} - {{ $ue->nom }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">Code UE:</td>
                                    <td class="fw-bold">{{ $ue->code }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Nom:</td>
                                    <td>{{ $ue->nom }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Filière:</td>
                                    <td>
                                        @if($ue->filiere)
                                            <span class="badge bg-info">{{ $ue->filiere->nom }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Semestre:</td>
                                    <td><span class="badge bg-secondary">{{ $ue->semestre }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Spécialité:</td>
                                    <td>{{ $ue->specialite ?: 'Générale' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Statut:</td>
                                    <td>
                                        @if($ue->est_vacant)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Vacant
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Affecté
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-clock me-2"></i>Charge Horaire
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">CM:</td>
                                    <td><span class="badge bg-success fs-6">{{ $ue->heures_cm ?: 0 }}h</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">TD:</td>
                                    <td><span class="badge bg-primary fs-6">{{ $ue->heures_td ?: 0 }}h</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">TP:</td>
                                    <td><span class="badge bg-danger fs-6">{{ $ue->heures_tp ?: 0 }}h</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Total:</td>
                                    <td>
                                        <span class="badge bg-dark fs-5">
                                            {{ ($ue->heures_cm ?: 0) + ($ue->heures_td ?: 0) + ($ue->heures_tp ?: 0) }}h
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vacataire Availability Card -->
            <div class="card shadow-sm mt-4">
                <div class="card-header {{ $isFullyAffected ? 'bg-success text-white' : 'bg-warning text-dark' }}">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-clock me-2"></i>Disponibilité pour Vacataires
                        @if($isFullyAffected)
                            <span class="badge bg-light text-success ms-2">
                                <i class="fas fa-check me-1"></i>UE Complètement Affectée
                            </span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($isFullyAffected)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Cette UE est complètement affectée pour l'année {{ $currentYear }}.</strong>
                            <br>
                            <small>Tous les types de séances disponibles ont été assignés à des enseignants.
                            Les options vacataires sont désactivées car il n'y a plus de créneaux disponibles.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Affectations actuelles:</label>
                            <div class="d-flex gap-2 flex-wrap mt-2">
                                @foreach($affectedTypes as $type)
                                    @php
                                        $affectation = $currentYearAffectations->where('type_seance', $type)->first();
                                        $hours = $type === 'CM' ? $ue->heures_cm : ($type === 'TD' ? $ue->heures_td : $ue->heures_tp);
                                        $badgeClass = $type === 'CM' ? 'bg-success' : ($type === 'TD' ? 'bg-primary' : 'bg-danger');
                                    @endphp
                                    <span class="badge {{ $badgeClass }} p-2">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $type }} ({{ $hours }}h) - {{ $affectation->user->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Pour modifier les affectations vacataires, vous devez d'abord libérer des créneaux en annulant certaines affectations.
                            </small>
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-lock me-2"></i>Indisponible
                            </button>
                        </div>
                    @else
                        <form id="vacataireAvailabilityForm" action="{{ route('chef.ue.update-vacataire-availability', $ue->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label fw-bold">Types de séances disponibles pour les vacataires:</label>

                                @if(!empty($affectedTypes))
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Certains types sont déjà affectés:</strong>
                                        @foreach($affectedTypes as $type)
                                            @php
                                                $affectation = $currentYearAffectations->where('type_seance', $type)->first();
                                                $hours = $type === 'CM' ? $ue->heures_cm : ($type === 'TD' ? $ue->heures_td : $ue->heures_tp);
                                            @endphp
                                            <span class="badge bg-secondary ms-1">{{ $type }} ({{ $hours }}h) - {{ $affectation->user->name }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex gap-3 mt-2">
                                    @php
                                        $currentTypes = $ue->vacataire_types ?? [];
                                    @endphp

                                    @if($ue->heures_cm > 0)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacataire_types[]"
                                                   value="CM" id="vacataire_cm"
                                                   {{ in_array('CM', $currentTypes) ? 'checked' : '' }}
                                                   {{ in_array('CM', $affectedTypes) ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="vacataire_cm">
                                                <span class="badge {{ in_array('CM', $affectedTypes) ? 'bg-secondary' : 'bg-success' }}">
                                                    CM ({{ $ue->heures_cm }}h)
                                                    @if(in_array('CM', $affectedTypes))
                                                        <i class="fas fa-lock ms-1"></i>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    @if($ue->heures_td > 0)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacataire_types[]"
                                                   value="TD" id="vacataire_td"
                                                   {{ in_array('TD', $currentTypes) ? 'checked' : '' }}
                                                   {{ in_array('TD', $affectedTypes) ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="vacataire_td">
                                                <span class="badge {{ in_array('TD', $affectedTypes) ? 'bg-secondary' : 'bg-primary' }}">
                                                    TD ({{ $ue->heures_td }}h)
                                                    @if(in_array('TD', $affectedTypes))
                                                        <i class="fas fa-lock ms-1"></i>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endif

                                    @if($ue->heures_tp > 0)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="vacataire_types[]"
                                                   value="TP" id="vacataire_tp"
                                                   {{ in_array('TP', $currentTypes) ? 'checked' : '' }}
                                                   {{ in_array('TP', $affectedTypes) ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="vacataire_tp">
                                                <span class="badge {{ in_array('TP', $affectedTypes) ? 'bg-secondary' : 'bg-danger' }}">
                                                    TP ({{ $ue->heures_tp }}h)
                                                    @if(in_array('TP', $affectedTypes))
                                                        <i class="fas fa-lock ms-1"></i>
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                </div>

                                @if($ue->heures_cm == 0 && $ue->heures_td == 0 && $ue->heures_tp == 0)
                                    <div class="alert alert-info mt-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Cette UE n'a pas d'heures définies. Veuillez d'abord configurer les volumes horaires.
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Seuls les types non affectés peuvent être assignés aux vacataires
                                </small>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Sauvegarder
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Affectations:</span>
                            <span class="fw-bold">{{ $ue->affectations->count() }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Validées:</span>
                            <span class="fw-bold text-success">{{ $ue->affectations->where('validee', 'valide')->count() }}</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">En attente:</span>
                            <span class="fw-bold text-warning">{{ $ue->affectations->where('validee', 'en_attente')->count() }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Rejetées:</span>
                            <span class="fw-bold text-danger">{{ $ue->affectations->where('validee', 'rejete')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affectations -->
    @if($ue->affectations->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>Affectations ({{ $ue->affectations->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th><i class="fas fa-user me-1"></i>Enseignant</th>
                                <th><i class="fas fa-tag me-1"></i>Type</th>
                                <th><i class="fas fa-check-circle me-1"></i>Statut</th>
                                <th><i class="fas fa-calendar me-1"></i>Année</th>
                                <th><i class="fas fa-clock me-1"></i>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ue->affectations as $affectation)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ $affectation->user ? substr($affectation->user->name, 0, 1) : 'N' }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $affectation->user->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $affectation->user->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ 
                                            $affectation->type_seance === 'CM' ? 'bg-success' :
                                            ($affectation->type_seance === 'TD' ? 'bg-primary' : 'bg-danger')
                                        }}">
                                            {{ $affectation->type_seance }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ 
                                            $affectation->validee === 'valide' ? 'bg-success' :
                                            ($affectation->validee === 'en_attente' ? 'bg-warning' : 'bg-danger')
                                        }}">
                                            <i class="fas {{ 
                                                $affectation->validee === 'valide' ? 'fa-check' :
                                                ($affectation->validee === 'en_attente' ? 'fa-clock' : 'fa-times')
                                            }} me-1"></i>
                                            {{ 
                                                $affectation->validee === 'valide' ? 'Validé' :
                                                ($affectation->validee === 'en_attente' ? 'En attente' : 'Rejeté')
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $affectation->annee_universitaire }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $affectation->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm mt-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-users-slash fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune affectation</h5>
                <p class="text-muted mb-4">Cette UE n'a pas encore d'affectations.</p>
                @if($ue->est_vacant)
                    <a href="{{ route('chef.ue.affecter', $ue->id) }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Affecter maintenant
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
    font-weight: 600;
}

.stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.stat-item:last-child {
    border-bottom: none;
}
</style>
@endsection
