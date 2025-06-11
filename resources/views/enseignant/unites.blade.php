@extends('layouts.teacher')

@section('title', 'Unités d\'enseignement')

@section('content')
<div class="container-fluid">
    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h3 class="mb-1">Mes Unités d'Enseignement</h3>
                            <p class="text-muted mb-0">Gestion de vos cours et modules assignés</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h4 class="text-primary mb-0">{{ $summary['total_ues'] }}</h4>
                                    <small class="text-muted">Total UEs</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success mb-0">{{ $summary['total_hours'] }}h</h4>
                                    <small class="text-muted">Total heures</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-info mb-0">{{ count($availableSemesters) }}</h4>
                                    <small class="text-muted">Semestres</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('enseignant.unites') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Rechercher</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search"
                                       placeholder="Code ou nom de l'UE..." value="{{ $filters['search'] ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="semestre" class="form-label">Semestre</label>
                            <select class="form-select" id="semestre" name="semestre">
                                <option value="">Tous les semestres</option>
                                @foreach($availableSemesters as $sem)
                                    <option value="{{ $sem }}" {{ ($filters['semestre'] ?? '') == $sem ? 'selected' : '' }}>
                                        Semestre {{ $sem }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('enseignant.unites') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="dropdown d-grid">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-download me-1"></i>Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Type Distribution -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">Répartition par type de séance</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-chalkboard-teacher text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $summary['by_type']['CM'] }}</h5>
                                    <small class="text-muted">Cours Magistraux</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-users text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $summary['by_type']['TD'] }}</h5>
                                    <small class="text-muted">Travaux Dirigés</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                    <i class="fas fa-laptop-code text-info"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $summary['by_type']['TP'] }}</h5>
                                    <small class="text-muted">Travaux Pratiques</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UEs List -->
    <div class="row">
        <div class="col-12">
            @if($groupedUnites->isEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book-open text-muted fa-3x mb-3"></i>
                        <h5 class="text-muted">Aucune unité d'enseignement assignée</h5>
                        <p class="text-muted">Vous n'avez actuellement aucune UE assignée pour cette période.</p>
                        <a href="#" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Demander une affectation
                        </a>
                    </div>
                </div>
            @else
                @foreach($groupedUnites as $semestre => $unites)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    Semestre {{ $semestre }}
                                </h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    {{ $unites->count() }} UE(s)
                                </span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="border-0">Code</th>
                                            <th class="border-0">Intitulé</th>
                                            <th class="border-0">Type</th>
                                            <th class="border-0">Volume horaire</th>
                                            <th class="border-0">Filière</th>
                                            <th class="border-0">Étudiants</th>
                                            <th class="border-0">Performance</th>
                                            <th class="border-0">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($unites as $unite)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ $unite->code }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-medium">{{ $unite->nom }}</div>
                                                        <small class="text-muted">{{ $unite->departement->nom ?? 'N/A' }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge
                                                        @if($unite->type_seance == 'CM') bg-primary
                                                        @elseif($unite->type_seance == 'TD') bg-success
                                                        @else bg-info @endif">
                                                        {{ $unite->type_seance }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <small><strong>{{ $unite->total_hours }}h</strong> total</small>
                                                        <small class="text-muted">
                                                            CM: {{ $unite->heures_cm }}h |
                                                            TD: {{ $unite->heures_td }}h |
                                                            TP: {{ $unite->heures_tp }}h
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $unite->filiere->nom ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-users text-muted me-1"></i>
                                                        <span>{{ $unite->student_count }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($unite->notes_stats['total'] > 0)
                                                        <div class="d-flex flex-column">
                                                            <small>
                                                                <i class="fas fa-chart-line text-success me-1"></i>
                                                                {{ $unite->notes_stats['average'] }}/20
                                                            </small>
                                                            <small class="text-muted">
                                                                {{ $unite->notes_stats['success_rate'] }}% réussite
                                                            </small>
                                                        </div>
                                                    @else
                                                        <small class="text-muted">
                                                            <i class="fas fa-minus-circle me-1"></i>
                                                            Pas de notes
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('enseignant.notes', ['ue_id' => $unite->id]) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Gérer les notes">
                                                            <i class="fas fa-graduation-cap"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-info" title="Détails"
                                                                data-bs-toggle="modal" data-bs-target="#ueModal{{ $unite->id }}">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                        <div class="btn-group" role="group">
                                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                    data-bs-toggle="dropdown" title="Plus d'actions">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="#">
                                                                        <i class="fas fa-calendar me-2"></i>Emploi du temps
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#">
                                                                        <i class="fas fa-users me-2"></i>Liste étudiants
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#">
                                                                        <i class="fas fa-download me-2"></i>Exporter données
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- UE Details Modal -->
                                            <div class="modal fade" id="ueModal{{ $unite->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ $unite->code }} - {{ $unite->nom }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <h6>Informations générales</h6>
                                                                    <table class="table table-sm">
                                                                        <tr>
                                                                            <td><strong>Code:</strong></td>
                                                                            <td>{{ $unite->code }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Semestre:</strong></td>
                                                                            <td>{{ $unite->semestre }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Filière:</strong></td>
                                                                            <td>{{ $unite->filiere->nom ?? 'N/A' }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td><strong>Type de séance:</strong></td>
                                                                            <td>{{ $unite->type_seance }}</td>
                                                                        </tr>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <h6>Volume horaire</h6>
                                                                    <div class="row text-center">
                                                                        <div class="col-4">
                                                                            <div class="bg-primary bg-opacity-10 rounded p-2">
                                                                                <h5 class="text-primary mb-0">{{ $unite->heures_cm }}</h5>
                                                                                <small>CM</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="bg-success bg-opacity-10 rounded p-2">
                                                                                <h5 class="text-success mb-0">{{ $unite->heures_td }}</h5>
                                                                                <small>TD</small>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-4">
                                                                            <div class="bg-info bg-opacity-10 rounded p-2">
                                                                                <h5 class="text-info mb-0">{{ $unite->heures_tp }}</h5>
                                                                                <small>TP</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            @if($unite->notes_stats['total'] > 0)
                                                                <hr>
                                                                <h6>Statistiques des notes</h6>
                                                                <div class="row text-center">
                                                                    <div class="col-3">
                                                                        <h5 class="text-primary">{{ $unite->notes_stats['total'] }}</h5>
                                                                        <small>Notes saisies</small>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <h5 class="text-success">{{ $unite->notes_stats['average'] }}</h5>
                                                                        <small>Moyenne</small>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <h5 class="text-info">{{ $unite->notes_stats['success_rate'] }}%</h5>
                                                                        <small>Réussite</small>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <h5 class="text-warning">{{ $unite->student_count }}</h5>
                                                                        <small>Étudiants</small>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                            <a href="{{ route('enseignant.notes', ['ue_id' => $unite->id]) }}" class="btn btn-primary">
                                                                Gérer les notes
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    document.getElementById('semestre').addEventListener('change', function() {
        this.form.submit();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
});
</script>
@endsection