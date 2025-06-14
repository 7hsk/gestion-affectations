@extends('layouts.enseignant')

@section('title', 'Modifier Note')

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
                                <i class="fas fa-edit me-2"></i>Modifier Note
                            </h4>
                            <p class="mb-0 opacity-75">Modifiez la note de {{ $note->etudiant->name ?? 'l\'étudiant' }}</p>
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
                            <span class="text-primary">{{ $note->uniteEnseignement->code ?? 'N/A' }}</span><br>
                            <small class="text-muted">{{ $note->uniteEnseignement->nom ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-3">
                            <strong>Étudiant:</strong><br>
                            <span class="text-success">{{ $note->etudiant->name ?? 'N/A' }}</span><br>
                            <small class="text-muted">{{ $note->etudiant->matricule ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-3">
                            <strong>Session:</strong><br>
                            <span class="badge bg-info">
                                {{ $note->session_type === 'normale' ? '📝 Session Normale' : '🔄 Session Rattrapage' }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Note Actuelle:</strong><br>
                            @if($note->is_absent)
                                <span class="badge bg-warning fs-6">Absent</span>
                            @else
                                <span class="badge bg-{{ $note->note >= 10 ? 'success' : 'danger' }} fs-6">
                                    {{ $note->note }}/20
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier la Note
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enseignant.notes.update', $note->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-1"></i>Nouvelle Note (/20)
                                </label>
                                <input type="number" name="note" class="form-control form-control-lg" 
                                       min="0" max="20" step="0.25" 
                                       value="{{ $note->is_absent ? '' : $note->note }}"
                                       placeholder="Ex: 15.5">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Note sur 20 (laissez vide si étudiant absent)
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user-times me-1"></i>Statut de Présence
                                </label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_absent" 
                                               id="present" value="0" {{ !$note->is_absent ? 'checked' : '' }}>
                                        <label class="form-check-label" for="present">
                                            <i class="fas fa-check text-success me-1"></i>Présent
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_absent" 
                                               id="absent" value="1" {{ $note->is_absent ? 'checked' : '' }}>
                                        <label class="form-check-label" for="absent">
                                            <i class="fas fa-times text-danger me-1"></i>Absent
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Attention:
                            </h6>
                            <ul class="mb-0">
                                <li>Cette modification remplacera définitivement la note actuelle</li>
                                <li>L'action sera enregistrée dans l'historique du système</li>
                                <li>Si vous marquez l'étudiant comme absent, la note sera effacée</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Dernière modification: {{ $note->updated_at->format('d/m/Y à H:i') }}
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('enseignant.notes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-save me-2"></i>Modifier la Note
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Zone Dangereuse
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-danger mb-1">Supprimer cette note</h6>
                            <p class="text-muted mb-0">
                                Cette action est irréversible. La note sera définitivement supprimée du système.
                            </p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle absence checkbox logic
    const presentRadio = document.getElementById('present');
    const absentRadio = document.getElementById('absent');
    const noteInput = document.querySelector('input[name="note"]');
    
    function toggleNoteInput() {
        if (absentRadio.checked) {
            noteInput.disabled = true;
            noteInput.value = '';
            noteInput.placeholder = 'Note non requise (étudiant absent)';
        } else {
            noteInput.disabled = false;
            noteInput.placeholder = 'Ex: 15.5';
        }
    }
    
    presentRadio.addEventListener('change', toggleNoteInput);
    absentRadio.addEventListener('change', toggleNoteInput);
    
    // Initial state
    toggleNoteInput();
});

function confirmDelete() {
    if (confirm('Êtes-vous absolument sûr de vouloir supprimer cette note ?\n\nCette action est irréversible et la note sera définitivement supprimée du système.')) {
        // Create form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("enseignant.notes.delete", $note->id) }}';

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
