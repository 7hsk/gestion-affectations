@extends('layouts.chef')

@section('title', 'Charge Horaire - ' . $enseignant->name)

@push('styles')
<style>
.profile-header {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    transform: translate(50%, -50%);
}

.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.charge-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.charge-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.charge-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.charge-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.8rem;
}

.charge-icon.cm {
    background: linear-gradient(135deg, #3498db, #2980b9);
    color: white;
}

.charge-icon.td {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.charge-icon.tp {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: white;
}

.charge-icon.total {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
}

.charge-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.charge-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.progress-bar-custom {
    height: 30px;
    border-radius: 15px;
    overflow: hidden;
    background: #e9ecef;
    position: relative;
    margin-bottom: 1rem;
}

.progress-fill {
    height: 100%;
    transition: width 0.8s ease;
    border-radius: 15px;
    position: relative;
}

.progress-fill.insufficient {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.progress-fill.normal {
    background: linear-gradient(90deg, #27ae60, #229954);
}

.progress-fill.excessive {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-weight: 600;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.affectations-table {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
}

.table-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 1.5rem;
}

.table-modern {
    margin: 0;
}

.table-modern thead th {
    background: #f8f9fa;
    color: #2c3e50;
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem;
}

.table-modern tbody tr:hover {
    background-color: rgba(44, 62, 80, 0.05);
}

.table-modern tbody td {
    padding: 1rem;
    border-color: #eee;
    vertical-align: middle;
}

.ue-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-left: 4px solid #2c3e50;
}

.ue-code {
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.ue-name {
    color: #6c757d;
    font-size: 0.9rem;
}

.type-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    font-size: 0.85rem;
}

.type-cm {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border: 1px solid rgba(52, 152, 219, 0.3);
}

.type-td {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.3);
}

.type-tp {
    background: rgba(155, 89, 182, 0.1);
    color: #9b59b6;
    border: 1px solid rgba(155, 89, 182, 0.3);
}

.recommendations {
    background: linear-gradient(135deg, #e8f4fd, #d6eaf8);
    border-left: 4px solid #3498db;
    border-radius: 0 12px 12px 0;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.recommendation-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.recommendation-item:last-child {
    margin-bottom: 0;
}

.recommendation-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3498db;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.chart-section {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.chart-container {
    position: relative;
    height: 300px;
    margin-top: 1rem;
}

.actions-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
}

.btn-action {
    margin: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 500;
    margin-bottom: 1rem;
}

.status-insufficient {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border: 1px solid rgba(231, 76, 60, 0.3);
}

.status-normal {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
    border: 1px solid rgba(39, 174, 96, 0.3);
}

.status-excessive {
    background: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border: 1px solid rgba(243, 156, 18, 0.3);
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="profile-avatar">
                    {{ strtoupper(substr($enseignant->name, 0, 2)) }}
                </div>
                <h2 class="mb-2">{{ $enseignant->name }}</h2>
                <p class="mb-1 opacity-75">{{ $enseignant->email }}</p>
                @if($enseignant->specialite)
                    <div class="mt-2">
                        <span class="badge bg-light text-dark">{{ $enseignant->specialite }}</span>
                    </div>
                @endif
                
                <!-- Status Indicator -->
                @php
                    $status = 'normal';
                    if ($charge['total'] < 192) {
                        $status = 'insufficient';
                    } elseif ($charge['total'] > 240) {
                        $status = 'excessive';
                    }
                @endphp
                
                <div class="mt-3">
                    <div class="status-indicator status-{{ $status }}">
                        @if($status == 'insufficient')
                            <i class="fas fa-exclamation-triangle me-2"></i>Charge Insuffisante
                        @elseif($status == 'excessive')
                            <i class="fas fa-arrow-up me-2"></i>Charge Excessive
                        @else
                            <i class="fas fa-check-circle me-2"></i>Charge Normale
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="text-white-50">Charge Totale</div>
                <div style="font-size: 3rem; font-weight: 700;">{{ $charge['total'] }}h</div>
                <div class="text-white-50">sur 192h minimum</div>
            </div>
        </div>
    </div>

    <!-- Charge Overview -->
    <div class="charge-overview">
        <div class="charge-card">
            <div class="charge-icon cm">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="charge-number">{{ $charge['CM'] }}</div>
            <div class="charge-label">Heures CM</div>
        </div>
        
        <div class="charge-card">
            <div class="charge-icon td">
                <i class="fas fa-users"></i>
            </div>
            <div class="charge-number">{{ $charge['TD'] }}</div>
            <div class="charge-label">Heures TD</div>
        </div>
        
        <div class="charge-card">
            <div class="charge-icon tp">
                <i class="fas fa-laptop-code"></i>
            </div>
            <div class="charge-number">{{ $charge['TP'] }}</div>
            <div class="charge-label">Heures TP</div>
        </div>
        
        <div class="charge-card">
            <div class="charge-icon total">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="charge-number">{{ $charge['total'] }}</div>
            <div class="charge-label">Total</div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="progress-section">
        <h5 class="mb-3">
            <i class="fas fa-chart-line me-2"></i>
            Progression de la Charge Horaire
        </h5>
        
        @php
            $percentage = min(100, ($charge['total'] / 192) * 100);
            $progressClass = $status == 'insufficient' ? 'insufficient' : ($status == 'excessive' ? 'excessive' : 'normal');
        @endphp
        
        <div class="progress-bar-custom">
            <div class="progress-fill {{ $progressClass }}" style="width: {{ $percentage }}%">
                <div class="progress-text">{{ $charge['total'] }}h / 192h ({{ round($percentage) }}%)</div>
            </div>
        </div>
        
        <div class="row text-center mt-3">
            <div class="col-3">
                <div class="text-muted small">Minimum</div>
                <div class="fw-bold">192h</div>
            </div>
            <div class="col-3">
                <div class="text-muted small">Actuel</div>
                <div class="fw-bold text-{{ $status == 'insufficient' ? 'danger' : ($status == 'excessive' ? 'warning' : 'success') }}">
                    {{ $charge['total'] }}h
                </div>
            </div>
            <div class="col-3">
                <div class="text-muted small">Optimal</div>
                <div class="fw-bold">240h</div>
            </div>
            <div class="col-3">
                <div class="text-muted small">Restant</div>
                <div class="fw-bold">{{ max(0, 192 - $charge['total']) }}h</div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if($status == 'insufficient')
        <div class="recommendations">
            <h6 class="mb-3">
                <i class="fas fa-lightbulb me-2"></i>
                Recommandations pour Atteindre la Charge Minimale
            </h6>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ 192 - $charge['total'] }}h supplémentaires nécessaires</div>
                    <div class="text-muted">Pour atteindre la charge minimale de 192h</div>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-search"></i>
                </div>
                <div>
                    <div class="fw-bold">Consulter les UEs vacantes</div>
                    <div class="text-muted">Rechercher des UEs disponibles dans votre spécialité</div>
                </div>
            </div>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <div class="fw-bold">Contacter le chef de département</div>
                    <div class="text-muted">Discuter des possibilités d'affectations supplémentaires</div>
                </div>
            </div>
        </div>
    @elseif($status == 'excessive')
        <div class="recommendations">
            <h6 class="mb-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Attention : Charge Excessive Détectée
            </h6>
            
            <div class="recommendation-item">
                <div class="recommendation-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ $charge['total'] - 240 }}h au-dessus de l'optimal</div>
                    <div class="text-muted">Considérer une redistribution des charges</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Affectations Details -->
    <div class="affectations-table">
        <div class="table-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Détail des Affectations ({{ $affectations->count() }} UEs)
            </h5>
        </div>
        
        @if($affectations->count() > 0)
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>UE</th>
                            <th>Filière</th>
                            <th>Semestre</th>
                            <th>Type</th>
                            <th>Volume</th>
                            <th>Année</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($affectations as $affectation)
                            <tr>
                                <td>
                                    <div class="ue-card">
                                        <div class="ue-code">{{ $affectation->uniteEnseignement->code }}</div>
                                        <div class="ue-name">{{ $affectation->uniteEnseignement->nom }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $affectation->uniteEnseignement->filiere->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $affectation->uniteEnseignement->semestre }}</td>
                                <td>
                                    <span class="type-badge type-{{ strtolower($affectation->type_seance) }}">
                                        {{ $affectation->type_seance }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $volume = 0;
                                        switch($affectation->type_seance) {
                                            case 'CM': $volume = $affectation->uniteEnseignement->heures_cm; break;
                                            case 'TD': $volume = $affectation->uniteEnseignement->heures_td; break;
                                            case 'TP': $volume = $affectation->uniteEnseignement->heures_tp; break;
                                        }
                                    @endphp
                                    <span class="fw-bold">{{ $volume }}h</span>
                                </td>
                                <td>{{ $affectation->annee_universitaire }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" 
                                                onclick="showUeDetails({{ $affectation->uniteEnseignement->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary" 
                                                onclick="showSchedule({{ $affectation->id }})">
                                            <i class="fas fa-calendar"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h6>Aucune affectation</h6>
                <p class="text-muted">Cet enseignant n'a aucune affectation validée pour l'année en cours.</p>
            </div>
        @endif
    </div>

    <!-- Chart Section -->
    <div class="chart-section">
        <h5 class="mb-3">
            <i class="fas fa-chart-pie me-2"></i>
            Répartition des Types de Cours
        </h5>
        <div class="chart-container">
            <canvas id="chargeChart"></canvas>
        </div>
    </div>

    <!-- Actions Section -->
    <div class="actions-section">
        <h6 class="mb-3">Actions Disponibles</h6>
        
        <button class="btn btn-primary btn-action" onclick="generateReport()">
            <i class="fas fa-file-pdf me-2"></i>Générer Rapport PDF
        </button>
        
        <button class="btn btn-success btn-action" onclick="exportExcel()">
            <i class="fas fa-file-excel me-2"></i>Exporter Excel
        </button>
        
        @if($status == 'insufficient')
            <button class="btn btn-warning btn-action" onclick="suggestAffectations()">
                <i class="fas fa-plus me-2"></i>Suggérer Affectations
            </button>
        @endif
        
        <button class="btn btn-info btn-action" onclick="showHistory()">
            <i class="fas fa-history me-2"></i>Voir Historique
        </button>
        
        <a href="{{ route('chef.enseignants') }}" class="btn btn-secondary btn-action">
            <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Charge Distribution Chart
const ctx = document.getElementById('chargeChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['CM', 'TD', 'TP'],
            datasets: [{
                data: [{{ $charge['CM'] }}, {{ $charge['TD'] }}, {{ $charge['TP'] }}],
                backgroundColor: [
                    '#3498db',
                    '#f39c12',
                    '#9b59b6'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

function generateReport() {
    // Implementation for generating PDF report
    console.log('Generate PDF report for enseignant:', {{ $enseignant->id }});
    alert('Génération du rapport PDF en cours...');
}

function exportExcel() {
    // Implementation for Excel export
    console.log('Export Excel for enseignant:', {{ $enseignant->id }});
    alert('Export Excel en cours...');
}

function suggestAffectations() {
    // Implementation for suggesting affectations
    console.log('Suggest affectations for enseignant:', {{ $enseignant->id }});
    alert('Recherche d\'affectations suggérées...');
}

function showHistory() {
    // Redirect to history with enseignant filter
    window.location.href = '{{ route("chef.historique", ["enseignant_id" => $enseignant->id]) }}';
}

function showUeDetails(ueId) {
    // Implementation for showing UE details
    console.log('Show UE details:', ueId);
}

function showSchedule(affectationId) {
    // Implementation for showing schedule
    console.log('Show schedule for affectation:', affectationId);
}

// Auto-refresh charge data every 5 minutes
setInterval(function() {
    // Refresh charge data
}, 300000);
</script>
@endpush
