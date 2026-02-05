<?php

namespace App\Http\Middleware;

use App\Enums\OrganizationStatus;
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
        $tenant = Filament::getTenant();

        // If there's no tenant (organization), continue normally
        if (!$tenant) {
            return $next($request);
        }

        // Check organization approval status
        if ($tenant->approval_status === OrganizationStatus::PENDING) {
            // Allow access only to the pending approval page
            if (!$request->routeIs('filament.organization.pages.pending-approval')) {
                return redirect()->route('filament.organization.pages.pending-approval', ['tenant' => $tenant]);
            }
        } elseif ($tenant->approval_status === OrganizationStatus::REJECTED) {
            // For rejected orgs, you might want a different page or message
            // For now, also redirect to pending page (can be customized later)
            if (!$request->routeIs('filament.organization.pages.pending-approval')) {
                return redirect()->route('filament.organization.pages.pending-approval', ['tenant' => $tenant]);
            }
        }

        return $next($request);
    }
}
