<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departement;
use Illuminate\Http\Request;
use App\Models\User;

class DepartementController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // Build query with relationships and counts
        $query = Departement::withCount(['users', 'unitesEnseignement'])
                           ->with(['users' => function($q) {
                               $q->where('role', 'chef');
                           }]);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->having('users_count', '>', 0);
            } elseif ($request->status === 'inactive') {
                $query->having('users_count', '=', 0);
            }
        }

        // Order by name
        $query->orderBy('nom');

        $departements = $query->get();

        // Calculate statistics
        $stats = [
            'total' => $departements->count(),
            'active' => $departements->where('users_count', '>', 0)->count(),
            'total_users' => $departements->sum('users_count'),
            'total_ues' => $departements->sum('unites_enseignement_count'),
        ];

        return view('admin.departements.index', compact('departements', 'stats'));
    }

    public function create()
    {
        return view('admin.departements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $departement = Departement::create($request->all());

        // Log the department creation activity
        \App\Models\Activity::log(
            'create',
            'department_created',
            "Nouveau département créé: {$departement->nom}",
            $departement,
            [
                'department_name' => $departement->nom,
                'department_description' => $departement->description,
                'created_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.departements.index')
            ->with('success', 'Department created successfully');
    }

    public function updateChef(Request $request, Departement $departement)
    {
        $validated = $request->validate([
            'chef_id' => 'required|exists:users,id'
        ]);

        $departement->setChef($validated['chef_id']);

        return redirect()->route('admin.departements.edit', $departement)
            ->with('success', 'Department head updated successfully');
    }

    public function show(Departement $departement)
    {
        return view('admin.departements.show', compact('departement'));
    }

    public function edit(Departement $departement)
    {
        $users = User::where('departement_id', $departement->id)
            ->where('role', '!=', 'etudiant')
            ->get();

        return view('admin.departements.edit', compact('departement', 'users'));
    }

    public function update(Request $request, Departement $departement)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $oldData = $departement->toArray();
        $departement->update($request->all());

        // Log the department update activity
        \App\Models\Activity::log(
            'update',
            'department_updated',
            "Département modifié: {$departement->nom}",
            $departement,
            [
                'department_name' => $departement->nom,
                'old_data' => $oldData,
                'new_data' => $request->all(),
                'updated_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.departements.index')
            ->with('success', 'Department updated successfully');
    }


    public function destroy(Departement $departement)
    {
        // Check if department has users
        if ($departement->users()->count() > 0) {
            return redirect()->route('admin.departements.index')
                ->with('error', 'Cannot delete department with assigned users. Please reassign users first.');
        }

        // Log the department deletion activity before deleting
        \App\Models\Activity::log(
            'delete',
            'department_deleted',
            "Département supprimé: {$departement->nom}",
            $departement,
            [
                'deleted_department_name' => $departement->nom,
                'deleted_department_description' => $departement->description,
                'deleted_by' => auth()->user()->name
            ]
        );

        $departement->delete();

        return redirect()->route('admin.departements.index')
            ->with('success', 'Department deleted successfully');
    }

    // Export departments to CSV
    public function export(Request $request)
    {
        $query = Departement::withCount(['users', 'filieres', 'unitesEnseignement']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->having('users_count', '>', 0);
            } elseif ($request->status === 'inactive') {
                $query->having('users_count', '=', 0);
            }
        }

        $departements = $query->orderBy('nom')->get();

        $filename = 'departements_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($departements) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Description',
                'Nombre d\'utilisateurs',
                'Nombre d\'UEs',
                'Chef de département',
                'Date de création'
            ]);

            // Add data rows
            foreach ($departements as $dept) {
                $chef = $dept->users()->where('role', 'chef')->first();

                fputcsv($file, [
                    $dept->id,
                    $dept->nom,
                    $dept->description ?: 'Aucune description',
                    $dept->users_count,
                    $dept->unites_enseignement_count,
                    $chef ? $chef->name : 'Non assigné',
                    $dept->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Get department statistics for API/AJAX calls
    public function getStats()
    {
        $stats = [
            'total' => Departement::count(),
            'with_users' => Departement::has('users')->count(),
            'without_chef' => Departement::whereDoesntHave('users', function($q) {
                $q->where('role', 'chef');
            })->count(),
            'avg_users_per_dept' => round(User::count() / max(Departement::count(), 1), 1),
            'total_users' => User::count(),
            'total_ues' => \App\Models\UniteEnseignement::count()
        ];

        return response()->json($stats);
    }
}
