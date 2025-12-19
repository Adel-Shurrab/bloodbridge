<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'user_id',
        'national_id',
        'gender',
        'birth_date',
        
        'city',
        'lat',
        'lng',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthProfile()
    {
        return $this->hasOne(DonorHealthProfile::class);
    }
}
