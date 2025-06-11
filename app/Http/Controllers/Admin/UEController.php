<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniteEnseignement;
use App\Models\Filiere;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UEController extends Controller
{
    // List all course units with filtering and statistics
    public function index(Request $request)
    {
        // Build query with filters
        $query = UniteEnseignement::with(['filiere', 'departement', 'responsable'])
                                 ->withCount(['affectations', 'notes', 'schedules']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%");
            });
        }

        // Semester filter
        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        // Department filter
        if ($request->filled('departement')) {
            $query->where('departement_id', $request->departement);
        }

        // Filiere filter
        if ($request->filled('filiere')) {
            $query->where('filiere_id', $request->filiere);
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'vacant') {
                $query->where('est_vacant', true);
            } elseif ($request->status === 'assigned') {
                $query->where('est_vacant', false);
            }
        }

        // Academic year filter - removed as column doesn't exist in unites_enseignement table

        // Order by semester and code
        $query->orderBy('semestre')->orderBy('code');

        // Paginate results
        $ues = $query->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => UniteEnseignement::count(),
            'vacant' => UniteEnseignement::where('est_vacant', true)->count(),
            'assigned' => UniteEnseignement::where('est_vacant', false)->count(),
            'by_semester' => UniteEnseignement::selectRaw('semestre, COUNT(*) as count')
                                            ->groupBy('semestre')
                                            ->pluck('count', 'semestre'),
            'total_hours' => UniteEnseignement::selectRaw('SUM(heures_cm + heures_td + heures_tp) as total')
                                            ->value('total') ?? 0,
        ];

        // Get filter options
        $filterOptions = [
            'departements' => Departement::orderBy('nom')->get(),
            'filieres' => Filiere::orderBy('nom')->get(),
            'semestres' => ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'],
            // Removed annees as annee_universitaire column doesn't exist in unites_enseignement table
        ];

        return view('admin.ues.index', compact('ues', 'stats', 'filterOptions'));
    }

    // Show UE creation form
    public function create()
    {
        $filieres = Filiere::with('departement')->orderBy('nom')->get();
        $departements = Departement::orderBy('nom')->get();
        $teachers = User::whereIn('role', ['enseignant', 'vacataire'])
                       ->orderBy('name')
                       ->get();

        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];

        return view('admin.ues.create', compact(
            'filieres', 'departements', 'teachers', 'semestres'
        ));
    }

    // Store new UE
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:unites_enseignement',
            'nom' => 'required|string|max:255',
            'heures_cm' => 'required|integer|min:0|max:100',
            'heures_td' => 'required|integer|min:0|max:100',
            'heures_tp' => 'required|integer|min:0|max:100',
            'semestre' => 'required|in:S1,S2,S3,S4,S5,S6',
            'groupes_td' => 'required|integer|min:0|max:20',
            'groupes_tp' => 'required|integer|min:0|max:20',
            'est_vacant' => 'boolean',
            'filiere_id' => 'required|exists:filieres,id',
            'departement_id' => 'required|exists:departements,id',
            'responsable_id' => 'nullable|exists:users,id',
            'specialite' => 'nullable|string|max:255'
        ]);

        // Ensure at least some hours are specified
        if ($validated['heures_cm'] + $validated['heures_td'] + $validated['heures_tp'] == 0) {
            return back()->withErrors(['heures' => 'Au moins un type d\'heures (CM, TD, TP) doit être spécifié.'])
                        ->withInput();
        }

        $validated['est_vacant'] = $request->has('est_vacant');

        $ue = UniteEnseignement::create($validated);

        // Log the UE creation activity
        \App\Models\Activity::log(
            'create',
            'ue_created',
            "Nouvelle UE créée: {$ue->code} - {$ue->nom}",
            $ue,
            [
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'semestre' => $ue->semestre,
                'heures_cm' => $ue->heures_cm,
                'heures_td' => $ue->heures_td,
                'heures_tp' => $ue->heures_tp,
                'created_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.ues.index')
                        ->with('success', 'Unité d\'enseignement créée avec succès');
    }

    // Show UE details
    public function show(UniteEnseignement $ue)
    {
        $ue->load(['filiere', 'departement', 'responsable', 'affectations.user', 'notes', 'schedules']);

        return view('admin.ues.show', compact('ue'));
    }

    // Show UE edit form
    public function edit(UniteEnseignement $ue)
    {
        $filieres = Filiere::with('departement')->orderBy('nom')->get();
        $departements = Departement::orderBy('nom')->get();
        $teachers = User::whereIn('role', ['enseignant', 'vacataire'])
                       ->orderBy('name')
                       ->get();

        $semestres = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6'];

        return view('admin.ues.edit', compact(
            'ue', 'filieres', 'departements', 'teachers',
            'semestres'
        ));
    }

    // Update UE
    public function update(Request $request, UniteEnseignement $ue)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:100', Rule::unique('unites_enseignement')->ignore($ue->id)],
            'nom' => 'required|string|max:255',
            'heures_cm' => 'required|integer|min:0|max:100',
            'heures_td' => 'required|integer|min:0|max:100',
            'heures_tp' => 'required|integer|min:0|max:100',
            'semestre' => 'required|in:S1,S2,S3,S4,S5,S6',
            'groupes_td' => 'required|integer|min:0|max:20',
            'groupes_tp' => 'required|integer|min:0|max:20',
            'est_vacant' => 'boolean',
            'filiere_id' => 'required|exists:filieres,id',
            'departement_id' => 'required|exists:departements,id',
            'responsable_id' => 'nullable|exists:users,id',
            'specialite' => 'nullable|string|max:255'
        ]);

        // Ensure at least some hours are specified
        if ($validated['heures_cm'] + $validated['heures_td'] + $validated['heures_tp'] == 0) {
            return back()->withErrors(['heures' => 'Au moins un type d\'heures (CM, TD, TP) doit être spécifié.'])
                        ->withInput();
        }

        $validated['est_vacant'] = $request->has('est_vacant');

        $oldData = $ue->toArray();
        $ue->update($validated);

        // Log the UE update activity
        \App\Models\Activity::log(
            'update',
            'ue_updated',
            "UE modifiée: {$ue->code} - {$ue->nom}",
            $ue,
            [
                'ue_code' => $ue->code,
                'ue_nom' => $ue->nom,
                'old_data' => $oldData,
                'new_data' => $validated,
                'updated_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.ues.index')
                        ->with('success', 'Unité d\'enseignement mise à jour avec succès');
    }

    // Delete UE
    public function destroy(UniteEnseignement $ue)
    {
        // Check if UE has related data
        if ($ue->affectations()->count() > 0) {
            return redirect()->route('admin.ues.index')
                           ->with('error', 'Impossible de supprimer cette UE car elle a des affectations associées.');
        }

        if ($ue->notes()->count() > 0) {
            return redirect()->route('admin.ues.index')
                           ->with('error', 'Impossible de supprimer cette UE car elle a des notes associées.');
        }

        // Log the UE deletion activity before deleting
        \App\Models\Activity::log(
            'delete',
            'ue_deleted',
            "UE supprimée: {$ue->code} - {$ue->nom}",
            $ue,
            [
                'deleted_ue_code' => $ue->code,
                'deleted_ue_nom' => $ue->nom,
                'deleted_ue_semestre' => $ue->semestre,
                'deleted_by' => auth()->user()->name
            ]
        );

        $ue->delete();

        return redirect()->route('admin.ues.index')
                        ->with('success', 'Unité d\'enseignement supprimée avec succès');
    }

    // Export UEs to CSV
    public function export(Request $request)
    {
        $query = UniteEnseignement::with(['filiere', 'departement', 'responsable']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        if ($request->filled('departement')) {
            $query->where('departement_id', $request->departement);
        }

        if ($request->filled('filiere')) {
            $query->where('filiere_id', $request->filiere);
        }

        if ($request->filled('status')) {
            if ($request->status === 'vacant') {
                $query->where('est_vacant', true);
            } elseif ($request->status === 'assigned') {
                $query->where('est_vacant', false);
            }
        }

        $ues = $query->orderBy('semestre')->orderBy('code')->get();

        $filename = 'unites_enseignement_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($ues) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'Code',
                'Nom',
                'Semestre',
                'Spécialité',
                'Niveau (Auto)',
                'Heures CM',
                'Heures TD',
                'Heures TP',
                'Total heures',
                'Groupes TD',
                'Groupes TP',
                'Filière d\'enseignement',
                'Département gestionnaire',
                'Responsable',
                'Statut',
                'Date de création'
            ]);

            // Add data rows
            foreach ($ues as $ue) {
                fputcsv($file, [
                    $ue->code,
                    $ue->nom,
                    $ue->semestre,
                    $ue->specialite ?? 'Non spécifiée',
                    $ue->niveau,
                    $ue->heures_cm,
                    $ue->heures_td,
                    $ue->heures_tp,
                    $ue->total_hours,
                    $ue->groupes_td,
                    $ue->groupes_tp,
                    $ue->filiere ? $ue->filiere->nom : 'Non assignée',
                    $ue->departement ? $ue->departement->nom : 'Non assigné',
                    $ue->responsable ? $ue->responsable->name : 'Non assigné',
                    $ue->est_vacant ? 'Vacant' : 'Assigné',
                    $ue->created_at ? $ue->created_at->format('d/m/Y H:i') : 'Non disponible'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Get UE statistics for API/AJAX calls
    public function getStats()
    {
        $stats = [
            'total' => UniteEnseignement::count(),
            'vacant' => UniteEnseignement::where('est_vacant', true)->count(),
            'assigned' => UniteEnseignement::where('est_vacant', false)->count(),
            'by_semester' => UniteEnseignement::selectRaw('semestre, COUNT(*) as count')
                                            ->groupBy('semestre')
                                            ->pluck('count', 'semestre'),
            'total_hours' => UniteEnseignement::selectRaw('SUM(heures_cm + heures_td + heures_tp) as total')
                                            ->value('total') ?? 0,
        ];

        return response()->json($stats);
    }
}
