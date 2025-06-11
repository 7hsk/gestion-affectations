@extends('layouts.coordonnateur')

@section('title', 'Historique des Affectations')

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #7c3aed 0%, #8b5cf6 50%, #059669 100%);
    transform: translateX(-50%);
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 3rem;
    width: 100%;
}

.timeline-item:nth-child(odd) .timeline-content {
    margin-left: 0;
    margin-right: 55%;
    text-align: right;
}

.timeline-item:nth-child(even) .timeline-content {
    margin-left: 55%;
    margin-right: 0;
    text-align: left;
}

.timeline-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
    position: relative;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 40px rgba(88, 28, 135, 0.15);
}

.timeline-content::before {
    content: '';
    position: absolute;
    top: 50%;
    width: 0;
    height: 0;
    border: 15px solid transparent;
    transform: translateY(-50%);
}

.timeline-item:nth-child(odd) .timeline-content::before {
    right: -30px;
    border-left-color: rgba(255, 255, 255, 0.95);
}

.timeline-item:nth-child(even) .timeline-content::before {
    left: -30px;
    border-right-color: rgba(255, 255, 255, 0.95);
}

.timeline-marker {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 4px solid #7c3aed;
    z-index: 10;
    transition: all 0.3s ease;
}

.timeline-marker.created {
    border-color: #059669;
    background: #10b981;
}

.timeline-marker.modified {
    border-color: #f59e0b;
    background: #fbbf24;
}

.timeline-marker.deleted {
    border-color: #ef4444;
    background: #f87171;
}

.timeline-item:hover .timeline-marker {
    transform: translate(-50%, -50%) scale(1.2);
}

.action-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.action-badge.created {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
}

.action-badge.modified {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: white;
}

.action-badge.deleted {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: white;
}

.action-badge.validated {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
}

/* Affectation Status Badges */
.action-badge.valide {
    background: linear-gradient(135deg, #059669, #10b981);
    color: white;
}

.action-badge.rejete {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: white;
}

.action-badge.annule {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    color: white;
}

.action-badge.en_attente {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
    color: white;
}

/* Timeline Markers for Affectation Status */
.timeline-marker.valide {
    border-color: #059669;
    background: #10b981;
}

.timeline-marker.rejete {
    border-color: #ef4444;
    background: #f87171;
}

.timeline-marker.annule {
    border-color: #6b7280;
    background: #9ca3af;
}

.timeline-marker.en_attente {
    border-color: #f59e0b;
    background: #fbbf24;
}

.historique-header {
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.5rem;
}

.historique-details {
    color: #6b7280;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.historique-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.historique-user {
    font-weight: 600;
    color: #7c3aed;
}

.historique-date {
    font-size: 0.8rem;
    color: #9ca3af;
}

.filter-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
}

.stats-section {
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

.ue-info {
    background: rgba(124, 58, 237, 0.1);
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.5rem 0;
}

.ue-code {
    font-weight: 800;
    color: #7c3aed;
}

.ue-title {
    font-size: 0.9rem;
    color: #6b7280;
}

.changes-list {
    background: rgba(5, 150, 105, 0.1);
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.5rem 0;
}

.change-item {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.change-item:last-child {
    margin-bottom: 0;
}

.old-value {
    color: #ef4444;
    text-decoration: line-through;
}

.new-value {
    color: #059669;
    font-weight: 600;
}

/* Year Tabs Styling */
.year-tabs {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 1rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
}

.year-tab {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    text-decoration: none;
    color: #6b7280;
    background: rgba(124, 58, 237, 0.1);
    transition: all 0.3s ease;
    font-weight: 500;
    border: 2px solid transparent;
}

.year-tab:hover {
    color: #7c3aed;
    background: rgba(124, 58, 237, 0.2);
    transform: translateY(-2px);
    text-decoration: none;
}

.year-tab.active {
    background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    color: white;
    border-color: #7c3aed;
    box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
}

/* Enhanced Statistics */
.stats-section {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(139, 92, 246, 0.1));
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.1);
    border: 1px solid rgba(124, 58, 237, 0.2);
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 12px;
    transition: all 0.3s ease;
    border: 1px solid rgba(124, 58, 237, 0.1);
}

