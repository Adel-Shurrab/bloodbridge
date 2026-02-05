<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestResponse extends Model
{


    // Defaults
    public const DEFAULT_STATUS = \App\Enums\RequestResponseStatus::PENDING;

    protected $fillable = [
        'blood_request_id',
        'donor_id',
        'status',
        'verification_qr_code',
        'qr_code_expires_at',
        'verified_at',
        'decline_reason',
        'responded_at',
        'appointment_id',
    ];

    protected $casts = [
        'status' => \App\Enums\RequestResponseStatus::class,
        'verified_at' => 'datetime',
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
}
