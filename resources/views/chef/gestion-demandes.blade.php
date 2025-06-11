@extends('layouts.chef')

@section('title', 'Gestion des Demandes')

@push('styles')
<style>
.demande-card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.demande-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.demande-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
}

.demande-body {
    padding: 1.5rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
    position: relative;
    z-index: 1;
}

.status-pending {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-approved {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.status-rejected {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: 1px solid rgba(220, 53, 69, 0.3);
}

.teacher-info {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.ue-info {
    background: #e8f4fd;
    border-left: 4px solid #3498db;
    padding: 1rem;
    border-radius: 0 8px 8px 0;
    margin-bottom: 1rem;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-approve {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-approve:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    color: white;
}

.btn-reject {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-reject:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    color: white;
}

.filter-card {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 2rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Gestion des Demandes</h2>
                    <p class="text-muted mb-0">Demandes d'affectation pour l'année prochaine ({{ $nextYear }})</p>
                </div>
                <div>
                    <button class="btn btn-outline-info" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Demandes</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">En Attente</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="stat-number">{{ $stats['approved'] }}</div>
                <div class="stat-label">Approuvées</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="stat-number">{{ $stats['rejected'] }}</div>
                <div class="stat-label">Rejetées</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card filter-card">
        <div class="card-body">
            <form method="GET" action="{{ route('chef.gestion-demandes') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="valide" {{ request('status') == 'valide' ? 'selected' : '' }}>Approuvées</option>
                            <option value="rejete" {{ request('status') == 'rejete' ? 'selected' : '' }}>Rejetées</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" name="filiere_id">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select class="form-select" name="type_seance">
                            <option value="">Tous les types</option>
                            <option value="CM" {{ request('type_seance') == 'CM' ? 'selected' : '' }}>CM</option>
                            <option value="TD" {{ request('type_seance') == 'TD' ? 'selected' : '' }}>TD</option>
                            <option value="TP" {{ request('type_seance') == 'TP' ? 'selected' : '' }}>TP</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-light flex-fill">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('chef.gestion-demandes') }}" class="btn btn-outline-light">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Demandes List -->
    @if($demandes->count() > 0)
        <div class="row">
            @foreach($demandes as $demande)
                <div class="col-lg-6 col-xl-4">
                    <div class="demande-card">
                        <div class="demande-header">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-3">
                                    <h6 class="mb-1">{{ $demande->uniteEnseignement->code }}</h6>
                                    <small class="opacity-75">{{ Str::limit($demande->uniteEnseignement->nom, 35) }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($demande->validee == 'en_attente')
                                        <span class="status-badge status-pending" title="En attente">
                                            <i class="fas fa-clock me-1"></i>En attente
                                        </span>
                                    @elseif($demande->validee == 'valide')
                                        <span class="status-badge status-approved" title="Approuvée">
                                            <i class="fas fa-check me-1"></i>Approuvée
                                        </span>
                                    @else
                                        <span class="status-badge status-rejected" title="Rejetée">
                                            <i class="fas fa-times me-1"></i>Rejetée
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="demande-body">
                            <!-- Teacher Info -->
                            <div class="teacher-info">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                            {{ strtoupper(substr($demande->user->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $demande->user->name }}</div>
                                        <small class="text-muted">{{ $demande->user->email }}</small>
                                        @if($demande->user->specialite)
                                            <div><small class="text-info">{{ $demande->user->specialite }}</small></div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- UE Info -->
                            <div class="ue-info">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">Filière:</small>
                                        <div class="fw-bold">{{ $demande->uniteEnseignement->filiere->nom }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Semestre:</small>
                                        <div class="fw-bold">{{ $demande->uniteEnseignement->semestre }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Type:</small>
                                        <div class="fw-bold">{{ $demande->type_seance }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Demandé le:</small>
                                        <div class="fw-bold">{{ $demande->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            @if($demande->validee == 'en_attente')
                                <div class="action-buttons">
                                    <button class="btn btn-approve" onclick="approveRequest({{ $demande->id }})">
                                        <i class="fas fa-check me-1"></i>Approuver
                                    </button>
                                    <button class="btn btn-reject" onclick="rejectRequest({{ $demande->id }})">
                                        <i class="fas fa-times me-1"></i>Rejeter
                                    </button>
                                </div>
                            @else
                                <div class="text-center">
                                    <small class="text-muted">
                                        Traité le {{ $demande->updated_at->format('d/m/Y à H:i') }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                {{ $demandes->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h4>Aucune demande trouvée</h4>
            <p>Aucune demande d'affectation ne correspond à vos critères.</p>
            <a href="{{ route('chef.gestion-demandes') }}" class="btn btn-primary">
                <i class="fas fa-refresh me-2"></i>Voir toutes les demandes
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function approveRequest(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir approuver cette demande ?')) {
        fetch(`/chef/demandes/${demandeId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

function rejectRequest(demandeId) {
    if (confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')) {
        fetch(`/chef/demandes/${demandeId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

// Export function removed - not in allowed list
</script>
@endpush
