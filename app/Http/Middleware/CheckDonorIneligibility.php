<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckDonorIneligibility
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->role === UserRole::ADMIN) {
            return $next($request);
        }

        if (!$user || !$user->donor) {
            return $next($request);
        }

        if ($user->donor->healthProfile?->chronic_disease) {

            if (
                !$request->routeIs('filament.donor.pages.ineligible-donor') &&
                !$request->routeIs('filament.donor.auth.logout')
            ) {
                return redirect()->route('filament.donor.pages.ineligible-donor');
            }
        }

        return $next($request);
    }
}
