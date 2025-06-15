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
                            <label for="filiere_id" class="form-label">Filière</label>
                            <select class="form-select @error('filiere_id') is-invalid @enderror" id="filiere_id" name="filiere_id" required>
                                <option value="">Sélectionner une filière</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" 
                                        {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}
                                        {{ $filiere->coordonnateurs->count() > 0 ? 'disabled' : '' }}>
                                        {{ $filiere->nom }}
                                        @if($filiere->coordonnateurs->count() > 0)
                                            (Déjà assignée)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
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