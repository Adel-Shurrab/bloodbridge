<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // Statuses
    public const STATUS_SCHEDULED = 0;
    public const STATUS_COMPLETED = 1;
    public const STATUS_CANCELLED = 2;

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_SCHEDULED => 'مجدول',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
        ];
    }

    // Defaults
    public const DEFAULT_STATUS = self::STATUS_SCHEDULED;

    protected $fillable = [
        'donor_id',
        'organization_id',
        'blood_request_id',
        'appointment_date',
        'status',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}
