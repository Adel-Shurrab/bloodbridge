<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ScoringSettings extends Settings
{
    public static function group(): string
    {
        return 'scoring';
    }

    public bool $ml_scoring_enabled = false;

    public bool $a_b_testing_enabled = false;

    public int $max_notifications_per_broadcast = 20;

    public float $exploration_ratio = 0.20;

    public int $score_staleness_days = 7;

    public int $min_history_for_exploitation = 5;

    public float $a_b_test_control_percentage = 0.50;

    public ?string $ml_enabled_since = null;

    public int $circuit_breaker_failure_threshold = 3;
    public int $circuit_breaker_recovery_seconds = 120;
}
