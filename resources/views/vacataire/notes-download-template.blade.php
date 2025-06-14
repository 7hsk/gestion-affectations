@extends('layouts.vacataire')

@section('title', 'Télécharger Modèle Excel')

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
                        <i class="fas fa-download me-1"></i>Télécharger Modèle
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
                                <i class="fas fa-download me-2"></i>Télécharger Modèle Excel
                            </h4>
                            <p class="mb-0 opacity-75">Générez un modèle Excel pré-rempli avec la liste des étudiants</p>
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
                        <i class="fas fa-file-excel me-2"></i>Configuration du Modèle
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vacataire.notes.download-template') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-book me-1"></i>Unité d'Enseignement
                                    </label>
                                    <select name="ue_id" class="form-select form-select-lg" required>
                                        <option value="">🔍 Sélectionner une UE</option>
                                        @foreach($uesAssignees as $ue)
                                            <option value="{{ $ue->id }}">
                                                {{ $ue->code }} - {{ $ue->nom }}
                                                <small>({{ $ue->filiere->nom ?? 'N/A' }})</small>
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Le modèle contiendra automatiquement tous les étudiants de cette UE
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-table me-2"></i>Le modèle Excel contiendra:
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Tableau simple:</strong> Liste des étudiants avec 4 colonnes à remplir</li>
                                <li><strong>Colonne A:</strong> Nom Etudiant (pré-rempli)</li>
                                <li><strong>Colonne B:</strong> CNE Etudiant (pré-rempli)</li>
                                <li><strong>Colonne C:</strong> Note (vide - à remplir de 0 à 20)</li>
                                <li><strong>Colonne D:</strong> Statut Absence (vide - "absent" ou laisser vide)</li>
                            </ul>
                        </div>

                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="fas fa-check-circle me-2"></i>Tableau prêt à remplir:
                            </h6>
                            <ul class="mb-0">
                                <li>✅ Noms et CNE des étudiants déjà remplis</li>
                                <li>✅ Colonnes Note et Statut Absence vides pour saisie</li>
                                <li>✅ Format Excel professionnel avec bordures</li>
                                <li>✅ Prêt pour import direct après remplissage</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-download me-1"></i>Télécharger le Modèle Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-primary"></i>
                    </div>
                    <h6>Étudiants Inclus</h6>
                    <p class="text-muted mb-0">Tous les étudiants de la filière de l'UE sélectionnée</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-table fa-3x text-success"></i>
                    </div>
                    <h6>Tableau Simple</h6>
                    <p class="text-muted mb-0">Tableau Excel propre et organisé, prêt à remplir</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-edit fa-3x text-warning"></i>
                    </div>
                    <h6>Saisie Rapide</h6>
                    <p class="text-muted mb-0">Remplissez directement les notes dans le tableau</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📥 DOWNLOAD TEMPLATE PAGE INITIALIZED');
    
    // Form validation
    const form = document.querySelector('form');
    const ueSelect = document.querySelector('select[name="ue_id"]');
    
    form.addEventListener('submit', function(e) {
        if (!ueSelect.value) {
            e.preventDefault();
            showNotification('Veuillez sélectionner une UE', 'error');
            ueSelect.focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Génération en cours...';
        submitBtn.disabled = true;
        
        // Reset after delay (file download)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            showNotification('Modèle Excel téléchargé avec succès!', 'success');
        }, 2000);
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
