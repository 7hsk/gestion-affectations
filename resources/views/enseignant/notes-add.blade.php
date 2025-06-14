@extends('layouts.enseignant')

@section('title', 'Ajouter Note')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea, #764ba2) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-plus me-2"></i>Ajouter une Note
                            </h4>
                            <p class="mb-0 opacity-75">Saisissez une note individuelle pour un étudiant</p>
                        </div>
                        <div>
                            <a href="{{ route('enseignant.notes') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Formulaire de Saisie
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enseignant.notes.store') }}">
                        @csrf
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-book me-1"></i>Unité d'Enseignement
                                </label>
                                <select name="ue_id" class="form-select form-select-lg" required>
                                    <option value="">Sélectionnez une UE</option>
                                    @foreach($uesAssignees as $ue)
                                        <option value="{{ $ue->id }}">
                                            {{ $ue->code }} - {{ $ue->nom }}
                                            @if($ue->filiere)
                                                ({{ $ue->filiere->nom }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Type de Session
                                </label>
                                <select name="session_type" class="form-select form-select-lg" required>
                                    <option value="">Sélectionnez le type</option>
                                    <option value="normale">📝 Session Normale</option>
                                    <option value="rattrapage">🔄 Session Rattrapage</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-id-card me-1"></i>CNE/Matricule Étudiant
                                </label>
                                <input type="text" name="matricule" class="form-control form-control-lg" 
                                       placeholder="Ex: R123456789" required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Saisissez le CNE ou matricule de l'étudiant
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Nom de l'Étudiant
                                </label>
                                <input type="text" name="nom_etudiant" class="form-control form-control-lg" 
                                       placeholder="Ex: Ahmed BENALI" required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Nom complet de l'étudiant pour vérification
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-1"></i>Note (/20)
                                </label>
                                <input type="number" name="note" class="form-control form-control-lg" 
                                       min="0" max="20" step="0.25" placeholder="Ex: 15.5">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Note sur 20 (laissez vide si étudiant absent)
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user-times me-1"></i>Statut de Présence
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_absent" 
                                               id="present" value="0" checked>
                                        <label class="form-check-label" for="present">
                                            <i class="fas fa-check text-success me-1"></i>Présent
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_absent" 
                                               id="absent" value="1">
                                        <label class="form-check-label" for="absent">
                                            <i class="fas fa-times text-danger me-1"></i>Absent
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Instructions:
                            </h6>
                            <ul class="mb-0">
                                <li>Vérifiez que le CNE/matricule correspond bien à l'étudiant</li>
                                <li>Les notes doivent être comprises entre 0 et 20</li>
                                <li>Si l'étudiant est absent, cochez "Absent" et laissez la note vide</li>
                                <li>Cette action remplacera toute note existante pour cet étudiant</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Les modifications sont enregistrées immédiatement
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('enseignant.notes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i>Enregistrer la Note
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-tips me-2"></i>Conseils Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                                    <i class="fas fa-upload text-primary fa-2x"></i>
                                </div>
                                <h6>Import en Masse</h6>
                                <small class="text-muted">
                                    Pour plusieurs notes, utilisez l'import Excel depuis la page principale
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                                    <i class="fas fa-edit text-success fa-2x"></i>
                                </div>
                                <h6>Modification</h6>
                                <small class="text-muted">
                                    Vous pouvez modifier les notes existantes depuis la liste principale
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-2">
                                    <i class="fas fa-history text-warning fa-2x"></i>
                                </div>
                                <h6>Historique</h6>
                                <small class="text-muted">
                                    Toutes les modifications sont enregistrées dans l'historique du système
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle absence checkbox logic
    const presentRadio = document.getElementById('present');
    const absentRadio = document.getElementById('absent');
    const noteInput = document.querySelector('input[name="note"]');
    
    function toggleNoteInput() {
        if (absentRadio.checked) {
            noteInput.disabled = true;
            noteInput.value = '';
            noteInput.placeholder = 'Note non requise (étudiant absent)';
        } else {
            noteInput.disabled = false;
            noteInput.placeholder = 'Ex: 15.5';
        }
    }
    
    presentRadio.addEventListener('change', toggleNoteInput);
    absentRadio.addEventListener('change', toggleNoteInput);
    
    // Initial state
    toggleNoteInput();
});
</script>
@endpush
@endsection
