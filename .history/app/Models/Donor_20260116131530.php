<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\BloodType;

class Donor extends Model
{
    use SoftDeletes, HasFactory;

    // Gender
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    public const GENDER_LIST = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    public static function getGenderOptions(): array
    {
        return [
            self::GENDER_MALE => 'ذكر',
            self::GENDER_FEMALE => 'أنثى',
        ];
    }

    // Blood Types
    public static function getBloodTypeOptions(): array
    {
        return collect(\App\Enums\BloodType::cases())
            ->mapWithKeys(fn(\App\Enums\BloodType $type) => [$type->value => $type->getLabel()])
            ->toArray();
    }

    protected $casts = [
        'blood_type' => \App\Enums\BloodType::class,
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
}
