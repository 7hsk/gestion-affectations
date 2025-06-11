@extends('layouts.admin')

@section('title', 'Gestion des Unités d\'Enseignement')

@section('content')
<div class="container-fluid">
    <!-- Success/Error Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Succès!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Erreur!</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                <div>
                    <strong>Erreurs de validation:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-1">
                                <i class="fas fa-book me-2"></i>
                                Gestion des Unités d'Enseignement
                            </h2>
                            <p class="mb-0 opacity-75">Gérez les unités d'enseignement de votre établissement</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['total'] }}</h4>
                                    <small class="text-white-50">Total</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['vacant'] }}</h4>
                                    <small class="text-white-50">Vacantes</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['assigned'] }}</h4>
                                    <small class="text-white-50">Assignées</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $stats['total_hours'] }}</h4>
                                    <small class="text-white-50">Total heures</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.ues.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Code ou nom de l'UE">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Semestre</label>
                            <select class="form-select" name="semestre">
                                <option value="">Tous</option>
                                @foreach(['S1', 'S2', 'S3', 'S4', 'S5', 'S6'] as $sem)
                                    <option value="{{ $sem }}" {{ request('semestre') == $sem ? 'selected' : '' }}>
                                        {{ $sem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Département</label>
                            <select class="form-select" name="departement">
                                <option value="">Tous</option>
                                @foreach($filterOptions['departements'] as $dept)
                                    <option value="{{ $dept->id }}" {{ request('departement') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Filière</label>
                            <select class="form-select" name="filiere">
                                <option value="">Toutes</option>
                                @foreach($filterOptions['filieres'] as $filiere)
                                    <option value="{{ $filiere->id }}" {{ request('filiere') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select class="form-select" name="status">
                                <option value="">Tous</option>
                                <option value="vacant" {{ request('status') == 'vacant' ? 'selected' : '' }}>Vacant</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigné</option>
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('admin.ues.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nouvelle UE
                    </a>
                    <a href="{{ route('admin.ues.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-refresh me-1"></i>Actualiser
                    </a>
                </div>
                <div>
                    <button class="btn btn-outline-info" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Unités d'Enseignement ({{ $ues->total() }} résultats)
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary active" id="cardViewBtn" onclick="switchView('card')">
                        <i class="fas fa-th-large me-1"></i>Cartes
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="tableViewBtn" onclick="switchView('table')">
                        <i class="fas fa-table me-1"></i>Tableau
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="row">
        @forelse($ues as $ue)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 border-0 shadow-sm ue-card {{ $ue->est_vacant ? 'vacant' : 'assigned' }}">
                    <div class="card-header bg-gradient-danger text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $ue->code }}</h6>
                                <small class="opacity-90">{{ $ue->filiere->nom ?? 'Non assignée' }}</small>
                            </div>
                            <span class="badge bg-light text-dark">{{ $ue->semestre }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <h6 class="card-title text-danger mb-3">{{ Str::limit($ue->nom, 50) }}</h6>

                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">CM</small>
                                    <strong class="text-danger">{{ $ue->heures_cm ?? 0 }}h</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">TD</small>
                                    <strong class="text-success">{{ $ue->heures_td ?? 0 }}h</strong>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">TP</small>
                                    <strong class="text-info">{{ $ue->heures_tp ?? 0 }}h</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Groupes TD</small>
                                    <strong>{{ $ue->groupes_td ?? 0 }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <small class="text-muted d-block">Groupes TP</small>
                                    <strong>{{ $ue->groupes_tp ?? 0 }}</strong>
                                </div>
                            </div>
                        </div>

                        @if($ue->responsable)
                            <div class="mb-3">
                                <small class="text-muted">Responsable:</small>
                                <div class="d-flex align-items-center mt-1">
                                    <div class="bg-danger bg-opacity-10 rounded-circle p-1 me-2">
                                        <i class="fas fa-user text-danger"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $ue->responsable->name }}</strong>
                                        <br><small class="text-muted">{{ ucfirst($ue->responsable->role) }}</small>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Statut:</small>
                            <div class="mt-1">
                                @if($ue->est_vacant)
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Vacant
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Assigné
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-transparent border-0">
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('admin.ues.show', $ue->id) }}" class="btn btn-outline-info btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>Voir
                            </a>
                            <a href="{{ route('admin.ues.edit', $ue->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i>Modifier
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm flex-fill"
                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ue->id }}">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-book fa-4x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-3">Aucune unité d'enseignement trouvée</h5>
                        <p class="text-muted mb-4">Commencez par créer votre première unité d'enseignement</p>
                        <a href="{{ route('admin.ues.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Créer une UE
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Table View (Hidden by default) -->
    <div id="tableView" class="row" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($ues->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Semestre</th>
                                        <th>Heures</th>
                                        <th>Groupes</th>
                                        <th>Filière</th>
                                        <th>Responsable</th>
                                        <th>Statut</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ues as $ue)
                                        <tr>
                                            <td>
                                                <strong class="text-danger">{{ $ue->code }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $ue->nom }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $ue->niveau }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $ue->semestre }}</span>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div>CM: {{ $ue->heures_cm }}h</div>
                                                    <div>TD: {{ $ue->heures_td }}h</div>
                                                    <div>TP: {{ $ue->heures_tp }}h</div>
                                                    <strong>Total: {{ ($ue->heures_cm ?? 0) + ($ue->heures_td ?? 0) + ($ue->heures_tp ?? 0) }}h</strong>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="small">
                                                    <div>TD: {{ $ue->groupes_td ?? 0 }}</div>
                                                    <div>TP: {{ $ue->groupes_tp ?? 0 }}</div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $ue->filiere->nom ?? 'Non assignée' }}</strong>
                                                    @if($ue->departement)
                                                        <br><small class="text-muted">{{ $ue->departement->nom }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($ue->responsable)
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-danger bg-opacity-10 rounded-circle p-1 me-2">
                                                            <i class="fas fa-user text-danger"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $ue->responsable->name }}</strong>
                                                            <br><small class="text-muted">{{ ucfirst($ue->responsable->role) }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Non assigné</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ue->est_vacant)
                                                    <span class="badge bg-warning">Vacant</span>
                                                @else
                                                    <span class="badge bg-success">Assigné</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.ues.show', $ue->id) }}"
                                                       class="btn btn-sm btn-outline-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.ues.edit', $ue->id) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ue->id }}" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
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
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune unité d'enseignement trouvée</h5>
                            <p class="text-muted">Commencez par créer votre première unité d'enseignement</p>
                            <a href="{{ route('admin.ues.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Créer une UE
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($ues->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $ues->links() }}
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Delete Modals (Outside table for better accessibility) -->
@foreach($ues as $ue)
    <div class="modal fade" id="deleteModal{{ $ue->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $ue->id }}" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel{{ $ue->id }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                            <i class="fas fa-book text-danger fa-2x"></i>
                        </div>
                        <h6 class="mb-2">Supprimer l'unité d'enseignement</h6>
                        <p class="mb-0">Êtes-vous sûr de vouloir supprimer l'UE <strong>{{ $ue->code }} - {{ $ue->nom }}</strong> ?</p>
                    </div>

                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>Cette action est irréversible et supprimera définitivement toutes les données associées.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <form action="{{ route('admin.ues.destroy', $ue->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
/* Enhanced UE Management Styles */
.bg-gradient-primary {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.card {
    transition: transform 0.2s ease-in-out;
    border-radius: 12px;
}

.card:hover {
    transform: translateY(-2px);
}

/* UE Card Styles */
.ue-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.ue-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(220, 53, 69, 0.15) !important;
}

.ue-card.vacant {
    border-color: rgba(255, 193, 7, 0.3);
}

.ue-card.vacant:hover {
    border-color: #ffc107;
}

.ue-card.assigned {
    border-color: rgba(25, 135, 84, 0.3);
}

.ue-card.assigned:hover {
    border-color: #198754;
}

.ue-card .card-header {
    position: relative;
    overflow: hidden;
}

.ue-card .card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
}

