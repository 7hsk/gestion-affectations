<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\{
    AdminController,
    DepartementController,
    FiliereController,
    UEController,
    AffectationController
};
use App\Http\Controllers\Admin\enseignant\EnseignantController;
use App\Http\Controllers\Enseignant\EnseignantUEController;
// use App\Http\Controllers\Admin\ChefDepartement\ChefController;
// use App\Http\Controllers\Admin\Coordonnateur\CoordonnateurController;
// use App\Http\Controllers\Admin\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->middleware(['auth', \App\Http\Middleware\CheckRole::class, \App\Http\Middleware\CheckRole::class . ':admin'])->group(function () {
    Route::get('/admin-dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('departements', \App\Http\Controllers\Admin\DepartementController::class);

    // Users Management
    // Use this single registration with the admin prefix
    Route::resource('users', UserController::class)->names('admin.users');
    Route::post('users/{user}/change-password', [UserController::class, 'changePassword'])
        ->name('users.change-password');
    Route::get('users/teachers', [UserController::class, 'teachers'])->name('users.teachers');
    Route::get('users/coordinators', [UserController::class, 'coordinators'])->name('users.coordinators');
    // Route::get('users/export', [UserController::class, 'export'])->name('admin.users.export'); // REMOVED - not in allowed list
    Route::get('users/stats', [UserController::class, 'getStats'])->name('admin.users.stats');

    // Departments Management
    Route::resource('departements', DepartementController::class)->names('admin.departements');
    Route::patch('departements/{departement}/update-chef', [DepartementController::class, 'updateChef'])
        ->name('admin.departements.updateChef');
    // Route::get('departements/export', [DepartementController::class, 'export'])->name('admin.departements.export'); // REMOVED - not in allowed list
    Route::get('departements/stats', [DepartementController::class, 'getStats'])->name('admin.departements.stats');

    // Unités d'Enseignement Management
    Route::resource('ues', UEController::class)->parameters(['ues' => 'ue'])->names('admin.ues');
    // Route::get('ues/export', [UEController::class, 'export'])->name('admin.ues.export'); // REMOVED - not in allowed list
    Route::get('ues/stats', [UEController::class, 'getStats'])->name('admin.ues.stats');
    // Programs Management
    Route::resource('filieres', FiliereController::class)->names('admin.filieres');
    Route::post('filieres/{filiere}/assign-coordinator', [FiliereController::class, 'assignCoordinator'])
        ->name('filieres.assign-coordinator');

    // Courses Management
    Route::resource('unites-enseignement', UEController::class)->names('admin.unites-enseignement');
    Route::post('unites-enseignement/import', [UEController::class, 'import'])
        ->name('unites-enseignement.import');

    // Assignments Management
    Route::resource('affectations', AffectationController::class)->names('admin.affectations');
    Route::post('affectations/validate/{affectation}', [AffectationController::class, 'validateAssignment'])
        ->name('affectations.validate');
    Route::post('affectations/bulk-assign', [AffectationController::class, 'bulkAssign'])
        ->name('affectations.bulk-assign');

    // Grades Management (TODO: Create NoteController)
    // Route::resource('notes', NoteController::class);
    // Route::post('notes/import', [NoteController::class, 'import'])->name('notes.import');
    // Route::get('notes/export', [NoteController::class, 'export'])->name('notes.export');

    // Reports
    Route::get('reports/teaching-loads', [AdminController::class, 'teachingLoadsReport'])
        ->name('reports.teaching-loads');
    Route::get('reports/department-stats', [AdminController::class, 'departmentStats'])
        ->name('reports.department-stats');

    // Activities Management
    Route::get('activities', [AdminController::class, 'activities'])->name('admin.activities');

    // API endpoints for real-time updates
    Route::get('api/dashboard-stats', [AdminController::class, 'getDashboardStats'])
        ->name('admin.api.dashboard-stats');
    Route::get('api/recent-activities', [AdminController::class, 'getRecentActivitiesApi'])
        ->name('admin.api.recent-activities');
});

