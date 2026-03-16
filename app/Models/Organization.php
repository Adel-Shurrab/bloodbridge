<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\OrganizationStatus;
use Spatie\Translatable\HasTranslations;

class Organization extends Model implements HasName
{
    use SoftDeletes, HasFactory, HasTranslations;

    public const DEFAULT_APPROVAL_STATUS = OrganizationStatus::PENDING;
    public const DEFAULT_DAILY_CAPACITY = 0;
    public const DEFAULT_TOTAL_REQUEST_CREATED = 0;
    public const DEFAULT_TOTAL_DONATION_VERIFIED = 0;

    public array $translatable = ['org_name', 'description', 'responsible_person_position', 'rejection_reason'];

    protected $casts = [
        'working_days' => 'array',
        'approval_status' => OrganizationStatus::class,
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
    ];

    protected $fillable = [
        'user_id',
        'org_name',
        'slug',
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
        'auto_location_address',
        'lat',
        'lng',
        'opening_time',
        'closing_time',
        'working_days',
        'daily_capacity',
        'approval_status',
    ];

    public function getFilamentName(): string
    {
        return $this->getTranslation('org_name', app()->getLocale(), false) ?: ($this->getTranslation('org_name', 'ar', false) ?: '');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function bloodRequests()
    {
        return $this->hasMany(BloodRequest::class);
    }

    public static function getWorkingDaysOptions(): array
    {
        return [
            6 => __('Saturday'),
            0 => __('Sunday'),
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
        ];
    }
}
