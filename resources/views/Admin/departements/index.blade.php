@extends('layouts.admin')

@section('title', 'Gestion des Départements')

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

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Attention!</strong> {{ session('warning') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-1">Gestion des Départements</h3>
                            <p class="text-muted mb-0">Créer, modifier et gérer les départements académiques</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-primary mb-0">{{ $stats['total'] }}</h4>
                                    <small class="text-muted">Total</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-success mb-0">{{ $stats['active'] }}</h4>
                                    <small class="text-muted">Actifs</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-info mb-0">{{ $stats['total_users'] }}</h4>
                                    <small class="text-muted">Utilisateurs</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-warning mb-0">{{ $stats['total_ues'] }}</h4>
                                    <small class="text-muted">UEs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('admin.departements.index') }}" class="row g-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" name="search"
                                               placeholder="Rechercher un département..."
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="status">
                                        <option value="">Tous les statuts</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.departements.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nouveau département
                                </a>
                                <a href="{{ route('admin.departements.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-2"></i>Actualiser
                                </a>
                                <button class="btn btn-outline-info" onclick="printDepartments()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Departments Grid -->
    <div class="row">
        @forelse($departements as $departement)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-building text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $departement->nom }}</h6>
                                    <small class="text-muted">{{ $departement->code ?? 'N/A' }}</small>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" data-bs-auto-close="true">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.departements.show', $departement->id) }}">
                                        <i class="fas fa-eye me-2"></i>Voir détails
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.departements.edit', $departement->id) }}">
                                        <i class="fas fa-edit me-2"></i>Modifier
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $departement->id }}">
                                        <i class="fas fa-trash me-2"></i>Supprimer
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($departement->description)
                            <p class="text-muted small mb-3">{{ Str::limit($departement->description, 100) }}</p>
                        @endif

                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="bg-success bg-opacity-10 rounded p-2">
                                    <h5 class="text-success mb-0">{{ $departement->users_count }}</h5>
                                    <small class="text-muted">Utilisateurs</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-warning bg-opacity-10 rounded p-2">
                                    <h5 class="text-warning mb-0">{{ $departement->unites_enseignement_count }}</h5>
                                    <small class="text-muted">Unités d'Enseignement</small>
                                </div>
                            </div>
                        </div>

                        <!-- Department Head -->
                        @php
                            $chef = $departement->users->where('role', 'chef')->first();
                        @endphp
                        @if($chef)
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2">
                                    <i class="fas fa-user-tie text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Chef de département</small>
                                    <div class="fw-medium">{{ $chef->name }}</div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning alert-sm">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <small>Aucun chef assigné</small>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Créé le {{ $departement->created_at->format('d/m/Y') }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.departements.show', $departement->id) }}"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.departements.edit', $departement->id) }}"
                                   class="btn btn-outline-success">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Modal -->
            <div class="modal fade" id="deleteModal{{ $departement->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmer la suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer le département <strong>{{ $departement->nom }}</strong> ?</p>
                            @if($departement->users->count() > 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Ce département contient {{ $departement->users->count() }} utilisateur(s).
                                    Veuillez les réassigner avant la suppression.
                                </div>
                            @endif
                            <p class="text-danger"><small>Cette action est irréversible.</small></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <form action="{{ route('admin.departements.destroy', $departement->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                        {{ $departement->users->count() > 0 ? 'disabled' : '' }}>
                                    Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-building text-muted fa-3x mb-3"></i>
                        <h5 class="text-muted">Aucun département trouvé</h5>
                        <p class="text-muted">Aucun département ne correspond à vos critères de recherche.</p>
                        <a href="{{ route('admin.departements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer le premier département
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.alert-sm {
    padding: 0.5rem;
    font-size: 0.875rem;
}

.btn-group-sm .btn {
    border-radius: 0.375rem !important;
}

.btn-group-sm .btn:not(:last-child) {
    margin-right: 2px;
}

/* Enhanced Dropdown Positioning */
.dropdown-menu-end {
    --bs-position: end;
}

.dropdown-menu {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    z-index: 1020;
}

.dropdown-item {
    border-radius: 8px;
    margin: 4px 8px;
    transition: all 0.2s ease;
    font-weight: 500;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white;
    transform: translateX(4px);
}

.dropdown-item.text-danger:hover {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
}

/* Ensure dropdown stays within viewport */
.dropdown {
    position: relative;
}

.card .dropdown-menu {
    position: absolute;
    right: 0;
    left: auto;
    transform: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelector('select[name="status"]').addEventListener('change', function() {
        this.form.submit();
    });

    // Search with debounce
    let searchTimeout;
    document.querySelector('input[name="search"]').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
});

// Print function
function printDepartments() {
    window.print();
}

// Real-time statistics update (optional)
function updateStats() {
    fetch('{{ route("admin.departements.stats") }}')
        .then(response => response.json())
        .then(data => {
            // Update statistics in real-time if needed
            console.log('Stats updated:', data);
        })
        .catch(error => console.error('Error updating stats:', error));
}

// Update stats every 5 minutes
setInterval(updateStats, 300000);
</script>
@endsection
