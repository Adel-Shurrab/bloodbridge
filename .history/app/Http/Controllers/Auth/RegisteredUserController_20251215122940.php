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
    // Eligibility Constants
    private const MIN_WEIGHT = 50;
    private const MAX_WEIGHT = 200;
    private const MIN_HEIGHT = 140;
    private const MAX_HEIGHT = 220;
    private const MIN_AGE = 18;
    private const DONATION_INTERVAL_DAYS = 90;
    private const SURGERY_RECOVERY_DAYS = 28;
    private const INFECTION_WAIT_DAYS = 14;

    /**
     * Show the Donor Registration Form
     */
    public function showDonorRegistrationForm(): View
    {
        return view('auth.register-donor');
    }

    /**
     * Store a new donor registration with validation and eligibility check
     */
    public function storeDonor(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->donorValidationRules());

        try {
            DB::transaction(function () use ($validated) {
                $user = $this->createUser($validated);
                $donor = $this->createDonor($user, $validated);
                $this->createHealthProfile($donor, $validated);

                event(new Registered($user));
                Auth::login($user);
            });

            return redirect(route('login', absolute: false));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.']);
        }
    }

    /**
     * Get validation rules for donor registration
     */
    private function donorValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'national_id' => ['required', 'string', 'digits:9', 'unique:donors'],
            'birth_date' => ['required', 'date', 'before_or_equal:-' . self::MIN_AGE . ' years'],
            'gender' => ['required', 'in:male,female'],
            'city' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'integer', 'min:' . self::MIN_WEIGHT, 'max:' . self::MAX_WEIGHT],
            'height' => ['required', 'integer', 'min:' . self::MIN_HEIGHT, 'max:' . self::MAX_HEIGHT],
            'chronic_disease' => ['nullable', 'boolean'],
            'recent_donation' => ['nullable', 'boolean'],
            'infection' => ['nullable', 'boolean'],
            'is_smoker' => ['nullable', 'boolean'],
            'has_recent_surgery' => ['nullable', 'boolean'],
            'surgery_date' => ['nullable', 'date', 'before_or_equal:today'],
            'last_donation_date' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }

    /**
     * Create a new user account
     */
    private function createUser(array $validated): User
    {
        return User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'donor',
            'is_active' => true,
        ]);
    }

    /**
     * Create donor profile linked to user
     */
    private function createDonor(User $user, array $validated): Donor
    {
        return Donor::create([
            'user_id' => $user->id,
            'national_id' => $validated['national_id'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'city' => $validated['city'],
            'blood_type' => null,
        ]);
    }

    /**
     * Create health profile and calculate eligibility
     */
    private function createHealthProfile(Donor $donor, array $validated): DonorHealthProfile
    {
        $eligibilityData = $this->checkEligibility($validated);

        return DonorHealthProfile::create([
            'donor_id' => $donor->id,
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'chronic_disease' => (bool) ($validated['chronic_disease'] ?? false),
            'recent_donation' => (bool) ($validated['recent_donation'] ?? false),
            'infection' => (bool) ($validated['infection'] ?? false),
            'is_smoker' => (bool) ($validated['is_smoker'] ?? false),
            'has_recent_surgery' => (bool) ($validated['has_recent_surgery'] ?? false),
            'surgery_date' => $validated['surgery_date'] ?? null,
            'last_donation_date' => $validated['last_donation_date'] ?? null,
            'is_eligible' => $eligibilityData['is_eligible'],
            'next_eligible_date' => $eligibilityData['next_eligible_date'],
        ]);
    }

    /**
     * Check donation eligibility based on health profile
     */
    private function checkEligibility(array $validated): array
    {
        $today = Carbon::now();
        $nextEligibleDate = null;

        // Chronic disease is disqualifying
        if ($validated['chronic_disease'] ?? false) {
            return ['is_eligible' => false, 'next_eligible_date' => null];
        }

        // Check physical requirements
        if ($this->failsPhysicalRequirements($validated)) {
            return ['is_eligible' => false, 'next_eligible_date' => null];
        }

        // Check temporary conditions
        if ($validated['infection'] ?? false) {
            $nextEligibleDate = $today->copy()->addDays(self::INFECTION_WAIT_DAYS);
            return ['is_eligible' => false, 'next_eligible_date' => $nextEligibleDate];
        }

        // Check recent donation interval
        if ($validated['recent_donation'] ?? false && $validated['last_donation_date']) {
            $result = $this->checkRecentDonation($validated['last_donation_date'], $today, $nextEligibleDate);
            if (!$result['is_eligible']) {
                return $result;
            }
            $nextEligibleDate = $result['next_eligible_date'] ?? $nextEligibleDate;
        }

        // Check recent surgery recovery
        if ($validated['has_recent_surgery'] ?? false && $validated['surgery_date']) {
            $result = $this->checkRecentSurgery($validated['surgery_date'], $today, $nextEligibleDate);
            if (!$result['is_eligible']) {
                return $result;
            }
            $nextEligibleDate = $result['next_eligible_date'] ?? $nextEligibleDate;
        }

        return [
            'is_eligible' => true,
            'next_eligible_date' => $nextEligibleDate,
        ];
    }

    /**
     * Check if physical requirements are met
     */
    private function failsPhysicalRequirements(array $validated): bool
    {
        $weight = (int) $validated['weight'];
        $height = (int) $validated['height'];

        return $weight < self::MIN_WEIGHT || $weight > self::MAX_WEIGHT ||
               $height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT;
    }

    /**
     * Check recent donation eligibility (90-day rule)
     */
    private function checkRecentDonation(string $lastDonationDate, Carbon $today, ?Carbon $nextEligibleDate): array
    {
        $lastDonation = Carbon::parse($lastDonationDate);
        $daysSinceDonation = $today->diffInDays($lastDonation);

        if ($daysSinceDonation < self::DONATION_INTERVAL_DAYS) {
            $futureDate = $lastDonation->copy()->addDays(self::DONATION_INTERVAL_DAYS);
            $nextEligibleDate = $this->getLatestDate($nextEligibleDate, $futureDate);
            return ['is_eligible' => false, 'next_eligible_date' => $nextEligibleDate];
        }

        return ['is_eligible' => true, 'next_eligible_date' => $nextEligibleDate];
    }

    /**
     * Check recent surgery eligibility (28-day rule)
     */
    private function checkRecentSurgery(string $surgeryDate, Carbon $today, ?Carbon $nextEligibleDate): array
    {
        $surgery = Carbon::parse($surgeryDate);
        $daysSinceSurgery = $today->diffInDays($surgery);

        if ($daysSinceSurgery < self::SURGERY_RECOVERY_DAYS) {
            $futureDate = $surgery->copy()->addDays(self::SURGERY_RECOVERY_DAYS);
            $nextEligibleDate = $this->getLatestDate($nextEligibleDate, $futureDate);
            return ['is_eligible' => false, 'next_eligible_date' => $nextEligibleDate];
        }

        return ['is_eligible' => true, 'next_eligible_date' => $nextEligibleDate];
    }

    /**
     * Get the latest date between two dates
     */
    private function getLatestDate(?Carbon $date1, Carbon $date2): Carbon
    {
        return !$date1 || $date2 > $date1 ? $date2 : $date1;
    }
}
