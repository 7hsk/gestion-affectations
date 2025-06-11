<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use App\Models\Filiere;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
    public function dashboard()
    {
        // Get comprehensive statistics
        $stats = [
            'users' => [
                'total' => User::count(),
                'by_role' => [
                    'admin' => User::where('role', 'admin')->count(),
                    'chef' => User::where('role', 'chef')->count(),
                    'coordonnateur' => User::where('role', 'coordonnateur')->count(),
                    'enseignant' => User::where('role', 'enseignant')->count(),
                    'vacataire' => User::where('role', 'vacataire')->count(),
                ],
                'recent' => User::latest()->take(5)->get(),
                'active_today' => User::whereDate('updated_at', today())->count(),
                'active_this_week' => User::where('updated_at', '>=', now()->subWeek())->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count()
            ],
            'departements' => [
                'total' => Departement::count(),
                'with_users' => Departement::has('users')->count(),
                'without_chef' => Departement::whereDoesntHave('users', function($q) {
                    $q->where('role', 'chef');
                })->count(),
                'recent' => Departement::latest()->take(3)->get(),
                'avg_users_per_dept' => round(User::count() / max(Departement::count(), 1), 1)
            ],

            'system' => [
                'total_ues' => \App\Models\UniteEnseignement::count(),
                'vacant_ues' => \App\Models\UniteEnseignement::where('est_vacant', true)->count(),
                'total_affectations' => \App\Models\Affectation::count(),
                'pending_affectations' => \App\Models\Affectation::where('validee', 'en_attente')->count(),
                'validated_affectations' => \App\Models\Affectation::where('validee', 'valide')->count(),
                'rejected_affectations' => \App\Models\Affectation::where('validee', 'refuse')->count(),
                'total_schedules' => \App\Models\Schedule::count(),
                'total_notes' => \App\Models\Note::count()
            ]
        ];

        // Get recent activities with real data
        $recentActivities = $this->getRecentActivities();

        // Get department statistics with detailed info
        $departmentStats = Departement::withCount([
            'users',
            'unitesEnseignement',
            'users as enseignants_count' => function($q) {
                $q->where('role', 'enseignant');
            },
            'users as chefs_count' => function($q) {
                $q->where('role', 'chef');
            }
        ])->get();

        // Get monthly user registration trend
        $monthlyUserTrend = $this->getMonthlyUserTrend();

        // Get affectation status distribution
        $affectationStats = $this->getAffectationStats();

        return view('admin.dashboard', compact(
            'stats',
            'recentActivities',
            'departmentStats',
            'monthlyUserTrend',
            'affectationStats'
        ));
    }

    // Helper method to get recent activities
    private function getRecentActivities()
    {
        $activities = collect();

        // Recent users
        $recentUsers = User::latest()->take(3)->get();
        foreach ($recentUsers as $user) {
            $activities->push([
                'type' => 'user_created',
                'message' => "Nouvel utilisateur créé: {$user->name} ({$user->role})",
                'time' => $user->created_at ? $user->created_at->diffForHumans() : 'Date inconnue',
                'icon' => 'fas fa-user-plus',
                'color' => 'success'
            ]);
        }

        // Recent departments
        $recentDepartments = Departement::latest()->take(2)->get();
        foreach ($recentDepartments as $dept) {
            $activities->push([
                'type' => 'department_created',
                'message' => "Nouveau département: {$dept->nom}",
                'time' => $dept->created_at ? $dept->created_at->diffForHumans() : 'Date inconnue',
                'icon' => 'fas fa-building',
                'color' => 'info'
            ]);
        }

        // Recent affectations
        $recentAffectations = \App\Models\Affectation::with(['user', 'uniteEnseignement'])
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentAffectations as $affectation) {
            $status = $affectation->validee;
            $color = $status === 'valide' ? 'success' : ($status === 'refuse' ? 'danger' : 'warning');
            $icon = $status === 'valide' ? 'fas fa-check' : ($status === 'refuse' ? 'fas fa-times' : 'fas fa-clock');

            $activities->push([
                'type' => 'affectation_updated',
                'message' => "Affectation {$status}: {$affectation->user->name} - {$affectation->uniteEnseignement->code}",
                'time' => $affectation->updated_at ? $affectation->updated_at->diffForHumans() : 'Date inconnue',
                'icon' => $icon,
                'color' => $color
            ]);
        }

        return $activities->sortByDesc('time')->take(5)->values();
    }

    // Helper method to get monthly user registration trend
    private function getMonthlyUserTrend()
    {
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $data[] = User::whereYear('created_at', $date->year)
                         ->whereMonth('created_at', $date->month)
                         ->count();
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    // Helper method to get affectation statistics
    private function getAffectationStats()
    {
        return [
            'en_attente' => \App\Models\Affectation::where('validee', 'en_attente')->count(),
            'valide' => \App\Models\Affectation::where('validee', 'valide')->count(),
            'refuse' => \App\Models\Affectation::where('validee', 'refuse')->count(),
        ];
    }

    public function manageUsers()
    {
        $users = User::with('departement')->get();
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $departements = Departement::all();
        $roles = ['admin', 'chef', 'coordonnateur', 'enseignant', 'vacataire', 'etudiant'];
        $specialites = [
            'Informatique',
            'Mathématiques',
            'Physique',
            'Chimie',
            'Génie Civil',
            'Génie Mécanique',
            'Électronique',
            'Télécommunications',
            'Réseaux',
            'Intelligence Artificielle',
            'Base de Données',
            'Développement Web',
            'Sécurité Informatique',
            'Systèmes Embarqués'
        ];

        return view('admin.users.create', compact('departements', 'roles', 'specialites'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,chef,coordonnateur,enseignant,vacataire,etudiant',
            'specialite' => 'nullable|array',
            'specialite.*' => 'string',
            'filiere_base' => 'nullable|array',
            'filiere_base.*' => 'string|in:GI,ID,GC,GM',
            'departement_id' => 'nullable|exists:departements,id',
        ]);

        // Convert specialite array to string for storage
        if (isset($validated['specialite'])) {
            $validated['specialite'] = implode(',', $validated['specialite']);
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Handle filiere assignments for coordonnateur
        if ($validated['role'] === 'coordonnateur' && isset($validated['filiere_base'])) {
            foreach ($validated['filiere_base'] as $filiereBase) {
                // Get all filieres that start with the base name (e.g., GI1, GI2, GI3 for GI)
                $filieres = \App\Models\Filiere::where('nom', 'LIKE', $filiereBase . '%')->get();

                foreach ($filieres as $filiere) {
                    DB::table('coordonnateurs_filieres')->insert([
                        'user_id' => $user->id,
                        'filiere_id' => $filiere->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    // API endpoint for real-time dashboard updates
    public function getDashboardStats()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'by_role' => [
                    'admin' => User::where('role', 'admin')->count(),
                    'chef' => User::where('role', 'chef')->count(),
                    'coordonnateur' => User::where('role', 'coordonnateur')->count(),
                    'enseignant' => User::where('role', 'enseignant')->count(),
                    'vacataire' => User::where('role', 'vacataire')->count(),
                ],
                'active_today' => User::whereDate('updated_at', today())->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count()
            ],
            'departements' => [
                'total' => Departement::count(),
                'with_users' => Departement::has('users')->count(),
            ],

            'system' => [
                'total_ues' => \App\Models\UniteEnseignement::count(),
                'pending_affectations' => \App\Models\Affectation::where('validee', 'en_attente')->count(),
                'validated_affectations' => \App\Models\Affectation::where('validee', 'valide')->count(),
            ]
        ];

        return response()->json($stats);
    }

    // API endpoint for recent activities
    public function getRecentActivitiesApi()
    {
        $activities = $this->getRecentActivities();
        return response()->json($activities);
    }

    // Method to create notifications for important events
    public static function createNotification($userId, $title, $message)
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'is_read' => false
        ]);
    }

    // Method to notify all admins
    public static function notifyAdmins($title, $message)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            self::createNotification($admin->id, $title, $message);
        }
    }

    // Method to notify department heads
    public static function notifyDepartmentHeads($title, $message, $departmentId = null)
    {
        $query = User::where('role', 'chef');
        if ($departmentId) {
            $query->where('departement_id', $departmentId);
        }

        $chefs = $query->get();
        foreach ($chefs as $chef) {
            self::createNotification($chef->id, $title, $message);
        }
    }

    // Activities view page
    public function activities(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        // Get all activities with pagination
        $activities = $this->getAllActivities($filter, $search, $perPage);

        // Get activity type counts for filters
        $activityCounts = $this->getActivityCounts();

        return view('admin.activities.index', compact('activities', 'activityCounts', 'filter', 'search'));
    }

    // Helper method to get all activities with pagination and filtering
    private function getAllActivities($filter = 'all', $search = null, $perPage = 10)
    {
        $allActivities = collect();

        // 1. Get real activities from database (Activity model) - THIS IS THE PRIMARY SOURCE
        $dbActivities = \App\Models\Activity::with(['user', 'subject'])
            ->when($filter !== 'all', function ($query) use ($filter) {
                return $query->where('type', $filter);
            })
            ->when($search, function ($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('action', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->take(1000) // Get more activities from the primary source
            ->get();

        // Transform database activities
        foreach ($dbActivities as $activity) {
            $allActivities->push([
                'type' => $activity->type,
                'action' => $activity->action,
                'message' => $activity->description,
                'time' => $activity->time,
                'date' => $activity->date,
                'timestamp' => $activity->created_at->timestamp,
                'icon' => $activity->icon,
                'color' => $activity->color,
                'user' => $activity->user ? $activity->user->name : 'Système',
                'user_role' => $activity->user ? $activity->user->role : 'system',
                'ip_address' => $activity->ip_address,
                'user_agent' => $activity->user_agent,
                'source' => 'database',
                'details' => array_merge($activity->properties ?? [], [
                    'activity_id' => $activity->id,
                    'subject_type' => $activity->subject_type,
                    'subject_id' => $activity->subject_id,
                    'performed_by' => $activity->user ? $activity->user->name : 'Système',
                    'ip_address' => $activity->ip_address,
                    'timestamp' => $activity->created_at->format('d/m/Y H:i:s')
                ])
            ]);
        }

        // 2. Get recent users (like in dashboard)
        $recentUsers = User::latest()->get();
        foreach ($recentUsers as $user) {
            $activity = [
                'type' => 'create',
                'action' => 'user_created',
                'message' => "Nouvel utilisateur créé: {$user->name} ({$user->role})",
                'time' => $user->created_at ? $user->created_at->diffForHumans() : 'Date inconnue',
                'date' => $user->created_at ? $user->created_at->format('d/m/Y') : 'Date inconnue',
                'timestamp' => $user->created_at ? $user->created_at->timestamp : 0,
                'icon' => 'fas fa-user-plus',
                'color' => 'success',
                'user' => 'Système',
                'user_role' => 'system',
                'ip_address' => 'N/A',
                'user_agent' => 'N/A',
                'source' => 'system',
                'details' => [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'departement' => $user->departement ? $user->departement->nom : 'N/A',
                    'specialite' => $user->specialite,
                    'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'Date inconnue'
                ]
            ];

            // Apply filters
            if ($filter === 'all' || $filter === 'create') {
                if (!$search || stripos($activity['message'], $search) !== false || stripos($user->name, $search) !== false) {
                    $allActivities->push($activity);
                }
            }
        }

        // 3. Get recent departments (like in dashboard)
        $recentDepartments = Departement::latest()->get();
        foreach ($recentDepartments as $dept) {
            $activity = [
                'type' => 'create',
                'action' => 'department_created',
                'message' => "Nouveau département créé: {$dept->nom}",
                'time' => $dept->created_at ? $dept->created_at->diffForHumans() : 'Date inconnue',
                'date' => $dept->created_at ? $dept->created_at->format('d/m/Y') : 'Date inconnue',
                'timestamp' => $dept->created_at ? $dept->created_at->timestamp : 0,
                'icon' => 'fas fa-building',
                'color' => 'info',
                'user' => 'Système',
                'user_role' => 'system',
                'ip_address' => 'N/A',
                'user_agent' => 'N/A',
                'source' => 'system',
                'details' => [
                    'department_name' => $dept->nom,
                    'department_description' => $dept->description,
                    'created_at' => $dept->created_at ? $dept->created_at->format('d/m/Y H:i:s') : 'Date inconnue'
                ]
            ];

            // Apply filters
            if ($filter === 'all' || $filter === 'create') {
                if (!$search || stripos($activity['message'], $search) !== false || stripos($dept->nom, $search) !== false) {
                    $allActivities->push($activity);
                }
            }
        }

        // 4. Get recent affectations (like in dashboard)
        $recentAffectations = \App\Models\Affectation::with(['user', 'uniteEnseignement'])
            ->latest()
            ->get();

        foreach ($recentAffectations as $affectation) {
            $status = $affectation->validee;
            $color = $status === 'valide' ? 'success' : ($status === 'refuse' ? 'danger' : 'warning');
            $icon = $status === 'valide' ? 'fas fa-check' : ($status === 'refuse' ? 'fas fa-times' : 'fas fa-clock');
            $type = $status === 'valide' ? 'approve' : ($status === 'refuse' ? 'reject' : 'update');

            $activity = [
                'type' => $type,
                'action' => 'affectation_' . $status,
                'message' => "Affectation {$status}: {$affectation->user->name} - {$affectation->uniteEnseignement->code}",
                'time' => $affectation->updated_at ? $affectation->updated_at->diffForHumans() : 'Date inconnue',
                'date' => $affectation->updated_at ? $affectation->updated_at->format('d/m/Y') : 'Date inconnue',
                'timestamp' => $affectation->updated_at ? $affectation->updated_at->timestamp : 0,
                'icon' => $icon,
                'color' => $color,
                'user' => $affectation->user->name,
                'user_role' => $affectation->user->role,
                'ip_address' => 'N/A',
                'user_agent' => 'N/A',
                'source' => 'system',
                'details' => [
                    'affectation_id' => $affectation->id,
                    'user_name' => $affectation->user->name,
                    'ue_code' => $affectation->uniteEnseignement->code,
                    'ue_nom' => $affectation->uniteEnseignement->nom,
                    'type_seance' => $affectation->type_seance,
                    'status' => $status,
                    'annee_universitaire' => $affectation->annee_universitaire,
                    'updated_at' => $affectation->updated_at->format('d/m/Y H:i:s')
                ]
            ];

            // Apply filters
            if ($filter === 'all' || $filter === $type) {
                if (!$search || stripos($activity['message'], $search) !== false ||
                    stripos($affectation->user->name, $search) !== false ||
                    stripos($affectation->uniteEnseignement->code, $search) !== false) {
                    $allActivities->push($activity);
                }
            }
        }

        // 5. Get UE activities
        $recentUEs = \App\Models\UniteEnseignement::with(['departement', 'filiere'])->latest()->get();
        foreach ($recentUEs as $ue) {
            $activity = [
                'type' => 'create',
                'action' => 'ue_created',
                'message' => "Nouvelle UE créée: {$ue->code} - {$ue->nom}",
                'time' => $ue->created_at ? $ue->created_at->diffForHumans() : 'Date inconnue',
                'date' => $ue->created_at ? $ue->created_at->format('d/m/Y') : 'Date inconnue',
                'timestamp' => $ue->created_at ? $ue->created_at->timestamp : 0,
                'icon' => 'fas fa-book',
                'color' => 'primary',
                'user' => 'Système',
                'user_role' => 'system',
                'ip_address' => 'N/A',
                'user_agent' => 'N/A',
                'source' => 'system',
                'details' => [
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'semestre' => $ue->semestre,
                    'heures_cm' => $ue->heures_cm,
                    'heures_td' => $ue->heures_td,
                    'heures_tp' => $ue->heures_tp,
                    'est_vacant' => $ue->est_vacant ? 'Oui' : 'Non',
                    'departement' => $ue->departement ? $ue->departement->nom : 'N/A',
                    'created_at' => $ue->created_at ? $ue->created_at->format('d/m/Y H:i:s') : 'Date inconnue'
                ]
            ];

            // Apply filters
            if ($filter === 'all' || $filter === 'create') {
                if (!$search || stripos($activity['message'], $search) !== false ||
                    stripos($ue->code, $search) !== false || stripos($ue->nom, $search) !== false) {
                    $allActivities->push($activity);
                }
            }
        }

        // Sort all activities by timestamp (newest first)
        $allActivities = $allActivities->sortByDesc('timestamp');

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = $allActivities->slice($offset, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allActivities->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return $paginator;
    }

    // Get activity counts for filters
    private function getActivityCounts()
    {
        // Count activities from Activity model
        $activityCounts = [
            'auth' => \App\Models\Activity::where('type', 'auth')->count(),
            'logout' => \App\Models\Activity::where('type', 'logout')->count(),
            'create' => \App\Models\Activity::where('type', 'create')->count(),
            'update' => \App\Models\Activity::where('type', 'update')->count(),
            'delete' => \App\Models\Activity::where('type', 'delete')->count(),
            'approve' => \App\Models\Activity::where('type', 'approve')->count(),
            'reject' => \App\Models\Activity::where('type', 'reject')->count(),
            'upload' => \App\Models\Activity::where('type', 'upload')->count(),
            'import' => \App\Models\Activity::where('type', 'import')->count(),
            'export' => \App\Models\Activity::where('type', 'export')->count(),
            'system' => \App\Models\Activity::where('type', 'system')->count(),
            'security' => \App\Models\Activity::where('type', 'security')->count(),
            'notification' => \App\Models\Activity::where('type', 'notification')->count(),
            'email' => \App\Models\Activity::where('type', 'email')->count(),
        ];

        // Add counts from other sources (same as in getAllActivities method)
        $activityCounts['create'] += \App\Models\User::count(); // User creations
        $activityCounts['create'] += \App\Models\Departement::count(); // Department creations
        $activityCounts['create'] += \App\Models\UniteEnseignement::count(); // UE creations

        // Add affectation counts
        $validatedAffectations = \App\Models\Affectation::where('validee', 'valide')->count();
        $rejectedAffectations = \App\Models\Affectation::where('validee', 'rejete')->count();
        $pendingAffectations = \App\Models\Affectation::where('validee', 'en_attente')->count();

        $activityCounts['approve'] += $validatedAffectations;
        $activityCounts['reject'] += $rejectedAffectations;
        $activityCounts['update'] += $pendingAffectations;

        // Calculate total
        $activityCounts['all'] = array_sum($activityCounts);

        return $activityCounts;
    }
}
