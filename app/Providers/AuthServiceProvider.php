<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Model::class => ModelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('admin-access', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('chef-access', function ($user) {
            return $user->role === 'chef';
        });

        Gate::define('coordonnateur-access', function ($user) {
            return $user->role === 'coordonnateur';
        });

        Gate::define('Enseignant-access', function ($user) {
            return in_array($user->role, ['enseignant', 'vacataire']);
        });
    }
}
