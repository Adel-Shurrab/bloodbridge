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
            // Allow access to admin, donor, and organization panels, and auth routes
            if (
                $request->is('admin*') ||
                $request->is('donor*') ||
                $request->is('org*') ||
                $request->is('login*') ||
                $request->is('logout') ||
                $request->is('password*') ||
                $request->is('up')
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
