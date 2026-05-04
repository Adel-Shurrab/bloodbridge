# BloodBridge — Project Memory & Feature Plans
**Authoritative project context for all future work in this conversation.**
*Generated from full SRS reading + deep codebase reverse-engineering. April 2026.*

---

## PART 1: PROJECT MEMORY

---

### 1.1 Project Purpose & Scope....

BloodBridge is a **web-based blood donation management platform** built for the **Gaza Strip, Palestine**. It replaces informal WhatsApp/telephone coordination between hospitals and donors with an automated, intelligent end-to-end system.

**Core value proposition:** Match eligible, geographically proximate, behaviorally-scored blood donors to urgent hospital requests — automatically, in real time, in Arabic and English.

**Stack:** Laravel 12 · PHP 8.3 · Filament v4 · MySQL/MariaDB · Laravel Reverb (WebSocket) · Python FastAPI · XGBoost · Vite · Tailwind CSS · PestPHP

---

### 1.2 User Roles & Permissions

There are **three roles**, stored as `UserRole` enum (int-backed) in `users.role`:

| Role | Value | Panel Path | Access Gate |
|------|-------|-----------|-------------|
| `DONOR` | 1 | `/donor` | role === DONOR |
| `ORGANIZATION` | 2 | `/org/{slug}` | role === ORGANIZATION + approved |
| `ADMIN` | 3 | `/admin` | role === ADMIN |

**Access enforcement:** `User::canAccessPanel(Panel $panel)` — checks `is_active` first (inactive = blocked from all panels), then matches panel ID to role. Organization panel uses Filament tenancy; the tenant is the `Organization` model resolved via `slug`.

**Key constraints:**
- Donors with `chronic_disease = true` are routed to an `IneligibleDonor` page on login, blocking all features.
- Organizations with `approval_status = PENDING` are routed to `PendingApproval` page.
- Email verification is required for Donor panel access (`MustVerifyEmail`).
- `is_active = false` blocks all panels for any role.

**Public (guest) access:** Home, About, Contact, Eligibility, Terms pages — no auth required. Located at locale-prefixed URLs (`/ar/`, `/en/`).

---

### 1.3 Main Modules & Features

#### A. Authentication & Registration
- **Two registration flows**: Donor registration (`/register/donor`) and Organization registration (`/register/organization`) — both are custom Blade forms with separate controllers, NOT Filament.
- Donor registration creates: `User` + `Donor` + `DonorHealthProfile` in a single DB transaction.
- Organization registration creates: `User` + `Organization`. Status starts as `PENDING`.
- Standard Laravel auth (login, password reset, email verification) using custom notification classes (`CustomVerifyEmail`, `CustomResetPassword`).

#### B. Blood Request Lifecycle
Status progression: `PENDING → BROADCASTED → EXPIRED/FULFILLED`

1. **PENDING:** Created by organization, not yet broadcast.
2. **BROADCASTED:** Broadcast triggered; donors notified.
3. **EXPIRED:** Auto-expired after 48h by `ExpireOldBloodRequests` command.
4. **FULFILLED:** Auto-set when `completed_responses_count >= units_needed`.

`softDeletes` enabled. `actual_search_radius_km` stores the final radius used after expansion.

#### C. Donor Discovery (Progressive Radius Expansion)
Handled by `BloodRequestBroadcastService::findEligibleDonorsWithExpansion()`:
- Initial radius = `search_radius_km` (×3 for CRITICAL)
- Expands by 5 km per iteration, max 25 km
- Target count = `units_needed × 2.0` (NORMAL) or `× 2.5` (CRITICAL)
- Fallback: donors with `UNKNOWN` blood type included for NORMAL requests when target not met
- Bounding-box pre-filter + Haversine SQL for performance
- Governorate-based fallback when GPS coords absent

#### D. Four-Level Scoring Waterfall
Managed by `DonorScoringService::getScoreResults()`:

| Level | Source | Condition |
|-------|--------|-----------|
| 1 | `donor_predictive_scores` DB cache | Score fresher than `score_staleness_days` |
| 2 | FastAPI XGBoost | `ml_scoring_enabled = true` AND circuit breaker closed |
| 3 | Rule-based PHP | `score = (acceptance_rate × 0.50) + (recency_score × 0.30) + (loyalty_score × 0.20)` with no-show penalty |
| 4 | Neutral 0.5 | Fallback for cold-start donors |

**No-show penalty:** `adjusted_total = total_responses + no_show_count` in denominator.

**Epsilon-greedy split:** `exploration_ratio` (default 0.20) → bottom epsilon% + cold-start = explorers; rest = exploiters. Budget: `max_notifications_per_broadcast` (×1.5 for CRITICAL). Backfill logic fills remaining slots.

#### E. FastAPI Circuit Breaker
`FastApiCircuitBreaker` uses file-cache (3 keys): `fastapi_circuit:state`, `:failures`, `:opened_at`.
States: `closed → open` (after `circuit_breaker_failure_threshold` = 3 failures) → `half_open` (after `circuit_breaker_recovery_seconds` = 120s) → `closed` on success.
**Critical note:** File-cache storage means **circuit breaker is not multi-server safe**. Single server only.

#### F. QR Code Verification Flow
1. Donor accepts → `BloodRequestActionService::accept()` creates `RequestResponse` (status=PENDING), calls `QRCodeService::generate()` → 32-char hex token stored in `verification_qr_code`, expires in 7 days.
2. Donor downloads SVG QR from Donor panel.
3. Organization scans QR via `ScanDonorQR` page → rate-limited 30/min/org → `QRCodeService::validate()` checks token, expiry, org ownership, request status.
4. On valid scan: `confirmAdmission()` sets status → `ACCEPTED`, records `verified_at`.
5. Organization then does lab work → in `ResponsesRelationManager`, sets status to `COMPLETED` (donated) or `DECLINED` (medically excluded).
6. COMPLETED: increments `donor_health_profiles.total_donations`, updates `last_donation_date`, saves health profile, may auto-fulfill the blood request.
7. QR revoked on donor cancellation or request expiry.

