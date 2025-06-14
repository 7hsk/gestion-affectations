@extends('layouts.vacataire')

@section('title', 'Ajouter une Note')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('vacataire.dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home me-1"></i>Tableau de Bord
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('vacataire.notes') }}" class="text-decoration-none">
                            <i class="fas fa-graduation-cap me-1"></i>Gestion Notes
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <i class="fas fa-plus me-1"></i>Ajouter Note
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #7c3aed, #a855f7) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-plus me-2"></i>Ajouter une Note
                            </h4>
                            <p class="mb-0 opacity-75">Saisissez une note individuelle pour un étudiant</p>
                        </div>
                        <div>
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Informations de la Note
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vacataire.notes.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-book me-1"></i>Unité d'Enseignement
                                    </label>
                                    <select name="ue_id" class="form-select form-select-lg" required>
                                        <option value="">🔍 Sélectionner une UE</option>
                                        @foreach($uesAssignees as $ue)
                                            <option value="{{ $ue->id }}">
                                                {{ $ue->code }} - {{ $ue->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar me-1"></i>Type de Session
                                    </label>
                                    <select name="session_type" class="form-select form-select-lg" required>
                                        <option value="normale">📝 Session Normale</option>
                                        <option value="rattrapage">🔄 Session Rattrapage</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-id-card me-1"></i>CNE/Matricule Étudiant
                                    </label>
                                    <input type="text" name="matricule" class="form-control form-control-lg" 
                                           placeholder="Ex: 12345678" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Numéro CNE ou matricule de l'étudiant
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user me-1"></i>Nom Étudiant
                                    </label>
                                    <input type="text" name="nom_etudiant" class="form-control form-control-lg" 
                                           placeholder="Ex: Jean Dupont" required>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nom complet de l'étudiant
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-star me-1"></i>Note (/20)
                                    </label>
                                    <input type="number" name="note" class="form-control form-control-lg" 
                                           min="0" max="20" step="0.01" placeholder="Ex: 15.5">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Note sur 20 (laissez vide si étudiant absent)
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-user-times me-1"></i>Statut de Présence
                                    </label>
                                    <select name="is_absent" class="form-select form-select-lg">
                                        <option value="0">✅ Présent</option>
                                        <option value="1">❌ Absent</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Si absent, la note sera ignorée
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Conseils:
                            </h6>
                            <ul class="mb-0">
                                <li>Vérifiez que le CNE/matricule correspond bien à l'étudiant</li>
                                <li>Les notes doivent être comprises entre 0 et 20</li>
                                <li>Utilisez le point (.) pour les décimales (ex: 15.5)</li>
                                <li>Si l'étudiant est absent, sélectionnez "Absent" et laissez la note vide</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i>Enregistrer la Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-download fa-3x text-primary"></i>
                    </div>
                    <h6>Modèle Excel</h6>
                    <p class="text-muted mb-3">Pour saisir plusieurs notes rapidement</p>
                    <a href="{{ route('vacataire.notes.download-template-page') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-download me-1"></i>Télécharger
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-upload fa-3x text-success"></i>
                    </div>
                    <h6>Import Excel</h6>
                    <p class="text-muted mb-3">Importez un fichier Excel avec plusieurs notes</p>
                    <a href="{{ route('vacataire.notes.import-page') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-upload me-1"></i>Importer
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-list fa-3x text-warning"></i>
                    </div>
                    <h6>Voir les Notes</h6>
                    <p class="text-muted mb-3">Consultez toutes les notes saisies</p>
                    <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-eye me-1"></i>Voir
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('➕ ADD NOTE PAGE INITIALIZED');
    
    // Form validation
    const form = document.querySelector('form');
    const ueSelect = document.querySelector('select[name="ue_id"]');
    const matriculeInput = document.querySelector('input[name="matricule"]');
    const nomInput = document.querySelector('input[name="nom_etudiant"]');
    const noteInput = document.querySelector('input[name="note"]');
    const absentSelect = document.querySelector('select[name="is_absent"]');
    
    // Handle absence status change
    absentSelect.addEventListener('change', function() {
        if (this.value === '1') { // Absent
            noteInput.value = '';
            noteInput.disabled = true;
            noteInput.placeholder = 'Note non requise (étudiant absent)';
        } else { // Present
            noteInput.disabled = false;
            noteInput.placeholder = 'Ex: 15.5';
        }
    });
    
    // Note validation
    noteInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (this.value && (isNaN(value) || value < 0 || value > 20)) {
            this.setCustomValidity('La note doit être comprise entre 0 et 20');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });
    
    form.addEventListener('submit', function(e) {
        // Validate required fields
        if (!ueSelect.value) {
            e.preventDefault();
            showNotification('Veuillez sélectionner une UE', 'error');
            ueSelect.focus();
            return false;
        }
        
        if (!matriculeInput.value.trim()) {
            e.preventDefault();
            showNotification('Veuillez saisir le CNE/matricule', 'error');
            matriculeInput.focus();
            return false;
        }
        
        if (!nomInput.value.trim()) {
            e.preventDefault();
            showNotification('Veuillez saisir le nom de l\'étudiant', 'error');
            nomInput.focus();
            return false;
        }
        
        // If present, note is required
        if (absentSelect.value === '0' && !noteInput.value) {
            e.preventDefault();
            showNotification('Veuillez saisir une note ou marquer l\'étudiant comme absent', 'error');
            noteInput.focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enregistrement...';
        submitBtn.disabled = true;
        
        showNotification('Enregistrement en cours...', 'info');
    });
});

function showNotification(message, type = 'info') {
    const notification = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 99999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notification);
    
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-of-type');
        if (alert) alert.remove();
    }, 5000);
}
</script>
@endpush
@endsection
