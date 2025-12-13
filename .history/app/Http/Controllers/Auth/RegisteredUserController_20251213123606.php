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
use App\Models\DonorHealthProfile;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{

    public function showRegisterSelection(): View
    {
        return view('auth.register_selection');
    }

    /**
     * Show the Donor Registration Form
     */
    public function showDonorRegistrationForm(): View
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
            'national_id' => ['required', 'string', 'digits:9', 'unique:donors'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'city' => ['required', 'string', 'max:255'],
            // Health Profile Fields
            'weight' => ['required', 'integer', 'min:50', 'max:200'],
            'height' => ['required', 'integer', 'min:140', 'max:220'],
            'chronic_disease' => ['nullable', 'boolean'],
            'recent_donation' => ['nullable', 'boolean'],
            'infection' => ['nullable', 'boolean'],
            'is_smoker' => ['nullable', 'boolean'],
            'has_recent_surgery' => ['nullable', 'boolean'],
            'surgery_date' => ['nullable', 'date'],
            'last_donation_date' => ['nullable', 'date'],
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
            $donor = Donor::create([
                'user_id' => $user->id,
                'national_id' => $request->national_id,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'city' => $request->city,
                'blood_type' => null,
            ]);

            // C. Create Health Profile and Calculate Eligibility
            $eligibilityData = $this->checkEligibility($request);

            DonorHealthProfile::create([
                'donor_id' => $donor->id,
                'weight' => $request->weight,
                'height' => $request->height,
                'chronic_disease' => $request->boolean('chronic_disease'),
                'recent_donation' => $request->boolean('recent_donation'),
                'infection' => $request->boolean('infection'),
                'is_smoker' => $request->boolean('is_smoker'),
                'has_recent_surgery' => $request->boolean('has_recent_surgery'),
                'surgery_date' => $request->surgery_date,
                'last_donation_date' => $request->last_donation_date,
                'is_eligible' => $eligibilityData['is_eligible'],
                'next_eligible_date' => $eligibilityData['next_eligible_date'],
            ]);

            // D. Trigger Events & Login
            event(new Registered($user));
            Auth::login($user);
        });

        // 3. Redirect
        return redirect(route('login', absolute: false));
    }

    /**
     * Check eligibility based on health profile
     */
    private function checkEligibility($request): array
    {
        $today = Carbon::now();
        $isEligible = true;
        $nextEligibleDate = null;

        // Check weight (minimum 50kg)
        if ($request->weight < 50) {
            $isEligible = false;
        }

        // Check height (minimum 140cm)
        if ($request->height < 140) {
            $isEligible = false;
        }

        // Check chronic disease
        if ($request->boolean('chronic_disease')) {
            $isEligible = false;
        }

        // Check infection
        if ($request->boolean('infection')) {
            $isEligible = false;
        }

        // Check recent donation (90 days rule)
        if ($request->last_donation_date) {
            $lastDonation = Carbon::parse($request->last_donation_date);
            $daysSinceDonation = $today->diffInDays($lastDonation);

            if ($daysSinceDonation < 90) {
                $isEligible = false;
                $futureDate = $lastDonation->addDays(90);
                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // Check recent surgery (4 weeks rule)
        if ($request->boolean('has_recent_surgery') && $request->surgery_date) {
            $surgery = Carbon::parse($request->surgery_date);
            $daysSinceSurgery = $today->diffInDays($surgery);

            if ($daysSinceSurgery < 28) {
                $isEligible = false;
                $futureDate = $surgery->addDays(28);
                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        return [
            'is_eligible' => $isEligible,
            'next_eligible_date' => $nextEligibleDate,
        ];
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
