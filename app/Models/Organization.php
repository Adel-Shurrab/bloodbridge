<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Organization extends Model implements HasName
{
    use SoftDeletes, HasFactory;

    public const DEFAULT_APPROVAL_STATUS = \App\Enums\OrganizationStatus::PENDING;
    public const DEFAULT_DAILY_CAPACITY = 0;
    public const DEFAULT_TOTAL_REQUEST_CREATED = 0;
    public const DEFAULT_TOTAL_DONATION_VERIFIED = 0;

    protected $casts = [
        'working_days' => 'array',
        'approval_status' => \App\Enums\OrganizationStatus::class,
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
        return $this->org_name;
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
}
