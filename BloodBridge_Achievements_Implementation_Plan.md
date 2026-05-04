# BloodBridge — Donor Achievements Feature: Complete Implementation Plan

**Version:** 1.0 — April 2026  
**Stack:** Laravel 12 · PHP 8.3 · Filament v4 · MySQL · spatie/laravel-translatable  
**Source:** Generated from full reverse-engineering of the actual codebase. Every fact is verified against source files — nothing is assumed.

---

## Table of Contents

1. [Current State Audit](#1-current-state-audit)
2. [Icon File Strategy](#2-icon-file-strategy)
3. [Achievement Definitions — All 33 Badges](#3-achievement-definitions--all-33-badges)
4. [Database Plan](#4-database-plan)
5. [Model Layer Fixes](#5-model-layer-fixes)
6. [AchievementService](#6-achievementservice)
7. [Trigger Integration Points](#7-trigger-integration-points)
8. [Seed Data](#8-seed-data)
9. [Backfill Command](#9-backfill-command)
10. [Admin Panel — AchievementResource](#10-admin-panel--achievementresource)
11. [Donor Panel — Dashboard Widget](#11-donor-panel--dashboard-widget)
12. [Donor Panel — Achievements Page](#12-donor-panel--achievements-page)
13. [Translation Keys](#13-translation-keys)
14. [Complete File Change Inventory](#14-complete-file-change-inventory)
15. [Risks and Edge Cases](#15-risks-and-edge-cases)
16. [Build Order](#16-build-order)

---

## 1. Current State Audit

### 1.1 What the Database Already Has

No migrations are needed. These tables and columns exist and are correct:

| Table / Column | Type | Status |
|---|---|---|
| `achievements` | Full table: id, name (JSON), description (JSON), points_rewards (int), `badge_icon` (varchar), badge_type (varchar), criteria_type (varchar), criteria_value (int), display_order (int), timestamps | **EXISTS — correct** |
| `donor_achievements` | id, donor_id FK, achievement_id FK, earned_at, meta (JSON), awarded_by FK, timestamps, deleted_at (softDeletes), UNIQUE(donor_id, achievement_id) | **EXISTS — correct** |
| `donors.points` | UNSIGNED INT default 0 | **EXISTS — never written** |
| `donors.level` | UNSIGNED INT default 1 | **EXISTS — never written** |

> **Critical:** The `achievements` table does NOT have a `deleted_at` column. Do not add `SoftDeletes` to the `Achievement` model or any delete action to the admin resource without first adding a migration.

### 1.2 Confirmed Model Bugs

These bugs exist in the current files and will silently corrupt data if not fixed before any service code is written:

| Model | Bug | Impact |
|---|---|---|
| `Achievement.php` | `$fillable` contains `'badge_image'` — DB column is `badge_icon` | `Achievement::create(['badge_icon' => ...])` saves null silently |
| `Achievement.php` | `$fillable` contains `'points_reward'` — DB column is `points_rewards` | Bonus points never stored correctly |
| `Achievement.php` | `badge_type` missing from `$fillable` entirely | Badge tier can never be mass-assigned |
| `DonorAchievement.php` | Class body completely empty — no `$fillable`, no casts, no relationships | Mass assignment blocked by `$guarded = ['*']` default |
| `Donor.php` | `points` and `level` absent from `$fillable` | `Donor::update(['points' => 100])` silently fails |

### 1.3 What is Entirely Missing

- No `AchievementService` class exists anywhere
- No trigger in `ResponsesRelationManager` calls achievement logic
- No admin resource, donor page, or widget touches achievements
- No PNG icons are stored anywhere in the project
- The `badge_icon` column currently stores nothing (all rows are null or don't exist yet)

### 1.4 Icon Files — Current Situation

The user has a folder `Achivments Gemini/` containing 33 PNG files. These files are **not yet in the project**. They must be copied into the Laravel storage system. The correct approach is described in Section 2.

---

## 2. Icon File Strategy

### 2.1 Recommended Approach: Laravel Public Storage

The project already uses `Storage::url()` for site_favicon and other uploaded assets, with the `public` disk at `storage/app/public/`. This is the correct and consistent location for achievement icons.

**Do not** put PNG files in `public/assets/images/`. That directory is for static frontend assets. Achievement icons are data-linked assets that may be updated by admins, so they belong in storage.

### 2.2 Icon Storage Location

```
storage/app/public/achievements/
├── قطرة-الحياة-الاولى.png
├── المساهم.png
├── المنقذ.png
├── ... (all 33 PNG files)
```

Run after copying:
```bash
php artisan storage:link
```

This creates the `public/storage` symlink. Icons are then accessible at:
```
https://your-domain.com/storage/achievements/icon-name.png
```

### 2.3 Recommended File Naming Convention

**Use English slugs** for the actual filenames, not Arabic. Arabic filenames can cause filesystem encoding issues across different servers. Map them as follows:

| Arabic Name | Recommended Filename |
|---|---|
| قطرة الحياة الاولى | first-drop.png |
| المساهم | contributor.png |
| المنقذ | savior.png |
| المنقذ العالمي | global-savior.png |
| الكنز الثمين | precious-treasure.png |
| الملتزم | committed.png |
| المنضبط | disciplined.png |
| الموثوق | reliable.png |
| الاستجابة السريعة | quick-response.png |
| الرد الفوري | immediate-reply.png |
| صاعقة البرق | lightning-bolt.png |
| بطل الطوارئ | emergency-hero.png |
| الفصيلة النادرة | rare-blood-type.png |
| البطل المحلي | local-hero.png |
| الجار الكريم | generous-neighbor.png |
| المسافر | traveler.png |
| المسافر الطويل | long-traveler.png |
| العابر للحدود | cross-border.png |
| جولة القطاع | sector-tour.png |
| المستقبل العالمي | global-future.png |
| ربع السنة الملتزم | quarter-year-committed.png |
| نصف العام الثابت | steady-half-year.png |
| الشهر المنتظم | regular-month.png |
| عام من العطاء | year-of-giving.png |
| العامان المتواصلان | two-continuous-years.png |
| بطل رمضان | ramadan-hero.png |
| يوم المتبرع العالمي | world-donor-day.png |
| دائما جاهز | always-ready.png |
| لا إلغاء | no-cancellation.png |
| الكمالية | perfection.png |
| الأسطورة | legend.png |
| الدقيق | the-precise.png |
| المائة الذهبية | golden-hundred.png |

### 2.4 How `badge_icon` Column Works with PNGs

The `badge_icon` column (varchar) will now store the **storage path** relative to the public disk, for example: `achievements/first-drop.png`.

In Blade views, reference it as:
```php
Storage::url($achievement->badge_icon)
// → /storage/achievements/first-drop.png
```

In the admin form, use Filament's `FileUpload` component targeting the `public` disk, `achievements` directory. The component automatically saves the path string to the database.

### 2.5 Initial Icon Setup Steps

```bash
# 1. Create the achievements directory in public storage
mkdir -p storage/app/public/achievements

# 2. Copy your renamed PNG files into it
# (rename them to English slugs first, as per the table above)
cp "Achivments Gemini/قطرة الحياة الاولى.png" storage/app/public/achievements/first-drop.png
# ... repeat for all 33

# 3. Ensure storage link exists
php artisan storage:link

# 4. Verify one icon is accessible
# Open in browser: http://localhost/storage/achievements/first-drop.png
```

---

## 3. Achievement Definitions — All 33 Badges

The current schema supports two criteria types: `donations` and `points`. Many of the 33 achievements require new criteria types. This section defines all 33 and categorises them by implementation phase.

### 3.1 Criteria Type Extensions Needed

Add these string values to `criteria_type`. The column is already `VARCHAR` — no migration needed. Just use these new strings and handle them in the service:

| New `criteria_type` | Meaning | Data Source |
|---|---|---|
| `donations` | Total completed donations | `donor_health_profiles.total_donations` |
| `points` | Total accumulated points | `donors.points` |
| `critical_donations` | Donations from CRITICAL urgency requests | `request_responses` joined with `blood_requests` |
| `streak_no_cancel` | Days active without any cancellation | `request_responses` |
| `response_time_fast` | Accepted a request within N hours | `request_responses.responded_at` vs notification time |
| `active_months` | Distinct calendar months with a completed donation | `request_responses` |
| `active_years` | Distinct calendar years with a completed donation | `request_responses` |
| `governorate_count` | Distinct governorates where donations occurred | `blood_requests.governorate_id` via responses |
| `special_date` | Donation on a specific date pattern (Ramadan / June 14) | `request_responses.verified_at` |
| `completion_rate_100` | 100% completion rate (no ignored/no-show) | calculated from responses |
| `rare_blood_type` | Donor has a rare blood type (AB-, B-, A-, O-) | `donor_health_profiles.blood_type` |

### 3.2 Phase 1 — Achievable with Existing + Simple Criteria (implement now)

| # | Icon File | Arabic Name | English Name | criteria_type | criteria_value | points_rewards | badge_type | display_order |
|---|---|---|---|---|---|---|---|---|
| 1 | first-drop.png | قطرة الحياة الاولى | First Drop of Life | donations | 1 | 50 | bronze | 1 |
| 2 | contributor.png | المساهم | The Contributor | donations | 3 | 75 | bronze | 2 |
| 3 | savior.png | المنقذ | The Savior | donations | 5 | 150 | silver | 3 |
| 4 | global-savior.png | المنقذ العالمي | Global Savior | donations | 10 | 300 | gold | 4 |
| 5 | precious-treasure.png | الكنز الثمين | Precious Treasure | donations | 25 | 750 | platinum | 5 |
| 6 | golden-hundred.png | المائة الذهبية | The Golden Hundred | donations | 100 | 2000 | diamond | 6 |
| 7 | legend.png | الأسطورة | The Legend | points | 1000 | 0 | diamond | 7 |
| 8 | emergency-hero.png | بطل الطوارئ | Emergency Hero | critical_donations | 1 | 100 | gold | 8 |
| 9 | rare-blood-type.png | الفصيلة النادرة | Rare Blood Type | rare_blood_type | 1 | 200 | gold | 9 |

### 3.3 Phase 2 — Time/Streak-Based Criteria (implement after Phase 1)

| # | Icon File | Arabic Name | English Name | criteria_type | criteria_value | points_rewards | badge_type | display_order |
|---|---|---|---|---|---|---|---|---|
| 10 | regular-month.png | الشهر المنتظم | Regular Month | active_months | 1 | 50 | bronze | 10 |
| 11 | quarter-year-committed.png | ربع السنة الملتزم | Quarter-Year Committed | active_months | 3 | 100 | silver | 11 |
| 12 | steady-half-year.png | نصف العام الثابت | Steady Half-Year | active_months | 6 | 200 | gold | 12 |
| 13 | year-of-giving.png | عام من العطاء | A Year of Giving | active_years | 1 | 400 | gold | 13 |
| 14 | two-continuous-years.png | العامان المتواصلان | Two Continuous Years | active_years | 2 | 800 | platinum | 14 |
| 15 | no-cancellation.png | لا إلغاء | No Cancellation | streak_no_cancel | 5 | 150 | silver | 15 |
| 16 | committed.png | الملتزم | The Committed | streak_no_cancel | 10 | 300 | gold | 16 |
| 17 | disciplined.png | المنضبط | The Disciplined | completion_rate_100 | 5 | 200 | gold | 17 |
| 18 | reliable.png | الموثوق | The Reliable | completion_rate_100 | 10 | 400 | platinum | 18 |
| 19 | perfection.png | الكمالية | Perfection | completion_rate_100 | 25 | 750 | diamond | 19 |
| 20 | always-ready.png | دائما جاهز | Always Ready | streak_no_cancel | 20 | 500 | platinum | 20 |

### 3.4 Phase 3 — Speed/Geo/Special-Date Criteria (implement last)

| # | Icon File | Arabic Name | English Name | criteria_type | criteria_value | points_rewards | badge_type | display_order |
|---|---|---|---|---|---|---|---|---|
| 21 | quick-response.png | الاستجابة السريعة | Quick Response | response_time_fast | 4 | 75 | bronze | 21 |
| 22 | immediate-reply.png | الرد الفوري | Immediate Reply | response_time_fast | 2 | 100 | silver | 22 |
| 23 | lightning-bolt.png | صاعقة البرق | Lightning Bolt | response_time_fast | 1 | 150 | gold | 23 |
| 24 | local-hero.png | البطل المحلي | Local Hero | governorate_count | 1 | 50 | bronze | 24 |
| 25 | generous-neighbor.png | الجار الكريم | Generous Neighbor | governorate_count | 2 | 100 | silver | 25 |
| 26 | traveler.png | المسافر | The Traveler | governorate_count | 3 | 150 | gold | 26 |
| 27 | long-traveler.png | المسافر الطويل | Long Traveler | governorate_count | 4 | 200 | platinum | 27 |
| 28 | cross-border.png | العابر للحدود | Cross-Border | governorate_count | 5 | 250 | diamond | 28 |
| 29 | sector-tour.png | جولة القطاع | Sector Tour | governorate_count | 5 | 300 | gold | 29 |
| 30 | global-future.png | المستقبل العالمي | Global Future | governorate_count | 5 | 500 | diamond | 30 |
| 31 | ramadan-hero.png | بطل رمضان | Ramadan Hero | special_date | 1 | 200 | gold | 31 |
| 32 | world-donor-day.png | يوم المتبرع العالمي | World Donor Day | special_date | 2 | 200 | gold | 32 |
| 33 | the-precise.png | الدقيق | The Precise | response_time_fast | 1 | 100 | silver | 33 |

> **Implementation note:** For Phase 1, implement `evaluateAndAward` with `meetsCriteria` handling only `donations`, `points`, `critical_donations`, and `rare_blood_type`. Return `false` for all other criteria types — the DB rows exist but nothing will be auto-awarded until the criteria handler is implemented in later phases.

---

## 4. Database Plan

### 4.1 No New Migrations Required for Schema

The `achievements` and `donor_achievements` tables are already correct. The `donors.points` and `donors.level` columns already exist.

### 4.2 One Small Migration Required — for `badge_icon` Column Length

The `badge_icon` column is `VARCHAR(255)`. Storage paths like `achievements/first-drop.png` are short enough. No migration needed for this either.

### 4.3 Summary of Database State

```sql
-- achievements: complete, no changes needed
-- donor_achievements: complete, no changes needed
-- donors.points: exists, just needs model $fillable fix
-- donors.level: exists, just needs model $fillable fix
```

### 4.4 Points Economy

| Event | Points |
|---|---|
| Any COMPLETED donation (base) | +50 |
| CRITICAL urgency bonus | +25 additional |
| Achievement badge earned | + `achievement.points_rewards` (varies) |

### 4.5 Level Thresholds

| Level | Min Points |
|---|---|
| 1 | 0 (starting) |
| 2 | 100 |
| 3 | 300 |
| 4 | 600 |
| 5 | 1000 (maximum) |

---

## 5. Model Layer Fixes

**These three files must be fixed before any service or resource is written. Fix them first.**

### 5.1 `app/Models/Achievement.php`

Replace the entire `$fillable` array and add one relationship. Keep everything else exactly as-is (the `HasTranslations` trait, constants, and `$translatable`).

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Achievement extends Model
{
    use HasTranslations;

    // Keep all existing constants exactly as-is
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

    // FIX: all three bugs corrected here
    protected $fillable = [
        'name',
        'description',
        'criteria_type',
        'criteria_value',
        'badge_icon',      // was 'badge_image'  — WRONG
        'points_rewards',  // was 'points_reward' — WRONG
        'badge_type',      // was MISSING
        'display_order',
    ];

    // ADD: relationship
    public function donorAchievements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DonorAchievement::class);
    }
}
```

### 5.2 `app/Models/DonorAchievement.php`

Implement the entire class from scratch. The `SoftDeletes` trait is required because the table has `deleted_at`.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorAchievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'donor_id',
        'achievement_id',
        'earned_at',
        'meta',
        'awarded_by',
    ];

    protected $casts = [
        'earned_at' => 'datetime',
        'meta'      => 'array',
    ];

    public function donor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Donor::class);
    }

    public function achievement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    // Admin user who manually awarded this (null = system auto-award)
    public function awardedByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}
```

### 5.3 `app/Models/Donor.php`

Add `points` and `level` to `$fillable` and add two achievement relationship methods. Do not change anything else — the existing casts, scopes, and relationships must be preserved.

```php
// Replace the existing $fillable array:
protected $fillable = [
    'user_id',
    'governorate_id',
    'national_id',
    'gender',
    'birth_date',
    'auto_location_address',
    'lat',
    'lng',
    'points',   // ADD
    'level',    // ADD
];

// Add these two methods after the existing relationships:

public function donorAchievements(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(DonorAchievement::class);
}

public function earnedAchievements(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    return $this->belongsToMany(Achievement::class, 'donor_achievements')
        ->withPivot(['earned_at', 'meta', 'awarded_by'])
        ->withTimestamps()
        ->wherePivotNull('deleted_at'); // honour soft deletes on the pivot
}
```

---

## 6. AchievementService

Create `app/Services/AchievementService.php`. Stateless class — no constructor injection, no HTTP calls, DB-only operations.

```php
<?php

namespace App\Services;

use App\Enums\BloodType;
use App\Enums\UrgencyLevel;
use App\Models\Achievement;
use App\Models\BloodRequest;
use App\Models\Donor;
use App\Models\DonorAchievement;
use App\Models\RequestResponse;
use App\Enums\RequestResponseStatus;
use Illuminate\Support\Facades\Log;

class AchievementService
{
    /**
     * Called once per COMPLETED donation.
     * Awards base points + urgency bonus, then evaluates all achievements.
     * MUST be called inside a DB::transaction — performs only DB operations.
     */
    public function awardDonationPoints(Donor $donor, BloodRequest $bloodRequest): void
    {
        $points = 50; // base

        if ($bloodRequest->urgency_level === UrgencyLevel::CRITICAL) {
            $points += 25;
        }

        // increment() bypasses $fillable — safe inside a transaction
        $donor->increment('points', $points);

        $this->evaluateAndAward($donor, bloodRequest: $bloodRequest);
    }

    /**
     * Evaluates all unearned achievements for a donor.
     * Awards any whose criteria are now met.
     * Returns list of newly awarded Achievement models (used by backfill for output).
     *
     * @param  bool  $backfillMode  Skip level recalculation and point awards (backfill only).
     */
    public function evaluateAndAward(
        Donor $donor,
        bool $backfillMode = false,
        ?BloodRequest $bloodRequest = null
    ): array {
        $earnedIds = $donor->donorAchievements()->pluck('achievement_id');

        $pending = Achievement::whereNotIn('id', $earnedIds)
            ->orderBy('display_order')
            ->get();

        $newlyAwarded = [];

        foreach ($pending as $achievement) {
            if ($this->meetsCriteria($donor, $achievement, $bloodRequest)) {
                if (! $backfillMode) {
                    $this->award($donor, $achievement);
                } else {
                    // Backfill: award badge only, no bonus points
                    $this->awardBadgeOnly($donor, $achievement);
                }
                $newlyAwarded[] = $achievement;
            }
        }

        if (! $backfillMode) {
            $this->recalculateLevel($donor);
        }

        return $newlyAwarded;
    }

    /**
     * Determines if a donor currently meets the criteria for a given achievement.
     */
    private function meetsCriteria(
        Donor $donor,
        Achievement $achievement,
        ?BloodRequest $context = null
    ): bool {
        $profile = $donor->healthProfile;

        return match ($achievement->criteria_type) {

            // ── Phase 1 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_DONATIONS =>
                ($profile?->total_donations ?? 0) >= $achievement->criteria_value,

            Achievement::CRITERIA_POINTS =>
                $donor->fresh()->points >= $achievement->criteria_value,

            Achievement::CRITERIA_CRITICAL =>
                DonorAchievement::query()
                    ->whereHas('achievement', fn($q) => $q->where('criteria_type', Achievement::CRITERIA_CRITICAL))
                    ->where('donor_id', $donor->id)
                    ->exists()
                    ? false // already earned one, check freshly
                    : $this->countCriticalDonations($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_RARE_BLOOD =>
                $this->hasRareBloodType($donor),

            // ── Phase 2 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_ACTIVE_MONTHS =>
                $this->countActiveMonths($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_ACTIVE_YEARS =>
                $this->countActiveYears($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_NO_CANCEL =>
                $this->countNoCancelStreak($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_COMPLETION_RATE =>
                $this->meetsCompletionRateCriteria($donor, $achievement->criteria_value),

            // ── Phase 3 ────────────────────────────────────────────────────────
            Achievement::CRITERIA_RESPONSE_TIME =>
                false, // requires notification timestamp data — implement in Phase 3

            Achievement::CRITERIA_GOVERNORATES =>
                $this->countDistinctGovernorates($donor) >= $achievement->criteria_value,

            Achievement::CRITERIA_SPECIAL_DATE =>
                false, // requires date-pattern checking — implement in Phase 3

            default => false,
        };
    }

    /**
     * Awards a badge AND its bonus points. Uses firstOrCreate for idempotency.
     * Wrapped in try/catch — achievement failure must never roll back a donation.
     */
    public function award(Donor $donor, Achievement $achievement, ?int $awardedBy = null): void
    {
        try {
            $created = DonorAchievement::firstOrCreate(
                ['donor_id' => $donor->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'awarded_by' => $awardedBy]
            );

            // Only add bonus points if this is a new award (not a duplicate)
            if ($created->wasRecentlyCreated && $achievement->points_rewards > 0) {
                $donor->increment('points', $achievement->points_rewards);
            }
        } catch (\Throwable $e) {
            Log::error('AchievementService::award failed', [
                'donor_id'       => $donor->id,
                'achievement_id' => $achievement->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Awards a badge without adding bonus points (used during backfill).
     */
    private function awardBadgeOnly(Donor $donor, Achievement $achievement): void
    {
        try {
            DonorAchievement::firstOrCreate(
                ['donor_id' => $donor->id, 'achievement_id' => $achievement->id],
                ['earned_at' => now(), 'awarded_by' => null]
            );
        } catch (\Throwable $e) {
            Log::error('AchievementService::awardBadgeOnly failed', [
                'donor_id'       => $donor->id,
                'achievement_id' => $achievement->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recalculates and saves donor level based on current points.
     * Calls fresh() because multiple increment() calls have occurred.
     */
    public function recalculateLevel(Donor $donor): void
    {
        $points = (int) $donor->fresh()->points;

        $level = match (true) {
            $points >= 1000 => 5,
            $points >= 600  => 4,
            $points >= 300  => 3,
            $points >= 100  => 2,
            default         => 1,
        };

        $donor->update(['level' => $level]);
    }

    // ── Private helper methods ─────────────────────────────────────────────

    private function countCriticalDonations(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->whereHas('bloodRequest', fn($q) => $q->where('urgency_level', UrgencyLevel::CRITICAL))
            ->count();
    }

    private function hasRareBloodType(Donor $donor): bool
    {
        $rareTypes = [
            BloodType::A_NEGATIVE,
            BloodType::B_NEGATIVE,
            BloodType::AB_NEGATIVE,
            BloodType::O_NEGATIVE,
        ];
        $bloodType = $donor->healthProfile?->verified_blood_type ?? $donor->healthProfile?->blood_type;
        return $bloodType !== null && in_array($bloodType, $rareTypes, true);
    }

    private function countActiveMonths(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->selectRaw('DATE_FORMAT(verified_at, "%Y-%m") as month')
            ->groupBy('month')
            ->count();
    }

    private function countActiveYears(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->selectRaw('YEAR(verified_at) as year')
            ->groupBy('year')
            ->count();
    }

    private function countNoCancelStreak(Donor $donor): int
    {
        // Count COMPLETED responses without any CANCELLED responses in between
        // Simplified: count total completed without having ANY cancellation record
        $hasCancellation = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::DECLINED)
            ->whereNotNull('correction_used_at') // self-cancelled
            ->exists();

        if ($hasCancellation) {
            return 0;
        }

        return (int) ($donor->healthProfile?->total_donations ?? 0);
    }

    private function meetsCompletionRateCriteria(Donor $donor, int $minDonations): bool
    {
        $total = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->whereIn('status', [
                RequestResponseStatus::COMPLETED,
                RequestResponseStatus::DECLINED,
                RequestResponseStatus::NO_SHOW,
                RequestResponseStatus::UNREACHABLE,
            ])
            ->count();

        $completed = RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->count();

        return $completed >= $minDonations && $total > 0 && ($completed / $total) === 1.0;
    }

    private function countDistinctGovernorates(Donor $donor): int
    {
        return RequestResponse::query()
            ->where('donor_id', $donor->id)
            ->where('status', RequestResponseStatus::COMPLETED)
            ->join('blood_requests', 'request_responses.blood_request_id', '=', 'blood_requests.id')
            ->join('organizations', 'blood_requests.organization_id', '=', 'organizations.id')
            ->distinct()
            ->count('organizations.governorate_id');
    }
}
```

---

## 7. Trigger Integration Points

Open `app/Filament/Organization/Resources/BloodRequests/RelationManagers/ResponsesRelationManager.php`.

There are exactly **two** locations where `$healthProfile->save()` is called inside the `eligible` branch of a DB transaction. Both need the hook.

### 7.1 Trigger Point 1 — Primary `medical_results` Action (line ~571)

Find this exact block:

```php
                                $healthProfile->total_donations = ($healthProfile->total_donations ?? 0) + 1;
                            }

                            $healthProfile->save();   // ← existing line ~571
```

**Add immediately after** `$healthProfile->save()`:

```php
                            $healthProfile->save();   // ← existing line

                            // Award points and evaluate achievements.
                            // DB-only — safe inside this transaction.
                            if ($record->status === RequestResponseStatus::COMPLETED) {
                                try {
                                    app(\App\Services\AchievementService::class)
                                        ->awardDonationPoints($record->donor, $record->bloodRequest);
                                } catch (\Throwable $e) {
                                    \Illuminate\Support\Facades\Log::error('Achievement hook failed (medical_results)', [
                                        'donor_id' => $record->donor_id,
                                        'error'    => $e->getMessage(),
                                    ]);
                                }
                            }
```

### 7.2 Trigger Point 2 — Correction `correct_medical_results` Action (line ~851)

Find the second `$healthProfile->save()` inside the correction flow's eligible branch:

```php
                                    $healthProfile->total_donations = ($healthProfile->total_donations ?? 0) + 1;
                                }

                                $healthProfile->save();   // ← existing line ~851
```

**Add the same hook immediately after:**

```php
                                $healthProfile->save();   // ← existing line

                                if ($record->status === RequestResponseStatus::COMPLETED) {
                                    try {
                                        app(\App\Services\AchievementService::class)
                                            ->awardDonationPoints($record->donor, $record->bloodRequest);
                                    } catch (\Throwable $e) {
                                        \Illuminate\Support\Facades\Log::error('Achievement hook failed (correction)', [
                                            'donor_id' => $record->donor_id,
                                            'error'    => $e->getMessage(),
                                        ]);
                                    }
                                }
```

### 7.3 What Must NOT Get the Hook

The `correct_blood_type` action (the third action in the file) must **not** receive the hook. It only corrects the verified blood type and does not increment `total_donations`.

---

## 8. Seed Data

Create `database/seeders/AchievementsSeeder.php`. Use `updateOrCreate` so it is safe to run multiple times. Do **not** add to `DatabaseSeeder::run()` — achievements may already exist in production at runtime.

```php
<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementsSeeder extends Seeder
{
    public function run(): void
    {
        // Phase 1 achievements only — auto-awarded by the service
        $achievements = [
            [
                'name'           => ['en' => 'First Drop of Life',  'ar' => 'قطرة الحياة الأولى'],
                'description'    => ['en' => 'Complete your first blood donation.', 'ar' => 'أتممت أول تبرع بالدم.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 1,
                'points_rewards' => 50,
                'badge_type'     => 'bronze',
                'badge_icon'     => 'achievements/first-drop.png',
                'display_order'  => 1,
            ],
            [
                'name'           => ['en' => 'The Contributor',     'ar' => 'المساهم'],
                'description'    => ['en' => 'Donate blood 3 times.', 'ar' => 'تبرعت بالدم 3 مرات.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 3,
                'points_rewards' => 75,
                'badge_type'     => 'bronze',
                'badge_icon'     => 'achievements/contributor.png',
                'display_order'  => 2,
            ],
            [
                'name'           => ['en' => 'The Savior',          'ar' => 'المنقذ'],
                'description'    => ['en' => 'Donate blood 5 times.', 'ar' => 'تبرعت بالدم 5 مرات.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 5,
                'points_rewards' => 150,
                'badge_type'     => 'silver',
                'badge_icon'     => 'achievements/savior.png',
                'display_order'  => 3,
            ],
            [
                'name'           => ['en' => 'Global Savior',       'ar' => 'المنقذ العالمي'],
                'description'    => ['en' => 'Donate blood 10 times.', 'ar' => 'تبرعت بالدم 10 مرات.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 10,
                'points_rewards' => 300,
                'badge_type'     => 'gold',
                'badge_icon'     => 'achievements/global-savior.png',
                'display_order'  => 4,
            ],
            [
                'name'           => ['en' => 'Precious Treasure',   'ar' => 'الكنز الثمين'],
                'description'    => ['en' => 'Donate blood 25 times.', 'ar' => 'تبرعت بالدم 25 مرة.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 25,
                'points_rewards' => 750,
                'badge_type'     => 'platinum',
                'badge_icon'     => 'achievements/precious-treasure.png',
                'display_order'  => 5,
            ],
            [
                'name'           => ['en' => 'The Golden Hundred',  'ar' => 'المائة الذهبية'],
                'description'    => ['en' => 'Donate blood 100 times.', 'ar' => 'تبرعت بالدم 100 مرة.'],
                'criteria_type'  => 'donations',
                'criteria_value' => 100,
                'points_rewards' => 2000,
                'badge_type'     => 'diamond',
                'badge_icon'     => 'achievements/golden-hundred.png',
                'display_order'  => 6,
            ],
            [
                'name'           => ['en' => 'The Legend',          'ar' => 'الأسطورة'],
                'description'    => ['en' => 'Reach 1000 points.', 'ar' => 'اجمع 1000 نقطة.'],
                'criteria_type'  => 'points',
                'criteria_value' => 1000,
                'points_rewards' => 0,
                'badge_type'     => 'diamond',
                'badge_icon'     => 'achievements/legend.png',
                'display_order'  => 7,
            ],
            [
                'name'           => ['en' => 'Emergency Hero',      'ar' => 'بطل الطوارئ'],
                'description'    => ['en' => 'Donate in response to a critical blood request.', 'ar' => 'تبرعت استجابةً لطلب دم حرج.'],
                'criteria_type'  => 'critical_donations',
                'criteria_value' => 1,
                'points_rewards' => 100,
                'badge_type'     => 'gold',
                'badge_icon'     => 'achievements/emergency-hero.png',
                'display_order'  => 8,
            ],
            [
                'name'           => ['en' => 'Rare Blood Type',     'ar' => 'الفصيلة النادرة'],
                'description'    => ['en' => 'You have a rare blood type. Your donation is especially precious.', 'ar' => 'لديك فصيلة دم نادرة. تبرعك نادر وثمين.'],
                'criteria_type'  => 'rare_blood_type',
                'criteria_value' => 1,
                'points_rewards' => 200,
                'badge_type'     => 'gold',
                'badge_icon'     => 'achievements/rare-blood-type.png',
                'display_order'  => 9,
            ],
            // Add all other Phase 2 and Phase 3 achievements the same way
        ];

        foreach ($achievements as $data) {
            Achievement::updateOrCreate(
                ['criteria_type' => $data['criteria_type'], 'criteria_value' => $data['criteria_value']],
                $data
            );
        }
    }
}
```

Run with:
```bash
php artisan db:seed --class=AchievementsSeeder
```

---

## 9. Backfill Command

Create `app/Console/Commands/BackfillAchievementsCommand.php`.

**Key design decision:** The backfill awards **badges only**, not points. Retroactively awarding 50 points × N historical donations would be dishonest and would push donors to max level on day one. Recognition is backfilled; points accumulate from deployment forward.

```php
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
```

---

## 10. Admin Panel — AchievementResource

### 10.1 Folder Structure

Match the existing admin resource pattern exactly (as used by `Announcements/`, `Donors/`, etc.):

```
app/Filament/Admin/Resources/Achievements/
├── AchievementResource.php
├── Pages/
│   ├── ListAchievements.php
│   ├── CreateAchievement.php
│   └── EditAchievement.php
├── Schemas/
│   └── AchievementForm.php
└── Tables/
    └── AchievementsTable.php
```

### 10.2 `AchievementResource.php`

```php
<?php

namespace App\Filament\Admin\Resources\Achievements;

use App\Models\Achievement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class AchievementResource extends Resource
{
    use Translatable;

    protected static ?string $model = Achievement::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 4; // after Donors(1), Orgs(2), Blood Requests(3)

    public static function getNavigationLabel(): string
    {
        return __('filament.resources.achievements.title');
    }

    public static function getNavigationGroup(): ?string
    {
        // Same group as Donors and Organizations
        return __('filament.navigation.operations');
    }

    public static function getModelLabel(): string
    {
        return __('filament.resources.achievements.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.achievements.plural');
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTranslatableLocales(): array
    {
        return ['ar', 'en'];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count() ?: null;
    }

    public static function form(Schema $schema): Schema
    {
        return AchievementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAchievements::route('/'),
            'create' => Pages\CreateAchievement::route('/create'),
            'edit'   => Pages\EditAchievement::route('/{record}/edit'),
            // No 'view' page — edit page is sufficient
        ];
    }
}
```

### 10.3 `Schemas/AchievementForm.php`

```php
<?php

namespace App\Filament\Admin\Resources\Achievements\Schemas;

use App\Models\Achievement;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make(__('admin.achievement_details'))
                ->description(__('admin.achievement_details_description'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('admin.achievement_name'))
                        ->required()
                        ->maxLength(100),

                    Textarea::make('description')
                        ->label(__('admin.achievement_description'))
                        ->rows(2)
                        ->maxLength(300),
                ]),

            Section::make(__('admin.achievement_badge'))
                ->schema([
                    // FileUpload replaces the old heroicon text input.
                    // Stores relative path like "achievements/first-drop.png"
                    FileUpload::make('badge_icon')
                        ->label(__('admin.badge_icon_image'))
                        ->helperText(__('admin.badge_icon_image_helper'))
                        ->image()
                        ->disk('public')
                        ->directory('achievements')
                        ->visibility('public')
                        ->imagePreviewHeight('80')
                        ->maxSize(512), // 512KB max

                    Select::make('badge_type')
                        ->label(__('admin.badge_type'))
                        ->options([
                            'bronze'   => __('admin.badge_bronze'),
                            'silver'   => __('admin.badge_silver'),
                            'gold'     => __('admin.badge_gold'),
                            'platinum' => __('admin.badge_platinum'),
                            'diamond'  => __('admin.badge_diamond'),
                        ])
                        ->native(false),

                    TextInput::make('display_order')
                        ->label(__('admin.display_order'))
                        ->numeric()
                        ->minValue(0)
                        ->default(Achievement::DEFAULT_DISPLAY_ORDER),
                ])->columns(2),

            Section::make(__('admin.achievement_criteria'))
                ->description(__('admin.achievement_criteria_description'))
                ->columns(2)
                ->schema([
                    Select::make('criteria_type')
                        ->label(__('admin.criteria_type'))
                        ->options(array_combine(Achievement::CRITERIA_LIST, array_map(
                            fn($k) => __("admin.criteria_{$k}"),
                            Achievement::CRITERIA_LIST
                        )))
                        ->required()
                        ->native(false)
                        // Disable on edit — changing criteria invalidates existing awards
                        ->disabled(fn($record) => $record !== null),

                    TextInput::make('criteria_value')
                        ->label(__('admin.criteria_value'))
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->disabled(fn($record) => $record !== null),

                    TextInput::make('points_rewards')
                        ->label(__('admin.points_rewards'))
                        ->helperText(__('admin.points_rewards_helper'))
                        ->numeric()
                        ->minValue(0)
                        ->default(Achievement::DEFAULT_POINTS_REWARDS),
                ]),
        ]);
    }
}
```

### 10.4 `Tables/AchievementsTable.php`

```php
<?php

namespace App\Filament\Admin\Resources\Achievements\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('badge_icon')
                    ->label(__('admin.badge_icon_image'))
                    ->disk('public')
                    ->height(48)
                    ->width(48),

                TextColumn::make('name')
                    ->label(__('admin.achievement_name'))
                    ->getStateUsing(fn($record, $livewire) =>
                        $record->getTranslation('name', $livewire?->activeLocale ?? app()->getLocale(), false)
                        ?: $record->getTranslation('name', 'ar', false)
                    )
                    ->searchable()
                    ->sortable(),

                TextColumn::make('criteria_type')
                    ->label(__('admin.criteria_type'))
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'donations'         => 'danger',
                        'points'            => 'success',
                        'critical_donations'=> 'warning',
                        'rare_blood_type'   => 'primary',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn($state) => __("admin.criteria_{$state}")),

                TextColumn::make('criteria_value')
                    ->label(__('admin.criteria_value'))
                    ->sortable(),

                TextColumn::make('points_rewards')
                    ->label(__('admin.points_rewards'))
                    ->sortable(),

                TextColumn::make('badge_type')
                    ->label(__('admin.badge_type'))
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'bronze'   => 'warning',
                        'silver'   => 'gray',
                        'gold'     => 'success',
                        'platinum' => 'info',
                        'diamond'  => 'primary',
                        default    => 'gray',
                    }),

                TextColumn::make('donorAchievements_count')
                    ->counts('donorAchievements')
                    ->label(__('admin.earned_by_donors'))
                    ->sortable(),

                TextColumn::make('display_order')
                    ->label(__('admin.display_order'))
                    ->sortable(),
            ])
            ->defaultSort('display_order', 'asc')
            ->recordActions([
                EditAction::make()->label(__('admin.edit')),
                // No DeleteAction — achievements table has no deleted_at column
            ]);
    }
}
```

### 10.5 Page Files

**`Pages/ListAchievements.php`**
```php
<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListAchievements extends ListRecords
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
```

**`Pages/CreateAchievement.php`**
```php
<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateAchievement extends CreateRecord
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array { return [LocaleSwitcher::make()]; }
}
```

**`Pages/EditAchievement.php`**
```php
<?php

namespace App\Filament\Admin\Resources\Achievements\Pages;

use App\Filament\Admin\Resources\Achievements\AchievementResource;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditAchievement extends EditRecord
{
    use Translatable;
    protected static string $resource = AchievementResource::class;
    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            // NO DeleteAction — achievements table has no deleted_at column
        ];
    }
}
```

---

## 11. Donor Panel — Dashboard Widget

### 11.1 Widget Class

Create `app/Filament/Donor/Widgets/DonorLatestAchievementWidget.php`:

```php
<?php

namespace App\Filament\Donor\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonorLatestAchievementWidget extends Widget
{
    protected string $view = 'filament.donor.widgets.donor-latest-achievement-widget';
    protected int|string|array $columnSpan = 'full';

    public function getLatestAchievementData(): array
    {
        $donor = Auth::user()?->donor;

        if (! $donor) {
            return ['has_achievement' => false, 'points' => 0, 'level' => 1, 'total' => 0];
        }

        $latest = $donor->donorAchievements()
            ->with('achievement')
            ->orderBy('earned_at', 'desc')
            ->first();

        return [
            'has_achievement' => $latest !== null,
            'latest'          => $latest,
            'points'          => (int) $donor->points,
            'level'           => (int) $donor->level,
            'total'           => $donor->donorAchievements()->count(),
        ];
    }
}
```

### 11.2 Register in Dashboard and Provider

**`app/Filament/Donor/Pages/Dashboard.php`** — add to `getWidgets()`:

```php
public function getWidgets(): array
{
    return [
        DonorHeaderWidget::class,
        DonorStatsOverviewWidget::class,
        DonorLatestAchievementWidget::class,  // ADD
    ];
}
```

**`app/Providers/Filament/DonorPanelProvider.php`** — add to `->widgets([...])`:

```php
->widgets([
    \App\Filament\Donor\Widgets\DonorHeaderWidget::class,
    \App\Filament\Donor\Widgets\DonorStatsOverviewWidget::class,
    \App\Filament\Donor\Widgets\DonorLatestAchievementWidget::class,  // ADD
])
```

### 11.3 Blade View

Create `resources/views/filament/donor/widgets/donor-latest-achievement-widget.blade.php`:

```blade
<x-filament-widgets::widget>
    @php($data = $this->getLatestAchievementData())

    <div class="ach-summary-card">

        {{-- Left: Level + Points --}}
        <div class="ach-summary-left">
            <span class="ach-level-pill">{{ __('donor.level') }} {{ $data['level'] }}</span>
            <div class="ach-points-row">
                <span class="ach-points-num">{{ number_format($data['points']) }}</span>
                <span class="ach-points-unit">{{ __('donor.points') }}</span>
            </div>
        </div>

        <div class="ach-divider"></div>

        {{-- Right: Latest badge or call-to-action --}}
        <div class="ach-summary-right">
            @if($data['has_achievement'])
                <div class="ach-badge-row">
                    @php($ach = $data['latest']->achievement)
                    <div class="ach-icon-wrap ach-tier-{{ $ach->badge_type }}">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-badge-img"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-badge-icon-fallback" />
                        @endif
                    </div>
                    <div class="ach-badge-info">
                        <span class="ach-badge-label">{{ __('donor.latest_achievement') }}</span>
                        <span class="ach-badge-name">{{ $ach->getTranslation('name', app()->getLocale()) }}</span>
                        <span class="ach-badge-date">{{ $data['latest']->earned_at?->toDateString() }}</span>
                    </div>
                </div>
                <a href="{{ \App\Filament\Donor\Pages\Achievements::getUrl() }}"
                   class="ach-view-link">
                    {{ __('donor.view_all_achievements') }} ({{ $data['total'] }})
                </a>
            @else
                <div class="ach-empty-row">
                    <x-filament::icon icon="heroicon-o-trophy" class="ach-empty-icon" />
                    <div>
                        <span class="ach-empty-text">{{ __('donor.no_achievements_yet') }}</span>
                        <a href="{{ \App\Filament\Donor\Pages\Achievements::getUrl() }}"
                           class="ach-view-link">{{ __('donor.view_achievements') }}</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .ach-summary-card {
            display: flex; align-items: center; gap: 1.5rem;
            background: #ffffff; border: 1px solid #fee2e2;
            border-radius: 1rem; padding: 1.25rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .dark .ach-summary-card {
            background: #111827; border-color: #450a0a;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        .ach-summary-left { display: flex; flex-direction: column; align-items: center; gap: 0.4rem; flex-shrink: 0; }
        .ach-level-pill {
            background: #fee2e2; color: #b91c1c; font-weight: 800;
            font-size: 0.82rem; padding: 0.25rem 0.85rem;
            border-radius: 9999px; border: 1px solid #fecaca; white-space: nowrap;
        }
        .dark .ach-level-pill { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
        .ach-points-row { display: flex; align-items: baseline; gap: 0.25rem; }
        .ach-points-num { font-size: 1.75rem; font-weight: 800; color: #dc2626; line-height: 1; }
        .dark .ach-points-num { color: #ef4444; }
        .ach-points-unit { font-size: 0.72rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
        .ach-divider { width: 1px; background: #f3f4f6; align-self: stretch; }
        .dark .ach-divider { background: #1f2937; }
        .ach-summary-right { flex: 1; display: flex; flex-direction: column; gap: 0.5rem; }
        .ach-badge-row { display: flex; align-items: center; gap: 0.85rem; }
        .ach-icon-wrap {
            width: 3.5rem; height: 3.5rem; border-radius: 0.75rem;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; overflow: hidden;
            border: 2px solid rgba(0,0,0,0.06);
        }
        /* Badge tier backgrounds */
        .ach-tier-bronze   { background: #fef3c7; }
        .ach-tier-silver   { background: #f3f4f6; }
        .ach-tier-gold     { background: #fef9c3; }
        .ach-tier-platinum { background: #e0f2fe; }
        .ach-tier-diamond  { background: #ede9fe; }
        .dark .ach-tier-bronze   { background: #78350f; }
        .dark .ach-tier-silver   { background: #374151; }
        .dark .ach-tier-gold     { background: #713f12; }
        .dark .ach-tier-platinum { background: #0c4a6e; }
        .dark .ach-tier-diamond  { background: #4c1d95; }
        .ach-badge-img { width: 100%; height: 100%; object-fit: contain; padding: 0.35rem; }
        .ach-badge-icon-fallback { width: 1.5rem; height: 1.5rem; color: #9ca3af; }
        .ach-badge-info { display: flex; flex-direction: column; gap: 0.1rem; }
        .ach-badge-label { font-size: 0.7rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .ach-badge-name { font-weight: 700; color: #1f2937; font-size: 1rem; }
        .dark .ach-badge-name { color: #f3f4f6; }
        .ach-badge-date { font-size: 0.75rem; color: #6b7280; }
        .ach-view-link { font-size: 0.8rem; color: #dc2626; font-weight: 600; text-decoration: none; width: fit-content; }
        .ach-view-link:hover { text-decoration: underline; }
        .ach-empty-row { display: flex; align-items: center; gap: 0.75rem; }
        .ach-empty-icon { width: 2rem; height: 2rem; color: #d1d5db; }
        .dark .ach-empty-icon { color: #374151; }
        .ach-empty-text { display: block; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.2rem; }
        @media (max-width: 640px) {
            .ach-summary-card { flex-direction: column; text-align: center; }
            .ach-divider { width: 100%; height: 1px; }
            .ach-badge-row { flex-direction: column; }
        }
    </style>
</x-filament-widgets::widget>
```

---

## 12. Donor Panel — Achievements Page

### 12.1 Page Class

Create `app/Filament/Donor/Pages/Achievements.php`:

```php
<?php

namespace App\Filament\Donor\Pages;

use App\Models\Achievement;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Achievements extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';
    protected static ?int $navigationSort = 20; // between Blood Requests and History
    protected string $view = 'filament.donor.pages.achievements';

    public static function getNavigationLabel(): string { return __('donor.achievements'); }
    public function getTitle(): string                  { return __('donor.achievements'); }
    public function getHeading(): string                { return ''; } // suppress default heading

    public function getAchievementsData(): array
    {
        $donor = Auth::user()?->donor;

        if (! $donor) {
            return ['earned' => [], 'locked' => [], 'points' => 0, 'level' => 1];
        }

        $earnedRows = $donor->donorAchievements()
            ->with('achievement')
            ->orderBy('earned_at', 'desc')
            ->get();

        $earnedIds = $earnedRows->pluck('achievement_id');

        $locked = Achievement::whereNotIn('id', $earnedIds)
            ->orderBy('display_order')
            ->get()
            ->map(function (Achievement $a) use ($donor) {
                $current = match ($a->criteria_type) {
                    'donations' => $donor->healthProfile?->total_donations ?? 0,
                    'points'    => $donor->points,
                    default     => 0,
                };
                $target   = $a->criteria_value;
                $progress = $target > 0 ? min(100, (int) round(($current / $target) * 100)) : 0;
                return compact('achievement', 'current', 'target', 'progress') +
                       ['achievement' => $a];
            });

        return [
            'earned'  => $earnedRows,
            'locked'  => $locked,
            'points'  => (int) $donor->points,
            'level'   => (int) $donor->level,
        ];
    }
}
```

Register in `DonorPanelProvider.php` `->pages([...])`:
```php
->pages([
    \App\Filament\Donor\Pages\Dashboard::class,
    \App\Filament\Donor\Pages\EditProfile::class,
    \App\Filament\Donor\Pages\ChangePassword::class,
    \App\Filament\Donor\Pages\Achievements::class,  // ADD
])
```

### 12.2 Blade View

Create `resources/views/filament/donor/pages/achievements.blade.php`:

```blade
<x-filament-panels::page>
    @php($data = $this->getAchievementsData())

    <div class="ach-page">

        {{-- ── Header ── --}}
        <div class="ach-header-card">
            <div class="ach-header-left">
                <span class="ach-level-pill">{{ __('donor.level') }} {{ $data['level'] }}</span>
                <div class="ach-pts-display">
                    <span class="ach-pts-num">{{ number_format($data['points']) }}</span>
                    <span class="ach-pts-unit">{{ __('donor.points') }}</span>
                </div>
            </div>
            <p class="ach-header-summary">
                {{ __('donor.achievements_earned_count', ['count' => count($data['earned'])]) }}
                &middot;
                {{ __('donor.achievements_remaining_count', ['count' => count($data['locked'])]) }}
                {{ __('donor.achievements_remaining_label') }}
            </p>
        </div>

        {{-- ── Earned ── --}}
        @if(count($data['earned']) > 0)
        <div class="ach-section">
            <h2 class="ach-section-title">
                <x-filament::icon icon="heroicon-o-check-badge" class="ach-section-icon" />
                {{ __('donor.earned_achievements') }}
            </h2>
            <div class="ach-grid">
                @foreach($data['earned'] as $row)
                @php($ach = $row->achievement)
                <div class="ach-card ach-card--earned ach-tier-{{ $ach->badge_type }}">
                    <div class="ach-card-icon-wrap">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-card-badge-img"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-card-icon" />
                        @endif
                    </div>
                    <div class="ach-card-body">
                        <span class="ach-card-name">
                            {{ $ach->getTranslation('name', app()->getLocale()) }}
                        </span>
                        <span class="ach-card-desc">
                            {{ $ach->getTranslation('description', app()->getLocale()) }}
                        </span>
                        <span class="ach-card-earned-at">
                            {{ __('donor.earned_on') }} {{ $row->earned_at?->toDateString() }}
                        </span>
                    </div>
                    <span class="ach-tier-label">{{ $ach->badge_type }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Locked ── --}}
        @if(count($data['locked']) > 0)
        <div class="ach-section">
            <h2 class="ach-section-title">
                <x-filament::icon icon="heroicon-o-lock-closed" class="ach-section-icon" />
                {{ __('donor.locked_achievements') }}
            </h2>
            <div class="ach-grid">
                @foreach($data['locked'] as $item)
                @php($ach = $item['achievement'])
                <div class="ach-card ach-card--locked">
                    <div class="ach-card-icon-wrap ach-locked-wrap">
                        @if($ach->badge_icon)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($ach->badge_icon) }}"
                                alt="{{ $ach->getTranslation('name', app()->getLocale()) }}"
                                class="ach-card-badge-img ach-card-badge-img--locked"
                            />
                        @else
                            <x-filament::icon icon="heroicon-o-trophy" class="ach-card-icon ach-card-icon--locked" />
                        @endif
                    </div>
                    <div class="ach-card-body">
                        <span class="ach-card-name ach-card-name--locked">
                            {{ $ach->getTranslation('name', app()->getLocale()) }}
                        </span>
                        {{-- "How to earn it" — pulled from achievement description --}}
                        <span class="ach-card-desc">
                            {{ $ach->getTranslation('description', app()->getLocale()) }}
                        </span>
                        {{-- Progress bar (only shown for simple donation/points criteria) --}}
                        @if(in_array($ach->criteria_type, ['donations', 'points']))
                        <div class="ach-progress-wrap">
                            <div class="ach-progress-bar">
                                <div class="ach-progress-fill" style="width: {{ $item['progress'] }}%"></div>
                            </div>
                            <span class="ach-progress-label">
                                {{ $item['current'] }} / {{ $item['target'] }}
                                ({{ $item['progress'] }}%)
                            </span>
                        </div>
                        @endif
                    </div>
                    <span class="ach-tier-label">{{ $ach->badge_type }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Empty state ── --}}
        @if(count($data['earned']) === 0 && count($data['locked']) === 0)
        <div class="ach-empty">
            <x-filament::icon icon="heroicon-o-trophy" class="ach-empty-icon" />
            <p>{{ __('donor.no_achievements_defined') }}</p>
        </div>
        @endif
    </div>

    <style>
        .ach-page { display: flex; flex-direction: column; gap: 2rem; }

        /* Header */
        .ach-header-card {
            display: flex; align-items: center; justify-content: space-between; gap: 1.5rem;
            background: #ffffff; border: 1px solid #fee2e2;
            border-radius: 1rem; padding: 1.25rem 1.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .dark .ach-header-card { background: #111827; border-color: #450a0a; }
        .ach-header-left { display: flex; align-items: center; gap: 1rem; }
        .ach-level-pill {
            background: #fee2e2; color: #b91c1c; font-weight: 800;
            font-size: 0.82rem; padding: 0.3rem 1rem;
            border-radius: 9999px; border: 1px solid #fecaca;
        }
        .dark .ach-level-pill { background: #450a0a; color: #fca5a5; border-color: #7f1d1d; }
        .ach-pts-display { display: flex; align-items: baseline; gap: 0.3rem; }
        .ach-pts-num { font-size: 2rem; font-weight: 800; color: #dc2626; line-height: 1; }
        .dark .ach-pts-num { color: #ef4444; }
        .ach-pts-unit { font-size: 0.78rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; }
        .ach-header-summary { color: #6b7280; font-size: 0.88rem; margin: 0; }
        .dark .ach-header-summary { color: #9ca3af; }

        /* Section */
        .ach-section { display: flex; flex-direction: column; gap: 1rem; }
        .ach-section-title {
            display: flex; align-items: center; gap: 0.5rem;
            font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 0;
        }
        .dark .ach-section-title { color: #f3f4f6; }
        .ach-section-icon { width: 1.2rem; height: 1.2rem; color: #dc2626; }

        /* Grid */
        .ach-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        /* Card */
        .ach-card {
            display: flex; flex-direction: column; gap: 0.75rem;
            background: #ffffff; border-radius: 1rem;
            padding: 1.25rem; position: relative; overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f3f4f6;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .ach-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.1); transform: translateY(-2px); }
        .dark .ach-card { background: #1f2937; border-color: #374151; }

        /* Earned card coloured left border based on tier */
        .ach-card--earned { border-left: 4px solid #9ca3af; }
        .ach-card--earned.ach-tier-bronze   { border-left-color: #d97706; }
        .ach-card--earned.ach-tier-silver   { border-left-color: #9ca3af; }
        .ach-card--earned.ach-tier-gold     { border-left-color: #eab308; }
        .ach-card--earned.ach-tier-platinum { border-left-color: #38bdf8; }
        .ach-card--earned.ach-tier-diamond  { border-left-color: #a78bfa; }

        /* Locked card muted */
        .ach-card--locked { opacity: 0.82; }

        /* Icon wrap */
        .ach-card-icon-wrap {
            width: 3rem; height: 3rem; border-radius: 0.65rem;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden; background: #fee2e2;
        }
        .dark .ach-card-icon-wrap { background: #450a0a; }
        .ach-locked-wrap { background: #f3f4f6; }
        .dark .ach-locked-wrap { background: #374151; }
        .ach-card-badge-img { width: 100%; height: 100%; object-fit: contain; padding: 0.3rem; }
        .ach-card-badge-img--locked { opacity: 0.5; filter: grayscale(100%); }
        .ach-card-icon { width: 1.4rem; height: 1.4rem; color: #dc2626; }
        .ach-card-icon--locked { color: #9ca3af; }

        /* Card body */
        .ach-card-body { display: flex; flex-direction: column; gap: 0.2rem; flex: 1; }
        .ach-card-name { font-weight: 700; color: #1f2937; font-size: 0.95rem; }
        .dark .ach-card-name { color: #f3f4f6; }
        .ach-card-name--locked { color: #6b7280; }
        .dark .ach-card-name--locked { color: #9ca3af; }
        .ach-card-desc { font-size: 0.78rem; color: #6b7280; line-height: 1.4; }
        .dark .ach-card-desc { color: #9ca3af; }
        .ach-card-earned-at { font-size: 0.72rem; color: #9ca3af; margin-top: 0.2rem; }

        /* Progress */
        .ach-progress-wrap { display: flex; flex-direction: column; gap: 0.2rem; margin-top: 0.3rem; }
        .ach-progress-bar { background: #f3f4f6; border-radius: 9999px; height: 5px; overflow: hidden; }
        .dark .ach-progress-bar { background: #374151; }
        .ach-progress-fill {
            background: linear-gradient(to right, #ef4444, #dc2626);
            height: 100%; border-radius: 9999px; transition: width 0.4s;
        }
        .ach-progress-label { font-size: 0.7rem; color: #9ca3af; }

        /* Tier label corner decoration */
        .ach-tier-label {
            position: absolute; top: 0.5rem; right: 0.6rem;
            font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: #9ca3af; opacity: 0.7;
        }

        /* Empty */
        .ach-empty {
            display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
            padding: 3rem; text-align: center;
            background: #ffffff; border-radius: 1rem; border: 1px dashed #e5e7eb;
        }
        .dark .ach-empty { background: #1f2937; border-color: #374151; }
        .ach-empty-icon { width: 3rem; height: 3rem; color: #e5e7eb; }
        .dark .ach-empty-icon { color: #374151; }
        .ach-empty p { color: #9ca3af; font-size: 0.95rem; margin: 0; }

        /* Responsive */
        @media (max-width: 640px) {
            .ach-header-card { flex-direction: column; text-align: center; }
            .ach-grid { grid-template-columns: 1fr; }
        }
    </style>
</x-filament-panels::page>
```

---

## 13. Translation Keys

### 13.1 `lang/en.json` additions

```json
"filament.resources.achievements.title":    "Achievements",
"filament.resources.achievements.singular": "Achievement",
"filament.resources.achievements.plural":   "Achievements"
```

### 13.2 `lang/ar.json` additions

```json
"filament.resources.achievements.title":    "الإنجازات",
"filament.resources.achievements.singular": "إنجاز",
"filament.resources.achievements.plural":   "الإنجازات"
```

### 13.3 `lang/en/donor.php` additions

```php
'achievements'                   => 'Achievements',
'points'                         => 'Points',
'level'                          => 'Level',
'latest_achievement'             => 'Latest Achievement',
'earned_on'                      => 'Earned on',
'earned_achievements'            => 'Earned Achievements',
'locked_achievements'            => 'Locked Achievements',
'no_achievements_yet'            => 'No achievements yet — complete your first donation!',
'no_achievements_defined'        => 'No achievements have been configured yet.',
'view_all_achievements'          => 'View all achievements',
'view_achievements'              => 'View Achievements',
'achievements_earned_count'      => ':count earned',
'achievements_remaining_count'   => ':count',
'achievements_remaining_label'   => 'remaining',
```

### 13.4 `lang/ar/donor.php` additions

```php
'achievements'                   => 'الإنجازات',
'points'                         => 'نقطة',
'level'                          => 'المستوى',
'latest_achievement'             => 'آخر إنجاز',
'earned_on'                      => 'حُصل عليه في',
'earned_achievements'            => 'الإنجازات المحققة',
'locked_achievements'            => 'الإنجازات المقفلة',
'no_achievements_yet'            => 'لا توجد إنجازات بعد — أكمل أول تبرع لك!',
'no_achievements_defined'        => 'لم يتم تكوين أي إنجازات بعد.',
'view_all_achievements'          => 'عرض جميع الإنجازات',
'view_achievements'              => 'عرض الإنجازات',
'achievements_earned_count'      => ':count محقق',
'achievements_remaining_count'   => ':count',
'achievements_remaining_label'   => 'متبقية',
```

### 13.5 `lang/en/admin.php` additions

```php
'achievement_details'              => 'Achievement Details',
'achievement_details_description'  => 'Name and description shown to donors',
'achievement_name'                 => 'Name',
'achievement_description'          => 'Description',
'achievement_badge'                => 'Badge',
'achievement_criteria'             => 'Criteria',
'achievement_criteria_description' => 'Define when this achievement is automatically awarded',
'badge_icon_image'                 => 'Badge Icon (PNG)',
'badge_icon_image_helper'          => 'Upload the PNG icon for this badge. Max 512KB.',
'badge_type'                       => 'Badge Tier',
'badge_bronze'                     => 'Bronze',
'badge_silver'                     => 'Silver',
'badge_gold'                       => 'Gold',
'badge_platinum'                   => 'Platinum',
'badge_diamond'                    => 'Diamond',
'criteria_type'                    => 'Criteria Type',
'criteria_value'                   => 'Threshold Value',
'points_rewards'                   => 'Bonus Points on Earn',
'points_rewards_helper'            => 'Extra points added to donor when this badge is earned',
'display_order'                    => 'Display Order',
'earned_by_donors'                 => 'Earned By',
// Criteria type labels:
'criteria_donations'               => 'Total Donations',
'criteria_points'                  => 'Accumulated Points',
'criteria_critical_donations'      => 'Critical Blood Requests',
'criteria_rare_blood_type'         => 'Rare Blood Type',
'criteria_active_months'           => 'Active Months',
'criteria_active_years'            => 'Active Years',
'criteria_streak_no_cancel'        => 'No-Cancellation Streak',
'criteria_completion_rate_100'     => 'Perfect Completion Rate',
'criteria_response_time_fast'      => 'Fast Response Time (hours)',
'criteria_governorate_count'       => 'Governorates Reached',
'criteria_special_date'            => 'Special Date Donation',
```

---

## 14. Complete File Change Inventory

| File | Change Type | Purpose |
|---|---|---|
| `app/Models/Achievement.php` | **MODIFY** | Fix 3 fillable bugs; add new criteria constants; add `donorAchievements()` relationship |
| `app/Models/DonorAchievement.php` | **REWRITE** | Implement from scratch: SoftDeletes, fillable, casts, 3 relationships |
| `app/Models/Donor.php` | **MODIFY** | Add `points`, `level` to fillable; add `donorAchievements()` and `earnedAchievements()` |
| `app/Services/AchievementService.php` | **CREATE** | Full service: `awardDonationPoints`, `evaluateAndAward`, `award`, `recalculateLevel`, helpers |
| `app/Filament/Organization/.../ResponsesRelationManager.php` | **MODIFY (2 locations)** | Add achievement hook after `$healthProfile->save()` at lines ~571 and ~851 |
| `database/seeders/AchievementsSeeder.php` | **CREATE** | Phase 1 achievements with AR + EN translations and icon paths |
| `app/Console/Commands/BackfillAchievementsCommand.php` | **CREATE** | `achievements:backfill` command for existing donors |
| `app/Filament/Admin/Resources/Achievements/AchievementResource.php` | **CREATE** | Admin CRUD resource |
| `app/Filament/Admin/Resources/Achievements/Pages/ListAchievements.php` | **CREATE** | List page |
| `app/Filament/Admin/Resources/Achievements/Pages/CreateAchievement.php` | **CREATE** | Create page |
| `app/Filament/Admin/Resources/Achievements/Pages/EditAchievement.php` | **CREATE** | Edit page — no DeleteAction |
| `app/Filament/Admin/Resources/Achievements/Schemas/AchievementForm.php` | **CREATE** | Form with FileUpload for PNG icons |
| `app/Filament/Admin/Resources/Achievements/Tables/AchievementsTable.php` | **CREATE** | Table with ImageColumn |
| `app/Filament/Donor/Widgets/DonorLatestAchievementWidget.php` | **CREATE** | Dashboard widget: level, points, latest badge |
| `resources/views/filament/donor/widgets/donor-latest-achievement-widget.blade.php` | **CREATE** | Widget blade view |
| `app/Filament/Donor/Pages/Achievements.php` | **CREATE** | Full achievements page |
| `resources/views/filament/donor/pages/achievements.blade.php` | **CREATE** | Page blade view |
| `app/Filament/Donor/Pages/Dashboard.php` | **MODIFY** | Append `DonorLatestAchievementWidget` to `getWidgets()` |
| `app/Providers/Filament/DonorPanelProvider.php` | **MODIFY** | Register widget and Achievements page |
| `lang/en.json` | **MODIFY** | Add `filament.resources.achievements.*` keys |
| `lang/ar.json` | **MODIFY** | Add Arabic translations |
| `lang/en/donor.php` | **MODIFY** | Add achievement, points, level keys |
| `lang/ar/donor.php` | **MODIFY** | Add Arabic donor keys |
| `lang/en/admin.php` | **MODIFY** | Add achievement admin form keys |
| `lang/ar/admin.php` | **MODIFY** | Add Arabic admin keys |
| `storage/app/public/achievements/` | **CREATE (folder + files)** | Copy and rename all 33 PNG icons here |

---

## 15. Risks and Edge Cases

### 15.1 Critical Risks

**Transaction safety.** The `awardDonationPoints` call runs inside `DB::transaction`. Every call in `AchievementService` is wrapped in its own `try/catch` to ensure that any failure in achievement logic never rolls back a confirmed donation. Do not remove these try/catch blocks.

**`achievements` table has no `deleted_at`.** Do not add a `DeleteAction` to the admin resource. Do not add `SoftDeletes` to the `Achievement` model. Both require a migration first. If an admin tries to delete an achievement row without the column, it will throw a SQL error.

**Do not wire the `correct_blood_type` action.** The third action in `ResponsesRelationManager` only updates the verified blood type and does not increment `total_donations`. Adding the achievement hook there causes double point awards for donors who get their blood type corrected.

### 15.2 Edge Cases

| Scenario | Safe Behaviour |
|---|---|
| `award()` called twice for same donor + achievement | `firstOrCreate` checks existing before creating. `wasRecentlyCreated` flag ensures bonus points are only added once. |
| Correction flow called after points already awarded for same response | Impossible — `correction_used_at !== null` guard prevents the correction flow from running twice for the same `RequestResponse`. |
| Donor has no `healthProfile` | `ResponsesRelationManager` already guards with `if ($healthProfile)` — service is called inside that branch only. |
| First donation ever (cold-start donor) | After `total_donations++` and `$healthProfile->save()`, the service checks `total_donations >= 1` — awards "First Drop of Life" correctly. |
| Points-criteria achievement evaluated before base donation points are saved | `awardDonationPoints` increments points **first**, then calls `evaluateAndAward`. Order is guaranteed. |
| Backfill run on donor who already has some badges | `whereNotIn('id', $earnedIds)` in `evaluateAndAward` skips already-earned ones. `firstOrCreate` is a second safety net. |
| Icon PNG file deleted from storage after being assigned to an achievement | `Storage::url()` returns a URL to a non-existent file. The `<img>` will show a broken image. The `@if($ach->badge_icon)` + `@else` fallback in blade views shows the Heroicon instead. |
| Admin uploads a PNG icon over 512KB | Filament's `FileUpload` component with `->maxSize(512)` rejects it at the form level. |
| Arabic locale active when `getTranslation()` is called but Arabic key missing | `spatie/laravel-translatable` falls back to the other available locale. Always provide both `ar` and `en` in the seeder. |
| `criteria_type` changed on an existing achievement | Disabled in the edit form — field is `->disabled(fn($record) => $record !== null)`. Changing criteria via direct DB edit invalidates existing awards silently. |

### 15.3 Missing Pieces (Acknowledged Gaps)

- **Phase 2 and Phase 3 criteria** — The `meetsCriteria` method returns `false` for unimplemented criteria types. DB rows for these achievements can exist, but no donor will be auto-awarded until the handler is coded. This is intentional.
- **Response-time achievements** — Require the notification dispatch timestamp to be stored alongside the `RequestResponse`. The system currently does not store when a notification was sent per donor per request. This needs a data model addition before Phase 3 response-time criteria can be evaluated.
- **Special-date achievements (Ramadan, World Donor Day)** — Require Islamic calendar conversion for Ramadan detection. Recommend `khaled-alshamaa/ar-php` library. Not complex, but out of scope for Phase 1.
- **Notification to donor on badge earned** — No `AchievementEarnedNotification` exists. This is additive and non-blocking. When added, dispatch it **outside** the `DB::transaction` closure.
- **Admin manual award action** — The `awarded_by` column exists in `donor_achievements` to support admin-manually-awarding badges, but no action for it exists yet in the admin panel. Implement as a Filament `Action` on the donor view page in a future phase.

---

## 16. Build Order

Execute steps in this exact order. Each step depends on the previous.

### Step 1 — Copy and Verify Icon Files
```bash
mkdir -p storage/app/public/achievements
# Copy and rename all 33 PNG files using the English slug naming table
php artisan storage:link
# Verify: open /storage/achievements/first-drop.png in the browser
```

### Step 2 — Fix Model Bugs *(blocking — nothing else works without this)*
- Edit `app/Models/Achievement.php`: fix `$fillable`, add new criteria constants, add relationship
- Edit `app/Models/DonorAchievement.php`: implement completely from scratch
- Edit `app/Models/Donor.php`: add `points` and `level` to `$fillable`, add relationships
- **Verify in tinker:** `Achievement::create([...correct keys...])` — no exception

### Step 3 — Create AchievementService
- Create `app/Services/AchievementService.php` with all methods
- **Verify in tinker:** `app(AchievementService::class)->evaluateAndAward($donor)` — no error

### Step 4 — Wire Both Trigger Points
- Modify `ResponsesRelationManager.php` at lines ~571 and ~851
- **Verify:** Run a full org panel flow (scan QR → eligible outcome) → check `donors.points` incremented

### Step 5 — Create and Run Seeder
```bash
php artisan db:seed --class=AchievementsSeeder
# Verify: SELECT * FROM achievements — should have 9+ rows with correct JSON names and icon paths
```

### Step 6 — Run Backfill Command
```bash
php artisan achievements:backfill --dry-run  # preview first
php artisan achievements:backfill            # execute
# Verify: donors with 1+ donations now have DonorAchievement rows
```

### Step 7 — Create Admin Resource
- Create all files in `app/Filament/Admin/Resources/Achievements/`
- Add translation keys to `lang/en.json`, `lang/ar.json`, `lang/en/admin.php`, `lang/ar/admin.php`
- **Verify:** Open `/admin/achievements` — list shows seeded achievements with icons

### Step 8 — Create Donor Dashboard Widget
- Create `DonorLatestAchievementWidget.php` and its blade view
- Register in `Dashboard.php` and `DonorPanelProvider.php`
- **Verify:** Open `/donor` — widget shows level, points, latest badge with PNG icon

### Step 9 — Create Donor Achievements Page
- Create `Achievements.php` page and its blade view
- Register in `DonorPanelProvider.php`
- Add all donor translation keys to `lang/en/donor.php` and `lang/ar/donor.php`
- **Verify:** Open `/donor/achievements` — earned grid shows PNG icons, locked badges show progress bars, descriptions explain how to earn

### Step 10 — Test RTL and Dark Mode
- Switch locale to Arabic → all text renders in Arabic, layout is mirrored correctly
- Switch to dark mode → all `.dark .` CSS classes apply, no white-box artifacts on card backgrounds

---

*End of document. Version 1.0 — April 2026.*
