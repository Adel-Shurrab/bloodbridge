<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_BROADCASTED = 'broadcasted';
    public const STATUS_MATCHED = 'matched';
    public const STATUS_FULFILLED = 'fulfilled';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED = 'expired';

    public const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_BROADCASTED,
        self::STATUS_MATCHED,
        self::STATUS_FULFILLED,
        self::STATUS_CANCELLED,
        self::STATUS_EXPIRED,
    ];

    // Urgency Levels
    public const URGENCY_LOW = 'low';
    public const URGENCY_MEDIUM = 'medium';
    public const URGENCY_HIGH = 'high';
    public const URGENCY_CRITICAL = 'critical';

    public const URGENCY_LIST = [
        self::URGENCY_LOW,
        self::URGENCY_MEDIUM,
        self::URGENCY_HIGH,
        self::URGENCY_CRITICAL,
    ];

    // Defaults
    public const DEFAULT_STATUS = self::STATUS_PENDING;
    public const DEFAULT_URGENCY_LEVEL = self::URGENCY_LOW;
    public const DEFAULT_SEARCH_RADIUS_KM = 10;
    public const DEFAULT_DONORS_ACCEPTED = 0;
    public const DEFAULT_DONORS_COMPLETED = 0;

    protected $fillable = [
        'organization_id',
        'blood_type',
        'units_needed',
        'urgency_level',
        'status',
        'deadline',
        'location_name',
        'lat',
        'lng',
        'search_radius',
        'notes',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
