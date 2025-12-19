<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes
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
