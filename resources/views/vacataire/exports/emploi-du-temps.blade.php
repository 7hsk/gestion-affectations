<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Emploi du Temps - {{ $vacataire->name }}</title>
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
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed; /* Ensures consistent column widths */
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }
        .schedule-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        .time-header {
            width: 100px;
            background-color: #e9ecef !important;
            font-weight: bold;
        }
        .schedule-item {
            border-radius: 4px;
            padding: 5px;
            margin-bottom: 3px;
            color: white;
            font-size: 0.75rem;
            line-height: 1.2;
            text-align: left;
        }
        .schedule-item.cm {
            background-color: #dc3545; /* Red */
        }
        .schedule-item.td {
            background-color: #28a745; /* Green */
        }
        .schedule-item.tp {
            background-color: #17a2b8; /* Blue */
        }
        .schedule-title {
            font-weight: bold;
        }
        .schedule-type {
            font-size: 0.65rem;
            opacity: 0.9;
        }
        .schedule-details {
            font-size: 0.65rem;
            opacity: 0.8;
        }
        .schedule-filiere {
            font-size: 0.6rem;
            margin-top: 3px;
            padding-top: 3px;
            border-top: 1px solid rgba(255,255,255,0.3);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .empty-slot {
            color: #bbb;
            font-style: italic;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Emploi du Temps</h1>
        <p>Vacataire: <strong>{{ $vacataire->name }}</strong></p>
        <p>Année universitaire: <strong>{{ $year }}</strong></p>
    </div>

    <table class="schedule-table">
        <thead>
            <tr>
                <th class="time-header">Horaires</th>
                @foreach($jours as $jour)
                    <th>{{ $jour }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($heures as $heure)
                <tr>
                    <td class="time-header">{{ $heure }}</td>
                    @foreach($jours as $jour)
                        <td>
                            @if(isset($emploiDuTemps[$jour][$heure]) && $emploiDuTemps[$jour][$heure]->count() > 0)
                                @foreach($emploiDuTemps[$jour][$heure] as $schedule)
                                    <div class="schedule-item {{ strtolower($schedule->type_seance) }}">
                                        <div class="schedule-title">{{ $schedule->uniteEnseignement->code }}</div>
                                        <div class="schedule-type">{{ $schedule->type_seance }}</div>
                                        <div class="schedule-details">
                                            @if($schedule->group_number)
                                                Gr. {{ $schedule->group_number }}
                                            @endif
                                            @if($schedule->salle)
                                                • {{ $schedule->salle }}
                                            @endif
                                        </div>
                                        <div class="schedule-filiere">
                                            {{ $schedule->uniteEnseignement->filiere->nom ?? 'N/A' }}
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-slot">Libre</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 