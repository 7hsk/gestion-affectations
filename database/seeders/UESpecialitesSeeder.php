<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UniteEnseignement;
use Illuminate\Support\Facades\DB;

class UESpecialitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define speciality mappings based on UE names and departments
        $specialityMappings = [
            // Informatique keywords
            'informatique' => ['Informatique', 'Programmation', 'Réseaux'],
            'programmation' => ['Informatique', 'Programmation'],
            'algorithme' => ['Informatique', 'Programmation'],
            'base de données' => ['Informatique', 'Base de données'],
            'bdd' => ['Informatique', 'Base de données'],
            'sql' => ['Informatique', 'Base de données'],
            'réseau' => ['Informatique', 'Réseaux'],
            'web' => ['Informatique', 'Développement Web'],
            'java' => ['Informatique', 'Programmation'],
            'python' => ['Informatique', 'Programmation'],
            'c++' => ['Informatique', 'Programmation'],
            'système' => ['Informatique', 'Systèmes'],
            'sécurité' => ['Informatique', 'Sécurité'],
            'intelligence artificielle' => ['Informatique', 'IA'],
            'ia' => ['Informatique', 'IA'],
            'machine learning' => ['Informatique', 'IA'],
            'data' => ['Informatique', 'Data Science'],

            // Mathématiques keywords
            'mathématiques' => ['Mathématiques'],
            'maths' => ['Mathématiques'],
            'analyse' => ['Mathématiques', 'Analyse'],
            'algèbre' => ['Mathématiques', 'Algèbre'],
            'géométrie' => ['Mathématiques', 'Géométrie'],
            'statistiques' => ['Mathématiques', 'Statistiques'],
            'probabilités' => ['Mathématiques', 'Probabilités'],
            'calcul' => ['Mathématiques', 'Calcul'],
            'équations' => ['Mathématiques', 'Équations'],

            // Physique keywords
            'physique' => ['Physique'],
            'mécanique' => ['Physique', 'Mécanique'],
            'électricité' => ['Physique', 'Électricité'],
            'optique' => ['Physique', 'Optique'],
            'thermodynamique' => ['Physique', 'Thermodynamique'],

            // Génie Civil keywords
            'génie civil' => ['Génie Civil'],
            'construction' => ['Génie Civil', 'Construction'],
            'béton' => ['Génie Civil', 'Matériaux'],
            'structure' => ['Génie Civil', 'Structures'],
            'hydraulique' => ['Génie Civil', 'Hydraulique'],
            'topographie' => ['Génie Civil', 'Topographie'],

            // Génie Mécanique keywords
            'génie mécanique' => ['Génie Mécanique'],
            'mécanique des fluides' => ['Génie Mécanique', 'Mécanique'],
            'thermique' => ['Génie Mécanique', 'Thermique'],
            'matériaux' => ['Génie Mécanique', 'Matériaux'],
            'fabrication' => ['Génie Mécanique', 'Fabrication'],

            // General keywords
            'communication' => ['Communication'],
            'langue' => ['Langues'],
            'anglais' => ['Langues', 'Anglais'],
            'français' => ['Langues', 'Français'],
            'management' => ['Management'],
            'économie' => ['Économie'],
            'droit' => ['Droit'],
            'stage' => ['Stage'],
            'projet' => ['Projet'],
            'pfe' => ['Projet']
        ];

        // Get all UEs
        $ues = UniteEnseignement::all();

        foreach ($ues as $ue) {
            $specialites = [];
            $ueName = strtolower($ue->nom);
            $ueCode = strtolower($ue->code);

            // Check UE name and code against keywords
            foreach ($specialityMappings as $keyword => $specs) {
                if (strpos($ueName, $keyword) !== false || strpos($ueCode, $keyword) !== false) {
                    $specialites = array_merge($specialites, $specs);
                }
            }

            // Add department-based specialities
            if ($ue->departement) {
                $deptName = strtolower($ue->departement->nom);

                if (strpos($deptName, 'informatique') !== false) {
                    $specialites[] = 'Informatique';
                }
                if (strpos($deptName, 'mathématiques') !== false) {
                    $specialites[] = 'Mathématiques';
                }
                if (strpos($deptName, 'génie civil') !== false) {
                    $specialites[] = 'Génie Civil';
                }
                if (strpos($deptName, 'génie mécanique') !== false) {
                    $specialites[] = 'Génie Mécanique';
                }
            }

            // Remove duplicates and create comma-separated string
            $specialites = array_unique($specialites);

            // If no specialities found, assign based on department or default
            if (empty($specialites)) {
                if ($ue->departement) {
                    $deptName = strtolower($ue->departement->nom);
                    if (strpos($deptName, 'informatique') !== false) {
                        $specialites = ['Informatique'];
                    } elseif (strpos($deptName, 'mathématiques') !== false) {
                        $specialites = ['Mathématiques'];
                    } elseif (strpos($deptName, 'génie civil') !== false) {
                        $specialites = ['Génie Civil'];
                    } elseif (strpos($deptName, 'génie mécanique') !== false) {
                        $specialites = ['Génie Mécanique'];
                    } else {
                        $specialites = ['Général'];
                    }
                } else {
                    $specialites = ['Général'];
                }
            }

            // Update UE with specialities
            $ue->update([
                'specialite' => implode(',', $specialites)
            ]);

            $this->command->info("UE {$ue->code} - {$ue->nom}: " . implode(', ', $specialites));
        }

        $this->command->info('UE specialities updated successfully!');
    }
}
