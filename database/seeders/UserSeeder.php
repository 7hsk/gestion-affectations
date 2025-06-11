<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Dr. Amal Chef',
                'email' => 'chef.informatique@example.com',
                'password' => Hash::make('password'),
                'role' => 'chef',
                'specialite' => 'Systèmes Informatiques',
                'departement_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'M. Karim Coordonnateur',
                'email' => 'coord.gi@example.com',
                'password' => Hash::make('password'),
                'role' => 'coordonnateur',
                'specialite' => 'Génie Informatique',
                'departement_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mme. Salma Enseignante',
                'email' => 'enseignante.gee@example.com',
                'password' => Hash::make('password'),
                'role' => 'enseignant',
                'specialite' => 'Énergétique',
                'departement_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'M. Youssef Vacataire',
                'email' => 'vacataire.gc@example.com',
                'password' => Hash::make('password'),
                'role' => 'vacataire',
                'specialite' => 'Génie Civil',
                'departement_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mme. Nadia Chef CP',
                'email' => 'chef.cp@example.com',
                'password' => Hash::make('password'),
                'role' => 'chef',
                'specialite' => 'Mathématiques & Physique',
                'departement_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'M. Hicham Prof CP',
                'email' => 'enseignant.cp@example.com',
                'password' => Hash::make('password'),
                'role' => 'enseignant',
                'specialite' => 'Physique',
                'departement_id' => 3,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}