// Department Head Routes (TODO: Create ChefController)
/*
Route::prefix('chef')->middleware(['auth', 'role:chef'])->group(function () {
    Route::get('/dashboard', [ChefController::class, 'dashboard'])->name('chef.dashboard');

    // Department Management
    Route::get('department', [ChefController::class, 'department'])->name('chef.department');
    Route::put('department', [ChefController::class, 'updateDepartment'])->name('chef.department.update');

    // Teachers Management
    Route::get('teachers', [ChefController::class, 'teachers'])->name('chef.teachers');
    Route::get('teachers/{Enseignant}/load', [ChefController::class, 'teacherLoad'])->name('chef.teachers.load');

    // Assignments
    Route::get('assignments', [ChefController::class, 'assignments'])->name('chef.assignments');
    Route::post('assignments/propose', [ChefController::class, 'proposeAssignment'])
        ->name('chef.assignments.propose');
    Route::post('assignments/{assignment}/recommend', [ChefController::class, 'recommendAssignment'])
        ->name('chef.assignments.recommend');

    // Schedule
    Route::get('schedule', [ChefController::class, 'schedule'])->name('chef.schedule');
    Route::post('schedule/generate', [ChefController::class, 'generateSchedule'])
        ->name('chef.schedule.generate');
});
*/

// Coordinator Routes (TODO: Create CoordonnateurController)
/*
Route::prefix('coordinator')->middleware(['auth', 'role:coordonnateur'])->group(function () {
    Route::get('/dashboard', [CoordonnateurController::class, 'dashboard'])->name('coordinator.dashboard');

    // Program Management
    Route::get('program', [CoordonnateurController::class, 'program'])->name('coordinator.program');
    Route::put('program', [CoordonnateurController::class, 'updateProgram'])->name('coordinator.program.update');

    // Courses Management
    Route::get('courses', [CoordonnateurController::class, 'courses'])->name('coordinator.courses');
    Route::post('courses/suggest', [CoordonnateurController::class, 'suggestCourse'])
        ->name('coordinator.courses.suggest');

    // Grades Management
    Route::get('grades', [CoordonnateurController::class, 'grades'])->name('coordinator.grades');
    Route::post('grades/validate', [CoordonnateurController::class, 'validateGrades'])
        ->name('coordinator.grades.validate');
    Route::get('grades/statistics', [CoordonnateurController::class, 'gradeStatistics'])
        ->name('coordinator.grades.statistics');
});
*/

// Enseignant Routes
Route::prefix('enseignant')->middleware(['auth', \App\Http\Middleware\CheckRole::class . ':enseignant'])->group(function () {
    Route::get('/enseignant-dashboard', [EnseignantController::class, 'dashboard'])->name('enseignant.dashboard');

    // UE Management and Affectation Requests
    Route::get('/ues', [EnseignantUEController::class, 'index'])->name('enseignant.ues.index');
    Route::get('/ue-status', [EnseignantUEController::class, 'ueStatus'])->name('enseignant.ue.status');
    Route::get('/ues/status', [EnseignantUEController::class, 'status'])->name('enseignant.ues.status');
    Route::post('/ues/request', [EnseignantUEController::class, 'requestAffectation'])->name('enseignant.ues.request');
    Route::delete('/ues/cancel/{affectation}', [EnseignantUEController::class, 'cancelRequest'])->name('enseignant.ues.cancel');
    Route::get('/ues/{ue}/details', [EnseignantUEController::class, 'getUEDetails'])->name('enseignant.ues.details');
    Route::get('/ues/stats', [EnseignantUEController::class, 'getStats'])->name('enseignant.ues.stats');

    // Legacy routes (keeping for compatibility)
    Route::get('/unites', [EnseignantController::class, 'unites'])->name('enseignant.unites');

    // Notes
    Route::get('/notes', [EnseignantController::class, 'notes'])->name('enseignant.notes');
    // Route::post('/notes/import', [EnseignantController::class, 'importNotes'])->name('enseignant.notes.import'); // REMOVED - not in allowed list
    Route::post('/notes/store', [EnseignantController::class, 'storeNotes'])->name('enseignant.notes.store');

    // Emploi du temps
    Route::get('/emploi-du-temps', [EnseignantController::class, 'emploiDuTemps'])->name('enseignant.emploi-du-temps');
    Route::get('/emploi-du-temps/export', [EnseignantController::class, 'exportEmploiDuTemps'])->name('enseignant.emploi-du-temps.export');
});

