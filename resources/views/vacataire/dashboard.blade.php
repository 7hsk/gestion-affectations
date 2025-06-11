@extends('layouts.vacataire')

@section('title', 'Tableau de Bord Vacataire')

@push('styles')
<style>
/* Welcome section with floating animation */
.card[style*="background: #7c3aed"] {
    position: relative;
    overflow: hidden;
}

.card[style*="background: #7c3aed"]::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
    animation: float-welcome 8s ease-in-out infinite;
}

.card[style*="background: #7c3aed"]::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.05) 0%, transparent 50%, rgba(255,255,255,0.02) 100%);
    pointer-events: none;
}

@keyframes float-welcome {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-30px) rotate(180deg); }
}

/* Floating geometric shapes */
.vacataire-floating-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.vacataire-floating-shape {
    position: absolute;
    opacity: 0.1;
    animation: float-shape 15s ease-in-out infinite;
}

.vacataire-floating-shape:nth-child(1) {
    top: 20%;
    left: 10%;
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #7c3aed, #8b5cf6);
    border-radius: 50%;
    animation-delay: 0s;
}

.vacataire-floating-shape:nth-child(2) {
    top: 60%;
    right: 15%;
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #a855f7, #c084fc);
    border-radius: 20px;
    animation-delay: 2s;
}

.vacataire-floating-shape:nth-child(3) {
    bottom: 30%;
    left: 20%;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #9333ea, #a855f7);
    transform: rotate(45deg);
    animation-delay: 4s;
}

.vacataire-floating-shape:nth-child(4) {
    top: 40%;
    left: 60%;
    width: 50px;
    height: 50px;
    background: linear-gradient(45deg, #f97316, #fb923c);
    border-radius: 50%;
    animation-delay: 6s;
}

@keyframes float-shape {
    0%, 100% {
        transform: translateY(0px) rotate(0deg) scale(1);
        opacity: 0.1;
    }
    25% {
        transform: translateY(-20px) rotate(90deg) scale(1.1);
        opacity: 0.2;
    }
    50% {
        transform: translateY(-40px) rotate(180deg) scale(0.9);
        opacity: 0.15;
    }
    75% {
        transform: translateY(-20px) rotate(270deg) scale(1.05);
        opacity: 0.25;
    }
}
</style>
@endpush

@section('content')
<!-- Floating Background Shapes -->
<div class="vacataire-floating-shapes">
    <div class="vacataire-floating-shape"></div>
    <div class="vacataire-floating-shape"></div>
    <div class="vacataire-floating-shape"></div>
    <div class="vacataire-floating-shape"></div>
</div>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg" style="background: #7c3aed; color: white;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">👋 Bienvenue, {{ Auth::user()->name }}!</h2>
                            <p class="mb-0 opacity-90">
                                Gérez vos unités d'enseignement et vos notes en tant que vacataire à l'ENSA Al Hoceima
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end">
                                <div class="bg-white bg-opacity-20 rounded-circle p-3">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-book text-primary fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-primary mb-1">{{ $stats['ues_assignees'] }}</h3>
                    <p class="text-muted mb-0">UEs Assignées</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-warning mb-1">{{ $stats['heures_totales'] }}h</h3>
                    <p class="text-muted mb-0">Heures Totales</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-graduation-cap text-success fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-success mb-1">{{ $stats['notes_saisies'] }}</h3>
                    <p class="text-muted mb-0">Notes Saisies</p>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-calendar-alt text-info fa-2x"></i>
                        </div>
                    </div>
                    <h3 class="text-info mb-1">{{ $stats['emploi_du_temps'] }}</h3>
                    <p class="text-muted mb-0">Créneaux Horaires</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- UEs Assignées -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Mes UEs Assignées
                    </h5>
                </div>
                <div class="card-body">
                    @if($uesAssignees->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($uesAssignees->take(5) as $affectation)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 text-primary">{{ $affectation->uniteEnseignement->code }}</h6>
                                            <p class="mb-1">{{ $affectation->uniteEnseignement->nom }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                {{ $affectation->uniteEnseignement->filiere->nom }}
                                            </small>
                                        </div>
                                        <span class="badge bg-warning">{{ $affectation->type_seance ?? 'CM' }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($uesAssignees->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('vacataire.unites-enseignement') }}" class="btn btn-outline-primary btn-sm">
                                    Voir toutes les UEs
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune UE assignée pour le moment</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Emploi du Temps -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Emploi du Temps
                    </h5>
                </div>
                <div class="card-body">
                    @if($emploiDuTemps->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($emploiDuTemps->take(5) as $schedule)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $schedule->uniteEnseignement->code }}</h6>
                                            <small class="text-muted">
                                                {{ $schedule->jour_semaine }} • 
                                                {{ substr($schedule->heure_debut, 0, 5) }}-{{ substr($schedule->heure_fin, 0, 5) }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $schedule->type_seance == 'CM' ? 'danger' : ($schedule->type_seance == 'TD' ? 'success' : 'info') }}">
                                            {{ $schedule->type_seance }}
                                            @if($schedule->group_number)
                                                -G{{ $schedule->group_number }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('vacataire.emploi-du-temps') }}" class="btn btn-outline-warning btn-sm">
                                Voir l'emploi complet
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun créneau programmé</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Notes Récentes
                    </h5>
                </div>
                <div class="card-body">
                    @if($notesRecentes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>UE</th>
                                        <th>Étudiant</th>
                                        <th>Note Normale</th>
                                        <th>Note Rattrapage</th>
                                        <th>Session</th>
                                        <th>Dernière Modification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notesRecentes->take(10) as $note)
                                        <tr>
                                            <td>
                                                <strong>{{ $note->uniteEnseignement->code }}</strong><br>
                                                <small class="text-muted">{{ $note->uniteEnseignement->nom }}</small>
                                            </td>
                                            <td>{{ $note->etudiant->nom ?? 'N/A' }}</td>
                                            <td>
                                                @if($note->note_normale !== null)
                                                    <span class="badge bg-{{ $note->note_normale >= 10 ? 'success' : 'danger' }}">
                                                        {{ $note->note_normale }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($note->note_rattrapage !== null)
                                                    <span class="badge bg-{{ $note->note_rattrapage >= 10 ? 'success' : 'danger' }}">
                                                        {{ $note->note_rattrapage }}/20
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $note->session ?? 'Normale' }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $note->updated_at->diffForHumans() }}
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-outline-success">
                                Gérer toutes les notes
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune note saisie pour le moment</p>
                            <a href="{{ route('vacataire.notes') }}" class="btn btn-primary">
                                Commencer la saisie des notes
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎓 VACATAIRE DASHBOARD INITIALIZED');
    console.log('💜 Purple-Orange theme loaded successfully');
    console.log('📊 Statistics:', {
        ues: {{ $stats['ues_assignees'] }},
        heures: {{ $stats['heures_totales'] }},
        notes: {{ $stats['notes_saisies'] }},
        emploi: {{ $stats['emploi_du_temps'] }}
    });
});
</script>
@endpush
@endsection
