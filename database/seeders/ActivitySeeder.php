<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing activities
        Activity::truncate();

        $users = User::all();
        $admin = $users->where('role', 'admin')->first();
        $chef = $users->where('role', 'chef')->first();
        $enseignant = $users->where('role', 'enseignant')->first();
        $coordonnateur = $users->where('role', 'coordonnateur')->first();
        $vacataire = $users->where('role', 'vacataire')->first();

        // Create comprehensive sample activities
        $activities = [
            // Authentication activities
            [
                'type' => 'auth',
                'action' => 'login',
                'description' => 'Connexion réussie: ' . ($admin->name ?? 'Admin') . ' (admin)',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\User',
                'subject_id' => $admin->id ?? 1,
                'properties' => [
                    'user_name' => $admin->name ?? 'Admin',
                    'user_role' => 'admin',
                    'login_method' => 'Email/Password',
                    'success' => true
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'type' => 'auth',
                'action' => 'login',
                'description' => 'Connexion réussie: ' . ($enseignant->name ?? 'Enseignant') . ' (enseignant)',
                'user_id' => $enseignant->id ?? 2,
                'subject_type' => 'App\Models\User',
                'subject_id' => $enseignant->id ?? 2,
                'properties' => [
                    'user_name' => $enseignant->name ?? 'Enseignant',
                    'user_role' => 'enseignant',
                    'login_method' => 'Email/Password',
                    'success' => true
                ],
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(25),
            ],
            [
                'type' => 'logout',
                'action' => 'logout',
                'description' => 'Déconnexion: ' . ($chef->name ?? 'Chef') . ' (chef)',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\User',
                'subject_id' => $chef->id ?? 3,
                'properties' => [
                    'user_name' => $chef->name ?? 'Chef',
                    'user_role' => 'chef',
                    'session_duration' => '2 heures'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(20),
            ],

            // User management activities
            [
                'type' => 'create',
                'action' => 'user_created',
                'description' => 'Nouvel utilisateur créé: Marie Dupont (enseignant)',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\User',
                'subject_id' => $enseignant->id ?? 2,
                'properties' => [
                    'user_name' => 'Marie Dupont',
                    'user_email' => 'marie.dupont@ensa.ma',
                    'user_role' => 'enseignant',
                    'department' => 'Informatique',
                    'created_by' => $admin->name ?? 'Admin'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'type' => 'update',
                'action' => 'user_updated',
                'description' => 'Utilisateur modifié: Pierre Martin (coordonnateur)',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\User',
                'subject_id' => $coordonnateur->id ?? 4,
                'properties' => [
                    'user_name' => 'Pierre Martin',
                    'user_role' => 'coordonnateur',
                    'updated_by' => $admin->name ?? 'Admin',
                    'changes' => 'Role changed from enseignant to coordonnateur'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(1),
            ],
            [
                'type' => 'delete',
                'action' => 'user_deleted',
                'description' => 'Utilisateur supprimé: Jean Doe (vacataire)',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\User',
                'subject_id' => null,
                'properties' => [
                    'deleted_user_name' => 'Jean Doe',
                    'deleted_user_role' => 'vacataire',
                    'deleted_by' => $admin->name ?? 'Admin',
                    'reason' => 'Compte inactif'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(3),
            ],

            // System activities
            [
                'type' => 'system',
                'action' => 'backup_created',
                'description' => 'Sauvegarde automatique de la base de données',
                'user_id' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'backup_size' => '2.5 MB',
                    'backup_type' => 'automatic',
                    'status' => 'completed'
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System/Cron',
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'type' => 'system',
                'action' => 'system_startup',
                'description' => 'Système démarré - Session administrative',
                'user_id' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'version' => 'v1.0.0',
                    'environment' => 'production',
                    'status' => 'operational'
                ],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System/Boot',
                'created_at' => Carbon::now()->subHours(8),
            ],

            // File activities
            [
                'type' => 'upload',
                'action' => 'file_uploaded',
                'description' => 'Fichier téléchargé: emploi_du_temps_S1.pdf',
                'user_id' => $admin->id ?? 1,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'filename' => 'emploi_du_temps_S1.pdf',
                    'file_size' => '1.2 MB',
                    'file_type' => 'PDF',
                    'uploaded_by' => $admin->name ?? 'Admin'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'type' => 'export',
                'action' => 'data_exported',
                'description' => 'Export des données: Liste des UEs',
                'user_id' => $admin->id ?? 1,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'export_type' => 'CSV',
                    'records_count' => '150 UEs',
                    'file_size' => '45 KB',
                    'exported_by' => $admin->name ?? 'Admin'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(4),
            ],

            // Security activities
            [
                'type' => 'security',
                'action' => 'failed_login',
                'description' => 'Tentative de connexion échouée: email incorrect',
                'user_id' => null,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'attempted_email' => 'hacker@example.com',
                    'failure_reason' => 'Invalid credentials',
                    'attempts_count' => 3
                ],
                'ip_address' => '192.168.1.999',
                'user_agent' => 'Mozilla/5.0 (Unknown)',
                'created_at' => Carbon::now()->subHours(5),
            ],

            // Approval/Rejection activities
            [
                'type' => 'approve',
                'action' => 'affectation_approved',
                'description' => 'Affectation approuvée: Marie Dupont - INF101',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\Affectation',
                'subject_id' => 1,
                'properties' => [
                    'user_name' => 'Marie Dupont',
                    'ue_code' => 'INF101',
                    'ue_name' => 'Programmation',
                    'approved_by' => $chef->name ?? 'Chef',
                    'session_type' => 'CM'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(45),
            ],
            [
                'type' => 'reject',
                'action' => 'affectation_rejected',
                'description' => 'Affectation rejetée: Jean Doe - MAT201',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\Affectation',
                'subject_id' => 2,
                'properties' => [
                    'user_name' => 'Jean Doe',
                    'ue_code' => 'MAT201',
                    'ue_name' => 'Mathématiques Avancées',
                    'rejected_by' => $chef->name ?? 'Chef',
                    'rejection_reason' => 'Spécialité non compatible'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(40),
            ],

            // UE Management activities
            [
                'type' => 'create',
                'action' => 'ue_created',
                'description' => 'Nouvelle UE créée: INF301 - Base de Données',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\UniteEnseignement',
                'subject_id' => 1,
                'properties' => [
                    'ue_code' => 'INF301',
                    'ue_name' => 'Base de Données',
                    'heures_cm' => 30,
                    'heures_td' => 20,
                    'heures_tp' => 10,
                    'created_by' => $chef->name ?? 'Chef'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(1)->subMinutes(30),
            ],
            [
                'type' => 'update',
                'action' => 'ue_updated',
                'description' => 'UE modifiée: INF301 - Base de Données',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\UniteEnseignement',
                'subject_id' => 1,
                'properties' => [
                    'ue_code' => 'INF301',
                    'ue_name' => 'Base de Données',
                    'changes' => 'Heures TP modifiées de 10 à 15',
                    'updated_by' => $chef->name ?? 'Chef'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subMinutes(50),
            ],

            // Additional activities to test pagination
            [
                'type' => 'create',
                'action' => 'department_created',
                'description' => 'Nouveau département créé: Mathématiques',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\Departement',
                'subject_id' => 1,
                'properties' => [
                    'department_name' => 'Mathématiques',
                    'created_by' => $admin->name ?? 'Admin'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'type' => 'update',
                'action' => 'profile_updated',
                'description' => 'Profil modifié: ' . ($enseignant->name ?? 'Enseignant'),
                'user_id' => $enseignant->id ?? 2,
                'subject_type' => 'App\Models\User',
                'subject_id' => $enseignant->id ?? 2,
                'properties' => [
                    'updated_fields' => 'email, phone',
                    'old_email' => 'old@ensa.ma',
                    'new_email' => 'new@ensa.ma'
                ],
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'type' => 'auth',
                'action' => 'login',
                'description' => 'Connexion réussie: ' . ($coordonnateur->name ?? 'Coordonnateur') . ' (coordonnateur)',
                'user_id' => $coordonnateur->id ?? 4,
                'subject_type' => 'App\Models\User',
                'subject_id' => $coordonnateur->id ?? 4,
                'properties' => [
                    'user_name' => $coordonnateur->name ?? 'Coordonnateur',
                    'user_role' => 'coordonnateur',
                    'login_method' => 'Email/Password'
                ],
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'type' => 'create',
                'action' => 'filiere_created',
                'description' => 'Nouvelle filière créée: GI4 - Génie Informatique 4ème année',
                'user_id' => $admin->id ?? 1,
                'subject_type' => 'App\Models\Filiere',
                'subject_id' => 1,
                'properties' => [
                    'filiere_name' => 'GI4',
                    'filiere_description' => 'Génie Informatique 4ème année',
                    'created_by' => $admin->name ?? 'Admin'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(7),
            ],
            [
                'type' => 'delete',
                'action' => 'ue_deleted',
                'description' => 'UE supprimée: MAT999 - Mathématiques Obsolètes',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\UniteEnseignement',
                'subject_id' => null,
                'properties' => [
                    'deleted_ue_code' => 'MAT999',
                    'deleted_ue_name' => 'Mathématiques Obsolètes',
                    'deletion_reason' => 'Programme obsolète'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'type' => 'export',
                'action' => 'schedule_exported',
                'description' => 'Export emploi du temps: Semestre 1 GI3',
                'user_id' => $coordonnateur->id ?? 4,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'export_type' => 'PDF',
                    'semester' => 'S1',
                    'filiere' => 'GI3',
                    'file_size' => '2.1 MB'
                ],
                'ip_address' => '192.168.1.103',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(9),
            ],
            [
                'type' => 'approve',
                'action' => 'schedule_approved',
                'description' => 'Emploi du temps approuvé: GI2 Semestre 3',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\EmploiDuTemps',
                'subject_id' => 1,
                'properties' => [
                    'filiere' => 'GI2',
                    'semester' => 'S3',
                    'approved_by' => $chef->name ?? 'Chef'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(10),
            ],
            [
                'type' => 'security',
                'action' => 'password_changed',
                'description' => 'Mot de passe modifié: ' . ($enseignant->name ?? 'Enseignant'),
                'user_id' => $enseignant->id ?? 2,
                'subject_type' => 'App\Models\User',
                'subject_id' => $enseignant->id ?? 2,
                'properties' => [
                    'user_name' => $enseignant->name ?? 'Enseignant',
                    'change_reason' => 'User request',
                    'security_level' => 'high'
                ],
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(11),
            ],
            [
                'type' => 'upload',
                'action' => 'document_uploaded',
                'description' => 'Document téléchargé: Règlement_Intérieur_2024.pdf',
                'user_id' => $admin->id ?? 1,
                'subject_type' => null,
                'subject_id' => null,
                'properties' => [
                    'filename' => 'Règlement_Intérieur_2024.pdf',
                    'file_size' => '3.5 MB',
                    'file_type' => 'PDF',
                    'category' => 'Administrative'
                ],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'type' => 'reject',
                'action' => 'request_rejected',
                'description' => 'Demande rejetée: Changement de filière - Étudiant X',
                'user_id' => $chef->id ?? 3,
                'subject_type' => 'App\Models\DemandeChangement',
                'subject_id' => 1,
                'properties' => [
                    'student_name' => 'Étudiant X',
                    'from_filiere' => 'GI2',
                    'to_filiere' => 'GI3',
                    'rejection_reason' => 'Notes insuffisantes'
                ],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::now()->subHours(13),
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