#### G. Notification System
- `BloodRequestMatchNotification` — sent via `database` + `broadcast` (Reverb WebSocket) channels.
- Dispatched from `DispatchBloodRequestNotifications` job in batches of 100.
- Each job re-validates eligibility before sending.
- Per-user locale applied via `NotificationService::send()` using `$notification->locale($recipientLocale)`.
- Other notifications: `DonorResponseNotification`, `DonorIneligibilityNotification`, `ResponseNotNeededNotification`, `SystemAnnouncement`.

#### H. Scheduled Commands

| Command | Schedule | Purpose |
|---------|----------|---------|
| `blood:cleanup-stale-responses` | Hourly | PENDING → UNREACHABLE (8h CRITICAL, 48h NORMAL) |
| `blood-requests:expire` | Twice daily | PENDING/BROADCASTED → EXPIRED after 48h |
| `scoring:decay-epsilon` | Weekly | Reduce exploration ratio over time |

#### I. Bilingual Support (AR + EN)
- All public routes prefixed via `mcamara/laravel-localization` (`/ar/`, `/en/`).
- Model translatable fields use `spatie/laravel-translatable` — stored as `{"ar": "...", "en": "..."}` JSON.
- `users.locale` stores preference; `User::preferredLocale()` returns it.
- `SyncUserLocale` middleware syncs app locale to user preference.
- Translation files: `lang/ar/*.php` and `lang/ar.json` (and `en`).
- `GeneralSettings` has a custom `TranslatableArrayCast` for CMS-driven translatable content.

#### J. Settings System
Two settings classes backed by `settings` table via `spatie/laravel-settings`:
- `GeneralSettings` — site content, contact info, social links, SEO, map defaults, eligibility thresholds, org limits, all CMS-driven page content (hero titles, features, team members, etc.)
- `ScoringSettings` — ML toggle, epsilon ratio, notification budget, staleness window, circuit breaker config.

Both editable at runtime via Admin panel pages.

#### K. Public Pages (Guest)
Blade views in `resources/views/pages/`: `home.blade.php`, `about.blade.php`, `contact.blade.php`, `eligibility.blade.php`, `terms.blade.php`. Layout: `resources/views/layouts/public.blade.php`. CSS loaded from `public/assets/styles/pages/*.css` (NOT Vite/Tailwind — custom raw CSS). Font: Cairo (Google Fonts). JS in `public/assets/scripts/pages/*.js`.

---

### 1.4 Database Structure & Key Relationships

#### Core Tables

```
users               id, name, email, phone, password, role (tinyint), locale, is_active, email_verified_at, softDeletes
donors              id, user_id(FK), governorate_id(FK), national_id(9), gender, birth_date, auto_location_address, lat, lng, points, level, softDeletes
organizations       id, user_id(FK), governorate_id(FK), org_name(JSON), slug, description(JSON), license_number, lat, lng, working_days(JSON), daily_capacity, approval_status(tinyint), approved_by(FK), softDeletes
donor_health_profiles  id, donor_id(FK), weight, height, chronic_disease, recent_donation, infection, is_eligible(bool), has_recent_surgery, surgery_date, next_eligible_date, last_donation_date, blood_type(tinyint), verified_blood_type(tinyint), verified_by_organization_id(FK), verified_at, total_donations, softDeletes
blood_requests      id, organization_id(FK), blood_type(tinyint), units_needed, urgency_level(tinyint), additional_notes(JSON), search_radius_km, actual_search_radius_km, lat, lng, location_address(JSON), status(tinyint), broadcasted_at, fulfilled_at, softDeletes
request_responses   id, blood_request_id(FK), donor_id(FK), status(tinyint), responded_at, decline_reason(JSON), verification_qr_code(unique), qr_code_expires_at, verified_at, correction_used_at, appointment_id(FK), softDeletes  UNIQUE(donor_id, blood_request_id)
governorates        id, name(JSON)  [5 rows: Gaza, Khan Younis, North Gaza, Deir al-Balah, Rafah]
```

#### Achievement-Related Tables (partially implemented)

```
achievements        id, name(JSON), description(JSON), points_rewards(int), badge_icon(string), badge_type(string), criteria_type(string), criteria_value(int), display_order(int)
donor_achievements  id, donor_id(FK), achievement_id(FK), earned_at(timestamp), meta(JSON), awarded_by(FK users), softDeletes  UNIQUE(donor_id, achievement_id)
```

⚠️ **Known mismatches between migration and Achievement model:**
- Migration column: `badge_icon` — Model fillable: `badge_image` **(mismatch — bug)**
- Migration column: `points_rewards` — Model constant: `DEFAULT_POINTS_REWARDS`, fillable: `points_reward` **(mismatch — bug)**

#### Supporting Tables

```
appointments            id, organization_id, donor_id, blood_request_id, appointment_date, status, cancellation_reason(JSON), completed_at, cancelled_at, softDeletes
donor_predictive_scores donor_id(PK), acceptance_probability(float), data_points_count, computed_at, model_version
model_training_logs     model_version(PK), training_date, data_records_used, algorithm, hyperparameters(JSON), metrics(JSON), feature_importance(JSON)
eligibility_logs        id, donor_id, organization_id, check_type, is_eligible, is_permanent, rejection_reason, answers_snapshot
contact_messages        id, name, email, subject, message, read_at
announcements           id, title(JSON), content(JSON), type, is_active, etc.
notifications           (Laravel standard — id, type, notifiable_type, notifiable_id, data(JSON), read_at)
settings                id, group, name, locked, payload(JSON)  [spatie/laravel-settings backing]
jobs / failed_jobs      (Laravel queue backing)
```

