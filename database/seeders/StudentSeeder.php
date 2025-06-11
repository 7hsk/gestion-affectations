<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Filiere;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing filières
        $filieres = Filiere::all();

        if ($filieres->isEmpty()) {
            $this->command->info('No filières found. Please create filières first.');
            return;
        }

        $students = [
            [
                'name' => 'Ahmed Benali',
                'email' => 'ahmed.benali@student.ensa.ma',
                'matricule' => 'GI2024001',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@student.ensa.ma',
                'matricule' => 'GI2024002',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Mohamed Alami',
                'email' => 'mohamed.alami@student.ensa.ma',
                'matricule' => 'GI2024003',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Aicha Idrissi',
                'email' => 'aicha.idrissi@student.ensa.ma',
                'matricule' => 'GI2024004',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Youssef Tazi',
                'email' => 'youssef.tazi@student.ensa.ma',
                'matricule' => 'GI2024005',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Khadija Bennani',
                'email' => 'khadija.bennani@student.ensa.ma',
                'matricule' => 'GI2024006',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Omar Fassi',
                'email' => 'omar.fassi@student.ensa.ma',
                'matricule' => 'GI2024007',
                'filiere_id' => $filieres->first()->id,
            ],
            [
                'name' => 'Salma Chraibi',
                'email' => 'salma.chraibi@student.ensa.ma',
                'matricule' => 'GI2024008',
                'filiere_id' => $filieres->first()->id,
            ],
        ];

        foreach ($students as $studentData) {
            User::updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'etudiant',
                    'matricule' => $studentData['matricule'],
                    'filiere_id' => $studentData['filiere_id'],
                    'departement_id' => 1, // Assuming department ID 1 exists
                ]
            );
        }

        $this->command->info('Students seeded successfully!');
    }
}
