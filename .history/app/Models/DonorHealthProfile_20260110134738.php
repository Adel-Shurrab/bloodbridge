<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class DonorHealthProfile extends Model
{
    use SoftDeletes;

    // Eligibility Statuses
    public const STATUS_ELIGIBLE = 'eligible';
    public const STATUS_INELIGIBLE = 'ineligible';

    // Check Types
    public const CHECK_TYPE_REGISTRATION = 'registration';
    public const CHECK_TYPE_REQUEST_ACCEPTANCE = 'request_acceptance';
    public const CHECK_TYPE_PROFILE_UPDATE = 'profile_update';

    // Defaults
    public const DEFAULT_CHRONIC_DISEASE = false;
    public const DEFAULT_RECENT_DONATION = false;
    public const DEFAULT_INFECTION = false;
    public const DEFAULT_IS_ELIGIBLE = true;
    public const DEFAULT_HAS_RECENT_SURGERY = false;
    public const DEFAULT_TOTAL_DONATIONS = 0;

    protected $fillable = [
        'donor_id',
        'weight',
        'height',
        'chronic_disease',
        'recent_donation',
        'infection',
        'is_eligible',
        'is_smoker',
        'has_recent_surgery',
        'surgery_date',
        'next_eligible_date',
        'last_donation_date',
        'blood_type',
        'verified_blood_type',
        'total_donations'
    ];

    protected $casts = [
        'chronic_disease' => 'boolean',
        'recent_donation' => 'boolean',
        'infection' => 'boolean',
        'is_eligible' => 'boolean',
        'is_smoker' => 'boolean',
        'has_recent_surgery' => 'boolean',
        'surgery_date' => 'date',
        'next_eligible_date' => 'date',
        'last_donation_date' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function (DonorHealthProfile $profile) {
            if (!$profile->recent_donation) {
                $profile->last_donation_date = null;
            }
            if (!$profile->has_recent_surgery) {
                $profile->surgery_date = null;
            }

            $eligibility = $profile->calculateEligibility();
            $profile->is_eligible = $eligibility['is_eligible'];
            $profile->next_eligible_date = $eligibility['next_eligible_date'];
        });
    }

    /**
     * Calculate eligibility based on current health profile attributes.
     *
     * @return array{is_eligible: bool, next_eligible_date: ?Carbon}
     */
    public function calculateEligibility(): array
    {
        $settings = app(\App\Settings\DonationSettings::class);

        $today = Carbon::now()->startOfDay();
        $isEligible = true;
        $nextEligibleDate = null;

        // 1. Permanent Disqualifiers
        if ($this->chronic_disease) {
            return [
                'is_eligible' => false,
                'next_eligible_date' => null,
            ];
        }

        // 2. Physical Stats
        if (($this->weight && $this->weight < $settings->min_weight) || ($this->height && $this->height < $settings->min_height)) {
            $isEligible = false;
        }

        // 3. Infection (Temporary)
        if ($this->infection) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays($settings->infection_deferral_days);
        }

        // 4. Donation Logic
        if ($this->last_donation_date) {
            $lastDonation = Carbon::parse($this->last_donation_date)->startOfDay();
            $daysSince = $lastDonation->diffInDays($today);

            if ($daysSince < $settings->donation_deferral_days) {
                $isEligible = false;
                $futureDate = $lastDonation->copy()->addDays($settings->donation_deferral_days);

                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // 5. Surgery Logic
        if ($this->surgery_date) {
            $surgery = Carbon::parse($this->surgery_date)->startOfDay();
            $daysSince = $surgery->diffInDays($today);

            if ($daysSince < $settings->surgery_deferral_days) {
                $isEligible = false;
                $futureDate = $surgery->copy()->addDays($settings->surgery_deferral_days);

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

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
