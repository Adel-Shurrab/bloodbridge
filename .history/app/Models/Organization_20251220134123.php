<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'user_id',
        'org_name',
        'license_number',
        'license_document_path',
        'responsible_person_name',
        'responsible_person_email',
        'contact_email',
        'contact_phone',
        'street_address',
        'city',
        'state',
        'approval_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
