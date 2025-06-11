<?php

namespace App\Http\Controllers\Admin\vacataire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{User, UniteEnseignement, Affectation, Schedule, Note, Filiere};
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class VacataireController extends Controller
{
    // No middleware in constructor - handled by routes

    // Dashboard principal du vacataire
    public function dashboard()
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        // Statistiques principales
        $stats = [
            'ues_assignees' => $this->getAssignedUEsCount($vacataire->id),
            'heures_totales' => $this->getTotalHours($vacataire->id),
            'notes_saisies' => $this->getNotesCount($vacataire->id),
            'emploi_du_temps' => $this->getScheduleCount($vacataire->id)
        ];

        // UEs assignées récentes
        $uesAssignees = Affectation::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Emploi du temps de la semaine
        $emploiDuTemps = Schedule::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->with(['uniteEnseignement'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        // Notes récentes
        $notesRecentes = Note::whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id);
        })
            ->with(['uniteEnseignement', 'etudiant'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('vacataire.dashboard', compact(
            'stats',
            'uesAssignees',
            'emploiDuTemps',
            'notesRecentes'
        ));
    }

    // Liste des UEs assignées au vacataire
    public function unitesEnseignement(Request $request)
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $query = Affectation::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere']);

        // Filtres
        if ($request->filled('filiere')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('filiere_id', $request->filiere);
            });
        }

        if ($request->filled('semestre')) {
            $query->whereHas('uniteEnseignement', function ($q) use ($request) {
                $q->where('semestre', $request->semestre);
            });
        }

        $affectations = $query->paginate(12);

        // Filières disponibles pour le filtre
        $filieres = Filiere::whereHas('unitesEnseignement.affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id);
        })->get();

        return view('vacataire.unites-enseignement', compact('affectations', 'filieres'));
    }

    // Détails d'une UE
    public function ueDetails($id)
    {
        $vacataire = Auth::user();

        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $id)
            ->where('validee', 'valide')
            ->with(['uniteEnseignement.filiere'])
            ->firstOrFail();

        $ue = $affectation->uniteEnseignement;

        // Emploi du temps pour cette UE
        $schedules = Schedule::where('user_id', $vacataire->id)
            ->where('ue_id', $id)
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get();

        // Notes pour cette UE
        $notes = Note::where('ue_id', $id)
            ->with(['etudiant'])
            ->orderBy('etudiant_id')
            ->get();

        return view('vacataire.ue-details', compact('ue', 'affectation', 'schedules', 'notes'));
    }

    // Gestion des notes
    public function notes(Request $request)
    {
        $vacataire = Auth::user();

        $query = Note::whereHas('uniteEnseignement.affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id);
        })->with(['uniteEnseignement.filiere', 'etudiant']);

        // Filtres
        if ($request->filled('ue_id')) {
            $query->where('ue_id', $request->ue_id);
        }

        if ($request->filled('session')) {
            $query->where('session', $request->session);
        }

        $notes = $query->orderBy('ue_id')->orderBy('etudiant_id')->paginate(20);

        // UEs assignées pour le filtre
        $uesAssignees = UniteEnseignement::whereHas('affectations', function ($q) use ($vacataire) {
            $q->where('user_id', $vacataire->id)->where('validee', 'valide');
        })->get();

        return view('vacataire.notes', compact('notes', 'uesAssignees'));
    }

    // Emploi du temps du vacataire
    public function emploiDuTemps()
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $schedules = Schedule::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->with(['uniteEnseignement.filiere'])
            ->get();

        // Organiser par jour et heure
        $emploiDuTemps = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $heures = ['08:30-10:30', '10:30-12:30', '14:30-16:30', '16:30-18:30'];

        foreach ($jours as $jour) {
            foreach ($heures as $heure) {
                $emploiDuTemps[$jour][$heure] = $schedules->filter(function ($schedule) use ($jour, $heure) {
                    $scheduleTime = substr($schedule->heure_debut, 0, 5) . '-' . substr($schedule->heure_fin, 0, 5);
                    return $schedule->jour_semaine === $jour && $scheduleTime === $heure;
                });
            }
        }

        return view('vacataire.emploi-du-temps', compact('emploiDuTemps', 'jours', 'heures'));
    }

    // Export Emploi du Temps to PDF
    public function exportEmploiDuTemps(Request $request)
    {
        $vacataire = Auth::user();
        $currentYear = date('Y') . '-' . (date('Y') + 1);

        $schedules = Schedule::where('user_id', $vacataire->id)
            ->where('annee_universitaire', $currentYear)
            ->with(['uniteEnseignement.filiere'])
            ->get();

        // Organize by day and time, similar to the view method
        $emploiDuTemps = [];
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        $heures = ['08:30-10:30', '10:30-12:30', '14:30-16:30', '16:30-18:30'];

        foreach ($jours as $jour) {
            foreach ($heures as $heure) {
                $emploiDuTemps[$jour][$heure] = $schedules->filter(function ($schedule) use ($jour, $heure) {
                    $scheduleTime = substr($schedule->heure_debut, 0, 5) . '-' . substr($schedule->heure_fin, 0, 5);
                    return $schedule->jour_semaine === $jour && $scheduleTime === $heure;
                });
            }
        }

        $pdf = Pdf::loadView('vacataire.exports.emploi-du-temps', [
            'emploiDuTemps' => $emploiDuTemps,
            'jours' => $jours,
            'heures' => $heures,
            'vacataire' => $vacataire,
            'year' => $currentYear
        ]);

        return $pdf->download("emploi_du_temps_{$vacataire->name}_{$currentYear}.pdf");
    }

    // Méthodes utilitaires privées
    private function getAssignedUEsCount($userId)
    {
        return Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->count();
    }

    private function getTotalHours($userId)
    {
        return Affectation::where('user_id', $userId)
            ->where('validee', 'valide')
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->join('unites_enseignement', 'affectations.ue_id', '=', 'unites_enseignement.id')
            ->sum(DB::raw('unites_enseignement.heures_cm + unites_enseignement.heures_td + unites_enseignement.heures_tp'));
    }

    private function getNotesCount($userId)
    {
        return Note::whereHas('uniteEnseignement.affectations', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->count();
    }

    private function getScheduleCount($userId)
    {
        return Schedule::where('user_id', $userId)
            ->where('annee_universitaire', date('Y') . '-' . (date('Y') + 1))
            ->count();
    }

    // Import notes from Excel/CSV
    public function importNotes(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
            'ue_id' => 'required|exists:unites_enseignement,id',
            'session' => 'required|in:normale,rattrapage'
        ]);

        $vacataire = Auth::user();

        // Verify vacataire is assigned to this UE
        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return back()->with('error', 'Vous n\'êtes pas autorisé à gérer les notes de cette UE.');
        }

        try {
            // Process file import (simplified for demo)
            $ue = \App\Models\UniteEnseignement::find($request->ue_id);

            // Log the notes import activity
            \App\Models\Activity::log(
                'import',
                'notes_imported_vacataire',
                "Import de notes par vacataire: {$vacataire->name} - {$ue->code} ({$request->session})",
                $ue,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'session_type' => $request->session,
                    'file_name' => $request->file('file')->getClientOriginalName(),
                    'file_size' => $request->file('file')->getSize(),
                    'department' => $ue->departement->nom ?? 'N/A',
                    'filiere' => $ue->filiere->nom ?? 'N/A'
                ]
            );

            return back()->with('success', 'Notes importées avec succès!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    // Store a single note
    public function storeNote(Request $request)
    {
        $request->validate([
            'ue_id' => 'required|exists:unites_enseignement,id',
            'matricule' => 'required|string',
            'nom_etudiant' => 'required|string',
            'note_normale' => 'nullable|numeric|min:0|max:20',
            'note_rattrapage' => 'nullable|numeric|min:0|max:20',
            'session' => 'required|in:normale,rattrapage'
        ]);

        $vacataire = Auth::user();

        // Verify vacataire is assigned to this UE
        $affectation = Affectation::where('user_id', $vacataire->id)
            ->where('ue_id', $request->ue_id)
            ->where('validee', 'valide')
            ->first();

        if (!$affectation) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        try {
            // Create or update note (simplified for demo)
            $ue = \App\Models\UniteEnseignement::find($request->ue_id);

            // Log the note creation activity
            \App\Models\Activity::log(
                'create',
                'note_created_vacataire',
                "Note créée par vacataire: {$vacataire->name} - {$ue->code} - {$request->nom_etudiant} ({$request->session})",
                $ue,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'ue_code' => $ue->code,
                    'ue_nom' => $ue->nom,
                    'etudiant_matricule' => $request->matricule,
                    'etudiant_nom' => $request->nom_etudiant,
                    'session_type' => $request->session,
                    'note_normale' => $request->note_normale,
                    'note_rattrapage' => $request->note_rattrapage,
                    'department' => $ue->departement->nom ?? 'N/A',
                    'filiere' => $ue->filiere->nom ?? 'N/A'
                ]
            );

            return response()->json(['success' => 'Note enregistrée avec succès!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'enregistrement'], 500);
        }
    }

    // Update an existing note
    public function updateNote(Request $request, $noteId)
    {
        $request->validate([
            'note_normale' => 'nullable|numeric|min:0|max:20',
            'note_rattrapage' => 'nullable|numeric|min:0|max:20',
        ]);

        $vacataire = Auth::user();

        // Find note and verify access
        $note = Note::whereHas('uniteEnseignement.affectations', function ($query) use ($vacataire) {
            $query->where('user_id', $vacataire->id)->where('validee', 'valide');
        })->findOrFail($noteId);

        try {
            $note->update($request->only(['note_normale', 'note_rattrapage']));

            // Log the note update activity
            \App\Models\Activity::log(
                'update',
                'note_updated_vacataire',
                "Note mise à jour par vacataire: {$vacataire->name} - {$note->uniteEnseignement->code} - " . ($note->etudiant->nom ?? 'Étudiant'),
                $note,
                [
                    'vacataire_name' => $vacataire->name,
                    'vacataire_email' => $vacataire->email,
                    'ue_code' => $note->uniteEnseignement->code,
                    'ue_nom' => $note->uniteEnseignement->nom,
                    'etudiant_nom' => $note->etudiant->nom ?? 'N/A',
                    'note_normale' => $request->note_normale,
                    'note_rattrapage' => $request->note_rattrapage,
                    'department' => $note->uniteEnseignement->departement->nom ?? 'N/A',
                    'filiere' => $note->uniteEnseignement->filiere->nom ?? 'N/A'
                ]
            );

            return response()->json(['success' => 'Note mise à jour avec succès!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour'], 500);
        }
    }
}
