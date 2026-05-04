<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Achievement extends Model
{
    use HasTranslations;

    public const CRITERIA_DONATIONS       = 'donations';
    public const CRITERIA_POINTS          = 'points';
    public const CRITERIA_CRITICAL        = 'critical_donations';
    public const CRITERIA_RARE_BLOOD      = 'rare_blood_type';
    public const CRITERIA_ACTIVE_MONTHS   = 'active_months';
    public const CRITERIA_ACTIVE_YEARS    = 'active_years';
    public const CRITERIA_NO_CANCEL       = 'streak_no_cancel';
    public const CRITERIA_COMPLETION_RATE = 'completion_rate_100';
    public const CRITERIA_RESPONSE_TIME   = 'response_time_fast';
    public const CRITERIA_GOVERNORATES    = 'governorate_count';
    public const CRITERIA_SPECIAL_DATE    = 'special_date';

    public const CRITERIA_LIST = [
        self::CRITERIA_DONATIONS,
        self::CRITERIA_POINTS,
        self::CRITERIA_CRITICAL,
        self::CRITERIA_RARE_BLOOD,
        self::CRITERIA_ACTIVE_MONTHS,
        self::CRITERIA_ACTIVE_YEARS,
        self::CRITERIA_NO_CANCEL,
        self::CRITERIA_COMPLETION_RATE,
        self::CRITERIA_RESPONSE_TIME,
        self::CRITERIA_GOVERNORATES,
        self::CRITERIA_SPECIAL_DATE,
    ];

    public const DEFAULT_POINTS_REWARDS = 0;
    public const DEFAULT_CRITERIA_VALUE = 0;
    public const DEFAULT_DISPLAY_ORDER  = 0;

    public array $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'description',
        'criteria_type',
        'criteria_value',
        'badge_icon',
        'points_rewards',
        'badge_type',
        'display_order',
    ];

    public function donorAchievements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DonorAchievement::class);
    }
}
