<?php

namespace App\Http\Controllers\Admin\vacataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{User, UniteEnseignement, Affectation, Schedule, Note, Filiere};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class VacataireController extends Controller
{
    // No middleware in constructor - handled by routes

    // Dashboard principal du vacataire
    public function dashboard()
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Statistiques principales
        $stats = [
            'ues_assignees' => $this->getAssignedUEsCount($vacataire->id),
            'heures_totales' => $this->getTotalHours($vacataire->id),
            'notes_saisies' => $this->getNotesCount($vacataire->id),
            'emploi_du_temps' => $this->getScheduleCount($vacataire->id)
        ];

        // UEs assignées récentes
        $uesAssignees = Affectation::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Emploi du temps de la semaine - FILTERED BY AFFECTATIONS
        // Get vacataire's valid affectations to filter schedules
        $affectations = Affectation::where('user_id', $vacataire->id)
            ->where('validee', 'valide')
            ->get();

        // Create a map of UE+type combinations that vacataire is actually assigned to
        $assignedCombinations = $affectations->map(function($affectation) {
            return $affectation->ue_id . '_' . $affectation->type_seance;
        })->toArray();

        // Get all schedules first
        $allSchedules = Schedule::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->with(['uniteEnseignement'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        // Filter schedules to only include those matching vacataire's affectations
        $emploiDuTemps = $allSchedules->filter(function($schedule) use ($assignedCombinations) {
            $combination = $schedule->ue_id . '_' . $schedule->type_seance;
            return in_array($combination, $assignedCombinations);
        });

        // Notes récentes
        $notesRecentes = Note::whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id);
        })
        ->with(['uniteEnseignement', 'etudiant'])
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();

        return view('vacataire.dashboard', compact(
            'stats', 
            'uesAssignees', 
            'emploiDuTemps', 
            'notesRecentes'
        ));
    }

    // Liste des UEs assignées au vacataire - GROUPED BY UE
    public function unitesEnseignement(Request $request)
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Get all affectations for the vacataire
        $affectationsQuery = Affectation::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere']);

        // Apply filters to affectations
        if ($request->filled('filiere')) {
            $affectationsQuery->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        if ($request->filled('semestre')) {
            $affectationsQuery->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('semestre', $request->semestre);
            });
        }

        $allAffectations = $affectationsQuery->get();

        // Group affectations by UE and collect all session types
        $groupedUEs = $allAffectations->groupBy('ue_id')->map(function ($affectations) {
            $firstAffectation = $affectations->first();
            $ue = $firstAffectation->uniteEnseignement;

            // Collect all session types for this UE
            $sessionTypes = $affectations->pluck('type_seance')->unique()->sort()->values();

            return (object) [
                'id' => $ue->id,
                'ue' => $ue,
                'session_types' => $sessionTypes,
                'affectations' => $affectations,
                'total_affectations' => $affectations->count()
            ];
        });

        // Convert to paginated collection for consistency
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 12;
        $currentItems = $groupedUEs->slice(($currentPage - 1) * $perPage, $perPage);

        $uesGrouped = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $groupedUEs->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Filières disponibles pour le filtre
        $filieres = Filiere::whereHas('unitesEnseignement.affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id);
        })->get();

        return view('vacataire.unites-enseignement', compact('uesGrouped', 'filieres'));
    }

    // Détails d'une UE - SHOW ALL AFFECTATIONS FOR THIS UE
    public function ueDetails($id)
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);
        
        // Get ALL affectations for this UE and vacataire
        $affectations = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $id)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere.departement'])
            ->get();

        if ($affectations->isEmpty()) {
            abort(404, 'UE non trouvée ou non assignée');
        }

        $ue = $affectations->first()->uniteEnseignement;
        $sessionTypes = $affectations->pluck('type_seance')->unique()->sort()->values();

        // Get vacataire's valid affectations to filter schedules
        $assignedCombinations = $affectations->map(function($affectation) {
            return $affectation->ue_id . '_' . $affectation->type_seance;
        })->toArray();

        // Emploi du temps pour cette UE - FILTERED BY AFFECTATIONS
        $allSchedules = Schedule::where('user_id', $vacataire->id)
            ->where('ue_id', $id)
            ->where('annee_universitaire', $currentYear)
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        // Filter schedules to only show assigned session types
        $schedules = $allSchedules->filter(function($schedule) use ($assignedCombinations) {
            $combination = $schedule->ue_id . '_' . $schedule->type_seance;
            return in_array($combination, $assignedCombinations);
        });

        // Notes pour cette UE
        $notes = Note::where('ue_id', $id)
            ->with(['etudiant'])
            ->orderBy('etudiant_id')
            ->get();

        return view('vacataire.ue-details', compact('ue', 'affectations', 'sessionTypes', 'schedules', 'notes'));
    }

    // Gestion des notes with grading logic
    public function notes(Request $request)
    {
        $vacataire = Auth::user();
        
        // Get all notes for this vacataire's UEs
        $baseQuery = Note::whereHas('uniteEnseignement.affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id)->where('validee', 'valide');
        })->with(['uniteEnseignement.filiere', 'etudiant']);

        // Apply filters
        if ($request->filled('ue_id')) {
            $baseQuery->where('ue_id', $request->ue_id);
        }

        if ($request->filled('session')) {
            $baseQuery->where('session_type', $request->session);
        }

        $allNotes = $baseQuery->get();

        // Group notes by student and UE to calculate final grades
        $groupedNotes = $allNotes->groupBy(function($note) {
            return $note->ue_id . '_' . $note->etudiant_id;
        });

        $processedNotes = [];

        foreach ($groupedNotes as $key => $studentNotes) {
            $normale = $studentNotes->where('session_type', 'normale')->first();
            $rattrapage = $studentNotes->where('session_type', 'rattrapage')->first();

            // Get student and UE info
            $firstNote = $studentNotes->first();
            $etudiant = $firstNote->etudiant;
            $ue = $firstNote->uniteEnseignement;

            // Calculate final grade and status
            $finalGrade = null;
            $status = 'Non Validé';
            $session = 'Normale';

            if ($normale && !$normale->is_absent && $normale->note !== null) {
                if ($normale->note >= 10) {
                    // Validated in normal session
                    $finalGrade = $normale->note;
                    $status = 'Validé';
                    $session = 'Normale';
                } else {
                    // Needs rattrapage
                    if ($rattrapage && !$rattrapage->is_absent && $rattrapage->note !== null) {
                        // Calculate final grade: 60% rattrapage + 40% normale
                        $calculatedGrade = ($rattrapage->note * 0.6) + ($normale->note * 0.4);

                        if ($calculatedGrade >= 10) {
                            $finalGrade = 10; // Max 10 for rattrapage validation
                            $status = 'Validé';
                        } else {
                            $finalGrade = round($calculatedGrade, 2);
                            $status = 'Non Validé';
                        }
                        $session = 'Rattrapage';
                    } else {
                        $finalGrade = $normale->note;
                        $status = 'Non Validé';
                        $session = 'Normale';
                    }
                }
            } elseif ($rattrapage && !$rattrapage->is_absent && $rattrapage->note !== null) {
                // Only rattrapage note exists
                if ($rattrapage->note >= 10) {
                    $finalGrade = 10; // Max 10 for rattrapage
                    $status = 'Validé';
                } else {
                    $finalGrade = $rattrapage->note;
                    $status = 'Non Validé';
                }
                $session = 'Rattrapage';
            }

            $processedNotes[] = (object) [
                'id' => $firstNote->id,
                'ue' => $ue,
                'etudiant' => $etudiant,
                'note_normale' => $normale ? ($normale->is_absent ? 'Absent' : $normale->note) : '-',
                'note_rattrapage' => $rattrapage ? ($rattrapage->is_absent ? 'Absent' : $rattrapage->note) : '-',
                'note_finale' => $finalGrade ? number_format($finalGrade, 2) . '/20' : '0.00/20',
                'session' => $session,
                'status' => $status,
                'normale_note_obj' => $normale,
                'rattrapage_note_obj' => $rattrapage
            ];
        }

        // Convert to collection and paginate
        $processedCollection = collect($processedNotes);
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $currentItems = $processedCollection->slice(($currentPage - 1) * $perPage, $perPage);

        $notes = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $processedCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // UEs assignées pour le filtre
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id)->where('validee', 'valide');
        })->get();

        return view('vacataire.notes', compact('notes', 'uesAssignees'));
    }

    // Emploi du temps du vacataire - AUTO-FILLED FROM AFFECTATIONS
    public function emploiDuTemps()
    {
        $vacataire = Auth::user();
        $daysOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        // Get vacataire's valid affectations to know which UE+type combinations he's assigned to
        $affectations = Affectation::where('user_id', $vacataire->id)
            ->where('validee', 'valide')
            ->get();

        // Create a map of UE+type combinations that vacataire is actually assigned to
        $assignedCombinations = $affectations->map(function($affectation) {
            return $affectation->ue_id . '_' . $affectation->type_seance;
        })->toArray();

        \Log::info('🔥 Vacataire assigned combinations:', [
            'vacataire' => $vacataire->name,
            'assigned_combinations' => $assignedCombinations,
            'affectations' => $affectations->map(function($a) {
                return [
                    'ue_id' => $a->ue_id,
                    'ue_code' => $a->uniteEnseignement->code ?? 'N/A',
                    'type_seance' => $a->type_seance
                ];
            })
        ]);

        // Get schedules but FILTER to only show UE+type combinations that vacataire is assigned to
        $allSchedules = Schedule::where('user_id', $vacataire->id)
            ->with(['uniteEnseignement', 'filiere'])
            ->orderByRaw("FIELD(jour_semaine, '".implode("','", $daysOrder)."')")
            ->orderBy('heure_debut')
            ->get();

        // Filter schedules to only include those matching vacataire's affectations
        $filteredSchedules = $allSchedules->filter(function($schedule) use ($assignedCombinations) {
            $combination = $schedule->ue_id . '_' . $schedule->type_seance;
            return in_array($combination, $assignedCombinations);
        });

        // Group filtered schedules by day and time
        $schedules = $filteredSchedules->groupBy(['jour_semaine', function ($item) {
            // Format time to match the view expectation (HH:MM-HH:MM)
            $debut = \Carbon\Carbon::parse($item->heure_debut)->format('H:i');
            $fin = \Carbon\Carbon::parse($item->heure_fin)->format('H:i');
            return $debut.'-'.$fin;
        }]);

        // Always use standard time slots for consistent display
        $timeSlots = [
            ['start' => '08:30', 'end' => '10:30'],
            ['start' => '10:30', 'end' => '12:30'],
            ['start' => '14:30', 'end' => '16:30'],
            ['start' => '16:30', 'end' => '18:30']
        ];

        // Get UEs for filter
        $unites = $affectations->pluck('uniteEnseignement')
            ->unique('id')
            ->sortBy('code');

        // Get session types that vacataire actually has assigned (from affectations only)
        $availableSessionTypes = $affectations->pluck('type_seance')
            ->unique()
            ->toArray();

        \Log::info('🔥 Final filtered data:', [
            'total_schedules' => $allSchedules->count(),
            'filtered_schedules' => $filteredSchedules->count(),
            'available_session_types' => $availableSessionTypes,
            'filtered_schedules_details' => $filteredSchedules->map(function($s) {
                return [
                    'ue' => $s->uniteEnseignement->code ?? 'N/A',
                    'type' => $s->type_seance,
                    'day' => $s->jour_semaine,
                    'time' => $s->heure_debut . '-' . $s->heure_fin
                ];
            })
        ]);

        return view('vacataire.emploi-du-temps', compact(
            'schedules',
            'timeSlots',
            'unites',
            'daysOrder',
            'availableSessionTypes'
        ));
    }

    // Auto-create schedule entry for an affectation
    private function autoCreateScheduleForAffectation($affectation, $currentYear)
    {
        try {
            $ue = $affectation->uniteEnseignement;

            // Find an available time slot (simple algorithm)
            $availableSlot = $this->findAvailableTimeSlot($affectation->user_id, $currentYear);

            if ($availableSlot) {
                $schedule = Schedule::create([
                    'ue_id' => $ue->id,
                    'user_id' => $affectation->user_id,
                    'jour_semaine' => $availableSlot['day'],
                    'heure_debut' => $availableSlot['start_time'],
                    'heure_fin' => $availableSlot['end_time'],
                    'type_seance' => $affectation->type_seance,
                    'groupe' => null, // Can be set later
                    'semestre' => $ue->semestre,
                    'annee_universitaire' => $currentYear,
                    'filiere_id' => $ue->filiere_id,
                    'created_by' => $affectation->user_id,
                    'auto_created' => true // Mark as auto-created
                ]);

                \Log::info('🔥 Auto-created schedule for vacataire:', [
                    'ue' => $ue->code,
                    'type' => $affectation->type_seance,
                    'day' => $availableSlot['day'],
                    'time' => $availableSlot['start_time'] . '-' . $availableSlot['end_time']
                ]);

                return $schedule;
            }
        } catch (\Exception $e) {
            \Log::error('Error auto-creating schedule: ' . $e->getMessage());
        }

        return null;
    }

    // Find an available time slot for the vacataire
    private function findAvailableTimeSlot($userId, $currentYear)
    {
        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $timeSlots = [
            ['start_time' => '08:30:00', 'end_time' => '10:30:00'],
            ['start_time' => '10:30:00', 'end_time' => '12:30:00'],
            ['start_time' => '14:30:00', 'end_time' => '16:30:00'],
            ['start_time' => '16:30:00', 'end_time' => '18:30:00']
        ];

        // Get existing schedules for this user
        $existingSchedules = Schedule::where('user_id', $userId)
            ->where('annee_universitaire', $currentYear)
            ->get();

        // Find first available slot
        foreach ($days as $day) {
            foreach ($timeSlots as $slot) {
                $isOccupied = $existingSchedules->where('jour_semaine', $day)
                    ->where('heure_debut', $slot['start_time'])
                    ->where('heure_fin', $slot['end_time'])
                    ->isNotEmpty();

                if (!$isOccupied) {
                    return [
                        'day' => $day,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time']
                    ];
                }
            }
        }

        // If no slot available, return first slot (will create conflict but at least shows the UE)
        return [
            'day' => 'Lundi',
            'start_time' => '08:30:00',
            'end_time' => '10:30:00'
        ];
    }

    // Export Emploi du Temps to PDF - SAME STRUCTURE AS ENSEIGNANT
    public function exportEmploiDuTemps(Request $request)
    {
        try {
            $vacataire = Auth::user();
            $daysOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

            // Get vacataire's valid affectations to filter schedules (SAME AS VIEW)
            $affectations = Affectation::where('user_id', $vacataire->id)
                ->where('validee', 'valide')
                ->get();

            // Create a map of UE+type combinations that vacataire is actually assigned to
            $assignedCombinations = $affectations->map(function($affectation) {
                return $affectation->ue_id . '_' . $affectation->type_seance;
            })->toArray();

            // Get all schedules first
            $allSchedules = Schedule::where('user_id', $vacataire->id)
                ->with(['uniteEnseignement', 'filiere'])
                ->orderByRaw("FIELD(jour_semaine, '".implode("','", $daysOrder)."')")
                ->orderBy('heure_debut')
                ->get();

            // Filter schedules to only include those matching vacataire's affectations
            $schedules = $allSchedules->filter(function($schedule) use ($assignedCombinations) {
                $combination = $schedule->ue_id . '_' . $schedule->type_seance;
                return in_array($combination, $assignedCombinations);
            });

            \Log::info('📄 Vacataire PDF Export (Filtered):', [
                'vacataire' => $vacataire->name,
                'total_schedules' => $allSchedules->count(),
                'filtered_schedules' => $schedules->count(),
                'assigned_combinations' => $assignedCombinations,
                'schedules_data' => $schedules->map(function($s) {
                    return [
                        'ue' => $s->uniteEnseignement->code ?? 'N/A',
                        'day' => $s->jour_semaine,
                        'time' => $s->heure_debut . '-' . $s->heure_fin,
                        'type' => $s->type_seance,
                        'group' => $s->group_number
                    ];
                })
            ]);

            $data = [
                'vacataire' => $vacataire,
                'schedules' => $schedules,
                'title' => 'Emploi du Temps - ' . $vacataire->name,
                'currentDate' => \Carbon\Carbon::now()->format('d/m/Y'),
                'academicYear' => date('Y') . '-' . (date('Y') + 1)
            ];

            // Configure PDF options for landscape orientation
            $pdf = Pdf::loadView('vacataire.exports.emploi-du-temps', $data)
                      ->setPaper('a4', 'landscape')
                      ->setOptions([
                          'dpi' => 150,
                          'defaultFont' => 'Arial',
                          'isRemoteEnabled' => true,
                          'isHtml5ParserEnabled' => true,
                          'chroot' => public_path(),
                          'enable_php' => true
                      ]);

            $filename = 'emploi-du-temps-vacataire-' . \Illuminate\Support\Str::slug($vacataire->name) . '-' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Vacataire PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    // Méthodes utilitaires privées
    private function getAssignedUEsCount($userId)
    {
        return Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->count();
    }

    private function getTotalHours($userId)
    {
        return Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->join('unites_enseignement', 'affectations.ue_id', '=', 'unites_enseignement.id')
            ->sum(DB::raw('unites_enseignement.heures_cm + unites_enseignement.heures_td + unites_enseignement.heures_tp'));
    }

    private function getNotesCount($userId)
    {
        return Note::whereHas('uniteEnseignement.affectations', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
    }

    private function getScheduleCount($userId)
    {
        // Get vacataire's valid affectations to filter schedules (SAME AS DASHBOARD)
        $affectations = Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->get();

        // Create a map of UE+type combinations that vacataire is actually assigned to
        $assignedCombinations = $affectations->map(function($affectation) {
            return $affectation->ue_id . '_' . $affectation->type_seance;
        })->toArray();

        // Get all schedules first
        $allSchedules = Schedule::where('user_id', $userId)
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->get();

        // Filter schedules to only include those matching vacataire's affectations
        $filteredSchedules = $allSchedules->filter(function($schedule) use ($assignedCombinations) {
            $combination = $schedule->ue_id . '_' . $schedule->type_seance;
            return in_array($combination, $assignedCombinations);
        });

        return $filteredSchedules->count();
    }

    // Import notes from Excel/CSV - COMPLETE IMPLEMENTATION
    public function importNotes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'ue_id' => 'required|exists:unites_enseignement,id',
            'session_type' => 'required|in:normale,rattrapage'
        ]);

        $vacataire = Auth::user();

        // Verify vacataire is assigned to this UE
        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer les notes de cette UE.');
        }

        try {
            $file = $request->file('file');
            $ue = \App\Models\UniteEnseignement::find($request->ue_id);

            if (!$ue) {
                return back()->with('error', 'UE sélectionnée introuvable.');
            }

            // Create import instance
            $import = new \App\Imports\NotesImport($request->ue_id, $vacataire->id, $request->session_type);

            // Validate Excel file structure and UE match
            if (!$import->validateExcelFile($file->getPathname())) {
                $errors = $import->getErrors();
                return back()->with('error', 'Validation échouée: ' . implode(' | ', $errors));
            }

            // Perform the import
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();

            // Log the notes import activity
            \App\Models\Activity::log(
                'import',
                'notes_imported_vacataire',
                "Import de notes par vacataire: {$vacataire->name} - {$ue->code} ({$request->session_type}) - {$importedCount} notes",
                $ue,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'session_type' => $request->session_type,
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'file_size' => $request->file('file')->getSize(),
                    'imported_count' => $importedCount,
                    'errors_count' => count($errors),
                    'department' => $ue->departement->nom ?? 'N/A',
                    'filiere' => $ue->filiere->nom ?? 'N/A'
                ]
            );

            // Handle results
            if ($import->hasErrors()) {
                $successMessage = $importedCount > 0 ? "{$importedCount} notes importées. " : "";
                $errorMessage = $successMessage . "Erreurs: " . implode(' | ', array_slice($errors, 0, 3));

                if (count($errors) > 3) {
                    $errorMessage .= " | (+" . (count($errors) - 3) . " autres)";
                }

                return back()->with('warning', $errorMessage);
            }

            if ($importedCount === 0) {
                return back()->with('warning', 'Aucune note importée. Vérifiez que le fichier contient des données d\'étudiants valides.');
            }

            return back()->with('success', "🎉 Import réussi! {$importedCount} notes importées pour l'UE {$ue->code} ({$request->session_type}).");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur d\'import: ' . $e->getMessage());
        }
    }

    // Show download template page
    public function showDownloadTemplatePage()
    {
        $vacataire = Auth::user();

        // Get vacataire's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id)
                  ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('vacataire.notes-download-template', compact('uesAssignees'));
    }

    // Show import notes page
    public function showImportPage()
    {
        $vacataire = Auth::user();

        // Get vacataire's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id)
                  ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('vacataire.notes-import', compact('uesAssignees'));
    }

    // Show add note page
    public function showAddNotePage()
    {
        $vacataire = Auth::user();

        // Get vacataire's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id)
                  ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('vacataire.notes-add', compact('uesAssignees'));
    }

    // Show edit note page
    public function showEditNotePage($noteId)
    {
        $vacataire = Auth::user();

        // Find note and verify access
        $note = Note::with(['uniteEnseignement', 'etudiant', 'uploadedBy'])
            ->whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
                $query->where('user_id', $vacataire->id)->where('validee', 'valide');
            })
            ->findOrFail($noteId);

        return view('vacataire.notes-edit', compact('note'));
    }

    // Download Excel template for notes
    public function downloadNotesTemplate(Request $request)
    {
        $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id'
        ]);

        $vacataire = Auth::user();

        // Verify vacataire is assigned to this UE
        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer les notes de cette UE.');
        }

        try {
            $ue = \App\Models\UniteEnseignement::find($request->ue_id);

            // Get students from the same filiere as the UE
            $etudiants = User::where('role', 'etudiant')
                ->where('filiere_id', $ue->filiere_id)
                ->orderBy('name')
                ->get();

            $filename = "template_notes_{$ue->code}_" . date('Y-m-d') . ".xlsx";

            // Create Excel file with template data
            return $this->generateNotesTemplate($etudiants, $ue, $filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du template: ' . $e->getMessage());
        }
    }

    // Generate Excel template
    private function generateNotesTemplate($etudiants, $ue, $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set sheet title
        $sheet->setTitle('Notes ' . $ue->code);

        // Set headers
        $headers = ['Nom Etudiant', 'CNE Etudiant', 'Note', 'Statut Absence'];
        $sheet->fromArray($headers, null, 'A1');

        // Style headers with purple theme
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '7c3aed']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // Add student data (empty table for filling)
        $row = 2;
        foreach ($etudiants as $etudiant) {
            $sheet->setCellValue('A' . $row, $etudiant->name);
            $sheet->setCellValue('B' . $row, $etudiant->matricule);
            $sheet->setCellValue('C' . $row, ''); // Empty note field
            $sheet->setCellValue('D' . $row, ''); // Empty absence status

            // Style data rows
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
            ];
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($dataStyle);

            $row++;
        }

        // Auto-size columns for better readability
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set minimum column widths
        $sheet->getColumnDimension('A')->setWidth(25); // Nom Etudiant
        $sheet->getColumnDimension('B')->setWidth(15); // CNE Etudiant
        $sheet->getColumnDimension('C')->setWidth(10); // Note
        $sheet->getColumnDimension('D')->setWidth(15); // Statut Absence

        // Add a simple header with UE info
        $sheet->insertNewRowBefore(1, 2);
        $sheet->setCellValue('A1', 'UE: ' . $ue->code . ' - ' . $ue->nom);
        $sheet->setCellValue('A2', 'Filière: ' . ($ue->filiere->nom ?? 'N/A'));

        // Style UE info
        $infoStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '7c3aed']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT]
        ];
        $sheet->getStyle('A1:A2')->applyFromArray($infoStyle);

        // Merge cells for UE info
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');

        // Create writer and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0'
        ];

        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    // Store a single note
    public function storeNote(Request $request)
    {
        $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'matricule' => 'required|string',
            'nom_etudiant' => 'required|string',
            'note' => 'nullable|numeric|min:0|max:20',
            'session_type' => 'required|in:normale,rattrapage',
            'is_absent' => 'required|boolean'
        ]);

        $vacataire = Auth::user();

        // Verify vacataire is assigned to this UE
        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer les notes de cette UE.');
        }

        try {
            // Find student by matricule
            $etudiant = User::where('matricule', $request->matricule)
                ->where('role', 'etudiant')
                ->first();

            if (!$etudiant) {
                return back()->with('error', 'Étudiant non trouvé avec ce CNE/matricule.');
            }

            // Validate note if student is present
            $note = null;
            $isAbsent = (bool) $request->is_absent;

            if (!$isAbsent && $request->note !== null) {
                $note = (float) $request->note;
            }

            // Create or update note
            Note::updateOrCreate(
                [
                    'ue_id' => $request->ue_id,
                    'etudiant_id' => $etudiant->id,
                    'session_type' => $request->session_type
                ],
                [
                    'note' => $note,
                    'is_absent' => $isAbsent,
                    'uploaded_by' => $vacataire->id
                ]
            );

            $ue = UniteEnseignement::find($request->ue_id);

            // Log the activity
            \App\Models\Activity::log(
                'create',
                'note_added_vacataire',
                "Note ajoutée par vacataire: {$vacataire->name} - {$ue->code} - {$etudiant->name} ({$request->session_type})",
                $ue,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'etudiant_name' => $etudiant->name,
                    'etudiant_matricule' => $etudiant->matricule,
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'session_type' => $request->session_type,
                    'note' => $note,
                    'is_absent' => $isAbsent,
                    'department' => $ue->departement->nom ?? 'N/A',
                    'filiere' => $ue->filiere->nom ?? 'N/A'
                ]
            );

            return redirect()->route('vacataire.notes')->with('success', 'Note enregistrée avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }



    // Delete a note
    public function deleteNote($noteId)
    {
        $vacataire = Auth::user();

        // Find note and verify access
        $note = Note::with(['uniteEnseignement', 'etudiant'])
            ->whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
                $query->where('user_id', $vacataire->id)->where('validee', 'valide');
            })
            ->findOrFail($noteId);

        try {
            // Log the activity before deletion
            \App\Models\Activity::log(
                'delete',
                'note_deleted_vacataire',
                "Note supprimée par vacataire: {$vacataire->name} - {$note->uniteEnseignement->code} - {$note->etudiant->name} ({$note->session_type})",
                $note,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'etudiant_name' => $note->etudiant->name,
                    'etudiant_matricule' => $note->etudiant->matricule,
                    'ue_code' => $note->uniteEnseignement->code,
                    'ue_nom' => $note->uniteEnseignement->nom,
                    'session_type' => $note->session_type,
                    'note' => $note->note,
                    'is_absent' => $note->is_absent,
                    'department' => $note->uniteEnseignement->departement->nom ?? 'N/A',
                    'filiere' => $note->uniteEnseignement->filiere->nom ?? 'N/A'
                ]
            );

            // Delete the note
            $note->delete();

            return redirect()->route('vacataire.notes')->with('success', 'Note supprimée avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Update an existing note
    public function updateNote(Request $request, $noteId)
    {
        $request->validate([
            'note' => 'nullable|numeric|min:0|max:20',
            'is_absent' => 'required|boolean'
        ]);

        $vacataire = Auth::user();

        // Find note and verify access
        $note = Note::with(['uniteEnseignement', 'etudiant'])
            ->whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id)->where('validee', 'valide');
            })
            ->findOrFail($noteId);

        try {
            // Validate note if student is present
            $noteValue = null;
            $isAbsent = (bool) $request->is_absent;

            if (!$isAbsent && $request->note !== null) {
                $noteValue = (float) $request->note;
            }

            // Update note
            $note->update([
                'note' => $noteValue,
                'is_absent' => $isAbsent,
                'uploaded_by' => $vacataire->id
            ]);

            // Log the activity
            \App\Models\Activity::log(
                'update',
                'note_updated_vacataire',
                "Note mise à jour par vacataire: {$vacataire->name} - {$note->uniteEnseignement->code} - {$note->etudiant->name} ({$note->session_type})",
                $note,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'etudiant_name' => $note->etudiant->name,
                    'etudiant_matricule' => $note->etudiant->matricule,
                    'ue_code' => $note->uniteEnseignement->code,
                    'ue_nom' => $note->uniteEnseignement->nom,
                    'session_type' => $note->session_type,
                    'note' => $noteValue,
                    'is_absent' => $isAbsent,
                    'department' => $note->uniteEnseignement->departement->nom ?? 'N/A',
                    'filiere' => $note->uniteEnseignement->filiere->nom ?? 'N/A'
                ]
            );

            return redirect()->route('vacataire.notes')->with('success', 'Note mise à jour avec succès!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
}
