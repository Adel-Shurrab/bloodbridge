<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Donor;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{

    public function showRegisterSelection(): View
    {
        return view('auth.register_selection');
    }

    /**
     * Show the Donor Registration Form
     */
    public function show(): View
    {
        return view('auth.register-donor');
    }

    /**
     * Handle the Donor Registration Logic
     */
    public function storeDonor(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'national_id' => ['required', 'string', 'max:20', 'unique:donors'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'city' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request) {
            // A. Create the Login User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'donor',
                'is_active' => true,
            ]);

            // B. Create the Profile linked to that User
            Donor::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'city' => $request->city,
                'blood_type' => null,
            ]);

            // C. Trigger Events & Login
            event(new Registered($user));
            Auth::login($user);
        });

        // 3. Redirect
        return redirect(route('login', absolute: false));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
