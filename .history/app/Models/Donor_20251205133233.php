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
        'blood_type',
        'city',
        'lat',
        'lng',
    ];
}
