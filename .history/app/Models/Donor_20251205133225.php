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
        'blood_type', // Can be null initially
        'city',
        'lat',        // Optional for now
        'lng',
    ];
}
