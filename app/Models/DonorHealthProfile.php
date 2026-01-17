<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Enums\BloodType;

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
        'verified_by_organization_id',
        'verified_at',
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
        'verified_at' => 'datetime',
        'last_donation_date' => 'date',
        'blood_type' => BloodType::class,
        'verified_blood_type' => BloodType::class,
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
        $today = Carbon::now()->startOfDay();

        // Respect current future date (e.g., from manual lab verification)
        $nextEligibleDate = ($this->next_eligible_date && Carbon::parse($this->next_eligible_date)->isFuture())
            ? Carbon::parse($this->next_eligible_date)->startOfDay()
            : null;

        $isEligible = $nextEligibleDate === null;

        // 1. Permanent Disqualifiers
        if ($this->chronic_disease) {
            return [
                'is_eligible' => false,
                'next_eligible_date' => null,
            ];
        }

        // 2. Physical Stats
        if (($this->weight && $this->weight < 50) || ($this->height && $this->height < 140)) {
            $isEligible = false;
        }

        // 3. Infection (Temporary 14 days)
        if ($this->infection) {
            $isEligible = false;
            $nextEligibleDate = $today->copy()->addDays(14);
        }

        // 4. Donation Logic (90 Days)
        if ($this->last_donation_date) {
            $lastDonation = Carbon::parse($this->last_donation_date)->startOfDay();
            $daysSince = $lastDonation->diffInDays($today);

            if ($daysSince < 90) {
                $isEligible = false;
                $futureDate = $lastDonation->copy()->addDays(90);

                if (!$nextEligibleDate || $futureDate > $nextEligibleDate) {
                    $nextEligibleDate = $futureDate;
                }
            }
        }

        // 5. Surgery Logic (28 Days)
        if ($this->surgery_date) {
            $surgery = Carbon::parse($this->surgery_date)->startOfDay();
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

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function verifyingOrganization()
    {
        return $this->belongsTo(Organization::class, 'verified_by_organization_id');
    }
}
