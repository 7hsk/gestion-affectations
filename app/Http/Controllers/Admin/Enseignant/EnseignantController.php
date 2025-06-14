<?php

namespace App\Http\Controllers\Admin\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departement;
use App\Models\UniteEnseignement;
use App\Models\Note;
use App\Models\Schedule;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\NotesImport;
use Barryvdh\DomPDF\Facade\Pdf;

class EnseignantController extends Controller
{
    // Dashboard - Enhanced main teacher view
    public function dashboard()
    {
        $teacher = Auth::user();
        $specialites = $teacher->specialite ? explode(',', $teacher->specialite) : [];
        $departement = Departement::find($teacher->departement_id);
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Get current teaching assignments with detailed info
        $affectations = Affectation::where('user_id', $teacher->id)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement' => function ($query) {
                $query->where('est_vacant', false);
            }])
            ->get()
            ->filter(function ($affectation) {
                return $affectation->uniteEnseignement !== null;
            });

        $teachingLoad = $affectations->groupBy('uniteEnseignement.semestre');

        // Calculate statistics
        $stats = [
            'total_ues' => $affectations->count(),
            'total_hours' => $affectations->sum(function ($affectation) {
                $ue = $affectation->uniteEnseignement;
                return $ue->heures_cm + $ue->heures_td + $ue->heures_tp;
            }),
            'pending_assignments' => Affectation::where('user_id', $teacher->id)
                ->where('validee', 'en_attente')->count(),
            'students_count' => $this->getTotalStudentsCount($teacher->id)
        ];

        // Get recent notifications
        $notifications = \App\Models\Notification::where('user_id', $teacher->id)
            ->where('is_read', false)
            ->latest()
            ->take(5)
            ->get();

        // Get upcoming schedule
        $upcomingSchedule = Schedule::where('user_id', $teacher->id)
            ->with('uniteEnseignement')
            ->whereRaw('DAYOFWEEK(CURDATE()) <= CASE
                WHEN jour_semaine = "lundi" THEN 2
                WHEN jour_semaine = "mardi" THEN 3
                WHEN jour_semaine = "mercredi" THEN 4
                WHEN jour_semaine = "jeudi" THEN 5
                WHEN jour_semaine = "vendredi" THEN 6
                WHEN jour_semaine = "samedi" THEN 7
                ELSE 1 END')
            ->orderByRaw('CASE
                WHEN jour_semaine = "lundi" THEN 1
                WHEN jour_semaine = "mardi" THEN 2
                WHEN jour_semaine = "mercredi" THEN 3
                WHEN jour_semaine = "jeudi" THEN 4
                WHEN jour_semaine = "vendredi" THEN 5
                WHEN jour_semaine = "samedi" THEN 6
                ELSE 7 END')
            ->orderBy('heure_debut')
            ->take(3)
            ->get();

        // Get workload distribution for chart
        $workloadData = $this->getWorkloadDistribution($teacher->id);

        return view('enseignant.dashboard', compact(
            'teacher',
            'specialites',
            'departement',
            'teachingLoad',
            'stats',
            'notifications',
            'upcomingSchedule',
            'workloadData'
        ));
    }

    // Helper method to get total students count
    private function getTotalStudentsCount($teacherId)
    {
        return Affectation::where('user_id', $teacherId)
            ->where('validee', 'valide')
            ->with('uniteEnseignement.filiere')
            ->get()
            ->sum(function ($affectation) {
                // Estimate students per UE based on filiere (this could be improved with actual enrollment data)
                return 30; // Default estimate
            });
    }

    // Helper method to get workload distribution
    private function getWorkloadDistribution($teacherId)
    {
        $affectations = Affectation::where('user_id', $teacherId)
            ->where('validee', 'valide')
            ->with('uniteEnseignement')
            ->get();

        $distribution = [
            'CM' => 0,
            'TD' => 0,
            'TP' => 0
        ];

        foreach ($affectations as $affectation) {
            $ue = $affectation->uniteEnseignement;
            if ($ue) {
                $distribution['CM'] += $ue->heures_cm;
                $distribution['TD'] += $ue->heures_td;
                $distribution['TP'] += $ue->heures_tp;
            }
        }

        return $distribution;
    }

    // Display teaching units with enhanced features
    public function unites(Request $request)
    {
        $teacher = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Get teacher's specialites
        $teacherSpecialites = $teacher->specialite ? explode(',', $teacher->specialite) : [];

        // Get current validated teaching assignments with their UEs
        $query = Affectation::where('user_id', $teacher->id)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement' => function ($query) use ($teacherSpecialites) {
                $query->where('est_vacant', false)
                    ->with(['filiere', 'departement'])
                    ->orderBy('semestre')
                    ->orderBy('code');

                // Filter by teacher's specialites if they exist
                if (!empty($teacherSpecialites)) {
                    $query->where(function ($q) use ($teacherSpecialites) {
                        foreach ($teacherSpecialites as $specialite) {
                            $q->orWhere('specialite', 'LIKE', '%' . trim($specialite) . '%');
                        }
                    });
                }
            }]);

        // Apply filters if provided
        if ($request->filled('semestre')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('semestre', $request->semestre);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('uniteEnseignement', function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $affectations = $query->get();

        // Extract and group UEs by semester with additional data
        $groupedUnites = $affectations->filter(function ($affectation) {
            return $affectation->uniteEnseignement !== null;
        })
            ->map(function ($affectation) {
                $ue = $affectation->uniteEnseignement;
                $ue->type_seance = $affectation->type_seance;
                $ue->total_hours = $ue->heures_cm + $ue->heures_td + $ue->heures_tp;

                // Get student count for this UE (estimated)
                $ue->student_count = 30; // This could be improved with actual enrollment data

                // Get notes statistics
                $ue->notes_stats = $this->getNotesStatistics($ue->id);

                return $ue;
            })
            ->groupBy('semestre');

        // Calculate summary statistics
        $summary = [
            'total_ues' => $affectations->count(),
            'total_hours' => $affectations->sum(function ($affectation) {
                $ue = $affectation->uniteEnseignement;
                return $ue ? $ue->heures_cm + $ue->heures_td + $ue->heures_tp : 0;
            }),
            'by_type' => [
                'CM' => $affectations->where('type_seance', 'CM')->count(),
                'TD' => $affectations->where('type_seance', 'TD')->count(),
                'TP' => $affectations->where('type_seance', 'TP')->count(),
            ]
        ];

        // Get available semesters for filter
        $availableSemesters = $affectations->pluck('uniteEnseignement.semestre')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('enseignant.unites', [
            'groupedUnites' => $groupedUnites,
            'teacher' => $teacher,
            'summary' => $summary,
            'availableSemesters' => $availableSemesters,
            'filters' => $request->only(['semestre', 'search'])
        ]);
    }

    // Helper method to get notes statistics for a UE
    private function getNotesStatistics($ueId)
    {
        $notes = Note::where('ue_id', $ueId)->get();

        if ($notes->isEmpty()) {
            return [
                'total' => 0,
                'average' => 0,
                'success_rate' => 0
            ];
        }

        $validNotes = $notes->whereNotNull('note');
        $average = $validNotes->avg('note');
        $successCount = $validNotes->where('note', '>=', 10)->count();
        $successRate = $validNotes->count() > 0 ? ($successCount / $validNotes->count()) * 100 : 0;

        return [
            'total' => $notes->count(),
            'average' => round($average, 2),
            'success_rate' => round($successRate, 1)
        ];
    }

    // Gestion des notes with grading logic (cloned from vacataire)
    public function notes(Request $request)
    {
        $enseignant = Auth::user();

        // Get all notes for this enseignant's UEs
        $baseQuery = Note::whereHas('uniteEnseignement.affectations', function ($q) use ($enseignant) {
            $q->where('user_id', $enseignant->id)->where('validee', 'valide');
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
        $groupedNotes = $allNotes->groupBy(function ($note) {
            return $note->ue_id . '_' . $note->etudiant_id;
        });

        $notes = collect();
        foreach ($groupedNotes as $key => $studentNotes) {
            $normaleNote = $studentNotes->where('session_type', 'normale')->first();
            $rattrapageNote = $studentNotes->where('session_type', 'rattrapage')->first();

            // Calculate final grade and status
            $noteFinale = null;
            $status = 'Non Validé';
            $session = 'Aucune';

            if ($normaleNote) {
                if ($normaleNote->is_absent) {
                    $noteNormale = 'Absent';
                } else {
                    $noteNormale = $normaleNote->note;
                    if ($noteNormale >= 10) {
                        $noteFinale = $noteNormale;
                        $status = 'Validé';
                        $session = 'Normale';
                    }
                }
            } else {
                $noteNormale = '-';
            }

            if ($rattrapageNote) {
                if ($rattrapageNote->is_absent) {
                    $noteRattrapage = 'Absent';
                } else {
                    $noteRattrapage = $rattrapageNote->note;
                    if ($noteRattrapage >= 10) {
                        $noteFinale = $noteRattrapage;
                        $status = 'Validé';
                        $session = 'Rattrapage';
                    }
                }
            } else {
                $noteRattrapage = '-';
            }

            // Final grade calculation
            if ($noteFinale === null) {
                if ($noteNormale !== '-' && $noteNormale !== 'Absent') {
                    $noteFinale = $noteNormale . '/20';
                } elseif ($noteRattrapage !== '-' && $noteRattrapage !== 'Absent') {
                    $noteFinale = $noteRattrapage . '/20';
                } else {
                    $noteFinale = 'Non Validé';
                }
            } else {
                $noteFinale = $noteFinale . '/20';
            }

            $notes->push((object)[
                'ue' => $normaleNote ? $normaleNote->uniteEnseignement : $rattrapageNote->uniteEnseignement,
                'etudiant' => $normaleNote ? $normaleNote->etudiant : $rattrapageNote->etudiant,
                'note_normale' => $noteNormale,
                'note_rattrapage' => $noteRattrapage,
                'note_finale' => $noteFinale,
                'status' => $status,
                'session' => $session,
                'normale_note_obj' => $normaleNote,
                'rattrapage_note_obj' => $rattrapageNote
            ]);
        }

        // Paginate the results
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $notes = new \Illuminate\Pagination\LengthAwarePaginator(
            $notes->forPage($currentPage, $perPage),
            $notes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Get enseignant's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($query) use ($enseignant) {
            $query->where('user_id', $enseignant->id)
                ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('enseignant.notes', compact('notes', 'uesAssignees'));
    }

    // Get real students for a UE from database
    private function getStudentsForUE($ue)
    {
        if (!$ue) {
            return collect();
        }

        // First, try to get students who already have notes for this UE
        $studentsWithNotes = Note::where('ue_id', $ue->id)
            ->with(['etudiant' => function ($query) {
                $query->with('departement');
            }])
            ->get()
            ->pluck('etudiant')
            ->filter()
            ->unique('id');

        if ($studentsWithNotes->isNotEmpty()) {
            // Add filière information to existing students
            return $studentsWithNotes->map(function ($student) use ($ue) {
                if ($student->filiere_id && $ue->filiere) {
                    $student->filiere = $ue->filiere->nom;
                } else {
                    $student->filiere = $ue->filiere ? $ue->filiere->nom : 'Non assignée';
                }

                // Ensure matricule exists
                if (!$student->matricule) {
                    $filiere = $ue->filiere ? $ue->filiere->nom : 'GI';
                    $level = substr($ue->semestre ?? 'S1', 1);
                    $student->matricule = $filiere . $level . str_pad($student->id, 3, '0', STR_PAD_LEFT);
                }

                return $student;
            });
        }

        // If no students have notes yet, get students from the same filière as the UE
        $query = User::where('role', 'etudiant');

        // If UE has a filière, get students from that filière
        if ($ue->filiere_id) {
            $query->where('filiere_id', $ue->filiere_id);
        }

        $realStudents = $query->with(['departement'])
            ->orderBy('name')
            ->get();

        if ($realStudents->isNotEmpty()) {
            return $realStudents->map(function ($student) use ($ue) {
                // Set filière name
                if ($student->filiere_id && $ue->filiere && $student->filiere_id == $ue->filiere_id) {
                    $student->filiere = $ue->filiere->nom;
                } else {
                    $student->filiere = $ue->filiere ? $ue->filiere->nom : 'Non assignée';
                }

                // Generate matricule if not exists
                if (!$student->matricule) {
                    $filiere = $ue->filiere ? $ue->filiere->nom : 'GI';
                    $level = substr($ue->semestre ?? 'S1', 1);
                    $student->matricule = $filiere . $level . str_pad($student->id, 3, '0', STR_PAD_LEFT);
                }

                return $student;
            });
        }

        // If no real students found, get all students as fallback
        $allStudents = User::where('role', 'etudiant')
            ->orderBy('name')
            ->get();

        if ($allStudents->isNotEmpty()) {
            return $allStudents->map(function ($student) use ($ue) {
                $student->filiere = $ue->filiere ? $ue->filiere->nom : 'Non assignée';

                if (!$student->matricule) {
                    $filiere = $ue->filiere ? $ue->filiere->nom : 'GI';
                    $level = substr($ue->semestre ?? 'S1', 1);
                    $student->matricule = $filiere . $level . str_pad($student->id, 3, '0', STR_PAD_LEFT);
                }

                return $student;
            });
        }

        // Last resort: generate sample students for demonstration
        return $this->generateSampleStudents($ue);
    }

    // Generate sample students for demonstration (when no real students exist)
    private function generateSampleStudents($ue)
    {
        $students = collect();
        $filiere = $ue->filiere ? $ue->filiere->nom : 'GI';
        $level = substr($ue->semestre ?? 'S1', 1); // Extract number from S1, S2, etc.

        for ($i = 1; $i <= 15; $i++) {
            $students->push((object)[
                'id' => 1000 + $i,
                'name' => "Étudiant {$i} {$filiere}",
                'email' => "etudiant{$i}.{$filiere}@ensa.ma",
                'matricule' => $filiere . $level . str_pad($i, 3, '0', STR_PAD_LEFT),
                'filiere' => $filiere,
                'role' => 'etudiant'
            ]);
        }

        return $students;
    }

    // Calculate detailed statistics
    private function calculateDetailedStatistics($ueId, $sessionType)
    {
        $notes = Note::where('ue_id', $ueId)
            ->where('session_type', $sessionType)
            ->whereNotNull('note')
            ->get();

        if ($notes->isEmpty()) {
            return [
                'total_students' => 0,
                'graded_students' => 0,
                'average' => 0,
                'median' => 0,
                'min' => 0,
                'max' => 0,
                'success_rate' => 0,
                'grade_ranges' => [
                    'excellent' => 0, // 16-20
                    'good' => 0,      // 14-16
                    'average' => 0,   // 12-14
                    'passing' => 0,   // 10-12
                    'failing' => 0    // 0-10
                ]
            ];
        }

        $grades = $notes->pluck('note')->sort()->values();
        $successCount = $grades->where('>=', 10)->count();

        // Get actual student count for this UE
        $ue = UniteEnseignement::find($ueId);
        $actualStudentCount = 0;

        if ($ue) {
            // Count students from the same filière as the UE
            if ($ue->filiere_id) {
                $actualStudentCount = User::where('role', 'etudiant')
                    ->where('filiere_id', $ue->filiere_id)
                    ->count();
            }

            // If no students in specific filière, count all students
            if ($actualStudentCount == 0) {
                $actualStudentCount = User::where('role', 'etudiant')->count();
            }

            // Fallback if no students in database
            if ($actualStudentCount == 0) {
                $actualStudentCount = 15;
            }
        } else {
            $actualStudentCount = 15; // Fallback
        }

        return [
            'total_students' => $actualStudentCount,
            'graded_students' => $notes->count(),
            'average' => round($grades->avg(), 2),
            'median' => $grades->median(),
            'min' => $grades->min(),
            'max' => $grades->max(),
            'success_rate' => round(($successCount / $notes->count()) * 100, 1),
            'grade_ranges' => [
                'excellent' => $grades->whereBetween('note', [16, 20])->count(),
                'good' => $grades->whereBetween('note', [14, 16])->count(),
                'average' => $grades->whereBetween('note', [12, 14])->count(),
                'passing' => $grades->whereBetween('note', [10, 12])->count(),
                'failing' => $grades->where('<', 10)->count()
            ]
        ];
    }

    // Get grade distribution for charts
    private function getGradeDistribution($ueId, $sessionType)
    {
        if (!$ueId) return [];

        $notes = Note::where('ue_id', $ueId)
            ->where('session_type', $sessionType)
            ->whereNotNull('note')
            ->get();

        $distribution = [
            '0-5' => 0,
            '5-10' => 0,
            '10-15' => 0,
            '15-20' => 0
        ];

        foreach ($notes as $note) {
            $grade = $note->note;
            if ($grade < 5) $distribution['0-5']++;
            elseif ($grade < 10) $distribution['5-10']++;
            elseif ($grade < 15) $distribution['10-15']++;
            else $distribution['15-20']++;
        }

        return $distribution;
    }

    // Store a single note (cloned from vacataire)
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

        $enseignant = Auth::user();

        // Verify enseignant is assigned to this UE
        $affectation = Affectation::where('user_id', $enseignant->id)
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
                    'uploaded_by' => $enseignant->id
                ]
            );

            $ue = UniteEnseignement::find($request->ue_id);

            // Log the activity
            \App\Models\Activity::log(
                'create',
                'note_added_enseignant',
                "Note ajoutée par enseignant: {$enseignant->name} - {$ue->code} - {$etudiant->name} ({$request->session_type})",
                $ue,
                [
                    'teacher_name' => $enseignant->name,
                    'teacher_email' => $enseignant->email,
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

            return redirect()->route('enseignant.notes')->with('success', 'Note enregistrée avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    // Import notes from Excel/CSV - COMPLETE IMPLEMENTATION (cloned from vacataire)
    public function importNotes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'ue_id' => 'required|exists:unites_enseignement,id',
            'session_type' => 'required|in:normale,rattrapage'
        ]);

        $enseignant = Auth::user();

        // Verify enseignant is assigned to this UE
        $affectation = Affectation::where('user_id', $enseignant->id)
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
            $import = new \App\Imports\NotesImport($request->ue_id, $enseignant->id, $request->session_type);

            // Validate Excel file structure and UE match
            if (!$import->validateExcelFile($file->getPathname())) {
                $errors = $import->getErrors();
                return back()->with('error', 'Validation échouée: ' . implode(' | ', $errors));
            }

            // Perform the import
            \Maatwebsite\Excel\Facades\Excel::import($import, $file);

            $importedCount = $import->getImportedCount();
            $errors = $import->getErrors();

            if ($import->hasErrors()) {
                $errorMessage = "Import terminé avec {$importedCount} notes importées, mais avec des erreurs: " . implode(' | ', $errors);
                return back()->with('warning', $errorMessage);
            }

            // Log the import activity
            \App\Models\Activity::log(
                'import',
                'notes_imported_enseignant',
                "Import de notes par enseignant: {$enseignant->name} - {$ue->code} ({$request->session_type}) - {$importedCount} notes",
                $ue,
                [
                    'teacher_name' => $enseignant->name,
                    'teacher_email' => $enseignant->email,
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'session_type' => $request->session_type,
                    'imported_count' => $importedCount,
                    'department' => $ue->departement->nom ?? 'N/A',
                    'filiere' => $ue->filiere->nom ?? 'N/A'
                ]
            );

            return redirect()->route('enseignant.notes')->with('success', "Import réussi! {$importedCount} notes ont été importées.");
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'import: ' . $e->getMessage());
        }
    }

    // Show download template page (cloned from vacataire)
    public function showDownloadTemplatePage()
    {
        $enseignant = Auth::user();

        // Get enseignant's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($query) use ($enseignant) {
            $query->where('user_id', $enseignant->id)
                ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('enseignant.notes-download-template', compact('uesAssignees'));
    }

    // Show import notes page (cloned from vacataire)
    public function showImportPage()
    {
        $enseignant = Auth::user();

        // Get enseignant's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($query) use ($enseignant) {
            $query->where('user_id', $enseignant->id)
                ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('enseignant.notes-import', compact('uesAssignees'));
    }

    // Show add note page (cloned from vacataire)
    public function showAddNotePage()
    {
        $enseignant = Auth::user();

        // Get enseignant's assigned UEs
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($query) use ($enseignant) {
            $query->where('user_id', $enseignant->id)
                ->where('validee', 'valide');
        })->with('filiere')->get();

        return view('enseignant.notes-add', compact('uesAssignees'));
    }

    // Show edit note page (cloned from vacataire)
    public function showEditNotePage($noteId)
    {
        $enseignant = Auth::user();

        // Find note and verify access
        $note = Note::with(['uniteEnseignement', 'etudiant'])
            ->whereHas('uniteEnseignement.affectations', function ($query) use ($enseignant) {
                $query->where('user_id', $enseignant->id)->where('validee', 'valide');
            })
            ->findOrFail($noteId);

        return view('enseignant.notes-edit', compact('note'));
    }

    // Download Excel template for notes (cloned from vacataire)
    public function downloadNotesTemplate(Request $request)
    {
        $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id'
        ]);

        $enseignant = Auth::user();

        // Verify enseignant is assigned to this UE
        $affectation = Affectation::where('user_id', $enseignant->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer les notes de cette UE.');
        }

        try {
            $ue = UniteEnseignement::with('filiere')->find($request->ue_id);

            if (!$ue) {
                return back()->with('error', 'UE introuvable.');
            }

            // Create export instance
            $export = new \App\Exports\NotesTemplateExport($ue);

            $filename = 'template-notes-' . $ue->code . '-' . date('Y-m-d') . '.xlsx';

            return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du template: ' . $e->getMessage());
        }
    }

    // Update an existing note (cloned from vacataire)
    public function updateNote(Request $request, $noteId)
    {
        $request->validate([
            'note' => 'nullable|numeric|min:0|max:20',
            'is_absent' => 'required|boolean'
        ]);

        $enseignant = Auth::user();

        // Find note and verify access
        $note = Note::with(['uniteEnseignement', 'etudiant'])
            ->whereHas('uniteEnseignement.affectations', function ($query) use ($enseignant) {
                $query->where('user_id', $enseignant->id)->where('validee', 'valide');
            })
            ->findOrFail($noteId);

        try {
            $isAbsent = (bool) $request->is_absent;
            $noteValue = null;

            if (!$isAbsent && $request->note !== null) {
                $noteValue = (float) $request->note;
            }

            $note->update([
                'note' => $noteValue,
                'is_absent' => $isAbsent,
                'uploaded_by' => $enseignant->id
            ]);

            // Log the activity
            \App\Models\Activity::log(
                'update',
                'note_updated_enseignant',
                "Note modifiée par enseignant: {$enseignant->name} - {$note->uniteEnseignement->code} - {$note->etudiant->name}",
                $note->uniteEnseignement,
                [
                    'teacher_name' => $enseignant->name,
                    'etudiant_name' => $note->etudiant->name,
                    'ue_code' => $note->uniteEnseignement->code,
                    'old_note' => $note->getOriginal('note'),
                    'new_note' => $noteValue,
                    'old_absent' => $note->getOriginal('is_absent'),
                    'new_absent' => $isAbsent
                ]
            );

            return redirect()->route('enseignant.notes')->with('success', 'Note modifiée avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    // Delete a note (cloned from vacataire)
    public function deleteNote($noteId)
    {
        $enseignant = Auth::user();

        try {
            // Find note and verify access
            $note = Note::with(['uniteEnseignement', 'etudiant'])
                ->whereHas('uniteEnseignement.affectations', function ($query) use ($enseignant) {
                    $query->where('user_id', $enseignant->id)->where('validee', 'valide');
                })
                ->findOrFail($noteId);

            // Log the activity before deletion
            \App\Models\Activity::log(
                'delete',
                'note_deleted_enseignant',
                "Note supprimée par enseignant: {$enseignant->name} - {$note->uniteEnseignement->code} - {$note->etudiant->name}",
                $note->uniteEnseignement,
                [
                    'teacher_name' => $enseignant->name,
                    'etudiant_name' => $note->etudiant->name,
                    'ue_code' => $note->uniteEnseignement->code,
                    'deleted_note' => $note->note,
                    'was_absent' => $note->is_absent
                ]
            );

            // Delete the note
            $note->delete();

            return redirect()->route('enseignant.notes')->with('success', 'Note supprimée avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    // Display schedule
    public function emploiDuTemps(Request $request)
    {
        $teacher = Auth::user();
        $daysOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

        // Get schedules grouped by day and time
        $schedules = Schedule::where('user_id', $teacher->id)
            ->with(['uniteEnseignement', 'filiere'])
            ->orderByRaw("FIELD(jour_semaine, '" . implode("','", $daysOrder) . "')")
            ->orderBy('heure_debut')
            ->get()
            ->groupBy(['jour_semaine', function ($item) {
                // Format time to match the view expectation (HH:MM-HH:MM)
                $debut = \Carbon\Carbon::parse($item->heure_debut)->format('H:i');
                $fin = \Carbon\Carbon::parse($item->heure_fin)->format('H:i');
                return $debut . '-' . $fin;
            }]);

        // Always use standard time slots for consistent display
        $timeSlots = [
            ['start' => '08:30', 'end' => '10:30'],
            ['start' => '10:30', 'end' => '12:30'],
            ['start' => '14:30', 'end' => '16:30'],
            ['start' => '16:30', 'end' => '18:30']
        ];

        // Get UEs for filter
        $unites = Affectation::where('user_id', $teacher->id)
            ->where('validee', true)
            ->with('uniteEnseignement')
            ->get()
            ->pluck('uniteEnseignement')
            ->unique('id')
            ->sortBy('code');

        return view('enseignant.emploi-du-temps', compact(
            'schedules',
            'timeSlots',
            'unites',
            'daysOrder'
        ));
    }

    // Export schedule to PDF with logo, teacher name, and school info
    public function exportEmploiDuTemps()
    {
        try {
            $teacher = Auth::user();
            $daysOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

            $schedules = Schedule::where('user_id', $teacher->id)
                ->with(['uniteEnseignement', 'filiere'])
                ->orderByRaw("FIELD(jour_semaine, '" . implode("','", $daysOrder) . "')")
                ->orderBy('heure_debut')
                ->get();

            $data = [
                'teacher' => $teacher,
                'schedules' => $schedules,
                'title' => 'Emploi du Temps - ' . $teacher->name,
                'currentDate' => \Carbon\Carbon::now()->format('d/m/Y'),
                'academicYear' => date('Y') . '-' . (date('Y') + 1)
            ];

            // Configure PDF options for landscape orientation
            $pdf = Pdf::loadView('enseignant.exports.emploi-du-temps', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true
                ]);

            $filename = 'emploi-du-temps-' . Str::slug($teacher->name) . '-' . date('Y-m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    // Display course details
    public function courses()
    {
        $teacher = Auth::user();

        $courses = Affectation::where('user_id', $teacher->id)
            ->where('validee', true)
            ->with(['uniteEnseignement' => function ($query) {
                $query->withCount('etudiants');
            }])
            ->get()
            ->pluck('uniteEnseignement')
            ->filter()
            ->sortBy('code');

        return view('enseignant.courses', compact('courses'));
    }

    // Display students for a course
    public function students($ueId)
    {
        $teacher = Auth::user();

        // Verify the UE is assigned to the teacher
        $isAssigned = Affectation::where('user_id', $teacher->id)
            ->where('ue_id', $ueId)
            ->where('validee', true)
            ->exists();

        if (!$isAssigned) {
            abort(403, 'Action non autorisée');
        }

        $ue = UniteEnseignement::with(['etudiants' => function ($query) {
            $query->orderBy('name');
        }])->findOrFail($ueId);

        return view('enseignant.students', compact('ue'));
    }

    // New method: Get available UEs for selection
    public function getAvailableUEs(Request $request)
    {
        $teacher = Auth::user();
        $specialites = $teacher->specialite ? explode(',', $teacher->specialite) : [];

        // Get UEs that match teacher's specialties and are not yet assigned
        $availableUEs = UniteEnseignement::where('est_vacant', true)
            ->where('departement_id', $teacher->departement_id)
            ->with(['filiere', 'departement'])
            ->get()
            ->filter(function ($ue) use ($specialites) {
                // Simple matching logic - could be improved
                foreach ($specialites as $specialite) {
                    if (stripos($ue->nom, trim($specialite)) !== false) {
                        return true;
                    }
                }
                return false;
            });

        // Calculate workload if teacher selects these UEs
        $currentLoad = $this->getWorkloadDistribution($teacher->id);

        return view('enseignant.available-ues', compact('availableUEs', 'currentLoad', 'teacher'));
    }

    // New method: Request UE assignment
    public function requestAssignment(Request $request)
    {
        $validated = $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'type_seance' => 'required|in:CM,TD,TP',
            'justification' => 'nullable|string|max:500'
        ]);

        $teacher = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Check if already requested
        $existingRequest = Affectation::where('user_id', $teacher->id)
            ->where('ue_id', $validated['ue_id'])
            ->where('annee_universitaire', $currentYear)
            ->first();

        if ($existingRequest) {
            return back()->with('error', 'Vous avez déjà fait une demande pour cette UE.');
        }

        // Create assignment request
        Affectation::create([
            'user_id' => $teacher->id,
            'ue_id' => $validated['ue_id'],
            'type_seance' => $validated['type_seance'],
            'annee_universitaire' => $currentYear,
            'validee' => 'en_attente'
        ]);

        // Create notification for department head
        $departmentHead = User::where('departement_id', $teacher->departement_id)
            ->where('role', 'chef')
            ->first();

        if ($departmentHead) {
            \App\Models\Notification::create([
                'user_id' => $departmentHead->id,
                'title' => 'Nouvelle demande d\'affectation',
                'message' => "L'enseignant {$teacher->name} a demandé l'affectation de l'UE " .
                    UniteEnseignement::find($validated['ue_id'])->code,
                'is_read' => false
            ]);
        }

        return back()->with('success', 'Votre demande d\'affectation a été envoyée avec succès.');
    }

    // New method: Teaching load calculator
    public function calculateLoad(Request $request)
    {
        $teacher = Auth::user();
        $selectedUEs = $request->get('ue_ids', []);

        $currentLoad = $this->getWorkloadDistribution($teacher->id);
        $projectedLoad = $currentLoad;

        if (!empty($selectedUEs)) {
            $additionalUEs = UniteEnseignement::whereIn('id', $selectedUEs)->get();
            foreach ($additionalUEs as $ue) {
                $projectedLoad['CM'] += $ue->heures_cm;
                $projectedLoad['TD'] += $ue->heures_td;
                $projectedLoad['TP'] += $ue->heures_tp;
            }
        }

        $totalCurrent = array_sum($currentLoad);
        $totalProjected = array_sum($projectedLoad);
        $minRequired = 192; // Minimum hours per year
        $maxRecommended = 240; // Maximum recommended hours

        $analysis = [
            'current' => $currentLoad,
            'projected' => $projectedLoad,
            'total_current' => $totalCurrent,
            'total_projected' => $totalProjected,
            'difference' => $totalProjected - $totalCurrent,
            'min_required' => $minRequired,
            'max_recommended' => $maxRecommended,
            'status' => $this->getLoadStatus($totalProjected, $minRequired, $maxRecommended)
        ];

        return response()->json($analysis);
    }

    // Helper method to determine load status
    private function getLoadStatus($total, $min, $max)
    {
        if ($total < $min) {
            return ['type' => 'warning', 'message' => 'Charge horaire insuffisante'];
        } elseif ($total > $max) {
            return ['type' => 'danger', 'message' => 'Charge horaire excessive'];
        } else {
            return ['type' => 'success', 'message' => 'Charge horaire optimale'];
        }
    }

    // New method: Performance analytics
    public function analytics()
    {
        $teacher = Auth::user();

        // Get teaching history
        $teachingHistory = \App\Models\HistoriqueAffectation::where('user_id', $teacher->id)
            ->with('uniteEnseignement')
            ->orderBy('annee_universitaire', 'desc')
            ->get()
            ->groupBy('annee_universitaire');

        // Get performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($teacher->id);

        // Get student feedback (mock data for now)
        $studentFeedback = $this->generateMockFeedback();

        return view('enseignant.analytics', compact(
            'teacher',
            'teachingHistory',
            'performanceMetrics',
            'studentFeedback'
        ));
    }

    // Helper method to calculate performance metrics
    private function calculatePerformanceMetrics($teacherId)
    {
        $affectations = Affectation::where('user_id', $teacherId)
            ->where('validee', 'valide')
            ->with('uniteEnseignement')
            ->get();

        $totalStudents = 0;
        $totalSuccessRate = 0;
        $ueCount = 0;

        foreach ($affectations as $affectation) {
            $ue = $affectation->uniteEnseignement;
            if ($ue) {
                $stats = $this->getNotesStatistics($ue->id);
                $totalStudents += $stats['total'];
                $totalSuccessRate += $stats['success_rate'];
                $ueCount++;
            }
        }

        return [
            'total_students' => $totalStudents,
            'average_success_rate' => $ueCount > 0 ? round($totalSuccessRate / $ueCount, 1) : 0,
            'total_ues' => $ueCount,
            'years_teaching' => 3 // Mock data
        ];
    }

    // Generate mock student feedback
    private function generateMockFeedback()
    {
        return [
            'overall_rating' => 4.2,
            'teaching_quality' => 4.5,
            'course_organization' => 4.0,
            'availability' => 4.3,
            'comments' => [
                'Excellent professeur, très pédagogue',
                'Cours bien structurés et intéressants',
                'Toujours disponible pour répondre aux questions'
            ]
        ];
    }
}
