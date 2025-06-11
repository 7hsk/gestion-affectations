@extends('layouts.admin')

@section('title', 'Toutes les Activités')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-history text-primary me-2"></i>Toutes les Activités
            </h1>
            <p class="mb-0 text-muted">Historique complet des activités du système</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-primary fs-6 px-3 py-2">
                {{ $activities->total() }} activités
            </span>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card shadow-sm mb-4">
        <div class="card-body" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
            <form method="GET" action="{{ route('admin.activities') }}" id="filter-form">
                <input type="hidden" name="per_page" value="10">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-white fw-bold">
                            <i class="fas fa-search me-2"></i>Rechercher
                        </label>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Rechercher dans les activités..."
                               style="border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.9);">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-white fw-bold">
                            <i class="fas fa-filter me-2"></i>Filtrer par type
                        </label>
                        <select class="form-select" id="filter" name="filter"
                                style="border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.9);">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>
                                Toutes ({{ $activityCounts['all'] }})
                            </option>
                            <option value="auth" {{ $filter === 'auth' ? 'selected' : '' }}>
                                Authentification ({{ $activityCounts['auth'] }})
                            </option>
                            <option value="create" {{ $filter === 'create' ? 'selected' : '' }}>
                                Créations ({{ $activityCounts['create'] }})
                            </option>
                            <option value="update" {{ $filter === 'update' ? 'selected' : '' }}>
                                Modifications ({{ $activityCounts['update'] }})
                            </option>
                            <option value="delete" {{ $filter === 'delete' ? 'selected' : '' }}>
                                Suppressions ({{ $activityCounts['delete'] }})
                            </option>
                            <option value="approve" {{ $filter === 'approve' ? 'selected' : '' }}>
                                Approbations ({{ $activityCounts['approve'] }})
                            </option>
                            <option value="reject" {{ $filter === 'reject' ? 'selected' : '' }}>
                                Rejets ({{ $activityCounts['reject'] }})
                            </option>
                            <option value="upload" {{ $filter === 'upload' ? 'selected' : '' }}>
                                Téléchargements ({{ $activityCounts['upload'] }})
                            </option>
                            <option value="export" {{ $filter === 'export' ? 'selected' : '' }}>
                                Exports ({{ $activityCounts['export'] }})
                            </option>
                            <option value="system" {{ $filter === 'system' ? 'selected' : '' }}>
                                Système ({{ $activityCounts['system'] }})
                            </option>
                            <option value="security" {{ $filter === 'security' ? 'selected' : '' }}>
                                Sécurité ({{ $activityCounts['security'] }})
                            </option>
                            <option value="logout" {{ $filter === 'logout' ? 'selected' : '' }}>
                                Déconnexions ({{ $activityCounts['logout'] }})
                            </option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-light fw-bold">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.activities') }}" class="btn btn-outline-light fw-bold">
                                <i class="fas fa-undo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Top Pagination -->
    @if($activities->count() > 0)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="fas fa-list me-2"></i>
                        <strong>{{ $activities->total() }} activités trouvées</strong>
                        @if($search)
                            <span class="ms-2">pour "{{ $search }}"</span>
                        @endif
                        @if($filter !== 'all')
                            <span class="badge bg-primary ms-2">{{ ucfirst($filter) }}</span>
                        @endif
                    </div>
                    <div>
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Activities List -->
    @if($activities->count() > 0)
        @foreach($activities as $activity)
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <!-- Activity Icon -->
                        <div class="me-3">
                            <div class="rounded-circle bg-{{ $activity['color'] }} text-white d-flex align-items-center justify-content-center"
                                 style="width: 50px; height: 50px; font-size: 18px;">
                                <i class="{{ $activity['icon'] }}"></i>
                            </div>
                        </div>

                        <!-- Activity Content -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-1 text-{{ $activity['color'] }} fw-bold">
                                    {{ $activity['message'] }}
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>{{ $activity['time'] }}
                                </small>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-{{ $activity['color'] }} bg-opacity-10 text-{{ $activity['color'] }} border border-{{ $activity['color'] }} border-opacity-25">
                                        {{ ucfirst($activity['type']) }}
                                    </span>
                                    @if(isset($activity['user']) && $activity['user'] !== 'Système')
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>{{ $activity['user'] }}
                                            @if(isset($activity['user_role']))
                                                <span class="badge bg-secondary ms-1">{{ $activity['user_role'] }}</span>
                                            @endif
                                        </small>
                                    @endif
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>{{ $activity['date'] }}
                                    </small>
                                    <button class="btn btn-outline-secondary btn-sm"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#details-{{ $loop->index }}"
                                            aria-expanded="false">
                                        <i class="fas fa-info-circle me-1"></i>Détails
                                    </button>
                                </div>
                            </div>

                            <!-- Collapsible Details -->
                            <div class="collapse mt-3" id="details-{{ $loop->index }}">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            @if(isset($activity['details']))
                                                @foreach($activity['details'] as $key => $value)
                                                    @if($value && $key !== 'activity_id')
                                                        <div class="col-md-6 mb-2">
                                                            <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            <div class="text-muted">{{ is_array($value) ? json_encode($value) : $value }}</div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Bottom Pagination -->
        <div class="card shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Affichage de {{ $activities->firstItem() }} à {{ $activities->lastItem() }}
                        sur {{ $activities->total() }} résultats</strong>
                    </div>
                    <div>
                        {{ $activities->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">Aucune activité trouvée</h5>
                <p class="text-muted mb-4">
                    @if($search)
                        Aucune activité ne correspond à votre recherche "{{ $search }}".
                    @elseif($filter !== 'all')
                        Aucune activité de type "{{ $filter }}" trouvée.
                    @else
                        Aucune activité n'a été enregistrée pour le moment.
                    @endif
                </p>
                @if($search || $filter !== 'all')
                    <a href="{{ route('admin.activities') }}" class="btn btn-primary">
                        <i class="fas fa-refresh me-2"></i>Voir toutes les activités
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit search form with debounce
    let searchTimeout;
    const searchInput = document.getElementById('search');
    const filterForm = document.getElementById('filter-form');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }

    // Auto-submit on filter change
    const filterSelect = document.getElementById('filter');

    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Add smooth hover effects to cards
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'all 0.3s ease';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Enhance pagination links
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Add loading state
            if (!this.parentElement.classList.contains('disabled') &&
                !this.parentElement.classList.contains('active')) {
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        });
    });
});
</script>

<style>
/* Custom styling for better look */
.card {
    transition: all 0.3s ease;
    border: 1px solid #e3e6f0;
}

.card:hover {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.form-control:focus, .form-select:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.btn-outline-light:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

.text-gray-800 {
    color: #5a5c69 !important;
}

/* Badge opacity fix */
.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}

.border-opacity-25 {
    --bs-border-opacity: 0.25;
}

/* Enhanced Pagination Styling */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    color: #dc3545;
    border-color: #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.pagination .page-item.active .page-link {
    background-color: #dc3545;
    border-color: #dc3545;
    color: #fff;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* Pagination container styling */
.card-body .pagination {
    justify-content: center;
}

@media (max-width: 768px) {
    .pagination {
        font-size: 0.875rem;
    }

    .pagination .page-link {
        padding: 0.375rem 0.5rem;
    }
}
</style>
@endsection
