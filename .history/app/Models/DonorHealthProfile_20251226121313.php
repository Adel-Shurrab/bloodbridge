<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
