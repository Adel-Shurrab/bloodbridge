<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class RequestResponse extends Model
{
    use HasTranslations;

    public const DEFAULT_STATUS = \App\Enums\RequestResponseStatus::PENDING;

    public array $translatable = ['decline_reason'];

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'verification_qr_code',
        'qr_code_expires_at',
        'verified_at',
        'correction_used_at',
        'decline_reason',
        'responded_at',
        'appointment_id',
    ];

    protected $casts = [
        'status' => \App\Enums\RequestResponseStatus::class,
        'verified_at' => 'datetime',
        'correction_used_at' => 'datetime',
        'responded_at' => 'datetime',
        'qr_code_expires_at' => 'datetime',
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function getQrStateLabelAttribute(): string
    {
        if (blank($this->verification_qr_code)) {
            return __('Not available');
        }

        if (filled($this->verified_at)) {
            return __('Used');
        }

        if (filled($this->qr_code_expires_at) && now()->greaterThan($this->qr_code_expires_at)) {
            return __('Expired');
        }

        return __('Active');
    }

    public function getQrStateColorAttribute(): string
    {
        if (blank($this->verification_qr_code)) {
            return 'gray';
        }

        if (filled($this->verified_at)) {
            return 'success';
        }

        if (filled($this->qr_code_expires_at) && now()->greaterThan($this->qr_code_expires_at)) {
            return 'danger';
        }

        return 'warning';
    }
}
