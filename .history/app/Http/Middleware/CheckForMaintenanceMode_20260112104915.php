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

            // 1. Always allow authenticated admins to bypass everything (including public site)
            if ($request->user() && $request->user()->role === \App\Models\User::ROLE_ADMIN) {
                return $next($request);
            }

            // 2. Allow essential auth and system routes for everyone (to allow admin login)
            if (
                $request->is('login*') ||
                $request->is('*/login') ||
                $request->is('logout') ||
                $request->is('*/logout') ||
                $request->is('password*') ||
                $request->is('livewire*') ||
                $request->is('up')
            ) {
                return $next($request);
            }

            // 3. Allow Livewire only for admins (we already handled admin above, so if we're here it's not an admin)
            // We should NOT allow livewire for non-admins if we want to block them entirely

            // 4. For all other routes (donors, organizations, and public visitors), show maintenance page
            return response()->view('errors.maintenance', [
                'settings' => $settings,
            ], 503);
        }

        return $next($request);
    }
}
