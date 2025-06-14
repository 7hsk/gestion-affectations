@extends('layouts.enseignant')

@section('title', 'Télécharger Modèle Excel')

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
                                <i class="fas fa-download me-2"></i>Télécharger Modèle Excel
                            </h4>
                            <p class="mb-0 opacity-75">Téléchargez le modèle Excel pour saisir les notes</p>
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

    <!-- Download Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-file-excel me-2"></i>Sélection de l'UE
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enseignant.notes.download-template') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">
                                <i class="fas fa-book me-1"></i>Unité d'Enseignement
                            </label>
                            <select name="ue_id" class="form-select form-select-lg" required>
                                <option value="">Sélectionnez une UE pour télécharger le modèle</option>
                                @foreach($uesAssignees as $ue)
                                    <option value="{{ $ue->id }}">
                                        {{ $ue->code }} - {{ $ue->nom }}
                                        @if($ue->filiere)
                                            ({{ $ue->filiere->nom }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Le modèle contiendra la liste des étudiants inscrits dans cette UE
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Le modèle Excel contiendra:
                            </h6>
                            <ul class="mb-0">
                                <li><strong>Colonne A:</strong> Nom complet des étudiants</li>
                                <li><strong>Colonne B:</strong> CNE/Matricule des étudiants</li>
                                <li><strong>Colonne C:</strong> Colonne vide pour saisir les notes</li>
                                <li><strong>Colonne D:</strong> Colonne vide pour marquer les absences</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-file-excel me-1"></i>
                                    Format: Excel (.xlsx) compatible avec Microsoft Excel et LibreOffice
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('enseignant.notes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-download me-2"></i>Télécharger le Modèle
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-list-ol me-2"></i>Instructions d'utilisation
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">
                                <i class="fas fa-edit me-1"></i>Comment remplir le modèle:
                            </h6>
                            <ol class="small">
                                <li><strong>Ne modifiez pas</strong> les colonnes A et B (noms et CNE)</li>
                                <li><strong>Colonne C:</strong> Saisissez les notes sur 20 (ex: 15.5)</li>
                                <li><strong>Colonne D:</strong> Écrivez "absent" pour les étudiants absents</li>
                                <li><strong>Laissez vide</strong> les cellules pour les notes non saisies</li>
                                <li><strong>Sauvegardez</strong> le fichier avant l'import</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">
                                <i class="fas fa-exclamation-triangle me-1"></i>Règles importantes:
                            </h6>
                            <ul class="small">
                                <li>Notes entre <strong>0 et 20</strong> uniquement</li>
                                <li>Utilisez le <strong>point (.)</strong> pour les décimales</li>
                                <li>Pas de virgule (,) pour les décimales</li>
                                <li>Écrivez exactement <strong>"absent"</strong> (en minuscules)</li>
                                <li>Ne supprimez aucune ligne d'étudiant</li>
                            </ul>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-success">
                                <i class="fas fa-check-circle me-1"></i>Exemple de saisie correcte:
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom Etudiant</th>
                                            <th>CNE Etudiant</th>
                                            <th>Note</th>
                                            <th>Statut Absence</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Ahmed BENALI</td>
                                            <td>R123456789</td>
                                            <td>15.5</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Fatima ALAOUI</td>
                                            <td>R987654321</td>
                                            <td>12</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Mohamed TAZI</td>
                                            <td>R456789123</td>
                                            <td></td>
                                            <td>absent</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
