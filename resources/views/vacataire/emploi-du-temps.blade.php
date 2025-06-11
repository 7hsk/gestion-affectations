@extends('layouts.vacataire')

@section('title', 'Mon Emploi du Temps')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Mon Emploi du Temps
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge bg-light text-warning">
                                Année {{ date('Y') }}-{{ date('Y') + 1 }}
                            </span>
                            <!-- Export removed - not in allowed list -->
                            <button class="btn btn-light btn-sm" onclick="exportEmploiDuTempsPdf()">
                                <i class="fas fa-file-pdf me-1"></i>Exporter PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Consultez votre emploi du temps hebdomadaire avec tous vos créneaux d'enseignement.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Legend -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3">
                        <i class="fas fa-info-circle me-2"></i>Légende des Types de Séances
                    </h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="schedule-legend cm me-2"></div>
                                <span>Cours Magistral (CM)</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="schedule-legend td me-2"></div>
                                <span>Travaux Dirigés (TD)</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="schedule-legend tp me-2"></div>
                                <span>Travaux Pratiques (TP)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered schedule-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="time-header">Horaires</th>
                                    @foreach($jours as $jour)
                                        <th class="day-header text-center">{{ $jour }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($heures as $heure)
                                    <tr>
                                        <td class="time-slot">
                                            <strong>{{ $heure }}</strong>
                                        </td>
                                        @foreach($jours as $jour)
                                            <td class="schedule-cell">
                                                @if(isset($emploiDuTemps[$jour][$heure]) && $emploiDuTemps[$jour][$heure]->count() > 0)
                                                    @foreach($emploiDuTemps[$jour][$heure] as $schedule)
                                                        <div class="schedule-item {{ strtolower($schedule->type_seance) }}">
                                                            <div class="schedule-header">
                                                                <strong>{{ $schedule->uniteEnseignement->code }}</strong>
                                                                <span class="schedule-type">{{ $schedule->type_seance }}</span>
                                                            </div>
                                                            <div class="schedule-title">
                                                                {{ Str::limit($schedule->uniteEnseignement->nom, 30) }}
                                                            </div>
                                                            <div class="schedule-details">
                                                                @if($schedule->group_number)
                                                                    <small class="text-muted">Groupe {{ $schedule->group_number }}</small>
                                                                @endif
                                                                @if($schedule->salle)
                                                                    <small class="text-muted">• {{ $schedule->salle }}</small>
                                                                @endif
                                                            </div>
                                                            <div class="schedule-filiere">
                                                                <small class="badge bg-secondary">
                                                                    {{ $schedule->uniteEnseignement->filiere->nom }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="empty-slot">
                                                        <small class="text-muted">Libre</small>
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Summary -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Répartition Hebdomadaire
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalCM = 0;
                        $totalTD = 0;
                        $totalTP = 0;
                        $totalCreneaux = 0;
                        
                        foreach($emploiDuTemps as $jour => $heuresJour) {
                            foreach($heuresJour as $heure => $schedules) {
                                foreach($schedules as $schedule) {
                                    $totalCreneaux++;
                                    if($schedule->type_seance == 'CM') $totalCM++;
                                    elseif($schedule->type_seance == 'TD') $totalTD++;
                                    elseif($schedule->type_seance == 'TP') $totalTP++;
                                }
                            }
                        }
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-danger">{{ $totalCM }}</div>
                                <div class="stat-label">CM</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-success">{{ $totalTD }}</div>
                                <div class="stat-label">TD</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-info">{{ $totalTP }}</div>
                                <div class="stat-label">TP</div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-item">
                                <div class="stat-number text-primary">{{ $totalCreneaux }}</div>
                                <div class="stat-label">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Charge Horaire
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $heuresParJour = [];
                        foreach($jours as $jour) {
                            $heuresParJour[$jour] = isset($emploiDuTemps[$jour]) ? 
                                collect($emploiDuTemps[$jour])->flatten()->count() * 2 : 0; // 2h par créneau
                        }
                        $totalHeures = array_sum($heuresParJour);
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total hebdomadaire:</span>
                            <strong class="text-primary">{{ $totalHeures }}h</strong>
                        </div>
                    </div>
                    
                    @foreach($heuresParJour as $jour => $heures)
                        @if($heures > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $jour }}:</span>
                                <span class="text-muted">{{ $heures }}h</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.schedule-table {
    font-size: 0.9rem;
}

.time-header,
.day-header {
    background: #7c3aed !important;
    color: white !important;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
}

.time-slot {
    background-color: #f8f9fa;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    width: 120px;
    color: #495057;
}

.schedule-cell {
    width: 200px;
    height: 120px;
    vertical-align: top;
    padding: 8px;
    position: relative;
}

.schedule-item {
    border-radius: 8px;
    padding: 8px;
    margin-bottom: 4px;
    color: white;
    font-size: 0.8rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.schedule-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.schedule-item.cm {
    background: #dc3545;
}

.schedule-item.td {
    background: #28a745;
}

.schedule-item.tp {
    background: #17a2b8;
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.schedule-type {
    background: rgba(255,255,255,0.2);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: bold;
}

.schedule-title {
    font-weight: 500;
    line-height: 1.2;
    margin-bottom: 4px;
}

.schedule-details {
    font-size: 0.7rem;
    opacity: 0.9;
}

.schedule-filiere {
    margin-top: 4px;
}

.schedule-filiere .badge {
    font-size: 0.6rem;
    padding: 2px 4px;
}

.empty-slot {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-style: italic;
}

.schedule-legend {
    width: 20px;
    height: 20px;
    border-radius: 4px;
    display: inline-block;
}

.schedule-legend.cm {
    background: #dc3545;
}

.schedule-legend.td {
    background: #28a745;
}

.schedule-legend.tp {
    background: #17a2b8;
}

.stat-item {
    padding: 1rem;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

@media (max-width: 768px) {
    .schedule-table {
        font-size: 0.8rem;
    }
    
    .schedule-cell {
        width: 150px;
        height: 100px;
        padding: 4px;
    }
    
    .schedule-item {
        padding: 4px;
        font-size: 0.7rem;
    }
    
    .time-slot {
        width: 80px;
    }
}

@media print {
    .card-header,
    .btn,
    .badge {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    
    .schedule-item {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📅 VACATAIRE EMPLOI DU TEMPS INITIALIZED');
    console.log('💜 Total créneaux:', {{ $totalCreneaux ?? 0 }});
    console.log('⏰ Total heures:', {{ $totalHeures ?? 0 }});
});

// Export function removed - not in allowed list

function printSchedule() {
    console.log('🖨️ Printing schedule...');
    window.print();
}

function exportEmploiDuTempsPdf() {
    // Open a new window for the download process
    const newWindow = window.open('about:blank', '_blank');
    if (!newWindow) {
        alert('Veuillez autoriser les pop-ups pour l\'exportation du PDF.');
        return;
    }
    newWindow.document.write('<html><head><title>Préparation de l'Emploi du Temps</title></head><body style="font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f0f2f5;"><div style="text-align: center; padding: 20px; border-radius: 10px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"><i class="fas fa-spinner fa-spin fa-3x" style="color: #007bff; margin-bottom: 20px;"></i><h2>Préparation de l'Emploi du Temps...</h2><p>Le téléchargement de votre emploi du temps va commencer sous peu.</p></div></body></html>');
    newWindow.document.close();

    // Create a temporary form to submit to the export route
    const form = document.createElement('form');
    form.action = `{{ route('vacataire.emploi-du-temps.export') }}`;
    form.method = 'POST';
    form.target = newWindow.name; // Target the newly opened window

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);

    // Set a timeout to close the window
    setTimeout(() => {
        if (newWindow && !newWindow.closed) {
            newWindow.close();
        }
    }, 3000); // Close after 3 seconds, adjust if download takes longer
}

function showNotification(message, type = 'info') {
    const notification = `
        <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        const alert = document.querySelector('.alert:last-of-type');
        if (alert) alert.remove();
    }, 5000);
}
</script>
@endpush
@endsection
