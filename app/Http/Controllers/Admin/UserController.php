<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'Systèmes d’exploitation',
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

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
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
            'Systèmes d’exploitation',
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
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,chef,coordonnateur,enseignant,vacataire,etudiant',
            'departement_id' => 'nullable|exists:departements,id',
            'specialite' => 'nullable|array',
            'filiere_base' => 'nullable|array',
            'filiere_base.*' => 'string|in:GI,ID,GC,GM'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'departement_id' => $request->departement_id,
            'specialite' => $request->specialite ? implode(',', $request->specialite) : null
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $oldData = $user->toArray();
        $user->update($data);

        // Log the user update activity
        \App\Models\Activity::log(
            'update',
            'user_updated',
            "Utilisateur modifié: {$user->name} ({$user->role})",
            $user,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'old_data' => $oldData,
                'new_data' => $data,
                'updated_by' => auth()->user()->name
            ]
        );

        // Handle filiere assignments for coordonnateur
        if ($request->role === 'coordonnateur') {
            // Remove existing filiere assignments
            \Illuminate\Support\Facades\DB::table('coordonnateurs_filieres')
                ->where('user_id', $user->id)
                ->delete();

            // Add new filiere assignments
            if ($request->filled('filiere_base')) {
                foreach ($request->filiere_base as $filiereBase) {
                    $filieres = \App\Models\Filiere::where('nom', 'LIKE', $filiereBase . '%')->get();

                    foreach ($filieres as $filiere) {
                        \Illuminate\Support\Facades\DB::table('coordonnateurs_filieres')->insert([
                            'user_id' => $user->id,
                            'filiere_id' => $filiere->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Log the user deletion activity before deleting
        \App\Models\Activity::log(
            'delete',
            'user_deleted',
            "Utilisateur supprimé: {$user->name} ({$user->role})",
            $user,
            [
                'deleted_user_name' => $user->name,
                'deleted_user_email' => $user->email,
                'deleted_user_role' => $user->role,
                'deleted_by' => auth()->user()->name
            ]
        );

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
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
}
