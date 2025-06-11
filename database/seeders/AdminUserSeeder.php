<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    // In database/seeders/AdminUserSeeder.php
    public function run()
    {
        $department = \App\Models\Departement::firstOrCreate(
            ['id' => 1],
            ['nom' => 'System Administration', 'description' => 'Department for system administrators']
        );

        User::updateOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'System Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('svr1'),
                'role' => 'admin',
                'departement_id' => $department->id,
            ]
        );
    }
}
