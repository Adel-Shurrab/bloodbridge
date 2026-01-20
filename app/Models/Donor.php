<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BloodType;

class Donor extends Model
{
    use SoftDeletes, HasFactory;



    protected $casts = [
        'blood_type' => BloodType::class,
        'gender' => \App\Enums\Gender::class,
    ];

    // Defaults
    public const DEFAULT_POINTS = 0;
    public const DEFAULT_LEVEL = 1;
    public const NATIONAL_ID_LENGTH = 9;

    protected $fillable = [
        'user_id',
        'governorate_id',
        'national_id',
        'gender',
        'birth_date',
        'lat',
        'lng',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function healthProfile()
    {
        return $this->hasOne(DonorHealthProfile::class);
    }

    public function responses()
    {
        return $this->hasMany(RequestResponse::class);
    }

    public function eligibilityLogs()
    {
        return $this->hasMany(EligibilityLog::class);
    }
}
