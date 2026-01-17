<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityLog extends Model
{
    // Check Types
    public const TYPE_REGISTRATION = 1;
    public const TYPE_REQUEST_ACCEPTANCE = 2;
    public const TYPE_PROFILE_UPDATE = 3;
    public const TYPE_LAB_VERIFICATION = 4;

    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_REGISTRATION => 'تسجيل جديد',
            self::TYPE_REQUEST_ACCEPTANCE => 'قبول طلب',
            self::TYPE_PROFILE_UPDATE => 'تحديث الملف الشخصي',
            self::TYPE_LAB_VERIFICATION => 'فحص مخبري',
        ];
    }

    protected $fillable = [
        'donor_id',
        'organization_id',
        'check_type',
        'is_eligible',
        'is_permanent',
        'rejection_reason',
        'answers_snapshot',
    ];

    protected $casts = [
        'is_eligible' => 'boolean',
        'is_permanent' => 'boolean',
        'answers_snapshot' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }
}
