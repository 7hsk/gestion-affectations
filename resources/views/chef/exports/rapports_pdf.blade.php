<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Statistiques Département {{ $departement->nom }} - {{ $year }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 0; }
        .container { padding: 32px 24px; }
        .header { border-bottom: 2px solid #2c3e50; margin-bottom: 24px; }
        .header h2 { margin: 0 0 4px 0; color: #2c3e50; }
        .header .meta { color: #888; font-size: 0.95rem; margin-bottom: 8px; }
        .stats-overview { display: flex; gap: 18px; margin-bottom: 24px; }
        .stat-card { background: #f8f9fa; border-radius: 10px; padding: 18px 20px; flex: 1; text-align: center; }
        .stat-number { font-size: 2.1rem; font-weight: bold; color: #2c3e50; }
        .stat-label { font-size: 0.95rem; color: #555; margin-top: 4px; text-transform: uppercase; }
        .kpi-grid { display: flex; gap: 18px; margin-bottom: 24px; }
        .kpi-card { background: #fff; border-radius: 10px; padding: 18px 20px; flex: 1; text-align: center; border: 1px solid #e1e1e1; }
        .kpi-value { font-size: 1.5rem; font-weight: bold; color: #2c3e50; }
        .kpi-label { font-size: 0.95rem; color: #555; margin-bottom: 8px; }
        .section-title { color: #2c3e50; font-size: 1.15rem; margin: 32px 0 12px 0; border-left: 4px solid #2c3e50; padding-left: 10px; }
        .filiere-table, .charges-table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .filiere-table th, .filiere-table td, .charges-table th, .charges-table td { border: 1px solid #e1e1e1; padding: 8px 10px; font-size: 0.97rem; }
        .filiere-table th, .charges-table th { background: #2c3e50; color: #fff; }
        .filiere-table td { background: #f8f9fa; }
        .charges-table td { background: #fff; }
        .chart-img { display: block; margin: 0 auto 18px auto; max-width: 100%; max-height: 320px; border: 1px solid #e1e1e1; border-radius: 8px; }
        .placeholder { text-align: center; color: #aaa; font-style: italic; margin: 24px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Rapport Statistiques - {{ $departement->nom }}</h2>
        <div class="meta">Année universitaire : <strong>{{ $year }}</strong></div>
    </div>
    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_ues'] }}</div>
            <div class="stat-label">Total UEs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['ues_affectees'] }}</div>
            <div class="stat-label">UEs Affectées</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['ues_vacantes'] }}</div>
            <div class="stat-label">UEs Vacantes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_enseignants'] }}</div>
            <div class="stat-label">Enseignants</div>
        </div>
    </div>
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total_ues'] > 0 ? round(($stats['ues_affectees'] / max($stats['total_ues'], 1)) * 100) : 0 }}%</div>
            <div class="kpi-label">Taux d'Affectation</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ count($chargesHoraires) > 0 ? round(collect($chargesHoraires)->avg('charge.total')) : 0 }}h</div>
            <div class="kpi-label">Charge Moyenne</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ collect($chargesHoraires)->where('status', 'insuffisant')->count() }}</div>
            <div class="kpi-label">Charges Insuffisantes</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $repartitionFilieres->sum('total_ues') }}</div>
            <div class="kpi-label">UEs par Filière</div>
        </div>
    </div>
    <!-- Répartition par Filière -->
    <div class="section-title">Répartition par Filière</div>
    @if($repartitionFilieres->isNotEmpty())
        <table class="filiere-table">
            <thead>
                <tr>
                    <th>Filière</th>
                    <th>Total UEs</th>
                    <th>Affectées</th>
                    <th>Vacantes</th>
                    <th>Taux</th>
                </tr>
            </thead>
            <tbody>
            @foreach($repartitionFilieres as $filiere)
                <tr>
                    <td>{{ $filiere->filiere }}</td>
                    <td>{{ $filiere->total_ues }}</td>
                    <td>{{ $filiere->ues_affectees }}</td>
                    <td>{{ $filiere->ues_vacantes }}</td>
                    <td>{{ $filiere->total_ues > 0 ? round(($filiere->ues_affectees / max($filiere->total_ues, 1)) * 100) : 0 }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="placeholder">Aucune donnée disponible</div>
    @endif
    <!-- Evolution des Affectations -->
    <div class="section-title">Évolution des Affectations</div>
    @if(!empty($chartImage))
        <img src="{{ $chartImage }}" class="chart-img" alt="Évolution des Affectations">
    @else
        <div class="placeholder">Aucun graphique disponible</div>
    @endif
    <!-- Charges Horaires des Enseignants -->
    <div class="section-title">Charges Horaires des Enseignants</div>
    @if(count($chargesHoraires) > 0)
        <table class="charges-table">
            <thead>
                <tr>
                    <th>Enseignant</th>
                    <th>Spécialité</th>
                    <th>CM</th>
                    <th>TD</th>
                    <th>TP</th>
                    <th>Total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
            @foreach($chargesHoraires as $item)
                <tr>
                    <td>{{ $item['enseignant']->name }}</td>
                    <td>{{ $item['enseignant']->specialite ?? 'Non définie' }}</td>
                    <td>{{ $item['charge']['CM'] }}h</td>
                    <td>{{ $item['charge']['TD'] }}h</td>
                    <td>{{ $item['charge']['TP'] }}h</td>
                    <td>{{ $item['charge']['total'] }}h</td>
                    <td>
                        @if($item['status'] == 'insuffisant')
                            Insuffisant
                        @elseif($item['status'] == 'excessif')
                            Excessif
                        @else
                            Normal
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="placeholder">Aucune donnée de charge horaire disponible</div>
    @endif
</div>
</body>
</html> 