// Chef de Département Routes
Route::prefix('chef')->name('chef.')->middleware(['auth', \App\Http\Middleware\CheckRole::class . ':chef'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'dashboard'])->name('dashboard');

    // Gestion des UEs
    Route::get('/unites-enseignement', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'unitesEnseignement'])->name('unites-enseignement');
    Route::get('/unites-enseignement/next-year', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'nextYearUEs'])->name('unites-enseignement.next-year');
    Route::get('/ue/{id}/details', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'showUeDetails'])->name('ue.details');
    Route::put('/ue/{id}/vacataire-availability', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'updateVacataireAvailability'])->name('ue.update-vacataire-availability');
    Route::get('/ue/{id}/affecter', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'showAffectationForm'])->name('ue.affecter');

    // Gestion des enseignants
    Route::get('/enseignants', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'enseignants'])->name('enseignants');
    Route::get('/enseignants/{id}/charge-horaire', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'genererChargeHoraire'])->name('enseignants.charge-horaire');
    Route::get('/enseignants-list', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'getEnseignantsList'])->name('enseignants-list');
    Route::get('/test-user', function () {
        $user = Auth::user();
        return response()->json([
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'departement_id' => $user->departement_id,
                'departement' => $user->departement ? $user->departement->nom : null
            ] : null,
            'authenticated' => Auth::check()
        ]);
    })->name('test-user');

    // Gestion des affectations
    Route::get('/affectations', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'affectations'])->name('affectations');
    Route::post('/affectations/{id}/valider', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'validerAffectation'])->name('affectations.valider');
    Route::post('/affecter-ue', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'affecterUE'])->name('affecter-ue');
    Route::get('/compatible-ues/{enseignant_id}', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'getCompatibleUEs'])->name('compatible-ues');
    Route::post('/save-drag-drop-assignments', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'saveDragDropAssignments'])->name('save-drag-drop-assignments');

    // Gestion des demandes pour l'année prochaine
    Route::get('/gestion-demandes', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'gestionDemandes'])->name('gestion-demandes');
    Route::post('/demandes/{id}/approve', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'approveRequest'])->name('demandes.approve');
    Route::post('/demandes/{id}/reject', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'rejectRequest'])->name('demandes.reject');
    // Route::get('/export-demandes', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'exportDemandes'])->name('export-demandes'); // REMOVED - not in allowed list

    // Historique et rapports
    Route::get('/historique', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'historique'])->name('historique');
    Route::get('/rapports', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'rapports'])->name('rapports');
    Route::post('/export/rapports', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'exportRapports'])->name('export.rapports');

    // Import/Export
    Route::post('/export', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'exportData'])->name('export');
    Route::post('/import', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'importData'])->name('import');

    // Notifications
    Route::post('/notifications/mark-read', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'marquerNotificationsLues'])->name('notifications.mark-read');

    // Additional API routes for AJAX calls (GET requests don't need CSRF)
    Route::get('/api/ue/{id}', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'getUeDetails'])->name('api.ue.details');
    Route::get('/api/enseignant/{id}/affectations', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'getEnseignantAffectations'])->name('api.enseignant.affectations');
    Route::get('/api/statistics', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'getStatistics'])->name('api.statistics');

    // Bulk operations
    Route::post('/affectations/bulk-validate', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'bulkValidateAffectations'])->name('affectations.bulk-validate');
    Route::post('/affectations/bulk-reject', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'bulkRejectAffectations'])->name('affectations.bulk-reject');

    // UE management
    Route::post('/ues/{id}/mark-vacant', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'markUeVacant'])->name('ues.mark-vacant');
    Route::post('/ues/{id}/assign-priority', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'assignUePriority'])->name('ues.assign-priority');

    // UE editing
    Route::get('/unites-enseignement/{id}/edit', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'editUE'])->name('unites-enseignement.edit');
    Route::put('/unites-enseignement/{id}', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'updateUE'])->name('unites-enseignement.update');

    // Export historique
    Route::post('/export/historique', [\App\Http\Controllers\Admin\chef\ChefDepartementController::class, 'exportHistorique'])->name('export.historique');
});

