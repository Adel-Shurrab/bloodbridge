<?php

namespace App\Http\Middleware;

use App\Enums\OrganizationStatus;
use App\Enums\UserRole;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOrganizationApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->role === UserRole::ADMIN) {
            return $next($request);
        }

        $tenant = Filament::getTenant();

        if (!$tenant) {
            return $next($request);
        }

        if ($tenant->approval_status === OrganizationStatus::PENDING) {
            
            if (!$request->routeIs('filament.organization.pages.pending-approval')) {
                return redirect()->route('filament.organization.pages.pending-approval', ['tenant' => $tenant]);
            }
        } elseif ($tenant->approval_status === OrganizationStatus::REJECTED) {

            if (!$request->routeIs('filament.organization.pages.pending-approval')) {
                return redirect()->route('filament.organization.pages.pending-approval', ['tenant' => $tenant]);
            }
        }

        return $next($request);
    }
}
