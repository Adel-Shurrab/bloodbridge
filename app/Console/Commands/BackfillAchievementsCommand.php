<?php

namespace App\Console\Commands;

use App\Models\Donor;
use App\Services\AchievementService;
use Illuminate\Console\Command;

class BackfillAchievementsCommand extends Command
{
    protected $signature   = 'achievements:backfill {--dry-run : Preview without writing to DB}';
    protected $description = 'Award achievements to existing donors based on their current donation data';

    public function handle(AchievementService $service): int
    {
        $this->info('Starting achievement backfill...');

        // lazy(100) streams donors in memory-safe 100-row chunks
        $donors = Donor::with(['healthProfile', 'donorAchievements.achievement'])
            ->whereHas('healthProfile', fn($q) => $q->where('total_donations', '>', 0))
            ->lazy(100);

        $totalAwarded = 0;

        foreach ($donors as $donor) {
            if ($this->option('dry-run')) {
                $earnedIds = $donor->donorAchievements->pluck('achievement_id');
                $would     = \App\Models\Achievement::whereNotIn('id', $earnedIds)->get()
                    ->filter(fn($a) => $a->criteria_type === 'donations'
                        && ($donor->healthProfile->total_donations ?? 0) >= $a->criteria_value);
                $totalAwarded += $would->count();
                if ($would->count()) {
                    $this->line("  [DRY] Donor #{$donor->id}: would earn {$would->count()} badge(s)");
                }
                continue;
            }

            $awarded       = $service->evaluateAndAward($donor, backfillMode: true);
            $totalAwarded += count($awarded);

            if (count($awarded)) {
                $this->line("  Donor #{$donor->id}: earned " . count($awarded) . " badge(s)");
            }
        }

        $this->info("Backfill complete. Total badges awarded: {$totalAwarded}");
        return self::SUCCESS;
    }
}
