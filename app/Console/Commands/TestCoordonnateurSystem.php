<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UniteEnseignement;
use App\Models\Affectation;
use App\Models\Filiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class TestCoordonnateurSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coordonnateur:test-system {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the complete coordonnateur system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'coordonnateur.gi@ensa.ma';
        
        $this->info("🔍 Testing Coordonnateur System for: {$email}");
        $this->newLine();

        // Test 1: User exists and has correct role
        $coordonnateur = User::where('email', $email)->first();
        
        if (!$coordonnateur) {
            $this->error("❌ Coordonnateur user not found with email: {$email}");
            return;
        }

        $this->info("✅ User found: {$coordonnateur->name}");
        
        if ($coordonnateur->role !== 'coordonnateur') {
            $this->error("❌ User role is not 'coordonnateur': {$coordonnateur->role}");
            return;
        }
        
        $this->info("✅ User role is correct: {$coordonnateur->role}");

        // Test 2: Check filiere assignments
        $filieres = DB::table('coordonnateurs_filieres')
            ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
            ->where('coordonnateurs_filieres.user_id', $coordonnateur->id)
            ->select('filieres.*')
            ->get();

        if ($filieres->isEmpty()) {
            $this->error("❌ Coordonnateur has no filiere assignments");
            return;
        }

        $this->info("✅ Filiere assignments found: " . $filieres->pluck('nom')->implode(', '));

        // Test 3: Check routes
        $this->info("🔍 Testing routes...");
        
        $routes = [
            'coordonnateur.dashboard',
            'coordonnateur.unites-enseignement',
            'coordonnateur.vacataires',
            'coordonnateur.affectations',
            'coordonnateur.emplois-du-temps',
            'coordonnateur.historique',
        ];

        foreach ($routes as $routeName) {
            if (Route::has($routeName)) {
                $this->info("✅ Route exists: {$routeName}");
            } else {
                $this->error("❌ Route missing: {$routeName}");
            }
        }

        // Test 4: Check dashboard data
        $this->info("🔍 Testing dashboard data...");
        
        $filiereIds = $filieres->pluck('id');

        try {
            // Statistics
            $stats = [
                'total_ues' => UniteEnseignement::whereIn('filiere_id', $filiereIds)->count(),
                'ues_vacantes' => UniteEnseignement::whereIn('filiere_id', $filiereIds)->where('est_vacant', true)->count(),
                'total_vacataires' => User::where('role', 'vacataire')->count(),
                'affectations_en_attente' => Affectation::whereHas('uniteEnseignement', function($query) use ($filiereIds) {
                    $query->whereIn('filiere_id', $filiereIds);
                })->where('validee', 'en_attente')->count(),
            ];

            $this->info("✅ Dashboard statistics calculated:");
            $this->line("  Total UEs: {$stats['total_ues']}");
            $this->line("  UEs Vacantes: {$stats['ues_vacantes']}");
            $this->line("  Total Vacataires: {$stats['total_vacataires']}");
            $this->line("  Affectations en attente: {$stats['affectations_en_attente']}");

            // Recent UEs
            $recentUes = UniteEnseignement::whereIn('filiere_id', $filiereIds)
                ->with(['filiere', 'departement'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $this->info("✅ Recent UEs query successful: {$recentUes->count()} UEs found");

            // Recent affectations
            $affectationsRecentes = Affectation::whereHas('uniteEnseignement', function($query) use ($filiereIds) {
                $query->whereIn('filiere_id', $filiereIds);
            })
            ->with(['user', 'uniteEnseignement'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

            $this->info("✅ Recent affectations query successful: {$affectationsRecentes->count()} affectations found");

            // UEs with missing groups
            $uesGroupesManquants = UniteEnseignement::whereIn('filiere_id', $filiereIds)
                ->where(function($query) {
                    $query->where('groupes_td', 0)->orWhere('groupes_tp', 0);
                })
                ->with(['filiere'])
                ->get();

            $this->info("✅ UEs with missing groups query successful: {$uesGroupesManquants->count()} UEs need group definition");

        } catch (\Exception $e) {
            $this->error("❌ Dashboard data test failed: " . $e->getMessage());
            return;
        }

        // Test 5: Check views exist
        $this->info("🔍 Testing view files...");
        
        $views = [
            'layouts.coordonnateur',
            'coordonnateur.dashboard',
            'coordonnateur.unites-enseignement',
            'coordonnateur.vacataires',
            'coordonnateur.affectations',
            'coordonnateur.emplois-du-temps',
            'coordonnateur.historique',
        ];

        foreach ($views as $view) {
            $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
            if (file_exists($viewPath)) {
                $this->info("✅ View exists: {$view}");
            } else {
                $this->error("❌ View missing: {$view}");
            }
        }

        // Test 6: Check controller methods
        $this->info("🔍 Testing controller methods...");
        
        $controller = new \App\Http\Controllers\Admin\coordonnateur\CoordonnateurController();
        $methods = [
            'dashboard',
            'unitesEnseignement',
            'creerUE',
            'definirGroupes',
            'vacataires',
            'creerVacataire',
            'affecterVacataire',
            'affectations',
            'emploisDuTemps',
            'historique',
            'exportData',
            'importData',
            'getUesFiliere',
            'getStatistics',
        ];

        foreach ($methods as $method) {
            if (method_exists($controller, $method)) {
                $this->info("✅ Controller method exists: {$method}");
            } else {
                $this->error("❌ Controller method missing: {$method}");
            }
        }

        // Test 7: Check middleware
        $this->info("🔍 Testing middleware...");
        
        $middlewareClass = \App\Http\Middleware\CheckRole::class;
        if (class_exists($middlewareClass)) {
            $this->info("✅ CheckRole middleware exists");
        } else {
            $this->error("❌ CheckRole middleware missing");
        }

        // Summary
        $this->newLine();
        $this->info("📋 System Test Summary:");
        $this->line("  User: {$coordonnateur->name} ({$coordonnateur->email})");
        $this->line("  Role: {$coordonnateur->role}");
        $this->line("  Filieres: " . $filieres->pluck('nom')->implode(', '));
        $this->line("  UEs managed: {$stats['total_ues']}");
        $this->line("  Routes: " . count($routes) . " tested");
        $this->line("  Views: " . count($views) . " tested");
        $this->line("  Controller methods: " . count($methods) . " tested");

        $this->newLine();
        $this->info("✅ Coordonnateur system test completed successfully!");
        $this->info("🚀 You can now login at: /coordonnateur/dashboard");
        $this->info("📧 Email: {$coordonnateur->email}");
        $this->info("🔑 Password: password");
    }
}
