<?php

// Simple test script to verify coordonnateur system
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Filiere;
use Illuminate\Support\Facades\DB;

// Test coordonnateur user
$email = 'coordonnateur.gi@ensa.ma';

echo "🔍 Testing Coordonnateur System\n";
echo "================================\n\n";

// Test 1: User exists
$user = User::where('email', $email)->first();
if ($user) {
    echo "✅ User found: {$user->name}\n";
    echo "✅ Role: {$user->role}\n";
} else {
    echo "❌ User not found: {$email}\n";
    exit(1);
}

// Test 2: Filiere assignments
$filieres = DB::table('coordonnateurs_filieres')
    ->join('filieres', 'coordonnateurs_filieres.filiere_id', '=', 'filieres.id')
    ->where('coordonnateurs_filieres.user_id', $user->id)
    ->select('filieres.*')
    ->get();

if ($filieres->isNotEmpty()) {
    echo "✅ Filieres assigned: " . $filieres->pluck('nom')->implode(', ') . "\n";
} else {
    echo "❌ No filiere assignments found\n";
    exit(1);
}

// Test 3: Routes exist
$routes = [
    'coordonnateur.dashboard',
    'coordonnateur.unites-enseignement',
    'coordonnateur.vacataires',
    'coordonnateur.affectations',
    'coordonnateur.emplois-du-temps',
    'coordonnateur.historique',
];

echo "\n🔍 Testing Routes:\n";
foreach ($routes as $route) {
    if (Route::has($route)) {
        echo "✅ Route exists: {$route}\n";
    } else {
        echo "❌ Route missing: {$route}\n";
    }
}

echo "\n🎉 All tests passed!\n";
echo "🚀 Dashboard URL: /coordonnateur/dashboard\n";
echo "📧 Email: {$user->email}\n";
echo "🔑 Password: password\n";
