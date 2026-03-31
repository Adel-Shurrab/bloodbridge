<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
use App\Enums\RequestResponseStatus;

class BloodRequest extends Model
{
    use SoftDeletes, HasFactory, HasTranslations;

    public const DEFAULT_STATUS = \App\Enums\BloodRequestStatus::PENDING;
    public const DEFAULT_URGENCY_LEVEL = \App\Enums\UrgencyLevel::NORMAL;
    public const DEFAULT_SEARCH_RADIUS_KM = 10;
    public const DEFAULT_DONORS_ACCEPTED = 0;
    public const DEFAULT_DONORS_COMPLETED = 0;

    public array $translatable = ['additional_notes', 'location_address'];

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

    public function acceptedResponses()
    {
        return $this->responses()->whereIn('status', [
            RequestResponseStatus::PENDING,
            RequestResponseStatus::ACCEPTED,
            RequestResponseStatus::COMPLETED,
        ]);
    }

    public function completedResponses()
    {
        return $this->responses()->where('status', RequestResponseStatus::COMPLETED);
    }

    public function getDonorsAcceptedAttribute(): int
    {
        if (array_key_exists('accepted_responses_count', $this->attributes)) {
            return (int) $this->attributes['accepted_responses_count'];
        }

        return $this->acceptedResponses()->count();
    }

    public function getDonorsCompletedAttribute(): int
    {
        if (array_key_exists('completed_responses_count', $this->attributes)) {
            return (int) $this->attributes['completed_responses_count'];
        }

        return $this->completedResponses()->count();
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
     * Get number of donors found (computed from accepted/pending/completed responses)
     */
    public function getDonorsFoundAttribute(): int
    {
        return $this->acceptedResponses()->count();
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

    /**
     * Scope to only include active blood requests (BROADCASTED, not fulfilled).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return void
     */
    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereIn('status', [
            \App\Enums\BloodRequestStatus::BROADCASTED,
        ])
            ->whereNull('fulfilled_at');
    }

    /**
     * Scope to filter requests compatible with a donor's blood type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \App\Enums\BloodType|null  $donorBloodType
     * @return void
     */
    public function scopeCompatibleWithDonor(\Illuminate\Database\Eloquent\Builder $query, ?\App\Enums\BloodType $donorBloodType): void
    {
        if ($donorBloodType === null || $donorBloodType === \App\Enums\BloodType::UNKNOWN) {
            return;
        }

        $compatibleTypes = $donorBloodType->getCompatibleRecipientTypes();

        if (! empty($compatibleTypes)) {
            $query->whereIn('blood_type', $compatibleTypes);
        }
    }

    /**
     * Check if this blood request is currently active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return in_array($this->status, [
            \App\Enums\BloodRequestStatus::BROADCASTED,
        ], true)
            && is_null($this->fulfilled_at)
            && is_null($this->deleted_at);
    }

    /**
     * Scope to add distance_km column relative to given coordinates.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  float|null  $lat
     * @param  float|null  $lng
     * @return void
     */
    public function scopeWithDistance(\Illuminate\Database\Eloquent\Builder $query, ?float $lat, ?float $lng): void
    {
        if ($lat === null || $lng === null) {
            return;
        }

        $query->selectRaw(
            "*, (
                6371 * acos(
                    cos(radians(?)) * cos(radians(lat)) * cos(radians(lng) - radians(?)) +
                    sin(radians(?)) * sin(radians(lat))
                )
            ) AS distance_km",
            [$lat, $lng, $lat]
        );
    }
}
