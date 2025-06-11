@extends('layouts.coordonnateur')

@section('title', 'Gestion des Unités d\'Enseignement')

@push('styles')
<style>
.ue-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    box-shadow: 0 4px 20px rgba(5, 150, 105, 0.08);
    transition: all 0.3s ease;
    border: 2px solid #059669;
    position: relative;
}

.ue-card:hover {
    box-shadow: 0 8px 30px rgba(5, 150, 105, 0.15);
    border-color: #10b981;
}

.ue-card.vacant {
    border: 2px solid #059669;
}

.ue-card.vacant:hover {
    border-color: #10b981;
    box-shadow: 0 8px 30px rgba(5, 150, 105, 0.15);
}

.ue-card.affecte {
    border: 2px solid #059669;
}

.ue-card.affecte:hover {
    border-color: #10b981;
    box-shadow: 0 8px 30px rgba(5, 150, 105, 0.15);
}

.ue-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    overflow: hidden;
}

.ue-info {
    flex: 1;
    min-width: 0;
}

.ue-code {
    font-weight: 700;
    font-size: 1.1rem;
    color: #059669;
    margin-bottom: 0.25rem;
}

.ue-title {
    font-weight: 500;
    color: #374151;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.ue-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.btn-details {
    background: linear-gradient(135deg, #059669, #10b981);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(5, 150, 105, 0.3);
    text-decoration: none;
}

.btn-details:hover {
    background: linear-gradient(135deg, #047857, #059669);
    box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);
    color: white;
    text-decoration: none;
}

.ue-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.ue-badges {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.vacant {
    background: linear-gradient(135deg, #fef3c7, #fed7aa);
    color: #d97706;
    border: 1px solid #f59e0b;
}

.status-badge.affecte {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #059669;
    border: 1px solid #10b981;
}

.ue-hours {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

.filter-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
}

.create-ue-form {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    color: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-floating label {
    color: rgba(255, 255, 255, 0.8);
}

.form-floating .form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
}

.form-floating .form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
}

.form-floating .form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.affectation-info {
    background: rgba(5, 150, 105, 0.1);
    border-radius: 8px;
    padding: 0.75rem;
    margin-top: 1rem;
}

.affectation-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(5, 150, 105, 0.1);
}

.affectation-item:last-child {
    border-bottom: none;
}

.semestre-badge {
    display: inline-block;
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-right: 0.5rem;
    white-space: nowrap;
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.filiere-badge {
    display: inline-block;
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
}

.groups-section {
    background: rgba(8, 145, 178, 0.1);
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.group-item {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.group-item:last-child {
    margin-bottom: 0;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header with Create UE Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Gestion des Unités d'Enseignement</h2>
            <p class="text-muted">Créer, modifier et gérer les UEs de vos filières</p>
        </div>
        <a href="{{ route('coordonnateur.unites-enseignement.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Créer une UE
        </a>
    </div>

    <!-- Year Selection Buttons -->
    <div class="year-selection mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-graduation-cap me-2"></i>Sélectionner l'Année de la Filière GI
                </h5>
                <div class="btn-group" role="group">
                    @foreach($filieres as $filiere)
                        <a href="{{ route('coordonnateur.unites-enseignement', ['filiere_id' => $filiere->id]) }}"
                           class="btn {{ isset($selectedFiliere) && $selectedFiliere->id == $filiere->id ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-book me-2"></i>{{ $filiere->nom }}
                        </a>
                    @endforeach
                </div>
                @if(isset($selectedFiliere))
                    <div class="mt-3">
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Actuellement: {{ $selectedFiliere->nom }}
                        </span>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-chart-bar me-1"></i>{{ $ues->total() }} UEs
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-3">
                <select class="form-select" id="filiereFilter">
                    <option value="">Toutes les filières</option>
                    @foreach($filieres as $filiere)
                        <option value="{{ $filiere->id }}">{{ $filiere->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="semestreFilter">
                    <option value="">Tous les semestres</option>
                    <option value="S1">Semestre 1</option>
                    <option value="S2">Semestre 2</option>
                    <option value="S3">Semestre 3</option>
                    <option value="S4">Semestre 4</option>
                    <option value="S5">Semestre 5</option>
                    <option value="S6">Semestre 6</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="statutFilter">
                    <option value="">Tous les statuts</option>
                    <option value="vacant">Vacantes</option>
                    <option value="affecte">Affectées</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- UEs List -->
    <div class="row">
        @forelse($ues as $ue)
            <div class="col-lg-6 col-xl-4">
                <div class="ue-card {{ $ue->est_vacant ? 'vacant' : 'affecte' }}">
                    <div class="ue-header">
                        <div class="ue-info">
                            <div class="ue-code">{{ $ue->code }}</div>
                            <div class="ue-title">{{ Str::limit($ue->nom, 40) }}</div>
                        </div>
                        <div class="ue-actions">
                            <a href="{{ route('coordonnateur.ue.details', $ue->id) }}" class="btn-details">
                                <i class="fas fa-eye me-1"></i>Détails
                            </a>
                            <a href="{{ route('coordonnateur.unites-enseignement.edit', $ue->id) }}" class="btn-details" style="background: linear-gradient(135deg, #f59e0b, #f97316); margin-left: 0.5rem;">
                                <i class="fas fa-edit me-1"></i>Modifier
                            </a>
                        </div>
                    </div>

                    <div class="ue-meta">
                        <div class="ue-badges">
                            <span class="semestre-badge" title="Semestre {{ $ue->semestre }}">{{ $ue->semestre }}</span>
                            <span class="filiere-badge" title="{{ $ue->filiere->nom }}">{{ $ue->filiere->nom }}</span>
                        </div>
                        <span class="status-badge {{ $ue->est_vacant ? 'vacant' : 'affecte' }}">
                            {{ $ue->est_vacant ? 'Vacant' : 'Affecté' }}
                        </span>
                    </div>

                    <div class="ue-hours">
                        <i class="fas fa-clock me-1"></i>
                        Total: {{ $ue->heures_cm + $ue->heures_td + $ue->heures_tp }}h
                        (CM: {{ $ue->heures_cm }}h, TD: {{ $ue->heures_td }}h, TP: {{ $ue->heures_tp }}h)
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-book fa-4x text-muted mb-3"></i>
                    <h4>Aucune unité d'enseignement</h4>
                    <p class="text-muted">Commencez par créer votre première UE</p>
                    <a href="{{ route('coordonnateur.unites-enseignement.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer une UE
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($ues->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $ues->links() }}
        </div>
    @endif
</div>





<!-- Define Groups Modal -->
<div class="modal fade" id="defineGroupsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Définir les groupes TD/TP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="defineGroupsForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="modal_groupes_td" name="groupes_td" min="0" required>
                                <label for="modal_groupes_td">Groupes TD</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" id="modal_groupes_tp" name="groupes_tp" min="0" required>
                                <label for="modal_groupes_tp">Groupes TP</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentUeId = null;



function defineGroups(ueId) {
    const form = document.getElementById('defineGroupsForm');
    form.action = `/coordonnateur/unites-enseignement/${ueId}/groupes`;

    const modal = new bootstrap.Modal(document.getElementById('defineGroupsModal'));
    modal.show();
}



// Filters
document.addEventListener('DOMContentLoaded', function() {
    const filiereFilter = document.getElementById('filiereFilter');
    const semestreFilter = document.getElementById('semestreFilter');
    const statutFilter = document.getElementById('statutFilter');
    const searchInput = document.getElementById('searchInput');

    function filterUes() {
        const cards = document.querySelectorAll('.ue-card');
        const filiereValue = filiereFilter.value;
        const semestreValue = semestreFilter.value;
        const statutValue = statutFilter.value;
        const searchValue = searchInput.value.toLowerCase();

        cards.forEach(card => {
            const cardParent = card.closest('.col-lg-6');
            let show = true;

            // Filter logic here
            if (searchValue && !card.textContent.toLowerCase().includes(searchValue)) {
                show = false;
            }

            if (statutValue) {
                const isVacant = card.classList.contains('vacant');
                if ((statutValue === 'vacant' && !isVacant) || (statutValue === 'affecte' && isVacant)) {
                    show = false;
                }
            }

            cardParent.style.display = show ? 'block' : 'none';
        });
    }

    filiereFilter.addEventListener('change', filterUes);
    semestreFilter.addEventListener('change', filterUes);
    statutFilter.addEventListener('change', filterUes);
    searchInput.addEventListener('input', filterUes);
});
</script>
@endpush
