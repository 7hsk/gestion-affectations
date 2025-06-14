<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEnseignantAccess
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $allowedRoles = ['enseignant', 'chef', 'coordonnateur'];
        if (in_array($request->user()->role, $allowedRoles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return redirect()->route('login')->withErrors(['message' => 'You do not have permission to access this page.']);
    }
}
