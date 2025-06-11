@extends('layouts.coordonnateur')

@section('title', 'Consultation des Affectations')

@push('styles')
<style>
.semestre-tabs {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
}

.semestre-tab {
    background: transparent;
    border: 2px solid rgba(124, 58, 237, 0.2);
    color: #7c3aed;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    margin: 0.25rem;
    transition: all 0.3s ease;
    font-weight: 600;
}

.semestre-tab.active {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    border-color: #7c3aed;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

.semestre-tab:hover {
    background: rgba(124, 58, 237, 0.1);
    border-color: #7c3aed;
}

.affectation-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #059669;
}

.affectation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(88, 28, 135, 0.15);
}

.affectation-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 1rem;
}

.ue-info {
    flex-grow: 1;
}

.ue-code {
    font-weight: 800;
    font-size: 1.2rem;
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.ue-title {
    font-weight: 600;
    color: #374151;
    margin: 0.25rem 0;
}

.enseignant-info {
    background: rgba(5, 150, 105, 0.1);
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1rem;
}

.enseignant-name {
    font-weight: 700;
    color: #059669;
    font-size: 1.1rem;
}

.enseignant-role {
    color: #6b7280;
    font-size: 0.9rem;
}

.seance-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.seance-badge {
    background: linear-gradient(135deg, #8b5cf6, #a855f7);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 15px;
    font-weight: 600;
    font-size: 0.9rem;
}

.heures-info {
    background: rgba(124, 58, 237, 0.1);
    border-radius: 8px;
    padding: 0.75rem;
    text-align: center;
}

.heures-number {
    font-size: 1.5rem;
    font-weight: 800;
    color: #7c3aed;
}

.heures-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-overview {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
}

.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.filiere-badge {
    display: inline-block;
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-left: 0.5rem;
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Badge overflow protection */
.badge {
    white-space: nowrap;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: inline-block;
}

.date-info {
    color: #6b7280;
    font-size: 0.85rem;
    margin-top: 0.5rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Consultation des Affectations</h2>
            <p class="text-muted">Visualiser les affectations par semestre</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="exportAffectations()">
                <i class="fas fa-download me-2"></i>Exporter
            </button>
            <button class="btn btn-primary" onclick="printAffectations()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
        </div>
    </div>

    <!-- Year Selection Buttons -->
    <div class="year-selection mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="fas fa-users me-2"></i>Sélectionner l'Année de la Filière GI
                </h5>
                <div class="btn-group" role="group">
                    @foreach($filieres as $filiere)
                        <a href="{{ route('coordonnateur.affectations', ['filiere_id' => $filiere->id, 'semestre' => $semestre]) }}"
                           class="btn {{ isset($selectedFiliere) && $selectedFiliere->id == $filiere->id ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-user-check me-2"></i>{{ $filiere->nom }}
                        </a>
                    @endforeach
                </div>
                @if(isset($selectedFiliere))
                    <div class="mt-3">
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Actuellement: {{ $selectedFiliere->nom }}
                        </span>
                        <span class="badge bg-info ms-2">
                            <i class="fas fa-chart-bar me-1"></i>{{ $affectations->total() }} Affectations
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Semestre Tabs -->
    <div class="semestre-tabs">
        <div class="d-flex flex-wrap justify-content-center">
            @if(isset($selectedFiliere))
                @php
                    // Determine available semesters based on filiere year
                    $availableSemesters = [];
                    $filiereName = $selectedFiliere->nom;

                    if (str_ends_with($filiereName, '1')) {
                        // First year: S1, S2
                        $availableSemesters = ['S1', 'S2'];
                    } elseif (str_ends_with($filiereName, '2')) {
                        // Second year: S3, S4
                        $availableSemesters = ['S3', 'S4'];
                    } elseif (str_ends_with($filiereName, '3')) {
                        // Third year: S5 only (no S6)
                        $availableSemesters = ['S5'];
                    } else {
                        // Fallback: all semesters except S6
                        $availableSemesters = ['S1', 'S2', 'S3', 'S4', 'S5'];
                    }
                @endphp

                @foreach($availableSemesters as $sem)
                    <button class="semestre-tab {{ $semestre == $sem ? 'active' : '' }}"
                            onclick="changeSemestre('{{ $sem }}')">
                        Semestre {{ substr($sem, 1) }}
                    </button>
                @endforeach
            @else
                <!-- Show message to select filiere first -->
                <div class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Sélectionnez d'abord une filière pour voir les semestres disponibles
                </div>
            @endif
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-overview">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $affectations->total() }}</div>
                    <div class="stat-label">Total Affectations</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">
                        {{ $affectations->unique('user_id')->count() }}
                    </div>
                    <div class="stat-label">Enseignants</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">
                        {{ $affectations->unique('ue_id')->count() }}
                    </div>
                    <div class="stat-label">UEs Affectées</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <div class="stat-number">
                        @php
                            $totalHeures = 0;
                            foreach($affectations as $affectation) {
                                $ue = $affectation->uniteEnseignement;
                                switch($affectation->type_seance) {
                                    case 'CM': $totalHeures += $ue->heures_cm; break;
                                    case 'TD': $totalHeures += $ue->heures_td; break;
                                    case 'TP': $totalHeures += $ue->heures_tp; break;
                                }
                            }
                        @endphp
                        {{ $totalHeures }}
                    </div>
                    <div class="stat-label">Total Heures</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affectations List -->
    <div class="row">
        @forelse($affectations as $affectation)
            <div class="col-lg-6 col-xl-4">
                <div class="affectation-card">
                    <div class="affectation-header">
                        <div class="ue-info">
                            <div class="ue-code">{{ $affectation->uniteEnseignement->code }}</div>
                            <div class="ue-title">{{ $affectation->uniteEnseignement->nom }}</div>
                            <span class="filiere-badge" title="{{ $affectation->uniteEnseignement->filiere->nom }}">{{ $affectation->uniteEnseignement->filiere->nom }}</span>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="voirDetails({{ $affectation->id }})">
                                    <i class="fas fa-eye me-2"></i>Voir détails
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="modifierAffectation({{ $affectation->id }})">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Enseignant Info -->
                    <div class="enseignant-info">
                        <div class="enseignant-name">{{ $affectation->user->name }}</div>
                        <div class="enseignant-role">
                            {{ ucfirst($affectation->user->role) }}
                            @if($affectation->user->specialite)
                                • {{ $affectation->user->specialite }}
                            @endif
                        </div>
                    </div>

                    <!-- Séance Info -->
                    <div class="seance-info">
                        <span class="seance-badge">{{ $affectation->type_seance }}</span>
                        <div class="heures-info">
                            <div class="heures-number">
                                @php
                                    $heures = 0;
                                    switch($affectation->type_seance) {
                                        case 'CM': $heures = $affectation->uniteEnseignement->heures_cm; break;
                                        case 'TD': $heures = $affectation->uniteEnseignement->heures_td; break;
                                        case 'TP': $heures = $affectation->uniteEnseignement->heures_tp; break;
                                    }
                                @endphp
                                {{ $heures }}
                            </div>
                            <div class="heures-label">Heures</div>
                        </div>
                    </div>

                    <!-- Date Info -->
                    <div class="date-info">
                        <i class="fas fa-calendar me-1"></i>
                        Affecté le {{ $affectation->created_at->format('d/m/Y') }}
                        <br>
                        <i class="fas fa-graduation-cap me-1"></i>
                        Année {{ $affectation->annee_universitaire }}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4>Aucune affectation pour {{ $semestre }}</h4>
                    <p class="text-muted">Les affectations pour ce semestre apparaîtront ici</p>
                    <a href="{{ route('coordonnateur.vacataires') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer des affectations
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($affectations->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $affectations->appends(['semestre' => $semestre])->links() }}
        </div>
    @endif
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de l'Affectation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeSemestre(semestre) {
    const currentFiliereId = '{{ isset($selectedFiliere) ? $selectedFiliere->id : "" }}';
    let url = `{{ route('coordonnateur.affectations') }}?semestre=${semestre}`;

    if (currentFiliereId) {
        url += `&filiere_id=${currentFiliereId}`;
    }

    window.location.href = url;
}

function voirDetails(affectationId) {
    // Implementation for viewing affectation details
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    document.getElementById('detailsContent').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    modal.show();

    // Load details via AJAX
    setTimeout(() => {
        document.getElementById('detailsContent').innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Détails de l'affectation #${affectationId}
            </div>
            <p>Fonctionnalité en cours de développement...</p>
        `;
    }, 1000);
}

function modifierAffectation(affectationId) {
    // Implementation for modifying affectation
    console.log('Modifier affectation:', affectationId);
}

function exportAffectations() {
    // Implementation for exporting affectations
    window.location.href = `{{ route('coordonnateur.export') }}?type=affectations&semestre={{ $semestre }}`;
}

function printAffectations() {
    // Implementation for printing affectations
    window.print();
}
</script>
@endpush