.stat-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(124, 58, 237, 0.15);
}

@media (max-width: 768px) {
    .timeline::before {
        left: 30px;
    }

    .timeline-item:nth-child(odd) .timeline-content,
    .timeline-item:nth-child(even) .timeline-content {
        margin-left: 60px;
        margin-right: 0;
        text-align: left;
    }

    .timeline-item:nth-child(odd) .timeline-content::before,
    .timeline-item:nth-child(even) .timeline-content::before {
        left: -30px;
        right: auto;
        border-right-color: rgba(255, 255, 255, 0.95);
        border-left-color: transparent;
    }

    .timeline-marker {
        left: 30px;
    }

    .year-tabs .d-flex {
        flex-direction: column;
    }

    .year-tab {
        margin-bottom: 0.5rem;
        text-align: center;
    }
}

/* Add Modal Styles */
.modal-content {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border: none;
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(88, 28, 135, 0.2);
}

.modal-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.affectation-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 1.5rem;
}

.affectation-table th {
    background: rgba(124, 58, 237, 0.1);
    padding: 1rem;
    font-weight: 600;
    color: #374151;
    text-align: left;
}

.affectation-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.affectation-table tr:last-child td {
    border-bottom: none;
}

.chart-container {
    margin-top: 2rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(88, 28, 135, 0.1);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">📚 Historique des Affectations</h2>
            <p class="text-muted">Suivi chronologique et traçabilité complète des années passées</p>
        </div>
        <div>
            <button class="btn btn-primary" id="downloadReportBtn">
                <i class="fas fa-chart-line me-2"></i>Rapport Analytique
            </button>
        </div>
    </div>
    <!-- Year Selection for PDF -->
    <div class="mb-4" style="max-width: 350px;">
        <label for="reportYear" class="form-label">Sélectionner l'année universitaire pour le rapport PDF</label>
        <select class="form-select" id="reportYear">
            @foreach($annees as $annee)
                <option value="{{ $annee }}">{{ $annee }}</option>
            @endforeach
        </select>
    </div>
    <!-- Hidden form for PDF download -->
    <form id="pdfDownloadForm" method="POST" action="{{ route('coordonnateur.download-rapport-analytique') }}" target="_blank" style="display:none;">
        @csrf
        <input type="hidden" name="year" id="pdfYearInput">
        <input type="hidden" name="chart_image" id="pdfChartImageInput">
    </form>

    <!-- Restore Year Tabs for historique view -->
    @if($annees->isNotEmpty())
        <div class="year-tabs mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('coordonnateur.historique') }}"
                   class="year-tab {{ !request('annee') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-1"></i>Toutes les années
                </a>
                @foreach($annees as $annee)
                    <a href="{{ route('coordonnateur.historique', ['annee' => $annee]) }}"
                       class="year-tab {{ request('annee') == $annee ? 'active' : '' }}">
                        {{ $annee }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Enhanced Statistics -->
    <div class="stats-section">
        <div class="row">
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['total'] ?? 0 }}</div>
                    <div class="stat-label">
                        <i class="fas fa-chart-bar me-1"></i>Total Actions
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['valide'] ?? 0 }}</div>
                    <div class="stat-label">
                        <i class="fas fa-check-circle me-1 text-success"></i>Approuvées
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['rejete'] ?? 0 }}</div>
                    <div class="stat-label">
                        <i class="fas fa-times-circle me-1 text-danger"></i>Rejetées
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['annule'] ?? 0 }}</div>
                    <div class="stat-label">
                        <i class="fas fa-ban me-1 text-secondary"></i>Annulées
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['en_attente'] ?? 0 }}</div>
                    <div class="stat-label">
                        <i class="fas fa-clock me-1 text-warning"></i>En Attente
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-item">
                    <div class="stat-number">{{ $filieres->count() }}</div>
                    <div class="stat-label">
                        <i class="fas fa-graduation-cap me-1 text-info"></i>Filières
                    </div>
                </div>
            </div>
        </div>

        @if(request('annee'))
            <div class="mt-3 text-center">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Statistiques pour l'année universitaire {{ request('annee') }}
                </small>
            </div>
        @endif
    </div>

    <!-- Enhanced Filters -->
    <div class="filter-section">
        <form method="GET" action="{{ route('coordonnateur.historique') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-tasks me-1"></i>Statut
                    </label>
                    <select class="form-select" name="action" onchange="submitFilters()">
                        <option value="">Tous les statuts</option>
                        <option value="valide" {{ request('action') == 'valide' ? 'selected' : '' }}>
                            <i class="fas fa-check-circle"></i> Approuvées
                        </option>
                        <option value="rejete" {{ request('action') == 'rejete' ? 'selected' : '' }}>
                            <i class="fas fa-times-circle"></i> Rejetées
                        </option>
                        <option value="annule" {{ request('action') == 'annule' ? 'selected' : '' }}>
                            <i class="fas fa-ban"></i> Annulées
                        </option>
                        <option value="en_attente" {{ request('action') == 'en_attente' ? 'selected' : '' }}>
                            <i class="fas fa-clock"></i> En Attente
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-graduation-cap me-1"></i>Filière
                    </label>
                    <select class="form-select" name="filiere_id" onchange="submitFilters()">
                        <option value="">Toutes les filières</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                {{ $filiere->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1"></i>Date début
                    </label>
                    <input type="date" class="form-control" name="date_debut"
                           value="{{ request('date_debut') }}" onchange="submitFilters()">
                </div>
                <div class="col-md-2">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1"></i>Date fin
                    </label>
                    <input type="date" class="form-control" name="date_fin"
                           value="{{ request('date_fin') }}" onchange="submitFilters()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-search me-1"></i>Recherche
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput"
                               placeholder="Rechercher UE, enseignant..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="button" onclick="performSearch()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Keep current year filter -->
            @if(request('annee'))
                <input type="hidden" name="annee" value="{{ request('annee') }}">
            @endif
        </form>
    </div>

    <!-- Timeline -->
    <div class="timeline">
        @forelse($historique as $item)
            <div class="timeline-item" data-action="{{ $item->action }}" data-filiere="{{ $item->uniteEnseignement->filiere_id ?? '' }}">
                <div class="timeline-marker {{ $item->action }}"></div>
                <div class="timeline-content">
                    <span class="action-badge {{ $item->action }}">
                        @switch($item->action)
                            @case('valide')
                                <i class="fas fa-check-circle me-1"></i>Approuvée
                                @break
                            @case('rejete')
                                <i class="fas fa-times-circle me-1"></i>Rejetée
                                @break
                            @case('annule')
                                <i class="fas fa-ban me-1"></i>Annulée
                                @break
                            @case('en_attente')
                                <i class="fas fa-clock me-1"></i>En Attente
                                @break
                            @case('created')
                                <i class="fas fa-plus me-1"></i>Création
                                @break
                            @case('modified')
                                <i class="fas fa-edit me-1"></i>Modification
                                @break
                            @case('deleted')
                                <i class="fas fa-trash me-1"></i>Suppression
                                @break
                            @case('validated')
                                <i class="fas fa-check me-1"></i>Validation
                                @break
                            @default
                                <i class="fas fa-info me-1"></i>{{ ucfirst($item->action) }}
                        @endswitch
                    </span>
                    
                    <div class="historique-header">
                        @if($item->action == 'valide')
                            Affectation approuvée
                        @elseif($item->action == 'rejete')
                            Affectation rejetée
                        @elseif($item->action == 'annule')
                            Affectation annulée
                        @elseif($item->action == 'en_attente')
                            Affectation en attente
                        @elseif($item->action == 'created')
                            Nouvelle affectation créée
                        @elseif($item->action == 'modified')
                            Affectation modifiée
                        @elseif($item->action == 'deleted')
                            Affectation supprimée
                        @elseif($item->action == 'validated')
                            Affectation validée
                        @else
                            Affectation - {{ ucfirst($item->action) }}
                        @endif
                    </div>
                    
                    <div class="historique-details">
                        {{ $item->description ?? 'Aucune description disponible' }}
                    </div>
                    
                    @if($item->uniteEnseignement)
                        <div class="ue-info">
                            <div class="ue-code">{{ $item->uniteEnseignement->code }}</div>
                            <div class="ue-title">{{ $item->uniteEnseignement->nom }}</div>
                            <small class="text-muted">{{ $item->uniteEnseignement->filiere->nom ?? 'Filière inconnue' }}</small>
                        </div>
                    @endif
                    
                    @if($item->user)
                        <div class="changes-list">
                            <strong>Enseignant concerné:</strong> {{ $item->user->name }}
                            @if($item->user->role)
                                <span class="badge bg-secondary ms-2">{{ ucfirst($item->user->role) }}</span>
                            @endif
                            @if($item->type_seance)
                                <span class="badge bg-primary ms-2">{{ $item->type_seance }}</span>
                            @endif
                        </div>
                    @endif

                    @if($item->commentaire)
                        <div class="changes-list">
                            <strong>Commentaire:</strong> {{ $item->commentaire }}
                        </div>
                    @endif

                    @if($item->date_validation)
                        <div class="changes-list">
                            <strong>Date de validation:</strong> {{ \Carbon\Carbon::parse($item->date_validation)->format('d/m/Y à H:i') }}
                        </div>
                    @endif
                    
                    @if(isset($item->changes) && $item->changes && is_array(json_decode($item->changes, true)))
                        <div class="changes-list">
                            <strong>Modifications:</strong>
                            @foreach(json_decode($item->changes, true) as $field => $change)
                                <div class="change-item">
                                    <strong>{{ ucfirst($field) }}:</strong>
                                    <span class="old-value">{{ $change['old'] ?? 'N/A' }}</span>
                                    →
                                    <span class="new-value">{{ $change['new'] ?? 'N/A' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    
                    <div class="historique-meta">
                        <div class="historique-user">
                            <i class="fas fa-user me-1"></i>
                            {{ $item->created_by_name ?? 'Système' }}
                        </div>
                        <div class="historique-date">
                            <i class="fas fa-clock me-1"></i>
                            {{ $item->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h4>Aucun historique</h4>
                <p class="text-muted">L'historique des affectations apparaîtra ici</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($historique->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $historique->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 ENHANCED HISTORIQUE SYSTEM INITIALIZED');
    console.log('📊 Year-based filtering with advanced analytics');
    console.log('📈 Export functionality for multiple data types');

    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }

    // Auto-submit form on filter changes
    const filterSelects = document.querySelectorAll('select[name], input[name="date_debut"], input[name="date_fin"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            console.log('Filter changed:', this.name, '=', this.value);
        });
    });

    const reportYear = document.getElementById('reportYear');
    const affectationsTableBody = document.getElementById('affectationsTableBody');
    let affectationsChart = null;

    // Function to load affectations data
    async function loadAffectationsData(year) {
        try {
            const response = await fetch(`/coordonnateur/api/affectations/${year}`);
            const data = await response.json();
            
            // Update table
            affectationsTableBody.innerHTML = data.affectations.map(aff => `
                <tr>
                    <td>${aff.ue_code} - ${aff.ue_nom}</td>
                    <td>${aff.enseignant}</td>
                    <td>${aff.type_seance}</td>
                    <td><span class="action-badge ${aff.statut}">${aff.statut}</span></td>
                    <td>${aff.date}</td>
                </tr>
            `).join('');

            // Update chart
            if (affectationsChart) {
                affectationsChart.destroy();
            }

            const ctx = document.getElementById('affectationsChart').getContext('2d');
            affectationsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.stats.labels,
                    datasets: [{
                        label: 'Nombre d\'affectations',
                        data: data.stats.data,
                        backgroundColor: [
                            'rgba(5, 150, 105, 0.8)',  // Valid
                            'rgba(239, 68, 68, 0.8)',  // Rejected
                            'rgba(245, 158, 11, 0.8)', // Pending
                            'rgba(107, 114, 128, 0.8)' // Cancelled
                        ],
                        borderColor: [
                            'rgb(5, 150, 105)',
                            'rgb(239, 68, 68)',
                            'rgb(245, 158, 11)',
                            'rgb(107, 114, 128)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Répartition des affectations par statut'
                        },
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading affectations data:', error);
        }
    }

    // Load data when year changes
    reportYear.addEventListener('change', function() {
        loadAffectationsData(this.value);
    });

    // Load initial data
    if (reportYear.value) {
        loadAffectationsData(reportYear.value);
    }
});

// Submit filters automatically
function submitFilters() {
    console.log('🔄 Submitting filters...');
    document.getElementById('filterForm').submit();
}

// Perform search
function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    console.log('🔍 Searching for:', searchValue);

    // Add search to form and submit
    const form = document.getElementById('filterForm');
    let searchInput = form.querySelector('input[name="search"]');
    if (!searchInput) {
        searchInput = document.createElement('input');
        searchInput.type = 'hidden';
        searchInput.name = 'search';
        form.appendChild(searchInput);
    }
    searchInput.value = searchValue;
    form.submit();
}

