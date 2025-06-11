@extends('layouts.chef')

@section('title', 'Affecter UE - ' . $ue->code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Affecter une UE</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('chef.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('chef.unites-enseignement') }}">Unités d'Enseignement</a></li>
                    <li class="breadcrumb-item active">Affecter {{ $ue->code }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('chef.ue.details', $ue->id) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Voir Détails
            </a>
        </div>
    </div>

    <div class="row">
        <!-- UE Information -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>UE à Affecter
                    </h6>
                </div>
                <div class="card-body">
                    <div class="ue-info">
                        <h5 class="fw-bold text-primary">{{ $ue->code }}</h5>
                        <p class="text-muted mb-3">{{ $ue->nom }}</p>
                        
                        <div class="info-grid">
                            <div class="info-item mb-2">
                                <small class="text-muted">Filière:</small>
                                <div class="fw-bold">{{ $ue->filiere->nom ?? 'N/A' }}</div>
                            </div>
                            <div class="info-item mb-2">
                                <small class="text-muted">Semestre:</small>
                                <div><span class="badge bg-secondary">{{ $ue->semestre }}</span></div>
                            </div>
                            <div class="info-item mb-2">
                                <small class="text-muted">Spécialité:</small>
                                <div class="fw-bold">{{ $ue->specialite ?: 'Générale' }}</div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-primary mb-2">
                            <i class="fas fa-clock me-1"></i>Charge Horaire
                        </h6>
                        <div class="hours-grid">
                            <div class="d-flex justify-content-between mb-1">
                                <span>CM:</span>
                                <span class="badge bg-success">{{ $ue->heures_cm ?: 0 }}h</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>TD:</span>
                                <span class="badge bg-primary">{{ $ue->heures_td ?: 0 }}h</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>TP:</span>
                                <span class="badge bg-danger">{{ $ue->heures_tp ?: 0 }}h</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total:</span>
                                <span class="badge bg-dark">{{ ($ue->heures_cm ?: 0) + ($ue->heures_td ?: 0) + ($ue->heures_tp ?: 0) }}h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Affectation Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-user-plus me-2"></i>Formulaire d'Affectation
                    </h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('chef.affecter-ue') }}" method="POST" id="affectationForm">
                        @csrf
                        <input type="hidden" name="ue_id" value="{{ $ue->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="user_id" class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>Enseignant *
                                    </label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" 
                                            name="user_id" id="user_id" required>
                                        <option value="">Sélectionner un enseignant</option>
                                        @foreach($enseignants as $enseignant)
                                            <option value="{{ $enseignant->id }}" 
                                                    data-charge="{{ $enseignant->charge_horaire['total'] }}"
                                                    data-specialite="{{ $enseignant->specialite }}"
                                                    {{ old('user_id') == $enseignant->id ? 'selected' : '' }}>
                                                {{ $enseignant->name }} 
                                                ({{ $enseignant->charge_horaire['total'] }}h/192h)
                                                @if($enseignant->specialite) - {{ $enseignant->specialite }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type_seance" class="form-label fw-bold">
                                        <i class="fas fa-tag me-1"></i>Type de Séance *
                                    </label>
                                    <select class="form-select @error('type_seance') is-invalid @enderror" 
                                            name="type_seance" id="type_seance" required>
                                        <option value="">Sélectionner le type</option>
                                        @if($ue->heures_cm > 0)
                                            <option value="CM" {{ old('type_seance') == 'CM' ? 'selected' : '' }}>
                                                Cours Magistral (CM) - {{ $ue->heures_cm }}h
                                            </option>
                                        @endif
                                        @if($ue->heures_td > 0)
                                            <option value="TD" {{ old('type_seance') == 'TD' ? 'selected' : '' }}>
                                                Travaux Dirigés (TD) - {{ $ue->heures_td }}h
                                            </option>
                                        @endif
                                        @if($ue->heures_tp > 0)
                                            <option value="TP" {{ old('type_seance') == 'TP' ? 'selected' : '' }}>
                                                Travaux Pratiques (TP) - {{ $ue->heures_tp }}h
                                            </option>
                                        @endif
                                    </select>
                                    @error('type_seance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="annee_universitaire" class="form-label fw-bold">
                                        <i class="fas fa-calendar me-1"></i>Année Universitaire *
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('annee_universitaire') is-invalid @enderror" 
                                           name="annee_universitaire" 
                                           id="annee_universitaire"
                                           value="{{ old('annee_universitaire', date('Y') . '-' . (date('Y') + 1)) }}" 
                                           required>
                                    @error('annee_universitaire')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-info-circle me-1"></i>Charge Enseignant
                                    </label>
                                    <div id="enseignantInfo" class="form-control-plaintext">
                                        <small class="text-muted">Sélectionnez un enseignant pour voir sa charge</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="commentaire" class="form-label fw-bold">
                                <i class="fas fa-comment me-1"></i>Commentaire (optionnel)
                            </label>
                            <textarea class="form-control" name="commentaire" id="commentaire" rows="3" 
                                      placeholder="Ajouter un commentaire sur cette affectation...">{{ old('commentaire') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Confirmer l'Affectation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    padding: 0.25rem 0;
}

.hours-grid {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.ue-info {
    text-align: center;
}

.card {
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.form-select:focus,
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const enseignantSelect = document.getElementById('user_id');
    const enseignantInfo = document.getElementById('enseignantInfo');

    enseignantSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const charge = selectedOption.dataset.charge;
            const specialite = selectedOption.dataset.specialite;
            
            let statusClass = 'text-success';
            let statusText = 'Charge normale';
            
            if (charge < 192) {
                statusClass = 'text-warning';
                statusText = 'Charge insuffisante';
            } else if (charge > 240) {
                statusClass = 'text-danger';
                statusText = 'Surcharge';
            }
            
            enseignantInfo.innerHTML = `
                <div class="${statusClass}">
                    <strong>${charge}h/192h</strong> - ${statusText}
                </div>
                ${specialite ? `<small class="text-muted">Spécialité: ${specialite}</small>` : ''}
            `;
        } else {
            enseignantInfo.innerHTML = '<small class="text-muted">Sélectionnez un enseignant pour voir sa charge</small>';
        }
    });
});
</script>
@endsection
