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
                            <!-- PDF Export Button -->
                            <button class="btn btn-light btn-sm" onclick="exportEmploiDuTempsPdf()" id="exportPdfBtn">
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
                    @if(!empty($availableSessionTypes))
                        @php
                            $legendColClass = count($availableSessionTypes) == 1 ? 'col-12' : (count($availableSessionTypes) == 2 ? 'col-md-6' : 'col-md-4');
                        @endphp
                        <div class="row">
                            @if(in_array('CM', $availableSessionTypes))
                                <div class="{{ $legendColClass }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="schedule-legend cm me-2"></div>
                                        <span>Cours Magistral (CM)</span>
                                    </div>
                                </div>
                            @endif

                            @if(in_array('TD', $availableSessionTypes))
                                <div class="{{ $legendColClass }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="schedule-legend td me-2"></div>
                                        <span>Travaux Dirigés (TD)</span>
                                    </div>
                                </div>
                            @endif

                            @if(in_array('TP', $availableSessionTypes))
                                <div class="{{ $legendColClass }}">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="schedule-legend tp me-2"></div>
                                        <span>Travaux Pratiques (TP)</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucune séance programmée pour le moment.
                        </div>
                    @endif
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
                                    @foreach($daysOrder as $day)
                                        <th class="day-header text-center">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($timeSlots as $time)
                                    <tr>
                                        <td class="time-slot">
                                            <strong>{{ $time['start'] }}-{{ $time['end'] }}</strong>
                                        </td>
                                        @foreach($daysOrder as $day)
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
                                                                'CM' => 'cm',
                                                                'TD' => 'td',
                                                                'TP' => 'tp',
                                                                default => 'other'
                                                            };
                                                        @endphp

                                                        <div class="schedule-item {{ $typeClass }}">
                                                            <div class="schedule-header">
                                                                <strong>{{ $schedule->uniteEnseignement->code }}</strong>
                                                                @php
                                                                    $typeDisplay = $schedule->type_seance;
                                                                    // Show group for TD and TP
                                                                    if(in_array($schedule->type_seance, ['TD', 'TP']) && $schedule->group_number) {
                                                                        $typeDisplay .= '-G' . $schedule->group_number;
                                                                    }
                                                                @endphp
                                                                <span class="schedule-type">{{ $typeDisplay }}</span>
                                                            </div>
                                                            <div class="schedule-title">
                                                                {{ Str::limit($schedule->uniteEnseignement->nom, 30) }}
                                                            </div>
                                                            <div class="schedule-details">
                                                                @if(in_array($schedule->type_seance, ['TD', 'TP']) && $schedule->group_number)
                                                                    <small class="text-white-50">
                                                                        <i class="fas fa-users me-1"></i>Groupe {{ $schedule->group_number }}
                                                                    </small>
                                                                @endif
                                                                @if($schedule->salle)
                                                                    <small class="text-white-50">
                                                                        @if(in_array($schedule->type_seance, ['TD', 'TP']) && $schedule->group_number) • @endif
                                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $schedule->salle }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <div class="schedule-filiere">
                                                                <small class="badge bg-light text-dark">
                                                                    {{ $schedule->uniteEnseignement->filiere->nom ?? 'N/A' }}
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

                        foreach($schedules as $day => $daySchedules) {
                            foreach($daySchedules as $time => $timeSchedules) {
                                foreach($timeSchedules as $schedule) {
                                    $totalCreneaux++;
                                    if($schedule->type_seance == 'CM') $totalCM++;
                                    elseif($schedule->type_seance == 'TD') $totalTD++;
                                    elseif($schedule->type_seance == 'TP') $totalTP++;
                                }
                            }
                        }
                    @endphp
                    
                    @php
                        $sessionTypesCount = count($availableSessionTypes);
                        $colClass = $sessionTypesCount == 1 ? 'col-6' : ($sessionTypesCount == 2 ? 'col-4' : 'col-3');
                    @endphp

                    <div class="row text-center">
                        @if(in_array('CM', $availableSessionTypes))
                            <div class="{{ $colClass }}">
                                <div class="stat-item">
                                    <div class="stat-number text-danger">{{ $totalCM }}</div>
                                    <div class="stat-label">CM</div>
                                </div>
                            </div>
                        @endif

                        @if(in_array('TD', $availableSessionTypes))
                            <div class="{{ $colClass }}">
                                <div class="stat-item">
                                    <div class="stat-number text-success">{{ $totalTD }}</div>
                                    <div class="stat-label">TD</div>
                                </div>
                            </div>
                        @endif

                        @if(in_array('TP', $availableSessionTypes))
                            <div class="{{ $colClass }}">
                                <div class="stat-item">
                                    <div class="stat-number text-info">{{ $totalTP }}</div>
                                    <div class="stat-label">TP</div>
                                </div>
                            </div>
                        @endif

                        <div class="{{ $colClass }}">
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
                        foreach($daysOrder as $day) {
                            $daySchedules = $schedules->get($day, collect());
                            $heuresParJour[$day] = $daySchedules->flatten()->count() * 2; // 2h par créneau
                        }
                        $totalHeures = array_sum($heuresParJour);
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total hebdomadaire:</span>
                            <strong class="text-primary">{{ $totalHeures }}h</strong>
                        </div>
                    </div>
                    
                    @foreach($heuresParJour as $day => $heures)
                        @if($heures > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $day }}:</span>
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
    console.log('🔥 EXPORT PDF BUTTON CLICKED!');

    try {
        // Show immediate feedback
        showNotification('Préparation du PDF en cours...', 'info');

        // Open a new window for the download process
        console.log('🔥 Opening new window...');
        const newWindow = window.open('about:blank', '_blank');

        if (!newWindow) {
            console.error('❌ Pop-up blocked!');
            alert('Veuillez autoriser les pop-ups pour l\'exportation du PDF.');
            showNotification('Pop-ups bloqués! Veuillez les autoriser.', 'error');
            return;
        }

        console.log('✅ New window opened successfully');

        // Write loading content
        newWindow.document.write(`
            <html>
                <head>
                    <title>Préparation de l'Emploi du Temps</title>
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                </head>
                <body style="font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f0f2f5;">
                    <div style="text-align: center; padding: 20px; border-radius: 10px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-spinner fa-spin fa-3x" style="color: #7c3aed; margin-bottom: 20px;"></i>
                        <h2 style="color: #7c3aed;">Préparation de l'Emploi du Temps...</h2>
                        <p>Le téléchargement de votre emploi du temps va commencer sous peu.</p>
                        <div style="margin-top: 20px;">
                            <div style="width: 200px; height: 4px; background: #e0e0e0; border-radius: 2px; margin: 0 auto;">
                                <div style="width: 0%; height: 100%; background: #7c3aed; border-radius: 2px; animation: progress 3s ease-in-out;"></div>
                            </div>
                        </div>
                    </div>
                    <style>
                        @keyframes progress {
                            0% { width: 0%; }
                            100% { width: 100%; }
                        }
                    </style>
                </body>
            </html>
        `);
        newWindow.document.close();

        console.log('✅ Loading content written to new window');

        // Create a temporary form to submit to the export route
        console.log('🔥 Creating form...');
        const form = document.createElement('form');
        form.action = '{{ route('vacataire.emploi-du-temps.export') }}';
        form.method = 'POST';
        form.target = newWindow.name; // Target the newly opened window

        console.log('🔥 Form action:', form.action);
        console.log('🔥 Form target:', form.target);

        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        console.log('🔥 CSRF token added:', csrfInput.value.substring(0, 10) + '...');

        // Add form to document and submit
        document.body.appendChild(form);
        console.log('🔥 Form added to document, submitting...');

        form.submit();
        console.log('✅ Form submitted successfully');

        document.body.removeChild(form);
        console.log('✅ Form removed from document');

        // Show success notification
        showNotification('PDF en cours de génération...', 'success');

        // Set a timeout to close the window and show completion
        setTimeout(() => {
            if (newWindow && !newWindow.closed) {
                newWindow.close();
                console.log('✅ New window closed after timeout');
            }
            showNotification('PDF généré avec succès!', 'success');
        }, 5000); // Increased to 5 seconds for better user experience

    } catch (error) {
        console.error('❌ Export PDF Error:', error);
        showNotification('Erreur lors de l\'exportation: ' + error.message, 'error');
    }
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