// Coordonnateur de Filière Routes
Route::prefix('coordonnateur')->name('coordonnateur.')->middleware(['auth', \App\Http\Middleware\CheckRole::class . ':coordonnateur'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'dashboard'])->name('dashboard');

    // Gestion des UEs de la filière
    Route::get('/unites-enseignement', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'unitesEnseignement'])->name('unites-enseignement');
    Route::get('/unites-enseignement/create', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'createUE'])->name('unites-enseignement.create');
    Route::get('/ue/{id}/details', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'showUeDetails'])->name('ue.details');
    Route::post('/unites-enseignement/creer', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'creerUE'])->name('unites-enseignement.creer');
    Route::post('/unites-enseignement/import', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'importUEs'])->name('unites-enseignement.import');
    Route::post('/unites-enseignement/{id}/groupes', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'definirGroupes'])->name('unites-enseignement.groupes');

    // UE editing
    Route::get('/unites-enseignement/{id}/edit', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'editUE'])->name('unites-enseignement.edit');
    Route::put('/unites-enseignement/{id}', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'updateUE'])->name('unites-enseignement.update');

    // Gestion des vacataires
    Route::get('/vacataires', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'vacataires'])->name('vacataires');
    Route::post('/vacataires/affecter', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'affecterVacataire'])->name('vacataires.affecter');
    Route::post('/vacataires/creer', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'creerVacataire'])->name('vacataires.creer');

    // Consultation des affectations
    Route::get('/affectations', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'affectations'])->name('affectations');

    // Gestion des emplois du temps
    Route::get('/emplois-du-temps', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'emploisDuTemps'])->name('emplois-du-temps');
    Route::post('/emplois-du-temps', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'saveScheduleChanges'])->name('emplois-du-temps.save');
    Route::post('/save-emploi-du-temps', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'saveEmploiDuTempsEnhanced'])->name('save-emploi-du-temps');
    Route::get('/emploi-du-temps/export', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'exportEmploiDuTemps'])->name('emploi-du-temps.export');

    // Historique
    Route::get('/historique', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'historique'])->name('historique');

    // Import/Export
    Route::post('/export', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'exportData'])->name('export');
    Route::post('/import', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'importData'])->name('import');

    // AJAX routes for vacataires (same as chef)
    Route::get('/api/vacataires-list', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getVacatairesList'])->name('api.vacataires.list');
    Route::get('/api/compatible-ues/{vacataire_id}', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getCompatibleUEs'])->name('api.compatible-ues');

    // API routes
    Route::get('/api/filieres/{id}/ues', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getUesFiliere'])->name('api.filieres.ues');
    Route::get('/api/statistics', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getStatistics'])->name('api.statistics');
    Route::get('/api/emploi-du-temps-data', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getEmploiDuTempsData'])->name('api.emploi-du-temps.data');
    Route::get('/api/affectations/{year}', [\App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class, 'getAffectationsData'])->name('api.affectations.data');

    // REMOVED: No longer needed - all operations handled by save function
});

// Vacataire Routes
Route::prefix('vacataire')->middleware(['auth', \App\Http\Middleware\CheckRole::class . ':vacataire'])->group(function () {
    Route::get('/vacataire-dashboard', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'dashboard'])->name('vacataire.dashboard');

    // UE Management
    Route::get('/unites-enseignement', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'unitesEnseignement'])->name('vacataire.unites-enseignement');
    Route::get('/ue/{id}/details', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'ueDetails'])->name('vacataire.ue.details');

    // Notes Management
    Route::get('/notes', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'notes'])->name('vacataire.notes');
    Route::post('/notes/import', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'importNotes'])->name('vacataire.notes.import');
    Route::post('/notes/store', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'storeNote'])->name('vacataire.notes.store');
    Route::put('/notes/{note}', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'updateNote'])->name('vacataire.notes.update');

    // Emploi du temps
    Route::get('/emploi-du-temps', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'emploiDuTemps'])->name('vacataire.emploi-du-temps');
    Route::post('/emploi-du-temps/export', [\App\Http\Controllers\Admin\vacataire\VacataireController::class, 'exportEmploiDuTemps'])->name('vacataire.emploi-du-temps.export');
});

// Common Routes (accessible by all authenticated users) (TODO: Create ProfileController)
/*
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])
        ->name('profile.change-password');

    // Notifications (TODO: Create NotificationController)
    // Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    // Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
    //     ->name('notifications.mark-all-read');
    // Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])
    //     ->name('notifications.mark-as-read');

    // Documents (TODO: Create DocumentController)
    // Route::get('/documents', [DocumentController::class, 'index'])->name('documents');
    // Route::get('/documents/download/{document}', [DocumentController::class, 'download'])
    //     ->name('documents.download');
});
*/

// API Routes for Schedule Management
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::post('/schedule/store', [App\Http\Controllers\Api\ScheduleController::class, 'store'])->name('api.schedule.store');
    Route::post('/schedule/check-conflict', [App\Http\Controllers\Api\ScheduleController::class, 'checkConflict'])->name('api.schedule.check-conflict');
    Route::delete('/schedule/destroy', [App\Http\Controllers\Api\ScheduleController::class, 'destroy'])->name('api.schedule.destroy');
});

// Fallback Route
Route::fallback(function () {
    return redirect()->route('login');
});

Route::post('/coordonnateur/download-rapport-analytique', [
    \App\Http\Controllers\Admin\coordonnateur\CoordonnateurController::class,
    'downloadRapportAnalytiquePDF'
])->name('coordonnateur.download-rapport-analytique');

Route::post('/chef/rapports/export-pdf', [
    \App\Http\Controllers\Admin\chef\ChefDepartementController::class,
    'exportRapportsPdf'
])->name('chef.rapports.export.pdf');
