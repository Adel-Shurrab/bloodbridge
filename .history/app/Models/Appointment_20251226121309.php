<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // Statuses
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LIST = [
        self::STATUS_SCHEDULED,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

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
