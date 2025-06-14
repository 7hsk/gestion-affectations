@extends('layouts.vacataire')

@section('title', 'Modifier une Note')

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
                        <i class="fas fa-edit me-1"></i>Modifier Note
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
                                <i class="fas fa-edit me-2"></i>Modifier une Note
                            </h4>
                            <p class="mb-0 opacity-75">Modifiez la note de {{ $note->etudiant->name ?? 'l\'étudiant' }}</p>
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

    <!-- Current Note Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations Actuelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>UE:</strong><br>
                            <span class="text-muted">{{ $note->uniteEnseignement->code }} - {{ $note->uniteEnseignement->nom }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Étudiant:</strong><br>
                            <span class="text-muted">{{ $note->etudiant->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>CNE:</strong><br>
                            <span class="text-muted">{{ $note->etudiant->matricule ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Session:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($note->session_type ?? 'normale') }}</span>
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
                        <i class="fas fa-edit me-2"></i>Modifier la Note
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vacataire.notes.update', $note->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-star me-1"></i>Note (/20)
                                    </label>
                                    <input type="number" name="note" class="form-control form-control-lg" 
                                           min="0" max="20" step="0.01" 
                                           value="{{ $note->note }}"
                                           placeholder="Ex: 15.5">
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
                                        <option value="0" {{ !$note->is_absent ? 'selected' : '' }}>✅ Présent</option>
                                        <option value="1" {{ $note->is_absent ? 'selected' : '' }}>❌ Absent</option>
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
                                <li>Les notes doivent être comprises entre 0 et 20</li>
                                <li>Utilisez le point (.) pour les décimales (ex: 15.5)</li>
                                <li>Si l'étudiant est absent, sélectionnez "Absent" et laissez la note vide</li>
                                <li>Cette modification remplacera la note existante</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-1"></i>Mettre à Jour la Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Note History -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Historique de la Note
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-plus-circle fa-2x text-success"></i>
                                    </div>
                                    <h6>Créée le</h6>
                                    <p class="text-muted mb-0">{{ $note->created_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-edit fa-2x text-warning"></i>
                                    </div>
                                    <h6>Modifiée le</h6>
                                    <p class="text-muted mb-0">{{ $note->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-user fa-2x text-info"></i>
                                    </div>
                                    <h6>Saisie par</h6>
                                    <p class="text-muted mb-0">{{ $note->uploadedBy->name ?? 'Système' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <i class="fas fa-plus fa-3x text-success"></i>
                    </div>
                    <h6>Ajouter Note</h6>
                    <p class="text-muted mb-3">Saisir une nouvelle note pour un autre étudiant</p>
                    <a href="{{ route('vacataire.notes.add-page') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-upload fa-3x text-primary"></i>
                    </div>
                    <h6>Import Excel</h6>
                    <p class="text-muted mb-3">Importez plusieurs notes depuis Excel</p>
                    <a href="{{ route('vacataire.notes.import-page') }}" class="btn btn-outline-primary btn-sm">
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
                    <h6>Toutes les Notes</h6>
                    <p class="text-muted mb-3">Voir la liste complète des notes</p>
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
    console.log('✏️ EDIT NOTE PAGE INITIALIZED');
    
    // Form validation
    const form = document.querySelector('form');
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
    
    // Initialize form state based on current absence status
    if (absentSelect.value === '1') {
        noteInput.disabled = true;
        noteInput.placeholder = 'Note non requise (étudiant absent)';
    }
    
    form.addEventListener('submit', function(e) {
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mise à jour...';
        submitBtn.disabled = true;
        
        showNotification('Mise à jour en cours...', 'info');
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
