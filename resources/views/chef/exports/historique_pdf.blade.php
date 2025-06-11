<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historique des Affectations</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #7f8c8d;
            margin: 5px 0 0;
        }
        .section-title {
            font-size: 18px;
            color: #2c3e50;
            margin: 30px 0 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.valide { background-color: #28a745; color: white; }
        .badge.en_attente { background-color: #ffc107; color: black; }
        .badge.rejete { background-color: #dc3545; color: white; }
        .badge.annule { background-color: #6c757d; color: white; }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historique des Affectations et Demandes</h1>
        <p>Année universitaire : <strong>{{ $year }}</strong></p>
    </div>

    <div class="section-title">Toutes les Activités</div>
    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>UE</th>
                <th>Enseignant</th>
                <th>Type Séance</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
        @forelse($activities as $activity)
            <tr>
                <td>{{ $activity['type'] }}</td>
                <td>{{ $activity['ue']->code ?? '' }} - {{ $activity['ue']->nom ?? '' }}</td>
                <td>{{ $activity['enseignant']->name ?? '' }}</td>
                <td>{{ $activity['type_seance'] }}</td>
                <td><span class="badge {{ strtolower(str_replace(' ', '_', $activity['statut'])) }}">{{ $activity['statut'] }}</span></td>
                <td>{{ $activity['date'] ? $activity['date']->format('d/m/Y H:i') : '' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center; color:#888;">Aucune activité trouvée pour cette année.</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html> 