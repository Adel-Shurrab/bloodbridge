<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     */
    public function __invoke(Request $request, $id, $hash): RedirectResponse
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        if ($user->hasVerifiedEmail()) {
            if (Auth::check() && Auth::id() == $user->id) {
                return redirect()->intended($user->getDashboardUrl() . '?verified=1');
            }
            return redirect()->route('login')->with('status', 'تم تأكيد بريدك الإلكتروني مسبقاً. يمكنك تسجيل الدخول.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if (Auth::check() && Auth::id() == $user->id) {
            return redirect()->intended($user->getDashboardUrl() . '?verified=1');
        }

        return redirect()->route('login')->with('status', 'تم تأكيد بريدك الإلكتروني بنجاح. يمكنك تسجيل الدخول الآن.');
    }
}
