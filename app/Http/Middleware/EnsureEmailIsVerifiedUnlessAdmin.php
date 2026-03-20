<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerifiedUnlessAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === UserRole::ADMIN) {
            return $next($request);
        }

        return app(\Illuminate\Auth\Middleware\EnsureEmailIsVerified::class)->handle($request, $next);
    }
}