// Clear all filters
function clearFilters() {
    console.log('🧹 Clearing all filters...');
    const currentUrl = new URL(window.location.href);
    const annee = currentUrl.searchParams.get('annee');

    // Keep only the year parameter if it exists
    let newUrl = '{{ route("coordonnateur.historique") }}';
    if (annee) {
        newUrl += '?annee=' + annee;
    }

    window.location.href = newUrl;
}

// Function to get affectations data for the selected year (reuse your API)
async function getAffectationsData(year) {
    const response = await fetch(`/coordonnateur/api/affectations/${year}`);
    return await response.json();
}

function renderPieChart(stats) {
    const canvas = document.createElement('canvas');
    canvas.id = 'pieChartForPDF';
    canvas.width = 500;
    canvas.height = 500;
    document.body.appendChild(canvas);
    const ctx = canvas.getContext('2d');
    const chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Validées', 'Rejetées', 'En Attente', 'Annulées'],
            datasets: [{
                data: [stats.valide, stats.rejete, stats.en_attente, stats.annule],
                backgroundColor: [
                    '#059669',
                    '#ef4444',
                    '#f59e0b',
                    '#6b7280'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        font: { size: 18 }
                    }
                },
                title: {
                    display: true,
                    text: 'Répartition des Affectations par Statut',
                    font: { size: 22 }
                }
            }
        }
    });
    return chart;
}

// Download PDF logic
const downloadBtn = document.getElementById('downloadReportBtn');
downloadBtn.addEventListener('click', async function() {
    const year = document.getElementById('reportYear').value;
    const data = await getAffectationsData(year);
    // Render pie chart offscreen
    const chart = renderPieChart(data.stats);
    setTimeout(() => {
        const chartImage = chart.toBase64Image();
        chart.destroy();
        document.getElementById('pieChartForPDF').remove();
        document.getElementById('pdfYearInput').value = year;
        document.getElementById('pdfChartImageInput').value = chartImage;
        document.getElementById('pdfDownloadForm').submit();
    }, 600);
});
</script>
@endpush
