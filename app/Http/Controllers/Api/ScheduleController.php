<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Save a schedule slot
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'ue_id' => 'required|exists:unites_enseignement,id',
                'filiere_id' => 'required|exists:filieres,id',
                'jour_semaine' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
                'heure_debut' => 'required',
                'heure_fin' => 'required',
                'type_seance' => 'required|in:CM,TD,TP',
                'group_number' => 'nullable|integer|min:1|max:4',
                'semestre' => 'required|string',
                'annee_universitaire' => 'required|string'
            ]);

            // Convert time slots to actual times
            $timeSlots = [
                '08:30-10:30' => ['08:30:00', '10:30:00'],
                '10:30-12:30' => ['10:30:00', '12:30:00'],
                '14:30-16:30' => ['14:30:00', '16:30:00'],
                '16:30-18:30' => ['16:30:00', '18:30:00']
            ];

            $timeSlot = $validated['heure_debut'] . '-' . $validated['heure_fin'];
            if (!isset($timeSlots[$timeSlot])) {
                return response()->json(['error' => 'Invalid time slot'], 400);
            }

            $validated['heure_debut'] = $timeSlots[$timeSlot][0];
            $validated['heure_fin'] = $timeSlots[$timeSlot][1];

            // Check for conflicts before saving
            $conflict = $this->checkConflicts($validated);
            if ($conflict) {
                return response()->json(['error' => $conflict], 409);
            }

            // Get assigned teacher for this UE and type
            $affectation = Affectation::where('ue_id', $validated['ue_id'])
                ->where('validee', 'valide')
                ->where('type_seance', 'LIKE', '%' . $validated['type_seance'] . '%')
                ->first();

            if ($affectation) {
                $validated['user_id'] = $affectation->user_id;
            }

            $schedule = Schedule::create($validated);

            return response()->json([
                'success' => true,
                'schedule' => $schedule->load(['uniteEnseignement', 'user'])
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save schedule: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check for scheduling conflicts
     */
    private function checkConflicts($data)
    {
        // If no teacher assigned, no conflict possible
        if (!isset($data['user_id']) || !$data['user_id']) {
            return null;
        }

        // Check if teacher is already scheduled at this time
        $existingSchedule = Schedule::where('user_id', $data['user_id'])
            ->where('jour_semaine', $data['jour_semaine'])
            ->where('heure_debut', $data['heure_debut'])
            ->where('heure_fin', $data['heure_fin'])
            ->where('annee_universitaire', $data['annee_universitaire'])
            ->first();

        if ($existingSchedule) {
            $ue = UniteEnseignement::find($existingSchedule->ue_id);
            return "Cet enseignant est déjà assigné à {$ue->code} - {$ue->nom} à ce créneau.";
        }

        return null;
    }

    /**
     * Check conflicts before dropping (AJAX endpoint)
     */
    public function checkConflict(Request $request)
    {
        try {
            $validated = $request->validate([
                'ue_id' => 'required|exists:unites_enseignement,id',
                'jour_semaine' => 'required|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
                'time_slot' => 'required|string',
                'type_seance' => 'required|in:CM,TD,TP',
                'annee_universitaire' => 'required|string'
            ]);

            // Convert time slot to actual times
            $timeSlots = [
                '08:30-10:30' => ['08:30:00', '10:30:00'],
                '10:30-12:30' => ['10:30:00', '12:30:00'],
                '14:30-16:30' => ['14:30:00', '16:30:00'],
                '16:30-18:30' => ['16:30:00', '18:30:00']
            ];

            if (!isset($timeSlots[$validated['time_slot']])) {
                return response()->json(['error' => 'Invalid time slot'], 400);
            }

            $heure_debut = $timeSlots[$validated['time_slot']][0];
            $heure_fin = $timeSlots[$validated['time_slot']][1];

            // Get assigned teacher for this UE and type
            $affectation = Affectation::where('ue_id', $validated['ue_id'])
                ->where('validee', 'valide')
                ->where('type_seance', 'LIKE', '%' . $validated['type_seance'] . '%')
                ->first();

            if (!$affectation) {
                return response()->json(['conflict' => false]);
            }

            // Check if teacher is already scheduled at this time
            $existingSchedule = Schedule::where('user_id', $affectation->user_id)
                ->where('jour_semaine', $validated['jour_semaine'])
                ->where('heure_debut', $heure_debut)
                ->where('heure_fin', $heure_fin)
                ->where('annee_universitaire', $validated['annee_universitaire'])
                ->with('uniteEnseignement')
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'conflict' => true,
                    'message' => "L'enseignant {$affectation->user->name} est déjà assigné à {$existingSchedule->uniteEnseignement->code} - {$existingSchedule->uniteEnseignement->nom} à ce créneau.",
                    'existing_ue' => $existingSchedule->uniteEnseignement->code
                ]);
            }

            return response()->json(['conflict' => false]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to check conflict: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove a schedule slot
     */
    public function destroy(Request $request)
    {
        try {
            $validated = $request->validate([
                'ue_id' => 'required|integer',
                'jour_semaine' => 'required|string',
                'time_slot' => 'required|string',
                'type_seance' => 'required|string',
                'group_number' => 'nullable|integer'
            ]);

            // Convert time slot to actual times
            $timeSlots = [
                '08:30-10:30' => ['08:30:00', '10:30:00'],
                '10:30-12:30' => ['10:30:00', '12:30:00'],
                '14:30-16:30' => ['14:30:00', '16:30:00'],
                '16:30-18:30' => ['16:30:00', '18:30:00']
            ];

            if (!isset($timeSlots[$validated['time_slot']])) {
                return response()->json(['error' => 'Invalid time slot'], 400);
            }

            $heure_debut = $timeSlots[$validated['time_slot']][0];
            $heure_fin = $timeSlots[$validated['time_slot']][1];

            $query = Schedule::where('ue_id', $validated['ue_id'])
                ->where('jour_semaine', $validated['jour_semaine'])
                ->where('heure_debut', $heure_debut)
                ->where('heure_fin', $heure_fin)
                ->where('type_seance', $validated['type_seance']);

            if ($validated['group_number']) {
                $query->where('group_number', $validated['group_number']);
            }

            $schedule = $query->first();

            if ($schedule) {
                $schedule->delete();
                return response()->json(['success' => true]);
            }

            return response()->json(['error' => 'Schedule not found'], 404);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to remove schedule: ' . $e->getMessage()], 500);
        }
    }
}
