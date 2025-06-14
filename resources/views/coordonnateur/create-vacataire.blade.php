@extends('layouts.coordonnateur')

@section('title', 'Créer un Compte Vacataire')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3" style="background: linear-gradient(135deg, #059669, #10b981);">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-user-plus me-2"></i>Créer un Nouveau Vacataire
                </h6>
                <a href="{{ route('coordonnateur.vacataires') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux Vacataires
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('coordonnateur.vacataires.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="form-label">Nom Complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password" class="form-label">Mot de Passe</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirmer le Mot de Passe</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="departement_id" class="form-label">Département</label>
                            <select class="form-select @error('departement_id') is-invalid @enderror" 
                                    id="departement_id" name="departement_id" required>
                                <option value="">Sélectionner un département</option>
                                @foreach($departements as $departement)
                                    <option value="{{ $departement->id }}" 
                                            {{ old('departement_id') == $departement->id ? 'selected' : '' }}>
                                        {{ $departement->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('departement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Spécialités (optionnel)</label>
                            <div class="card">
                                <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($specialites as $specialite)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   name="specialite[]"
                                                   id="specialite_{{ $loop->index }}"
                                                   value="{{ $specialite }}"
                                                {{ in_array($specialite, old('specialite', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="specialite_{{ $loop->index }}">
                                                {{ $specialite }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('specialite')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Role Information -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> Le compte sera créé avec le rôle <strong>"Vacataire"</strong> automatiquement.
                            Vous pouvez sélectionner plusieurs spécialités pour le vacataire. Le vacataire pourra se connecter et accéder à ses fonctionnalités dédiées.
                        </div>
                    </div>
                </div>

                <!-- Hidden role field -->
                <input type="hidden" name="role" value="vacataire">

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus me-2"></i>Créer le Compte Vacataire
                    </button>
                    <a href="{{ route('coordonnateur.vacataires') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactive feedback
    const form = document.querySelector('form');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...';
        submitBtn.disabled = true;
    });
    
    // Password confirmation validation
    const password = document.getElementById('password');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    function validatePasswords() {
        if (password.value !== passwordConfirmation.value) {
            passwordConfirmation.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            passwordConfirmation.setCustomValidity('');
        }
    }
    
    password.addEventListener('input', validatePasswords);
    passwordConfirmation.addEventListener('input', validatePasswords);
});
</script>
@endsection
