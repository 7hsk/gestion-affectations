<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FiliereController extends Controller
{
    // List all programs
    public function index()
    {
        $filieres = Filiere::with('departement')->get();
        return view('admin.filieres.index', compact('filieres'));
    }

    // Show program creation form
    public function create()
    {
        $departements = Departement::all();
        return view('admin.filieres.create', compact('departements'));
    }

    // Store new program
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'departement_id' => 'required|exists:departements,id'
        ]);

        Filiere::create($validated);
        return redirect()->route('admin.filieres.index')->with('success', 'Program created successfully');
    }

    // Show program edit form
    public function edit(Filiere $filiere)
    {
        $departements = Departement::all();
        return view('admin.filieres.edit', compact('filiere', 'departements'));
    }

    // Update program
    public function update(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'departement_id' => 'required|exists:departements,id'
        ]);

        $filiere->update($validated);
        return redirect()->route('admin.filieres.index')->with('success', 'Program updated successfully');
    }

    // Delete program
    public function destroy(Filiere $filiere)
    {
        $filiere->delete();
        return redirect()->route('admin.filieres.index')->with('success', 'Program deleted successfully');
    }

    // Assign coordinator to program
    public function assignCoordinator(Request $request, Filiere $filiere)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        // Remove existing coordinators
        $filiere->coordonnateurs()->detach();

        // Add new coordinator
        $filiere->coordonnateurs()->attach($validated['user_id']);

        // Update user role if needed
        $user = User::find($validated['user_id']);
        if ($user->role !== 'coordonnateur') {
            $user->update(['role' => 'coordonnateur']);
        }

        return redirect()->route('admin.filieres.index')->with('success', 'Coordinator assigned successfully');
    }
}
