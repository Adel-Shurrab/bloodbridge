<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('scoring.ml_scoring_enabled', false);
        $this->migrator->add('scoring.a_b_testing_enabled', false);
        $this->migrator->add('scoring.max_notifications_per_broadcast', 20);
        $this->migrator->add('scoring.exploration_ratio', 0.20);
        $this->migrator->add('scoring.score_staleness_days', 7);
        $this->migrator->add('scoring.min_history_for_exploitation', 5);
        $this->migrator->add('scoring.a_b_test_control_percentage', 0.50);
        $this->migrator->add('scoring.ml_enabled_since', null);
        $this->migrator->add('scoring.circuit_breaker_failure_threshold', 3);
        $this->migrator->add('scoring.circuit_breaker_recovery_seconds', 120);
    }
};
