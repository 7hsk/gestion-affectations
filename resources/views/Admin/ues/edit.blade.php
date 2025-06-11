@extends('layouts.admin')

@section('title', 'Modifier l\'Unité d\'Enseignement')

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

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                <div>
                    <strong>Erreurs de validation:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Modifier l'Unité d'Enseignement
                    </h2>
                    <p class="text-muted mb-0">Modifiez les informations de l'UE: <strong>{{ $ue->code }} - {{ $ue->nom }}</strong></p>
                </div>
                <div>
                    <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                    <a href="{{ route('admin.ues.show', $ue->id) }}" class="btn btn-outline-info ms-2">
                        <i class="fas fa-eye me-1"></i>Voir détails
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Modifier les informations
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.ues.update', $ue->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Code de l'UE <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                                           id="code" name="code" value="{{ old('code', $ue->code) }}"
                                           placeholder="Ex: INF101" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nom" class="form-label">Nom de l'UE <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                           id="nom" name="nom" value="{{ old('nom', $ue->nom) }}"
                                           placeholder="Ex: Programmation Orientée Objet" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semestre" class="form-label">Semestre <span class="text-danger">*</span></label>
                                    <select class="form-select @error('semestre') is-invalid @enderror"
                                            id="semestre" name="semestre" required>
                                        <option value="">Sélectionner un semestre</option>
                                        @foreach($semestres as $sem)
                                            <option value="{{ $sem }}" {{ old('semestre', $ue->semestre) == $sem ? 'selected' : '' }}>
                                                {{ $sem }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('semestre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="specialite" class="form-label">Spécialité</label>
                                    <input type="text" class="form-control @error('specialite') is-invalid @enderror"
                                           id="specialite" name="specialite" value="{{ old('specialite', $ue->specialite) }}"
                                           placeholder="Ex: Informatique, Réseaux, etc.">
                                    <small class="text-muted">Spécialité requise pour enseigner cette UE</small>
                                    @error('specialite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Hours Configuration -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-clock me-2"></i>
                                    Configuration des Heures
                                </h6>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="heures_cm" class="form-label">Heures CM <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('heures_cm') is-invalid @enderror"
                                               id="heures_cm" name="heures_cm" value="{{ old('heures_cm', $ue->heures_cm) }}"
                                               min="0" max="100" required>
                                        <span class="input-group-text">h</span>
                                    </div>
                                    @error('heures_cm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="heures_td" class="form-label">Heures TD <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('heures_td') is-invalid @enderror"
                                               id="heures_td" name="heures_td" value="{{ old('heures_td', $ue->heures_td) }}"
                                               min="0" max="100" required>
                                        <span class="input-group-text">h</span>
                                    </div>
                                    @error('heures_td')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="heures_tp" class="form-label">Heures TP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('heures_tp') is-invalid @enderror"
                                               id="heures_tp" name="heures_tp" value="{{ old('heures_tp', $ue->heures_tp) }}"
                                               min="0" max="100" required>
                                        <span class="input-group-text">h</span>
                                    </div>
                                    @error('heures_tp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="groupes_td" class="form-label">Nombre de Groupes TD <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('groupes_td') is-invalid @enderror"
                                           id="groupes_td" name="groupes_td" value="{{ old('groupes_td', $ue->groupes_td) }}"
                                           min="0" max="20" required>
                                    @error('groupes_td')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="groupes_tp" class="form-label">Nombre de Groupes TP <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('groupes_tp') is-invalid @enderror"
                                           id="groupes_tp" name="groupes_tp" value="{{ old('groupes_tp', $ue->groupes_tp) }}"
                                           min="0" max="20" required>
                                    @error('groupes_tp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Information -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-3">
                                    <i class="fas fa-users me-2"></i>
                                    Affectation et Responsabilité
                                </h6>
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Note:</strong> Le département et la filière sont indépendants. Une UE peut être gérée par un département et enseignée dans une filière d'un autre département.
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="departement_id" class="form-label">Département Gestionnaire <span class="text-danger">*</span></label>
                                    <select class="form-select @error('departement_id') is-invalid @enderror"
                                            id="departement_id" name="departement_id" required>
                                        <option value="">Sélectionner un département</option>
                                        @foreach($departements as $dept)
                                            <option value="{{ $dept->id }}" {{ old('departement_id', $ue->departement_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Département responsable de la gestion de cette UE</small>
                                    @error('departement_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="filiere_id" class="form-label">Filière d'Enseignement <span class="text-danger">*</span></label>
                                    <select class="form-select @error('filiere_id') is-invalid @enderror"
                                            id="filiere_id" name="filiere_id" required>
                                        <option value="">Sélectionner une filière</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" {{ old('filiere_id', $ue->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                                {{ $filiere->nom }}
                                                @if($filiere->departement)
                                                    ({{ $filiere->departement->nom }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Filière dans laquelle cette UE est enseignée</small>
                                    @error('filiere_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="responsable_id" class="form-label">Responsable de l'UE</label>
                                    <select class="form-select @error('responsable_id') is-invalid @enderror"
                                            id="responsable_id" name="responsable_id">
                                        <option value="">Aucun responsable assigné</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('responsable_id', $ue->responsable_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->name }} ({{ ucfirst($teacher->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('responsable_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Statut</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="est_vacant" name="est_vacant"
                                               {{ old('est_vacant', $ue->est_vacant) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="est_vacant">
                                            Marquer comme vacant
                                        </label>
                                    </div>
                                    <small class="text-muted">Une UE vacante n'a pas d'enseignant assigné</small>
                                </div>
                            </div>
                        </div>

                        <!-- Total Hours Display -->
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Total des heures:</strong>
                                    <span id="total-hours">{{ $ue->total_hours }}</span> heures
                                    <small class="d-block mt-1">
                                        Ce total sera calculé automatiquement en fonction des heures CM, TD et TP saisies.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </a>
                                    <a href="{{ route('admin.ues.show', $ue->id) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye me-1"></i>Voir détails
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Mettre à jour
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced form styling */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e0e6ed;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    border-radius: 0 8px 8px 0;
    border: 1px solid #e0e6ed;
    background: #f8f9fa;
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

.card {
    border-radius: 12px;
    border: none;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.alert-info {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 8px;
}

#total-hours {
    font-weight: bold;
    color: #667eea;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calculate total hours
    function calculateTotalHours() {
        const cm = parseInt(document.getElementById('heures_cm').value) || 0;
        const td = parseInt(document.getElementById('heures_td').value) || 0;
        const tp = parseInt(document.getElementById('heures_tp').value) || 0;
        const total = cm + td + tp;

        document.getElementById('total-hours').textContent = total;

        // Update alert color based on total
        const alert = document.querySelector('.alert-info');
        if (total === 0) {
            alert.className = 'alert alert-warning';
        } else if (total > 60) {
            alert.className = 'alert alert-danger';
        } else {
            alert.className = 'alert alert-info';
        }
    }

    // Add event listeners to hour inputs
    ['heures_cm', 'heures_td', 'heures_tp'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateTotalHours);
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const cm = parseInt(document.getElementById('heures_cm').value) || 0;
        const td = parseInt(document.getElementById('heures_td').value) || 0;
        const tp = parseInt(document.getElementById('heures_tp').value) || 0;

        if (cm + td + tp === 0) {
            e.preventDefault();
            alert('Veuillez spécifier au moins un type d\'heures (CM, TD ou TP).');
            return false;
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mise à jour...';
        submitBtn.disabled = true;
    });

    // Initialize total hours calculation
    calculateTotalHours();
});
</script>
@endsection
