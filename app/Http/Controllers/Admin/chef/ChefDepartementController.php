<?php

namespace App\Http\Controllers\Admin\chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use App\Models\Departement;
use App\Models\Filiere;
use App\Models\HistoriqueAffectation;
use App\Models\ChargeHoraire;
use App\Models\Notification;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ChefDepartementController extends Controller
{
    // Constructor removed - middleware handled in routes

    // Dashboard principal
    public function dashboard()
    {
        $chef = Auth::user();
        $departement = $chef->departement;
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Statistiques générales
        $stats = [
            'total_ues' => UniteEnseignement::where('departement_id', $departement->id)->count(),
            'ues_vacantes' => UniteEnseignement::where('departement_id', $departement->id)
                ->where('est_vacant', true)->count(),
            'total_enseignants' => User::where('departement_id', $departement->id)
                ->whereIn('role', ['enseignant', 'chef'])->count(),
            'affectations_en_attente' => Affectation::whereHas('uniteEnseignement', function ($query) use ($departement) {
                $query->where('departement_id', $departement->id);
            })->where('validee', 'en_attente')->count(),
        ];

        // UEs récemment ajoutées
        $recentUes = UniteEnseignement::where('departement_id', $departement->id)
            ->with(['filiere'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Enseignants avec charge horaire insuffisante
        $enseignantsChargeInsuffisante = $this->getEnseignantsChargeInsuffisante($departement->id);

        // Affectations récentes en attente
        $affectationsEnAttente = Affectation::whereHas('uniteEnseignement', function ($query) use ($departement) {
            $query->where('departement_id', $departement->id);
        })
            ->where('validee', 'en_attente')
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Notifications non lues
        $notifications = collect(); // Default empty collection
        try {
            $notifications = Notification::where('user_id', $chef->id)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            // Handle if notifications table doesn't exist or other errors
            $notifications = collect();
        }

        return view('chef.dashboard', compact(
            'stats',
            'recentUes',
            'enseignantsChargeInsuffisante',
            'affectationsEnAttente',
            'notifications',
            'departement'
        ));
    }

    // Gestion des UEs du département
    public function unitesEnseignement(Request $request)
    {
        $chef = Auth::user();
        $departement = $chef->departement;
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $query = UniteEnseignement::where('departement_id', $departement->id)
            ->with(['filiere', 'responsable', 'affectations' => function ($q) use ($currentYear) {
                $q->where('annee_universitaire', $currentYear)
                    ->where('validee', 'valide');
            }]);

        // Handle view mode filter (affected vs vacant)
        $viewMode = $request->get('view_mode', 'vacant');
        if ($viewMode === 'vacant') {
            // Show only UEs that are NOT affected for current year
            $query->whereDoesntHave('affectations', function ($q) use ($currentYear) {
                $q->where('annee_universitaire', $currentYear)
                    ->where('validee', 'valide');
            });
        } elseif ($viewMode === 'affected') {
            // Show only UEs that ARE affected for current year
            $query->whereHas('affectations', function ($q) use ($currentYear) {
                $q->where('annee_universitaire', $currentYear)
                    ->where('validee', 'valide');
            });
        }

        // Other filters
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $unites = $query->orderBy('code')->paginate(15);

        // Get demands for next year
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        // Get ACTIVE demands (pending only) for chef to approve/reject
        $demandes = Affectation::whereHas('uniteEnseignement', function ($query) use ($departement) {
            $query->where('departement_id', $departement->id);
        })
            ->where('annee_universitaire', $nextYear)
            ->where('validee', 'en_attente') // ONLY PENDING DEMANDS
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get HISTORIQUE demands (rejected/approved/annulled) for reference
        $historiquedemandes = Affectation::whereHas('uniteEnseignement', function ($query) use ($departement) {
            $query->where('departement_id', $departement->id);
        })
            ->where('annee_universitaire', $nextYear)
            ->whereIn('validee', ['rejete', 'valide', 'annule']) // PROCESSED DEMANDS
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('updated_at', 'desc')
            ->get();

        $demandesCount = $demandes->count(); // Only pending count

        // Données pour les filtres
        $filieres = Filiere::where('departement_id', $departement->id)->get();
        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];

        return view('chef.unites-enseignement', compact('unites', 'filieres', 'semestres', 'demandes', 'demandesCount', 'historiquedemandes', 'currentYear', 'viewMode'));
    }

    // Show UE details page
    public function showUeDetails($id)
    {
        $chef = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->with(['filiere', 'responsable', 'affectations' => function ($query) use ($currentYear) {
                $query->with('user');
            }])
            ->firstOrFail();

        // Get current year affectations specifically
        $currentYearAffectations = $ue->affectations->where('annee_universitaire', $currentYear)
            ->where('validee', 'valide');

        // Check which types are already affected for current year
        $affectedTypes = $currentYearAffectations->pluck('type_seance')->unique()->toArray();

        // Determine if UE is fully affected (all available types are covered)
        $availableTypes = [];
        if ($ue->heures_cm > 0) $availableTypes[] = 'CM';
        if ($ue->heures_td > 0) $availableTypes[] = 'TD';
        if ($ue->heures_tp > 0) $availableTypes[] = 'TP';

        $isFullyAffected = !empty($availableTypes) && empty(array_diff($availableTypes, $affectedTypes));

        return view('chef.ue-details', compact('ue', 'currentYear', 'currentYearAffectations', 'affectedTypes', 'isFullyAffected', 'availableTypes'));
    }

    // View next year UEs for demand management
    public function nextYearUEs(Request $request)
    {
        $chef = Auth::user();
        $departement = $chef->departement;
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        $query = UniteEnseignement::where('departement_id', $departement->id)
            ->with(['filiere', 'responsable', 'affectations' => function ($q) use ($nextYear) {
                $q->where('annee_universitaire', $nextYear);
            }]);

        // Other filters
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $unites = $query->orderBy('code')->paginate(15);

        // Get demands for next year
        $demandes = Affectation::whereHas('uniteEnseignement', function ($query) use ($departement) {
            $query->where('departement_id', $departement->id);
        })
            ->where('validee', 'en_attente')
            ->where('annee_universitaire', $nextYear)
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Données pour les filtres
        $filieres = Filiere::where('departement_id', $departement->id)->get();
        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];

        return view('chef.next-year-ues', compact('unites', 'filieres', 'semestres', 'demandes', 'nextYear'));
    }

    // Update vacataire availability for UE
    public function updateVacataireAvailability(Request $request, $id)
    {
        $request->validate([
            'vacataire_types' => 'nullable|array',
            'vacataire_types.*' => 'in:CM,TD,TP'
        ]);

        $chef = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Get UE and verify it belongs to chef's department
        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->with(['affectations' => function ($query) use ($currentYear) {
                $query->where('annee_universitaire', $currentYear)
                    ->where('validee', 'valide');
            }])
            ->firstOrFail();

        // Get currently affected types for this year
        $affectedTypes = $ue->affectations->pluck('type_seance')->unique()->toArray();

        // Filter out any requested types that are already affected
        $requestedTypes = $request->vacataire_types ?? [];
        $allowedTypes = array_diff($requestedTypes, $affectedTypes);

        // Check if user tried to select already affected types
        $blockedTypes = array_intersect($requestedTypes, $affectedTypes);

        if (!empty($blockedTypes)) {
            return back()->with('error', 'Impossible de sélectionner les types déjà affectés: ' . implode(', ', $blockedTypes));
        }

        // Update vacataire types with only allowed types
        $ue->update([
            'vacataire_types' => $allowedTypes
        ]);

        $message = 'Disponibilité pour vacataires mise à jour avec succès.';
        if (count($allowedTypes) < count($requestedTypes)) {
            $message .= ' Certains types ont été ignorés car ils sont déjà affectés.';
        }

        return back()->with('success', $message);
    }

    // Show UE edit form
    public function editUE($id)
    {
        $chef = Auth::user();

        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->with(['filiere', 'departement', 'responsable'])
            ->firstOrFail();

        // Get filieres from chef's department
        $filieres = Filiere::where('departement_id', $chef->departement_id)
            ->orderBy('nom')
            ->get();

        // Get departements (chef can only edit UEs in their department)
        $departements = Departement::where('id', $chef->departement_id)->get();

        // Get potential responsables (enseignants from the department)
        $responsables = User::where('departement_id', $chef->departement_id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->orderBy('name')
            ->get();

        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5']; // No S6
        $specialites = ['Informatique', 'Mathématiques', 'Génie Civil', 'Énergétique', 'Mécanique', 'Électronique'];

        return view('chef.ue-edit', compact(
            'ue',
            'filieres',
            'departements',
            'responsables',
            'semestres',
            'specialites'
        ));
    }

    // Update UE
    public function updateUE(Request $request, $id)
    {
        $chef = Auth::user();

        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->firstOrFail();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('unites_enseignement')->ignore($ue->id)],
            'nom' => 'required|string|max:255',
            'heures_cm' => 'required|integer|min:0|max:100',
            'heures_td' => 'required|integer|min:0|max:100',
            'heures_tp' => 'required|integer|min:0|max:100',
            'semestre' => 'required|in:S1,S2,S3,S4,S5', // No S6
            'annee_universitaire' => 'required|string|max:9',
            'groupes_td' => 'required|integer|min:0|max:20',
            'groupes_tp' => 'required|integer|min:0|max:20',
            'filiere_id' => 'required|exists:filieres,id',
            'responsable_id' => 'nullable|exists:users,id',
            'specialite' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'est_vacant' => 'boolean',
            'vacataire_types' => 'nullable|array',
            'vacataire_types.*' => 'in:CM,TD,TP'
        ]);

        // Ensure at least some hours are specified
        if ($validated['heures_cm'] + $validated['heures_td'] + $validated['heures_tp'] == 0) {
            return back()->withErrors(['heures' => 'Au moins un type d\'heures (CM, TD, TP) doit être spécifié.'])
                ->withInput();
        }

        // Verify filiere belongs to chef's department
        $filiere = Filiere::findOrFail($validated['filiere_id']);
        if ($filiere->departement_id !== $chef->departement_id) {
            return back()->withErrors(['filiere_id' => 'Cette filière n\'appartient pas à votre département.'])
                ->withInput();
        }

        // Set department to chef's department
        $validated['departement_id'] = $chef->departement_id;
        $validated['est_vacant'] = $request->has('est_vacant');
        $validated['vacataire_types'] = $request->input('vacataire_types', []);

        $ue->update($validated);

        return redirect()->route('chef.unites-enseignement')
            ->with('success', 'Unité d\'enseignement mise à jour avec succès');
    }

    // Show affectation form page
    public function showAffectationForm($id)
    {
        $chef = Auth::user();
        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->with(['filiere'])
            ->firstOrFail();

        // Get available enseignants
        $enseignants = User::where('departement_id', $chef->departement_id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->orderBy('name')
            ->get();

        // Calculate charge horaire for each enseignant
        foreach ($enseignants as $enseignant) {
            $enseignant->charge_horaire = $this->calculateChargeHoraire($enseignant->id);
        }

        return view('chef.ue-affecter', compact('ue', 'enseignants'));
    }



    // Gestion des enseignants du département
    public function enseignants(Request $request)
    {
        $chef = Auth::user();
        $departement = $chef->departement;

        $query = User::where('departement_id', $departement->id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->with(['affectations.uniteEnseignement']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('specialite', 'like', "%{$search}%");
            });
        }

        $enseignants = $query->orderBy('name')->paginate(15);

        // Calculer la charge horaire pour chaque enseignant
        foreach ($enseignants as $enseignant) {
            $enseignant->charge_horaire = $this->calculateChargeHoraire($enseignant->id);
        }

        // Trier par charge horaire (insuffisante en premier)
        $enseignants->getCollection()->transform(function ($enseignant) {
            return $enseignant;
        })->sortBy(function ($enseignant) {
            return $enseignant->charge_horaire['total'] < 192 ? 0 : 1;
        });

        return view('chef.enseignants', compact('enseignants'));
    }

    /* Removed duplicate getEnseignantsList method - using the one at line 611 */

    // Gestion des affectations
    public function affectations(Request $request)
    {
        $chef = Auth::user();

        // Check if chef has a department assigned
        if (!$chef->departement_id) {
            return redirect()->route('chef.dashboard')->with('error', 'Aucun département assigné. Contactez l\'administrateur.');
        }

        // Get the department object for later use
        $departement = \App\Models\Departement::find($chef->departement_id);

        if (!$departement) {
            return redirect()->route('chef.dashboard')->with('error', 'Département introuvable. Contactez l\'administrateur.');
        }

        $query = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->with(['user', 'uniteEnseignement.filiere']);

        // Filtres
        if ($request->filled('statut')) {
            $query->where('validee', $request->statut);
        }

        if ($request->filled('annee')) {
            $query->where('annee_universitaire', $request->annee);
        }

        if ($request->filled('enseignant_id')) {
            $query->where('user_id', $request->enseignant_id);
        }

        $affectations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate charge horaire for each user in affectations
        foreach ($affectations as $affectation) {
            if ($affectation->user) {
                $affectation->user->charge_horaire = $this->calculateChargeHoraire($affectation->user->id);
            }
        }

        // Données pour les filtres
        $enseignants = User::where('departement_id', $chef->departement_id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->select('id', 'name', 'email', 'specialite')
            ->orderBy('name')
            ->get();

        // Calculate charge horaire for each enseignant
        foreach ($enseignants as $enseignant) {
            $enseignant->charge_horaire = $this->calculateChargeHoraire($enseignant->id);
        }

        // Calculate statistics
        $pending = $query->clone()->where('validee', 'en_attente')->count();
        $approved = $query->clone()->where('validee', 'valide')->count();
        $rejected = $query->clone()->where('validee', 'rejete')->count();

        $annees = Affectation::select('annee_universitaire')
            ->distinct()
            ->orderBy('annee_universitaire', 'desc')
            ->pluck('annee_universitaire');

        return view('chef.affectations', compact('affectations', 'enseignants', 'annees', 'pending', 'approved', 'rejected', 'departement'));
    }

    // Valider une affectation
    public function validerAffectation(Request $request, $id)
    {
        $affectation = Affectation::findOrFail($id);

        // Vérifier que l'UE appartient au département du chef
        if ($affectation->uniteEnseignement->departement_id !== Auth::user()->departement_id) {
            return back()->with('error', 'Vous ne pouvez pas valider cette affectation.');
        }

        $validated = $request->validate([
            'action' => 'required|in:valider,rejeter',
            'commentaire' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($affectation, $validated) {
            if ($validated['action'] === 'valider') {
                $affectation->update(['validee' => 'valide']);

                // Marquer l'UE comme non vacante
                $affectation->uniteEnseignement->update(['est_vacant' => false]);

                // Créer une entrée dans l'historique
                HistoriqueAffectation::create([
                    'ue_id' => $affectation->ue_id,
                    'user_id' => $affectation->user_id,
                    'annee_universitaire' => $affectation->annee_universitaire
                ]);

                // Log the affectation approval activity
                \App\Models\Activity::log(
                    'approve',
                    'affectation_approved_chef',
                    "Affectation approuvée par chef: {$affectation->user->name} - {$affectation->uniteEnseignement->code} ({$affectation->type_seance})",
                    $affectation,
                    [
                        'teacher_name' => $affectation->user->name,
                        'ue_code' => $affectation->uniteEnseignement->code,
                        'ue_nom' => $affectation->uniteEnseignement->nom,
                        'type_seance' => $affectation->type_seance,
                        'annee_universitaire' => $affectation->annee_universitaire,
                        'approved_by' => auth()->user()->name,
                        'chef_department' => auth()->user()->departement->nom ?? 'N/A',
                        'commentaire' => $validated['commentaire'] ?? null
                    ]
                );

                $message = 'Affectation validée avec succès.';
            } else {
                $affectation->update(['validee' => 'rejete']);

                // Log the affectation rejection activity
                \App\Models\Activity::log(
                    'reject',
                    'affectation_rejected_chef',
                    "Affectation rejetée par chef: {$affectation->user->name} - {$affectation->uniteEnseignement->code} ({$affectation->type_seance})",
                    $affectation,
                    [
                        'teacher_name' => $affectation->user->name,
                        'ue_code' => $affectation->uniteEnseignement->code,
                        'ue_nom' => $affectation->uniteEnseignement->nom,
                        'type_seance' => $affectation->type_seance,
                        'annee_universitaire' => $affectation->annee_universitaire,
                        'rejected_by' => auth()->user()->name,
                        'chef_department' => auth()->user()->departement->nom ?? 'N/A',
                        'commentaire' => $validated['commentaire'] ?? null
                    ]
                );

                $message = 'Affectation rejetée.';
            }

            // Créer une notification pour l'enseignant
            Notification::create([
                'user_id' => $affectation->user_id,
                'title' => $validated['action'] === 'valider' ? 'Affectation validée' : 'Affectation rejetée',
                'message' => $validated['action'] === 'valider'
                    ? "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} a été validée."
                    : "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} a été rejetée.",
                'is_read' => false
            ]);
        });

        return back()->with('success', $message);
    }

    // Affecter manuellement une UE à un enseignant
    public function affecterUE(Request $request)
    {
        $validated = $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'user_id' => 'required|exists:users,id',
            'type_seance' => 'required|in:CM,TD,TP',
            'annee_universitaire' => 'required|string'
        ]);

        $chef = Auth::user();
        $ue = UniteEnseignement::findOrFail($validated['ue_id']);

        // Vérifier que l'UE appartient au département du chef
        if ($ue->departement_id !== $chef->departement_id) {
            return back()->with('error', 'Vous ne pouvez pas affecter cette UE.');
        }

        // Vérifier que l'enseignant appartient au département
        $enseignant = User::findOrFail($validated['user_id']);
        if ($enseignant->departement_id !== $chef->departement_id) {
            return back()->with('error', 'Cet enseignant n\'appartient pas à votre département.');
        }

        // Créer l'affectation
        $affectation = Affectation::create([
            'ue_id' => $validated['ue_id'],
            'user_id' => $validated['user_id'],
            'type_seance' => $validated['type_seance'],
            'annee_universitaire' => $validated['annee_universitaire'],
            'validee' => 'valide',
            'validee_par' => $chef->id,
            'date_validation' => now()
        ]);

        // Log the manual assignment activity
        \App\Models\Activity::log(
            'create',
            'manual_affectation_chef',
            "Affectation manuelle par chef: {$enseignant->name} - {$ue->code} ({$validated['type_seance']})",
            $affectation,
            [
                'teacher_name' => $enseignant->name,
                'teacher_email' => $enseignant->email,
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'type_seance' => $validated['type_seance'],
                'annee_universitaire' => $validated['annee_universitaire'],
                'assigned_by' => $chef->name,
                'chef_department' => $chef->departement->nom ?? 'N/A'
            ]
        );

        // Marquer l'UE comme non vacante
        $ue->update(['est_vacant' => false]);

        // Créer une notification pour l'enseignant
        Notification::create([
            'user_id' => $validated['user_id'],
            'title' => 'Nouvelle affectation',
            'message' => "Vous avez été affecté à l'UE {$ue->code} - {$ue->nom}",
            'is_read' => false
        ]);

        return back()->with('success', 'UE affectée avec succès.');
    }

    // Calculer la charge horaire d'un enseignant
    private function calculateChargeHoraire($userId, $year = null)
    {
        $year = $year ?: (date('Y') . '-' . (date('Y') + 1));
        $affectations = Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $year)
            ->with('uniteEnseignement')
            ->get();
        $charge = [
            'CM' => 0,
            'TD' => 0,
            'TP' => 0,
            'total' => 0
        ];
        foreach ($affectations as $affectation) {
            $ue = $affectation->uniteEnseignement;
            switch ($affectation->type_seance) {
                case 'CM':
                    $charge['CM'] += $ue->heures_cm;
                    break;
                case 'TD':
                    $charge['TD'] += $ue->heures_td;
                    break;
                case 'TP':
                    $charge['TP'] += $ue->heures_tp;
                    break;
            }
        }
        $charge['total'] = $charge['CM'] + $charge['TD'] + $charge['TP'];
        return $charge;
    }

    private function calculateAllChargesHoraires($departementId, $year)
    {
        $enseignants = User::where('departement_id', $departementId)
            ->whereIn('role', ['enseignant', 'chef'])
            ->get();
        $chargesHoraires = [];
        foreach ($enseignants as $enseignant) {
            $charge = $this->calculateChargeHoraire($enseignant->id, $year);
            $chargesHoraires[] = [
                'enseignant' => $enseignant,
                'charge' => $charge,
                'status' => $charge['total'] < 192 ? 'insuffisant' : ($charge['total'] > 240 ? 'excessif' : 'normal')
            ];
        }
        usort($chargesHoraires, function ($a, $b) {
            return $a['charge']['total'] <=> $b['charge']['total'];
        });
        return $chargesHoraires;
    }

    // Obtenir les enseignants avec charge horaire insuffisante
    private function getEnseignantsChargeInsuffisante($departementId)
    {
        $enseignants = User::where('departement_id', $departementId)
            ->whereIn('role', ['enseignant', 'chef'])
            ->get();

        $enseignantsInsuffisants = collect();

        foreach ($enseignants as $enseignant) {
            $charge = $this->calculateChargeHoraire($enseignant->id);
            if ($charge['total'] < 192) { // Charge minimale
                $enseignant->charge_horaire = $charge;
                $enseignantsInsuffisants->push($enseignant);
            }
        }

        return $enseignantsInsuffisants->sortBy('charge_horaire.total');
    }



    // Historique des affectations et demandes - FILTERED BY CHEF'S DEPARTMENT
    public function historique(Request $request)
    {
        $chef = Auth::user();

        // Check if chef has a department assigned
        if (!$chef->departement_id) {
            return redirect()->route('chef.dashboard')->with('error', 'Aucun département assigné. Contactez l\'administrateur.');
        }

        $currentYear = date('Y') . '-' . (date('Y') + 1);
        $activities = collect();

        // Get filter parameters
        $typeFilter = $request->get('type', 'all'); // all, affectations, demandes
        $anneeFilter = $request->get('annee', $currentYear);
        $enseignantFilter = $request->get('enseignant_id');
        $statutFilter = $request->get('statut'); // valide, rejete, en_attente, annule

        // 1. Get Historique Affectations (validated assignments)
        if ($typeFilter === 'all' || $typeFilter === 'affectations') {
            $historiqueQuery = HistoriqueAffectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
                $q->where('departement_id', $chef->departement_id);
            })->with(['user', 'uniteEnseignement.filiere']);

            if ($anneeFilter) {
                $historiqueQuery->where('annee_universitaire', $anneeFilter);
            }
            if ($enseignantFilter) {
                $historiqueQuery->where('user_id', $enseignantFilter);
            }

            $historique = $historiqueQuery->get();

            foreach ($historique as $item) {
                $activities->push([
                    'id' => 'hist_' . $item->id,
                    'type' => 'affectation',
                    'statut' => 'valide',
                    'title' => 'Affectation',
                    'description' => "Affectation: {$item->user->name} → {$item->uniteEnseignement->code} - {$item->uniteEnseignement->nom}",
                    'enseignant' => $item->user,
                    'ue' => $item->uniteEnseignement,
                    'annee_universitaire' => $item->annee_universitaire,
                    'date' => $item->created_at,
                    'icon' => 'fas fa-user-check',
                    'color' => 'success',
                    'details' => [
                        'action' => 'Affectation',
                        'filiere' => $item->uniteEnseignement->filiere->nom ?? 'N/A',
                        'semestre' => $item->uniteEnseignement->semestre ?? 'N/A',
                        'status_detail' => 'Validée et active'
                    ]
                ]);
            }
        }

        // 2. Get Current Affectations - will be split into "Affectation" or "Demande" based on logic
        $affectationsQuery = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->with(['user', 'uniteEnseignement.filiere']);

        if ($anneeFilter) {
            $affectationsQuery->where('annee_universitaire', $anneeFilter);
        }
        if ($enseignantFilter) {
            $affectationsQuery->where('user_id', $enseignantFilter);
        }
        if ($statutFilter) {
            $affectationsQuery->where('validee', $statutFilter);
        }

        $affectations = $affectationsQuery->get();

        foreach ($affectations as $affectation) {
            // ORIGINAL AFFECTATIONS LOGIC - Direct assignments by chef
            $isDirectAssignment = $affectation->validee === 'valide' &&
                $affectation->validee_par &&
                $affectation->date_validation;

            if ($isDirectAssignment) {
                // This is a direct assignment by chef - show as AFFECTATION
                $activities->push([
                    'id' => 'aff_' . $affectation->id,
                    'type' => 'affectation',
                    'statut' => 'valide',
                    'title' => 'Affectation',
                    'description' => "Affectation: {$affectation->user->name} → {$affectation->uniteEnseignement->code} - {$affectation->uniteEnseignement->nom}",
                    'enseignant' => $affectation->user,
                    'ue' => $affectation->uniteEnseignement,
                    'annee_universitaire' => $affectation->annee_universitaire,
                    'date' => $affectation->created_at,
                    'icon' => 'fas fa-user-check',
                    'color' => 'success',
                    'details' => [
                        'action' => 'Affectation',
                        'status_detail' => 'Affectée par chef',
                        'type_seance' => $affectation->type_seance,
                        'filiere' => $affectation->uniteEnseignement->filiere->nom ?? 'N/A',
                        'semestre' => $affectation->uniteEnseignement->semestre ?? 'N/A',
                        'commentaire' => $affectation->commentaire,
                        'date_validation' => $affectation->date_validation,
                        'assigned_by_chef' => true
                    ]
                ]);
            }
        }

        // SEPARATE LOGIC FOR DEMANDES - Get ALL affectations (status from validee column)
        $demandesQuery = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->with(['user', 'uniteEnseignement.filiere']);

        if ($anneeFilter) {
            $demandesQuery->where('annee_universitaire', $anneeFilter);
        }
        if ($enseignantFilter) {
            $demandesQuery->where('user_id', $enseignantFilter);
        }
        if ($statutFilter) {
            $demandesQuery->where('validee', $statutFilter);
        }

        $demandes = $demandesQuery->get();

        foreach ($demandes as $demande) {
            // ALL records from affectations table are shown as DEMANDES
            // Status (etat) comes from the validee column
            $etat = $demande->validee; // Use validee column as the status



            // Map the validee column values to display info
            $statusMap = [
                // Main status values found in database
                'valide' => ['status_detail' => 'Approuvée', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
                'rejete' => ['status_detail' => 'Rejetée', 'icon' => 'fas fa-times-circle', 'color' => 'danger'],
                'annule' => ['status_detail' => 'Annulée', 'icon' => 'fas fa-ban', 'color' => 'secondary'],

                // Possible additional values in validee column
                'en_attente' => ['status_detail' => 'En Attente', 'icon' => 'fas fa-clock', 'color' => 'warning'],
                'refuse' => ['status_detail' => 'Refusée', 'icon' => 'fas fa-times', 'color' => 'danger'],
                'pending' => ['status_detail' => 'En Attente', 'icon' => 'fas fa-clock', 'color' => 'warning'],
                'approved' => ['status_detail' => 'Approuvée', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
                'rejected' => ['status_detail' => 'Rejetée', 'icon' => 'fas fa-times-circle', 'color' => 'danger'],
                'cancelled' => ['status_detail' => 'Annulée', 'icon' => 'fas fa-ban', 'color' => 'secondary'],

                // Handle any other values that might exist
                null => ['status_detail' => 'Non défini', 'icon' => 'fas fa-question', 'color' => 'secondary'],
                '' => ['status_detail' => 'Non défini', 'icon' => 'fas fa-question', 'color' => 'secondary']
            ];

            $statusInfo = $statusMap[$etat] ?? ['status_detail' => ucfirst($etat), 'icon' => 'fas fa-question', 'color' => 'secondary'];

            $activities->push([
                'id' => 'dem_' . $demande->id,
                'type' => 'demande',
                'statut' => $etat, // Use validee column directly
                'title' => 'Demande',
                'description' => "Demande: {$demande->user->name} → {$demande->uniteEnseignement->code} - {$demande->uniteEnseignement->nom}",
                'enseignant' => $demande->user,
                'ue' => $demande->uniteEnseignement,
                'annee_universitaire' => $demande->annee_universitaire,
                'date' => $demande->updated_at ?? $demande->created_at,
                'icon' => $statusInfo['icon'],
                'color' => $statusInfo['color'],
                'details' => [
                    'action' => 'Demande',
                    'status_detail' => $statusInfo['status_detail'],
                    'type_seance' => $demande->type_seance,
                    'filiere' => $demande->uniteEnseignement->filiere->nom ?? 'N/A',
                    'semestre' => $demande->uniteEnseignement->semestre ?? 'N/A',
                    'commentaire' => $demande->commentaire,
                    'date_validation' => $demande->date_validation,
                    'assigned_by_chef' => false,
                    'etat_from_validee' => $etat
                ]
            ]);
        }

        // Sort activities by date (most recent first)
        $activities = $activities->sortByDesc('date');

        // Filter is now applied during collection, not here

        // Paginate manually
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $total = $activities->count();
        $activities = $activities->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Create pagination
        $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
            $activities,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get filter data
        $enseignants = User::whereHas('affectations', function ($q) use ($chef) {
            $q->whereHas('uniteEnseignement', function ($subQ) use ($chef) {
                $subQ->where('departement_id', $chef->departement_id);
            });
        })
            ->orWhereHas('historiqueAffectations', function ($q) use ($chef) {
                $q->whereHas('uniteEnseignement', function ($subQ) use ($chef) {
                    $subQ->where('departement_id', $chef->departement_id);
                });
            })
            ->whereIn('role', ['enseignant', 'chef', 'vacataire'])
            ->distinct()
            ->orderBy('name')
            ->get();

        // Get available years
        $historiqueYears = HistoriqueAffectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->distinct()->pluck('annee_universitaire');

        $affectationYears = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->distinct()->pluck('annee_universitaire');

        $annees = $historiqueYears->merge($affectationYears)->unique()->sort()->reverse()->values();

        // Get department info and stats
        $departement = $chef->departement;

        // Calculate stats with SEPARATE LOGIC
        $historicalCount = HistoriqueAffectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->count();

        // Affectations = historical + direct chef assignments
        $directAssignmentsCount = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->where('validee', 'valide')
            ->whereNotNull('validee_par')
            ->whereNotNull('date_validation')
            ->count();

        // Demandes = ALL records from affectations table (separate query)
        $allDemandes = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })->where('annee_universitaire', $currentYear);

        $stats = [
            'total_affectations' => $historicalCount + $directAssignmentsCount,
            'demandes_en_attente' => (clone $allDemandes)->whereIn('validee', ['en_attente', 'pending', null, ''])->count(),
            'demandes_approuvees' => (clone $allDemandes)->whereIn('validee', ['valide', 'approved'])->count(),
            'demandes_rejetees' => (clone $allDemandes)->whereIn('validee', ['rejete', 'annule', 'refuse', 'rejected', 'cancelled'])->count()
        ];

        // Separate into affectations and demandes
        // Affectations = only historical records
        $affectationsData = $activities->filter(function ($activity) {
            return $activity['title'] === 'Affectation';
        })->values();

        // Demandes = all records from affectations table
        $demandesData = $activities->filter(function ($activity) {
            return $activity['title'] === 'Demande';
        })->values();

        return view('chef.historique', compact('affectationsData', 'demandesData', 'enseignants', 'annees', 'departement', 'stats', 'currentYear'));
    }

    // Rapports et statistiques
    public function rapports(Request $request)
    {
        $chef = Auth::user();

        // Check if chef has a department assigned
        if (!$chef->departement_id) {
            return redirect()->route('chef.dashboard')->with('error', 'Aucun département assigné. Contactez l\'administrateur.');
        }

        $currentYear = date('Y') . '-' . (date('Y') + 1);
        $selectedYear = $request->get('annee', $currentYear);

        // Statistiques générales
        $stats = [
            'total_ues' => UniteEnseignement::where('departement_id', $chef->departement_id)->count(),
            'ues_affectees' => UniteEnseignement::where('departement_id', $chef->departement_id)
                ->where('est_vacant', false)->count(),
            'ues_vacantes' => UniteEnseignement::where('departement_id', $chef->departement_id)
                ->where('est_vacant', true)->count(),
            'total_enseignants' => User::where('departement_id', $chef->departement_id)
                ->whereIn('role', ['enseignant', 'chef'])->count(),
        ];

        // Répartition par filière
        $repartitionFilieres = DB::table('unites_enseignement as ue')
            ->join('filieres as f', 'ue.filiere_id', '=', 'f.id')
            ->where('ue.departement_id', $chef->departement_id)
            ->select(
                'f.nom as filiere',
                DB::raw('COUNT(*) as total_ues'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 0 THEN 1 ELSE 0 END) as ues_affectees'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 1 THEN 1 ELSE 0 END) as ues_vacantes')
            )
            ->groupBy('f.id', 'f.nom')
            ->get();

        // Charge horaire par enseignant
        $enseignants = User::where('departement_id', $chef->departement_id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->get();

        $chargesHoraires = [];
        foreach ($enseignants as $enseignant) {
            $charge = $this->calculateChargeHoraire($enseignant->id);
            $chargesHoraires[] = [
                'enseignant' => $enseignant,
                'charge' => $charge,
                'status' => $charge['total'] < 192 ? 'insuffisant' : ($charge['total'] > 240 ? 'excessif' : 'normal')
            ];
        }

        // Trier par charge horaire
        usort($chargesHoraires, function ($a, $b) {
            return $a['charge']['total'] <=> $b['charge']['total'];
        });

        // Évolution des affectations par mois
        $evolutionAffectations = DB::table('affectations as a')
            ->join('unites_enseignement as ue', 'a.ue_id', '=', 'ue.id')
            ->where('ue.departement_id', $chef->departement_id)
            ->where('a.annee_universitaire', $selectedYear)
            ->select(
                DB::raw('MONTH(a.created_at) as mois'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN a.validee = "valide" THEN 1 ELSE 0 END) as validees'),
                DB::raw('SUM(CASE WHEN a.validee = "en_attente" THEN 1 ELSE 0 END) as en_attente'),
                DB::raw('SUM(CASE WHEN a.validee = "rejete" THEN 1 ELSE 0 END) as rejetees')
            )
            ->groupBy(DB::raw('MONTH(a.created_at)'))
            ->orderBy('mois')
            ->get();

        $annees = Affectation::select('annee_universitaire')
            ->distinct()
            ->orderBy('annee_universitaire', 'desc')
            ->pluck('annee_universitaire');

        return view('chef.rapports', compact(
            'stats',
            'repartitionFilieres',
            'chargesHoraires',
            'evolutionAffectations',
            'annees',
            'selectedYear'
        ));
    }

    // Exporter les données
    public function exportData(Request $request)
    {
        $type = $request->get('type', 'affectations');
        $format = $request->get('format', 'excel');

        // Implementation will be added later
        return back()->with('info', 'Fonctionnalité d\'export en cours de développement.');
    }

    // Importer les données
    public function importData(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'type' => 'required|in:ues,enseignants,affectations'
        ]);

        // Implementation will be added later
        return back()->with('info', 'Fonctionnalité d\'import en cours de développement.');
    }

    // Générer la charge horaire d'un enseignant
    public function genererChargeHoraire($userId)
    {
        $enseignant = User::findOrFail($userId);

        // Vérifier que l'enseignant appartient au département
        if ($enseignant->departement_id !== Auth::user()->departement_id) {
            return back()->with('error', 'Cet enseignant n\'appartient pas à votre département.');
        }

        $charge = $this->calculateChargeHoraire($userId);
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Obtenir les affectations détaillées
        $affectations = Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', $currentYear)
            ->with('uniteEnseignement.filiere')
            ->get();

        return view('chef.charge-horaire', compact('enseignant', 'charge', 'affectations'));
    }

    // Marquer les notifications comme lues
    public function marquerNotificationsLues()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // Get enseignants list for AJAX requests
    public function getEnseignantsList()
    {
        try {
            $chef = Auth::user();

            // Check if chef has a department assigned
            if (!$chef->departement_id) {
                return response()->json(['error' => 'Aucun département assigné'], 400);
            }

            $enseignants = User::where('departement_id', $chef->departement_id)
                ->whereIn('role', ['enseignant', 'chef'])
                ->select('id', 'name', 'email', 'specialite')
                ->orderBy('name')
                ->get();

            return response()->json($enseignants);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des enseignants: ' . $e->getMessage()], 500);
        }
    }



    // Get UEs compatible with enseignant specialities
    public function getCompatibleUEs($enseignantId)
    {
        try {
            $chef = Auth::user();
            $currentYear = date('Y') . '-' . (date('Y') + 1);

            // Check if chef has a department assigned
            if (!$chef->departement_id) {
                return response()->json(['error' => 'Aucun département assigné'], 400);
            }

            // Get enseignant details
            $enseignant = User::find($enseignantId);
            if (!$enseignant || $enseignant->departement_id !== $chef->departement_id) {
                return response()->json(['error' => 'Enseignant non trouvé ou non autorisé'], 404);
            }

            // Get enseignant specialities
            $enseignantSpecialities = $enseignant->specialite ? explode(',', $enseignant->specialite) : [];
            $enseignantSpecialities = array_map('trim', $enseignantSpecialities);

            // Get available UEs (not currently assigned for current year)
            $query = UniteEnseignement::where('departement_id', $chef->departement_id)
                ->whereDoesntHave('affectations', function ($q) use ($currentYear) {
                    $q->where('annee_universitaire', $currentYear)
                        ->where('validee', 'valide');
                })
                ->with(['filiere']);

            // Filter by specialities if enseignant has any
            if (!empty($enseignantSpecialities)) {
                $query->where(function ($q) use ($enseignantSpecialities) {
                    foreach ($enseignantSpecialities as $specialite) {
                        $q->orWhere('specialite', 'LIKE', "%{$specialite}%");
                    }
                });
            }

            $ues = $query->get()->map(function ($ue) {
                return [
                    'id' => $ue->id,
                    'code' => $ue->code,
                    'nom' => $ue->nom,
                    'specialite' => $ue->specialite,
                    'filiere_nom' => $ue->filiere->nom ?? 'N/A',
                    'total_hours' => $ue->total_hours ?? 0,
                    'type' => 'CM' // Default type
                ];
            });

            return response()->json($ues);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des UEs: ' . $e->getMessage()], 500);
        }
    }

    // Save drag and drop assignments
    public function saveDragDropAssignments(Request $request)
    {
        try {
            $chef = Auth::user();

            // Check if chef has a department assigned
            if (!$chef->departement_id) {
                return response()->json(['error' => 'Aucun département assigné'], 400);
            }

            // Validate request
            $request->validate([
                'enseignant_id' => 'required|exists:users,id',
                'ues' => 'required|array|min:1',
                'ues.*.ue_id' => 'required|exists:unites_enseignement,id',
                'ues.*.type_seance' => 'required|in:CM,TD,TP'
            ]);

            $enseignantId = $request->input('enseignant_id');
            $ues = $request->input('ues');

            // Verify enseignant belongs to chef's department
            $enseignant = User::find($enseignantId);
            if (!$enseignant || $enseignant->departement_id !== $chef->departement_id) {
                return response()->json(['error' => 'Enseignant non autorisé'], 403);
            }

            $createdCount = 0;
            $errors = [];
            $currentYear = date('Y') . '-' . (date('Y') + 1);

            foreach ($ues as $ueData) {
                try {
                    // Verify UE belongs to chef's department
                    $ue = UniteEnseignement::find($ueData['ue_id']);
                    if (!$ue || $ue->departement_id !== $chef->departement_id) {
                        $errors[] = "UE {$ueData['ue_id']} non autorisée";
                        continue;
                    }

                    // Check if affectation already exists
                    $existingAffectation = Affectation::where([
                        'user_id' => $enseignantId,
                        'ue_id' => $ueData['ue_id'],
                        'annee_universitaire' => $currentYear
                    ])->first();

                    if ($existingAffectation) {
                        $errors[] = "Affectation déjà existante pour UE {$ue->code}";
                        continue;
                    }

                    // Create new affectation
                    $affectation = Affectation::create([
                        'user_id' => $enseignantId,
                        'ue_id' => $ueData['ue_id'],
                        'type_seance' => $ueData['type_seance'],
                        'annee_universitaire' => $currentYear,
                        'validee' => 'valide', // Auto-approve chef assignments
                        'date_validation' => now(),
                        'validee_par' => $chef->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Log the drag-and-drop assignment activity
                    \App\Models\Activity::log(
                        'create',
                        'drag_drop_affectation_chef',
                        "Affectation par glisser-déposer: {$enseignant->name} - {$ue->code} ({$ueData['type_seance']})",
                        $affectation,
                        [
                            'teacher_name' => $enseignant->name,
                            'teacher_email' => $enseignant->email,
                            'ue_code' => $ue->code,
                            'ue_nom' => $ue->nom,
                            'type_seance' => $ueData['type_seance'],
                            'annee_universitaire' => $currentYear,
                            'assigned_by' => $chef->name,
                            'chef_department' => $chef->departement->nom ?? 'N/A',
                            'method' => 'drag_and_drop'
                        ]
                    );

                    // Mark UE as not vacant
                    $ue->update(['est_vacant' => false]);

                    $createdCount++;
                } catch (\Exception $e) {
                    $errors[] = "Erreur pour UE {$ueData['ue_id']}: " . $e->getMessage();
                }
            }

            // Prepare response
            $response = [
                'success' => $createdCount > 0,
                'created_count' => $createdCount,
                'total_requested' => count($ues)
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                $response['message'] = "Certaines affectations n'ont pas pu être créées";
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get UE details for AJAX requests
    public function getUeDetails($id)
    {
        try {
            $chef = Auth::user();
            $ue = UniteEnseignement::where('id', $id)
                ->where('departement_id', $chef->departement_id)
                ->with(['filiere', 'responsable', 'affectations.user'])
                ->first();

            if (!$ue) {
                return response()->json(['error' => 'UE not found'], 404);
            }

            return response()->json([
                'id' => $ue->id,
                'code' => $ue->code,
                'nom' => $ue->nom,
                'semestre' => $ue->semestre,
                'specialite' => $ue->specialite,
                'est_vacant' => $ue->est_vacant,
                'heures_cm' => $ue->heures_cm,
                'heures_td' => $ue->heures_td,
                'heures_tp' => $ue->heures_tp,
                'total_hours' => ($ue->heures_cm ?? 0) + ($ue->heures_td ?? 0) + ($ue->heures_tp ?? 0),
                'filiere' => $ue->filiere ? [
                    'id' => $ue->filiere->id,
                    'nom' => $ue->filiere->nom
                ] : null,
                'affectations' => $ue->affectations->map(function ($affectation) {
                    return [
                        'id' => $affectation->id,
                        'type_seance' => $affectation->type_seance,
                        'validee' => $affectation->validee,
                        'annee_universitaire' => $affectation->annee_universitaire,
                        'user' => $affectation->user ? [
                            'id' => $affectation->user->id,
                            'name' => $affectation->user->name,
                            'email' => $affectation->user->email
                        ] : null
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement des détails: ' . $e->getMessage()], 500);
        }
    }

    // Get enseignant affectations for AJAX requests
    public function getEnseignantAffectations($id)
    {
        $chef = Auth::user();
        $enseignant = User::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->first();

        if (!$enseignant) {
            return response()->json(['error' => 'Enseignant not found'], 404);
        }

        $affectations = Affectation::where('user_id', $id)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere'])
            ->get();

        $charge = $this->calculateChargeHoraire($id);

        return response()->json([
            'enseignant' => $enseignant,
            'affectations' => $affectations,
            'charge' => $charge
        ]);
    }

    // Get real-time statistics for AJAX requests
    public function getStatistics()
    {
        $chef = Auth::user();

        // Check if chef has a department assigned
        if (!$chef->departement_id) {
            return response()->json(['error' => 'Aucun département assigné'], 400);
        }

        $stats = [
            'total_ues' => UniteEnseignement::where('departement_id', $chef->departement_id)->count(),
            'ues_vacantes' => UniteEnseignement::where('departement_id', $chef->departement_id)
                ->where('est_vacant', true)->count(),
            'total_enseignants' => User::where('departement_id', $chef->departement_id)
                ->whereIn('role', ['enseignant', 'chef'])->count(),
            'affectations_en_attente' => Affectation::whereHas('uniteEnseignement', function ($query) use ($chef) {
                $query->where('departement_id', $chef->departement_id);
            })->where('validee', 'en_attente')->count(),
            'last_updated' => now()->toISOString()
        ];

        return response()->json($stats);
    }

    // Bulk validate affectations
    public function bulkValidateAffectations(Request $request)
    {
        $validated = $request->validate([
            'affectation_ids' => 'required|array',
            'affectation_ids.*' => 'exists:affectations,id'
        ]);

        $chef = Auth::user();
        $count = 0;

        DB::transaction(function () use ($validated, $chef, &$count) {
            foreach ($validated['affectation_ids'] as $affectationId) {
                $affectation = Affectation::whereHas('uniteEnseignement', function ($query) use ($chef) {
                    $query->where('departement_id', $chef->departement_id);
                })->where('id', $affectationId)->first();

                if ($affectation && $affectation->validee == 'en_attente') {
                    $affectation->update(['validee' => 'valide']);
                    $affectation->uniteEnseignement->update(['est_vacant' => false]);

                    // Log the bulk validation activity
                    \App\Models\Activity::log(
                        'approve',
                        'bulk_affectation_approved_chef',
                        "Validation en lot par chef: {$affectation->user->name} - {$affectation->uniteEnseignement->code} ({$affectation->type_seance})",
                        $affectation,
                        [
                            'teacher_name' => $affectation->user->name,
                            'ue_code' => $affectation->uniteEnseignement->code,
                            'ue_nom' => $affectation->uniteEnseignement->nom,
                            'type_seance' => $affectation->type_seance,
                            'annee_universitaire' => $affectation->annee_universitaire,
                            'approved_by' => $chef->name,
                            'chef_department' => $chef->departement->nom ?? 'N/A',
                            'method' => 'bulk_operation'
                        ]
                    );

                    // Create notification
                    Notification::create([
                        'user_id' => $affectation->user_id,
                        'title' => 'Affectation validée',
                        'message' => "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} a été validée.",
                        'is_read' => false
                    ]);

                    $count++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$count} affectation(s) validée(s) avec succès.",
            'count' => $count
        ]);
    }

    // Bulk reject affectations
    public function bulkRejectAffectations(Request $request)
    {
        $validated = $request->validate([
            'affectation_ids' => 'required|array',
            'affectation_ids.*' => 'exists:affectations,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $chef = Auth::user();
        $count = 0;

        DB::transaction(function () use ($validated, $chef, &$count) {
            foreach ($validated['affectation_ids'] as $affectationId) {
                $affectation = Affectation::whereHas('uniteEnseignement', function ($query) use ($chef) {
                    $query->where('departement_id', $chef->departement_id);
                })->where('id', $affectationId)->first();

                if ($affectation && $affectation->validee == 'en_attente') {
                    $affectation->update(['validee' => 'rejete']);

                    // Create notification
                    Notification::create([
                        'user_id' => $affectation->user_id,
                        'title' => 'Affectation rejetée',
                        'message' => "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} a été rejetée." .
                            ($validated['reason'] ? " Motif: {$validated['reason']}" : ''),
                        'is_read' => false
                    ]);

                    $count++;
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => "{$count} affectation(s) rejetée(s) avec succès.",
            'count' => $count
        ]);
    }

    // Mark UE as vacant
    public function markUeVacant($id)
    {
        $chef = Auth::user();
        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->first();

        if (!$ue) {
            return response()->json(['error' => 'UE not found'], 404);
        }

        $ue->update(['est_vacant' => true]);

        // Remove current affectations
        Affectation::where('ue_id', $id)
            ->where('validee', 'valide')
            ->update(['validee' => 'annule']);

        return response()->json([
            'success' => true,
            'message' => 'UE marquée comme vacante avec succès.'
        ]);
    }

    // Assign priority to UE
    public function assignUePriority(Request $request, $id)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $chef = Auth::user();
        $ue = UniteEnseignement::where('id', $id)
            ->where('departement_id', $chef->departement_id)
            ->first();

        if (!$ue) {
            return response()->json(['error' => 'UE not found'], 404);
        }

        // Note: This assumes you have a priority column in unites_enseignement table
        // If not, you might want to add it or store priority in a separate table
        $ue->update(['priority' => $validated['priority']]);

        return response()->json([
            'success' => true,
            'message' => 'Priorité assignée avec succès.'
        ]);
    }

    // Gestion des demandes pour l'année prochaine
    public function gestionDemandes(Request $request)
    {
        $chef = Auth::user();
        $departement = $chef->departement;
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        // Query for next year requests
        $query = Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
            $q->where('departement_id', $departement->id);
        })
            ->where('annee_universitaire', $nextYear)
            ->with(['user', 'uniteEnseignement.filiere']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('validee', $request->status);
        }

        if ($request->filled('filiere_id')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        if ($request->filled('type_seance')) {
            $query->where('type_seance', $request->type_seance);
        }

        $demandes = $query->orderBy('created_at', 'desc')->paginate(12);

        // Statistics
        $stats = [
            'total' => Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
                $q->where('departement_id', $departement->id);
            })->where('annee_universitaire', $nextYear)->count(),

            'pending' => Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
                $q->where('departement_id', $departement->id);
            })->where('annee_universitaire', $nextYear)->where('validee', 'en_attente')->count(),

            'approved' => Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
                $q->where('departement_id', $departement->id);
            })->where('annee_universitaire', $nextYear)->where('validee', 'valide')->count(),

            'rejected' => Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
                $q->where('departement_id', $departement->id);
            })->where('annee_universitaire', $nextYear)->where('validee', 'rejete')->count(),
        ];

        // Get filieres for filter
        $filieres = Filiere::where('departement_id', $departement->id)->get();

        return view('chef.gestion-demandes', compact('demandes', 'stats', 'filieres', 'nextYear'));
    }

    // Approve a request
    public function approveRequest($id)
    {
        $chef = Auth::user();
        $affectation = Affectation::whereHas('uniteEnseignement', function ($query) use ($chef) {
            $query->where('departement_id', $chef->departement_id);
        })->findOrFail($id);

        if ($affectation->validee !== 'en_attente') {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.']);
        }

        $affectation->update([
            'validee' => 'valide',
            'date_validation' => now(),
            'validee_par' => $chef->id,
            'commentaire' => 'Approuvée par le chef de département'
        ]);

        // Create notification for teacher
        try {
            Notification::create([
                'user_id' => $affectation->user_id,
                'title' => 'Demande approuvée',
                'message' => "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} (année {$affectation->annee_universitaire}) a été approuvée.",
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            // Handle if notifications table doesn't exist
        }

        return response()->json(['success' => true, 'message' => 'Demande approuvée avec succès.']);
    }

    // Reject a request
    public function rejectRequest($id)
    {
        $chef = Auth::user();
        $affectation = Affectation::whereHas('uniteEnseignement', function ($query) use ($chef) {
            $query->where('departement_id', $chef->departement_id);
        })->findOrFail($id);

        if ($affectation->validee !== 'en_attente') {
            return response()->json(['success' => false, 'message' => 'Cette demande a déjà été traitée.']);
        }

        $affectation->update([
            'validee' => 'rejete',
            'date_validation' => now(),
            'validee_par' => $chef->id,
            'commentaire' => 'Rejetée par le chef de département'
        ]);

        // Create notification for teacher
        try {
            Notification::create([
                'user_id' => $affectation->user_id,
                'title' => 'Demande rejetée',
                'message' => "Votre demande d'affectation pour {$affectation->uniteEnseignement->code} (année {$affectation->annee_universitaire}) a été rejetée.",
                'is_read' => false
            ]);
        } catch (\Exception $e) {
            // Handle if notifications table doesn't exist
        }

        return response()->json(['success' => true, 'message' => 'Demande rejetée avec succès.']);
    }

    // Export demandes
    public function exportDemandes(Request $request)
    {
        $chef = Auth::user();
        $departement = $chef->departement;
        $nextYear = (date('Y') + 1) . '-' . (date('Y') + 2);

        $demandes = Affectation::whereHas('uniteEnseignement', function ($q) use ($departement) {
            $q->where('departement_id', $departement->id);
        })
            ->where('annee_universitaire', $nextYear)
            ->with(['user', 'uniteEnseignement.filiere'])
            ->get();

        $filename = "demandes_affectation_{$nextYear}_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($demandes) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Enseignant',
                'Email',
                'UE Code',
                'UE Nom',
                'Filière',
                'Type Séance',
                'Statut',
                'Date Demande',
                'Date Traitement'
            ]);

            // CSV data
            foreach ($demandes as $demande) {
                fputcsv($file, [
                    $demande->user->name,
                    $demande->user->email,
                    $demande->uniteEnseignement->code,
                    $demande->uniteEnseignement->nom,
                    $demande->uniteEnseignement->filiere->nom,
                    $demande->type_seance,
                    $demande->validee,
                    $demande->created_at->format('d/m/Y H:i'),
                    $demande->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRapportsPdf(Request $request)
    {
        $chef = Auth::user();
        $year = $request->input('year');
        $chartImage = $request->input('chart_image');
        if (!$chef->departement_id) {
            return back()->with('error', 'Aucun département assigné.');
        }
        // Gather the same data as in rapports()
        $departement = $chef->departement;
        $stats = [
            'total_ues' => UniteEnseignement::where('departement_id', $departement->id)->count(),
            'ues_affectees' => UniteEnseignement::where('departement_id', $departement->id)
                ->whereHas('affectations', function ($q) use ($year) {
                    $q->where('annee_universitaire', $year)->where('validee', 'valide');
                })->count(),
            'ues_vacantes' => UniteEnseignement::where('departement_id', $departement->id)
                ->whereDoesntHave('affectations', function ($q) use ($year) {
                    $q->where('annee_universitaire', $year)->where('validee', 'valide');
                })->count(),
            'total_enseignants' => User::where('departement_id', $departement->id)
                ->whereIn('role', ['enseignant', 'chef'])->count(),
        ];
        $chargesHoraires = $this->calculateAllChargesHoraires($departement->id, $year);
        $repartitionFilieres = DB::table('unites_enseignement as ue')
            ->join('filieres as f', 'ue.filiere_id', '=', 'f.id')
            ->where('ue.departement_id', $departement->id)
            ->select(
                'f.nom as filiere',
                DB::raw('COUNT(*) as total_ues'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 0 THEN 1 ELSE 0 END) as ues_affectees'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 1 THEN 1 ELSE 0 END) as ues_vacantes')
            )
            ->groupBy('f.id', 'f.nom')
            ->get();
        $evolutionAffectations = DB::table('affectations as a')
            ->join('unites_enseignement as ue', 'a.ue_id', '=', 'ue.id')
            ->where('ue.departement_id', $departement->id)
            ->where('a.annee_universitaire', $year)
            ->select(
                DB::raw('MONTH(a.created_at) as mois'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN a.validee = "valide" THEN 1 ELSE 0 END) as validees'),
                DB::raw('SUM(CASE WHEN a.validee = "en_attente" THEN 1 ELSE 0 END) as en_attente'),
                DB::raw('SUM(CASE WHEN a.validee = "rejete" THEN 1 ELSE 0 END) as rejetees')
            )
            ->groupBy(DB::raw('MONTH(a.created_at)'))
            ->orderBy('mois')
            ->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('chef.exports.rapports_pdf', [
            'year' => $year,
            'stats' => $stats,
            'chargesHoraires' => $chargesHoraires,
            'repartitionFilieres' => $repartitionFilieres,
            'evolutionAffectations' => $evolutionAffectations,
            'chartImage' => $chartImage,
            'departement' => $departement
        ])->setPaper('a4', 'portrait');
        return $pdf->download('rapport_statistiques_departement_' . $year . '.pdf');
    }

    public function exportHistorique(Request $request)
    {
        $chef = Auth::user();
        $year = $request->input('year');

        if (!$chef->departement_id) {
            return back()->with('error', 'Aucun département assigné.');
        }

        // Get Historique Affectations
        $historiqueAffectations = HistoriqueAffectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })
            ->where('annee_universitaire', $year)
            ->with(['user', 'uniteEnseignement.filiere'])
            ->get();

        // Get Demandes (from Affectation model)
        $demandes = Affectation::whereHas('uniteEnseignement', function ($q) use ($chef) {
            $q->where('departement_id', $chef->departement_id);
        })
            ->where('annee_universitaire', $year)
            ->with(['user', 'uniteEnseignement.filiere'])
            ->get();

        // Combine and standardize data for PDF
        $combinedActivities = collect();

        foreach ($historiqueAffectations as $item) {
            $combinedActivities->push([
                'type' => 'Affectation',
                'ue' => $item->uniteEnseignement,
                'enseignant' => $item->user,
                'type_seance' => $item->type_seance,
                'statut' => 'Validée', // HistoriqueAffectation are always validated
                'date' => $item->created_at,
            ]);
        }

        foreach ($demandes as $item) {
            $combinedActivities->push([
                'type' => 'Demande',
                'ue' => $item->uniteEnseignement,
                'enseignant' => $item->user,
                'type_seance' => $item->type_seance,
                'statut' => ucfirst(str_replace('_', ' ', $item->validee)), // Format status
                'date' => $item->created_at,
            ]);
        }

        // Sort by date (descending, or as per your preference)
        $combinedActivities = $combinedActivities->sortByDesc('date')->values();

        $pdf = PDF::loadView('chef.exports.historique_pdf', [
            'year' => $year,
            'activities' => $combinedActivities,
        ]);

        return $pdf->download("historique_{$year}.pdf");
    }

    // Export Rapports PDF
    public function exportRapports(Request $request)
    {
        $chef = Auth::user();

        if (!$chef->departement_id) {
            return back()->with('error', 'Aucun département assigné.');
        }

        $selectedYear = $request->input('year');

        // Reuse data gathering logic from rapports() method
        $stats = [
            'total_ues' => UniteEnseignement::where('departement_id', $chef->departement_id)->count(),
            'ues_affectees' => UniteEnseignement::where('departement_id', $chef->departement_id)
                ->where('est_vacant', false)->count(),
            'ues_vacantes' => UniteEnseignement::where('departement_id', $chef->departement_id)
                ->where('est_vacant', true)->count(),
            'total_enseignants' => User::where('departement_id', $chef->departement_id)
                ->whereIn('role', ['enseignant', 'chef'])->count(),
        ];

        $repartitionFilieres = DB::table('unites_enseignement as ue')
            ->join('filieres as f', 'ue.filiere_id', '=', 'f.id')
            ->where('ue.departement_id', $chef->departement_id)
            ->select(
                'f.nom as filiere',
                DB::raw('COUNT(*) as total_ues'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 0 THEN 1 ELSE 0 END) as ues_affectees'),
                DB::raw('SUM(CASE WHEN ue.est_vacant = 1 THEN 1 ELSE 0 END) as ues_vacantes')
            )
            ->groupBy('f.id', 'f.nom')
            ->get();

        $enseignants = User::where('departement_id', $chef->departement_id)
            ->whereIn('role', ['enseignant', 'chef'])
            ->get();

        $chargesHoraires = [];
        foreach ($enseignants as $enseignant) {
            $charge = $this->calculateChargeHoraire($enseignant->id);
            $chargesHoraires[] = [
                'enseignant' => $enseignant,
                'charge' => $charge,
                'status' => $charge['total'] < 192 ? 'insuffisant' : ($charge['total'] > 240 ? 'excessif' : 'normal')
            ];
        }

        usort($chargesHoraires, function ($a, $b) {
            return $a['charge']['total'] <=> $b['charge']['total'];
        });

        $evolutionAffectations = DB::table('affectations as a')
            ->join('unites_enseignement as ue', 'a.ue_id', '=', 'ue.id')
            ->where('ue.departement_id', $chef->departement_id)
            ->where('a.annee_universitaire', $selectedYear)
            ->select(
                DB::raw('MONTH(a.created_at) as mois'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN a.validee = "valide" THEN 1 ELSE 0 END) as validees'),
                DB::raw('SUM(CASE WHEN a.validee = "en_attente" THEN 1 ELSE 0 END) as en_attente'),
                DB::raw('SUM(CASE WHEN a.validee = "rejete" THEN 1 ELSE 0 END) as rejetees')
            )
            ->groupBy(DB::raw('MONTH(a.created_at)'))
            ->orderBy('mois')
            ->get();

        $pdf = PDF::loadView('chef.exports.rapports_pdf', [
            'year' => $selectedYear,
            'stats' => $stats,
            'repartitionFilieres' => $repartitionFilieres,
            'chargesHoraires' => $chargesHoraires,
            'evolutionAffectations' => $evolutionAffectations
        ]);

        return $pdf->download("rapports_{$selectedYear}.pdf");
    }
}
