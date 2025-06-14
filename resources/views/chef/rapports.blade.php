@extends('layouts.chef')

@section('title', 'Rapports et Statistiques')

@push('styles')
<style>
.report-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    margin-bottom: 2rem;
}

.report-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.report-header {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem;
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.8rem;
}

.stat-icon.primary {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
}

.stat-icon.warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
}

.stat-icon.danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.chart-container {
    position: relative;
    height: 400px;
    margin: 1rem 0;
}

.chart-small {
    height: 300px;
}

.filters-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    color: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.table-modern {
    margin: 0;
}

.table-modern thead th {
    background: #2c3e50;
    color: white;
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
}

.charge-bar {
    height: 20px;
    border-radius: 10px;
    overflow: hidden;
    background: #e9ecef;
    position: relative;
}

.charge-fill {
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 10px;
}

.charge-insufficient {
    background: linear-gradient(90deg, #e74c3c, #c0392b);
}

.charge-normal {
    background: linear-gradient(90deg, #27ae60, #229954);
}

.charge-excessive {
    background: linear-gradient(90deg, #f39c12, #e67e22);
}

.charge-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.export-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.export-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.btn-export {
    padding: 1rem;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-export:hover {
    transform: translateY(-2px);
    border-color: #2c3e50;
}

.export-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.filiere-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 8px;
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border: 2px solid #2c3e50;
    box-shadow: 0 2px 15px rgba(44, 62, 80, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 110px;
}

.filiere-card::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    height: 6px;
    background: linear-gradient(90deg, #2c3e50 0%, #34495e 100%);
    border-radius: 12px 12px 0 0;
    z-index: 1;
}

.filiere-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.15);
    border-color: #34495e;
}

.filiere-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.3rem;
    border-bottom: 1px solid rgba(44, 62, 80, 0.08);
}

.filiere-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.filiere-badge {
    background: linear-gradient(135deg, #2c3e50, #34495e);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.65rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(44, 62, 80, 0.3);
}

.filiere-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.3rem;
}

.stat-item {
    text-align: center;
    padding: 0.3rem;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 4px;
    border: 1px solid rgba(44, 62, 80, 0.05);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-1px);
}

.stat-value {
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 0.1rem;
    line-height: 1;
}

.stat-value.success {
    color: #27ae60;
}

.stat-value.warning {
    color: #f39c12;
}

.stat-value.primary {
    color: #2c3e50;
}

.stat-label {
    font-size: 0.6rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.1px;
    font-weight: 600;
    margin: 0;
}

.progress-ring {
    width: 80px;
    height: 80px;
    margin: 0 auto;
}

.progress-ring-circle {
    fill: none;
    stroke: #e9ecef;
    stroke-width: 8;
}

.progress-ring-progress {
    fill: none;
    stroke: #2c3e50;
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dasharray 0.3s ease;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    text-align: center;
}

