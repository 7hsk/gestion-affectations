@extends('layouts.enseignant')

@section('title', 'Importer Notes')

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
                                <i class="fas fa-upload me-2"></i>Importer Notes depuis Excel
                            </h4>
                            <p class="mb-0 opacity-75">Importez les notes de vos étudiants via un fichier Excel</p>
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

    <!-- Import Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-excel me-2"></i>Formulaire d'Import
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enseignant.notes.import') }}" enctype="multipart/form-data">
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

                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-file-excel me-1"></i>Fichier Excel
                            </label>
                            <input type="file" name="file" class="form-control form-control-lg" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats acceptés: .xlsx, .xls, .csv (Taille max: 2MB)
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Format requis:
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Colonne A - Nom Etudiant:</strong> Nom complet de l'étudiant</li>
                                <li><strong>Colonne B - CNE Etudiant:</strong> Numéro CNE/Matricule</li>
                                <li><strong>Colonne C - Note:</strong> Note sur 20 (ex: 15.5)</li>
                                <li><strong>Colonne D - Statut Absence:</strong> "absent" ou vide si présent</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Vos données sont sécurisées et traitées de manière confidentielle
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('enseignant.notes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload me-2"></i>Importer les Notes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Aide et Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-download me-1"></i>Étapes d'import:
                            </h6>
                            <ol class="small">
                                <li>Téléchargez le modèle Excel depuis la page principale</li>
                                <li>Remplissez les colonnes avec les données des étudiants</li>
                                <li>Sélectionnez l'UE et le type de session</li>
                                <li>Choisissez votre fichier Excel rempli</li>
                                <li>Cliquez sur "Importer les Notes"</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>Points importants:
                            </h6>
                            <ul class="small">
                                <li>Les notes doivent être entre 0 et 20</li>
                                <li>Utilisez le point (.) pour les décimales</li>
                                <li>Marquez "absent" pour les étudiants absents</li>
                                <li>Vérifiez les CNE/Matricules des étudiants</li>
                                <li>L'import remplace les notes existantes</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
