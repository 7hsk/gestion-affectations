@extends('layouts.vacataire')

@section('title', 'Gestion des Notes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #7c3aed, #a855f7) !important;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">
                                <i class="fas fa-graduation-cap me-2"></i>Gestion des Notes
                            </h4>
                            <p class="mb-0 opacity-75">Importez et gérez les notes de vos étudiants via Excel</p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-primary fs-6">
                                {{ $notes->total() }} note(s) saisie(s)
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Téléchargez le modèle Excel, remplissez les notes, puis importez le fichier pour une saisie rapide et efficace.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <a href="{{ route('vacataire.notes.download-template-page') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-1"></i>Télécharger Modèle
                                </a>
                                <a href="{{ route('vacataire.notes.import-page') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload me-1"></i>Importer Notes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-book-open fa-2x text-primary"></i>
                    </div>
                    <h5 class="mb-1">{{ $uesAssignees->count() }}</h5>
                    <small class="text-muted">UEs Assignées</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                    <h5 class="mb-1">{{ $notes->where('session_type', 'normale')->count() }}</h5>
                    <small class="text-muted">Notes Normales</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-redo fa-2x text-warning"></i>
                    </div>
                    <h5 class="mb-1">{{ $notes->where('session_type', 'rattrapage')->count() }}</h5>
                    <small class="text-muted">Notes Rattrapage</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-user-times fa-2x text-danger"></i>
                    </div>
                    <h5 class="mb-1">{{ $notes->where('is_absent', true)->count() }}</h5>
                    <small class="text-muted">Absents</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtres de Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('vacataire.notes') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Unité d'Enseignement</label>
                                <select name="ue_id" class="form-select">
                                    <option value="">🔍 Toutes les UEs</option>
                                    @foreach($uesAssignees as $ue)
                                        <option value="{{ $ue->id }}" {{ request('ue_id') == $ue->id ? 'selected' : '' }}>
                                            {{ $ue->code }} - {{ $ue->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Type de Session</label>
                                <select name="session" class="form-select">
                                    <option value="">📋 Toutes les sessions</option>
                                    <option value="normale" {{ request('session') == 'normale' ? 'selected' : '' }}>📝 Session Normale</option>
                                    <option value="rattrapage" {{ request('session') == 'rattrapage' ? 'selected' : '' }}>🔄 Session Rattrapage</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Rechercher
                                    </button>
                                    <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-refresh me-1"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-table me-2"></i>Liste des Notes
                        </h6>
                        <div class="d-flex gap-2">
                            <!-- Export removed - not in allowed list -->
                            <a href="{{ route('vacataire.notes.add-page') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-plus me-1"></i>Ajouter Note
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>UE</th>
                                        <th>Étudiant</th>
                                        <th>Note Normale</th>
                                        <th>Note Rattrapage</th>
                                        <th>Note Finale</th>
                                        <th>Session</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong class="text-primary">{{ $note->ue->code ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($note->ue->nom ?? 'N/A', 30) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $note->etudiant->name ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $note->etudiant->matricule ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($note->note_normale === 'Absent')
                                                    <span class="badge bg-warning fs-6">Absent</span>
                                                @elseif($note->note_normale !== '-')
                                                    <span class="badge bg-{{ $note->note_normale >= 10 ? 'success' : 'danger' }} fs-6">
                                                        {{ $note->note_normale }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($note->note_rattrapage === 'Absent')
                                                    <span class="badge bg-warning fs-6">Absent</span>
                                                @elseif($note->note_rattrapage !== '-')
                                                    <span class="badge bg-{{ $note->note_rattrapage >= 10 ? 'success' : 'danger' }} fs-6">
                                                        {{ $note->note_rattrapage }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($note->status === 'Validé')
                                                    <span class="badge bg-success fs-6">{{ $note->note_finale }}</span>
                                                @else
                                                    <span class="badge bg-danger fs-6">{{ $note->note_finale }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $note->session }}</span>
                                            </td>
                                            <td>
                                                @if($note->status === 'Validé')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Validé
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-times me-1"></i>Non Validé
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($note->normale_note_obj)
                                                        <a href="{{ route('vacataire.notes.edit-page', $note->normale_note_obj->id) }}"
                                                           class="btn btn-outline-primary"
                                                           title="Modifier note normale">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if($note->rattrapage_note_obj)
                                                        <a href="{{ route('vacataire.notes.edit-page', $note->rattrapage_note_obj->id) }}"
                                                           class="btn btn-outline-warning"
                                                           title="Modifier note rattrapage">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if($note->normale_note_obj)
                                                        <button class="btn btn-outline-danger"
                                                                onclick="deleteNote({{ $note->normale_note_obj->id }})"
                                                                title="Supprimer note">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-graduation-cap fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-3">Aucune note trouvée</h5>
                            <p class="text-muted mb-4">
                                @if(request()->hasAny(['ue_id', 'session']))
                                    Aucune note ne correspond à vos critères de recherche.
                                @else
                                    Vous n'avez pas encore saisi de notes pour vos étudiants.
                                @endif
                            </p>
                            <div class="d-flex gap-2 justify-content-center">
                                @if(request()->hasAny(['ue_id', 'session']))
                                    <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-times me-1"></i>Réinitialiser les filtres
                                    </a>
                                @endif
                                <a href="{{ route('vacataire.notes.add-page') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Ajouter une note
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($notes->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $notes->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modals removed - using separate blade views instead -->

@push('styles')
<style>
/* Fix modal z-index issues */
.modal {
    z-index: 9999 !important;
}

.modal-backdrop {
    z-index: 9998 !important;
}

.modal-dialog {
    z-index: 10000 !important;
    position: relative;
}

.modal-content {
    z-index: 10001 !important;
    position: relative;
}

/* Ensure form elements are clickable */
.modal-body input,
.modal-body select,
.modal-body textarea,
.modal-body button {
    z-index: 10002 !important;
    position: relative;
}

/* Fix any overlay issues */
.modal.show {
    display: block !important;
    z-index: 9999 !important;
}

.modal.show .modal-dialog {
    transform: none !important;
    z-index: 10000 !important;
}

/* Ensure dropdown menus work in modals */
.modal .dropdown-menu {
    z-index: 10003 !important;
}

/* Fix select2 or other plugin dropdowns if used */
.select2-container {
    z-index: 10004 !important;
}

.select2-dropdown {
    z-index: 10005 !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 VACATAIRE NOTES PAGE INITIALIZED');
    console.log('💜 Total notes:', {{ $notes->total() }});

    // Fix modal z-index issues
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.style.zIndex = '9999';

        // Ensure modal shows properly
        modal.addEventListener('show.bs.modal', function() {
            this.style.zIndex = '9999';
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '9998';
            }
        });

        // Fix modal dialog positioning
        modal.addEventListener('shown.bs.modal', function() {
            const dialog = this.querySelector('.modal-dialog');
            if (dialog) {
                dialog.style.zIndex = '10000';
                dialog.style.position = 'relative';
            }

            const content = this.querySelector('.modal-content');
            if (content) {
                content.style.zIndex = '10001';
                content.style.position = 'relative';
            }
        });
    });

    // Ensure form elements are accessible
    const formElements = document.querySelectorAll('.modal input, .modal select, .modal textarea, .modal button');
    formElements.forEach(element => {
        element.style.zIndex = '10002';
        element.style.position = 'relative';
    });
});

// Modal functions removed - using separate blade views instead

function deleteNote(noteId) {
    console.log('🗑️ DELETE NOTE CLICKED:', noteId);

    if (confirm('Êtes-vous sûr de vouloir supprimer cette note ? Cette action est irréversible.')) {
        // Show loading notification
        showNotification('Suppression en cours...', 'info');

        // Create form to submit delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/vacataire/notes/${noteId}/delete`;

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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

// Export function removed - not in allowed list

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

    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-of-type');
        if (alert) alert.remove();
    }, 5000);
}
</script>
@endpush
@endsection