.kpi-value {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.kpi-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.kpi-trend {
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
}

.trend-up {
    background: rgba(39, 174, 96, 0.1);
    color: #27ae60;
}

.trend-down {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.trend-stable {
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Rapports et Statistiques</h2>
                    <p class="text-muted mb-0">Analyse détaillée des affectations et charges horaires</p>
                </div>
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <label for="exportYearSelect" class="form-label mb-0 me-2">Année</label>
                            <select class="form-select" id="exportYearSelect" style="max-width: 180px;">
                                @foreach($annees as $annee)
                                    <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button class="btn btn-primary" id="exportPdfBtn">
                                <i class="fas fa-file-pdf me-2"></i>Exporter PDF
                    </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Year Filter -->
    <div class="filters-section">
        <form method="GET" action="{{ route('chef.rapports') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Année Universitaire</label>
                    <select class="form-select" name="annee">
                        @foreach($annees as $annee)
                            <option value="{{ $annee }}" {{ $selectedYear == $annee ? 'selected' : '' }}>
                                {{ $annee }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-number">{{ $stats['total_ues'] }}</div>
            <div class="stat-label">Total UEs</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $stats['ues_affectees'] }}</div>
            <div class="stat-label">UEs Affectées</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-number">{{ $stats['ues_vacantes'] }}</div>
            <div class="stat-label">UEs Vacantes</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number">{{ $stats['total_enseignants'] }}</div>
            <div class="stat-label">Enseignants</div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-value">{{ round(($stats['ues_affectees'] / max($stats['total_ues'], 1)) * 100) }}%</div>
            <div class="kpi-label">Taux d'Affectation</div>
            <span class="kpi-trend trend-up">
                <i class="fas fa-arrow-up me-1"></i>+5% vs année précédente
            </span>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ count($chargesHoraires) > 0 ? round(collect($chargesHoraires)->avg('charge.total')) : 0 }}h</div>
            <div class="kpi-label">Charge Moyenne</div>
            <span class="kpi-trend trend-stable">
                <i class="fas fa-minus me-1"></i>Stable
            </span>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ collect($chargesHoraires)->where('status', 'insuffisant')->count() }}</div>
            <div class="kpi-label">Charges Insuffisantes</div>
            <span class="kpi-trend trend-down">
                <i class="fas fa-arrow-down me-1"></i>-2 vs mois dernier
            </span>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $repartitionFilieres->sum('total_ues') }}</div>
            <div class="kpi-label">UEs par Filière</div>
            <span class="kpi-trend trend-up">
                <i class="fas fa-arrow-up me-1"></i>Répartition équilibrée
            </span>
        </div>
    </div>

    <!-- Répartition par Filière - Full Width -->
    <div class="row">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition par Filière
                    </h5>
                </div>
                <div class="card-body">
                    @if($repartitionFilieres->isNotEmpty())
                        <div class="row">
                            @foreach($repartitionFilieres as $filiere)
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2">
                                    <div class="filiere-card">
                                        <div class="filiere-header">
                                            <h6 class="filiere-title">{{ $filiere->filiere }}</h6>
                                            <span class="filiere-badge">{{ $filiere->total_ues }} UEs</span>
                                        </div>

                                        <div class="filiere-stats">
                                            <div class="stat-item">
                                                <div class="stat-value success">{{ $filiere->ues_affectees }}</div>
                                                <div class="stat-label">Affectées</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value warning">{{ $filiere->ues_vacantes }}</div>
                                                <div class="stat-label">Vacantes</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-value primary">{{ round(($filiere->ues_affectees / max($filiere->total_ues, 1)) * 100) }}%</div>
                                                <div class="stat-label">Taux</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune donnée disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Évolution des Affectations - Separate Row -->
    <div class="row">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Évolution des Affectations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container chart-small">
                        <canvas id="evolutionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charges Horaires des Enseignants -->
    <div class="row">
        <div class="col-12">
            <div class="report-card">
                <div class="report-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        Charges Horaires des Enseignants
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($chargesHoraires) > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Enseignant</th>
                                        <th>Spécialité</th>
                                        <th>CM</th>
                                        <th>TD</th>
                                        <th>TP</th>
                                        <th>Total</th>
                                        <th>Charge Visuelle</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chargesHoraires as $item)
                                        @php
                                            $percentage = min(100, ($item['charge']['total'] / 192) * 100);
                                            $statusClass = $item['status'] == 'insuffisant' ? 'charge-insufficient' :
                                                          ($item['status'] == 'excessif' ? 'charge-excessive' : 'charge-normal');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $item['enseignant']->name }}</div>
                                                <small class="text-muted">{{ $item['enseignant']->email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $item['enseignant']->specialite ?? 'Non définie' }}
                                                </span>
                                            </td>
                                            <td>{{ $item['charge']['CM'] }}h</td>
                                            <td>{{ $item['charge']['TD'] }}h</td>
                                            <td>{{ $item['charge']['TP'] }}h</td>
                                            <td class="fw-bold">{{ $item['charge']['total'] }}h</td>
                                            <td>
                                                <div class="charge-bar">
                                                    <div class="charge-fill {{ $statusClass }}"
                                                         style="width: {{ $percentage }}%"></div>
                                                    <div class="charge-text">{{ $item['charge']['total'] }}/192h</div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($item['status'] == 'insuffisant')
                                                    <span class="badge bg-danger">Insuffisant</span>
                                                @elseif($item['status'] == 'excessif')
                                                    <span class="badge bg-warning">Excessif</span>
                                                @else
                                                    <span class="badge bg-success">Normal</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune donnée de charge horaire disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<form id="exportPdfForm" method="POST" action="{{ route('chef.rapports.export.pdf') }}" target="_blank" style="display:none;">
    @csrf
    <input type="hidden" name="year" id="exportYearInput">
    <input type="hidden" name="chart_image" id="exportChartImageInput">
