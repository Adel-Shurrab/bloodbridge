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

        if ($settings->maintenance_mode) {

            $user = $request->user();

            if ($user && $user->role === \App\Enums\UserRole::ADMIN) {
                return $next($request);
            }

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

            return response()->view('errors.maintenance', [
                'settings' => $settings,
            ], 503);
        }

        return $next($request);
    }
}
