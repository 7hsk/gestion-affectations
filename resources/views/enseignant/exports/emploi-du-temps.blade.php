<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du Temps - {{ $teacher->name }}</title>
    <style>
        @page {
            margin: 20mm;
            size: A4 landscape;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1e40af;
        }
        
        .header-left {
            flex: 1;
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .logo-left {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .school-info {
            flex: 1;
        }

        .school-name {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .school-subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 10px;
        }

        .current-date {
            font-size: 10px;
            color: #888;
        }
        
        .header-center {
            flex: 2;
            text-align: center;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            display: block;
        }
        
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .teacher-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .academic-year {
            font-size: 12px;
            color: #666;
        }
        
        .header-right {
            flex: 1;
            text-align: right;
        }
        
        .export-info {
            font-size: 10px;
            color: #888;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        
        .schedule-table th,
        .schedule-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        
        .schedule-table th {
            background-color: #1e40af;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        
        .time-header {
            background-color: #059669 !important;
            width: 120px;
        }
        
        .day-header {
            width: 140px;
        }
        
        .time-slot {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #1e40af;
        }
        
        .schedule-cell {
            height: 60px;
            vertical-align: top;
            padding: 5px;
        }
        
        .schedule-item {
            background-color: #e3f2fd;
            border: 1px solid #1976d2;
            border-radius: 4px;
            padding: 4px;
            margin: 2px 0;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .schedule-item.cm {
            background-color: #ffebee;
            border-color: #d32f2f;
            color: #d32f2f;
        }
        
        .schedule-item.td {
            background-color: #e8f5e8;
            border-color: #388e3c;
            color: #388e3c;
        }
        
        .schedule-item.tp {
            background-color: #e3f2fd;
            border-color: #1976d2;
            color: #1976d2;
        }
        
        .ue-code {
            font-weight: bold;
            display: block;
        }
        
        .ue-name {
            font-size: 9px;
            color: #666;
            display: block;
            margin-top: 2px;
        }
        
        .session-type {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-top: 2px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #888;
        }
        
        .legend {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
        }
        
        .legend-color {
            width: 15px;
            height: 15px;
            border-radius: 3px;
            border: 1px solid #ccc;
        }
        
        .legend-color.cm {
            background-color: #ffebee;
            border-color: #d32f2f;
        }
        
        .legend-color.td {
            background-color: #e8f5e8;
            border-color: #388e3c;
        }
        
        .legend-color.tp {
            background-color: #e3f2fd;
            border-color: #1976d2;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo ENSA" class="logo-left">
            <div class="school-info">
                <div class="school-name">École Nationale des Sciences Appliquées</div>
                <div class="school-subtitle">Al Hoceima</div>
                <div class="current-date">Généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</div>
            </div>
        </div>
        
        <div class="header-center">
            <div class="document-title">EMPLOI DU TEMPS</div>
            <div class="teacher-name">{{ $teacher->name }}</div>
            <div class="academic-year">Année Universitaire {{ date('Y') }}-{{ date('Y') + 1 }}</div>
        </div>
        
        <div class="header-right">
            <div class="export-info">
                Document généré automatiquement<br>
                Système de Gestion des Affectations
            </div>
        </div>
    </div>

    <!-- Schedule Table -->
    <table class="schedule-table">
        <thead>
            <tr>
                <th class="time-header">Horaires</th>
                <th class="day-header">Lundi</th>
                <th class="day-header">Mardi</th>
                <th class="day-header">Mercredi</th>
                <th class="day-header">Jeudi</th>
                <th class="day-header">Vendredi</th>
                <th class="day-header">Samedi</th>
            </tr>
        </thead>
        <tbody>
            @php
                $timeSlots = ['08:30-10:30', '10:30-12:30', '14:30-16:30', '16:30-18:30'];
                $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

                // Group schedules by day and time
                $groupedSchedules = collect();
                if($schedules && $schedules->count() > 0) {
                    $groupedSchedules = $schedules->groupBy(['jour_semaine', function ($item) {
                        $debut = \Carbon\Carbon::parse($item->heure_debut)->format('H:i');
                        $fin = \Carbon\Carbon::parse($item->heure_fin)->format('H:i');
                        return $debut.'-'.$fin;
                    }]);
                }
            @endphp
            
            @foreach($timeSlots as $timeSlot)
                <tr>
                    <td class="time-slot">{{ $timeSlot }}</td>
                    @foreach($days as $day)
                        <td class="schedule-cell">
                            @if($groupedSchedules->isNotEmpty() && isset($groupedSchedules[$day][$timeSlot]))
                                @foreach($groupedSchedules[$day][$timeSlot] as $schedule)
                                    <div class="schedule-item {{ strtolower($schedule->type_seance) }}">
                                        <span class="ue-code">{{ $schedule->uniteEnseignement->code ?? 'N/A' }}</span>
                                        <span class="ue-name">{{ Str::limit($schedule->uniteEnseignement->nom ?? 'Non défini', 25) }}</span>
                                        <span class="session-type">{{ $schedule->type_seance ?? 'N/A' }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($schedules->isEmpty())
        <div style="text-align: center; margin: 30px 0; padding: 20px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
            <p style="color: #6c757d; font-style: italic; margin: 0;">
                Aucun cours programmé pour le moment.
            </p>
        </div>
    @endif

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color cm"></div>
            <span>CM - Cours Magistral</span>
        </div>
        <div class="legend-item">
            <div class="legend-color td"></div>
            <span>TD - Travaux Dirigés</span>
        </div>
        <div class="legend-item">
            <div class="legend-color tp"></div>
            <span>TP - Travaux Pratiques</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Ce document a été généré automatiquement par le Système de Gestion des Affectations d'Enseignement</p>
        <p>ENSA Al Hoceima - École Nationale des Sciences Appliquées</p>
    </div>
</body>
</html>