</form>
@endsection

@push('scripts')
<script>
// Évolution Chart
@if(!empty($evolutionAffectations))
    const evolutionCtx = document.getElementById('evolutionChart');
    if (evolutionCtx) {
        new Chart(evolutionCtx, {
            type: 'line',
            data: {
                labels: [
                    @foreach($evolutionAffectations as $item)
                        'Mois {{ $item->mois }}',
                    @endforeach
                ],
                datasets: [{
                    label: 'Validées',
                    data: [
                        @foreach($evolutionAffectations as $item)
                            {{ $item->validees }},
                        @endforeach
                    ],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                }, {
                    label: 'En attente',
                    data: [
                        @foreach($evolutionAffectations as $item)
                            {{ $item->en_attente }},
                        @endforeach
                    ],
                    borderColor: '#f39c12',
                    backgroundColor: 'rgba(243, 156, 18, 0.1)',
                    tension: 0.4
                }, {
                    label: 'Rejetées',
                    data: [
                        @foreach($evolutionAffectations as $item)
                            {{ $item->rejetees }},
                        @endforeach
                    ],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
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

function exportPdf() {
    const year = document.querySelector('select[name="annee"]').value;

    // Open a new window for the download process
    const newWindow = window.open('about:blank', '_blank');
    if (!newWindow) {
        alert('Veuillez autoriser les pop-ups pour l\'exportation du PDF.');
        return;
    }
    newWindow.document.write('<html><head><title>Préparation du PDF</title></head><body style="font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f0f2f5;"><div style="text-align: center; padding: 20px; border-radius: 10px; background: white; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"><i class="fas fa-spinner fa-spin fa-3x" style="color: #007bff; margin-bottom: 20px;"></i><h2>Préparation du PDF...</h2><p>Le téléchargement de votre rapport va commencer sous peu.</p></div></body></html>');
    newWindow.document.close();

    // Create a temporary form to submit to the export route
    const form = document.createElement('form');
    form.action = `{{ route('chef.export.rapports') }}`;
    form.method = 'POST';
    form.target = newWindow.name; // Target the newly opened window

    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    // Add year input
    const yearInput = document.createElement('input');
    yearInput.type = 'hidden';
    yearInput.name = 'year';
    yearInput.value = year;
    form.appendChild(yearInput);

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

function printReport() {
    window.print();
}

// Auto-refresh every 10 minutes
setInterval(function() {
    // Refresh data
}, 600000);

document.getElementById('exportPdfBtn').addEventListener('click', function() {
    var year = document.getElementById('exportYearSelect').value;
    document.getElementById('exportYearInput').value = year;
    // Generate Chart.js image for evolution chart
    var chartCanvas = document.getElementById('evolutionChart');
    if (chartCanvas && window.Chart && Chart.getChart(chartCanvas)) {
        var chart = Chart.getChart(chartCanvas);
        var chartImage = chart.toBase64Image();
        let imgInput = document.getElementById('exportChartImageInput');
        if (!imgInput) {
            imgInput = document.createElement('input');
            imgInput.type = 'hidden';
            imgInput.name = 'chart_image';
            imgInput.id = 'exportChartImageInput';
            document.getElementById('exportPdfForm').appendChild(imgInput);
        }
        imgInput.value = chartImage;
    }
    document.getElementById('exportPdfForm').submit();
});
</script>
@endpush
