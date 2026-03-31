# BloodBridge

A blood donation management platform built with Laravel 12 and Filament 4. It connects blood donors with hospitals and blood banks through intelligent geographic matching, donor scoring, and real-time notifications.

---

## Overview

BloodBridge serves three types of users through separate Filament panels: system administrators, blood donors, and medical organizations. When an organization creates a blood request, the system automatically finds eligible donors nearby based on blood type compatibility, GPS coordinates, and historical response behavior, then dispatches notifications asynchronously via a queue.

The platform targets Egypt and supports Arabic and English throughout the interface.

---

## Core Features

### Blood Request Lifecycle

- Organizations create requests specifying blood type, units needed, urgency level, GPS location, and search radius
- Broadcast triggers automatic donor matching and notification dispatch
- Progressive radius expansion: if insufficient donors are found within the initial radius, the system expands by 5 km steps up to 25 km
- Critical-urgency requests use a wider initial radius (3×) and shorter notification cooldown (30 min vs. 2 h)
- Requests expire automatically after 48 hours if not fulfilled

### Donor Matching

- Blood type compatibility matrix covers all ABO/Rh combinations including `UNKNOWN` type as a fallback
- Geographic matching uses Haversine SQL (`withinRadius` scope) or governorate-level fallback when coordinates are unavailable
- Donors must meet eligibility criteria (minimum weight/height, no recent donation within 90 days, no active infection, no chronic disease) to appear in results

### Donor Scoring (4-level waterfall)

Donors are scored before each notification batch. The waterfall never fails silently:

| Level | Source | Condition |
|-------|--------|-----------|
| 1 | Cached DB score (`donor_predictive_scores`) | Score exists and is within staleness window |
| 2 | FastAPI XGBoost model | ML scoring enabled and circuit not open |
| 3 | Rule-based PHP | Always available as fallback |
| 4 | Neutral 0.5 | Cold-start or no data |

Rule-based formula: `acceptance_rate × 0.50 + recency_score × 0.30 + loyalty_score × 0.20` with a no-show penalty on the denominator.

An epsilon-greedy strategy (default 80/20 exploitation/exploration) determines which scored donors actually receive notifications. Cold-start donors are always routed to the exploration bucket.

### QR Code Verification

When a donor accepts a request, a 32-character hex token is stored against their response and encoded as an SVG QR code. Organizations scan it at admission. The token expires in 7 days and is revoked on cancel or ignore.

### Real-time Notifications

- Notifications are queued and dispatched in batches of 100 donors
- Each job re-validates donor eligibility at execution time
- Delivered via `database` + `broadcast` channels (Laravel Reverb or Pusher)
- Delivered in the donor's preferred locale

### Multi-tenancy

The Organization panel uses Filament's built-in tenancy. Each organization is a tenant keyed by `slug`. Users with the `ORGANIZATION` role belong to exactly one tenant.

### Localization

Bilingual: Arabic (`ar`) and English (`en`). Per-user locale stored in `users.locale` and synced via middleware. Model fields use `spatie/laravel-translatable`. URL-prefixed locale routing via `mcamara/laravel-localization`.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 12, PHP 8.3+ |
| Admin UI | Filament 4.x (three panels) |
| Frontend | Alpine.js, Tailwind CSS 4, Vite 7 |
| Database | MySQL (production), SQLite (local dev) |
| Queue | Laravel Queue, `database` driver |
| Broadcasting | Laravel Reverb (dev) / Pusher (production) |
| ML Service | Python FastAPI + XGBoost (sidecar) |
| QR Codes | `simplesoftwareio/simple-qrcode` |
| Settings | `spatie/laravel-settings` |
| Testing | Pest 4 |
| Code style | Laravel Pint |

---

## Architecture

### Three Filament Panels

| Panel | URL | Auth gate |
|-------|-----|-----------|
| Admin | `/admin` | `role = ADMIN (3)` |
| Donor | `/donor` | `role = DONOR (1)` |
| Organization | `/org/{tenant:slug}` | `role = ORGANIZATION (2)` |

Inactive users (`is_active = false`) are blocked from all panels. The donor panel additionally enforces email verification and chronic-disease ineligibility checks via middleware.

### Application Layers

```
app/
├── Console/Commands/        # ExpireOldBloodRequests, CleanupStaleResponses, DecayEpsilonCommand
├── Enums/                   # UserRole, BloodType, BloodRequestStatus, UrgencyLevel,
│                            #   RequestResponseStatus, OrganizationStatus, ...
├── Filament/
│   ├── Admin/               # Resources: Users, Donors, Organizations, BloodRequests,
│   │                        #   Announcements, ContactMessages; Settings pages; Statistics page
│   ├── Donor/               # Dashboard, BloodRequests, History, EditProfile, ChangePassword,
│   │                        #   IneligibleDonor
│   └── Organization/        # Dashboard, BloodRequests, ScanDonorQR, Statistics,
│                            #   EditOrganizationProfile, PendingApproval
├── Jobs/                    # DispatchBloodRequestNotifications, CancelExcessResponsesJob
├── Models/                  # User, Donor, DonorHealthProfile, Organization,
│                            #   BloodRequest, RequestResponse, DonorPredictiveScore,
│                            #   DonorBehavioralMetric, Achievement, Announcement, ...
├── Notifications/           # BloodRequestMatchNotification, DonorResponseNotification,
│                            #   ResponseNotNeededNotification, SystemAnnouncement, ...
├── Services/
│   ├── BloodRequestBroadcastService.php
│   ├── BloodRequestActionService.php
│   ├── DonorScoringService.php
│   ├── FastApiCircuitBreaker.php
│   ├── NotificationService.php
│   └── QRCodeService.php
└── Settings/                # GeneralSettings, ScoringSettings

ai_service/                  # Python FastAPI sidecar (XGBoost scoring)
├── app.py
├── config.py
└── models/                  # donor_scorer.pkl, feature_names.pkl
```

