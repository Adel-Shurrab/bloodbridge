<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodRequest extends Model
{
    use SoftDeletes;
    // Statuses
    public const STATUS_PENDING = 0;
    public const STATUS_BROADCASTED = 1;
    public const STATUS_MATCHED = 2;
    public const STATUS_FULFILLED = 3;
    public const STATUS_CANCELLED = 4;
    public const STATUS_EXPIRED = 5;

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_BROADCASTED => 'تم البث',
            self::STATUS_MATCHED => 'تمت المطابقة',
            self::STATUS_FULFILLED => 'تم التنفيذ',
            self::STATUS_CANCELLED => 'ملغي',
            self::STATUS_EXPIRED => 'منتهي',
        ];
    }

    // Urgency Levels
    public const URGENCY_LOW = 1;
    public const URGENCY_MEDIUM = 2;
    public const URGENCY_HIGH = 3;
    public const URGENCY_CRITICAL = 4;

    public static function getUrgencyOptions(): array
    {
        return [
            self::URGENCY_LOW => 'منخفض',
            self::URGENCY_MEDIUM => 'متوسط',
            self::URGENCY_HIGH => 'عالي',
            self::URGENCY_CRITICAL => 'حرج',
        ];
    }

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
        'additional_notes',
        'search_radius_km',
        'status',
        'donors_accepted',
        'donors_completed',
        'broadcasted_at',
        'fulfilled_at',
    ];

    // Blood Types
    public const BLOOD_TYPE_O_POSITIVE = 1;
    public const BLOOD_TYPE_O_NEGATIVE = 2;
    public const BLOOD_TYPE_A_POSITIVE = 3;
    public const BLOOD_TYPE_A_NEGATIVE = 4;
    public const BLOOD_TYPE_B_POSITIVE = 5;
    public const BLOOD_TYPE_B_NEGATIVE = 6;
    public const BLOOD_TYPE_AB_POSITIVE = 7;
    public const BLOOD_TYPE_AB_NEGATIVE = 8;

    public static function getBloodTypeOptions(): array
    {
        return [
            self::BLOOD_TYPE_O_POSITIVE => 'O+',
            self::BLOOD_TYPE_O_NEGATIVE => 'O-',
            self::BLOOD_TYPE_A_POSITIVE => 'A+',
            self::BLOOD_TYPE_A_NEGATIVE => 'A-',
            self::BLOOD_TYPE_B_POSITIVE => 'B+',
            self::BLOOD_TYPE_B_NEGATIVE => 'B-',
            self::BLOOD_TYPE_AB_POSITIVE => 'AB+',
            self::BLOOD_TYPE_AB_NEGATIVE => 'AB-',
        ];
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }
}
