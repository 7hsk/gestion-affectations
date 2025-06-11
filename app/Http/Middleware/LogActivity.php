<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Activity;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            $this->logPageVisit($request);
        }

        return $response;
    }

    /**
     * Log page visits and important actions
     */
    private function logPageVisit(Request $request)
    {
        $user = auth()->user();
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $method = $request->method();
        $url = $request->url();

        // Skip logging for certain routes to avoid spam
        $skipRoutes = [
            'api.',
            '.ajax',
            '.json',
            'notifications.',
            'assets.',
            'css.',
            'js.',
            'images.'
        ];

        foreach ($skipRoutes as $skipRoute) {
            if ($routeName && str_contains($routeName, $skipRoute)) {
                return;
            }
        }

        // Skip AJAX requests and API calls
        if ($request->ajax() || $request->wantsJson()) {
            return;
        }

        // Only log GET requests for page visits
        if ($method !== 'GET') {
            return;
        }

        // Determine activity type and description based on route
        $activityData = $this->getActivityDataFromRoute($routeName, $user, $request);

        if ($activityData) {
            Activity::log(
                $activityData['type'],
                $activityData['action'],
                $activityData['description'],
                null,
                array_merge($activityData['properties'], [
                    'route_name' => $routeName,
                    'url' => $url,
                    'user_agent' => $request->userAgent(),
                    'referer' => $request->header('referer')
                ])
            );
        }
    }

    /**
     * Get activity data based on route name
     */
    private function getActivityDataFromRoute($routeName, $user, $request)
    {
        if (!$routeName) {
            return null;
        }

        $role = $user->role;
        $userName = $user->name;

        // Dashboard visits
        if (str_contains($routeName, '.dashboard')) {
            return [
                'type' => 'system',
                'action' => 'dashboard_visit',
                'description' => "Accès au tableau de bord {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'dashboard_type' => $role
                ]
            ];
        }

        // UE management pages
        if (str_contains($routeName, '.ues') || str_contains($routeName, '.unitesEnseignement')) {
            return [
                'type' => 'system',
                'action' => 'ue_page_visit',
                'description' => "Consultation page UEs par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'ue_management'
                ]
            ];
        }

        // Affectations pages
        if (str_contains($routeName, '.affectations')) {
            return [
                'type' => 'system',
                'action' => 'affectations_page_visit',
                'description' => "Consultation page affectations par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'affectations'
                ]
            ];
        }

        // Notes pages
        if (str_contains($routeName, '.notes')) {
            return [
                'type' => 'system',
                'action' => 'notes_page_visit',
                'description' => "Consultation page notes par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'notes_management'
                ]
            ];
        }

        // Schedule pages
        if (str_contains($routeName, '.emploi') || str_contains($routeName, '.schedule')) {
            return [
                'type' => 'system',
                'action' => 'schedule_page_visit',
                'description' => "Consultation emploi du temps par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'schedule'
                ]
            ];
        }

        // User management pages (admin)
        if (str_contains($routeName, '.users') && $role === 'admin') {
            return [
                'type' => 'system',
                'action' => 'users_page_visit',
                'description' => "Consultation gestion utilisateurs par admin: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'user_management'
                ]
            ];
        }

        // Department management pages
        if (str_contains($routeName, '.departements')) {
            return [
                'type' => 'system',
                'action' => 'departments_page_visit',
                'description' => "Consultation gestion départements par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'department_management'
                ]
            ];
        }

        // Vacataires pages
        if (str_contains($routeName, '.vacataires')) {
            return [
                'type' => 'system',
                'action' => 'vacataires_page_visit',
                'description' => "Consultation page vacataires par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'vacataires_management'
                ]
            ];
        }

        // Reports and analytics pages
        if (str_contains($routeName, '.rapports') || str_contains($routeName, '.analytics') || str_contains($routeName, '.reports')) {
            return [
                'type' => 'system',
                'action' => 'reports_page_visit',
                'description' => "Consultation rapports/analytics par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'reports_analytics'
                ]
            ];
        }

        // Activities page (admin)
        if (str_contains($routeName, '.activities') || str_contains($routeName, '.activites')) {
            return [
                'type' => 'system',
                'action' => 'activities_page_visit',
                'description' => "Consultation page activités par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'activities_monitoring'
                ]
            ];
        }

        // Settings pages
        if (str_contains($routeName, '.settings') || str_contains($routeName, '.config')) {
            return [
                'type' => 'system',
                'action' => 'settings_page_visit',
                'description' => "Consultation paramètres par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'settings'
                ]
            ];
        }

        // Profile pages
        if (str_contains($routeName, '.profile') || str_contains($routeName, '.profil')) {
            return [
                'type' => 'system',
                'action' => 'profile_page_visit',
                'description' => "Consultation profil par {$role}: {$userName}",
                'properties' => [
                    'user_role' => $role,
                    'page_type' => 'profile'
                ]
            ];
        }

        return null;
    }
}
