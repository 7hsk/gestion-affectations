<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Departement;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use Illuminate\Support\Facades\Route;

class CheckChefSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chef:check-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Chef de Département system status and configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking Chef de Département System Status...');
        $this->newLine();

        // Check Routes
        $this->checkRoutes();
        $this->newLine();

        // Check Middleware
        $this->checkMiddleware();
        $this->newLine();

        // Check Database
        $this->checkDatabase();
        $this->newLine();

        // Check Users
        $this->checkUsers();
        $this->newLine();

        // Check Permissions
        $this->checkPermissions();
        $this->newLine();

        $this->info('✅ System check completed!');
    }

    private function checkRoutes()
    {
        $this->info('📍 Checking Routes...');
        
        $chefRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->getName() ?? '', 'chef.');
        });

        $expectedRoutes = [
            'chef.dashboard',
            'chef.unites-enseignement',
            'chef.enseignants',
            'chef.affectations',
            'chef.ues-vacantes',
            'chef.historique',
            'chef.rapports',
            'chef.enseignants.charge-horaire',
            'chef.affectations.valider',
            'chef.affecter-ue'
        ];

        foreach ($expectedRoutes as $routeName) {
            if ($chefRoutes->where('name', $routeName)->isNotEmpty()) {
                $this->line("  ✅ {$routeName}");
            } else {
                $this->error("  ❌ {$routeName} - MISSING");
            }
        }

        $this->info("  📊 Total chef routes: " . $chefRoutes->count());
    }

    private function checkMiddleware()
    {
        $this->info('🛡️ Checking Middleware...');
        
        // Check if ChefDepartementMiddleware exists
        if (class_exists(\App\Http\Middleware\ChefDepartementMiddleware::class)) {
            $this->line("  ✅ ChefDepartementMiddleware class exists");
        } else {
            $this->error("  ❌ ChefDepartementMiddleware class missing");
        }

        // Check if middleware is registered
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $middlewareGroups = $kernel->getMiddlewareGroups();
        
        if (isset($middlewareGroups['chef'])) {
            $this->line("  ✅ Chef middleware group registered");
        } else {
            $this->line("  ⚠️ Chef middleware group not found (using individual middleware)");
        }
    }

    private function checkDatabase()
    {
        $this->info('🗄️ Checking Database...');
        
        try {
            // Check tables exist
            $tables = [
                'users' => User::count(),
                'departements' => Departement::count(),
                'unites_enseignement' => UniteEnseignement::count(),
                'affectations' => Affectation::count(),
            ];

            foreach ($tables as $table => $count) {
                $this->line("  ✅ {$table}: {$count} records");
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Database error: " . $e->getMessage());
        }
    }

    private function checkUsers()
    {
        $this->info('👥 Checking Users...');
        
        try {
            $chefs = User::where('role', 'chef')->count();
            $enseignants = User::where('role', 'enseignant')->count();
            $chefsWithDepartment = User::where('role', 'chef')
                ->whereNotNull('departement_id')
                ->count();

            $this->line("  ✅ Chefs de département: {$chefs}");
            $this->line("  ✅ Enseignants: {$enseignants}");
            $this->line("  ✅ Chefs with department: {$chefsWithDepartment}");

            if ($chefs > 0 && $chefsWithDepartment == $chefs) {
                $this->line("  ✅ All chefs have departments assigned");
            } elseif ($chefs > 0) {
                $this->warn("  ⚠️ Some chefs don't have departments assigned");
            }

        } catch (\Exception $e) {
            $this->error("  ❌ User check error: " . $e->getMessage());
        }
    }

    private function checkPermissions()
    {
        $this->info('🔐 Checking Permissions...');
        
        try {
            // Check if chef users can access their department data
            $chef = User::where('role', 'chef')->first();
            
            if ($chef) {
                $this->line("  ✅ Test chef found: {$chef->name}");
                
                if ($chef->departement) {
                    $this->line("  ✅ Chef has department: {$chef->departement->nom}");
                    
                    // Check department data access
                    $departmentUes = UniteEnseignement::where('departement_id', $chef->departement_id)->count();
                    $departmentEnseignants = User::where('departement_id', $chef->departement_id)
                        ->whereIn('role', ['enseignant', 'chef'])
                        ->count();
                    
                    $this->line("  ✅ Department UEs: {$departmentUes}");
                    $this->line("  ✅ Department staff: {$departmentEnseignants}");
                } else {
                    $this->warn("  ⚠️ Chef has no department assigned");
                }
            } else {
                $this->warn("  ⚠️ No chef users found for testing");
            }

        } catch (\Exception $e) {
            $this->error("  ❌ Permission check error: " . $e->getMessage());
        }
    }
}
