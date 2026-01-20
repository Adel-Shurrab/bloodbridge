<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use SoftDeletes, HasFactory;


    // Defaults
    public const DEFAULT_STATUS = \App\Enums\BloodRequestStatus::PENDING;
    public const DEFAULT_URGENCY_LEVEL = \App\Enums\UrgencyLevel::LOW;
    public const DEFAULT_SEARCH_RADIUS_KM = 10;
    public const DEFAULT_DONORS_ACCEPTED = 0;
    public const DEFAULT_DONORS_COMPLETED = 0;

    protected $fillable = [
        'organization_id',
        'blood_type',
        'units_needed',
        'urgency_level',
        'additional_notes',
        'search_radius_km',
        'lat',
        'lng',
        'location_address',
        'status',
        'donors_accepted',
        'donors_completed',
        'broadcasted_at',
        'fulfilled_at',
    ];

    protected $casts = [
        'blood_type' => \App\Enums\BloodType::class,
        'status' => \App\Enums\BloodRequestStatus::class,
        'urgency_level' => \App\Enums\UrgencyLevel::class,
        'lat' => 'float',
        'lng' => 'float',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }

    /**
     * Broadcast this blood request to eligible donors within the search radius
     * 
     * @return int Number of donors found
     * @throws \Exception
     */
    public function broadcastToEligibleDonors(): int
    {
        return app(\App\Services\BloodRequestBroadcastService::class)->broadcast($this);
    }
}
