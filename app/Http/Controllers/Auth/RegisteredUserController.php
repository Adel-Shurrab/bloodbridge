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
use Illuminate\Support\Facades\DB;
use App\Models\Governorate;
use Illuminate\Support\Str;

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
        $governorates = Governorate::all();
        $bloodTypes = collect(\App\Enums\BloodType::cases())
            ->mapWithKeys(fn(\App\Enums\BloodType $type) => [$type->value => $type->getLabel()])
            ->toArray();
        return view('auth.register-donor', compact('governorates', 'bloodTypes'));
    }

    /**
     * Show the Organization Registration Form
     */
    public function showOrganizationRegistrationForm(): View
    {
        $governorates = Governorate::all();
        return view('auth.register-organization', compact('governorates'));
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
            'birth_date' => ['required', 'date', 'before_or_equal:-' . app(\App\Settings\GeneralSettings::class)->min_donor_age . ' years', 'after_or_equal:-' . app(\App\Settings\GeneralSettings::class)->max_donor_age . ' years'],
            'gender' => ['required', 'in:' . implode(',', array_column(\App\Enums\Gender::cases(), 'value'))],
            'governorate_id' => ['required', 'exists:governorates,id'],
            // Health Profile Fields
            'weight' => ['required', 'integer', 'min:' . app(\App\Settings\GeneralSettings::class)->min_donor_weight, 'max:300'],
            'height' => ['required', 'integer', 'min:' . app(\App\Settings\GeneralSettings::class)->min_donor_height, 'max:250'],
            'chronic_disease' => ['nullable', 'boolean', 'in:0'],
            'recent_donation' => ['required', 'boolean'],
            'infection' => ['nullable', 'boolean'],
            'has_recent_surgery' => ['required', 'boolean'],
            'surgery_date' => ['required_if:has_recent_surgery,1', 'nullable', 'date', 'before_or_equal:' . now()->subDays(app(\App\Settings\GeneralSettings::class)->min_days_after_surgery)->format('Y-m-d')],
            'last_donation_date' => ['required_if:recent_donation,1', 'nullable', 'date', 'before_or_equal:' . now()->subDays(app(\App\Settings\GeneralSettings::class)->min_days_between_donations)->format('Y-m-d')],
            'blood_type' => ['nullable', 'in:' . implode(',', array_column(\App\Enums\BloodType::cases(), 'value'))],
            // Auto-locate fields with Palestine-specific bounds
            'auto_location_address' => ['nullable', 'string', 'max:500'],
            'auto_lat' => ['nullable', 'numeric', 'between:31.2,32.6'], // Palestine latitude range
            'auto_lng' => ['nullable', 'numeric', 'between:34.2,35.6'], // Palestine longitude range
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
                'role' => \App\Enums\UserRole::DONOR,
                'is_active' => User::DEFAULT_IS_ACTIVE,
            ]);

            // B. Create the Profile linked to that User
            $donor = Donor::create([
                'user_id' => $user->id,
                'governorate_id' => $request->governorate_id,
                'national_id' => $request->national_id,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'auto_location_address' => $request->auto_location_address,
                'lat' => $request->auto_lat,
                'lng' => $request->auto_lng,
                'blood_type' => null,
            ]);

            // C. Create Health Profile (Eligibility is handled by Model Hook)
            DonorHealthProfile::create([
                'donor_id' => $donor->id,
                'weight' => $request->weight,
                'height' => $request->height,
                'blood_type' => $request->blood_type,
                'verified_blood_type' => null,
                'chronic_disease' => $request->boolean('chronic_disease'),
                'infection' => $request->boolean('infection'),
                'recent_donation' => $request->boolean('recent_donation'),
                'has_recent_surgery' => $request->boolean('has_recent_surgery'),
                'surgery_date' => $request->surgery_date,
                'last_donation_date' => $request->last_donation_date,
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
            'opening_time' => ['required_unless:is_24_hours,1', 'nullable', 'date_format:H:i'],
            'closing_time' => ['required_unless:is_24_hours,1', 'nullable', 'date_format:H:i', 'after:opening_time'],
            'working_days' => ['required', 'array', 'min:1'],
            'working_days.*' => ['string'],
            'daily_capacity' => ['required', 'integer', 'min:1'],

            // Contact Info
            'contactEmail' => ['required', 'string', 'email', 'max:255', 'unique:organizations,contact_email'],
            'contactPhone' => ['required', 'string', 'max:20', 'unique:organizations,contact_phone'],

            'governorate_id' => ['required', 'exists:governorates,id'],

            // Admin & License Info
            'licenseNumber' => ['required', 'string', 'max:50', 'unique:organizations,license_number'],
            'licenseUpload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5MB
            'adminName' => ['required', 'string', 'max:255'],
            'responsible_person_position' => ['required', 'string', 'max:255'],
            'adminEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // إيميل تسجيل الدخول
            'adminPassword' => ['required', 'confirmed', Rules\Password::defaults()],
            // Auto-locate fields with Palestine-specific bounds
            'auto_location_address' => ['nullable', 'string', 'max:500'],
            'auto_lat' => ['nullable', 'numeric', 'between:31.2,32.6'], // Palestine latitude range
            'auto_lng' => ['nullable', 'numeric', 'between:34.2,35.6'], // Palestine longitude range
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
                'role' => \App\Enums\UserRole::ORGANIZATION,
                'is_active' => User::DEFAULT_IS_ACTIVE,
            ]);

            // 3. Create the Organization
            $organization = Organization::create([
                'user_id' => $user->id,
                'org_name' => $request->organizationName,
                'slug' => Str::slug($request->organizationName),
                'description' => $request->organizationDescription,
                'governorate_id' => $request->governorate_id,
                'license_number' => $request->licenseNumber,
                'license_document_path' => $licensePath,
                'responsible_person_name' => $request->adminName,
                'responsible_person_position' => $request->responsible_person_position,
                'responsible_person_email' => $request->adminEmail,
                'contact_email' => $request->contactEmail,
                'contact_phone' => $request->contactPhone,

                'auto_location_address' => $request->auto_location_address,
                'lat' => $request->auto_lat,
                'lng' => $request->auto_lng,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'working_days' => $request->working_days,
                'daily_capacity' => $request->daily_capacity,
                'approval_status' => \App\Enums\OrganizationStatus::PENDING,
            ]);



            // 5. Trigger Events & Login
            event(new Registered($user));
            Auth::login($user);

            return $user;
        });

        // Redirect using the centralized dashboard URL
        return redirect()->to($user->getDashboardUrl())
            ->with('success', 'تم تقديم طلبك بنجاح! حسابك قيد المراجعة حالياً.');
    }
}
