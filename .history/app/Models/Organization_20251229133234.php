<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    // Approval Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LIST = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

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
        'responsible_person_name',
        'responsible_person_position',
        'responsible_person_email',
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