#### Key Relationships

```
User hasOne Donor
User hasOne Organization
Donor hasOne DonorHealthProfile
Donor hasMany RequestResponse
Donor hasMany EligibilityLog
Organization hasMany BloodRequest
BloodRequest hasMany RequestResponse
RequestResponse belongsTo Donor
RequestResponse belongsTo BloodRequest
```

---

### 1.5 Request Flow & Data Flow

#### Blood Request Broadcast Flow (complete):
```
1. Org creates BloodRequest (PENDING) via Filament form
2. Org triggers broadcast (button action in BloodRequestResource)
3. BloodRequestBroadcastService::broadcast() called
   a. hasValidLocation() check
   b. DB::transaction:
      - findEligibleDonorsWithExpansion() → Haversine SQL
      - updateBroadcastStatus() → BROADCASTED + broadcasted_at
   c. donorScoringService->scoreAndSelect() → waterfall scoring + epsilon-greedy selection
   d. notifyEligibleDonors() → DispatchBloodRequestNotifications::dispatchBatches()
4. Queue worker processes job batches:
   - Re-validates eligibility per donor
   - NotificationService::send() per donor (database + broadcast channels)
5. Donors receive real-time notification via WebSocket (Reverb)
```

#### Donor Accept → Donation Complete Flow:
```
1. Donor sees request in /donor blood requests page
2. Donor clicks Accept → BloodRequestActionService::accept()
   - DB::lockForUpdate() on both request + donor
   - Creates RequestResponse (status=PENDING)
   - QRCodeService::generate() → 32-char hex token
   - Notifies org via DonorResponseNotification
3. Donor downloads QR SVG from panel
4. Donor arrives at org; org scans QR → ScanDonorQR::verifyQRCode()
   - Rate limit check (30/min)
   - QRCodeService::validate()
   - confirmAdmission() → status = ACCEPTED, verified_at = now()
5. Org performs lab check → ResponsesRelationManager action
   - If eligible: status = COMPLETED, total_donations++, last_donation_date = now()
   - If medically excluded: status = DECLINED
   - Updates health profile, creates EligibilityLog
   - If completedCount >= units_needed → BloodRequest status = FULFILLED
```

---

### 1.6 Filament Structure

#### Admin Panel (`/admin`)
**Resources:** Users, Donors, Organizations, BloodRequests (view-only), Announcements, ContactMessages
**Pages:** Dashboard, Statistics, DonorScoringSimulation, ManageGeneralSettings, ManageScoringSettings
**Widgets (Dashboard):** DashboardHeaderWidget, StatsOverview, PendingOrganizationsWidget
**Widgets (Statistics page):** AdvancedStatsOverview, RecentActivityWidget, BloodTypeDemandWidget, EngagementChartWidget, MLScoringMonitorWidget

#### Donor Panel (`/donor`)
**Resources:** None (no separate CRUD resources)
**Pages:** Dashboard, BloodRequests, History, EditProfile, ChangePassword, IneligibleDonor
**Widgets (Dashboard):** EligibilityCountdownWidget, DonorHeaderWidget, DonorStatsOverviewWidget

#### Organization Panel (`/org/{slug}`)
**Resources:** BloodRequests (full CRUD + responses relation manager)
**Pages:** Dashboard, ScanDonorQR, Statistics, EditOrganizationProfile, ChangePassword, PendingApproval
**Widgets (Dashboard):** OrganizationHeaderWidget, BloodRequestStatsWidget + Statistics sub-widgets

#### Filament Pattern Conventions
- Each resource has sub-folders: `Pages/`, `Schemas/` (Form + Infolist), `Tables/`, `RelationManagers/`
- Schemas are separated from Resource class (e.g., `BloodRequestForm.php`, `BloodRequestInfolist.php`)
- `SpatieTranslatablePlugin` active in all three panels for bilingual field editing
- `dotswan/filament-map-picker` used for GPS coordinate input
- `HasActiveLocaleSwitcher` trait for inline locale switching in donor panel pages

---

### 1.7 Frontend Structure (Guest/Public)

- **Layout:** `resources/views/layouts/public.blade.php` — full HTML shell, Cairo font, Font Awesome 6.5, custom CSS links, Vite assets.
- **Components:** `resources/views/components/` — `navbar.blade.php`, `footer.blade.php`, `modal.blade.php`, `eligibility-modal.blade.php`, `privacy-modal.blade.php`.
- **Page views:** `resources/views/pages/` — each page links its own CSS: `public/assets/styles/pages/home.css`, `about.css`, `contact.css`, etc.
- **Main CSS:** `public/assets/styles/main.css` — defines `.btn`, `.btn-primary`, `.btn-outline`, color variables, layout utilities.
- **Color scheme:** Primary red `#d32f2f` / `#DC143C`. Secondary blue `#0063a0`. Neutral grays. Background near-white.
- **Font:** Cairo (Google Fonts, weights 400/600/700/800). Used everywhere including Filament panels.
- **JS:** Per-page vanilla JS in `public/assets/scripts/pages/*.js`. `main.js` handles navbar scroll effects, smooth scroll, intersection observers for animations.
- **RTL/LTR:** `lang` and `dir` attributes set on `<html>` from `app()->getLocale()` and `LaravelLocalization::getCurrentLocaleDirection()`.
- **Content driven by:** `GeneralSettings` — all text, images, features, team members, etc. come from admin-configurable settings.

---

### 1.8 Business Rules (Non-Negotiable)

