<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestChefAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chef:test-access {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test chef access and verify user setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'chef.informatique@ensa.ma';
        
        $this->info("🔍 Testing Chef Access for: {$email}");
        $this->newLine();

        // Find the chef user
        $chef = User::where('email', $email)->first();
        
        if (!$chef) {
            $this->error("❌ Chef user not found with email: {$email}");
            $this->info("Available chef users:");
            
            $chefs = User::where('role', 'chef')->get(['id', 'name', 'email', 'departement_id']);
            foreach ($chefs as $c) {
                $this->line("  - {$c->name} ({$c->email}) - Dept ID: {$c->departement_id}");
            }
            return;
        }

        $this->info("✅ Chef user found:");
        $this->line("  Name: {$chef->name}");
        $this->line("  Email: {$chef->email}");
        $this->line("  Role: {$chef->role}");
        $this->line("  Department ID: {$chef->departement_id}");
        
        // Check department
        if ($chef->departement) {
            $this->line("  Department: {$chef->departement->nom}");
            $this->info("✅ Department relationship working");
        } else {
            $this->error("❌ No department assigned or relationship broken");
            return;
        }

        // Check password
        if (Hash::check('password', $chef->password)) {
            $this->info("✅ Default password 'password' is correct");
        } else {
            $this->warn("⚠️ Password is not the default 'password'");
        }

        // Check role middleware compatibility
        if ($chef->role === 'chef') {
            $this->info("✅ Role is correct for chef middleware");
        } else {
            $this->error("❌ Role mismatch - expected 'chef', got '{$chef->role}'");
        }

        // Test department data access
        $departmentUes = \App\Models\UniteEnseignement::where('departement_id', $chef->departement_id)->count();
        $departmentStaff = User::where('departement_id', $chef->departement_id)->count();
        
        $this->newLine();
        $this->info("📊 Department Data Access:");
        $this->line("  UEs in department: {$departmentUes}");
        $this->line("  Staff in department: {$departmentStaff}");

        if ($departmentUes > 0 || $departmentStaff > 0) {
            $this->info("✅ Department has data to manage");
        } else {
            $this->warn("⚠️ Department has no UEs or staff to manage");
        }

        $this->newLine();
        $this->info("🎯 Login Instructions:");
        $this->line("  URL: /chef/dashboard");
        $this->line("  Email: {$chef->email}");
        $this->line("  Password: password");
        
        $this->newLine();
        $this->info("✅ Chef access test completed successfully!");
    }
}
