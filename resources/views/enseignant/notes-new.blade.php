@extends('layouts.enseignant')

@section('title', 'Gestion des notes')

@push('styles')
<style>
/* Enhanced gradient styling to match UE view */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.card {
    border-radius: 12px;
    transition: transform 0.2s ease-in-out;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.card:hover {
    transform: translateY(-2px);
}

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

.nav-pills .nav-link {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.stats-card {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border: 1px solid rgba(102, 126, 234, 0.2);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header with Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-1">
                                <i class="fas fa-graduation-cap me-2"></i>
                                Gestion des Notes
                            </h2>
                            <p class="mb-0 opacity-75">Gérez les notes de vos unités d'enseignement</p>
                        </div>
                        <div class="col-md-6">
                            <div class="row text-center">
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $unites->count() }}</h4>
                                    <small class="text-white-50">UEs</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $statistics['total_students'] ?? 0 }}</h4>
                                    <small class="text-white-50">Étudiants</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ $statistics['graded_students'] ?? 0 }}</h4>
                                    <small class="text-white-50">Notes saisies</small>
                                </div>
                                <div class="col-3">
                                    <h4 class="text-white mb-0">{{ round($statistics['success_rate'] ?? 0, 1) }}%</h4>
                                    <small class="text-white-50">Taux de réussite</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UE and Session Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('enseignant.notes') }}" class="row g-3">
                        <div class="col-md-5">
                            <label for="ue_id" class="form-label">
                                <i class="fas fa-book me-1"></i>Sélectionner une UE
                            </label>
                            <select class="form-select" id="ue_id" name="ue_id" required>
                                <option value="">Choisir une unité d'enseignement</option>
                                @foreach($unites as $unite)
                                    <option value="{{ $unite->id }}" {{ $selectedUeId == $unite->id ? 'selected' : '' }}>
                                        {{ $unite->code }} - {{ $unite->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="session_type" class="form-label">
                                <i class="fas fa-calendar me-1"></i>Type de session
                            </label>
                            <select class="form-select" id="session_type" name="session_type">
                                @foreach($sessionTypes as $key => $label)
                                    <option value="{{ $key }}" {{ $selectedSession == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Charger
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <a href="{{ route('enseignant.dashboard') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($selectedUe)
        <!-- UE Information Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">
                                    <i class="fas fa-book-open me-2"></i>
                                    {{ $selectedUe->code }} - {{ $selectedUe->nom }}
                                </h4>
                                <p class="mb-0 opacity-75">
                                    <span class="badge bg-light text-dark me-2">{{ $sessionTypes[$selectedSession] }}</span>
                                    Filière: {{ $selectedUe->filiere->nom ?? 'Non assignée' }} |
                                    Semestre: {{ $selectedUe->semestre }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <!-- Import removed - not in allowed list -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content with Three Sections -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-pills nav-fill mb-4" id="notesTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="add-notes-tab" data-bs-toggle="tab" data-bs-target="#add-notes" type="button" role="tab">
                                    <i class="fas fa-plus-circle me-2"></i>Saisir les Notes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="view-notes-tab" data-bs-toggle="tab" data-bs-target="#view-notes" type="button" role="tab">
                                    <i class="fas fa-list me-2"></i>Consulter les Notes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="#statistics" type="button" role="tab">
                                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="notesTabContent">
                            <!-- Section 1: Add Notes -->
                            <div class="tab-pane fade show active" id="add-notes" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-edit me-2"></i>
                                            Saisie des Notes - {{ $sessionTypes[$selectedSession] }}
                                        </h5>

                                        @if($etudiants->isNotEmpty())
                                            <form method="POST" action="{{ route('enseignant.notes.store') }}">
                                                @csrf
                                                <input type="hidden" name="ue_id" value="{{ $selectedUeId }}">
                                                <input type="hidden" name="session_type" value="{{ $selectedSession }}">

                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="15%">Matricule</th>
                                                                <th width="30%">Nom Complet</th>
                                                                <th width="15%">Filière</th>
                                                                <th width="20%">Note (/20)</th>
                                                                <th width="10%">Absent</th>
                                                                <th width="10%">Note Actuelle</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($etudiants as $etudiant)
                                                                @php
                                                                    $existingNote = $existingNotes->get($etudiant->id)?->get($selectedSession)?->first();
                                                                @endphp
                                                                <tr>
                                                                    <td>
                                                                        <strong>{{ $etudiant->matricule }}</strong>
                                                                    </td>
                                                                    <td>{{ $etudiant->name }}</td>
                                                                    <td>
                                                                        <span class="badge bg-secondary">{{ $etudiant->filiere }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <input type="number"
                                                                               class="form-control"
                                                                               name="notes[{{ $etudiant->id }}]"
                                                                               min="0"
                                                                               max="20"
                                                                               step="0.25"
                                                                               value="{{ $existingNote->note ?? '' }}"
                                                                               placeholder="0.00">
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-check">
                                                                            <input type="checkbox"
                                                                                   class="form-check-input"
                                                                                   name="absences[{{ $etudiant->id }}]"
                                                                                   {{ $existingNote && $existingNote->is_absent ? 'checked' : '' }}>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        @if($existingNote)
                                                                            @if($existingNote->is_absent)
                                                                                <span class="badge bg-warning">Absent</span>
                                                                            @else
                                                                                <span class="badge bg-success">{{ $existingNote->note }}/20</span>
                                                                            @endif
                                                                        @else
                                                                            <span class="badge bg-secondary">Non saisi</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <div class="d-flex justify-content-between align-items-center mt-4">
                                                    <div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Les notes sont sur 20. Cochez "Absent" pour marquer un étudiant absent.
                                                        </small>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-lg">
                                                        <i class="fas fa-save me-2"></i>Enregistrer les Notes
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun étudiant trouvé</h5>
                                                <p class="text-muted">Aucun étudiant n'est inscrit dans cette UE.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: View Notes -->
                            <div class="tab-pane fade" id="view-notes" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-3">
                                            <i class="fas fa-eye me-2"></i>
                                            Consultation des Notes - {{ $sessionTypes[$selectedSession] }}
                                        </h5>

                                        <!-- Filter Options -->
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <select class="form-select" id="filterStatus">
                                                    <option value="">Tous les étudiants</option>
                                                    <option value="graded">Notes saisies</option>
                                                    <option value="pending">En attente</option>
                                                    <option value="absent">Absents</option>
                                                    <option value="passed">Admis (≥10)</option>
                                                    <option value="failed">Échec (<10)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="searchStudent" placeholder="Rechercher un étudiant...">
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn btn-outline-info" onclick="window.print()">
                                                    <i class="fas fa-print me-1"></i>Imprimer
                                                </button>
                                            </div>
                                        </div>

                                        @if($etudiants->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="notesTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Matricule</th>
                                                            <th>Nom Complet</th>
                                                            <th>Filière</th>
                                                            <th>Note</th>
                                                            <th>Statut</th>
                                                            <th>Date de saisie</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($etudiants as $etudiant)
                                                            @php
                                                                $existingNote = $existingNotes->get($etudiant->id)?->get($selectedSession)?->first();
                                                            @endphp
                                                            <tr class="student-row"
                                                                data-status="{{ $existingNote ? ($existingNote->is_absent ? 'absent' : ($existingNote->note >= 10 ? 'passed' : 'failed')) : 'pending' }}"
                                                                data-name="{{ strtolower($etudiant->name) }}"
                                                                data-matricule="{{ $etudiant->matricule }}">
                                                                <td><strong>{{ $etudiant->matricule }}</strong></td>
                                                                <td>{{ $etudiant->name }}</td>
                                                                <td><span class="badge bg-secondary">{{ $etudiant->filiere }}</span></td>
                                                                <td>
                                                                    @if($existingNote)
                                                                        @if($existingNote->is_absent)
                                                                            <span class="badge bg-warning fs-6">Absent</span>
                                                                        @else
                                                                            <span class="badge {{ $existingNote->note >= 10 ? 'bg-success' : 'bg-danger' }} fs-6">
                                                                                {{ $existingNote->note }}/20
                                                                            </span>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge bg-secondary fs-6">Non saisi</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($existingNote)
                                                                        @if($existingNote->is_absent)
                                                                            <span class="badge bg-warning">Absent</span>
                                                                        @elseif($existingNote->note >= 10)
                                                                            <span class="badge bg-success">Admis</span>
                                                                        @else
                                                                            <span class="badge bg-danger">Échec</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge bg-secondary">En attente</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{ $existingNote ? $existingNote->created_at->format('d/m/Y H:i') : '-' }}
                                                                </td>
                                                                <td>
                                                                    @if($existingNote)
                                                                        <button class="btn btn-sm btn-outline-primary" onclick="editNote({{ $etudiant->id }}, '{{ $existingNote->note }}', {{ $existingNote->is_absent ? 'true' : 'false' }})">
                                                                            <i class="fas fa-edit"></i>
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucun étudiant trouvé</h5>
                                                <p class="text-muted">Aucun étudiant n'est inscrit dans cette UE.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Statistics -->
                            <div class="tab-pane fade" id="statistics" role="tabpanel">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-4">
                                            <i class="fas fa-chart-bar me-2"></i>
                                            Statistiques - {{ $sessionTypes[$selectedSession] }}
                                        </h5>

                                        @if(!empty($statistics) && $statistics['graded_students'] > 0)
                                            <!-- Statistics Cards -->
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <div class="card stats-card">
                                                        <div class="card-body text-center">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                                                <i class="fas fa-chart-line text-primary fa-2x"></i>
                                                            </div>
                                                            <h4 class="text-primary">{{ $statistics['average'] }}/20</h4>
                                                            <p class="mb-1">Moyenne générale</p>
                                                            <small class="text-muted">Min: {{ $statistics['min'] }} | Max: {{ $statistics['max'] }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card stats-card">
                                                        <div class="card-body text-center">
                                                            <div class="bg-success bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                                                <i class="fas fa-check-circle text-success fa-2x"></i>
                                                            </div>
                                                            <h4 class="text-success">{{ $statistics['success_rate'] }}%</h4>
                                                            <p class="mb-1">Taux de réussite</p>
                                                            <small class="text-muted">{{ $statistics['grade_ranges']['passing'] + $statistics['grade_ranges']['average'] + $statistics['grade_ranges']['good'] + $statistics['grade_ranges']['excellent'] }} étudiants admis</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card stats-card">
                                                        <div class="card-body text-center">
                                                            <div class="bg-info bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                                                <i class="fas fa-users text-info fa-2x"></i>
                                                            </div>
                                                            <h4 class="text-info">{{ $statistics['graded_students'] }}/{{ $statistics['total_students'] }}</h4>
                                                            <p class="mb-1">Notes saisies</p>
                                                            <small class="text-muted">{{ $statistics['total_students'] - $statistics['graded_students'] }} en attente</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card stats-card">
                                                        <div class="card-body text-center">
                                                            <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
                                                                <i class="fas fa-star text-warning fa-2x"></i>
                                                            </div>
                                                            <h4 class="text-warning">{{ $statistics['median'] }}</h4>
                                                            <p class="mb-1">Médiane</p>
                                                            <small class="text-muted">Note médiane</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Grade Distribution Chart -->
                                            <div class="row mb-4">
                                                <div class="col-md-8">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">
                                                                <i class="fas fa-chart-bar me-2"></i>
                                                                Distribution des Notes
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="gradesChart" height="300"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h6 class="mb-0">
                                                                <i class="fas fa-list me-2"></i>
                                                                Répartition par Mention
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-2">
                                                                <div class="col-8">
                                                                    <small class="text-muted">Échec (0-10)</small>
                                                                </div>
                                                                <div class="col-4 text-end">
                                                                    <span class="badge bg-danger">{{ $statistics['grade_ranges']['failing'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-8">
                                                                    <small class="text-muted">Passable (10-12)</small>
                                                                </div>
                                                                <div class="col-4 text-end">
                                                                    <span class="badge bg-warning">{{ $statistics['grade_ranges']['passing'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-8">
                                                                    <small class="text-muted">Assez Bien (12-14)</small>
                                                                </div>
                                                                <div class="col-4 text-end">
                                                                    <span class="badge bg-info">{{ $statistics['grade_ranges']['average'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-8">
                                                                    <small class="text-muted">Bien (14-16)</small>
                                                                </div>
                                                                <div class="col-4 text-end">
                                                                    <span class="badge bg-success">{{ $statistics['grade_ranges']['good'] }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-8">
                                                                    <small class="text-muted">Très Bien (16-20)</small>
                                                                </div>
                                                                <div class="col-4 text-end">
                                                                    <span class="badge bg-primary">{{ $statistics['grade_ranges']['excellent'] }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                                                <h5 class="text-muted">Aucune statistique disponible</h5>
                                                <p class="text-muted">Saisissez des notes pour voir les statistiques.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No UE Selected -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-mouse-pointer fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted mb-3">Sélectionnez une UE</h4>
                        <p class="text-muted mb-4">Choisissez une unité d'enseignement pour commencer à gérer les notes.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Import Modal removed - not in allowed list -->

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart for grade distribution
@if(isset($gradeDistribution) && !empty($gradeDistribution))
    const ctx = document.getElementById('gradesChart');
    if (ctx) {
        const gradesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['0-5', '5-10', '10-15', '15-20'],
                datasets: [{
                    label: 'Nombre d\'étudiants',
                    data: [
                        {{ $gradeDistribution['0-5'] ?? 0 }},
                        {{ $gradeDistribution['5-10'] ?? 0 }},
                        {{ $gradeDistribution['10-15'] ?? 0 }},
                        {{ $gradeDistribution['15-20'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(220, 53, 69, 0.7)',   // Red for 0-5
                        'rgba(255, 193, 7, 0.7)',   // Yellow for 5-10
                        'rgba(25, 135, 84, 0.7)',   // Green for 10-15
                        'rgba(13, 110, 253, 0.7)'   // Blue for 15-20
                    ],
                    borderColor: [
                        'rgba(220, 53, 69, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(13, 110, 253, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribution des Notes'
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
    }
@endif

// Filter functionality
document.getElementById('filterStatus')?.addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('.student-row');

    rows.forEach(row => {
        if (filterValue === '' || row.dataset.status === filterValue ||
            (filterValue === 'graded' && row.dataset.status !== 'pending')) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Search functionality
document.getElementById('searchStudent')?.addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');

    rows.forEach(row => {
        const name = row.dataset.name;
        const matricule = row.dataset.matricule.toLowerCase();

        if (name.includes(searchValue) || matricule.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Edit note function
function editNote(studentId, currentNote, isAbsent) {
    // This would open a modal or inline edit
    console.log('Edit note for student:', studentId, currentNote, isAbsent);
}

// Export function removed - not in allowed list
</script>
@endpush
@endsection