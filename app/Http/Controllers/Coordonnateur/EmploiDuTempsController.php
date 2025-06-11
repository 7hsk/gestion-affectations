<?php

namespace App\Http\Controllers\Coordonnateur;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use App\Models\User;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmploiDuTempsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get coordonnateur's filieres
        $filieres = Filiere::where('departement_id', $user->departement_id)->get();
        
        // Get existing schedules
        $schedules = Schedule::with(['uniteEnseignement', 'user', 'filiere'])
            ->whereIn('filiere_id', $filieres->pluck('id'))
            ->get();
        
        return view('coordonnateur.emplois-du-temps', compact('filieres', 'schedules'));
    }

    public function saveEmploiDuTemps(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $changes = $request->input('changes', []);
            $filiereId = $request->input('filiere_id');
            $semester = $request->input('semester');
            $currentYear = $request->input('current_year', date('Y'));
            $anneeUniversitaire = $currentYear . '-' . ($currentYear + 1);
            
            $affectedTeachers = [];
            $conflicts = [];
            
            foreach ($changes as $change) {
                if ($change['action'] === 'add') {
                    // Get UE and check if it has an assigned teacher
                    $ue = UniteEnseignement::find($change['ue_id']);
                    if (!$ue) continue;
                    
                    // Get assigned teacher for this UE and type
                    $affectation = Affectation::where('ue_id', $ue->id)
                        ->where('type_seance', $change['type_seance'])
                        ->where('validee', 'valide')
                        ->first();
                    
                    if (!$affectation) {
                        // No teacher assigned yet, skip for now
                        continue;
                    }
                    
                    $teacherId = $affectation->user_id;
                    
                    // Convert time slot to actual times
                    $timeSlots = $this->getTimeSlotHours($change['time_slot']);
                    
                    // Check for conflicts - same teacher at same time
                    $conflict = Schedule::where('user_id', $teacherId)
                        ->where('jour_semaine', $change['day'])
                        ->where('heure_debut', $timeSlots['debut'])
                        ->where('heure_fin', $timeSlots['fin'])
                        ->where('semestre', $semester)
                        ->where('annee_universitaire', $anneeUniversitaire)
                        ->exists();
                    
                    if ($conflict) {
                        $teacher = User::find($teacherId);
                        $conflicts[] = [
                            'teacher' => $teacher->name,
                            'ue' => $ue->code,
                            'day' => $change['day'],
                            'time' => $change['time_slot'],
                            'type' => $change['type_seance']
                        ];
                        continue;
                    }
                    
                    // Create schedule entry
                    Schedule::create([
                        'ue_id' => $ue->id,
                        'user_id' => $teacherId,
                        'filiere_id' => $filiereId,
                        'jour_semaine' => $change['day'],
                        'heure_debut' => $timeSlots['debut'],
                        'heure_fin' => $timeSlots['fin'],
                        'type_seance' => $change['type_seance'],
                        'semestre' => $semester,
                        'annee_universitaire' => $anneeUniversitaire,
                        'salle' => null, // To be assigned later
                    ]);
                    
                    $affectedTeachers[] = $teacherId;
                    
                } elseif ($change['action'] === 'remove') {
                    // Remove schedule entry
                    $timeSlots = $this->getTimeSlotHours($change['time_slot']);
                    
                    Schedule::where('ue_id', $change['ue_id'])
                        ->where('jour_semaine', $change['day'])
                        ->where('heure_debut', $timeSlots['debut'])
                        ->where('heure_fin', $timeSlots['fin'])
                        ->where('semestre', $semester)
                        ->where('annee_universitaire', $anneeUniversitaire)
                        ->delete();
                }
            }
            
            DB::commit();
            
            // Return response with conflicts if any
            if (!empty($conflicts)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conflits détectés',
                    'conflicts' => $conflicts,
                    'affected_teachers' => count(array_unique($affectedTeachers))
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Emploi du temps sauvegardé avec succès',
                'affected_teachers' => count(array_unique($affectedTeachers)),
                'changes_applied' => count($changes)
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getTimeSlotHours($timeSlot)
    {
        $timeSlots = [
            '08:30-10:30' => ['debut' => '08:30:00', 'fin' => '10:30:00'],
            '10:30-12:30' => ['debut' => '10:30:00', 'fin' => '12:30:00'],
            '14:30-16:30' => ['debut' => '14:30:00', 'fin' => '16:30:00'],
            '16:30-18:30' => ['debut' => '16:30:00', 'fin' => '18:30:00'],
        ];
        
        return $timeSlots[$timeSlot] ?? ['debut' => '08:30:00', 'fin' => '10:30:00'];
    }
    
    public function checkConflicts(Request $request)
    {
        $teacherId = $request->input('teacher_id');
        $day = $request->input('day');
        $timeSlot = $request->input('time_slot');
        $semester = $request->input('semester');
        $anneeUniversitaire = $request->input('annee_universitaire');
        
        $timeSlots = $this->getTimeSlotHours($timeSlot);
        
        $conflict = Schedule::where('user_id', $teacherId)
            ->where('jour_semaine', $day)
            ->where('heure_debut', $timeSlots['debut'])
            ->where('heure_fin', $timeSlots['fin'])
            ->where('semestre', $semester)
            ->where('annee_universitaire', $anneeUniversitaire)
            ->with(['uniteEnseignement'])
            ->first();
        
        return response()->json([
            'has_conflict' => !!$conflict,
            'conflict_details' => $conflict ? [
                'ue_code' => $conflict->uniteEnseignement->code,
                'ue_name' => $conflict->uniteEnseignement->nom,
                'type' => $conflict->type_seance
            ] : null
        ]);
    }
}
