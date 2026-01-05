<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    // Approval Statuses
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;

    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_APPROVED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    // Defaults
    public const DEFAULT_APPROVAL_STATUS = self::STATUS_PENDING;
    public const DEFAULT_DAILY_CAPACITY = 0;
    public const DEFAULT_TOTAL_REQUEST_CREATED = 0;
    public const DEFAULT_TOTAL_DONATION_VERIFIED = 0;

    protected $casts = [
        'working_days' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'org_name',
        'governorate_id',
        'description',
        'license_number',
        'license_document_path',
        'responsible_person_position',
        'contact_email',
        'contact_phone',
        'street_address',
        'opening_time',
        'closing_time',
        'working_days',
        'daily_capacity',
        'approval_status',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
