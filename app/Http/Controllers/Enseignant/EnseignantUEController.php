<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\UniteEnseignement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnseignantUEController extends Controller
{
    // Display UE management dashboard for enseignant
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        // CURRENT YEAR AFFECTATIONS (for current teaching)
        // Get approved affectations for CURRENT YEAR (left side)
        $approvedAffectations = Affectation::with(['uniteEnseignement.filiere', 'uniteEnseignement.departement'])
            ->where('user_id', $user->id)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $currentYear)
            ->orderBy('created_at', 'desc')
            ->get();

        // NEXT YEAR DEMANDS (for next year requests)
        // Get pending demands for NEXT YEAR (right side)
        $pendingAffectations = Affectation::with(['uniteEnseignement.filiere', 'uniteEnseignement.departement'])
            ->where('user_id', $user->id)
            ->where('validee', 'en_attente')
            ->where('annee_universitaire', $nextYear) // NEXT YEAR ONLY
            ->orderBy('created_at', 'desc')
            ->get();

        // Get rejected/annulled demands for NEXT YEAR (historique)
        $rejectedAffectations = Affectation::with(['uniteEnseignement.filiere', 'uniteEnseignement.departement'])
            ->where('user_id', $user->id)
            ->whereIn('validee', ['rejete', 'annule'])
            ->where('annee_universitaire', $nextYear) // NEXT YEAR ONLY
            ->orderBy('created_at', 'desc')
            ->get();

        // Get user's specialities for filtering
        $userSpecialites = explode(',', $user->specialite ?? '');
        $userSpecialites = array_map('trim', $userSpecialites);

        // Get available UEs for new requests with filters (only matching specialities)
        // UEs are now general - year is handled through affectations
        $availableUEsQuery = UniteEnseignement::with(['filiere', 'departement'])
            ->where('est_vacant', true)
            ->where(function($query) use ($userSpecialites) {
                foreach ($userSpecialites as $specialite) {
                    if (!empty($specialite)) {
                        $query->orWhere('specialite', 'LIKE', '%' . $specialite . '%');
                    }
                }
                // If no specialities, show no UEs
                if (empty(array_filter($userSpecialites))) {
                    $query->where('id', 0); // No results
                }
            })
            ->whereDoesntHave('affectations', function($query) use ($user, $nextYear) {
                // Check if user already has pending/approved request for NEXT YEAR
                $query->where('user_id', $user->id)
                      ->where('annee_universitaire', $nextYear) // NEXT YEAR ONLY
                      ->whereIn('validee', ['en_attente', 'valide']);
            });

        // Apply filters if provided
        if ($request->has('filiere_filter') && $request->filiere_filter != '') {
            $availableUEsQuery->where('filiere_id', $request->filiere_filter);
        }

        if ($request->has('semestre_filter') && $request->semestre_filter != '') {
            $availableUEsQuery->where('semestre', $request->semestre_filter);
        }

        $availableUEs = $availableUEsQuery->orderBy('semestre')
            ->orderBy('code')
            ->get();

        // Get filter options
        $filieres = \App\Models\Filiere::orderBy('nom')->get();

        // Dynamic semester options based on selected filière
        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5']; // Default all semesters
        if ($request->has('filiere_filter') && $request->filiere_filter != '') {
            $selectedFiliere = \App\Models\Filiere::find($request->filiere_filter);
            if ($selectedFiliere) {
                $filiereNom = $selectedFiliere->nom;
                // Extract the level number from filière name (e.g., GI1 -> 1, TDIA2 -> 2)
                if (preg_match('/(\d)$/', $filiereNom, $matches)) {
                    $level = (int)$matches[1];
                    switch ($level) {
                        case 1:
                            $semestres = ['S1', 'S2'];
                            break;
                        case 2:
                            $semestres = ['S3', 'S4'];
                            break;
                        case 3:
                            $semestres = ['S5'];
                            break;
                        default:
                            $semestres = ['S1', 'S2', 'S3', 'S4', 'S5'];
                    }
                }
            }
        }

        // Calculate statistics
        $stats = [
            'approved' => $approvedAffectations->count(),
            'pending' => $pendingAffectations->count(),
            'rejected' => $rejectedAffectations->count(),
            'total_hours_approved' => $approvedAffectations->sum(function($affectation) {
                return $affectation->uniteEnseignement->total_hours;
            }),
            'available_ues' => $availableUEs->count()
        ];

        return view('enseignant.ues.index', compact(
            'approvedAffectations',
            'pendingAffectations',
            'rejectedAffectations',
            'availableUEs',
            'stats',
            'currentYear',
            'nextYear',
            'filieres',
            'semestres'
        ));
    }

    // Display UE status - assigned UEs with detailed information
    public function status(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Get all approved affectations with detailed UE information
        $assignedUEs = Affectation::with([
            'uniteEnseignement.filiere.departement',
            'uniteEnseignement.departement',
            'uniteEnseignement.responsable'
        ])
            ->where('user_id', $user->id)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $currentYear)
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by teaching type for better organization
        $uesByType = [
            'CM' => $assignedUEs->where('type_seance', 'CM'),
            'TD' => $assignedUEs->where('type_seance', 'TD'),
            'TP' => $assignedUEs->where('type_seance', 'TP')
        ];

        // Calculate detailed statistics
        $stats = [
            'total_ues' => $assignedUEs->count(),
            'total_hours' => $assignedUEs->sum(function($affectation) {
                $ue = $affectation->uniteEnseignement;
                switch($affectation->type_seance) {
                    case 'CM': return $ue->heures_cm;
                    case 'TD': return $ue->heures_td;
                    case 'TP': return $ue->heures_tp;
                    default: return 0;
                }
            }),
            'by_type' => [
                'CM' => [
                    'count' => $uesByType['CM']->count(),
                    'hours' => $uesByType['CM']->sum(fn($a) => $a->uniteEnseignement->heures_cm)
                ],
                'TD' => [
                    'count' => $uesByType['TD']->count(),
                    'hours' => $uesByType['TD']->sum(fn($a) => $a->uniteEnseignement->heures_td)
                ],
                'TP' => [
                    'count' => $uesByType['TP']->count(),
                    'hours' => $uesByType['TP']->sum(fn($a) => $a->uniteEnseignement->heures_tp)
                ]
            ],
            'by_semester' => $assignedUEs->groupBy(fn($a) => $a->uniteEnseignement->semestre)->map->count(),
            'by_filiere' => $assignedUEs->groupBy(fn($a) => $a->uniteEnseignement->filiere->nom ?? 'Non assignée')->map->count()
        ];

        return view('enseignant.ues.status', compact(
            'assignedUEs',
            'uesByType',
            'stats',
            'currentYear'
        ));
    }

    // Request affectation for a UE
    public function requestAffectation(Request $request)
    {
        $validated = $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'type_seance' => 'required|array|min:1',
            'type_seance.*' => 'in:CM,TD,TP',
            'message' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2); // Next academic year

        // Check if user already has a request for this UE for next year
        $existingRequest = Affectation::where('user_id', $user->id)
            ->where('ue_id', $validated['ue_id'])
            ->where('annee_universitaire', $nextYear)
            ->whereIn('validee', ['en_attente', 'valide'])
            ->first();

        if ($existingRequest) {
            return redirect()->route('enseignant.ues.index')
                           ->with('error', 'Vous avez déjà une demande pour cette UE.');
        }

        // Check if UE is still vacant
        $ue = UniteEnseignement::findOrFail($validated['ue_id']);
        if (!$ue->est_vacant) {
            return redirect()->route('enseignant.ues.index')
                           ->with('error', 'Cette UE n\'est plus vacante.');
        }

        // Create affectation request for next year with multiple type seances
        $typeSeanceString = implode(',', $validated['type_seance']);

        $affectation = Affectation::create([
            'user_id' => $user->id,
            'ue_id' => $validated['ue_id'],
            'type_seance' => $typeSeanceString,
            'validee' => 'en_attente',
            'annee_universitaire' => $nextYear
        ]);

        // Log the UE request activity
        \App\Models\Activity::log(
            'create',
            'ue_request_enseignant',
            "Demande d'UE par enseignant: {$user->name} - {$ue->code} ({$typeSeanceString}) pour {$nextYear}",
            $affectation,
            [
                'teacher_name' => $user->name,
                'teacher_email' => $user->email,
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'type_seance' => $typeSeanceString,
                'annee_universitaire' => $nextYear,
                'department' => $ue->departement->nom ?? 'N/A',
                'filiere' => $ue->filiere->nom ?? 'N/A',
                'message' => $validated['message'] ?? null
            ]
        );

        // Create notification for department head and admin
        $this->notifyAffectationRequest($ue, $user, $typeSeanceString);

        return redirect()->route('enseignant.ues.index')
                        ->with('success', 'Demande d\'affectation pour l\'année prochaine (' . $nextYear . ') envoyée avec succès.');
    }

    // Cancel pending affectation request
    public function cancelRequest(Affectation $affectation)
    {
        $user = Auth::user();

        // Check if the affectation belongs to the current user
        if ($affectation->user_id !== $user->id) {
            return redirect()->route('enseignant.ues.index')
                           ->with('error', 'Vous n\'êtes pas autorisé à annuler cette demande.');
        }

        // Check if the affectation is still pending
        if ($affectation->validee !== 'en_attente') {
            return redirect()->route('enseignant.ues.index')
                           ->with('error', 'Seules les demandes en attente peuvent être annulées.');
        }

        // Mark as annulled instead of deleting (for historique)
        $affectation->update([
            'validee' => 'annule',
            'date_validation' => now(),
            'commentaire' => 'Annulée par l\'enseignant'
        ]);

        // Log the cancellation activity
        \App\Models\Activity::log(
            'update',
            'ue_request_cancelled_enseignant',
            "Annulation de demande d'UE: {$user->name} - {$affectation->uniteEnseignement->code} ({$affectation->type_seance})",
            $affectation,
            [
                'teacher_name' => $user->name,
                'teacher_email' => $user->email,
                'ue_code' => $affectation->uniteEnseignement->code,
                'ue_nom' => $affectation->uniteEnseignement->nom,
                'type_seance' => $affectation->type_seance,
                'annee_universitaire' => $affectation->annee_universitaire,
                'cancelled_by' => $user->name
            ]
        );

        return redirect()->route('enseignant.ues.index')
                        ->with('success', 'Demande d\'affectation annulée avec succès.');
    }

    // Get UE details for modal
    public function getUEDetails(UniteEnseignement $ue)
    {
        $ue->load(['filiere', 'departement', 'responsable']);

        return response()->json([
            'id' => $ue->id,
            'code' => $ue->code,
            'nom' => $ue->nom,
            'semestre' => $ue->semestre,
            'niveau' => $ue->niveau,
            'heures_cm' => $ue->heures_cm,
            'heures_td' => $ue->heures_td,
            'heures_tp' => $ue->heures_tp,
            'total_hours' => $ue->total_hours,
            'groupes_td' => $ue->groupes_td,
            'groupes_tp' => $ue->groupes_tp,
            'filiere' => $ue->filiere ? $ue->filiere->nom : 'Non assignée',
            'departement' => $ue->departement ? $ue->departement->nom : 'Non assigné',
            'responsable' => $ue->responsable ? $ue->responsable->name : 'Non assigné',
            'annee_universitaire' => $ue->annee_universitaire
        ]);
    }

    // Get user's affectation statistics
    public function getStats()
    {
        $user = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $stats = [
            'approved' => Affectation::where('user_id', $user->id)
                                   ->where('validee', 'valide')
                                   ->where('annee_universitaire', $currentYear)
                                   ->count(),
            'pending' => Affectation::where('user_id', $user->id)
                                  ->where('validee', 'en_attente')
                                  ->where('annee_universitaire', $currentYear)
                                  ->count(),
            'rejected' => Affectation::where('user_id', $user->id)
                                   ->where('validee', 'rejete')
                                   ->where('annee_universitaire', $currentYear)
                                   ->count(),
            'total_hours' => Affectation::with('uniteEnseignement')
                                      ->where('user_id', $user->id)
                                      ->where('validee', 'valide')
                                      ->where('annee_universitaire', $currentYear)
                                      ->get()
                                      ->sum(function($affectation) {
                                          return $affectation->uniteEnseignement->total_hours;
                                      }),
            'by_type' => [
                'CM' => Affectation::where('user_id', $user->id)
                                 ->where('validee', 'valide')
                                 ->where('type_seance', 'CM')
                                 ->where('annee_universitaire', $currentYear)
                                 ->count(),
                'TD' => Affectation::where('user_id', $user->id)
                                 ->where('validee', 'valide')
                                 ->where('type_seance', 'TD')
                                 ->where('annee_universitaire', $currentYear)
                                 ->count(),
                'TP' => Affectation::where('user_id', $user->id)
                                 ->where('validee', 'valide')
                                 ->where('type_seance', 'TP')
                                 ->where('annee_universitaire', $currentYear)
                                 ->count(),
            ]
        ];

        return response()->json($stats);
    }

    // UE Status view - shows current year teaching and next year approved
    public function ueStatus()
    {
        $user = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        // Current year teaching assignments (for emploi du temps)
        $currentYearUEs = Affectation::with(['uniteEnseignement.filiere', 'uniteEnseignement.departement'])
            ->where('user_id', $user->id)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $currentYear)
            ->orderBy('created_at', 'desc')
            ->get();

        // Next year approved assignments (future teaching)
        $nextYearUEs = Affectation::with(['uniteEnseignement.filiere', 'uniteEnseignement.departement'])
            ->where('user_id', $user->id)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $nextYear)
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'current_year_count' => $currentYearUEs->count(),
            'next_year_count' => $nextYearUEs->count(),
            'current_year_hours' => $currentYearUEs->sum(function($affectation) {
                return $affectation->uniteEnseignement->total_hours ?? 0;
            }),
            'next_year_hours' => $nextYearUEs->sum(function($affectation) {
                return $affectation->uniteEnseignement->total_hours ?? 0;
            })
        ];

        return view('enseignant.ue-status', compact(
            'currentYearUEs',
            'nextYearUEs',
            'stats',
            'currentYear',
            'nextYear'
        ));
    }

    // Private method to notify about affectation request
    private function notifyAffectationRequest($ue, $user, $typeSeance)
    {
        // Notify the chef de département responsible for this UE's department
        $departmentHead = \App\Models\User::where('role', 'chef')
            ->where('departement_id', $ue->departement_id)
            ->first();

        if ($departmentHead) {
            \App\Models\Notification::create([
                'user_id' => $departmentHead->id,
                'title' => 'Nouvelle demande d\'affectation pour l\'année prochaine',
                'message' => "Demande d'affectation de {$user->name} pour l'UE {$ue->code} ({$typeSeance}) du département {$ue->departement->nom} - Année " . ((date('Y') + 1) . '-' . (date('Y') + 2)),
                'is_read' => false,
                'created_at' => now()
            ]);
        } else {
            // If no chef found for the department, notify all chefs
            $allChefs = \App\Models\User::where('role', 'chef')->get();
            foreach ($allChefs as $chef) {
                \App\Models\Notification::create([
                    'user_id' => $chef->id,
                    'title' => 'Nouvelle demande d\'affectation pour l\'année prochaine',
                    'message' => "Demande d'affectation de {$user->name} pour l'UE {$ue->code} ({$typeSeance}) du département {$ue->departement->nom} - Année " . ((date('Y') + 1) . '-' . (date('Y') + 2)),
                    'is_read' => false,
                    'created_at' => now()
                ]);
            }
        }

        // Notify admins
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id' => $admin->id,
                'title' => 'Nouvelle demande d\'affectation pour l\'année prochaine',
                'message' => "Demande d'affectation de {$user->name} pour l'UE {$ue->code} ({$typeSeance}) du département {$ue->departement->nom} - Année " . ((date('Y') + 1) . '-' . (date('Y') + 2)),
                'is_read' => false,
                'created_at' => now()
            ]);
        }
    }
}
