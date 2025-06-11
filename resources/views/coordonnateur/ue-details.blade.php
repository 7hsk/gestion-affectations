@extends('layouts.coordonnateur')

@section('title', 'Détails UE - ' . $ue->code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Détails de l'UE</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('coordonnateur.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('coordonnateur.unites-enseignement') }}">Unités d'Enseignement</a></li>
                    <li class="breadcrumb-item active">{{ $ue->code }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('coordonnateur.unites-enseignement') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- UE Information Card -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #059669, #10b981); color: white;">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>{{ $ue->code }} - {{ $ue->nom }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3" style="color: #059669;">
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
                                            <span class="badge" style="background-color: #059669;">{{ $ue->filiere->nom }}</span>
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
                            <h6 class="mb-3" style="color: #059669;">
                                <i class="fas fa-clock me-2"></i>Charge Horaire
                            </h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted">CM:</td>
                                    <td><span class="badge bg-success fs-6">{{ $ue->heures_cm ?: 0 }}h</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">TD:</td>
                                    <td><span class="badge fs-6" style="background-color: #059669;">{{ $ue->heures_td ?: 0 }}h</span></td>
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
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981, #34d399); color: white;">
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

            <!-- Groups Information -->
            <div class="card shadow-sm mt-3">
                <div class="card-header" style="background: linear-gradient(135deg, #10b981, #34d399); color: white;">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Groupes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Groupes TD:</span>
                            <span class="fw-bold">{{ $ue->groupes_td ?: 'Non défini' }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Groupes TP:</span>
                            <span class="fw-bold">{{ $ue->groupes_tp ?: 'Non défini' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affectations List -->
    @if($ue->affectations->count() > 0)
        <div class="card shadow-sm mt-4">
            <div class="card-header" style="background: linear-gradient(135deg, #059669, #10b981); color: white;">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-check me-2"></i>Affectations
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Enseignant</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Année</th>
                                <th>Date de demande</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ue->affectations as $affectation)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title rounded-circle" style="background-color: #059669; color: white;">
                                                    {{ substr($affectation->user->name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $affectation->user->name }}</h6>
                                                <small class="text-muted">{{ $affectation->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $affectation->type_seance }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $affectation->validee === 'valide' ? 'success' : ($affectation->validee === 'rejete' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($affectation->validee) }}
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
            </div>
        </div>
    @endif
</div>

<style>
.avatar-sm {
    width: 2rem;
    height: 2rem;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.stat-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.stat-item:last-child {
    border-bottom: none;
}
</style>
@endsection
