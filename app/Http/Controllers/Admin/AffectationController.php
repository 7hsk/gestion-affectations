<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\UniteEnseignement;
use App\Models\User;
use Illuminate\Http\Request;

class AffectationController extends Controller
{
    // List all assignments
    public function index()
    {
        $affectations = Affectation::with(['ue', 'user'])->get();
        return view('admin.affectations.index', compact('affectations'));
    }

    // Show assignment creation form
    public function create()
    {
        $ues = UniteEnseignement::all();
        $teachers = User::whereIn('role', ['enseignant', 'vacataire'])->get();
        return view('admin.affectations.create', compact('ues', 'teachers'));
    }

    // Store new assignment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'user_id' => 'required|exists:users,id',
            'type_seance' => 'required|integer',
            'annee_universitaire' => 'required|string|max:20'
        ]);

        $affectation = Affectation::create($validated);

        // Log the affectation creation activity
        \App\Models\Activity::log(
            'create',
            'affectation_created',
            "Nouvelle affectation créée: {$affectation->user->name} - {$affectation->uniteEnseignement->code}",
            $affectation,
            [
                'user_name' => $affectation->user->name,
                'ue_code' => $affectation->uniteEnseignement->code,
                'type_seance' => $affectation->type_seance,
                'annee_universitaire' => $affectation->annee_universitaire,
                'created_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.affectations.index')->with('success', 'Assignment created successfully');
    }

    // Validate an assignment
    public function validateAssignment(Affectation $affectation)
    {
        $affectation->update(['validee' => true]);

        // Log the affectation validation activity
        \App\Models\Activity::log(
            'approve',
            'affectation_validated',
            "Affectation validée: {$affectation->user->name} - {$affectation->uniteEnseignement->code}",
            $affectation,
            [
                'user_name' => $affectation->user->name,
                'ue_code' => $affectation->uniteEnseignement->code,
                'validated_by' => auth()->user()->name
            ]
        );

        return redirect()->route('admin.affectations.index')->with('success', 'Assignment validated successfully');
    }

    // Bulk assign teachers to courses
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'assignments' => 'required|array',
            'assignments.*.ue_id' => 'required|exists:unites_enseignement,id',
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.type_seance' => 'required|integer',
            'annee_universitaire' => 'required|string|max:20'
        ]);

        foreach ($validated['assignments'] as $assignment) {
            Affectation::create([
                'ue_id' => $assignment['ue_id'],
                'user_id' => $assignment['user_id'],
                'type_seance' => $assignment['type_seance'],
                'annee_universitaire' => $validated['annee_universitaire']
            ]);
        }

        return redirect()->route('admin.affectations.index')->with('success', 'Bulk assignments created successfully');
    }

    // Delete assignment
    public function destroy(Affectation $affectation)
    {
        $affectation->delete();
        return redirect()->route('admin.affectations.index')->with('success', 'Assignment deleted successfully');
    }
}
