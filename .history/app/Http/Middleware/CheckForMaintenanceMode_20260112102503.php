<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Settings\GeneralSettings;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(GeneralSettings::class);

        // If site is disabled (site_active is true)
        if ($settings->site_active) {
            // 1. Always allow access to login, logout, password resets and health check
            if (
                $request->is('login*') ||
                $request->is('logout') ||
                $request->is('password*') ||
                $request->is('up')
            ) {
                return $next($request);
            }

            // 2. Allow ALL Livewire requests (to prevent AJAX overlays in dashboards)
            if ($request->is('livewire*')) {
                return $next($request);
            }

            // 3. Allow authenticated admins to bypass everything else
            if ($request->user() && $request->user()->role === \App\Models\User::ROLE_ADMIN) {
                return $next($request);
            }

            // 4. Allow access to designated panels (Admin, Donor, Org)
            if (
                $request->is('admin*') ||
                $request->is('donor*') ||
                $request->is('org*')
            ) {
                return $next($request);
            }

            // For all other public routes, show maintenance page
            return response()->view('errors.maintenance', [
                'settings' => $settings,
            ], 503);
        }

        return $next($request);
    }
}
