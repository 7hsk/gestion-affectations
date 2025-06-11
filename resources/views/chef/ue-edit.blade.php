@extends('layouts.chef')

@section('title', 'Modifier UE')

@push('styles')
<style>
.form-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
}

.form-control {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    outline: none;
}

.form-select {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    outline: none;
}

.btn-save {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
}

.btn-cancel {
    background: #6b7280;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1.1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-cancel:hover {
    background: #4b5563;
    color: white;
    text-decoration: none;
}

.hours-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.groups-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.checkbox-group {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.checkbox-item:hover {
    border-color: #dc2626;
    background: #fef2f2;
}

.checkbox-item input[type="checkbox"] {
    margin: 0;
}

.checkbox-item.checked {
    border-color: #dc2626;
    background: #fef2f2;
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .hours-grid {
        grid-template-columns: 1fr;
    }
    
    .groups-grid {
        grid-template-columns: 1fr;
    }
    
    .checkbox-group {
        flex-direction: column;
    }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-edit me-2"></i>Modifier l'UE
            </h2>
            <p class="text-muted mb-0">Modifiez les informations de l'unité d'enseignement</p>
        </div>
        <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-cancel">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        <form action="{{ route('chef.unites-enseignement.update', $ue->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code" class="form-label">Code UE *</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" value="{{ old('code', $ue->code) }}" required>
                        @error('code')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nom" class="form-label">Nom de l'UE *</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom', $ue->nom) }}" required>
                        @error('nom')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Hours Configuration -->
                <div class="col-12">
                    <label class="form-label">Volume Horaire *</label>
                    <div class="hours-grid">
                        <div class="form-group">
                            <label for="heures_cm" class="form-label">Heures CM</label>
                            <input type="number" class="form-control @error('heures_cm') is-invalid @enderror" 
                                   id="heures_cm" name="heures_cm" value="{{ old('heures_cm', $ue->heures_cm) }}" 
                                   min="0" max="100" required>
                            @error('heures_cm')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="heures_td" class="form-label">Heures TD</label>
                            <input type="number" class="form-control @error('heures_td') is-invalid @enderror" 
                                   id="heures_td" name="heures_td" value="{{ old('heures_td', $ue->heures_td) }}" 
                                   min="0" max="100" required>
                            @error('heures_td')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="heures_tp" class="form-label">Heures TP</label>
                            <input type="number" class="form-control @error('heures_tp') is-invalid @enderror" 
                                   id="heures_tp" name="heures_tp" value="{{ old('heures_tp', $ue->heures_tp) }}" 
                                   min="0" max="100" required>
                            @error('heures_tp')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @error('heures')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Groups Configuration -->
                <div class="col-12">
                    <label class="form-label">Nombre de Groupes</label>
                    <div class="groups-grid">
                        <div class="form-group">
                            <label for="groupes_td" class="form-label">Groupes TD</label>
                            <input type="number" class="form-control @error('groupes_td') is-invalid @enderror" 
                                   id="groupes_td" name="groupes_td" value="{{ old('groupes_td', $ue->groupes_td) }}" 
                                   min="0" max="20" required>
                            @error('groupes_td')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="groupes_tp" class="form-label">Groupes TP</label>
                            <input type="number" class="form-control @error('groupes_tp') is-invalid @enderror" 
                                   id="groupes_tp" name="groupes_tp" value="{{ old('groupes_tp', $ue->groupes_tp) }}" 
                                   min="0" max="20" required>
                            @error('groupes_tp')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="semestre" class="form-label">Semestre *</label>
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
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="annee_universitaire" class="form-label">Année Universitaire *</label>
                        <input type="text" class="form-control @error('annee_universitaire') is-invalid @enderror" 
                               id="annee_universitaire" name="annee_universitaire" 
                               value="{{ old('annee_universitaire', $ue->annee_universitaire) }}" 
                               placeholder="2024-2025" required>
                        @error('annee_universitaire')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Filiere and Responsable -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="filiere_id" class="form-label">Filière *</label>
                        <select class="form-select @error('filiere_id') is-invalid @enderror" 
                                id="filiere_id" name="filiere_id" required>
                            <option value="">Sélectionner une filière</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ old('filiere_id', $ue->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('filiere_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="responsable_id" class="form-label">Responsable</label>
                        <select class="form-select @error('responsable_id') is-invalid @enderror" 
                                id="responsable_id" name="responsable_id">
                            <option value="">Aucun responsable</option>
                            @foreach($responsables as $responsable)
                                <option value="{{ $responsable->id }}" {{ old('responsable_id', $ue->responsable_id) == $responsable->id ? 'selected' : '' }}>
                                    {{ $responsable->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('responsable_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Specialite -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="specialite" class="form-label">Spécialité</label>
                        <select class="form-select @error('specialite') is-invalid @enderror" 
                                id="specialite" name="specialite">
                            <option value="">Aucune spécialité</option>
                            @foreach($specialites as $spec)
                                <option value="{{ $spec }}" {{ old('specialite', $ue->specialite) == $spec ? 'selected' : '' }}>
                                    {{ $spec }}
                                </option>
                            @endforeach
                        </select>
                        @error('specialite')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Vacataire Options -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Disponible pour Vacataires</label>
                        <div class="checkbox-group">
                            @foreach(['CM', 'TD', 'TP'] as $type)
                                <div class="checkbox-item">
                                    <input type="checkbox" name="vacataire_types[]" value="{{ $type }}" 
                                           id="vacataire_{{ $type }}"
                                           {{ in_array($type, old('vacataire_types', $ue->vacataire_types ?? [])) ? 'checked' : '' }}>
                                    <label for="vacataire_{{ $type }}">{{ $type }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Description de l'unité d'enseignement...">{{ old('description', $ue->description) }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-end gap-3 mt-4">
                <a href="{{ route('chef.unites-enseignement') }}" class="btn btn-cancel">
                    <i class="fas fa-times me-2"></i>Annuler
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save me-2"></i>Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle checkbox styling
    const checkboxItems = document.querySelectorAll('.checkbox-item');
    checkboxItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        
        // Initial state
        if (checkbox.checked) {
            item.classList.add('checked');
        }
        
        // Handle clicks
        item.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                checkbox.checked = !checkbox.checked;
            }
            
            if (checkbox.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        });
        
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                item.classList.add('checked');
            } else {
                item.classList.remove('checked');
            }
        });
    });
});
</script>
@endpush
@endsection
