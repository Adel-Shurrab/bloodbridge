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

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_ACCEPTED => 'مقبول',
            self::STATUS_DECLINED => 'مرفوض',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_IGNORED => 'متجاهل',
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

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
