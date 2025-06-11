@extends('layouts.vacataire')

@section('title', 'Gestion des Notes')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Gestion des Notes
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-success">
                                {{ $notes->total() }} note(s)
                            </span>
                            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                <i class="fas fa-upload me-1"></i>Importer Notes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Gérez les notes de vos étudiants pour les sessions normale et rattrapage.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('vacataire.notes') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-book me-1"></i>Unité d'Enseignement
                            </label>
                            <select name="ue_id" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes les UEs</option>
                                @foreach($uesAssignees as $ue)
                                    <option value="{{ $ue->id }}" {{ request('ue_id') == $ue->id ? 'selected' : '' }}>
                                        {{ $ue->code }} - {{ $ue->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-calendar me-1"></i>Session
                            </label>
                            <select name="session" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes les sessions</option>
                                <option value="normale" {{ request('session') == 'normale' ? 'selected' : '' }}>Session Normale</option>
                                <option value="rattrapage" {{ request('session') == 'rattrapage' ? 'selected' : '' }}>Session Rattrapage</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Réinitialiser
                                </a>
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
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                <i class="fas fa-plus me-1"></i>Ajouter Note
                            </button>
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
                                                    <strong class="text-primary">{{ $note->uniteEnseignement->code }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $note->uniteEnseignement->nom }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $note->etudiant->nom ?? 'N/A' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $note->etudiant->matricule ?? 'N/A' }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($note->note_normale !== null)
                                                    <span class="badge bg-{{ $note->note_normale >= 10 ? 'success' : 'danger' }} fs-6">
                                                        {{ number_format($note->note_normale, 2) }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($note->note_rattrapage !== null)
                                                    <span class="badge bg-{{ $note->note_rattrapage >= 10 ? 'success' : 'danger' }} fs-6">
                                                        {{ number_format($note->note_rattrapage, 2) }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $noteFinale = max($note->note_normale ?? 0, $note->note_rattrapage ?? 0);
                                                @endphp
                                                <span class="badge bg-{{ $noteFinale >= 10 ? 'success' : 'danger' }} fs-6">
                                                    {{ number_format($noteFinale, 2) }}/20
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($note->session ?? 'normale') }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $noteFinale = max($note->note_normale ?? 0, $note->note_rattrapage ?? 0);
                                                @endphp
                                                @if($noteFinale >= 10)
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
                                                    <button class="btn btn-outline-primary" 
                                                            onclick="editNote({{ $note->id }})"
                                                            title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" 
                                                            onclick="viewNoteHistory({{ $note->id }})"
                                                            title="Historique">
                                                        <i class="fas fa-history"></i>
                                                    </button>
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
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                    <i class="fas fa-plus me-1"></i>Ajouter une note
                                </button>
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

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Importer des Notes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Unité d'Enseignement</label>
                        <select name="ue_id" class="form-select" required>
                            <option value="">Sélectionner une UE</option>
                            @foreach($uesAssignees as $ue)
                                <option value="{{ $ue->id }}">{{ $ue->code }} - {{ $ue->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Session</label>
                        <select name="session" class="form-select" required>
                            <option value="normale">Session Normale</option>
                            <option value="rattrapage">Session Rattrapage</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fichier Excel/CSV</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            Formats acceptés: Excel (.xlsx, .xls) ou CSV (.csv)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Format requis:</strong> Le fichier doit contenir les colonnes: Matricule, Nom, Note
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="uploadNotes()">
                    <i class="fas fa-upload me-1"></i>Importer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Ajouter une Note
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addNoteForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Unité d'Enseignement</label>
                        <select name="ue_id" class="form-select" required>
                            <option value="">Sélectionner une UE</option>
                            @foreach($uesAssignees as $ue)
                                <option value="{{ $ue->id }}">{{ $ue->code }} - {{ $ue->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Matricule Étudiant</label>
                        <input type="text" name="matricule" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nom Étudiant</label>
                        <input type="text" name="nom_etudiant" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Note Normale (/20)</label>
                                <input type="number" name="note_normale" class="form-control" 
                                       min="0" max="20" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Note Rattrapage (/20)</label>
                                <input type="number" name="note_rattrapage" class="form-control" 
                                       min="0" max="20" step="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Session</label>
                        <select name="session" class="form-select" required>
                            <option value="normale">Session Normale</option>
                            <option value="rattrapage">Session Rattrapage</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveNote()">
                    <i class="fas fa-save me-1"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 VACATAIRE NOTES PAGE INITIALIZED');
    console.log('💜 Total notes:', {{ $notes->total() }});
});

function uploadNotes() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importation...';
    btn.disabled = true;
    
    // Simulate upload (replace with actual endpoint)
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
        
        // Show success message
        showNotification('Notes importées avec succès!', 'success');
        
        // Reload page
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }, 2000);
}

function saveNote() {
    const form = document.getElementById('addNoteForm');
    const formData = new FormData(form);
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Enregistrement...';
    btn.disabled = true;
    
    // Simulate save (replace with actual endpoint)
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        bootstrap.Modal.getInstance(document.getElementById('addNoteModal')).hide();
        
        // Show success message
        showNotification('Note ajoutée avec succès!', 'success');
        
        // Reload page
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    }, 1000);
}

function editNote(noteId) {
    console.log('Editing note:', noteId);
    showNotification('Fonctionnalité d\'édition en cours de développement', 'info');
}

function viewNoteHistory(noteId) {
    console.log('Viewing history for note:', noteId);
    showNotification('Historique des modifications en cours de développement', 'info');
}

// Export function removed - not in allowed list

function showNotification(message, type = 'info') {
    const notification = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
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