1. **Eligibility auto-calculation:** `DonorHealthProfile::calculateEligibility()` fires on every `saving` event. `is_eligible` and `next_eligible_date` are NEVER set manually.
2. **Chronic disease = permanent ban:** `chronic_disease = true` means `is_eligible = false` with no `next_eligible_date`. Registration blocks chronic disease donors via validation.
3. **Donation cooldown:** 90 days minimum (not 56 days from `GeneralSettings::min_days_between_donations` — the eligibility calc uses 90 days hardcoded).
4. **Surgery cooldown:** 28 days.
5. **Infection cooldown:** 14 days from active infection.
6. **Weight/height thresholds:** <50 kg or <140 cm = temporarily ineligible (no next_eligible_date computed for these).
7. **One active response per donor:** A donor cannot accept two requests simultaneously. Enforced with `lockForUpdate` in `BloodRequestActionService::accept()`.
8. **QR token validity:** 7 days from `responded_at`. Revoked on cancellation.
9. **Organization daily request limit:** `GeneralSettings::org_max_requests_per_day` (default 5).
10. **Notification cooldown:** 2 hours (NORMAL) / 30 minutes (CRITICAL) — donors recently notified are excluded.
11. **Epsilon decay:** Automatic weekly decay from 20% to 5% over 60 days after ML activation.
12. **UNKNOWN blood type:** Valid donor type, serves as fallback category; excluded from `getCompatibleDonorTypes()` return.

---

### 1.9 Naming Conventions & Coding Patterns

**Models:**
- All use Eloquent ORM. Soft deletes on all core entities.
- `DEFAULT_*` constants on models for documentation of defaults.
- Translatable fields via `HasTranslations` use `public array $translatable = [...]`.
- Enums are int-backed, implement `HasLabel` + `HasColor` for Filament auto-rendering.

**Services:**
- Stateless PHP classes, injected via constructor DI.
- No business logic in controllers or Filament pages — delegated to services.
- `BloodRequestBroadcastService`, `DonorScoringService`, `QRCodeService`, `BloodRequestActionService`, `NotificationService`, `FastApiCircuitBreaker`.

**Jobs:**
- `ShouldQueue` + `Queueable`. DB queue driver.
- `DispatchBloodRequestNotifications::dispatchBatches()` static method for chunked dispatch.

**Settings:**
- `App\Settings\GeneralSettings` and `ScoringSettings` extend `Spatie\LaravelSettings\Settings`.
- Read anywhere via `app(GeneralSettings::class)`.

**Filament Resources:**
- Schema/Form/Table separated into `Schemas/` and `Tables/` sub-namespaces.
- RelationManagers in `RelationManagers/` sub-namespace.
- No ViewResource page for most entities — use `ViewBloodRequest` etc.

**Routes:**
- All public routes inside `LaravelLocalization::setLocale()` group.
- Email verification route outside localization group (signed URL stability).
- Filament panels auto-register their routes.

**Tests:**
- PestPHP. Tests in `tests/Feature/Auth/` (standard Laravel auth tests). Minimal coverage otherwise.

---

### 1.10 Limitations, Gaps & Technical Risks

**Schema Bugs:**
- `Achievement` model has `badge_image` in fillable but migration creates column `badge_icon`. **Will cause silent column mismatch.**
- `Achievement` model has `points_reward` (singular) in fillable but migration creates `points_rewards` (plural). **Same issue.**
- `DonorAchievement` model is completely empty — no `$fillable`, no `$casts`, no relationships whatsoever.
- `Donor` model does NOT have `points` and `level` in `$fillable` despite columns existing in migration.

