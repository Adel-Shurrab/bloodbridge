<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\DonorPredictiveScore;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeds donor_predictive_scores with realistic XGBoost ML scores.
 *
 * These represent Level 1 (DB cache) in the scoring waterfall:
 *   Level 1 → donor_predictive_scores (this table) ← seeded here
 *   Level 2 → FastAPI / XGBoost (live service, not seeded)
 *   Level 3 → Rule-based PHP formula (always available)
 *
 * ══════════════════════════════════════════════════════════════════
 * DEMO SCENARIO: O+ Critical Request — الشفاء
 * ══════════════════════════════════════════════════════════════════
 *
 * Eligible O+ / O- donors, ordered by score:
 *
 *  ML Scoring (this seeder):
 *    1. يوسف عادل زعرب     O-  7 دونات   ML: 0.9071  ← Universal donor bonus
 *    2. حسن منير أبو حسن   O+  6 دونات   ML: 0.8649
 *    3. محمد خالد أبو عمر  O+  5 دونات   ML: 0.8107
 *    4. جمال رشيد أبو دقة  O+  4 دونات   ML: 0.7438
 *    5. وائل حمدي سعد      O-  1 دونة    ML: 0.4723  → Exploration bucket
 *
 *  Rule-based Scoring (formula: acceptance×0.50 + recency×0.30 + loyalty×0.20):
 *    1. يوسف   ≈ 0.734  (loyalty=0.70, high acceptance, recent)
 *    2. حسن    ≈ 0.714  (loyalty=0.60, veteran acceptance rate)
 *    3. محمد   ≈ 0.682  (loyalty=0.50, regular acceptance)
 *    4. جمال   ≈ 0.637  (loyalty=0.40)
 *    5. وائل   ≈ 0.468  (loyalty=0.10, limited history)
 *
 *  Key difference: ML boosts يوسف more strongly (universal donor intelligence)
 *  and differentiates veterans from regulars more clearly.
 *
 * ══════════════════════════════════════════════════════════════════
 * NOT SEEDED (expected behavior in the waterfall):
 * ══════════════════════════════════════════════════════════════════
 *  Cold-start (0 donations, exploration bucket):
 *    - سامي رامي سلامة     A-  0 دونات  → Exploration (no ML score)
 *    - عدنان نبيل الزواوي   B+  0 دونات  → Exploration (no ML score)
 *
 *  Permanently ineligible (chronic disease / underweight):
 *    - فراس ناصر العمري    B-  chronic_disease=true  → Blocked from system
 *    - رامي عزيز الكردي    B-  weight=47 kg          → Blocked from system
 *
 *  Temporarily ineligible (self-heals after next_eligible_date):
 *    - إياد رفيق الغصين    A-  active infection      → Blocked now
 *    - نبيل كريم جودة       O-  active infection      → Blocked now
 *    - عمر سعيد الشريف     B+  donated 60 days ago   → Eligible in ~30 days
 *    - بلال واصف الجمل     O+  donated 30 days ago   → Eligible in ~60 days
 *    - طارق جمال الدبعي    O+  surgery 10 days ago   → Eligible in ~18 days
 */
