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
    public const BLOOD_TYPE_O_POSITIVE = 1;
    public const BLOOD_TYPE_O_NEGATIVE = 2;
    public const BLOOD_TYPE_A_POSITIVE = 3;
    public const BLOOD_TYPE_A_NEGATIVE = 4;
    public const BLOOD_TYPE_B_POSITIVE = 5;
    public const BLOOD_TYPE_B_NEGATIVE = 6;
    public const BLOOD_TYPE_AB_POSITIVE = 7;
    public const BLOOD_TYPE_AB_NEGATIVE = 8;

    public const BLOOD_TYPES_LIST = [
        'O+',
        'O-',
        'A+',
        'A-',
        'B+',
        'B-',
        'AB+',
        'AB-',
    ];

    public static function getBloodTypeIdByLabel(string $label): ?int
    {
        $map = array_flip(self::getBloodTypeOptions());
        return $map[$label] ?? null;
    }

    public static function getBloodTypeOptions(): array
    {
        return [
            self::BLOOD_TYPE_O_POSITIVE => 'O+',
            self::BLOOD_TYPE_O_NEGATIVE => 'O-',
            self::BLOOD_TYPE_A_POSITIVE => 'A+',
            self::BLOOD_TYPE_A_NEGATIVE => 'A-',
            self::BLOOD_TYPE_B_POSITIVE => 'B+',
            self::BLOOD_TYPE_B_NEGATIVE => 'B-',
            self::BLOOD_TYPE_AB_POSITIVE => 'AB+',
            self::BLOOD_TYPE_AB_NEGATIVE => 'AB-',
        ];
    }

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
