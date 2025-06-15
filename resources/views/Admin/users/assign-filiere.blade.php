@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Assigner une Filière au Coordonnateur</h4>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.coordonnateur.assign-filiere', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="coordonnateur" class="form-label">Coordonnateur</label>
                            <input type="text" class="form-control" id="coordonnateur" value="{{ $user->name }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="filiere_base" class="form-label">Filière</label>
                            <select class="form-select @error('filiere_base') is-invalid @enderror" id="filiere_base" name="filiere_base" required>
                                <option value="">Sélectionner une filière</option>
                                @php
                                    $baseNames = $filieres->map(function($filiere) {
                                        return preg_replace('/\d+$/', '', $filiere->nom);
                                    })->unique();

                                    // Check which base names have any filières already assigned
                                    $assignedBaseNames = $filieres->filter(function($filiere) {
                                        return $filiere->coordonnateurs->count() > 0;
                                    })->map(function($filiere) {
                                        return preg_replace('/\d+$/', '', $filiere->nom);
                                    })->unique();
                                @endphp
                                
                                @foreach($baseNames as $baseName)
                                    <option value="{{ $baseName }}" 
                                        {{ old('filiere_base') == $baseName ? 'selected' : '' }}
                                        {{ $assignedBaseNames->contains($baseName) ? 'disabled' : '' }}>
                                        {{ $baseName }}
                                        @if($assignedBaseNames->contains($baseName))
                                            (Déjà assignée)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_base')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Retour</a>
                            <button type="submit" class="btn btn-primary">Assigner la Filière</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 