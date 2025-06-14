@extends('layouts.vacataire')

@section('title', 'Détails UE - ' . $ue->code)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-primary">
                        <i class="fas fa-book me-2"></i>{{ $ue->code }} - {{ $ue->nom }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('vacataire.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('vacataire.unites-enseignement') }}">Mes UEs</a></li>
                            <li class="breadcrumb-item active">{{ $ue->code }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('vacataire.unites-enseignement') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- UE Information -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: #7c3aed;">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de l'UE
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Code UE</label>
                                <div class="fw-bold">{{ $ue->code }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Nom complet</label>
                                <div class="fw-bold">{{ $ue->nom }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Filière</label>
                                <div class="fw-bold">{{ $ue->filiere->nom ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Semestre</label>
                                <div class="fw-bold">{{ $ue->semestre }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Département</label>
                                <div class="fw-bold">{{ $ue->filiere->departement->nom ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small">Spécialité requise</label>
                                <div class="fw-bold">
                                    @if($ue->specialite)
                                        <span class="badge bg-secondary">{{ $ue->specialite }}</span>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: #7c3aed;">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Volume Horaire
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="text-muted">Heures CM</span>
                                <strong class="text-danger">{{ $ue->heures_cm ?? 0 }}h</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="text-muted">Heures TD</span>
                                <strong class="text-success">{{ $ue->heures_td ?? 0 }}h</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                <span class="text-muted">Heures TP</span>
                                <strong class="text-info">{{ $ue->heures_tp ?? 0 }}h</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-2 bg-primary text-white rounded">
                                <span>Total</span>
                                <strong>{{ ($ue->heures_cm ?? 0) + ($ue->heures_td ?? 0) + ($ue->heures_tp ?? 0) }}h</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Affectations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient text-white" style="background: #7c3aed;">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>Mes Affectations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($affectations as $affectation)
                            <div class="col-md-6 col-lg-4">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        @php
                                            $badgeClass = $affectation->type_seance === 'CM' ? 'bg-danger' : 
                                                         ($affectation->type_seance === 'TD' ? 'bg-success' : 'bg-info');
                                        @endphp
                                        <div class="mb-2">
                                            <span class="badge {{ $badgeClass }} fs-6">{{ $affectation->type_seance }}</span>
                                        </div>
                                        <div class="text-muted small">
                                            Affecté le {{ \Carbon\Carbon::parse($affectation->created_at)->format('d/m/Y') }}
                                        </div>
                                        <div class="mt-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Validée
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emploi du Temps -->
    @if($schedules->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient text-white" style="background: #7c3aed;">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Emploi du Temps
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jour</th>
                                        <th>Heure</th>
                                        <th>Type</th>
                                        <th>Groupe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->jour_semaine }}</td>
                                            <td>{{ \Carbon\Carbon::parse($schedule->heure_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->heure_fin)->format('H:i') }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = $schedule->type_seance === 'CM' ? 'bg-danger' : 
                                                                 ($schedule->type_seance === 'TD' ? 'bg-success' : 'bg-info');
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $schedule->type_seance }}</span>
                                            </td>
                                            <td>{{ $schedule->groupe ?? 'Tous' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if($notes->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-gradient text-white" style="background: #7c3aed;">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>Notes des Étudiants
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Note Normale</th>
                                        <th>Note Rattrapage</th>
                                        <th>Session</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notes as $note)
                                        <tr>
                                            <td>{{ $note->etudiant->nom ?? 'N/A' }}</td>
                                            <td>{{ $note->note_normale ?? '-' }}</td>
                                            <td>{{ $note->note_rattrapage ?? '-' }}</td>
                                            <td>{{ $note->session ?? 'normale' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bg-gradient {
    background: linear-gradient(135deg, #7c3aed, #a855f7) !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(124, 58, 237, 0.15) !important;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📖 VACATAIRE UE DETAILS PAGE INITIALIZED');
    console.log('💜 UE Code:', '{{ $ue->code }}');
    console.log('💜 Session Types:', @json($sessionTypes));
    console.log('💜 Total Affectations:', {{ $affectations->count() }});
    console.log('💜 Total Schedules:', {{ $schedules->count() }});
    console.log('💜 Total Notes:', {{ $notes->count() }});
});
</script>
@endpush
@endsection
