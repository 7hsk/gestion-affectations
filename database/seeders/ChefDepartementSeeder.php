<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Departement;
use Illuminate\Support\Facades\Hash;

class ChefDepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a department
        $departement = Departement::firstOrCreate(
            ['nom' => 'Informatique'],
            [
                'nom' => 'Informatique',
                'description' => 'Département d\'Informatique et Technologies',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Create chef de département
        $chef = User::updateOrCreate(
            ['email' => 'chef.informatique@ensa.ma'],
            [
                'name' => 'Dr. Hassan Benali',
                'email' => 'chef.informatique@ensa.ma',
                'password' => Hash::make('password'),
                'role' => 'chef',
                'specialite' => 'Informatique et Réseaux',
                'departement_id' => $departement->id,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Create additional enseignants for the department
        $enseignants = [
            [
                'name' => 'Dr. Amina Tazi',
                'email' => 'amina.tazi@ensa.ma',
                'specialite' => 'Intelligence Artificielle',
            ],
            [
                'name' => 'Prof. Mohamed Alami',
                'email' => 'mohamed.alami@ensa.ma',
                'specialite' => 'Bases de Données',
            ],
            [
                'name' => 'Dr. Fatima Zahra',
                'email' => 'fatima.zahra@ensa.ma',
                'specialite' => 'Développement Web',
            ],
            [
                'name' => 'Dr. Youssef Bennani',
                'email' => 'youssef.bennani@ensa.ma',
                'specialite' => 'Sécurité Informatique',
            ],
            [
                'name' => 'Prof. Aicha Idrissi',
                'email' => 'aicha.idrissi@ensa.ma',
                'specialite' => 'Systèmes Distribués',
            ]
        ];

        foreach ($enseignants as $enseignantData) {
            User::updateOrCreate(
                ['email' => $enseignantData['email']],
                [
                    'name' => $enseignantData['name'],
                    'email' => $enseignantData['email'],
                    'password' => Hash::make('password'),
                    'role' => 'enseignant',
                    'specialite' => $enseignantData['specialite'],
                    'departement_id' => $departement->id,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        $this->command->info('Chef de département and enseignants created successfully!');
        $this->command->info('Chef Login: chef.informatique@ensa.ma / password');
        $this->command->info('Department: ' . $departement->nom);
        $this->command->info('Enseignants created: ' . count($enseignants));
    }
}
