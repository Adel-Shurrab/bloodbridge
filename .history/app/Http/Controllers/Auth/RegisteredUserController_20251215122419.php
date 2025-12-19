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
        // First, check for PERMANENT ineligibility (before validation)
        if ($request->boolean('chronic_disease') || $request->integer('weight') < 50 || $request->integer('height') < 140) {
            // This shouldn't normally happen as frontend prevents this, but just in case
            return redirect()->route('register.donor.show')->withErrors([
                'general' => 'عذراً، لا يمكن قبول تسجيلك كمتبرع بناءً على المعايير الطبية.'
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'national_id' => ['required', 'string', 'digits:9', 'unique:donors'],
            'birth_date' => ['required', 'date', 'before_or_equal:-18 years'],
            'gender' => ['required', 'in:male,female'],
            'city' => ['required', 'string', 'max:255'],
            // Health Profile Fields
            'weight' => ['required', 'integer', 'min:50', 'max:200'],
            'height' => ['required', 'integer', 'min:140', 'max:220'],
            'chronic_disease' => ['nullable'],
            'recent_donation' => ['nullable'],
            'infection' => ['nullable'],
            'is_smoker' => ['nullable'],
            'has_recent_surgery' => ['nullable'],
            'surgery_date' => ['nullable', 'date', 'before_or_equal:today'],
            'last_donation_date' => ['nullable', 'date', 'before_or_equal:today'],
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

        // PERMANENT INELIGIBILITY - Don't proceed if these are true
        if ($request->boolean('chronic_disease')) {
            return ['is_eligible' => false, 'next_eligible_date' => null];
        }

        // Check Physical Stats - PERMANENT INELIGIBILITY
        if ($request->integer('weight') < 50 || $request->integer('height') < 140) {
            return ['is_eligible' => false, 'next_eligible_date' => null];
        }

        // TEMPORARY CONDITIONS - Allow registration but mark ineligible until condition clears

        // Check Infection (14 Days Rule)
        if ($request->boolean('infection')) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays(14);
        }

        // Check Recent Donation (90 Days Rule)
        if ($request->boolean('recent_donation') && $request->last_donation_date) {
            $lastDonation = Carbon::parse($request->last_donation_date);
            // diffInDays gives absolute difference, we need to check if donation was LESS than 90 days ago
            $daysSinceDonation = $today->diffInDays($lastDonation, false);

            if ($daysSinceDonation < 90) {
                $isEligible = false;
                // Calculate when they'll be eligible (90 days after their last donation)
                $futureDate = $lastDonation->copy()->addDays(90);

                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // Check Recent Surgery (28 Days Rule)
        if ($request->boolean('has_recent_surgery') && $request->surgery_date) {
            $surgery = Carbon::parse($request->surgery_date);
            $daysSinceSurgery = $today->diffInDays($surgery, false);

            if ($daysSinceSurgery < 28) {
                $isEligible = false;
                // Calculate when they'll be eligible (28 days after their surgery)
                $futureDate = $surgery->copy()->addDays(28);

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
