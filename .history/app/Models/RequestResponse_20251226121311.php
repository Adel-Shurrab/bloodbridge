<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestResponse extends Model
{
    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_IGNORED = 'ignored';

    public const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_ACCEPTED,
        self::STATUS_DECLINED,
        self::STATUS_COMPLETED,
        self::STATUS_IGNORED,
    ];

    // Defaults
    public const DEFAULT_STATUS = self::STATUS_PENDING;

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'verification_code',
        'verified_at',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