**Unimplemented Features (schema exists, code doesn't):**
- `Appointment` model exists with schema, but no Filament resource, no workflow, no service. Dead code.
- `Achievement` and `DonorAchievement` — tables exist, models are stubs, zero logic.
- `donors.points` and `donors.level` — columns exist, never updated.
- `DonorPredictiveScore.data_points_count` — always set to 0.

**Technical Risks:**
- **Circuit breaker file cache**: Not multi-server safe. Must migrate to Redis for horizontal scaling.
- **No Redis**: Queue driver and circuit breaker both use database/file store. Production-grade deployment needs Redis.
- **Synthetic ML training data**: XGBoost trained on 500 synthetic records. Real-world accuracy untested.
- **Eligibility discrepancy**: `GeneralSettings::min_days_between_donations = 56` but `calculateEligibility()` hardcodes 90 days. These are inconsistent. GeneralSettings value is shown on the eligibility checker page but NOT used in actual calculation.
- **Donor model fillable gap**: `points` and `level` columns are not in `$fillable`. Mass assignment would fail.
- **No test coverage** for scoring waterfall, circuit breaker, epsilon selection, eligibility logic.
- **`BloodRequestStatus::FULFILLED`** referenced in `ResponsesRelationManager` but not defined in `BloodRequestStatus` enum. This will cause a runtime error. (Likely `EXPIRED` is the catch-all — needs verification.)
- **Behavioral metrics table dropped**: `donor_behavioral_metrics` was created then dropped in `2026_04_02_000000_drop_donor_behavioral_metrics_table.php`. Code may still reference it.

---

## PART 2: FEATURE PLANS

---

## Feature A: Donor Achievements System

### Overview & Goal
Activate the already-scaffolded but completely unimplemented achievements system. Donors earn achievements automatically based on donation behavior. This improves long-term engagement, gamifies the donor experience, and exposes a meaningful progression layer on the Donor panel.

---

### 2A.1 Current State Audit

**What exists (tables/schema):**
```sql
achievements (id, name JSON, description JSON, points_rewards int, badge_icon string, badge_type string, criteria_type string, criteria_value int, display_order int)
donor_achievements (id, donor_id FK, achievement_id FK, earned_at, meta JSON, awarded_by FK, softDeletes, UNIQUE donor_id+achievement_id)
donors (points int default 0, level int default 1)  -- columns exist but never used
```

**What exists (models — barely):**
- `Achievement` model: has `HasTranslations`, `CRITERIA_DONATIONS` and `CRITERIA_POINTS` constants, translatable `name`+`description`, partial fillable (with bugs).
- `DonorAchievement` model: completely empty class body.

**What doesn't exist:**
- No relationships between models
- No service to check/award achievements
- No admin resource to manage achievements
- No donor-facing display
- No trigger anywhere that calls achievement logic

**Schema bugs to fix first:**
- `badge_image` → rename to `badge_icon` in model fillable (or add migration to rename column to `badge_image` — preferably fix the MODEL to match the COLUMN)
- `points_reward` → fix to `points_rewards` in model fillable
- Add `points` and `level` to `Donor::$fillable`

---

### 2A.2 Achievement Criteria Design

Based on `Achievement::CRITERIA_LIST = ['donations', 'points']` and the existing schema, expand to the following criteria types:

| `criteria_type` | `criteria_value` | Meaning |
|-----------------|-----------------|---------|
| `donations` | N | Total confirmed donations (COMPLETED responses) |
| `points` | N | Total donor points accumulated |
| `streak` | N | N consecutive requests responded to (accepted, not ignored/no-show) |
| `blood_type` | blood_type_int | First verified donation of specific blood type |

**Note:** Start with only `donations` and `points` since those match existing constants. Add `streak` as future extension.

**Seed data — suggested initial achievements:**

| Name | criteria_type | criteria_value | points_rewards | badge_type |
|------|--------------|----------------|----------------|------------|
| First Drop | donations | 1 | 50 | bronze |
| Blood Hero | donations | 5 | 150 | silver |
| Life Saver | donations | 10 | 300 | gold |
| Guardian Angel | donations | 25 | 750 | platinum |
| Century Donor | donations | 100 | 2000 | diamond |
| Point Collector | points | 100 | 0 | bronze |
| Point Champion | points | 500 | 0 | silver |
| Point Legend | points | 1000 | 0 | gold |

---

### 2A.3 Points System Design

Points are awarded when a donation is `COMPLETED`. Points come from:
- Base donation: **50 points** per completed donation
- Urgency bonus: +**25 points** for CRITICAL requests
- Plus `achievements.points_rewards` when an achievement is earned

`donors.level` is calculated from `donors.points`:

| Level | Points Required |
|-------|----------------|
| 1 | 0 |
| 2 | 100 |
| 3 | 300 |
| 4 | 600 |
| 5 | 1000 |

**Level calculation:** `floor(sqrt(points / 25))` or a tiered match — simple and re-computable at any time.

---

### 2A.4 Database Changes

**Migration 1: Fix Achievement model column alignment**
```php
// No column changes needed — fix the MODEL to match the existing columns
// badge_icon (correct in DB), points_rewards (correct in DB)
```

**Migration 2: Add points tracking to donors (already exists, just unused)**
```php
// columns donors.points and donors.level already exist
// Just add to Donor::$fillable
```

**No new migrations needed** — the schema is already sufficient. Only model and logic changes are required.

---

### 2A.5 Files to Create / Modify

#### Fix Model Bugs (Priority 0)

**`app/Models/Achievement.php`** — fix fillable:
```php
protected $fillable = [
    'name',
    'description',
    'criteria_type',
    'criteria_value',
    'badge_icon',        // was badge_image (wrong)
    'points_rewards',    // was points_reward (wrong)
    'badge_type',        // was missing
    'display_order',
];

// Add relationships:
public function donorAchievements()
{
    return $this->hasMany(DonorAchievement::class);
}
```

**`app/Models/DonorAchievement.php`** — fill it in:
```php
protected $fillable = ['donor_id', 'achievement_id', 'earned_at', 'meta', 'awarded_by'];
protected $casts = ['earned_at' => 'datetime', 'meta' => 'array'];

public function donor() { return $this->belongsTo(Donor::class); }
public function achievement() { return $this->belongsTo(Achievement::class); }
public function awardedByUser() { return $this->belongsTo(User::class, 'awarded_by'); }
```

**`app/Models/Donor.php`** — add `points` and `level` to fillable, add relationships:
```php
protected $fillable = [
    // ... existing ...
    'points',
    'level',
];

public function achievements() { return $this->hasMany(DonorAchievement::class); }
public function earnedAchievements() {
    return $this->belongsToMany(Achievement::class, 'donor_achievements')
        ->withPivot(['earned_at', 'meta', 'awarded_by'])
        ->withTimestamps();
}
```

#### New Service

**`app/Services/AchievementService.php`**
```php
class AchievementService
{
    public function evaluateAndAward(Donor $donor): array
    {
        // Load all achievements not yet earned by this donor
        $earnedIds = $donor->achievements()->pluck('achievement_id');
        $pending = Achievement::whereNotIn('id', $earnedIds)->get();

        $newlyAwarded = [];

        foreach ($pending as $achievement) {
            if ($this->meetsСriteria($donor, $achievement)) {
                $this->award($donor, $achievement);
                $newlyAwarded[] = $achievement;
            }
        }

        // Recalculate level after awards
        $this->recalculateLevel($donor);

        return $newlyAwarded;
    }

    private function meetsCriteria(Donor $donor, Achievement $achievement): bool
    {
        return match ($achievement->criteria_type) {
            'donations' => ($donor->healthProfile->total_donations ?? 0) >= $achievement->criteria_value,
            'points'    => $donor->points >= $achievement->criteria_value,
            default     => false,
        };
    }

    private function award(Donor $donor, Achievement $achievement, ?int $awardedBy = null): void
    {
        DonorAchievement::create([
            'donor_id'       => $donor->id,
            'achievement_id' => $achievement->id,
            'earned_at'      => now(),
            'awarded_by'     => $awardedBy,
        ]);

        // Add achievement's points reward to donor
        if ($achievement->points_rewards > 0) {
            $donor->increment('points', $achievement->points_rewards);
        }

        // Notify donor
        // $donor->user->notify(new AchievementEarnedNotification($achievement));
    }

    public function awardDonationPoints(Donor $donor, BloodRequest $bloodRequest): void
    {
        $points = 50; // base
        if ($bloodRequest->urgency_level === UrgencyLevel::CRITICAL) {
            $points += 25;
        }
        $donor->increment('points', $points);
        $this->evaluateAndAward($donor);
    }

    private function recalculateLevel(Donor $donor): void
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
}
```

#### Integration Point

**`app/Filament/Organization/Resources/BloodRequests/RelationManagers/ResponsesRelationManager.php`**

In the COMPLETED action (where `$healthProfile->total_donations++` happens), after `$healthProfile->save()`, add:

```php
// After $healthProfile->save():
$awardService = app(\App\Services\AchievementService::class);
$awardService->awardDonationPoints($record->donor, $record->bloodRequest);
```

This is the **only trigger needed**. The `COMPLETED` status is set exactly once per verified donation. This is deterministic and safe.

#### Admin Filament Resource

**`app/Filament/Admin/Resources/Achievements/AchievementResource.php`**

Standard Filament resource with:
- Table columns: name (translated), criteria_type badge, criteria_value, points_rewards, badge_type badge, display_order
- Form: TextInput name (translatable), Textarea description (translatable), Select criteria_type, TextInput criteria_value, TextInput points_rewards, Select badge_type, TextInput display_order, FileUpload badge_icon
- No delete (soft delete not available — achievements table has no softDeletes, add if needed)

#### Donor Panel Display

**`app/Filament/Donor/Pages/Dashboard.php`** — add `DonorAchievementsWidget` to widget list.

**New widget `app/Filament/Donor/Widgets/DonorAchievementsWidget.php`** — displays:
- Current level badge + points
- Grid of earned achievements (icon + name + earned_at)
- Locked upcoming achievements (blurred/grayed with progress bar)

**`resources/views/filament/donor/widgets/donor-achievements-widget.blade.php`** — custom Blade view.

---

### 2A.6 Logic Flow (Complete)

```
Org confirms donation → COMPLETED set in ResponsesRelationManager
    ↓
healthProfile.total_donations++, last_donation_date set, healthProfile.save()
    ↓
AchievementService::awardDonationPoints(donor, bloodRequest)
    ↓
donor.points += 50 (+ 25 if CRITICAL)
    ↓
AchievementService::evaluateAndAward(donor)
    ↓
Load all unearned achievements
For each: meetsCriteria(donor, achievement)?
  donations: total_donations >= criteria_value?
  points: donor.points >= criteria_value?
    ↓ yes
DonorAchievement::create(...)
donor.points += achievement.points_rewards
    ↓
recalculateLevel(donor) → update donors.level
    ↓ (future)
AchievementEarnedNotification → donor user
```

---

### 2A.7 Edge Cases & Risks

- **Idempotency:** `donor_achievements` has `UNIQUE(donor_id, achievement_id)` — double-awarding safely fails. Use `firstOrCreate` in award method to avoid exception noise.
- **Points and achievements on existing donors:** Run a backfill Artisan command (`php artisan achievements:backfill`) that evaluates all existing donors on first deployment. This is essential since current donors have donations but no points/achievements.
- **The `correction_used_at` column:** `request_responses` has a `correction_used_at` field (added in `2026_04_16` migration) suggesting a correction flow. If a COMPLETED status is later corrected backward, points/achievements should NOT be removed (complexity not worth it for MVP — just note it).
- **Admin manual award:** `donor_achievements.awarded_by` FK supports admin-manually-awarding badges. The Admin panel resource can include an action to award arbitrary achievements to donors.
- **Performance:** `evaluateAndAward` fires inside the already-open DB transaction in ResponsesRelationManager. Keep it lightweight — no external calls. The current implementation does only DB reads/writes which is fine.

---

### 2A.8 Step-by-Step Implementation Plan

1. **Fix model bugs** (30 min): Update `Achievement::$fillable` (badge_icon, points_rewards), fully implement `DonorAchievement` model, add `points`+`level` to `Donor::$fillable`, add relationships.
2. **Create seed data** (30 min): `AchievementsSeeder` with the 8 achievements above.
3. **Create `AchievementService`** (2 hours): Full implementation including `awardDonationPoints`, `evaluateAndAward`, `award`, `recalculateLevel`.
4. **Wire trigger** (30 min): Single line in `ResponsesRelationManager` COMPLETED flow.
5. **Admin resource** (2 hours): `AchievementResource` with CRUD. Optional: add relation tab on `DonorResource` showing earned achievements.
6. **Donor panel widget** (2 hours): `DonorAchievementsWidget` showing level, points, earned badges, progress toward next.
7. **Backfill command** (1 hour): `php artisan achievements:backfill` that processes all donors.
8. **Notification** (1 hour, optional): `AchievementEarnedNotification` sent to donor user on new achievement.

**Total estimate: ~9 hours**

---

## Feature B: Modernize Guest Pages

### Overview & Goal
Redesign the Home, About, and Contact guest pages to be visually striking, modern, and professional — while preserving the bilingual (AR/EN) CMS-driven content system, the `public.blade.php` layout structure, the navbar/footer components, and complete RTL support.

---

### 2B.1 Current State Audit

**What exists:**
- Custom raw CSS per page in `public/assets/styles/pages/` (no Tailwind, no framework)
- Very basic card-based layout with flat colors
- Cairo font (good — keep it)
- Minimal animations (float keyframe, hover translateY)
- JS per page for tabs, mobile menu
- All content driven by `GeneralSettings` — this MUST remain intact

**What it lacks:**
- Depth and visual hierarchy (flat, plain backgrounds)
- Scroll-triggered reveal animations
- 3D/perspective effects
- Strong typographic contrast
- Modern glassmorphism or layered card effects
- Engaging micro-interactions
- A distinctive visual identity beyond "basic red and white"

**Hard constraints:**
- Cannot break bilingual (`lang`, `dir` switching)
- All content variables (from `GeneralSettings`) must remain where they are
- Blade template structure (`@push('styles')`, `x-layout`, etc.) must stay
- RTL CSS must work (`dir="rtl"` on `<html>` for Arabic)
- No build step change — CSS files remain in `public/assets/styles/`
- No jQuery or heavy libraries — vanilla JS only

---

### 2B.2 Design Direction

**Aesthetic:** "Humanitarian Urgency meets Digital Sophistication." Think: warm crimson red (#C41E3A), deep charcoal navy (#0D1B2A), pure white, with gold accent (#FFB800) for highlights. Glass-morphism panels. Smooth 3D card tilts on hover. Particle-like background elements suggesting blood cells. Bold, editorial typography with large numerals for stats.

**Key design decisions:**
- **Hero section:** Full-screen with a subtle animated SVG particle background (CSS-only blood cell shapes orbiting). Two-column split layout preserved. Stats with animated counter (IntersectionObserver).
- **Feature cards:** Glass-morphism effect (`backdrop-filter: blur()`), subtle 3D tilt on mousemove (JavaScript).
- **Section transitions:** Smooth scroll-reveal with staggered delays using IntersectionObserver.
- **Color system:** CSS custom properties on `:root` for full theming consistency.
- **Typography scale:** Large display heading (clamp-based responsive), strong weight contrast between heading and body.
- **Buttons:** Gradient fill (`linear-gradient(135deg, #C41E3A, #8B0000)`) with glowing box-shadow on hover.

---

### 2B.3 Files to Create / Modify

**Primary changes (CSS files — full rewrites):**
- `public/assets/styles/main.css` — update color variables, button styles, global typography
- `public/assets/styles/pages/index.css` — full redesign of home page
- `public/assets/styles/pages/about.css` — full redesign of about page
- `public/assets/styles/pages/contact.css` — full redesign of contact page

**Primary changes (JS — partial updates):**
- `public/assets/scripts/main.js` — add scroll reveal logic, stat counter animation, 3D card tilt
- `public/assets/scripts/pages/index.js` — add particle background (CSS-only or minimal canvas), counter animation

**Blade templates (minimal changes — content structure preserved):**
- `resources/views/pages/home.blade.php` — add `data-reveal` attributes, adjust class names to match new CSS
- `resources/views/pages/about.blade.php` — same pattern
- `resources/views/pages/contact.blade.php` — same pattern
- `resources/views/components/navbar.blade.php` — frosted glass effect on scroll
- `resources/views/components/footer.blade.php` — redesign footer layout

**No Blade logic changes** — content variables all stay exactly the same.

---

### 2B.4 CSS Architecture (New System)

New `:root` CSS variables in `main.css`:

```css
:root {
  /* Brand */
  --color-primary:     #C41E3A;
  --color-primary-dark:#8B0000;
  --color-primary-light:#F44336;
  --color-accent:      #FFB800;
  --color-navy:        #0D1B2A;
  --color-navy-light:  #1A2E45;

  /* Neutrals */
  --color-white:    #FFFFFF;
  --color-gray-50:  #F9FAFB;
  --color-gray-100: #F3F4F6;
  --color-gray-600: #4B5563;
  --color-gray-900: #111827;

  /* Typography */
  --font-display: 'Cairo', sans-serif;
  --text-hero:    clamp(2.5rem, 6vw, 5rem);
  --text-h2:      clamp(1.8rem, 3.5vw, 2.8rem);
  --text-body:    1.1rem;
  --line-height:  1.7;

  /* Effects */
  --glass-bg:      rgba(255,255,255,0.08);
  --glass-border:  rgba(255,255,255,0.15);
  --shadow-card:   0 25px 50px rgba(0,0,0,0.12);
  --shadow-glow:   0 0 40px rgba(196,30,58,0.3);
  --radius-lg:     20px;
  --radius-card:   16px;

  /* Transitions */
  --transition:    all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
```

---

### 2B.5 Key Visual Effects

#### 1. Animated Hero Background
```css
.hero {
  background: linear-gradient(135deg, var(--color-navy) 0%, #1a0a0a 50%, #2d0010 100%);
  position: relative;
  overflow: hidden;
}

/* Floating blood-cell orbs */
.hero::before, .hero::after {
  content: '';
  position: absolute;
  border-radius: 50%;
  animation: orbit 20s linear infinite;
}
.hero::before {
  width: 600px; height: 600px;
  background: radial-gradient(circle, rgba(196,30,58,0.15) 0%, transparent 70%);
  top: -200px; right: -100px;
}
```

#### 2. Glassmorphism Cards
```css
.feature-card {
  background: rgba(255,255,255,0.05);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: var(--radius-card);
  transform-style: preserve-3d;
  transition: var(--transition);
}
```

#### 3. Scroll Reveal (JS — IntersectionObserver)
```javascript
// In main.js
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('revealed');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('[data-reveal]').forEach(el => {
  revealObserver.observe(el);
});
```

```css
[data-reveal] {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}
[data-reveal].revealed {
  opacity: 1;
  transform: translateY(0);
}
[data-reveal-delay="1"] { transition-delay: 0.1s; }
[data-reveal-delay="2"] { transition-delay: 0.2s; }
/* etc */
```

#### 4. 3D Card Tilt
```javascript
document.querySelectorAll('.feature-card').forEach(card => {
  card.addEventListener('mousemove', e => {
    const rect = card.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    card.style.transform = `perspective(1000px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateZ(10px)`;
  });
  card.addEventListener('mouseleave', () => {
    card.style.transform = '';
  });
});
```

#### 5. Animated Stat Counters
```javascript
function animateCounter(el) {
  const target = parseInt(el.getAttribute('data-target'));
  const duration = 2000;
  const step = target / (duration / 16);
  let current = 0;
  const interval = setInterval(() => {
    current = Math.min(current + step, target);
    el.textContent = Math.floor(current).toLocaleString();
    if (current >= target) clearInterval(interval);
  }, 16);
}
```

#### 6. RTL-safe 3D effects
For Arabic (RTL), rotateY direction needs to be flipped:
```javascript
const isRTL = document.documentElement.dir === 'rtl';
const yRotation = isRTL ? -(x * 10) : (x * 10);
```

---

### 2B.6 Page-by-Page Redesign

**Home Page (`index.css`):**
- **Hero:** Dark navy/crimson gradient background, white hero text, animated orb backgrounds, stat cards with glassmorphism and animated counters, image with subtle drop shadow
- **Features section:** Dark navy background, grid of glassmorphism feature cards with tilt effect, emoji icons replaced with SVG/icon font icons wrapped in colored circles
- **How It Works:** Alternating layout, timeline connector line, step numbers in large crimson circles
- **CTA section:** Bold full-width section with gradient background, large text, glowing button

**About Page (`about.css`):**
- **Hero:** Same dark navy gradient with subtle text reveal
- **Mission/Vision cards:** Side-by-side glassmorphism cards with icon accent and top-border color band
- **Values grid:** Warm white background, values in icon+title+text cards with hover lift
- **Team section:** Photo cards with overlay gradient revealing name/role on hover
- **Impact section:** Bold statistics in large numerals on dark background

**Contact Page (`contact.css`):**
- **Header:** Simple but elegant with subtitle
- **Contact grid:** 2-column — info card (dark glassmorphism) + form card (clean white)
- **Form:** Floating label style inputs, focus border glow animation
- **FAQ accordion:** Smooth expand/collapse with icon rotation

---

### 2B.7 Navbar Enhancement
```css
.navbar {
  background: rgba(13, 27, 42, 0.0);
  transition: background 0.4s ease, backdrop-filter 0.4s ease;
}
.navbar.scrolled {
  background: rgba(13, 27, 42, 0.92);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 4px 30px rgba(0,0,0,0.3);
}
```

JS in `main.js`:
```javascript
window.addEventListener('scroll', () => {
  document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
});
```

---

### 2B.8 Risks & Constraints

- **`backdrop-filter` support:** Not supported in older browsers. Add `-webkit-backdrop-filter` prefix. Provide graceful fallback (semi-transparent solid background).
- **RTL layout:** Test every flexbox/grid section in Arabic mode. `margin-inline-start/end` instead of `margin-left/right` in critical spots. The 3D tilt JS must flip X rotation direction for RTL.
- **Performance:** Particle effects and heavy CSS animations can drop framerate on mobile. Use `@media (prefers-reduced-motion: reduce)` to disable animations for accessibility. Keep particle count low.
- **Dark hero on light content:** The shift from dark hero to lighter section backgrounds needs careful gradient transition to avoid jarring cut.
- **Content CMS compatibility:** Content is pulled from `GeneralSettings` — the Blade template `{{ $settings->getTranslation('home_hero_title') }}` must never be touched. Only surrounding HTML structure and CSS classes change.

---

### 2B.9 Step-by-Step Implementation Plan

1. **Design token system** (1 hour): Rewrite `main.css` `:root` variables, update `.btn` styles, update global typography.
2. **Home page redesign** (4 hours): New `index.css` — hero (dark + orbs), features (glass cards), how-it-works (timeline), CTA. Add `data-reveal` attributes to `home.blade.php`.
3. **Navbar enhancement** (1 hour): Frosted glass on scroll, update `navbar.css`.
4. **JS enhancements** (2 hours): `main.js` — scroll reveal, counter animation, 3D tilt, navbar scroll class.
5. **About page redesign** (3 hours): New `about.css` — team card hover effects, values grid, impact numbers.
6. **Contact page redesign** (2 hours): New `contact.css` — floating labels, info card, FAQ accordion.
7. **RTL testing** (1 hour): Test every section in Arabic mode, fix any LTR-assumed CSS.
8. **Mobile/responsive pass** (1 hour): Test at 375px, 768px, 1200px. Adjust breakpoints.
9. **Performance audit** (30 min): Add `prefers-reduced-motion`, verify no reflows on scroll.

**Total estimate: ~15.5 hours**

---

## Summary: Implementation Priority

| Task | Effort | Risk | Impact | Priority |
|------|--------|------|--------|----------|
| Fix Achievement model bugs | Low (30m) | Low | Blocking | Immediate |
| Achievement Service | Medium (3h) | Low | High | High |
| Achievement Admin Resource | Medium (2h) | Low | Medium | High |
| Achievement Donor Widget | Medium (2h) | Low | High | High |
| Achievement Backfill Command | Low (1h) | Low | Required | High |
| Home Page Redesign | High (5h) | Medium | Very High | High |
| Navbar Enhancement | Low (1h) | Low | High | High |
| About Page Redesign | Medium (3h) | Medium | High | Medium |
| Contact Page Redesign | Medium (2h) | Low | Medium | Medium |
| RTL + Mobile Testing | Low (2h) | Medium | Critical | Always last |

---

*This document is the authoritative project memory for BloodBridge. Any new feature must be planned against this context.*
