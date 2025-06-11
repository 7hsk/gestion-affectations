@extends('layouts.coordonnateur')

@section('title', 'Créer une Unité d\'Enseignement')

@push('styles')
<style>
.create-ue-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.main-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(5, 150, 105, 0.1);
    overflow: hidden;
}

.card-header-custom {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 2rem;
    text-align: center;
}

.form-section {
    padding: 2rem;
}

.section-title {
    color: #059669;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e2e8f0;
}

.form-floating label {
    color: #6b7280;
}

.form-control:focus, .form-select:focus {
    border-color: #059669;
    box-shadow: 0 0 0 0.2rem rgba(5, 150, 105, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #047857, #059669);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(5, 150, 105, 0.3);
}

.btn-outline-secondary {
    border-color: #6b7280;
    color: #6b7280;
    border-radius: 10px;
    padding: 0.75rem 2rem;
    font-weight: 600;
}

.btn-outline-secondary:hover {
    background-color: #6b7280;
    border-color: #6b7280;
}

.import-section {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border: 2px dashed #059669;
    border-radius: 15px;
    padding: 2rem;
    margin-top: 2rem;
    text-align: center;
}

.import-icon {
    font-size: 3rem;
    color: #059669;
    margin-bottom: 1rem;
}

.volume-horaire-summary {
    background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
    border-radius: 15px;
    padding: 1.5rem;
    margin-top: 1rem;
    border: 1px solid #0ea5e9;
}

.hour-input {
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
}

.total-hours {
    font-size: 1.5rem;
    font-weight: 700;
    color: #059669;
}

.specialite-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.specialite-tag {
    background: #e2e8f0;
    color: #475569;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.specialite-tag:hover {
    background: #059669;
    color: white;
}

.csv-format-info {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
    font-size: 0.9rem;
}

.form-check {
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.form-check:hover {
    background-color: #f0f9ff;
    border-color: #059669;
}

.form-check-input:checked {
    background-color: #059669;
    border-color: #059669;
}

.form-check-input:focus {
    border-color: #059669;
    box-shadow: 0 0 0 0.2rem rgba(5, 150, 105, 0.25);
}

.specialites-container {
    transition: border-color 0.3s ease;
}

.specialites-container:hover {
    border-color: #059669 !important;
}

/* Disabled semester field styling */
.form-select:disabled {
    background-color: #f8f9fa;
    opacity: 0.6;
    cursor: not-allowed;
    border-color: #dee2e6;
}

.form-select:disabled:hover {
    border-color: #dee2e6;
}

/* Enhanced transition for semester field activation */
.form-select {
    transition: all 0.3s ease, transform 0.2s ease;
}

/* Visual indicator for field dependency */
.field-dependency-indicator {
    font-size: 0.75rem;
    color: #6b7280;
    font-style: italic;
}
</style>
@endpush

@section('content')
<div class="create-ue-container">
    <div class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Créer une Unité d'Enseignement</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('coordonnateur.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('coordonnateur.unites-enseignement') }}">Unités d'Enseignement</a></li>
                        <li class="breadcrumb-item active">Créer UE</li>
                    </ol>
                </nav>
            </div>
            <div>
                <a href="{{ route('coordonnateur.unites-enseignement') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Main Creation Form -->
                <div class="main-card">
                    <div class="card-header-custom">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Descriptif de l'Unité d'Enseignement
                        </h4>
                        <p class="mb-0 mt-2 opacity-75">Saisissez les informations complètes de l'UE selon le cahier des charges</p>
                    </div>

                    <form action="{{ route('coordonnateur.unites-enseignement.creer') }}" method="POST">
                        @csrf
                        
                        <!-- Section 1: Informations Générales -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-info-circle me-2"></i>Informations Générales
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                               id="nom" name="nom" value="{{ old('nom') }}" required>
                                        <label for="nom">Nom de l'Unité d'Enseignement *</label>
                                        @error('nom')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                               id="code" name="code" value="{{ old('code') }}" required>
                                        <label for="code">Code UE *</label>
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" style="color: #059669; font-weight: 600;">
                                            <i class="fas fa-tags me-2"></i>Spécialités du module
                                        </label>
                                        <div class="specialites-container" style="max-height: 200px; overflow-y: auto; border: 2px solid #e2e8f0; border-radius: 10px; padding: 1rem; background: #f8fafc;">
                                            @if($allSpecialites->count() > 0)
                                                @foreach($allSpecialites as $specialite)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox"
                                                               name="specialite[]" value="{{ $specialite }}"
                                                               id="specialite_{{ $loop->index }}"
                                                               {{ in_array($specialite, old('specialite', [])) ? 'checked' : '' }}
                                                               style="border-color: #059669;">
                                                        <label class="form-check-label" for="specialite_{{ $loop->index }}">
                                                            {{ $specialite }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted mb-0">Aucune spécialité disponible</p>
                                            @endif
                                        </div>
                                        @error('specialite')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">Sélectionnez une ou plusieurs spécialités pour cette UE</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('responsable_id') is-invalid @enderror" 
                                                id="responsable_id" name="responsable_id">
                                            <option value="">Aucun responsable assigné</option>
                                            @foreach($responsables as $responsable)
                                                <option value="{{ $responsable->id }}" 
                                                        {{ old('responsable_id') == $responsable->id ? 'selected' : '' }}>
                                                    {{ $responsable->name }} ({{ $responsable->specialite ?? 'Générale' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="responsable_id">Responsable du Module</label>
                                        @error('responsable_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Volume Horaire -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock me-2"></i>Volume Horaire
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control hour-input @error('heures_cm') is-invalid @enderror" 
                                               id="heures_cm" name="heures_cm" value="{{ old('heures_cm', 0) }}" 
                                               min="0" onchange="calculateTotal()">
                                        <label for="heures_cm">Heures CM *</label>
                                        @error('heures_cm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control hour-input @error('heures_td') is-invalid @enderror" 
                                               id="heures_td" name="heures_td" value="{{ old('heures_td', 0) }}" 
                                               min="0" onchange="calculateTotal()">
                                        <label for="heures_td">Heures TD *</label>
                                        @error('heures_td')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control hour-input @error('heures_tp') is-invalid @enderror" 
                                               id="heures_tp" name="heures_tp" value="{{ old('heures_tp', 0) }}" 
                                               min="0" onchange="calculateTotal()">
                                        <label for="heures_tp">Heures TP *</label>
                                        @error('heures_tp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="volume-horaire-summary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Volume Horaire Total:</span>
                                    <span class="total-hours" id="total-hours">0h</span>
                                </div>
                                @error('heures')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section 3: Organisation Pédagogique -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-graduation-cap me-2"></i>Organisation Pédagogique
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('filiere_id') is-invalid @enderror"
                                                id="filiere_id" name="filiere_id" required>
                                            <option value="">Choisir la filière</option>
                                            @foreach($filieres as $filiere)
                                                <option value="{{ $filiere->id }}"
                                                        {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                                    {{ $filiere->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="filiere_id">Filière *</label>
                                        @error('filiere_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('semestre') is-invalid @enderror"
                                                id="semestre" name="semestre" required disabled>
                                            <option value="">Sélectionnez d'abord une filière</option>
                                            <option value="S1" {{ old('semestre') == 'S1' ? 'selected' : '' }}>Semestre 1</option>
                                            <option value="S2" {{ old('semestre') == 'S2' ? 'selected' : '' }}>Semestre 2</option>
                                            <option value="S3" {{ old('semestre') == 'S3' ? 'selected' : '' }}>Semestre 3</option>
                                            <option value="S4" {{ old('semestre') == 'S4' ? 'selected' : '' }}>Semestre 4</option>
                                            <option value="S5" {{ old('semestre') == 'S5' ? 'selected' : '' }}>Semestre 5</option>
                                            <option value="S6" {{ old('semestre') == 'S6' ? 'selected' : '' }}>Semestre 6</option>
                                        </select>
                                        <label for="semestre" id="semestre-label">Semestre * <small class="text-muted">(Choisir filière d'abord)</small></label>
                                        @error('semestre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('groupes_td') is-invalid @enderror" 
                                               id="groupes_td" name="groupes_td" value="{{ old('groupes_td', 1) }}" 
                                               min="0" required>
                                        <label for="groupes_td">Nombre de groupes TD *</label>
                                        @error('groupes_td')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control @error('groupes_tp') is-invalid @enderror" 
                                               id="groupes_tp" name="groupes_tp" value="{{ old('groupes_tp', 1) }}" 
                                               min="0" required>
                                        <label for="groupes_tp">Nombre de groupes TP *</label>
                                        @error('groupes_tp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Description (Optional) -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-file-alt me-2"></i>Description (Optionnel)
                            </h5>
                            
                            <div class="form-floating mb-3">
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" style="height: 120px;">{{ old('description') }}</textarea>
                                <label for="description">Description du contenu de l'UE</label>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-section">
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('coordonnateur.unites-enseignement') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Créer l'UE
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Import Section -->
                <div class="main-card">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="fas fa-upload me-2"></i>Importation en Lot
                        </h5>
                    </div>
                    
                    <div class="form-section">
                        <div class="import-section">
                            <div class="import-icon">
                                <i class="fas fa-file-csv"></i>
                            </div>
                            <h6 class="mb-3">Importer depuis un fichier CSV</h6>
                            <p class="text-muted mb-3">Importez plusieurs UEs en une seule fois</p>
                            
                            <form action="{{ route('coordonnateur.unites-enseignement.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="import_filiere_id" name="filiere_id" required>
                                        <option value="">Choisir la filière</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                                        @endforeach
                                    </select>
                                    <label for="import_filiere_id">Filière pour l'import</label>
                                </div>
                                
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="file" name="file" 
                                           accept=".csv,.xlsx,.xls" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-2"></i>Importer
                                </button>
                            </form>
                            
                            <div class="csv-format-info">
                                <strong>Format CSV attendu:</strong><br>
                                <small>
                                    Nom, Code, Spécialité, CM, TD, TP, Semestre, Groupes_TD, Groupes_TP<br>
                                    <em>Exemple: "Algorithmique", "M101", "Informatique", 26, 16, 18, "S1", 2, 2</em>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const cm = parseInt(document.getElementById('heures_cm').value) || 0;
    const td = parseInt(document.getElementById('heures_td').value) || 0;
    const tp = parseInt(document.getElementById('heures_tp').value) || 0;
    const total = cm + td + tp;

    document.getElementById('total-hours').textContent = total + 'h';
}

// Smart semester filtering based on filière name
function filterSemesters() {
    const filiereSelect = document.getElementById('filiere_id');
    const semestreSelect = document.getElementById('semestre');
    const semestreLabel = document.getElementById('semestre-label');
    const selectedOption = filiereSelect.options[filiereSelect.selectedIndex];

    if (!selectedOption.value) {
        // Disable semester select if no filière selected
        disableSemestreSelect();
        return;
    }

    // Enable semester select since a filière is selected
    semestreSelect.disabled = false;
    semestreSelect.style.opacity = '1';
    semestreSelect.style.cursor = 'pointer';

    const filiereName = selectedOption.text.trim();

    // Hide all semester options first (except the default)
    const semestreOptions = semestreSelect.querySelectorAll('option');
    semestreOptions.forEach(option => {
        if (option.value === '') {
            option.textContent = 'Choisir le semestre';
            return;
        }
        option.style.display = 'none';
        option.disabled = true;
    });

    // Show relevant semesters based on filière name pattern
    let allowedSemesters = [];

    // Extract the number from filière name (e.g., GI1, GI2, GI3, ID1, etc.)
    const match = filiereName.match(/(\d+)$/);
    if (match) {
        const level = parseInt(match[1]);

        switch(level) {
            case 1:
                allowedSemesters = ['S1', 'S2'];
                break;
            case 2:
                allowedSemesters = ['S3', 'S4'];
                break;
            case 3:
                allowedSemesters = ['S5'];
                break;
            default:
                // If level is not 1, 2, or 3, show all semesters
                allowedSemesters = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];
        }
    } else {
        // If no number found, show all semesters
        allowedSemesters = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];
    }

    // Show and enable allowed semesters
    allowedSemesters.forEach(semester => {
        const option = semestreSelect.querySelector(`option[value="${semester}"]`);
        if (option) {
            option.style.display = 'block';
            option.disabled = false;
        }
    });

    // Clear current selection if it's not in allowed semesters
    if (semestreSelect.value && !allowedSemesters.includes(semestreSelect.value)) {
        semestreSelect.value = '';
    }

    // Add visual feedback with cool styling
    if (allowedSemesters.length < 6) {
        semestreLabel.innerHTML = `Semestre * <small class="text-success fw-bold">(${allowedSemesters.join(', ')} disponibles pour ${filiereName})</small>`;
    } else {
        semestreLabel.innerHTML = 'Semestre *';
    }

    // Add a subtle animation to indicate the field is now active
    semestreSelect.style.transform = 'scale(1.02)';
    setTimeout(() => {
        semestreSelect.style.transform = 'scale(1)';
    }, 200);
}

function disableSemestreSelect() {
    const semestreSelect = document.getElementById('semestre');
    const semestreLabel = document.getElementById('semestre-label');
    const semestreOptions = semestreSelect.querySelectorAll('option');

    // Disable the select
    semestreSelect.disabled = true;
    semestreSelect.style.opacity = '0.6';
    semestreSelect.style.cursor = 'not-allowed';
    semestreSelect.value = '';

    // Reset the default option text
    const defaultOption = semestreSelect.querySelector('option[value=""]');
    if (defaultOption) {
        defaultOption.textContent = 'Sélectionnez d\'abord une filière';
    }

    // Hide all semester options except default
    semestreOptions.forEach(option => {
        if (option.value === '') return;
        option.style.display = 'none';
        option.disabled = true;
    });

    // Reset label
    semestreLabel.innerHTML = 'Semestre * <small class="text-muted">(Choisir filière d\'abord)</small>';
}

function showAllSemesters() {
    const semestreSelect = document.getElementById('semestre');
    const semestreLabel = document.getElementById('semestre-label');
    const semestreOptions = semestreSelect.querySelectorAll('option');

    // Enable the select
    semestreSelect.disabled = false;
    semestreSelect.style.opacity = '1';
    semestreSelect.style.cursor = 'pointer';

    // Show all semester options
    semestreOptions.forEach(option => {
        if (option.value === '') {
            option.textContent = 'Choisir le semestre';
            return;
        }
        option.style.display = 'block';
        option.disabled = false;
    });

    semestreLabel.innerHTML = 'Semestre *';
}

// Enhanced checkbox styling for specialities
function enhanceSpecialiteCheckboxes() {
    const checkboxes = document.querySelectorAll('input[name="specialite[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.parentElement;
            if (this.checked) {
                label.style.backgroundColor = '#dcfce7';
                label.style.borderColor = '#059669';
                label.style.color = '#047857';
                label.style.fontWeight = '600';
            } else {
                label.style.backgroundColor = 'transparent';
                label.style.borderColor = 'transparent';
                label.style.color = 'inherit';
                label.style.fontWeight = 'normal';
            }
        });

        // Apply initial styling for checked items
        if (checkbox.checked) {
            const label = checkbox.parentElement;
            label.style.backgroundColor = '#dcfce7';
            label.style.borderColor = '#059669';
            label.style.color = '#047857';
            label.style.fontWeight = '600';
        }
    });
}

// Initialize everything on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
    enhanceSpecialiteCheckboxes();

    // Add event listener for filière change
    const filiereSelect = document.getElementById('filiere_id');
    filiereSelect.addEventListener('change', filterSemesters);

    // Initialize semester field state based on filière selection
    if (filiereSelect.value) {
        // If a filière is already selected (e.g., from old input), apply filtering
        filterSemesters();
    } else {
        // If no filière selected, ensure semester field is disabled
        disableSemestreSelect();
    }

    // Add smooth transitions to form elements
    const formControls = document.querySelectorAll('.form-control, .form-select');
    formControls.forEach(control => {
        control.style.transition = 'all 0.3s ease';
    });

    // Add special styling for disabled semester field
    const semestreSelect = document.getElementById('semestre');
    if (semestreSelect.disabled) {
        semestreSelect.style.opacity = '0.6';
        semestreSelect.style.cursor = 'not-allowed';
    }
});
</script>
@endsection