class DonorScoreSeeder extends Seeder
{
    /**
     * national_id → [acceptance_probability, data_points_count]
     *
     * Scores reflect what the XGBoost model predicts based on:
     *  - Donation history & acceptance rate
     *  - Recency of participation
     *  - Blood type (universal donors get a slight boost)
     *  - Health risk factors (smokers get a penalty)
     *  - Distance consistency (no-GPS donors get a slight penalty)
     */
    private array $mlScores = [

        // ─────────────────────────────────────────────────────────────────
        // TIER 1: VETERANS (≥6 donations) — High ML scores, rich data
        // ─────────────────────────────────────────────────────────────────

        // سلمان أمين الرنتيسي — A+, 10 donations, خانيونس
        // Highest loyalty + near-perfect acceptance history
        '430123456' => [0.9512, 24],

        // ماجد صلاح عبدالله — AB+, 8 donations, دير البلح
        // Very consistent veteran with excellent record
        '420345678' => [0.9284, 21],

        // يوسف عادل زعرب — O-, 7 donations, غزة
        // Universal donor: ML boosts score for blood-type flexibility
        '400567890' => [0.9071, 18],

        // صلاح الدين عمر البح — AB+, 6 donations, رفح
        // Veteran with strong completion history
        '440567890' => [0.8823, 16],

        // حسن منير أبو حسن — O+, 6 donations, دير البلح
        // Reliable veteran, consistent response pattern
        '420123456' => [0.8649, 16],

        // ─────────────────────────────────────────────────────────────────
        // TIER 2: REGULARS (3-5 donations) — Moderate ML scores
        // ─────────────────────────────────────────────────────────────────

        // محمد خالد أبو عمر — O+, 5 donations, غزة
        '400123456' => [0.8107, 14],

        // بلال واصف الجمل — O+, 4 donations (donated 30 days ago → ineligible until ~day 90)
        // Good score; eligibility filter excludes him during cooldown
        '430345678' => [0.6890, 12],

        // جمال رشيد أبو دقة — O+, 4 donations, رفح
        '440123456' => [0.7438, 12],

        // خالد إبراهيم النجار — AB+, 4 donations, غزة
        '400456789' => [0.7312, 12],

        // إبراهيم توفيق قاسم — A-, 3 donations, خانيونس
        '430567890' => [0.6795, 10],

        // أنس حسين المصري — B+, 3 donations, no GPS, خانيونس
        // Slight penalty: no GPS → higher response-time variance in ML features
        '430234567' => [0.6341, 10],

        // عمر سعيد الشريف — B+, 3 donations (donated 60 days ago → ineligible ~30 more days)
        '400345678' => [0.6180, 10],

        // ─────────────────────────────────────────────────────────────────
        // TIER 3: NEW DONORS (1-2 donations) — Higher uncertainty, lower scores
        // ─────────────────────────────────────────────────────────────────

        // أحمد يوسف الحسن — A+, 2 donations, no GPS, غزة
        // No-GPS penalty; limited history
        '400234567' => [0.5512, 7],

        // نادر وليد الشوبكي — B+, 2 donations, شمال غزة
        '410567890' => [0.5387, 7],

        // زياد مروان الأسطل — AB-, 2 donations, خانيونس
        // Rare blood type (AB-), limited pool — some variance
        '430456789' => [0.5274, 7],

        // وائل حمدي سعد — O-, 1 donation, دير البلح
        // Universal donor but only 1 donation → limited certainty
        '420567890' => [0.4723, 6],

        // حاتم فارس الدالي — A+, 1 donation, رفح
        '440345678' => [0.4391, 5],

        // باسل حازم حمدان — A+, 2 donations, SMOKER, شمال غزة
        // ML applies health-risk penalty for smoking → lower than rule-based would give
        '410456789' => [0.4106, 6],

        // طارق جمال الدبعي — O+, 1 donation (surgery 10 days ago → ineligible ~18 days)
        // Score stored; eligibility filter handles the exclusion
        '410345678' => [0.3842, 5],

    ];

    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // Step 1: Fix donor eligibility
        //
        // DatabaseSeeder uses WithoutModelEvents which silences the `saving`
        // event, so calculateEligibility() never ran during seeding.
        // We re-compute it here for every health profile.
        // ─────────────────────────────────────────────────────────────────────
        $fixed = 0;
        \App\Models\DonorHealthProfile::all()->each(function ($profile) use (&$fixed) {
            $eligibility = $profile->calculateEligibility();
            $profile->updateQuietly([
                'is_eligible'      => $eligibility['is_eligible'],
                'next_eligible_date' => $eligibility['next_eligible_date'],
            ]);
            $fixed++;
        });
        $this->command->info("DonorScoreSeeder: eligibility re-computed for {$fixed} health profiles.");

        // ─────────────────────────────────────────────────────────────────────
        // Step 2: Seed ML scores
        // ─────────────────────────────────────────────────────────────────────

        // Scores computed 2 days ago — fresh within 7-day staleness window
        $computedAt = Carbon::now()->subDays(2)->startOfDay()->addHours(3);

        $inserted = 0;

        foreach ($this->mlScores as $nationalId => [$probability, $dataPoints]) {
            $donor = Donor::where('national_id', $nationalId)->first();

            if (! $donor) {
                $this->command->warn("Donor not found: {$nationalId}");
                continue;
            }

            DonorPredictiveScore::upsert(
                [[
                    'donor_id'               => $donor->id,
                    'acceptance_probability' => $probability,
                    'data_points_count'      => $dataPoints,
                    'computed_at'            => $computedAt,
                    'model_version'          => 'xgboost_v1',
                    'created_at'             => $computedAt,
                    'updated_at'             => $computedAt,
                ]],
                ['donor_id'],
                ['acceptance_probability', 'data_points_count', 'computed_at', 'model_version', 'updated_at']
            );

            $inserted++;
        }

        $this->command->info("DonorScoreSeeder: {$inserted} ML scores inserted.");
        $this->command->info('Cold-start donors (no score, go to exploration bucket): سامي (A-, 0 donations), عدنان (B+, 0 donations)');
    }
}
