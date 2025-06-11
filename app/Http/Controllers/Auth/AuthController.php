<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Check if user is logged in
        if (Auth::check()) {
            // Log the logout action
            Log::info('User auto-logged out from login page', ['user_id' => Auth::id()]);

            // Perform logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $credentials['email']]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            Log::warning('User not found', ['email' => $credentials['email']]);
            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            Log::info('User logged in', ['user_id' => Auth::id(), 'role' => Auth::user()->role]);
            return $this->redirectToDashboard(Auth::user()->role);
        }

        Log::warning('Login failed', ['email' => $credentials['email']]);
        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    protected function redirectToDashboard($role)
    {
        $routes = [
            'admin' => 'admin.dashboard',
            'chef' => 'chef.dashboard',
            'coordonnateur' => 'coordonnateur.dashboard',
            'enseignant' => 'enseignant.dashboard',
            'vacataire' => 'vacataire.dashboard', // Vacataire has its own dashboard with purple-orange theme
        ];

        if (!array_key_exists($role, $routes)) {
            Log::error('Unknown user role attempted login', ['role' => $role]);
            Auth::logout();
            return redirect('/')->withErrors([
                'message' => 'Your account has an invalid role configuration.'
            ]);
        }

        return redirect()->route($routes[$role]);
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();
        $role = Auth::user()->role ?? 'unknown';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['user_id' => $userId, 'role' => $role]);

        return redirect('/');
    }
}
