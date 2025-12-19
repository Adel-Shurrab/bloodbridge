<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorHealthProfile extends Model
{
    use SoftDeletes;

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
        'verified_blood_type'
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
