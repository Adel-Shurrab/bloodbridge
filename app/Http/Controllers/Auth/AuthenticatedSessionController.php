<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Traits\ProvidesPublicStats;

class AuthenticatedSessionController extends Controller
{
    use ProvidesPublicStats;

    /**
     * Display the login view.
     */
    public function create(): View
    {
        $stats = $this->getStats();
        return view('auth.login', compact('stats'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        $url = $user->getDashboardUrl();

        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $redirect = $request->query('redirect', '/');
        return redirect($redirect);
    }
}
