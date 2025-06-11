@extends('layouts.admin')

@section('title', 'Détails de l\'Unité d\'Enseignement')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-eye text-info me-2"></i>
                        Détails de l'UE: {{ $ue->code }}
                    </h2>
                    <p class="text-muted mb-0">{{ $ue->nom }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.ues.edit', $ue->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-1"></i>Modifier
                    </a>
                    <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations Générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Code de l'UE</label>
                                <div class="fw-bold fs-5 text-primary">{{ $ue->code }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Nom de l'UE</label>
                                <div class="fw-bold">{{ $ue->nom }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Semestre</label>
                                <div>
                                    <span class="badge bg-info fs-6">{{ $ue->semestre }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Niveau</label>
                                <div>
                                    <span class="badge bg-secondary fs-6">{{ $ue->niveau }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Année Universitaire</label>
                                <div class="fw-bold">{{ $ue->annee_universitaire }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Statut</label>
                                <div>
                                    @if($ue->est_vacant)
                                        <span class="badge bg-warning fs-6">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Vacant
                                        </span>
                                    @else
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check-circle me-1"></i>Assigné
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Date de création</label>
                                <div>{{ $ue->created_at ? $ue->created_at->format('d/m/Y à H:i') : 'Non disponible' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hours Configuration -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Configuration des Heures
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3 mb-3">
                                <div class="fs-4 fw-bold text-primary">{{ $ue->heures_cm }}</div>
                                <div class="text-muted">Heures CM</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-success bg-opacity-10 rounded p-3 mb-3">
                                <div class="fs-4 fw-bold text-success">{{ $ue->heures_td }}</div>
                                <div class="text-muted">Heures TD</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-warning bg-opacity-10 rounded p-3 mb-3">
                                <div class="fs-4 fw-bold text-warning">{{ $ue->heures_tp }}</div>
                                <div class="text-muted">Heures TP</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="bg-info bg-opacity-10 rounded p-3 mb-3">
                                <div class="fs-4 fw-bold text-info">{{ $ue->total_hours }}</div>
                                <div class="text-muted">Total</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Groupes TD</label>
                                <div class="fw-bold">{{ $ue->groupes_td }} groupe(s)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Groupes TP</label>
                                <div class="fw-bold">{{ $ue->groupes_tp }} groupe(s)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Affectations -->
            @if($ue->affectations->count() > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Affectations ({{ $ue->affectations->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Enseignant</th>
                                    <th>Type de séance</th>
                                    <th>Statut</th>
                                    <th>Année</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ue->affectations as $affectation)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="fas fa-user text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $affectation->user->name }}</strong>
                                                <br><small class="text-muted">{{ ucfirst($affectation->user->role) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $affectation->type_seance }}</span>
                                    </td>
                                    <td>
                                        @if($affectation->validee === 'valide')
                                            <span class="badge bg-success">Validé</span>
                                        @elseif($affectation->validee === 'refuse')
                                            <span class="badge bg-danger">Refusé</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                    <td>{{ $affectation->annee_universitaire ?? '2024-2025' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Information -->
        <div class="col-md-4">
            <!-- Assignment Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>
                        Affectation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Département Gestionnaire</label>
                        <div class="fw-bold">
                            @if($ue->departement)
                                <i class="fas fa-building text-primary me-1"></i>
                                {{ $ue->departement->nom }}
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Filière d'Enseignement</label>
                        <div class="fw-bold">
                            @if($ue->filiere)
                                <i class="fas fa-graduation-cap text-success me-1"></i>
                                {{ $ue->filiere->nom }}
                                @if($ue->filiere->departement)
                                    <br><small class="text-muted">{{ $ue->filiere->departement->nom }}</small>
                                @endif
                            @else
                                <span class="text-muted">Non assignée</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Responsable de l'UE</label>
                        <div>
                            @if($ue->responsable)
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-user text-success"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $ue->responsable->name }}</strong>
                                        <br><small class="text-muted">{{ ucfirst($ue->responsable->role) }}</small>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">Non assigné</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-info bg-opacity-10 rounded p-2 mb-2">
                                <div class="fs-5 fw-bold text-info">{{ $ue->affectations_count ?? 0 }}</div>
                                <small class="text-muted">Affectations</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-warning bg-opacity-10 rounded p-2 mb-2">
                                <div class="fs-5 fw-bold text-warning">{{ $ue->notes_count ?? 0 }}</div>
                                <small class="text-muted">Notes</small>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 rounded p-2">
                                <div class="fs-5 fw-bold text-primary">{{ $ue->schedules_count ?? 0 }}</div>
                                <small class="text-muted">Emplois</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 rounded p-2">
                                <div class="fs-5 fw-bold text-success">{{ $ue->reclamations_count ?? 0 }}</div>
                                <small class="text-muted">Réclamations</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.ues.edit', $ue->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i>Modifier l'UE
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>Supprimer l'UE
                        </button>
                        <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-1"></i>Toutes les UEs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                        <i class="fas fa-book text-danger fa-2x"></i>
                    </div>
                    <h6 class="mb-2">Supprimer l'unité d'enseignement</h6>
                    <p class="mb-0">Êtes-vous sûr de vouloir supprimer l'UE <strong>{{ $ue->code }} - {{ $ue->nom }}</strong> ?</p>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>Cette action est irréversible et supprimera définitivement toutes les données associées.</small>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
                <form action="{{ route('admin.ues.destroy', $ue->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 12px;
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-weight: 500;
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
</style>
@endsection
