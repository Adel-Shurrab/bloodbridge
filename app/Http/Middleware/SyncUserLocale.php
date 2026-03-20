<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SyncUserLocale
{
    /**
     * Handle an incoming request.
     *
     * We call $next($request) FIRST, so route-level middleware like
     * localeSessionRedirect from mcamara/laravel-localization has already
     * run and set app()->getLocale() by the time we read it.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $currentLocale = app()->getLocale();

            if ($user->locale !== $currentLocale) {
                $user->update(['locale' => $currentLocale]);
            }
        }

        return $response;
    }
}
