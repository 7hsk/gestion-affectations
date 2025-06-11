<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        if (empty($roles) || in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return redirect()->route('login')->withErrors(['message' => 'You do not have permission to access this page.']);
    }
}
