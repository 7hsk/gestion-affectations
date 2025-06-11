<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport Analytique des Affectations - {{ $year }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #fff;
            color: #222;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #7c3aed;
            color: #fff;
            padding: 24px 0 16px 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.2em;
            letter-spacing: 1px;
        }
        .header p {
            margin: 8px 0 0 0;
            font-size: 1.1em;
        }
        .section-title {
            color: #7c3aed;
            font-size: 1.3em;
            margin-top: 32px;
            margin-bottom: 12px;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            font-size: 0.98em;
        }
        th {
            background: #ede9fe;
            color: #4b2997;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 0.95em;
            color: #fff;
        }
        .badge.valide { background: #059669; }
        .badge.rejete { background: #ef4444; }
        .badge.annule { background: #6b7280; }
        .badge.en_attente { background: #f59e0b; }
        .chart-section {
            text-align: center;
            margin-top: 32px;
        }
        .chart-section img {
            max-width: 90%;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 2px 8px #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Rapport Analytique des Affectations</h1>
        <p>Année universitaire : <strong>{{ $year }}</strong></p>
    </div>

    <div class="section-title">Tableau des Affectations</div>
    <table>
        <thead>
            <tr>
                <th>UE</th>
                <th>Enseignant</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @forelse($affectations as $aff)
            <tr>
                <td>{{ $aff['ue_code'] }} - {{ $aff['ue_nom'] }}</td>
                <td>{{ $aff['enseignant'] }}</td>
                <td>{{ $aff['type_seance'] }}</td>
                <td><span class="badge {{ $aff['statut'] }}">{{ $aff['statut'] }}</span></td>
                <td>{{ $aff['date'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; color:#888;">Aucune affectation pour cette année.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="section-title">Répartition des Affectations par Statut</div>
    <table style="margin: 0 auto; min-width: 300px;">
        <tr>
            <th style="background:#059669; color:#fff;">Validées</th>
            <th style="background:#ef4444; color:#fff;">Rejetées</th>
            <th style="background:#f59e0b; color:#fff;">En Attente</th>
            <th style="background:#6b7280; color:#fff;">Annulées</th>
        </tr>
        <tr>
            <td style="text-align:center; font-weight:bold;">{{ $stats['valide'] }}</td>
            <td style="text-align:center; font-weight:bold;">{{ $stats['rejete'] }}</td>
            <td style="text-align:center; font-weight:bold;">{{ $stats['en_attente'] }}</td>
            <td style="text-align:center; font-weight:bold;">{{ $stats['annule'] }}</td>
        </tr>
    </table>

    <!-- Page break for chart -->
    <div style="page-break-before: always;"></div>
    <div class="section-title" style="margin-top: 40px;">Diagramme des Affectations par Statut</div>
    <div style="width: 80%; margin: 60px auto 0 auto;">
        @php
            $max = max(1, $stats['valide'], $stats['rejete'], $stats['en_attente'], $stats['annule']);
            $barMaxWidth = 600; // px
        @endphp
        <div style="margin-bottom: 32px;">
            <div style="font-weight: bold; color: #059669; margin-bottom: 6px;">Validées</div>
            <div style="background: #e0f7ef; border-radius: 8px; height: 38px; width: {{$barMaxWidth}}px;">
                <div style="background: #059669; height: 38px; border-radius: 8px; width: {{ round($stats['valide']/$max*$barMaxWidth) }}px; display: flex; align-items: center; font-size: 1.3em; color: #fff; font-weight: bold; padding-left: 18px;">
                    {{ $stats['valide'] }}
                </div>
            </div>
        </div>
        <div style="margin-bottom: 32px;">
            <div style="font-weight: bold; color: #ef4444; margin-bottom: 6px;">Rejetées</div>
            <div style="background: #fde8e8; border-radius: 8px; height: 38px; width: {{$barMaxWidth}}px;">
                <div style="background: #ef4444; height: 38px; border-radius: 8px; width: {{ round($stats['rejete']/$max*$barMaxWidth) }}px; display: flex; align-items: center; font-size: 1.3em; color: #fff; font-weight: bold; padding-left: 18px;">
                    {{ $stats['rejete'] }}
                </div>
            </div>
        </div>
        <div style="margin-bottom: 32px;">
            <div style="font-weight: bold; color: #f59e0b; margin-bottom: 6px;">En Attente</div>
            <div style="background: #fff7e6; border-radius: 8px; height: 38px; width: {{$barMaxWidth}}px;">
                <div style="background: #f59e0b; height: 38px; border-radius: 8px; width: {{ round($stats['en_attente']/$max*$barMaxWidth) }}px; display: flex; align-items: center; font-size: 1.3em; color: #fff; font-weight: bold; padding-left: 18px;">
                    {{ $stats['en_attente'] }}
                </div>
            </div>
        </div>
        <div style="margin-bottom: 32px;">
            <div style="font-weight: bold; color: #6b7280; margin-bottom: 6px;">Annulées</div>
            <div style="background: #e5e7eb; border-radius: 8px; height: 38px; width: {{$barMaxWidth}}px;">
                <div style="background: #6b7280; height: 38px; border-radius: 8px; width: {{ round($stats['annule']/$max*$barMaxWidth) }}px; display: flex; align-items: center; font-size: 1.3em; color: #fff; font-weight: bold; padding-left: 18px;">
                    {{ $stats['annule'] }}
                </div>
            </div>
        </div>
    </div>
</body>
</html> 