### Settings System

Two settings groups backed by the `settings` DB table (managed in the Admin panel):

- **GeneralSettings** — site content (translatable), eligibility thresholds (`min_donor_age`, `min_days_between_donations`, etc.), organization limits, contact/social links
- **ScoringSettings** — ML on/off toggle, epsilon, budget cap multiplier, circuit breaker thresholds, score staleness window

### FastAPI Circuit Breaker

State stored in the `file` cache store (not `database`) under keys `fastapi_circuit:state`, `fastapi_circuit:failures`, `fastapi_circuit:opened_at`. Opens after N failures (default 3), enters half-open after a recovery window (default 120 s). When open, scoring falls back to Level 3 (rule-based PHP).

---

## Database

22 migrations covering:

- `users`, `donors`, `donor_health_profiles`, `organizations`
- `blood_requests`, `request_responses`, `appointments`
- `achievements`, `donor_achievements`, `eligibility_logs`
- `donor_predictive_scores`, `donor_behavioral_metrics`, `model_training_logs`
- `notifications`, `announcements`, `contact_messages`
- `settings`, `cache`, `jobs`, `governorates`
- Spatial indexes on lat/lng columns

---

## Scheduled Commands

Registered in `routes/console.php`:

| Command | Schedule | Purpose |
|---------|----------|---------|
| `blood:cleanup-stale-responses` | Hourly | Marks PENDING responses as UNREACHABLE after 8 h (critical) / 48 h (normal) |
| `blood-requests:expire` | Twice daily | Expires PENDING/BROADCASTED requests older than 48 h; triggers NOT_NEEDED for remaining responses |
| `scoring:decay-epsilon` | Daily | Decays exploration epsilon in ScoringSettings |

Run locally with:
```bash
php artisan schedule:work
```

---

## Setup

### Requirements

- PHP 8.3+
- Composer 2
- Node.js 18+
- MySQL (or SQLite for local dev)

### Installation

```bash
# Clone and enter the project
git clone <repo-url>
cd bloodbridge

# Install dependencies and initialise the environment
composer setup
# This runs: composer install, cp .env.example .env, key:generate, migrate, npm install, npm run build

# Edit .env with your database credentials, app URL, and mail settings
```

### Local Development

```bash
# Start server + queue worker + Vite in one command
composer dev
```

This runs `php artisan serve`, `php artisan queue:listen --tries=1`, and `npm run dev` concurrently.

### FastAPI ML Service (optional)

The sidecar Python service is only required for Level 2 ML scoring. Without it, the waterfall continues to rule-based PHP scoring automatically.

```bash
cd ai_service
source venv/Scripts/activate      # Windows
uvicorn app:app --reload --port 8000
```

The service reads DB credentials from the root `.env` using the same `DB_*` keys Laravel uses.

Enable ML scoring in the Admin panel under **Settings → Scoring Settings** (`ml_scoring_enabled = true`).

---

## Environment Variables

Key variables (see `.env.example` for the full list):

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
BROADCAST_CONNECTION=reverb      # or pusher

REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=
```

Do not commit `.env`. It is git-ignored.

---

## Testing

```bash
# Run the full test suite
composer test

# Run a specific file
php artisan test tests/Feature/NotificationClassesTest.php
./vendor/bin/pest tests/Feature/Auth/AuthenticationTest.php
```

Tests use Pest 4. The test suite covers authentication, notifications, and blood request lifecycle logic.

---

## Key Public Routes

All public routes are prefixed with the active locale (`/en/` or `/ar/`):

| Route | Description |
|-------|-------------|
| `/` | Landing page |
| `/about` | About page |
| `/eligibility` | Donor eligibility information |
| `/contact` | Contact form (rate-limited: 3/min) |
| `/terms` | Terms of service |
| `/register/donor` | Donor registration |
| `/register/organization` | Organization registration |

---

## Notes

- The appointments and achievements models and migrations exist in the database but the Filament UI for these features is not fully surfaced yet.
- The ML model was trained on 500 synthetic records (AUC-ROC 0.926). It is functional but should be retrained on real data before production use.
- `min_history_for_exploitation` is currently set to 1 in both `ScoringSettings` and `ai_service/config.py`; this should be raised to 5 for production.
- The `/api/retrain` endpoint in the FastAPI service does not yet connect to an actual retraining script.

---

## License

MIT
