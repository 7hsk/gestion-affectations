<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Departement;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // Build query with filters
        $query = User::with('departement');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('departement_id', $request->department);
        }

        // Status filter (active/inactive based on recent activity)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('updated_at', '>=', now()->subDays(30));
            } elseif ($request->status === 'inactive') {
                $query->where('updated_at', '<', now()->subDays(30));
            }
        }

        // Order by latest
        $query->latest();

        // Paginate results
        $users = $query->paginate(15)->withQueryString();

        // Get statistics for the header
        $totalUsers = User::count();
        $usersByRole = [
            'enseignant' => User::where('role', 'enseignant')->count(),
            'chef' => User::where('role', 'chef')->count(),
            'coordonnateur' => User::where('role', 'coordonnateur')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'vacataire' => User::where('role', 'vacataire')->count(),
        ];

        return view('admin.users.index', compact('users', 'totalUsers', 'usersByRole'));
    }

    public function create()
    {
        $departements = Departement::all();
        $roles = ['admin', 'chef', 'coordonnateur', 'enseignant', 'vacataire'];
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

        return view('admin.users.create', compact('departements', 'roles', 'specialites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,chef,coordonnateur,enseignant,vacataire',
            'departement_id' => 'nullable|exists:departements,id',
            'specialite' => 'required|array'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'departement_id' => $request->departement_id,
            'specialite' => implode(',', $request->specialite)
        ]);

        // Log the user creation activity
        \App\Models\Activity::log(
            'create',
            'user_created',
            "Nouvel utilisateur créé: {$user->name} ({$user->role})",
            $user,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'department' => $user->departement->nom ?? 'Non assigné',
                'specialite' => $user->specialite ?? 'Non spécifiée',
                'created_by' => auth()->user()->name
            ]
        );

        // If the user is a coordonnateur, redirect to filière assignment
        if ($user->role === 'coordonnateur') {
            return redirect()->route('admin.coordonnateur.assign-filiere', $user)
                ->with('success', 'Utilisateur créé avec succès. Veuillez maintenant assigner une filière.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $departements = Departement::all();
        $roles = ['admin', 'chef', 'coordonnateur', 'enseignant', 'vacataire'];
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

        return view('admin.users.edit', compact('user', 'departements', 'roles', 'specialites'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,chef,coordonnateur,enseignant,vacataire',
            'departement_id' => 'nullable|exists:departements,id',
            'specialite' => 'required|array'
        ]);

        $oldRole = $user->role;
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'departement_id' => $request->departement_id,
            'specialite' => implode(',', $request->specialite)
        ]);

        // Log the user update activity
        \App\Models\Activity::log(
            'update',
            'user_updated',
            "Utilisateur mis à jour: {$user->name} ({$user->role})",
            $user,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'department' => $user->departement->nom ?? 'Non assigné',
                'specialite' => $user->specialite ?? 'Non spécifiée',
                'updated_by' => auth()->user()->name
            ]
        );

        // If the user is now a coordonnateur and wasn't before, redirect to filière assignment
        if ($user->role === 'coordonnateur' && $oldRole !== 'coordonnateur') {
            return redirect()->route('admin.coordonnateur.assign-filiere', $user)
                ->with('success', 'Utilisateur mis à jour avec succès. Veuillez maintenant assigner une filière.');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user)
    {
        try {
            DB::transaction(function () use ($user) {
                // If the user is a coordonnateur, delete their filière assignments
                if ($user->role === 'coordonnateur') {
                    // Delete all relationships in coordonnateurs_filieres table
                    DB::table('coordonnateurs_filieres')
                        ->where('user_id', $user->id)
                        ->delete();
                }

                // Delete the user
                $user->delete();
            });

            return redirect()->route('admin.users.index')
                ->with('success', 'L\'utilisateur a été supprimé avec succès.');
        } catch (\Exception $e) {
            Log::error('Error deleting user', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.users.index')
                ->with('error', 'Une erreur est survenue lors de la suppression de l\'utilisateur.');
        }
    }

    // Export users to Excel/CSV
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');

        // Build the same query as index with filters
        $query = User::with('departement');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $query->where('departement_id', $request->department);
        }

        $users = $query->latest()->get();

        if ($format === 'excel') {
            return $this->exportToExcel($users);
        } else {
            return $this->exportToCsv($users);
        }
    }

    private function exportToExcel($users)
    {
        $filename = 'users_' . date('Y-m-d_H-i-s') . '.xlsx';

        // For now, return a simple CSV since we don't have Excel package
        return $this->exportToCsv($users, $filename);
    }

    private function exportToCsv($users, $filename = null)
    {
        $filename = $filename ?: 'users_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Email',
                'Rôle',
                'Département',
                'Spécialités',
                'Date de création',
                'Dernière mise à jour'
            ]);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    ucfirst($user->role),
                    $user->departement ? $user->departement->nom : 'Non assigné',
                    $user->specialite ?: 'Aucune',
                    $user->created_at->format('d/m/Y H:i'),
                    $user->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Get user statistics for API/AJAX calls
    public function getStats()
    {
        $stats = [
            'total' => User::count(),
            'by_role' => [
                'admin' => User::where('role', 'admin')->count(),
                'chef' => User::where('role', 'chef')->count(),
                'coordonnateur' => User::where('role', 'coordonnateur')->count(),
                'enseignant' => User::where('role', 'enseignant')->count(),
                'vacataire' => User::where('role', 'vacataire')->count(),
            ],
            'by_department' => User::with('departement')
                ->get()
                ->groupBy('departement.nom')
                ->map(function ($users) {
                    return $users->count();
                }),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'active_users' => User::where('updated_at', '>=', now()->subDays(7))->count()
        ];

        return response()->json($stats);
    }

    public function showAssignFiliereForm(User $user)
    {
        if ($user->role !== 'coordonnateur') {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cette fonctionnalité est uniquement disponible pour les coordonnateurs.');
        }

        $filieres = \App\Models\Filiere::with('coordonnateurs')->get();
        return view('admin.users.assign-filiere', compact('user', 'filieres'));
    }

    public function assignFiliere(Request $request, User $user)
    {
        $request->validate([
            'filiere_base' => 'required|string'
        ]);

        // Get all filières that match the base name
        $filieres = Filiere::where('nom', 'like', $request->filiere_base . '%')->get();

        // Check if any of the filières are already assigned
        $alreadyAssigned = $filieres->filter(function ($filiere) {
            return $filiere->coordonnateurs->count() > 0;
        });

        if ($alreadyAssigned->isNotEmpty()) {
            return back()->with('error', 'Certaines filières sont déjà assignées à d\'autres coordonnateurs.');
        }

        // Assign all matching filières
        foreach ($filieres as $filiere) {
            $user->filieres()->attach($filiere->id);
        }

        // Log the activity using Laravel's built-in logging
        Log::info('Filières assignées au coordonnateur', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'filieres' => $filieres->pluck('nom')->toArray(),
            'assigned_by' => Auth::user()->name
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Les filières ont été assignées avec succès.');
    }
}
