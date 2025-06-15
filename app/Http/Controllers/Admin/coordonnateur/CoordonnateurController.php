<?php

namespace App\Http\Controllers\Admin\coordonnateur;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use App\Models\Departement;
use App\Models\Filiere;
use App\Models\Schedule;
use App\Models\HistoriqueAffectation;
use App\Models\ChargeHoraire;
use App\Models\Notification;
use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class CoordonnateurController extends Controller
{
    // Dashboard principal du coordonnateur
    public function dashboard()
    {
        $coordonnateur = Auth::user();

        // Récupérer les filières gérées par le coordonnateur
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        if ($filieres->isEmpty()) {
            return redirect()->route('login')->with('error', 'Votre compte coordonnateur n\'est pas encore associé à une filière. Veuillez contacter l\'administrateur.');
        }

        $filiereIds = $filieres->pluck('id');

        // Statistiques générales
        $stats = [
            'total_ues' => UniteEnseignement::whereIn('filiere_id', $filiereIds)->count(),
            'ues_vacantes' => UniteEnseignement::whereIn('filiere_id', $filiereIds)->where('est_vacant', true)->count(),
            'total_vacataires' => User::where('role', 'vacataire')->count(),
            'affectations_en_attente' => Affectation::whereHas('uniteEnseignement', function ($query) use ($filiereIds) {
                $query->whereIn('filiere_id', $filiereIds);
            })->where('validee', 'en_attente')->count(),
        ];

        // UEs récemment ajoutées
        $recentUes = UniteEnseignement::whereIn('filiere_id', $filiereIds)
            ->with(['filiere', 'departement'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Affectations récentes
        $affectationsRecentes = Affectation::whereHas('uniteEnseignement', function ($query) use ($filiereIds) {
            $query->whereIn('filiere_id', $filiereIds);
        })
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Notifications non lues
        $notifications = collect();
        try {
            $notifications = Notification::where('user_id', $coordonnateur->id)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            $notifications = collect();
        }

        // UEs nécessitant définition des groupes TD/TP
        $uesGroupesManquants = UniteEnseignement::whereIn('filiere_id', $filiereIds)
            ->where(function ($query) {
                $query->where('groupes_td', 0)->orWhere('groupes_tp', 0);
            })
            ->with(['filiere'])
            ->get();

        return view('coordonnateur.dashboard', compact(
            'filieres',
            'stats',
            'recentUes',
            'affectationsRecentes',
            'notifications',
            'uesGroupesManquants'
        ));
    }

    // Gestion des UEs de la filière
    public function unitesEnseignement(Request $request)
    {
        $coordonnateur = Auth::user();

        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->orderBy('filieres.nom')
            ->get();

        // Get selected filiere or default to first one
        $selectedFiliereId = $request->get('filiere_id', $filieres->first()->id ?? null);
        $selectedFiliere = $filieres->where('id', $selectedFiliereId)->first();

        $ues = UniteEnseignement::where('filiere_id', $selectedFiliereId)
            ->with(['filiere', 'departement', 'affectations.user'])
            ->orderBy('semestre')
            ->orderBy('nom')
            ->paginate(15);

        return view('coordonnateur.unites-enseignement', compact('ues', 'filieres', 'selectedFiliere'));
    }

    // Show UE creation form
    public function createUE()
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->orderBy('filieres.nom')
            ->get();

        // Get potential responsables (enseignants and chefs from the same department)
        $departementIds = $filieres->pluck('departement_id')->unique();
        $responsables = User::whereIn('departement_id', $departementIds)
            ->whereIn('role', ['enseignant', 'chef'])
            ->orderBy('name')
            ->get();

        // Get all unique specialities from users and existing UEs
        $userSpecialites = User::whereNotNull('specialite')
            ->pluck('specialite')
            ->flatMap(function ($specialite) {
                return explode(',', $specialite);
            })
            ->map('trim')
            ->unique()
            ->sort()
            ->values();

        $ueSpecialites = UniteEnseignement::whereNotNull('specialite')
            ->pluck('specialite')
            ->flatMap(function ($specialite) {
                return explode(',', $specialite);
            })
            ->map('trim')
            ->unique()
            ->sort()
            ->values();

        // Merge and get unique specialities
        $allSpecialites = $userSpecialites->merge($ueSpecialites)
            ->unique()
            ->sort()
            ->values();

        return view('coordonnateur.create-ue', compact('filieres', 'responsables', 'allSpecialites'));
    }

    // Créer/Modifier une UE
    public function creerUE(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:unites_enseignement,code',
            'specialite' => 'nullable|array',
            'specialite.*' => 'string|max:255',
            'heures_cm' => 'required|integer|min:0',
            'heures_td' => 'required|integer|min:0',
            'heures_tp' => 'required|integer|min:0',
            'semestre' => 'required|in:S1,S2,S3,S4,S5,S6',
            'filiere_id' => 'required|exists:filieres,id',
            'responsable_id' => 'nullable|exists:users,id',
            'groupes_td' => 'required|integer|min:0',
            'groupes_tp' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        // Ensure at least some hours are specified
        if ($request->heures_cm + $request->heures_td + $request->heures_tp == 0) {
            return back()->withErrors(['heures' => 'Au moins un type d\'heures (CM, TD, TP) doit être spécifié.'])
                ->withInput();
        }

        // Vérifier que le coordonnateur gère cette filière
        $coordonnateur = Auth::user();
        $gereFiliere = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->where('filiere_id', $request->filiere_id)
            ->exists();

        if (!$gereFiliere) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer cette filière.');
        }

        $filiere = Filiere::findOrFail($request->filiere_id);

        // Prepare specialities as comma-separated string
        $specialites = $request->specialite ? implode(',', $request->specialite) : null;

        $ue = UniteEnseignement::create([
            'nom' => $request->nom,
            'code' => $request->code,
            'specialite' => $specialites,
            'heures_cm' => $request->heures_cm,
            'heures_td' => $request->heures_td,
            'heures_tp' => $request->heures_tp,
            'semestre' => $request->semestre,
            'est_vacant' => $request->responsable_id ? false : true,
            'groupes_td' => $request->groupes_td,
            'groupes_tp' => $request->groupes_tp,
            'filiere_id' => $request->filiere_id,
            'departement_id' => $filiere->departement_id,
            'responsable_id' => $request->responsable_id,
        ]);

        // Log the UE creation activity
        \App\Models\Activity::log(
            'create',
            'ue_created_coordonnateur',
            "UE créée par coordonnateur: {$ue->code} - {$ue->nom} ({$filiere->nom})",
            $ue,
            [
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'semestre' => $ue->semestre,
                'heures_cm' => $ue->heures_cm,
                'heures_td' => $ue->heures_td,
                'heures_tp' => $ue->heures_tp,
                'groupes_td' => $ue->groupes_td,
                'groupes_tp' => $ue->groupes_tp,
                'specialites' => $specialites,
                'filiere' => $filiere->nom,
                'departement' => $filiere->departement->nom ?? 'N/A',
                'created_by' => auth()->user()->name,
                'coordonnateur_role' => 'coordonnateur'
            ]
        );

        return redirect()->route('coordonnateur.unites-enseignement')
            ->with('success', 'Unité d\'enseignement créée avec succès.');
    }

    // Import UEs from CSV/Excel file
    public function importUEs(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
            'filiere_id' => 'required|exists:filieres,id'
        ]);

        $coordonnateur = Auth::user();

        // Vérifier que le coordonnateur gère cette filière
        $gereFiliere = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->where('filiere_id', $request->filiere_id)
            ->exists();

        if (!$gereFiliere) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer cette filière.');
        }

        try {
            $file = $request->file('file');
            $filiere = Filiere::findOrFail($request->filiere_id);

            // Read CSV file
            $csvData = array_map('str_getcsv', file($file->path()));
            $header = array_shift($csvData); // Remove header row

            $imported = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                try {
                    if (count($row) < 6) continue; // Skip incomplete rows

                    $ueData = [
                        'nom' => trim($row[0]),
                        'code' => trim($row[1]),
                        'specialite' => trim($row[2]) ?: null,
                        'heures_cm' => (int)($row[3] ?? 0),
                        'heures_td' => (int)($row[4] ?? 0),
                        'heures_tp' => (int)($row[5] ?? 0),
                        'semestre' => trim($row[6] ?? 'S1'),
                        'groupes_td' => (int)($row[7] ?? 1),
                        'groupes_tp' => (int)($row[8] ?? 1),
                        'filiere_id' => $request->filiere_id,
                        'departement_id' => $filiere->departement_id,
                        'est_vacant' => true,
                    ];

                    // Validate required fields
                    if (empty($ueData['nom']) || empty($ueData['code'])) {
                        $errors[] = "Ligne " . ($index + 2) . ": Nom et code requis";
                        continue;
                    }

                    // Check if code already exists
                    if (UniteEnseignement::where('code', $ueData['code'])->exists()) {
                        $errors[] = "Ligne " . ($index + 2) . ": Code {$ueData['code']} déjà existant";
                        continue;
                    }

                    UniteEnseignement::create($ueData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "$imported UE(s) importée(s) avec succès.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " et " . (count($errors) - 3) . " autres...";
                }
            }

            return redirect()->route('coordonnateur.unites-enseignement.create')
                ->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    // Show UE details page
    public function showUeDetails($id)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres
        $filiereIds = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        $ue = UniteEnseignement::where('id', $id)
            ->whereIn('filiere_id', $filiereIds)
            ->with(['filiere', 'departement', 'affectations.user'])
            ->firstOrFail();

        return view('coordonnateur.ue-details', compact('ue'));
    }

    // Show UE edit form
    public function editUE($id)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres
        $filiereIds = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        $ue = UniteEnseignement::where('id', $id)
            ->whereIn('filiere_id', $filiereIds)
            ->with(['filiere', 'departement', 'responsable'])
            ->firstOrFail();

        // Get coordonnateur's managed filieres
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->orderBy('filieres.nom')
            ->get();

        // Get potential responsables (enseignants and chefs from the same department)
        $departementIds = $filieres->pluck('departement_id')->unique();
        $responsables = User::whereIn('departement_id', $departementIds)
            ->whereIn('role', ['enseignant', 'chef'])
            ->orderBy('name')
            ->get();

        // Get all unique specialities from users and existing UEs
        $userSpecialites = User::whereNotNull('specialite')
            ->pluck('specialite')
            ->flatMap(function ($specialite) {
                return explode(',', $specialite);
            })
            ->map('trim')
            ->unique()
            ->sort()
            ->values();

        $ueSpecialites = UniteEnseignement::whereNotNull('specialite')
            ->pluck('specialite')
            ->flatMap(function ($specialite) {
                return explode(',', $specialite);
            })
            ->map('trim')
            ->unique()
            ->sort()
            ->values();

        // Merge and get unique specialities
        $allSpecialites = $userSpecialites->merge($ueSpecialites)
            ->unique()
            ->sort()
            ->values();

        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5']; // No S6

        return view('coordonnateur.ue-edit', compact(
            'ue',
            'filieres',
            'responsables',
            'allSpecialites',
            'semestres'
        ));
    }

    // Update UE
    public function updateUE(Request $request, $id)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres
        $filiereIds = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        $ue = UniteEnseignement::where('id', $id)
            ->whereIn('filiere_id', $filiereIds)
            ->firstOrFail();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('unites_enseignement')->ignore($ue->id)],
            'nom' => 'required|string|max:255',
            'heures_cm' => 'required|integer|min:0|max:100',
            'heures_td' => 'required|integer|min:0|max:100',
            'heures_tp' => 'required|integer|min:0|max:100',
            'semestre' => 'required|in:S1,S2,S3,S4,S5', // No S6
            'groupes_td' => 'required|integer|min:0|max:20',
            'groupes_tp' => 'required|integer|min:0|max:20',
            'filiere_id' => 'required|exists:filieres,id',
            'responsable_id' => 'nullable|exists:users,id',
            'specialite' => 'nullable|array',
            'specialite.*' => 'string|max:255',
            'description' => 'nullable|string'
        ]);

        // Ensure at least some hours are specified
        if ($validated['heures_cm'] + $validated['heures_td'] + $validated['heures_tp'] == 0) {
            return back()->withErrors(['heures' => 'Au moins un type d\'heures (CM, TD, TP) doit être spécifié.'])
                ->withInput();
        }

        // Verify filiere is managed by coordonnateur
        if (!in_array($validated['filiere_id'], $filiereIds->toArray())) {
            return back()->withErrors(['filiere_id' => 'Vous n\'êtes pas autorisé à gérer cette filière.'])
                ->withInput();
        }

        // Get filiere to set department
        $filiere = Filiere::findOrFail($validated['filiere_id']);
        $validated['departement_id'] = $filiere->departement_id;

        // Prepare specialities as comma-separated string
        $validated['specialite'] = $request->specialite ? implode(',', $request->specialite) : null;

        $ue->update($validated);

        return redirect()->route('coordonnateur.unites-enseignement')
            ->with('success', 'Unité d\'enseignement mise à jour avec succès');
    }

    // Show vacataires affectation page with drag-and-drop
    public function vacataires()
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres with their departments
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $filiereIds = $filieres->pluck('id');
        $departementIds = $filieres->pluck('departement_id')->unique();

        // Get UEs available for vacataires (those marked by chef)
        $uesDisponibles = UniteEnseignement::whereIn('filiere_id', $filiereIds)
            ->whereNotNull('vacataire_types')
            ->where('vacataire_types', '!=', '[]')
            ->with(['filiere', 'departement'])
            ->get();

        // Get vacataires from the same departments as the coordonnateur's filieres
        $vacataires = User::where('role', 'vacataire')
            ->whereIn('departement_id', $departementIds)
            ->orderBy('name')
            ->get();

        // Get departements for the modal form
        $departements = \App\Models\Departement::whereIn('id', $departementIds)
            ->orderBy('nom')
            ->get();

        return view('coordonnateur.vacataires', compact('uesDisponibles', 'vacataires', 'filieres', 'departements'));
    }

    // Affecter UE to vacataire
    public function affecterVacataire(Request $request)
    {
        $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'vacataire_id' => 'required|exists:users,id',
            'type_seance' => 'required|in:CM,TD,TP'
        ]);

        $coordonnateur = Auth::user();

        // Verify UE belongs to coordonnateur's filieres
        $filiereIds = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        $ue = UniteEnseignement::whereIn('filiere_id', $filiereIds)
            ->where('id', $request->ue_id)
            ->firstOrFail();

        // Verify the type is available for vacataires
        if (!in_array($request->type_seance, $ue->vacataire_types ?? [])) {
            return response()->json(['error' => 'Ce type de séance n\'est pas disponible pour les vacataires.'], 400);
        }

        // Create affectation
        $affectation = Affectation::create([
            'user_id' => $request->vacataire_id,
            'ue_id' => $request->ue_id,
            'type_seance' => $request->type_seance,
            'validee' => 'valide', // Auto-approve for vacataires
            'annee_universitaire' => '2024-2025'
        ]);

        // Log the vacataire affectation activity
        $vacataire = \App\Models\User::find($request->vacataire_id);
        \App\Models\Activity::log(
            'create',
            'vacataire_affectation_coordonnateur',
            "Affectation vacataire par coordonnateur: {$vacataire->name} - {$ue->code} ({$request->type_seance})",
            $affectation,
            [
                'vacataire_name' => $vacataire->name,
                'vacataire_email' => $vacataire->email,
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'type_seance' => $request->type_seance,
                'annee_universitaire' => '2024-2025',
                'filiere' => $ue->filiere->nom ?? 'N/A',
                'assigned_by' => auth()->user()->name,
                'coordonnateur_role' => 'coordonnateur'
            ]
        );

        return response()->json(['success' => 'Vacataire affecté avec succès.']);
    }

    // Définir les groupes TD/TP
    public function definirGroupes(Request $request, $id)
    {
        $request->validate([
            'groupes_td' => 'required|integer|min:0',
            'groupes_tp' => 'required|integer|min:0',
        ]);

        $coordonnateur = Auth::user();
        $ue = UniteEnseignement::findOrFail($id);

        // Vérifier que le coordonnateur gère cette filière
        $gereFiliere = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->where('filiere_id', $ue->filiere_id)
            ->exists();

        if (!$gereFiliere) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette UE.');
        }

        $ue->update([
            'groupes_td' => $request->groupes_td,
            'groupes_tp' => $request->groupes_tp,
        ]);

        return back()->with('success', 'Groupes TD/TP définis avec succès.');
    }



    // Créer un compte vacataire
    public function creerVacataire(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'specialite' => 'required|string|max:255',
        ]);

        $vacataire = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'vacataire',
            'specialite' => $request->specialite,
        ]);

        // Log the vacataire creation activity
        \App\Models\Activity::log(
            'create',
            'vacataire_created_coordonnateur',
            "Compte vacataire créé par coordonnateur: {$vacataire->name} ({$vacataire->email})",
            $vacataire,
            [
                'vacataire_name' => $vacataire->name,
                'vacataire_email' => $vacataire->email,
                'specialite' => $vacataire->specialite,
                'created_by' => auth()->user()->name,
                'coordonnateur_role' => 'coordonnateur'
            ]
        );

        return back()->with('success', 'Compte vacataire créé avec succès.');
    }

    // Show create vacataire form
    public function showCreateVacataire()
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres with their departments
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $departementIds = $filieres->pluck('departement_id')->unique();

        // Get departements for the form
        $departements = \App\Models\Departement::whereIn('id', $departementIds)
            ->orderBy('nom')
            ->get();

        // Get specialities list (same as admin)
        $specialites = [
            'Structures et béton armé',
            'Géotechnique',
            'Hydraulique urbaine',
            'Topographie',
            'Matériaux de construction',
            'Modélisation et calcul de structures',
            'Machines électriques',
            'Électronique de puissance',
            'Automatismes',
            'Réseaux électriques',
            'Commande des systèmes',
            'Développement logiciel',
            'Systèmes d\'exploitation',
            'Sécurité informatique',
            'Intelligence artificielle',
            'Réseaux & cybersécurité',
            'Bases de données',
            'CAO/DAO',
            'Mécanique des solides',
            'Fabrication mécanique',
            'Tribologie',
            'Vibrations et acoustique',
            'Thermodynamique',
            'Transferts thermiques',
            'Systèmes énergétiques',
            'Energies renouvelables',
            'Efficacité énergétique',
            'Traitement des eaux',
            'Hydrologie',
            'Écologie industrielle',
            'Analyse du cycle de vie',
            'Génie des procédés environnementaux',
            'Chimie organique et analytique',
            'Thermochimie',
            'Génie des réacteurs',
            'Opérations unitaires',
            'Séparation et distillation',
            'Réseaux informatiques',
            'Télécommunications',
            'Systèmes embarqués',
            'Protocoles réseau',
            'Analyse / Algèbre',
            'Statistiques et probabilités',
            'Mécanique physique',
            'Thermodynamique fondamentale',
            'Communication écrite et orale',
            'Anglais technique',
            'Français scientifique',
            'Management de projet',
            'Entrepreneuriat / Innovation'
        ];

        return view('coordonnateur.create-vacataire', compact('departements', 'specialites'));
    }

    // Store vacataire account (form submission)
    public function storeVacataire(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'departement_id' => 'required|exists:departements,id',
            'specialite' => 'nullable|array',
            'specialite.*' => 'string',
        ]);

        $coordonnateur = Auth::user();

        // Convert specialite array to string for storage (same as admin)
        $specialiteString = null;
        if ($request->has('specialite') && is_array($request->specialite)) {
            $specialiteString = implode(',', $request->specialite);
        }

        $vacataire = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'vacataire',
            'departement_id' => $request->departement_id,
            'specialite' => $specialiteString,
        ]);

        // Log the vacataire creation activity
        \App\Models\Activity::log(
            'create',
            'vacataire_created_coordonnateur_form',
            "Compte vacataire créé par coordonnateur via formulaire: {$vacataire->name} ({$vacataire->email})",
            $vacataire,
            [
                'vacataire_name' => $vacataire->name,
                'vacataire_email' => $vacataire->email,
                'departement_id' => $vacataire->departement_id,
                'specialite' => $vacataire->specialite,
                'created_by' => $coordonnateur->name,
                'created_by_id' => $coordonnateur->id,
                'coordonnateur_role' => 'coordonnateur',
                'creation_method' => 'form_page'
            ]
        );

        return redirect()->route('coordonnateur.vacataires')
            ->with('success', 'Compte vacataire créé avec succès! Le vacataire peut maintenant se connecter avec ses identifiants.');
    }

    // Save vacataire affectations via drag and drop
    public function saveVacataireAffectations(Request $request)
    {
        try {
            $coordonnateur = Auth::user();

            // Validate request
            $request->validate([
                'vacataire_id' => 'required|exists:users,id',
                'ues' => 'required|array|min:1',
                'ues.*.ue_id' => 'required|exists:unites_enseignement,id',
                'ues.*.type_seance' => 'required|in:CM,TD,TP'
            ]);

            $vacataireId = $request->vacataire_id;
            $ues = $request->ues;
            $currentYear = date('Y') . '-' . (date('Y') + 1);

            // Verify vacataire exists and belongs to coordonnateur's managed departments
            $vacataire = User::where('id', $vacataireId)
                ->where('role', 'vacataire')
                ->first();

            if (!$vacataire) {
                return response()->json(['error' => 'Vacataire non trouvé'], 400);
            }

            // Get coordonnateur's managed filieres and their departments
            $filieres = DB::table('coordonnateurs_filieres')
                ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
                ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
                ->select('filieres.*')
                ->get();

            $departementIds = $filieres->pluck('departement_id')->unique();

            // Verify vacataire belongs to managed departments
            if (!$departementIds->contains($vacataire->departement_id)) {
                return response()->json(['error' => 'Ce vacataire n\'appartient pas à vos départements gérés'], 403);
            }

            $createdCount = 0;
            $errors = [];

            DB::transaction(function () use ($ues, $vacataireId, $currentYear, $coordonnateur, $vacataire, &$createdCount, &$errors) {
                foreach ($ues as $ueData) {
                    try {
                        // Get UE and verify it belongs to coordonnateur's managed filieres
                        $ue = UniteEnseignement::find($ueData['ue_id']);

                        if (!$ue) {
                            $errors[] = "UE {$ueData['ue_id']} non trouvée";
                            continue;
                        }

                        // Verify UE belongs to coordonnateur's managed filieres
                        $coordonnateur = Auth::user();
                        $managedFiliereIds = DB::table('coordonnateurs_filieres')
                            ->where('user_id', $coordonnateur->id)
                            ->pluck('filiere_id');

                        if (!$managedFiliereIds->contains($ue->filiere_id)) {
                            $errors[] = "UE {$ue->code} n'appartient pas à vos filières gérées";
                            continue;
                        }

                        // Check if affectation already exists for this year
                        $existingAffectation = Affectation::where('ue_id', $ue->id)
                            ->where('user_id', $vacataireId)
                            ->where('type_seance', $ueData['type_seance'])
                            ->where('annee_universitaire', $currentYear)
                            ->first();

                        if ($existingAffectation) {
                            $errors[] = "Affectation déjà existante pour UE {$ue->code} ({$ueData['type_seance']})";
                            continue;
                        }

                        // Create affectation
                        $affectation = Affectation::create([
                            'ue_id' => $ue->id,
                            'user_id' => $vacataireId,
                            'type_seance' => $ueData['type_seance'],
                            'annee_universitaire' => $currentYear,
                            'validee' => 'valide', // Coordonnateur can directly validate
                            'validee_par' => $coordonnateur->id,
                            'date_validation' => now(),
                            'commentaire' => 'Affectation directe par coordonnateur via glisser-déposer'
                        ]);

                        // Log the drag-and-drop assignment activity
                        \App\Models\Activity::log(
                            'create',
                            'drag_drop_affectation_coordonnateur_vacataire',
                            "Affectation vacataire par glisser-déposer: {$vacataire->name} - {$ue->code} ({$ueData['type_seance']})",
                            $affectation,
                            [
                                'vacataire_name' => $vacataire->name,
                                'vacataire_email' => $vacataire->email,
                                'ue_code' => $ue->code,
                                'ue_nom' => $ue->nom,
                                'type_seance' => $ueData['type_seance'],
                                'annee_universitaire' => $currentYear,
                                'assigned_by' => $coordonnateur->name,
                                'coordonnateur_role' => 'coordonnateur',
                                'method' => 'drag_and_drop_vacataire'
                            ]
                        );

                        // Mark UE as not vacant
                        $ue->update(['est_vacant' => false]);

                        $createdCount++;
                    } catch (\Exception $e) {
                        $errors[] = "Erreur pour UE {$ueData['ue_id']}: " . $e->getMessage();
                    }
                }
            });

            // Prepare response - FIXED LOGIC
            if ($createdCount > 0 && empty($errors)) {
                // Complete success
                return response()->json([
                    'success' => true,
                    'created_count' => $createdCount,
                    'total_requested' => count($ues),
                    'message' => "{$createdCount} affectation(s) créée(s) avec succès!"
                ]);
            } elseif ($createdCount > 0 && !empty($errors)) {
                // Partial success
                return response()->json([
                    'success' => true,
                    'created_count' => $createdCount,
                    'total_requested' => count($ues),
                    'errors' => $errors,
                    'message' => "{$createdCount} affectation(s) créée(s), mais certaines ont échoué"
                ]);
            } else {
                // Complete failure
                return response()->json([
                    'success' => false,
                    'created_count' => 0,
                    'total_requested' => count($ues),
                    'errors' => $errors,
                    'message' => "Aucune affectation n'a pu être créée"
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Coordonnateur vacataire affectation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }

    // Create vacataire account via AJAX (JSON response) - DEPRECATED, keeping for compatibility
    public function createVacataire(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'departement_id' => 'required|exists:departements,id',
                'specialite' => 'nullable|string|max:255',
            ]);

            $coordonnateur = Auth::user();

            $vacataire = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'vacataire',
                'departement_id' => $request->departement_id,
                'specialite' => $request->specialite,
            ]);

            // Log the vacataire creation activity
            \App\Models\Activity::log(
                'create',
                'vacataire_created_coordonnateur_ajax',
                "Compte vacataire créé par coordonnateur via interface: {$vacataire->name} ({$vacataire->email})",
                $vacataire,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'departement_id' => $vacataire->departement_id,
                    'specialite' => $vacataire->specialite,
                    'created_by' => $coordonnateur->name,
                    'created_by_id' => $coordonnateur->id,
                    'coordonnateur_role' => 'coordonnateur',
                    'creation_method' => 'ajax_modal'
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Compte vacataire créé avec succès!',
                'vacataire' => [
                    'id' => $vacataire->id,
                    'name' => $vacataire->name,
                    'email' => $vacataire->email,
                    'specialite' => $vacataire->specialite,
                    'departement' => $vacataire->departement->nom ?? null
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating vacataire: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du compte: ' . $e->getMessage()
            ], 500);
        }
    }



    // Consulter les affectations par semestre
    public function affectations(Request $request)
    {
        $coordonnateur = Auth::user();

        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->orderBy('filieres.nom')
            ->get();

        // Get selected filiere or default to first one
        $selectedFiliereId = $request->get('filiere_id', $filieres->first()->id ?? null);
        $selectedFiliere = $filieres->where('id', $selectedFiliereId)->first();
        $semestre = $request->get('semestre', 'S1');

        $affectations = Affectation::whereHas('uniteEnseignement', function ($query) use ($selectedFiliereId, $semestre) {
            $query->where('filiere_id', $selectedFiliereId)->where('semestre', $semestre);
        })
            ->with(['user', 'uniteEnseignement.filiere'])
            ->where('validee', 'valide')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('coordonnateur.affectations', compact('affectations', 'filieres', 'selectedFiliere', 'semestre'));
    }

    // Gestion des emplois du temps
    public function emploisDuTemps(Request $request)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->orderBy('filieres.nom')
            ->get();

        // Get selected filiere from request or default to first one
        $selectedFiliereId = $request->get('filiere_id');
        $selectedFiliere = null;

        if ($selectedFiliereId) {
            $selectedFiliere = $filieres->where('id', $selectedFiliereId)->first();
        } else {
            $selectedFiliere = $filieres->first();
            $selectedFiliereId = $selectedFiliere->id ?? null;
        }

        // Get selected semester from request
        $selectedSemester = $request->get('semester');

        // Determine available semesters and default semester based on selected filiere
        $availableSemesters = [];
        $semesters = [];
        $defaultSemester = null;

        if ($selectedFiliere) {
            // Extract the year number from filiere name (GI1, GI2, GI3, etc.)
            $filiereNumber = substr($selectedFiliere->nom, -1);

            if ($filiereNumber == '1') {
                $availableSemesters = ['S1', 'S2'];
                $defaultSemester = 'S1';
            } elseif ($filiereNumber == '2') {
                $availableSemesters = ['S3', 'S4'];
                $defaultSemester = 'S3';
            } elseif ($filiereNumber == '3') {
                $availableSemesters = ['S5'];
                $defaultSemester = 'S5';
            } else {
                // Default fallback
                $availableSemesters = ['S1', 'S2'];
                $defaultSemester = 'S1';
            }

            $semesters = $availableSemesters;

            // If no semester specified, use default semester
            if (!$selectedSemester) {
                $selectedSemester = $defaultSemester;
            }
        }

        // Get UEs for the selected filiere and optionally filter by semester
        $uesQuery = UniteEnseignement::where('filiere_id', $selectedFiliereId)
            ->with(['affectations.user', 'filiere']);

        if ($selectedSemester && in_array($selectedSemester, $availableSemesters)) {
            $uesQuery->where('semestre', $selectedSemester);
        }

        $availableUEs = $uesQuery->orderBy('semestre')->orderBy('code')->get();

        // Get schedules filtered by filiere and optionally by semester
        $schedulesQuery = Schedule::whereHas('uniteEnseignement', function ($query) use ($selectedFiliereId) {
            $query->where('filiere_id', $selectedFiliereId);
        });

        if ($selectedSemester && in_array($selectedSemester, $availableSemesters)) {
            $schedulesQuery->where('semestre', $selectedSemester);
        }

        $schedules = $schedulesQuery
            ->with(['user', 'uniteEnseignement.filiere'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        return view('coordonnateur.emplois-du-temps', compact(
            'schedules',
            'filieres',
            'selectedFiliere',
            'selectedSemester',
            'availableSemesters',
            'defaultSemester',
            'semesters',
            'availableUEs'
        ));
    }

    // Save schedule changes via AJAX
    public function saveScheduleChanges(Request $request)
    {
        try {
            $coordonnateur = Auth::user();
            $changes = $request->input('changes', []);
            $filiereId = $request->input('filiere_id');

            // Verify coordonnateur has access to this filiere
            $hasAccess = DB::table('coordonnateurs_filieres')
                ->where('user_id', $coordonnateur->id)
                ->where('filiere_id', $filiereId)
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé à cette filière']);
            }

            foreach ($changes as $change) {
                switch ($change['action']) {
                    case 'add':
                        $this->addScheduleSlot($change, $filiereId);
                        break;
                    case 'remove':
                        $this->removeScheduleSlot($change, $filiereId);
                        break;
                    case 'delete':
                        $this->deleteSchedule($change['schedule_id']);
                        break;
                }
            }

            return response()->json(['success' => true, 'message' => 'Emploi du temps sauvegardé avec succès']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
    }

    // Enhanced save method - ALWAYS REPLACE ALL DATA for filiere+semester
    public function saveEmploiDuTempsEnhanced(Request $request)
    {
        try {
            $coordonnateur = Auth::user();

            // Always use the current state approach - no distinction between new/existing
            return $this->saveCurrentScheduleState($request);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()]);
        }
    }

    // Save current schedule state - ALWAYS REPLACE ALL DATA for filiere+semester
    public function saveCurrentScheduleState(Request $request)
    {
        try {
            $coordonnateur = Auth::user();
            $scheduleItems = $request->input('schedule_items', []);
            $filiereId = $request->input('filiere_id');
            $filiereName = $request->input('filiere_name');
            $semester = $request->input('semester');
            $currentYear = $request->input('current_year', date('Y'));
            $updateEnseignantSchedules = $request->input('update_enseignant_schedules', false);

            \Log::info('Saving schedule state - REPLACE ALL', [
                'filiere_id' => $filiereId,
                'semester' => $semester,
                'items_count' => count($scheduleItems),
                'coordonnateur_id' => $coordonnateur->id
            ]);

            // Verify coordonnateur has access to this filiere
            $hasAccess = DB::table('coordonnateurs_filieres')
                ->where('user_id', $coordonnateur->id)
                ->where('filiere_id', $filiereId)
                ->exists();

            if (!$hasAccess) {
                return response()->json(['success' => false, 'message' => 'Accès non autorisé à cette filière']);
            }

            DB::beginTransaction();

            // STEP 1: Clear ALL existing schedules for this filiere and semester
            $deletedCount = Schedule::whereHas('uniteEnseignement', function ($query) use ($filiereId) {
                $query->where('filiere_id', $filiereId);
            })
                ->where('semestre', $semester)
                ->where('annee_universitaire', $currentYear . '-' . ($currentYear + 1))
                ->delete();

            \Log::info('Deleted existing schedules', ['count' => $deletedCount]);

            $processedSchedules = [];
            $affectedTeachers = 0;

            // STEP 2: Create new schedules from current visible state
            foreach ($scheduleItems as $item) {
                $schedule = $this->createScheduleFromItem($item, $filiereId, $semester, $currentYear);
                if ($schedule) {
                    $processedSchedules[] = $schedule;
                }
            }

            \Log::info('Created new schedules', ['count' => count($processedSchedules)]);

            // STEP 3: Update enseignant schedules if requested
            if ($updateEnseignantSchedules && !empty($processedSchedules)) {
                $affectedTeachers = $this->updateEnseignantSchedules($processedSchedules, $filiereId, $semester, $currentYear);
            }

            // Log the schedule save activity
            \App\Models\Activity::log(
                'update',
                'schedule_saved_coordonnateur',
                "Emploi du temps sauvegardé par coordonnateur: {$filiereName} - {$semester} ({$currentYear}-" . ($currentYear + 1) . ")",
                null,
                [
                    'filiere_name' => $filiereName,
                    'filiere_id' => $filiereId,
                    'semester' => $semester,
                    'annee_universitaire' => $currentYear . '-' . ($currentYear + 1),
                    'processed_schedules' => count($processedSchedules),
                    'deleted_schedules' => $deletedCount,
                    'affected_teachers' => $affectedTeachers,
                    'saved_by' => $coordonnateur->name,
                    'coordonnateur_role' => 'coordonnateur'
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Emploi du temps sauvegardé avec succès (remplacement complet)',
                'affected_teachers' => $affectedTeachers,
                'processed_schedules' => count($processedSchedules),
                'deleted_schedules' => $deletedCount,
                'semester' => $semester,
                'filiere' => $filiereName
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving schedule state', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()]);
        }
    }

    // Create schedule from current DOM item
    private function createScheduleFromItem($item, $filiereId, $semester, $currentYear)
    {
        try {
            // Get UE details
            $ue = UniteEnseignement::find($item['ue_id']);
            if (!$ue) {
                throw new \Exception("UE non trouvée: " . $item['ue_id']);
            }

            // Get assigned teacher for this UE
            $affectation = DB::table('affectations')
                ->where('ue_id', $item['ue_id'])
                ->where('validee', 'valide')
                ->where('annee_universitaire', $currentYear . '-' . ($currentYear + 1))
                ->first();

            $teacherId = $affectation ? $affectation->user_id : null;

            // Create schedule entry
            $schedule = Schedule::create([
                'ue_id' => $item['ue_id'],
                'user_id' => $teacherId,
                'jour_semaine' => $item['jour_semaine'],
                'heure_debut' => $item['heure_debut'],
                'heure_fin' => $item['heure_fin'],
                'type_seance' => $item['type_seance'] ?? 'CM',
                'groupe' => $item['groupe'],
                'semestre' => $semester,
                'annee_universitaire' => $currentYear . '-' . ($currentYear + 1),
                'filiere_id' => $filiereId,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $schedule;
        } catch (\Exception $e) {
            \Log::error('Error creating schedule from item: ' . $e->getMessage());
            return null;
        }
    }

    // Enhanced add schedule slot with semester and year tracking
    private function addScheduleSlotEnhanced($change, $filiereId, $semester, $currentYear)
    {
        try {
            // Parse time slot
            $timeSlot = $change['time_slot'];
            $timeParts = explode('-', $timeSlot);
            $heureDebut = $timeParts[0];
            $heureFin = $timeParts[1];

            // Get UE details
            $ue = UniteEnseignement::find($change['ue_id']);
            if (!$ue) {
                throw new \Exception("UE non trouvée: " . $change['ue_id']);
            }

            // Get assigned teacher for this UE
            $affectation = DB::table('affectations')
                ->where('ue_id', $change['ue_id'])
                ->where('validee', 'valide')
                ->where('annee_universitaire', $currentYear . '-' . ($currentYear + 1))
                ->first();

            $teacherId = $affectation ? $affectation->user_id : null;

            // Create schedule entry
            $schedule = Schedule::create([
                'ue_id' => $change['ue_id'],
                'user_id' => $teacherId,
                'jour_semaine' => $change['day'],
                'heure_debut' => $heureDebut,
                'heure_fin' => $heureFin,
                'type_seance' => $change['type_seance'] ?? 'CM',
                'semestre' => $semester,
                'annee_universitaire' => $currentYear . '-' . ($currentYear + 1),
                'filiere_id' => $filiereId,
                'created_by' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $schedule;
        } catch (\Exception $e) {
            \Log::error('Error adding schedule slot: ' . $e->getMessage());
            return null;
        }
    }

    // Enhanced remove schedule slot
    private function removeScheduleSlotEnhanced($change, $filiereId, $semester)
    {
        try {
            $timeSlot = $change['time_slot'];
            $timeParts = explode('-', $timeSlot);
            $heureDebut = $timeParts[0];
            $heureFin = $timeParts[1];

            Schedule::where('ue_id', $change['ue_id'])
                ->where('jour_semaine', $change['day'])
                ->where('heure_debut', $heureDebut)
                ->where('heure_fin', $heureFin)
                ->where('semestre', $semester)
                ->whereHas('uniteEnseignement', function ($query) use ($filiereId) {
                    $query->where('filiere_id', $filiereId);
                })
                ->delete();
        } catch (\Exception $e) {
            \Log::error('Error removing schedule slot: ' . $e->getMessage());
        }
    }

    // Enhanced delete schedule
    private function deleteScheduleEnhanced($scheduleId)
    {
        try {
            Schedule::where('id', $scheduleId)->delete();
        } catch (\Exception $e) {
            \Log::error('Error deleting schedule: ' . $e->getMessage());
        }
    }

    // Update enseignant schedules based on coordonnateur changes
    private function updateEnseignantSchedules($schedules, $filiereId, $semester, $currentYear)
    {
        $affectedTeachers = 0;
        $teacherIds = [];

        foreach ($schedules as $schedule) {
            if ($schedule->user_id && !in_array($schedule->user_id, $teacherIds)) {
                $teacherIds[] = $schedule->user_id;

                // Create or update enseignant schedule entry
                $this->createEnseignantScheduleEntry($schedule, $currentYear);
                $affectedTeachers++;
            }
        }

        return $affectedTeachers;
    }

    // Create enseignant schedule entry
    private function createEnseignantScheduleEntry($schedule, $currentYear)
    {
        try {
            // Check if enseignant schedule entry already exists
            $existingEntry = DB::table('enseignant_schedules')->where([
                'user_id' => $schedule->user_id,
                'ue_id' => $schedule->ue_id,
                'jour_semaine' => $schedule->jour_semaine,
                'heure_debut' => $schedule->heure_debut,
                'heure_fin' => $schedule->heure_fin,
                'annee_universitaire' => $currentYear . '-' . ($currentYear + 1)
            ])->first();

            if (!$existingEntry) {
                DB::table('enseignant_schedules')->insert([
                    'user_id' => $schedule->user_id,
                    'ue_id' => $schedule->ue_id,
                    'jour_semaine' => $schedule->jour_semaine,
                    'heure_debut' => $schedule->heure_debut,
                    'heure_fin' => $schedule->heure_fin,
                    'type_seance' => $schedule->type_seance,
                    'semestre' => $schedule->semestre,
                    'annee_universitaire' => $schedule->annee_universitaire,
                    'filiere_id' => $schedule->filiere_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating enseignant schedule entry: ' . $e->getMessage());
        }
    }

    private function addScheduleSlot($change, $filiereId)
    {
        // Parse time slot (e.g., "08:00-09:30")
        $timeParts = explode('-', $change['time_slot']);
        $heureDebut = $timeParts[0];
        $heureFin = $timeParts[1];

        // Get UE details
        $ue = UniteEnseignement::find($change['ue_id']);
        if (!$ue) return;

        // Get assigned teacher for this UE
        $affectation = Affectation::where('ue_id', $change['ue_id'])
            ->where('validee', 'valide')
            ->first();

        // Create schedule entry
        Schedule::create([
            'ue_id' => $change['ue_id'],
            'user_id' => $affectation ? $affectation->user_id : null,
            'filiere_id' => $filiereId,
            'jour_semaine' => $change['day'],
            'heure_debut' => $heureDebut,
            'heure_fin' => $heureFin,
            'type_seance' => $change['type_seance'] ?? 'CM',
            'semestre' => $ue->semestre,
            'annee_universitaire' => '2024-2025'
        ]);
    }

    private function removeScheduleSlot($change, $filiereId)
    {
        // Parse time slot
        $timeParts = explode('-', $change['time_slot']);
        $heureDebut = $timeParts[0];
        $heureFin = $timeParts[1];

        Schedule::where('ue_id', $change['ue_id'])
            ->where('filiere_id', $filiereId)
            ->where('jour_semaine', $change['day'])
            ->where('heure_debut', $heureDebut)
            ->where('heure_fin', $heureFin)
            ->delete();
    }

    private function deleteSchedule($scheduleId)
    {
        Schedule::find($scheduleId)?->delete();
    }

    // ENHANCED Historique des affectations avec filtres avancés - SHOWS ALL AFFECTATIONS FOR FILIERE UEs
    public function historique(Request $request)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's filieres
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $filiereIds = $filieres->pluck('id');

        // Build query for ALL AFFECTATIONS (not just historique) for filiere UEs
        $query = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })->with(['user', 'uniteEnseignement.filiere']);

        // Year filter
        if ($request->filled('annee')) {
            $query->where('annee_universitaire', $request->annee);
        }

        // Filiere filter
        if ($request->filled('filiere_id')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        // Status filter (using validee column)
        if ($request->filled('action')) {
            $query->where('validee', $request->action);
        }

        // Date range filter
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $affectations = $query->orderBy('created_at', 'desc')->paginate(20);

        // Transform affectations to historique format for view compatibility
        $historique = $affectations->through(function ($affectation) {
            return (object) [
                'id' => $affectation->id,
                'action' => $affectation->validee, // Use validee as action
                'description' => "Affectation: {$affectation->user->name} → {$affectation->uniteEnseignement->code} ({$affectation->type_seance})",
                'annee_universitaire' => $affectation->annee_universitaire,
                'created_at' => $affectation->created_at,
                'updated_at' => $affectation->updated_at,
                'created_by_name' => $affectation->validee_par ? User::find($affectation->validee_par)->name ?? 'Système' : 'Système',
                'user' => $affectation->user,
                'uniteEnseignement' => $affectation->uniteEnseignement,
                'type_seance' => $affectation->type_seance,
                'commentaire' => $affectation->commentaire,
                'date_validation' => $affectation->date_validation,
                'changes' => null // No changes for affectations (not modification logs)
            ];
        });

        // Get available years for filter from affectations table
        $annees = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })
            ->select('annee_universitaire')
            ->distinct()
            ->orderBy('annee_universitaire', 'desc')
            ->pluck('annee_universitaire');

        // Get statistics
        $stats = $this->getHistoriqueStats($filiereIds, $request);

        return view('coordonnateur.historique', compact('historique', 'filieres', 'annees', 'stats'));
    }

    // Get affectations statistics for coordonnateur historique
    private function getHistoriqueStats($filiereIds, $request)
    {
        $query = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        });

        // Apply same filters as main query
        if ($request->filled('annee')) {
            $query->where('annee_universitaire', $request->annee);
        }
        if ($request->filled('filiere_id')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        $baseQuery = clone $query;

        return [
            'total' => $baseQuery->count(),
            'valide' => (clone $query)->where('validee', 'valide')->count(),
            'rejete' => (clone $query)->where('validee', 'rejete')->count(),
            'annule' => (clone $query)->where('validee', 'annule')->count(),
            'en_attente' => (clone $query)->where('validee', 'en_attente')->count(),
        ];
    }

    // ENHANCED Import/Export des données
    public function exportData(Request $request)
    {
        $coordonnateur = Auth::user();
        $type = $request->get('type', 'historique');
        $format = $request->get('format', 'excel');

        // Get coordonnateur's filieres
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $filiereIds = $filieres->pluck('id');

        switch ($type) {
            case 'historique':
                return $this->exportHistorique($filiereIds, $request);
            case 'affectations':
                return $this->exportAffectations($filiereIds, $request);
            case 'schedules':
                return $this->exportSchedules($filiereIds, $request);
            case 'ues':
                return $this->exportUEs($filiereIds, $request);
            default:
                return back()->with('error', 'Type d\'export non supporté.');
        }
    }

    // Export historique to CSV
    private function exportHistorique($filiereIds, $request)
    {
        $query = HistoriqueAffectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })->with(['user', 'uniteEnseignement.filiere']);

        // Apply filters
        if ($request->filled('annee')) {
            $query->where('annee_universitaire', $request->annee);
        }

        $historique = $query->orderBy('created_at', 'desc')->get();

        $filename = "historique_affectations_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($historique) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Date',
                'Action',
                'UE Code',
                'UE Nom',
                'Filière',
                'Enseignant',
                'Année Universitaire',
                'Description'
            ]);

            // CSV data
            foreach ($historique as $item) {
                fputcsv($file, [
                    $item->created_at->format('d/m/Y H:i'),
                    ucfirst($item->action ?? 'N/A'),
                    $item->uniteEnseignement->code ?? 'N/A',
                    $item->uniteEnseignement->nom ?? 'N/A',
                    $item->uniteEnseignement->filiere->nom ?? 'N/A',
                    $item->user->name ?? 'N/A',
                    $item->annee_universitaire ?? 'N/A',
                    $item->description ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export current affectations
    private function exportAffectations($filiereIds, $request)
    {
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $affectations = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })
            ->where('annee_universitaire', $currentYear)
            ->with(['user', 'uniteEnseignement.filiere'])
            ->get();

        $filename = "affectations_coordonnateur_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($affectations) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'UE Code',
                'UE Nom',
                'Filière',
                'Enseignant',
                'Type Séance',
                'Statut',
                'Date Validation',
                'Année Universitaire'
            ]);

            foreach ($affectations as $affectation) {
                fputcsv($file, [
                    $affectation->uniteEnseignement->code ?? 'N/A',
                    $affectation->uniteEnseignement->nom ?? 'N/A',
                    $affectation->uniteEnseignement->filiere->nom ?? 'N/A',
                    $affectation->user->name ?? 'N/A',
                    $affectation->type_seance ?? 'N/A',
                    ucfirst($affectation->validee ?? 'N/A'),
                    $affectation->date_validation ? $affectation->date_validation->format('d/m/Y') : 'N/A',
                    $affectation->annee_universitaire ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export schedules
    private function exportSchedules($filiereIds, $request)
    {
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $schedules = Schedule::whereIn('filiere_id', $filiereIds)
            ->where('annee_universitaire', $currentYear)
            ->with(['uniteEnseignement', 'user'])
            ->get();

        $filename = "emplois_du_temps_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($schedules) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'UE Code',
                'UE Nom',
                'Jour',
                'Heure Début',
                'Heure Fin',
                'Type Séance',
                'Groupe',
                'Enseignant',
                'Salle',
                'Semestre'
            ]);

            foreach ($schedules as $schedule) {
                fputcsv($file, [
                    $schedule->uniteEnseignement->code ?? 'N/A',
                    $schedule->uniteEnseignement->nom ?? 'N/A',
                    $schedule->jour_semaine ?? 'N/A',
                    $schedule->heure_debut ?? 'N/A',
                    $schedule->heure_fin ?? 'N/A',
                    $schedule->type_seance ?? 'N/A',
                    $schedule->group_number ?? 'N/A',
                    $schedule->user->name ?? 'Non assigné',
                    $schedule->salle ?? 'N/A',
                    $schedule->semestre ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Export UEs
    private function exportUEs($filiereIds, $request)
    {
        $ues = UniteEnseignement::whereIn('filiere_id', $filiereIds)
            ->with(['filiere', 'responsable'])
            ->get();

        $filename = "unites_enseignement_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($ues) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Code',
                'Nom',
                'Filière',
                'Semestre',
                'Heures CM',
                'Heures TD',
                'Heures TP',
                'Spécialité',
                'Responsable',
                'Est Vacant',
                'Groupes TD',
                'Groupes TP'
            ]);

            foreach ($ues as $ue) {
                fputcsv($file, [
                    $ue->code ?? 'N/A',
                    $ue->nom ?? 'N/A',
                    $ue->filiere->nom ?? 'N/A',
                    $ue->semestre ?? 'N/A',
                    $ue->heures_cm ?? 0,
                    $ue->heures_td ?? 0,
                    $ue->heures_tp ?? 0,
                    $ue->specialite ?? 'N/A',
                    $ue->responsable->name ?? 'N/A',
                    $ue->est_vacant ? 'Oui' : 'Non',
                    $ue->groupes_td ?? 0,
                    $ue->groupes_tp ?? 0
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importData(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
            'type' => 'required|in:ues,vacataires,schedules',
        ]);

        // Logique d'import selon le type
        return back()->with('success', 'Import réalisé avec succès.');
    }

    // Méthodes utilitaires
    private function verifierStatutVacance($ue)
    {
        $affectationsCM = Affectation::where('ue_id', $ue->id)->where('type_seance', 'CM')->where('validee', 'valide')->count();
        $affectationsTD = Affectation::where('ue_id', $ue->id)->where('type_seance', 'TD')->where('validee', 'valide')->count();
        $affectationsTP = Affectation::where('ue_id', $ue->id)->where('type_seance', 'TP')->where('validee', 'valide')->count();

        $estVacant = ($ue->heures_cm > 0 && $affectationsCM == 0) ||
            ($ue->heures_td > 0 && $affectationsTD == 0) ||
            ($ue->heures_tp > 0 && $affectationsTP == 0);

        $ue->update(['est_vacant' => $estVacant]);
    }

    // API pour récupérer les UEs d'une filière avec filtrage par semestre
    public function getUesFiliere($filiereId)
    {
        $coordonnateur = Auth::user();

        // Vérifier que le coordonnateur gère cette filière
        $gereFiliere = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->where('filiere_id', $filiereId)
            ->exists();

        if (!$gereFiliere) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        $ues = UniteEnseignement::where('filiere_id', $filiereId)
            ->select('id', 'nom', 'code', 'semestre', 'est_vacant', 'heures_cm', 'heures_td', 'heures_tp')
            ->orderBy('semestre')
            ->orderBy('code')
            ->get();

        return response()->json($ues);
    }

    // AJAX endpoint for dynamic filiere and semester filtering
    public function getEmploiDuTempsData(Request $request)
    {
        $coordonnateur = Auth::user();
        $filiereId = $request->get('filiere_id');
        $semester = $request->get('semester');

        // Verify access to filiere
        $hasAccess = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->where('filiere_id', $filiereId)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Get filiere info
        $filiere = DB::table('filieres')->where('id', $filiereId)->first();

        // Calculate available semesters and default semester based on filiere
        $filiereNumber = substr($filiere->nom, -1);
        $availableSemesters = [];
        $defaultSemester = null;

        if ($filiereNumber == '1') {
            $availableSemesters = ['S1', 'S2'];
            $defaultSemester = 'S1';
        } elseif ($filiereNumber == '2') {
            $availableSemesters = ['S3', 'S4'];
            $defaultSemester = 'S3';
        } elseif ($filiereNumber == '3') {
            $availableSemesters = ['S5'];
            $defaultSemester = 'S5';
        } else {
            $availableSemesters = ['S1', 'S2']; // Default fallback
            $defaultSemester = 'S1';
        }

        // If no semester specified, use default semester
        if (!$semester) {
            $semester = $defaultSemester;
        }

        // Get UEs with optional semester filtering
        $uesQuery = UniteEnseignement::where('filiere_id', $filiereId)
            ->with(['affectations.user', 'filiere']);

        if ($semester && in_array($semester, $availableSemesters)) {
            $uesQuery->where('semestre', $semester);
        }

        $allUes = $uesQuery->orderBy('semestre')->orderBy('code')->get();

        // Get schedules with optional semester filtering
        $schedulesQuery = Schedule::whereHas('uniteEnseignement', function ($query) use ($filiereId) {
            $query->where('filiere_id', $filiereId);
        });

        if ($semester && in_array($semester, $availableSemesters)) {
            $schedulesQuery->where('semestre', $semester);
        }

        $schedules = $schedulesQuery
            ->with(['user', 'uniteEnseignement.filiere'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        // Create a map of placed UEs with their types and groups to filter available UEs
        $placedUEs = [];
        foreach ($schedules as $schedule) {
            $ueId = $schedule->ue_id;
            $type = $schedule->type_seance;
            $group = $schedule->groupe;

            if (!isset($placedUEs[$ueId])) {
                $placedUEs[$ueId] = [];
            }

            if ($type === 'CM') {
                $placedUEs[$ueId]['CM'] = true;
            } else if ($type === 'TD' || $type === 'TP') {
                if (!isset($placedUEs[$ueId][$type])) {
                    $placedUEs[$ueId][$type] = [];
                }
                if ($group && !in_array($group, $placedUEs[$ueId][$type])) {
                    $placedUEs[$ueId][$type][] = $group;
                }
            }
        }

        // Show ALL UEs in carousel (since we don't auto-drop existing schedules)
        $ues = $allUes->map(function ($ue) {
            // Add all available types for all UEs
            $availableTypes = [];
            if ($ue->heures_cm > 0) $availableTypes[] = 'CM';
            if ($ue->heures_td > 0) $availableTypes[] = 'TD';
            if ($ue->heures_tp > 0) $availableTypes[] = 'TP';

            $ue->available_types = $availableTypes;
            return $ue;
        })->values();

        // Debug logging
        \Log::info('EmploiDuTemps Data Response', [
            'filiere_id' => $filiereId,
            'semester' => $semester,
            'total_ues_before_filter' => $allUes->count(),
            'total_ues_after_filter' => $ues->count(),
            'total_schedules' => $schedules->count(),
            'placed_ues' => $placedUEs
        ]);

        return response()->json([
            'success' => true,
            'filiere' => $filiere,
            'availableSemesters' => $availableSemesters,
            'defaultSemester' => $defaultSemester,
            'selectedSemester' => $semester,
            'ues' => $ues,
            'schedules' => $schedules,
            'debug_info' => [
                'total_ues_before_filter' => $allUes->count(),
                'total_ues_after_filter' => $ues->count(),
                'placed_ues' => $placedUEs
            ],
            'stats' => [
                'total_creneaux' => $schedules->count(),
                'cours_cm' => $schedules->where('type_seance', 'CM')->count(),
                'seances_td' => $schedules->where('type_seance', 'TD')->count(),
                'seances_tp' => $schedules->where('type_seance', 'TP')->count(),
                'ues_disponibles' => $ues->count()
            ]
        ]);
    }

    // Statistiques pour le dashboard
    public function getStatistics()
    {
        $coordonnateur = Auth::user();

        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $filiereIds = $filieres->pluck('id');

        $stats = [
            'ues_par_semestre' => UniteEnseignement::whereIn('filiere_id', $filiereIds)
                ->selectRaw('semestre, COUNT(*) as count')
                ->groupBy('semestre')
                ->get(),
            'affectations_par_type' => Affectation::whereHas('uniteEnseignement', function ($query) use ($filiereIds) {
                $query->whereIn('filiere_id', $filiereIds);
            })
                ->selectRaw('type_seance, COUNT(*) as count')
                ->where('validee', 'valide')
                ->groupBy('type_seance')
                ->get(),
        ];

        return response()->json($stats);
    }

    // AJAX methods for vacataires (same as chef pattern)
    public function getVacatairesList()
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's managed filieres with their departments
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        $departementIds = $filieres->pluck('departement_id')->unique();

        // Get vacataires from the same departments as the coordonnateur's filieres
        $vacataires = User::where('role', 'vacataire')
            ->whereIn('departement_id', $departementIds)
            ->with('departement')
            ->orderBy('name')
            ->get();

        return response()->json($vacataires);
    }

    public function getCompatibleUEs($vacataireId)
    {
        try {
            $coordonnateur = Auth::user();
            $currentYear = date('Y') . '-' . (date('Y') + 1);

            // Get coordonnateur's managed filieres
            $filiereIds = DB::table('coordonnateurs_filieres')
                ->where('user_id', $coordonnateur->id)
                ->pluck('filiere_id');

            \Log::info('🔍 Coordonnateur filieres:', [
                'coordonnateur_id' => $coordonnateur->id,
                'filiere_ids' => $filiereIds->toArray()
            ]);

            // Get vacataire with eager loaded affectations
            $vacataire = User::with(['affectations' => function ($query) use ($currentYear) {
                $query->where('annee_universitaire', $currentYear)
                    ->where('validee', 'valide');
            }])->findOrFail($vacataireId);

            // Get vacataire specialities (properly trimmed)
            $vacataireSpecialites = [];
            if ($vacataire->specialite) {
                $vacataireSpecialites = array_map('trim', explode(',', $vacataire->specialite));
                $vacataireSpecialites = array_filter($vacataireSpecialites); // Remove empty values
                $vacataireSpecialites = array_map('strtolower', $vacataireSpecialites); // Convert to lowercase
            }

            \Log::info('🔍 Vacataire details:', [
                'id' => $vacataire->id,
                'name' => $vacataire->name,
                'specialites' => $vacataireSpecialites,
                'existing_affectations' => $vacataire->affectations->count()
            ]);

            // Get ALL UEs in coordonnateur's filieres first (for debugging)
            $allUes = UniteEnseignement::whereIn('filiere_id', $filiereIds)
                ->with(['filiere', 'departement', 'affectations' => function ($query) use ($currentYear) {
                    $query->where('annee_universitaire', $currentYear)
                        ->where('validee', 'valide');
                }])
                ->get();

            \Log::info('🔍 All UEs in filieres:', [
                'total_count' => $allUes->count(),
                'sample_ues' => $allUes->take(3)->map(function ($ue) {
                    return [
                        'id' => $ue->id,
                        'code' => $ue->code,
                        'nom' => $ue->nom,
                        'est_vacant' => $ue->est_vacant,
                        'vacataire_types' => $ue->vacataire_types,
                        'specialite' => $ue->specialite,
                        'affectations_count' => $ue->affectations->count()
                    ];
                })
            ]);

            // Get UEs that are:
            // 1. In coordonnateur's managed filieres
            // 2. Marked as vacant (est_vacant = true)
            // 3. Have vacataire_types defined
            $ues = UniteEnseignement::whereIn('filiere_id', $filiereIds)
                ->where('est_vacant', true)
                ->whereNotNull('vacataire_types')
                ->where('vacataire_types', '!=', '[]')
                ->where('vacataire_types', '!=', 'null')
                ->with(['filiere', 'departement'])
                ->get();

            \Log::info('🔍 Filtered UEs:', [
                'total_count' => $ues->count(),
                'filiere_ids' => $filiereIds->toArray(),
                'conditions' => [
                    'est_vacant' => true,
                    'has_vacataire_types' => true
                ],
                'sample_ues' => $ues->take(3)->map(function ($ue) {
                    return [
                        'id' => $ue->id,
                        'code' => $ue->code,
                        'nom' => $ue->nom,
                        'est_vacant' => $ue->est_vacant,
                        'vacataire_types' => $ue->vacataire_types,
                        'specialite' => $ue->specialite
                    ];
                })
            ]);

            // Process each UE to check compatibility
            $compatibleUEs = [];

            foreach ($ues as $ue) {
                try {
                    // STEP 1: Check speciality compatibility (MORE FLEXIBLE MATCHING)
                    $isSpecialityCompatible = false;

                    // If UE has no speciality, it's compatible with everyone
                    if (empty($ue->specialite)) {
                        $isSpecialityCompatible = true;
                    }
                    // If vacataire has no specialities, they can teach general UEs
                    else if (empty($vacataireSpecialites)) {
                        $isSpecialityCompatible = strtolower($ue->specialite) === 'général';
                    }
                    // FLEXIBLE CHECK: ANY UE speciality can match ANY vacataire speciality
                    else {
                        $ueSpecialites = array_map('trim', explode(',', $ue->specialite));
                        $ueSpecialites = array_filter($ueSpecialites); // Remove empty values
                        $ueSpecialites = array_map('strtolower', $ueSpecialites); // Convert to lowercase

                        // Check if ANY UE speciality matches ANY vacataire speciality
                        foreach ($ueSpecialites as $ueSpec) {
                            foreach ($vacataireSpecialites as $vacSpec) {
                                if (
                                    stripos($ueSpec, $vacSpec) !== false ||
                                    stripos($vacSpec, $ueSpec) !== false ||
                                    $ueSpec === 'général' // General UEs are compatible with everyone
                                ) {
                                    $isSpecialityCompatible = true;
                                    break 2; // Break both loops when a match is found
                                }
                            }
                        }
                    }

                    // Skip UE if specialities don't match
                    if (!$isSpecialityCompatible) {
                        \Log::info('❌ UE skipped due to speciality mismatch:', [
                            'ue_id' => $ue->id,
                            'ue_code' => $ue->code,
                            'ue_specialites' => $ue->specialite,
                            'vacataire_specialites' => $vacataireSpecialites
                        ]);
                        continue;
                    }

                    // STEP 2: Extract available vacataire types
                    $vacataireTypes = [];
                    if ($ue->vacataire_types) {
                        if (is_string($ue->vacataire_types)) {
                            $decoded = json_decode($ue->vacataire_types, true);
                            if (is_array($decoded)) {
                                $vacataireTypes = $decoded;
                            }
                        } else if (is_array($ue->vacataire_types)) {
                            $vacataireTypes = $ue->vacataire_types;
                        }
                    }

                    // Skip UE if no vacataire types defined
                    if (empty($vacataireTypes)) {
                        \Log::info('❌ UE skipped due to no vacataire types:', [
                            'ue_id' => $ue->id,
                            'ue_code' => $ue->code
                        ]);
                        continue;
                    }

                    // STEP 3: Check session types not assigned to ANY user
                    $assignedTypesToAnyUser = Affectation::where('ue_id', $ue->id)
                        ->where('annee_universitaire', $currentYear)
                        ->where('validee', 'valide')
                        ->distinct()
                        ->pluck('type_seance')
                        ->toArray();

                    // Only show session types that are:
                    // 1. Approved as vacataire by chef (in vacataire_types)
                    // 2. Not assigned to ANY user yet
                    $availableTypes = [];
                    foreach ($vacataireTypes as $type) {
                        if (!in_array($type, $assignedTypesToAnyUser)) {
                            $availableTypes[] = $type;
                        }
                    }

                    // Skip UE if no available types remain
                    if (empty($availableTypes)) {
                        \Log::info('❌ UE skipped due to no available types:', [
                            'ue_id' => $ue->id,
                            'ue_code' => $ue->code,
                            'assigned_types' => $assignedTypesToAnyUser,
                            'vacataire_types' => $vacataireTypes
                        ]);
                        continue;
                    }

                    // Add UE to compatible list with available types
                    $compatibleUEs[] = [
                        'id' => $ue->id,
                        'code' => $ue->code,
                        'nom' => $ue->nom,
                        'specialite' => $ue->specialite,
                        'filiere_id' => $ue->filiere_id,
                        'filiere_nom' => $ue->filiere->nom ?? 'N/A',
                        'departement_nom' => $ue->departement->nom ?? 'N/A',
                        'vacataire_types' => $availableTypes,
                        'total_hours' => $ue->heures_cm + $ue->heures_td + $ue->heures_tp
                    ];

                    \Log::info('✅ UE added to compatible list:', [
                        'ue_id' => $ue->id,
                        'ue_code' => $ue->code,
                        'available_types' => $availableTypes
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error processing UE: ' . $e->getMessage(), [
                        'ue_id' => $ue->id ?? 'unknown',
                        'ue_code' => $ue->code ?? 'unknown',
                        'error_line' => $e->getLine(),
                        'error_file' => $e->getFile()
                    ]);
                    continue;
                }
            }

            \Log::info('🔥 Final compatible UEs for vacataire:', [
                'vacataire_id' => $vacataireId,
                'vacataire_name' => $vacataire->name,
                'total_ues_found' => count($compatibleUEs),
                'filtering_conditions' => [
                    'belongs_to_coordonnateur_filieres' => true,
                    'est_vacant' => true,
                    'has_vacataire_types' => true,
                    'flexible_speciality_match' => 'ANY UE speciality can match ANY vacataire speciality',
                    'session_types_not_assigned_to_any_user' => true
                ],
                'sample_ues' => array_slice($compatibleUEs, 0, 3)
            ]);

            return response()->json($compatibleUEs);
        } catch (\Exception $e) {
            \Log::error('Error in getCompatibleUEs: ' . $e->getMessage(), [
                'vacataire_id' => $vacataireId,
                'error_line' => $e->getLine(),
                'error_file' => $e->getFile(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erreur lors du chargement des UEs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Export emploi du temps to PDF - CURRENT DRAG-AND-DROP STATE
    public function exportEmploiDuTemps(Request $request)
    {
        try {
            $coordonnateur = Auth::user();
            $filiereId = $request->get('filiere_id');
            $semester = $request->get('semester');
            $year = $request->get('year', date('Y'));

            if (!$filiereId || !$semester) {
                return redirect()->back()->with('error', 'Filière et semestre requis pour l\'export.');
            }

            // Verify coordonnateur manages this filiere
            $gereFiliere = DB::table('coordonnateurs_filieres')
                ->where('user_id', $coordonnateur->id)
                ->where('filiere_id', $filiereId)
                ->exists();

            if (!$gereFiliere) {
                return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à gérer cette filière.');
            }

            $filiere = Filiere::findOrFail($filiereId);
            $daysOrder = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            $anneeUniversitaire = $year . '-' . ($year + 1);

            // FETCH EXACT CURRENT STATE: All schedules for this filiere+semester+year
            // This matches what's displayed in the drag-and-drop interface
            $schedules = Schedule::where('filiere_id', $filiereId)
                ->where('semestre', $semester)
                ->where('annee_universitaire', $anneeUniversitaire)
                ->with(['uniteEnseignement', 'user', 'filiere'])
                ->orderByRaw("FIELD(jour_semaine, '" . implode("','", $daysOrder) . "')")
                ->orderBy('heure_debut')
                ->orderBy('heure_fin')
                ->get();

            \Log::info('📄 Coordonnateur PDF Export:', [
                'coordonnateur' => $coordonnateur->name,
                'filiere' => $filiere->nom,
                'semester' => $semester,
                'year' => $anneeUniversitaire,
                'schedules_count' => $schedules->count(),
                'schedules_data' => $schedules->map(function ($s) {
                    return [
                        'ue' => $s->uniteEnseignement->code ?? 'N/A',
                        'day' => $s->jour_semaine,
                        'time' => $s->heure_debut . '-' . $s->heure_fin,
                        'type' => $s->type_seance,
                        'teacher' => $s->user->name ?? 'Non assigné'
                    ];
                })
            ]);

            $data = [
                'coordonnateur' => $coordonnateur,
                'filiere' => $filiere,
                'semester' => $semester,
                'schedules' => $schedules,
                'title' => 'Emploi du Temps - ' . $filiere->nom . ' ' . $semester,
                'currentDate' => \Carbon\Carbon::now()->format('d/m/Y'),
                'academicYear' => $anneeUniversitaire,
                'exportInfo' => [
                    'total_courses' => $schedules->count(),
                    'unique_ues' => $schedules->pluck('uniteEnseignement.code')->unique()->count(),
                    'assigned_teachers' => $schedules->whereNotNull('user_id')->count()
                ]
            ];

            // Configure PDF options for landscape orientation
            $pdf = Pdf::loadView('coordonnateur.exports.emploi-du-temps', $data)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'Arial',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true
                ]);

            $filename = 'emploi-du-temps-' . Str::slug($filiere->nom) . '-' . $semester . '-' . $year . '-' . date('m-d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Export Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        }
    }

    // REMOVED: No longer needed - all operations handled by save function

    // API endpoint for affectations report data
    public function getAffectationsData($year)
    {
        $coordonnateur = Auth::user();

        // Get coordonnateur's filieres
        $filiereIds = DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        // Get affectations for the selected year
        $affectations = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })
            ->where('annee_universitaire', $year)
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform affectations for the table
        $affectationsData = $affectations->map(function ($affectation) {
            return [
                'ue_code' => $affectation->uniteEnseignement->code,
                'ue_nom' => $affectation->uniteEnseignement->nom,
                'enseignant' => $affectation->user->name,
                'type_seance' => $affectation->type_seance,
                'statut' => $affectation->validee,
                'date' => $affectation->created_at->format('d/m/Y H:i')
            ];
        });

        // Calculate statistics for the chart
        $stats = [
            'labels' => ['Validées', 'Rejetées', 'En attente', 'Annulées'],
            'data' => [
                $affectations->where('validee', 'valide')->count(),
                $affectations->where('validee', 'rejete')->count(),
                $affectations->where('validee', 'en_attente')->count(),
                $affectations->where('validee', 'annule')->count()
            ]
        ];

        return response()->json([
            'affectations' => $affectationsData,
            'stats' => $stats
        ]);
    }

    public function downloadRapportAnalytiquePDF(Request $request)
    {
        $coordonnateur = Auth::user();
        $year = $request->input('year');
        $chartImage = $request->input('chart_image');

        // Get coordonnateur's filieres
        $filiereIds = \DB::table('coordonnateurs_filieres')
            ->where('user_id', $coordonnateur->id)
            ->pluck('filiere_id');

        // Get affectations for the selected year
        $affectations = Affectation::whereHas('uniteEnseignement', function ($q) use ($filiereIds) {
            $q->whereIn('filiere_id', $filiereIds);
        })
            ->where('annee_universitaire', $year)
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Prepare data for the table
        $affectationsData = $affectations->map(function ($affectation) {
            return [
                'ue_code' => $affectation->uniteEnseignement->code,
                'ue_nom' => $affectation->uniteEnseignement->nom,
                'enseignant' => $affectation->user->name,
                'type_seance' => $affectation->type_seance,
                'statut' => $affectation->validee,
                'date' => $affectation->created_at->format('d/m/Y H:i')
            ];
        });

        // Compute stats for each status
        $stats = [
            'valide' => $affectations->where('validee', 'valide')->count(),
            'rejete' => $affectations->where('validee', 'rejete')->count(),
            'en_attente' => $affectations->where('validee', 'en_attente')->count(),
            'annule' => $affectations->where('validee', 'annule')->count(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('coordonnateur.pdf_rapport_analytique', [
            'year' => $year,
            'affectations' => $affectationsData,
            'chartImage' => $chartImage,
            'stats' => $stats
        ])->setPaper('a4', 'portrait');

        return $pdf->download('rapport_analytique_affectations_' . $year . '.pdf');
    }
}
