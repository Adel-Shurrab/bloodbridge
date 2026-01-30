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
    public const DEFAULT_URGENCY_LEVEL = \App\Enums\UrgencyLevel::NORMAL;
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
        'actual_search_radius_km',
    ];

    protected $casts = [
        'blood_type' => \App\Enums\BloodType::class,
        'status' => \App\Enums\BloodRequestStatus::class,
        'urgency_level' => \App\Enums\UrgencyLevel::class,
        'lat' => 'float',
        'lng' => 'float',
        'actual_search_radius_km' => 'integer',
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

    /**
     * Check if radius was expanded during broadcast
     */
    public function wasExpanded(): bool
    {
        return $this->actual_search_radius_km && $this->actual_search_radius_km > $this->search_radius_km;
    }

    /**
     * Get number of expansion steps (computed)
     */
    public function getExpansionStepsAttribute(): int
    {
        if (!$this->actual_search_radius_km || $this->actual_search_radius_km <= $this->search_radius_km) {
            return 0;
        }

        return (int) (($this->actual_search_radius_km - $this->search_radius_km) / 5);
    }

    /**
     * Get number of donors found (computed from responses)
     */
    public function getDonorsFoundAttribute(): int
    {
        return $this->responses()->count();
    }

    /**
     * Get human-readable expansion summary
     */
    public function getExpansionSummary(): string
    {
        if (!$this->wasExpanded()) {
            return "Searched at {$this->search_radius_km}km";
        }

        $steps = $this->expansion_steps;
        return "Expanded from {$this->search_radius_km}km to {$this->actual_search_radius_km}km ({$steps} expansion" . ($steps > 1 ? 's' : '') . ")";
    }
}
