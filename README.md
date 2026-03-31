# BloodBridge

A blood donation management platform built with Laravel 12 and Filament 4. It connects blood donors with hospitals and blood banks through geographic matching, multi-level donor scoring, and asynchronous notifications.

The platform is designed for the Gaza Strip and supports Arabic and English throughout.

---

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Modules](#modules)
- [Setup](#setup)
- [Configuration](#configuration)
- [Background Jobs and Scheduling](#background-jobs-and-scheduling)
- [Notes](#notes)

---

## Overview

BloodBridge gives three types of users separate, purpose-built interfaces: system administrators, blood donors, and medical organizations. When an organization creates a blood request, the system finds eligible donors by blood type compatibility and GPS proximity, scores them using a multi-level algorithm, and dispatches notifications asynchronously through a queue worker. Donors can accept or decline requests; organizations verify attendance by scanning a QR code at admission.

The system covers five governorates of the Gaza Strip: Gaza, North Gaza, Deir al-Balah, Khan Younis, and Rafah.

---

## Key Features

### Blood Request Lifecycle

- Organizations create requests specifying blood type, units needed, urgency (Normal or Critical), GPS location, and initial search radius.
- On broadcast, the system finds eligible donors and dispatches notifications. The request status moves from `PENDING` → `BROADCASTED` → `FULFILLED` or `EXPIRED`.
- If the initial radius does not yield enough donors, the system expands by 5 km per step up to a 25 km maximum. Critical requests start with a 3× wider radius and a tighter notification cooldown (30 minutes vs. 2 hours for normal).
- Requests that remain open after 48 hours are expired automatically by a scheduled command.

### Donor Matching

- Blood type compatibility covers all ABO/Rh combinations. An `UNKNOWN` type is used as a fallback.
- Geographic matching uses a Haversine SQL query with a bounding-box pre-filter. When GPS coordinates are unavailable the system falls back to governorate-level matching.
- Donors must pass eligibility checks at both broadcast time and job execution time: minimum weight (50 kg) and height (140 cm), no active infection, no chronic disease, no donation within 90 days, no recent surgery within 28 days.

### Donor Scoring

Donors are scored before each notification batch through a four-level waterfall that never fails silently:

| Level | Source | Condition |
|-------|--------|-----------|
| 1 | Cached DB score (`donor_predictive_scores`) | Score exists within the staleness window |
| 2 | FastAPI XGBoost model | ML scoring enabled and circuit breaker closed |
| 3 | Rule-based PHP | Always available as fallback |
| 4 | Neutral 0.5 | Cold-start or no history |

Rule-based formula:

```
score = (acceptance_rate × 0.50)
      + (recency_score   × 0.30)
      + (loyalty_score   × 0.20)

acceptance_rate = accepted / (total_responses + no_show_penalty)
recency_score   = 1.0 (≤7d) | 0.8 (≤30d) | 0.5 (≤90d) | 0.3 (≤180d) | 0.1 (older)
loyalty_score   = min(total_donations / 10, 1.0)
```

An epsilon-greedy strategy (default 80 % exploitation / 20 % exploration) determines which scored donors receive notifications. Donors with fewer responses than `min_history_for_exploitation` are always placed in the exploration bucket. Critical requests receive a 1.5× notification budget.

### QR Code Verification

When a donor accepts a request, a 32-character hex token is generated and stored against their response. Donors download the QR code (SVG) from their panel. Organizations scan it at the `ScanDonorQR` page (rate-limited: 30 scans per minute per organization). A valid scan sets the response to `ACCEPTED` with a `verified_at` timestamp. The token is revoked on cancel or ignore.

### Multi-Panel System

Three separate Filament panels, each with its own layout, middleware chain, and resource tree:

| Panel | URL | Role |
|-------|-----|------|
| Admin | `/admin` | System administrators |
| Donor | `/donor` | Blood donors |
| Organization | `/org/{slug}` | Hospitals and blood banks (multi-tenant) |

The Organization panel uses Filament tenancy keyed by organization `slug`. Each organization user belongs to exactly one tenant.

### Notifications

- Notifications are dispatched in batches of up to 100 donors per job.
- Delivered via `database` + `broadcast` channels.
- Each donor receives the notification in their preferred locale (Arabic or English).
- Real-time delivery uses Laravel Reverb (development) or Pusher (production).
- All sends go through `NotificationService`, which wraps `notify()`, applies the recipient's locale, and logs successes and failures.

### Localization

Bilingual: Arabic (`ar`) and English (`en`). Per-user locale is stored in `users.locale` and synced via middleware on each request. Model fields use `spatie/laravel-translatable`. Public routes are prefixed with the active locale via `mcamara/laravel-localization`.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12, PHP 8.3+ |
| Admin UI | Filament 4 (three panels) |
| Frontend | Alpine.js, Tailwind CSS 4, Vite 7 |
| Database | MySQL (production), SQLite (local default) |
| Queue | Laravel Queue, `database` driver |
| Broadcasting | Laravel Reverb (dev) / Pusher (production) |
| ML sidecar | Python 3, FastAPI, XGBoost |
| QR codes | `simplesoftwareio/simple-qrcode` |
| Settings | `spatie/laravel-settings` |
| Trends / charts | `flowframe/laravel-trend` |
| Testing | Pest 4 |
| Code style | Laravel Pint |

Redis is present in `.env.example` but is not used by default — the application uses the `database` driver for cache, queue, and sessions in the reference configuration.

There are no Livewire components. All interactive UI is handled by Filament (which uses Livewire internally) and Alpine.js on public pages.

---

## Architecture

### Directory Structure

```
bloodbridge/
├── app/
│   ├── Console/Commands/        # ExpireOldBloodRequests, CleanupStaleResponses,
│   │                            #   DecayEpsilonCommand
│   ├── Enums/                   # UserRole, BloodType, BloodRequestStatus,
│   │                            #   UrgencyLevel, RequestResponseStatus,
│   │                            #   OrganizationStatus, Gender, NotificationType
│   ├── Filament/
│   │   ├── Admin/               # Resources: Users, Donors, Organizations,
│   │   │                        #   BloodRequests, Announcements, ContactMessages
│   │   │                        # Pages: Dashboard, Statistics, Settings
│   │   ├── Donor/               # Pages: Dashboard, BloodRequests, History,
│   │   │                        #   EditProfile, ChangePassword, IneligibleDonor
│   │   └── Organization/        # Pages: Dashboard, Statistics, ScanDonorQR,
│   │                            #   EditOrganizationProfile, PendingApproval
│   ├── Jobs/                    # DispatchBloodRequestNotifications,
│   │                            #   CancelExcessResponsesJob
│   ├── Models/                  # User, Donor, DonorHealthProfile, Organization,
│   │                            #   BloodRequest, RequestResponse,
│   │                            #   DonorPredictiveScore, DonorBehavioralMetric,
│   │                            #   Achievement, DonorAchievement, Announcement,
│   │                            #   ContactMessage, Governorate, ModelTrainingLog, ...
│   ├── Notifications/           # BloodRequestMatchNotification,
│   │                            #   DonorResponseNotification,
│   │                            #   ResponseNotNeededNotification,
│   │                            #   SystemAnnouncement, ...
│   ├── Providers/Filament/      # AdminPanelProvider, DonorPanelProvider,
│   │                            #   OrganizationPanelProvider
│   ├── Services/
│   │   ├── BloodRequestBroadcastService.php
│   │   ├── BloodRequestActionService.php
│   │   ├── DonorScoringService.php
│   │   ├── FastApiCircuitBreaker.php
│   │   ├── NotificationService.php
│   │   └── QRCodeService.php
│   └── Settings/                # GeneralSettings, ScoringSettings
├── ai_service/                  # FastAPI + XGBoost sidecar
│   ├── app.py
│   ├── config.py
│   └── models/                  # donor_scorer.pkl, feature_names.pkl
├── database/
│   ├── migrations/              # 22 migrations
│   └── seeders/                 # Admin, organizations, donors,
│                                #   blood requests, interactions
└── routes/
    ├── web.php                  # Public + auth routes (locale-prefixed)
    └── console.php              # Scheduled commands
```

### Middleware Chains

- **Donor panel**: `Authenticate` → `EnsureEmailIsVerifiedUnlessAdmin` → `CheckDonorIneligibility`
- **Organization panel tenant**: `CheckOrganizationApproved` (redirects to `PendingApproval` if status is PENDING or REJECTED)

### Settings System

Two settings groups backed by the `settings` database table, managed through the Admin panel:

- **GeneralSettings** — site content (translatable), eligibility thresholds (`min_donor_age`, `min_days_between_donations`, weight/height limits), organization request limits, contact and social links
- **ScoringSettings** — ML on/off toggle, epsilon value, budget cap multiplier, circuit breaker thresholds, score staleness window, `ml_enabled_since` timestamp used by the epsilon decay command

### FastAPI Circuit Breaker

State is stored in the `file` cache store under three keys: `fastapi_circuit:state`, `fastapi_circuit:failures`, `fastapi_circuit:opened_at`. The breaker opens after a configurable number of consecutive failures (default 3) and enters half-open after a recovery window (default 120 seconds). When open, ML scoring is skipped and the waterfall continues to Level 3 (rule-based PHP).

---

## Modules

### Blood Request Lifecycle

```
Create (PENDING)
  └─ broadcastToEligibleDonors()
       ├─ Validate location (GPS or governorate fallback)
       ├─ Progressive radius expansion (5 km steps, max 25 km)
       ├─ Score donors (4-level waterfall)
       ├─ Epsilon-greedy selection
       ├─ Dispatch DispatchBloodRequestNotifications in batches
       └─ Status → BROADCASTED

BROADCASTED
  ├─ Donor accepts → response PENDING, QR token generated
  ├─ Organization scans QR → response ACCEPTED (verified_at set)
  ├─ Donation confirmed → response COMPLETED
  ├─ All units confirmed → request FULFILLED
  │    └─ CancelExcessResponsesJob: remaining PENDING → NOT_NEEDED
  └─ 48 h timeout → request EXPIRED
       └─ CancelExcessResponsesJob: remaining PENDING → NOT_NEEDED
```

### Response Status Lifecycle

```
PENDING → ACCEPTED (QR scanned at org)
        → DECLINED (medical exclusion)
        → IGNORED (donor did not respond)
        → NO_SHOW (accepted but did not arrive)
        → UNREACHABLE (8 h for critical / 48 h for normal, via cleanup command)
        → NOT_NEEDED (request fulfilled or expired before donor acted)
ACCEPTED → COMPLETED (donation recorded)
```

### Donor Eligibility

Computed automatically on every `saving` event of `DonorHealthProfile` via `calculateEligibility()`. Permanent ineligibility: `chronic_disease = true`. Temporary ineligibility: weight below 50 kg, height below 140 cm, active infection (14-day cooldown), donation within 90 days, surgery within 28 days. The resulting `is_eligible` flag and `next_eligible_date` are stored, not manually set.

### Admin Panel Pages

- **Dashboard** — header widget, stats overview, pending organizations queue
- **Statistics** — blood type demand, engagement charts, ML scoring monitor (30-second polling), recent activity
- **Settings** — General Settings page, Scoring Settings page

### Organization Panel Pages

- **Dashboard** — organization header, blood request stats
- **Statistics** — overview stats, request trends, blood type distribution, search radius stats, recent responses, unknown donor impact
- **ScanDonorQR** — QR scanner with rate limiting
- **PendingApproval** — shown when approval status is PENDING or REJECTED

### Donor Panel Pages

- **Dashboard** — donor header, stats overview
- **BloodRequests** — active compatible requests with distance, accept/decline actions, QR download
- **History** — past responses and donations
- **EditProfile**, **ChangePassword**, **IneligibleDonor**

---

## Setup

### Requirements

- PHP 8.3+
- Composer 2
- Node.js 18+
- MySQL (or SQLite for local development)

### Installation

```bash
git clone <repo-url>
cd bloodbridge

# Installs dependencies, copies .env, generates app key, runs migrations, builds assets
composer setup

# Edit .env with your database credentials, app URL, and mail settings
```

### Local Development

```bash
# Starts HTTP server + queue worker + Vite in one terminal
composer dev
```

This runs `php artisan serve`, `php artisan queue:listen --tries=1`, and `npm run dev` concurrently.

### FastAPI ML Sidecar (optional)

The Python service is required only for Level 2 ML scoring. Without it, the waterfall falls through to rule-based PHP automatically.

```bash
cd ai_service
source venv/Scripts/activate    # Windows
uvicorn app:app --reload --port 8000
```

The service reads database credentials from the root `.env` using the same `DB_*` keys Laravel uses. Enable ML scoring in **Admin → Settings → Scoring Settings** (`ml_scoring_enabled = true`).

---

## Configuration

Key `.env` variables (see `.env.example` for the full list):

```env
APP_NAME=BloodBridge
APP_URL=http://localhost
APP_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=bloodbridge
DB_USERNAME=
DB_PASSWORD=

QUEUE_CONNECTION=database
BROADCAST_CONNECTION=reverb     # or pusher

REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=
```

Do not commit `.env`. The file is git-ignored.

---

## Background Jobs and Scheduling

Commands are registered in `routes/console.php`:

| Command | Schedule | Purpose |
|---------|----------|---------|
| `blood:cleanup-stale-responses` | Hourly | Marks PENDING responses UNREACHABLE after 8 h (critical) / 48 h (normal) |
| `blood-requests:expire` | Twice daily | Expires BROADCASTED/PENDING requests older than 48 h; triggers NOT_NEEDED cleanup |
| `scoring:decay-epsilon` | Daily | Adjusts exploration epsilon based on days since ML was enabled |

Run the scheduler locally with:

```bash
php artisan schedule:work
```

### Epsilon Decay Schedule

| Days since ML enabled | Epsilon |
|-----------------------|---------|
| 0 – 13 | 0.20 |
| 14 – 29 | 0.15 |
| 30 – 59 | 0.10 |
| 60+ | 0.05 |

---

## Testing

```bash
composer test

# Single file
php artisan test tests/Feature/NotificationClassesTest.php
./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php
```

---

## Notes

- `min_history_for_exploitation` in `ScoringSettings` and `MIN_HISTORY_FOR_MODEL` in `ai_service/config.py` are both set to low values suitable for development. Raise both to 5 before production use.
- The XGBoost model was trained on 500 synthetic records (AUC-ROC 0.926 on that dataset). It should be retrained on real data before production use.
- The `/api/retrain` endpoint in the FastAPI service exists but does not yet connect to a training script.
- The `achievements` and `appointments` tables and models exist. The Filament UI for these is not yet implemented.
- The `ModelTrainingLog` model exists for tracking retraining runs; auto-insertion after retraining is not yet wired up.

---

## License

MIT
