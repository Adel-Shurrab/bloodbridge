<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donor extends Model
{
    use SoftDeletes;

    // Gender
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    public const GENDER_LIST = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    // Blood Types
    public const BLOOD_TYPE_O_POSITIVE = 'O+';
    public const BLOOD_TYPE_O_NEGATIVE = 'O-';
    public const BLOOD_TYPE_A_POSITIVE = 'A+';
    public const BLOOD_TYPE_A_NEGATIVE = 'A-';
    public const BLOOD_TYPE_B_POSITIVE = 'B+';
    public const BLOOD_TYPE_B_NEGATIVE = 'B-';
    public const BLOOD_TYPE_AB_POSITIVE = 'AB+';
    public const BLOOD_TYPE_AB_NEGATIVE = 'AB-';

    public const BLOOD_TYPES_LIST = [
        'O+',
        'O-',
        'A+',
        'A-',
        'B+',
        'B-',
        'AB+',
        'AB-'
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
}
