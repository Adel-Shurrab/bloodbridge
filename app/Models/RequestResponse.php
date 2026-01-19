<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestResponse extends Model
{
    // Statuses
    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_DECLINED = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_IGNORED = 4;
    public const STATUS_NO_SHOW = 5;

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_ACCEPTED => 'قادم للتبرع',
            self::STATUS_DECLINED => 'استبعاد طبي',
            self::STATUS_COMPLETED => 'تم التبرع بنجاح',
            self::STATUS_IGNORED => 'متجاهل',
            self::STATUS_NO_SHOW => 'لم يحضر',
        ];
    }

    // Defaults
    public const DEFAULT_STATUS = self::STATUS_PENDING;

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'verification_qr_code',
        'verified_at',
        'decline_reason',
        'responded_at',
        'appointment_id',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'responded_at' => 'datetime',
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
