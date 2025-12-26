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
use App\Models\Organization;
use App\Models\BloodRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration selection page
     */
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
     * Show the Organization Registration Form
     */
    public function showOrganizationRegistrationForm(): View
    {
        return view('auth.register-organization');
    }

    /**
     * Handle the Donor Registration Logic
     */
    public function storeDonor(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::default()],
            'phone' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'national_id' => ['required', 'string', 'digits:9', 'unique:' . Donor::class],
            'birth_date' => ['required', 'date', 'before_or_equal:-18 years'],
            'gender' => ['required', 'in:' . implode(',', Donor::GENDER_LIST)],
            'city' => ['required', 'string', 'max:255'],
            // Health Profile Fields
            'weight' => ['required', 'integer', 'min:50', 'max:200'],
            'height' => ['required', 'integer', 'min:140', 'max:220'],
            'chronic_disease' => ['nullable', 'boolean', 'in:0'],
            'recent_donation' => ['nullable', 'boolean'],
            'infection' => ['nullable', 'boolean'],
            'has_recent_surgery' => ['nullable', 'boolean'],
            'surgery_date' => ['nullable', 'date', 'before_or_equal:today'],
            'last_donation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'blood_type' => ['nullable', 'string', 'in:' . implode(',', Donor::BLOOD_TYPES_LIST)],
        ], [
            'chronic_disease.in' => 'عذراً، المتبرعون الذين يعانون من أمراض مزمنة غير مؤهلين للتبرع.',
        ]);

        $user = DB::transaction(function () use ($request) {
            // A. Create the Login User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_DONOR,
                'is_active' => User::DEFAULT_IS_ACTIVE,
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
                'blood_type' => $request->blood_type,
                'verified_blood_type' => null,
                'chronic_disease' => $request->boolean('chronic_disease'),
                'infection' => $request->boolean('infection'),
                'recent_donation' => $request->last_donation_date ? true : false,
                'has_recent_surgery' => $request->surgery_date ? true : false,
                'surgery_date' => $request->surgery_date,
                'last_donation_date' => $request->last_donation_date,
                'is_eligible' => $eligibilityData['is_eligible'],
                'next_eligible_date' => $eligibilityData['next_eligible_date'],
            ]);

            // D. Trigger Events & Login
            event(new Registered($user));
            Auth::login($user);

            return $user;
        });

        // 3. Redirect
        return redirect()->to($user->getDashboardUrl());
    }

    public function storeOrganization(Request $request): RedirectResponse
    {
        $request->validate([
            // Organization Info
            'organizationName' => ['required', 'string', 'max:255'],
            'organizationDescription' => ['nullable', 'string', 'max:1000'],

            // Contact Info
            'contactEmail' => ['required', 'string', 'email', 'max:255'], // إيميل التواصل العام
            'contactPhone' => ['required', 'string', 'max:20'],
            'streetAddress' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'postalCode' => ['nullable', 'string', 'max:20'],

            // Admin & License Info
            'licenseNumber' => ['required', 'string', 'max:50', 'unique:organizations,license_number'],
            'licenseUpload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'adminName' => ['required', 'string', 'max:255'],
            'adminEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // إيميل تسجيل الدخول
            'adminPassword' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = DB::transaction(function () use ($request) {
            // 1. Handle File Upload
            $licensePath = null;
            if ($request->hasFile('licenseUpload')) {
                $licensePath = $request->file('licenseUpload')->store('licenses', 'public');
            }

            // 2. Create the User (Admin of the Org)
            $user = User::create([
                'name' => $request->adminName,
                'email' => $request->adminEmail,
                'phone' => $request->contactPhone,
                'password' => Hash::make($request->adminPassword),
                'role' => User::ROLE_ORGANIZATION,
                'is_active' => User::DEFAULT_IS_ACTIVE,
            ]);

            // 3. Create the Organization
            $organization = \App\Models\Organization::create([
                'user_id' => $user->id,
                'org_name' => $request->organizationName,
                'license_number' => $request->licenseNumber,
                'license_document_path' => $licensePath,
                'responsible_person_name' => $request->adminName,
                'responsible_person_email' => $request->adminEmail,
                'contact_email' => $request->contactEmail,
                'contact_phone' => $request->contactPhone,
                'street_address' => $request->streetAddress,
                'city' => $request->city,
                'state' => $request->state,
                'approval_status' => Organization::STATUS_PENDING,
            ]);

            // 4. Link User to Organization
            $user->update(['organization_id' => $organization->id]);

            // 5. Trigger Events & Login
            event(new Registered($user));
            Auth::login($user);

            return $user;
        });

        // Redirect using the centralized dashboard URL
        return redirect()->to($user->getDashboardUrl())
            ->with('success', 'تم تقديم طلبك بنجاح! حسابك قيد المراجعة حالياً.');
    }

    /**
     * Check eligibility based on health profile
     */
    private function checkEligibility($request): array
    {
        $today = Carbon::now()->startOfDay();
        $isEligible = true;
        $nextEligibleDate = null;

        // 1. Permanent Disqualifiers
        if ($request->boolean('chronic_disease')) {
            return ['is_eligible' => false, 'next_eligible_date' => null];
        }

        // 2. Physical Stats
        if ($request->weight < 50 || $request->height < 140) {
            $isEligible = false;
        }

        // 3. Infection (Temporary 14 days)
        if ($request->boolean('infection')) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays(14);
        }

        // 4. Donation Logic (90 Days)
        if ($request->last_donation_date) {
            $lastDonation = Carbon::parse($request->last_donation_date)->startOfDay();
            $daysSince = $lastDonation->diffInDays($today); // Absolute difference

            // Only punish if WITHIN the window
            if ($daysSince < 90) {
                $isEligible = false;
                $futureDate = $lastDonation->copy()->addDays(90);

                // Keep the furthest date
                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // 5. Surgery Logic (28 Days)
        if ($request->surgery_date) {
            $surgery = Carbon::parse($request->surgery_date)->startOfDay();
            $daysSince = $surgery->diffInDays($today);

            if ($daysSince < 28) {
                $isEligible = false;
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
}
