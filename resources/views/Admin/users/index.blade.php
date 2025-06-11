@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs')

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
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-1">Gestion des Utilisateurs</h3>
                            <p class="text-muted mb-0">Créer, modifier et gérer les comptes utilisateurs</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-primary mb-0">{{ $totalUsers }}</h4>
                                    <small class="text-muted">Total</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-success mb-0">{{ $usersByRole['enseignant'] }}</h4>
                                    <small class="text-muted">Enseignants</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-info mb-0">{{ $usersByRole['chef'] }}</h4>
                                    <small class="text-muted">Chefs</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-warning mb-0">{{ $usersByRole['coordonnateur'] }}</h4>
                                    <small class="text-muted">Coordinateurs</small>
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
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" name="search"
                                               placeholder="Rechercher par nom ou email..."
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="role">
                                        <option value="">Tous les rôles</option>
                                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="chef" {{ request('role') == 'chef' ? 'selected' : '' }}>Chef</option>
                                        <option value="coordonnateur" {{ request('role') == 'coordonnateur' ? 'selected' : '' }}>Coordonnateur</option>
                                        <option value="enseignant" {{ request('role') == 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                                        <option value="vacataire" {{ request('role') == 'vacataire' ? 'selected' : '' }}>Vacataire</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" name="department">
                                        <option value="">Tous les départements</option>
                                        @foreach(\App\Models\Departement::all() as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-filter me-1"></i>Filtrer
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Nouvel utilisateur
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-refresh me-2"></i>Actualiser
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Liste des utilisateurs</h5>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                            {{ $users->total() }} utilisateur(s)
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Utilisateur</th>
                                    <th class="border-0">Rôle</th>
                                    <th class="border-0">Département</th>
                                    <th class="border-0">Spécialités</th>
                                    <th class="border-0">Statut</th>
                                    <th class="border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge
                                                @if($user->role == 'admin') bg-danger
                                                @elseif($user->role == 'chef') bg-primary
                                                @elseif($user->role == 'coordonnateur') bg-info
                                                @elseif($user->role == 'enseignant') bg-success
                                                @else bg-warning @endif">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->departement)
                                                <span class="badge bg-light text-dark">{{ $user->departement->nom }}</span>
                                            @else
                                                <span class="text-muted">Non assigné</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->specialite)
                                                @php
                                                    $specialites = explode(',', $user->specialite);
                                                    $displaySpecialites = array_slice($specialites, 0, 2);
                                                @endphp
                                                @foreach($displaySpecialites as $spec)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">{{ trim($spec) }}</span>
                                                @endforeach
                                                @if(count($specialites) > 2)
                                                    <span class="badge bg-light text-muted">+{{ count($specialites) - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>
                                                Actif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.users.show', $user->id) }}"
                                                   class="btn btn-sm btn-outline-info" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer" data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal{{ $user->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>


                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5">
                                                    <i class="fas fa-users text-muted fa-3x mb-3"></i>
                                                    <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                                                    <p class="text-muted">Aucun utilisateur ne correspond à vos critères de recherche.</p>
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                                        <i class="fas fa-plus me-2"></i>Créer le premier utilisateur
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="card-footer bg-white border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted">
                                            Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }}
                                            sur {{ $users->total() }} résultats
                                        </small>
                                    </div>
                                    <div>
                                        {{ $users->links() }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modals (Outside table for better accessibility) -->
@foreach($users as $user)
    <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $user->id }}" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel{{ $user->id }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                            <i class="fas fa-user-times text-danger fa-2x"></i>
                        </div>
                        <h6 class="mb-2">Supprimer l'utilisateur</h6>
                        <p class="mb-0">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->name }}</strong> ?</p>
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
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
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
.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    vertical-align: middle;
}

.card {
    transition: all 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
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

/* Ensure modals are always accessible */
.modal.show {
    display: block !important;
}

.modal-dialog-centered {
    min-height: calc(100% - 1rem);
}

/* Success/Error notifications positioning */
.alert-dismissible {
    position: relative;
    z-index: 1060;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.querySelector('select[name="role"]').addEventListener('change', function() {
        this.form.submit();
    });

    document.querySelector('select[name="department"]').addEventListener('change', function() {
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

    // Enhanced modal handling
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            // Ensure modal is properly positioned
            document.body.classList.add('modal-open');
        });

        modal.addEventListener('hidden.bs.modal', function (event) {
            // Clean up when modal is closed
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
});
</script>
@endsection
