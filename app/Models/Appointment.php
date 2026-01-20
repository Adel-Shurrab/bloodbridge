<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{


    // Defaults
    public const DEFAULT_STATUS = \App\Enums\AppointmentStatus::SCHEDULED;

    protected $casts = [
        'status' => \App\Enums\AppointmentStatus::class,
    ];

    protected $fillable = [
        'donor_id',
        'organization_id',
        'blood_request_id',
        'appointment_date',
        'status',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }
}
