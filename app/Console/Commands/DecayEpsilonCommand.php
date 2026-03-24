<?php

namespace App\Console\Commands;

use App\Settings\ScoringSettings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DecayEpsilonCommand extends Command
{
    protected $signature   = 'scoring:decay-epsilon';
    protected $description = 'Reduce exploration ratio based on elapsed time since ML activation';

    public function handle(ScoringSettings $settings): int
    {
        // ML not enabled yet — nothing to decay
        if (!$settings->ml_scoring_enabled) {
            $this->info('ML scoring not enabled. Nothing to decay.');
            return self::SUCCESS;
        }

        // First run — record activation timestamp
        if ($settings->ml_enabled_since === null) {
            $settings->ml_enabled_since = now()->toIso8601String();
            $settings->save();
            $this->info('First run — ml_enabled_since recorded: ' . $settings->ml_enabled_since);
            return self::SUCCESS;
        }

        // Calculate elapsed days since ML was first enabled
        $elapsedDays = Carbon::parse($settings->ml_enabled_since)->diffInDays(now());

        // Time-based decay schedule:
        //  0–13 days  → 20% (aggressive exploration — model is new)
        //  14–29 days → 15%
        //  30–59 days → 10%
        //  60+ days   →  5% (mature model — mostly exploit)
        $newEpsilon = match (true) {
            $elapsedDays >= 60 => 0.05,
            $elapsedDays >= 30 => 0.10,
            $elapsedDays >= 14 => 0.15,
            default            => 0.20,
        };

        $old = $settings->exploration_ratio;

        if (abs($newEpsilon - $old) > 0.001) {
            $settings->exploration_ratio = $newEpsilon;
            $settings->save();

            Log::info("Epsilon decayed: {$old} → {$newEpsilon} (day {$elapsedDays})");
            $this->info("Updated: {$old} → {$newEpsilon} (day {$elapsedDays})");
        } else {
            $this->info("Unchanged: {$newEpsilon} (day {$elapsedDays})");
        }

        return self::SUCCESS;
    }
}