.table th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
    border-color: rgba(0,0,0,0.05);
}

.table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.btn-group .btn {
    margin-right: 2px;
}

.badge {
    font-weight: 500;
}

/* Enhanced Modal Styling */
.modal {
    z-index: 1055 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header.bg-danger {
    border-bottom: none;
}

.modal-footer {
    background: rgba(248, 249, 250, 0.5);
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* Success/Error notifications positioning */
.alert-dismissible {
    position: relative;
    z-index: 1060;
}

/* Enhanced form controls */
.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e0e6ed;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    border-radius: 8px 0 0 8px;
    border: 1px solid #e0e6ed;
    background: #f8f9fa;
}

/* Enhanced buttons */
.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔴 ADMIN UE MANAGEMENT ENHANCED - Sidebar always visible');

    // Search with debounce
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // Enhanced modal handling
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            document.body.classList.add('modal-open');
        });

        modal.addEventListener('hidden.bs.modal', function (event) {
            document.body.classList.remove('modal-open');
        });
    });

    // Handle delete confirmation with better UX
    document.querySelectorAll('form[method="POST"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Suppression...';
                submitBtn.disabled = true;
            }
        });
    });

    // Initialize view preference
    const savedView = localStorage.getItem('adminUeView') || 'card';
    if (savedView === 'table') {
        switchView('table');
    }
});

// View switching functionality
function switchView(viewType) {
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    const cardBtn = document.getElementById('cardViewBtn');
    const tableBtn = document.getElementById('tableViewBtn');

    if (viewType === 'card') {
        cardView.style.display = 'flex';
        tableView.style.display = 'none';
        cardBtn.classList.add('active');
        tableBtn.classList.remove('active');
        localStorage.setItem('adminUeView', 'card');
    } else {
        cardView.style.display = 'none';
        tableView.style.display = 'block';
        tableBtn.classList.add('active');
        cardBtn.classList.remove('active');
        localStorage.setItem('adminUeView', 'table');
    }
}
</script>
@endsection
