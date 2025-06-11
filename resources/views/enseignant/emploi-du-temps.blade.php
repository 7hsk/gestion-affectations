@extends('layouts.enseignant')

@section('title', 'Emploi du temps')

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

.schedule-cell {
    min-height: 120px;
    max-height: 120px;
    border: 2px solid #e2e8f0;
    position: relative;
    padding: 0.5rem;
    vertical-align: top;
    background: #f8fafc;
}

.schedule-item {
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.schedule-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.schedule-item .badge {
    font-size: 0.6rem;
    padding: 0.2rem 0.4rem;
}

.libre-slot {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #6b7280;
    font-style: italic;
    font-size: 0.8rem;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border: 2px dashed #d1d5db;
    border-radius: 8px;
}

.time-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 0.75rem;
    font-size: 0.85rem;
}

.day-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-align: center;
    padding: 0.75rem;
    font-size: 0.9rem;
}

/* Enhanced table styling */
.table-responsive {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.table {
    margin-bottom: 0;
    font-size: 0.85rem;
}

.table td, .table th {
    border-color: #e2e8f0;
    padding: 0;
}

/* Week overview styling */
.week-overview {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e2e8f0;
}

.week-overview h5 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .schedule-cell {
        min-height: 80px;
        max-height: 80px;
        padding: 0.25rem;
    }

    .schedule-item {
        font-size: 0.65rem;
        padding: 0.25rem !important;
    }

    .time-header, .day-header {
        font-size: 0.75rem;
        padding: 0.5rem;
    }

    .libre-slot {
        font-size: 0.7rem;
    }
}

@media (max-width: 576px) {
    .table-responsive {
        font-size: 0.7rem;
    }

    .schedule-cell {
        min-height: 60px;
        max-height: 60px;
    }
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
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Mon Emploi du Temps
                                </h2>
                                <p class="mb-0 opacity-75">Consultez votre planning de cours et séances</p>
                            </div>
                            <div class="col-md-6">
                                <div class="row text-center">
                                    <div class="col-3">
                                        <h4 class="text-white mb-0">{{ $schedules->flatten()->count() }}</h4>
                                        <small class="text-white-50">Cours/semaine</small>
                                    </div>
                                    <div class="col-3">
                                        <h4 class="text-white mb-0">{{ $schedules->flatten()->sum(function($schedule) { return \Carbon\Carbon::parse($schedule->heure_fin)->diffInHours(\Carbon\Carbon::parse($schedule->heure_debut)); }) }}</h4>
                                        <small class="text-white-50">Heures/semaine</small>
                                    </div>
                                    <div class="col-3">
                                        <h4 class="text-white mb-0">{{ $schedules->flatten()->pluck('salle')->unique()->count() }}</h4>
                                        <small class="text-white-50">Salles</small>
                                    </div>
                                    <div class="col-3">
                                        <h4 class="text-white mb-0">0</h4>
                                        <small class="text-white-50">Conflits</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                Planning Hebdomadaire
                            </h5>
                            <div class="btn-group">
                                <a href="{{ route('enseignant.emploi-du-temps.export') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                                </a>
                                <a href="{{ route('enseignant.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i>Retour
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">


                        <div class="table-responsive">
                            <table class="table table-bordered" style="table-layout: fixed;">
                                <thead>
                                <tr>
                                    <th class="time-header" style="width: 120px;">Horaires</th>
                                    <th class="day-header">Lundi</th>
                                    <th class="day-header">Mardi</th>
                                    <th class="day-header">Mercredi</th>
                                    <th class="day-header">Jeudi</th>
                                    <th class="day-header">Vendredi</th>
                                    <th class="day-header">Samedi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($timeSlots as $time)
                                    <tr>
                                        <td class="time-header">{{ $time['start'] }}<br>{{ $time['end'] }}</td>
                                        @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $day)
                                            <td class="schedule-cell">
                                                @php
                                                    $timeKey = $time['start'] . '-' . $time['end'];
                                                    $daySchedules = $schedules->get($day, collect());
                                                    $timeSchedules = $daySchedules->get($timeKey, collect());
                                                @endphp

                                                @if($timeSchedules->isNotEmpty())
                                                    @foreach($timeSchedules as $schedule)
                                                        @php
                                                            $typeClass = match($schedule->type_seance) {
                                                                'CM' => 'bg-danger',
                                                                'TD' => 'bg-warning text-dark',
                                                                'TP' => 'bg-info',
                                                                default => 'bg-secondary'
                                                            };

                                                            // Create abbreviation from UE name
                                                            $abbreviation = collect(explode(' ', $schedule->uniteEnseignement->nom))
                                                                ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                                                ->take(4)
                                                                ->join('');
                                                        @endphp

                                                        <div class="schedule-item {{ $typeClass }} text-white p-2 mb-1"
                                                             title="{{ $schedule->uniteEnseignement->nom }} - {{ $schedule->type_seance }}@if($schedule->group_number) Groupe {{ $schedule->group_number }}@endif">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <strong style="font-size: 0.8rem;">{{ $schedule->uniteEnseignement->code }}</strong>
                                                                <span class="badge bg-light text-dark">
                                                                    {{ $schedule->type_seance }}@if($schedule->group_number)-G{{ $schedule->group_number }}@endif
                                                                </span>
                                                            </div>
                                                            <div class="small text-center" style="font-size: 0.7rem; opacity: 0.9;">
                                                                {{ $abbreviation }}
                                                            </div>
                                                            <div class="small text-center mt-1" style="font-size: 0.65rem; opacity: 0.8;">
                                                                @if($schedule->filiere)
                                                                    {{ $schedule->filiere->nom }} - {{ $schedule->semestre }}
                                                                @endif
                                                            </div>
                                                            @if($schedule->salle)
                                                                <div class="small text-center mt-1" style="font-size: 0.65rem;">
                                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $schedule->salle }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="libre-slot">
                                                        <div class="text-center">
                                                            <i class="fas fa-clock mb-1" style="font-size: 1.2rem; opacity: 0.5;"></i>
                                                            <div style="font-size: 0.8rem; font-weight: 500;">Libre</div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h5>Légende</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-danger">
                                    <i class="fas fa-chalkboard-teacher me-1"></i>CM - Cours Magistral
                                </span>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-users me-1"></i>TD - Travaux Dirigés
                                </span>
                                <span class="badge bg-info">
                                    <i class="fas fa-laptop-code me-1"></i>TP - Travaux Pratiques
                                </span>
                            </div>

                            @if($schedules->flatten()->isNotEmpty())
                                <div class="mt-3">
                                    <h6>Statistiques</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h5 class="text-danger mb-0">{{ $schedules->flatten()->where('type_seance', 'CM')->count() }}</h5>
                                                <small class="text-muted">Cours CM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h5 class="text-warning mb-0">{{ $schedules->flatten()->where('type_seance', 'TD')->count() }}</h5>
                                                <small class="text-muted">Séances TD</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h5 class="text-info mb-0">{{ $schedules->flatten()->where('type_seance', 'TP')->count() }}</h5>
                                                <small class="text-muted">Séances TP</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h5 class="text-success mb-0">{{ $schedules->flatten()->pluck('ue_id')->unique()->count() }}</h5>
                                                <small class="text-muted">UEs différentes</small>
                                            </div>
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

    <style>
        .schedule-item {
            cursor: pointer;
            transition: all 0.2s;
        }
        .schedule-item:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
    </style>
@endsection
