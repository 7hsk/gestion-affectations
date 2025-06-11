<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Use role-based redirection instead of generic dashboard
        $role = auth()->user()->role;
        $routes = [
            'admin' => 'admin.dashboard',
            'chef' => 'chef.dashboard',
            'coordonnateur' => 'coordonnateur.dashboard',
            'enseignant' => 'enseignant.dashboard',
            'vacataire' => 'vacataire.dashboard', // Vacataire has its own dashboard with purple-orange theme
        ];

        if (!array_key_exists($role, $routes)) {
            auth()->logout();
            return redirect('/')->withErrors([
                'message' => 'Your account has an invalid role configuration.'
            ]);
        }

        // Log the login activity
        \App\Models\Activity::log(
            'auth',
            'login',
            "Connexion réussie: " . auth()->user()->name . " (" . auth()->user()->role . ")",
            auth()->user(),
            [
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->role,
                'user_email' => auth()->user()->email,
                'login_method' => 'Email/Password',
                'success' => true,
                'redirect_to' => $routes[$role]
            ]
        );

        return redirect()->route($routes[$role]);
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log the logout activity before logging out
        if (auth()->check()) {
            \App\Models\Activity::log(
                'logout',
                'logout',
                "Déconnexion: " . auth()->user()->name . " (" . auth()->user()->role . ")",
                auth()->user(),
                [
                    'user_name' => auth()->user()->name,
                    'user_role' => auth()->user()->role,
                    'session_duration' => 'N/A'
                ]
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
