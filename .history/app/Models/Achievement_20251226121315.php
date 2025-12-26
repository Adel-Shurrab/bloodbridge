<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    // Criteria Types
    public const CRITERIA_DONATIONS = 'donations';
    public const CRITERIA_POINTS = 'points';

    public const CRITERIA_LIST = [
        self::CRITERIA_DONATIONS,
        self::CRITERIA_POINTS,
    ];

    // Defaults
    public const DEFAULT_POINTS_REWARDS = 0;
    public const DEFAULT_CRITERIA_VALUE = 0;
    public const DEFAULT_DISPLAY_ORDER = 0;

    protected $fillable = [
        'name',
        'description',
        'criteria_type',
        'criteria_value',
        'badge_image',
        'points_reward',
        'display_order',
    ];
}
