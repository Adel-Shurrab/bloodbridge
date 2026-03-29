# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Start all dev services (server + queue worker + Vite) — use this for normal dev
composer dev

# Run all tests
composer test

# Run a single test file
php artisan test tests/Feature/NotificationClassesTest.php
./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php

# Fix code style
./vendor/bin/pint

# Build frontend assets
npm run dev    # watch mode
npm run build  # production

# First-time project setup
composer setup

# Artisan commands
php artisan blood-requests:expire          # expire requests older than 48h (scheduled 2×/day)
php artisan blood:cleanup-stale-responses  # mark stale PENDING responses UNREACHABLE (scheduled hourly)
```

### Scheduler

Both artisan commands are scheduled in `routes/console.php`. For local dev run:
```bash
php artisan schedule:work
```

### FastAPI ML Service

The sidecar Python service lives in `ai_service/`. It requires a MySQL database (not SQLite).

```bash
cd ai_service
source venv/Scripts/activate   # Windows: venv\Scripts\activate.bat
uvicorn app:app --reload --port 8001
```

The service reads DB credentials from the root `.env` (same keys Laravel uses: `DB_HOST`, `DB_DATABASE`, etc.). When the FastAPI service is down the scoring waterfall silently falls back to rule-based PHP — ML scoring only activates when `ml_scoring_enabled = true` in `ScoringSettings`.

## Architecture

BloodBridge is a **Laravel 12 + Filament 4** blood donation management platform with a sidecar **Python FastAPI** ML scoring service.

### Three Filament Panels

Each has its own panel provider (`app/Providers/Filament/`) and resource/page tree (`app/Filament/{Admin,Donor,Organization}/`).

| Panel | URL path | Color | Gate |
|-------|----------|-------|------|
| Admin | `/admin` | Red | `users.role = ADMIN (3)` |
| Donor | `/donor` | Red | `users.role = DONOR (1)` |
| Organization | `/org/{tenant:slug}` | Blue | `users.role = ORGANIZATION (2)` |

Panel access is enforced by `User::canAccessPanel()`. Inactive users (`is_active = false`) are blocked from all panels. The Organization panel uses **Filament tenancy** — `Organization` is the tenant model (keyed by `slug`). Each organization user only has one tenant.

### Middleware Guards

- **Donor panel auth chain**: `Authenticate` → `EnsureEmailIsVerifiedUnlessAdmin` → `CheckDonorIneligibility`
  - `CheckDonorIneligibility`: redirects donors with `chronic_disease = true` to an ineligible page
- **Organization panel tenant middleware**: `CheckOrganizationApproved`
  - Redirects to `PendingApproval` page if org status is `PENDING` or `REJECTED`

### User / Role Model

`User` has a single `role` enum (`UserRole`: `DONOR=1`, `ORGANIZATION=2`, `ADMIN=3`). Donor and Organization data are in **separate models** (`Donor`, `Organization`) linked via `user_id` (one-to-one from User). There are no polymorphic role tables.

`User::getDashboardUrl()` routes to the correct Filament panel after login based on role.

### Core Blood Request Lifecycle

1. **Organization creates** a `BloodRequest` (status `PENDING`) with blood type, units needed, urgency, GPS coords, and search radius.

2. **Broadcast triggered** → `BloodRequest::broadcastToEligibleDonors()` → `BloodRequestBroadcastService::broadcast()`:
   - Validates location (GPS coords or governorate fallback)
   - Wraps everything in a DB transaction
   - Sets status to `BROADCASTED`, records `broadcasted_at`

3. **Progressive radius expansion** (`findEligibleDonorsWithExpansion`):
   - Starts at `search_radius_km` (×3 for CRITICAL)
   - Expands by 5 km per step up to 25 km max
   - Target donor count = `units_needed × 2.0` (normal) or `× 2.5` (critical)
   - Falls back to `UNKNOWN` blood type donors if target not met (normal only)
   - Uses `Donor::withinRadius()` scope (Haversine SQL) with `compatible()` and `eligible()` scopes
   - Respects notification cooldown: 2h (normal) / 30min (critical)
   - Saves final `actual_search_radius_km` to the request

4. **Donor scoring** → `DonorScoringService::scoreAndSelect()`:
   - Scores all eligible donors via a 4-level waterfall (never fails silently)
   - Applies epsilon-greedy bucketing (80% exploitation / 20% exploration)
   - Budget cap = `max_notifications_per_broadcast` (×1.5 for CRITICAL)
   - Returns selected donors with stats for logging

5. **Notification dispatch** → `DispatchBloodRequestNotifications::dispatchBatches()`:
   - Chunks donors into batches of 100 (queue payload limit)
   - Each job re-validates eligibility at execution time
   - Sends `BloodRequestMatchNotification` via `database` + `broadcast` channels
   - Notifications carry the donor's distance and are delivered in the donor's preferred locale

### Scoring Waterfall (`DonorScoringService`)

Level 1 → Level 2 → Level 3 → Level 4 (implicit):

| Level | Source | When used |
|-------|--------|-----------|
| 1 | DB cache (`donor_predictive_scores`) | Score exists and is within `score_staleness_days` |
| 2 | FastAPI XGBoost | `ml_scoring_enabled = true` and circuit not open |
| 3 | Rule-based PHP query | All others; always available |
| 4 | Neutral 0.5 | Implicit via `ScoringResult::neutral()` in `scoreAndSelect` |

Rule-based formula: `(acceptance_rate × 0.50) + (recency_score × 0.30) + (loyalty_score × 0.20)`, with no-show penalty on denominator.

`ScoringResult` DTO has: `donorId`, `score` (0–1), `isColdStart` (bool), `source` (string).

Cold-start donors (history < `min_history_for_exploitation`) always go to the exploration bucket.

### FastAPI Circuit Breaker (`FastApiCircuitBreaker`)

State stored in `file` cache store (not `database`) under three keys: `fastapi_circuit:state`, `fastapi_circuit:failures`, `fastapi_circuit:opened_at`.

States: `closed` → `open` (after N failures) → `half_open` (after recovery window) → `closed`.

Thresholds from `ScoringSettings`: `circuit_breaker_failure_threshold` (default 3), `circuit_breaker_recovery_seconds` (default 120). When open, `getFromFastApi()` returns `[]` and the waterfall continues to Level 3.

### QR Code Verification Flow

When a donor accepts a request:
1. `BloodRequestActionService::accept()` calls `QRCodeService::generate()` — creates a 32-char hex token stored in `request_responses.verification_qr_code`, expires in 7 days.
2. Donor downloads the QR (SVG) from their panel — `BloodRequests` page → `download_qr` action.
3. Organization scans it at `ScanDonorQR` page (rate-limited: 30 scans/min per org).
4. `QRCodeService::validate()` checks token, expiry, organization ownership, and request status.
5. `confirmAdmission()` sets response status to `ACCEPTED` with `verified_at`.
6. On cancel/ignore: `QRCodeService::revoke()` nulls out the token.

### Donor Eligibility (`DonorHealthProfile`)

Eligibility is calculated automatically on every `saving` event via `calculateEligibility()`:
- Permanent ineligibility: `chronic_disease = true` (no `next_eligible_date`)
- Temporary: weight < 50 kg, height < 140 cm, active infection (+14 days), recent donation < 90 days, recent surgery < 28 days
- `is_eligible` and `next_eligible_date` are computed and stored, not manually set

### Blood Type Compatibility

`BloodType` enum (int-backed, 1–9 including `UNKNOWN=9`) contains the full compatibility matrix:
- `getCompatibleDonorTypes()` — who can donate to this type (used in broadcast matching)
- `getCompatibleRecipientTypes()` — what types this donor can donate to (used in donor blood request display)

Both methods are derived from a single source of truth (`getCompatibleDonorTypes`) to stay in sync.

### Response Status Lifecycle

`RequestResponseStatus` (int-backed): `PENDING=0` → `ACCEPTED=1` (scanned at org), `DECLINED=2` (medical exclusion), `COMPLETED=3` (donated), `IGNORED=4` (donor declined), `NO_SHOW=5`, `UNREACHABLE=6`, `NOT_NEEDED=7`.

Cleanup:
- `blood:cleanup-stale-responses` (hourly): PENDING → UNREACHABLE after 8h (critical) / 48h (normal)
- `blood-requests:expire` (twice daily): PENDING/BROADCASTED requests → EXPIRED after 48h; triggers `CancelExcessResponsesJob` to set remaining PENDING responses to NOT_NEEDED and notify donors

### Settings System

`spatie/laravel-settings` — two groups backed by the `settings` DB table (not `.env`):

- `GeneralSettings` (group `general`) — site content (translatable), donation eligibility thresholds (`min_donor_age`, `min_days_between_donations`, etc.), org limits (`org_max_requests_per_day`), contact/social links
- `ScoringSettings` (group `scoring`) — ML toggle, epsilon, budget cap, circuit breaker thresholds, staleness days

Managed via Admin panel: `/admin` → Settings → General Settings / Scoring Settings.

`GeneralSettings` has a custom `TranslatableArray` cast for fields that hold `{ar: ..., en: ...}` JSON. Access via `$settings->site_name` (returns current locale string) or `$settings->getTranslation('site_name', 'ar')`.

### Localization

Bilingual: Arabic (`ar`) and English (`en`). Translation files:
- `lang/ar/` and `lang/en/` — PHP files per domain (`admin.php`, `donor.php`, `organization.php`, `constants.php`, etc.)
- `lang/ar.json` and `lang/en.json` — JSON keys for Filament/Blade string lookups

Model fields use `spatie/laravel-translatable` (`HasTranslations`). Per-user locale stored in `users.locale`, synced via `SyncUserLocale` middleware, delivered in notifications via `User::preferredLocale()`.

### Public Routes

All public routes are wrapped in `mcamara/laravel-localization` prefix middleware (locale in URL). Key public pages: `/`, `/about`, `/contact`, `/eligibility`, `/terms`. Separate registration flows for donors (`/register/donor`) and organizations (`/register/organization`).

### Queue & Broadcasting

- Queue: `database` driver (dev). `composer dev` starts the worker. Worker uses `--tries=1`.
- Broadcasting: Laravel Reverb (dev) or Pusher (prod). `routes/channels.php` has channel definitions.
- `BROADCAST_DRIVER=log` in `.env` disables live WebSocket delivery locally.
- All notifications (`BloodRequestMatchNotification`, etc.) implement `ShouldQueue` and deliver on `database` + `broadcast` channels.
- `NotificationService` wraps `$notifiable->notify()`, applies the recipient's locale, and logs all successes/failures. Use it instead of calling `notify()` directly.

### Admin Panel Pages & Widgets

Beyond standard CRUD resources (Users, Donors, Organizations, BloodRequests, Announcements, ContactMessages), the Admin panel has:

- `Dashboard` — `DashboardHeaderWidget`, `StatsOverview`, `PendingOrganizationsWidget`
- `Statistics` page — manually registered widgets (not on Dashboard): `AdvancedStatsOverview`, `BloodTypeDemandWidget`, `EngagementChartWidget`, `MLScoringMonitorWidget`, `RecentActivityWidget`
- `ManageGeneralSettings` — edits `GeneralSettings`
- `ManageScoringSettings` — edits `ScoringSettings` (ML on/off, epsilon, circuit breaker, A/B test)

### Organization Panel Pages

- `Dashboard` — `OrganizationHeaderWidget`, `BloodRequestStatsWidget`
- `Statistics` page — `OverviewStatsWidget`, `BloodRequestTrendWidget`, `BloodTypeDistributionWidget`, `SearchRadiusStatsWidget`, `RecentResponsesWidget`, `UnknownDonorImpactWidget`
- `ScanDonorQR` — QR scanner with rate limiting (30/min per org)
- `PendingApproval` — shown when `approval_status` is PENDING or REJECTED
- `EditOrganizationProfile` — tenant profile page

### Donor Panel Pages

- `Dashboard` — `DonorHeaderWidget`, `DonorStatsOverviewWidget`
- `BloodRequests` — main page for viewing and responding to active requests; shows distance, QR download; donors can only hold one active PENDING response at a time
- `History` — past donation/response history
- `EditProfile` — donor profile editing
- `ChangePassword`
- `IneligibleDonor` — redirect target when `chronic_disease = true`


### ML Scoring System — Current Status

**Completed components:**
- `DonorScoringService` — 4-level waterfall scoring
- `FastApiCircuitBreaker` — file cache store, 3-failure threshold
- `ScoringResult` DTO — donorId, score, isColdStart, source
- `DecayEpsilonCommand` — weekly epsilon decay via scheduler
- `MLScoringMonitorWidget` — on Statistics page, 30s polling

**XGBoost Model:**
- Trained: 500 synthetic records, 13 features
- AUC-ROC: 0.926
- Files: `ai_service/models/donor_scorer.pkl`, `feature_names.pkl`
- Training notebook: `ai_service/notebooks/03_dataset_generation.ipynb`

**Circuit breaker cache keys (file store):**
- `fastapi_circuit:state` — closed | open | half_open
- `fastapi_circuit:failures` — int counter
- `fastapi_circuit:opened_at` — Unix timestamp

**Current dev settings:**
- `min_history_for_exploitation = 1` (change to 5 for production)
- `MIN_HISTORY_FOR_MODEL = 1` in `ai_service/config.py` (change to 5)
- `ml_scoring_enabled = true`
- FastAPI runs on port 8000 (not 8001)

**What remains:**
- Connect /api/retrain to actual training script
- Auto-insert model_training_logs after retraining
- Set min_history = 5 for production