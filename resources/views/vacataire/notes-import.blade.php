@extends('layouts.vacataire')

@section('title', 'Importer Notes Excel')

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
                        <i class="fas fa-upload me-1"></i>Importer Notes
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
                                <i class="fas fa-upload me-2"></i>Importer Notes depuis Excel
                            </h4>
                            <p class="mb-0 opacity-75">Importez les notes de vos étudiants à partir d'un fichier Excel</p>
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
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Configuration de l'Import
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vacataire.notes.import') }}" enctype="multipart/form-data">
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
                                                <small>({{ $ue->filiere->nom ?? 'N/A' }})</small>
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

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-excel me-1"></i>Fichier Excel
                            </label>
                            <input type="file" name="file" class="form-control form-control-lg" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats acceptés: Excel (.xlsx, .xls) ou CSV (.csv) - Taille max: 2MB
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Format requis:
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>Colonne A - Nom Etudiant:</strong> Nom complet de l'étudiant</li>
                                        <li><strong>Colonne B - CNE Etudiant:</strong> Numéro CNE/Matricule</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="mb-0">
                                        <li><strong>Colonne C - Note:</strong> Note sur 20 (ex: 15.5)</li>
                                        <li><strong>Colonne D - Statut Absence:</strong> "absent" ou vide si présent</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Conseil:
                            </h6>
                            <p class="mb-2">Téléchargez d'abord le modèle Excel - tableau simple prêt à remplir!</p>
                            <a href="{{ route('vacataire.notes.download-template-page') }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download me-1"></i>Télécharger le Tableau Excel
                            </a>
                        </div>

                        <div class="d-flex gap-3 justify-content-center">
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-upload me-1"></i>Importer les Notes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-table me-2"></i>Exemple de Format Excel
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-primary">
                                <tr>
                                    <th>A - Nom Etudiant</th>
                                    <th>B - CNE Etudiant</th>
                                    <th>C - Note</th>
                                    <th>D - Statut Absence</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jean Dupont</td>
                                    <td>12345678</td>
                                    <td>15.5</td>
                                    <td>présent</td>
                                </tr>
                                <tr>
                                    <td>Marie Martin</td>
                                    <td>87654321</td>
                                    <td></td>
                                    <td>absent</td>
                                </tr>
                                <tr>
                                    <td>Pierre Durand</td>
                                    <td>11223344</td>
                                    <td>12</td>
                                    <td>présent</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h6>Validation Automatique</h6>
                    <p class="text-muted mb-0">Le système vérifie automatiquement le format et les données</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                    </div>
                    <h6>Gestion d'Erreurs</h6>
                    <p class="text-muted mb-0">Rapport détaillé des erreurs avec numéros de ligne</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-database fa-3x text-info"></i>
                    </div>
                    <h6>Mise à Jour Intelligente</h6>
                    <p class="text-muted mb-0">Met à jour les notes existantes ou en crée de nouvelles</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📤 IMPORT NOTES PAGE INITIALIZED');
    
    // Form validation
    const form = document.querySelector('form');
    const ueSelect = document.querySelector('select[name="ue_id"]');
    const fileInput = document.querySelector('input[name="file"]');
    
    // File input validation
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024; // MB
            const fileName = file.name;
            const fileExt = fileName.split('.').pop().toLowerCase();
            
            if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                showNotification('Format de fichier non supporté. Utilisez .xlsx, .xls ou .csv', 'error');
                this.value = '';
                return;
            }
            
            if (fileSize > 2) {
                showNotification('Le fichier est trop volumineux. Taille max: 2MB', 'error');
                this.value = '';
                return;
            }
            
            showNotification(`Fichier sélectionné: ${fileName} (${fileSize.toFixed(2)} MB)`, 'success');
        }
    });
    
    form.addEventListener('submit', function(e) {
        if (!ueSelect.value) {
            e.preventDefault();
            showNotification('Veuillez sélectionner une UE', 'error');
            ueSelect.focus();
            return false;
        }
        
        if (!fileInput.files.length) {
            e.preventDefault();
            showNotification('Veuillez sélectionner un fichier Excel', 'error');
            fileInput.focus();
            return false;
        }
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importation en cours...';
        submitBtn.disabled = true;
        
        showNotification('Import en cours... Veuillez patienter', 'info');
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
