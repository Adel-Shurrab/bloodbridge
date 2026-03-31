# BloodBridge — Blood Request End-to-End Test Suite

**Version:** 1.0
**Date:** 2026-03-31
**Author:** QA Engineering
**System Under Test:** BloodBridge — Blood Donation Management Platform
**Feature:** Blood Request Full Lifecycle

---

## Table of Contents

1. [Test Scope](#1-test-scope)
2. [Assumptions](#2-assumptions)
3. [Roles and Permissions](#3-roles-and-permissions)
4. [Preconditions](#4-preconditions)
5. [Business Rules](#5-business-rules)
6. [Main End-to-End Flow](#6-main-end-to-end-flow)
7. [Alternative Flows](#7-alternative-flows)
8. [Exception/Error Flows](#8-exceptionerror-flows)
9. [Detailed Test Cases](#9-detailed-test-cases)
10. [API and Backend Validation Checks](#10-api-and-backend-validation-checks)
11. [Security and Access Control Cases](#11-security-and-access-control-cases)
12. [UI/UX Validation Points](#12-uiux-validation-points)
13. [Notifications and Real-Time Update Checks](#13-notifications-and-real-time-update-checks)
14. [Data Integrity and Audit Trail Checks](#14-data-integrity-and-audit-trail-checks)
15. [Coverage Summary](#15-coverage-summary)

---

## 1. Test Scope

### In Scope

| Area | Coverage |
|------|----------|
| Blood Request Creation | Organization panel — form fields, validation, GPS/map location, blood type, urgency, units, radius |
| Broadcasting | `BloodRequestBroadcastService` — progressive radius expansion, donor filtering, scoring waterfall, notification dispatch |
| Donor Matching | Blood type compatibility matrix, eligibility checks, notification cooldowns, location-based filtering (Haversine) |
| Donor Scoring | 4-level waterfall (DB cache → FastAPI ML → Rule-based PHP → Neutral 0.5), epsilon-greedy selection |
| Donor Response | Accept / Ignore / Cancel actions via `BloodRequestActionService`, single-active-response constraint |
| QR Code Verification | Generation, download (SVG), scanning at organization, rate limiting (30/min), admission confirmation |
| Medical Results | Blood type lab verification, eligibility assessment (eligible / temporary / permanent), EligibilityLog |
| Request Lifecycle | PENDING → BROADCASTED → FULFILLED / CANCELLED / EXPIRED status transitions |
| Response Lifecycle | PENDING → ACCEPTED → COMPLETED / DECLINED / NO_SHOW / UNREACHABLE / NOT_NEEDED |
| Scheduled Commands | `blood-requests:expire` (48h), `blood:cleanup-stale-responses` (8h critical / 48h normal) |
| Admin Oversight | View all requests, view responses, infolist with timeline and search scope details |
| Notifications | `BloodRequestMatchNotification`, `DonorResponseNotification`, `DonorIneligibilityNotification`, `ResponseNotNeededNotification` |
| Authorization | `BloodRequestPolicy`, `RequestResponsePolicy`, panel access gates, middleware guards |

### Out of Scope

- User registration and onboarding flows
- Organization approval workflow (tested separately)
- Donor profile creation and health profile setup (tested separately)
- Payment or billing systems (not applicable)
- FastAPI model training / retraining pipeline
- Frontend CSS/layout pixel-perfect validation

---

## 2. Assumptions

| # | Assumption |
|---|-----------|
| A1 | Test environment has a seeded MySQL database with organizations (APPROVED status), donors (with health profiles), and an admin user |
| A2 | The queue worker is running (`php artisan queue:work`) or jobs are executed synchronously for testing (`QUEUE_CONNECTION=sync`) |
| A3 | Broadcasting is set to `log` driver for testing — real-time WebSocket delivery is not validated in this suite |
| A4 | FastAPI ML service may or may not be running; tests cover both ML-available and ML-unavailable scenarios |
| A5 | All donor accounts are email-verified and active (`is_active = true`) unless stated otherwise |
| A6 | GPS coordinates are available for test organizations and donors unless explicitly testing governorate fallback |
| A7 | `org_max_requests_per_day` is set to 5 (default from `GeneralSettings`) |
| A8 | Scoring settings use defaults: `max_notifications_per_broadcast = 20`, `exploration_ratio = 0.20`, `score_staleness_days = 7` |
| A9 | Time-sensitive tests use Laravel's `Carbon::setTestNow()` or `$this->travel()` to control timestamps |
| A10 | All translations exist for both `ar` and `en` locales |

---

## 3. Roles and Permissions

### 3.1 Role Definitions

| Role | Enum Value | Panel Access | Key Capabilities |
|------|-----------|-------------|-----------------|
| **Donor** | `DONOR (1)` | `/donor` | View compatible requests, accept/ignore/cancel, download QR, view history |
| **Organization** | `ORGANIZATION (2)` | `/org/{tenant:slug}` | Create/edit/view requests, manage responses, scan QR, mark medical results, view statistics |
| **Admin** | `ADMIN (3)` | `/admin` | View all requests (read-only), view all responses, manage users, manage settings |

### 3.2 Permission Matrix — Blood Requests

| Action | Donor | Organization (Own) | Organization (Other) | Admin |
|--------|-------|-------------------|---------------------|-------|
| List requests | Compatible only | Own only | No | All |
| View request detail | If has response | Yes | No | Yes |
| Create request | No | Yes | N/A | No |
| Edit request | No | Yes (unless FULFILLED/CANCELLED) | No | No |
| Delete request | No | Yes (soft-delete) | No | No |
| Accept/Ignore request | Yes (if eligible) | No | No | No |
| Manage responses | No | Yes (own requests) | No | View only |
| Scan QR | No | Yes | No | No |
| Mark medical results | No | Yes | No | No |

### 3.3 Permission Matrix — Request Responses

| Action | Donor | Organization (Owner) | Admin |
|--------|-------|---------------------|-------|
| View own responses | Yes | N/A | N/A |
| View responses to request | No | Yes | Yes |
| Update response status | No | Yes | No |

---

## 4. Preconditions

### 4.1 Data Preconditions

| # | Precondition | Details |
|---|-------------|---------|
| P1 | Approved Organization | Organization with `approval_status = APPROVED`, valid `slug`, assigned `user_id` with `role = ORGANIZATION` |
| P2 | Organization Location | Organization has `lat`, `lng` coordinates and `governorate` set |
| P3 | Eligible Donors | At least 5 donors with: `is_eligible = true`, `next_eligible_date = null` or past, compatible blood types, GPS coordinates within 25 km of organization |
| P4 | Ineligible Donor | At least 1 donor with `is_eligible = false` (e.g., chronic disease or recent donation) |
| P5 | Donor with Active Response | At least 1 donor already holding a PENDING response to another request |
| P6 | Admin User | User with `role = ADMIN`, `is_active = true` |
| P7 | Inactive User | User with `is_active = false` for access-denied testing |

### 4.2 System Preconditions

| # | Precondition |
|---|-------------|
| S1 | Application is deployed and accessible at test URL |
| S2 | Database migrations are current (`php artisan migrate`) |
| S3 | Queue worker is running or `QUEUE_CONNECTION=sync` |
| S4 | Cache is cleared before each test run |
| S5 | Scheduler is running for command-based tests (`php artisan schedule:work`) |

---

## 5. Business Rules

### 5.1 Request Creation Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR1 | Organization can create max 5 requests per day | `org_max_requests_per_day` in `GeneralSettings` |
| BR2 | Blood type is required and must be a valid `BloodType` enum value (1-9) | Form validation |
| BR3 | Units needed must be between 1 and 100 | Numeric validation |
| BR4 | Search radius must be between 1 and 100 km (default 10) | Numeric validation |
| BR5 | Urgency level must be NORMAL (1) or CRITICAL (2), defaults to NORMAL | Enum validation |
| BR6 | GPS coordinates (lat/lng) are optional; if provided, both must be present | Conditional validation |
| BR7 | Request is created in PENDING status | Model default |
| BR8 | Broadcast is triggered immediately after creation | `CreateBloodRequest::afterCreate()` |

### 5.2 Broadcasting Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR9 | Location must have GPS coordinates OR governorate fallback | `validateLocation()` |
| BR10 | Donor target = `units_needed × 2.0` (normal) or `× 2.5` (critical) | Safety multipliers |
| BR11 | Initial radius for CRITICAL = `search_radius_km × 3` | `CRITICAL_RADIUS_MULTIPLIER` |
| BR12 | Radius expands by 5 km per step, max 25 km total | `RADIUS_EXPANSION_STEP_KM`, `MAX_SEARCH_RADIUS_KM` |
| BR13 | UNKNOWN blood type donors are included as fallback for NORMAL requests only | `findEligibleDonorsWithExpansion()` |
| BR14 | Notification cooldown: 2h (normal), 30min (critical) | `NOTIFICATION_COOLDOWN_*_HOURS` |
| BR15 | Notification budget = `max_notifications_per_broadcast` (×1.5 for CRITICAL) | Scoring settings |
| BR16 | Status transitions to BROADCASTED with `broadcasted_at` timestamp | Service logic |
| BR17 | `actual_search_radius_km` is saved to the request after expansion completes | Service logic |

### 5.3 Donor Eligibility Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR18 | Chronic disease → permanently ineligible | `DonorHealthProfile::calculateEligibility()` |
| BR19 | Weight < 50 kg → ineligible | Threshold from `GeneralSettings` |
| BR20 | Height < 140 cm → ineligible | Threshold from `GeneralSettings` |
| BR21 | Active infection → ineligible for 14 days | Calculated next_eligible_date |
| BR22 | Recent donation → ineligible for 56–90 days | `min_days_between_donations` |
| BR23 | Recent surgery → ineligible for 28 days | `min_days_after_surgery` |
| BR24 | Eligible scope: `is_eligible = true AND (next_eligible_date IS NULL OR ≤ today)` | `Donor::eligible()` scope |

### 5.4 Blood Type Compatibility Rules

| Recipient Type | Compatible Donor Types |
|---------------|----------------------|
| O+ | O+, O- |
| O- | O- |
| A+ | A+, A-, O+, O- |
| A- | A-, O- |
| B+ | B+, B-, O+, O- |
| B- | B-, O- |
| AB+ | A+, A-, B+, B-, AB+, AB-, O+, O- (universal recipient) |
| AB- | AB-, A-, B-, O- |
| UNKNOWN | Empty set (UNKNOWN donors used as fallback for NORMAL requests) |

### 5.5 Response Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR25 | Donor can hold only ONE active response (PENDING or ACCEPTED) at a time | `BloodRequestActionService::accept()` |
| BR26 | Accept generates a 32-char hex QR token, expires in 7 days | `QRCodeService::generate()` |
| BR27 | Cancel deletes the response record and revokes QR | `BloodRequestActionService::cancel()` |
| BR28 | Ignore sets status to IGNORED and revokes QR | `BloodRequestActionService::ignore()` |
| BR29 | QR scanning is rate-limited to 30 per minute per organization | `ScanDonorQR` rate limiter |
| BR30 | QR validation checks: token exists, not expired, belongs to org, request is BROADCASTED | `QRCodeService::validate()` |

### 5.6 Fulfillment and Expiry Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR31 | Request FULFILLED when `donors_completed >= units_needed` | Checked after each COMPLETED status |
| BR32 | Fulfillment triggers `CancelExcessResponsesJob` (PENDING → NOT_NEEDED) | Job dispatch |
| BR33 | Requests expire after 48h if still PENDING or BROADCASTED | `blood-requests:expire` command |
| BR34 | Stale PENDING responses → UNREACHABLE after 8h (critical) / 48h (normal) | `blood:cleanup-stale-responses` command |
| BR35 | Expired requests trigger `CancelExcessResponsesJob` | Command logic |

### 5.7 Edit/Re-broadcast Rules

| # | Rule | Implementation |
|---|------|---------------|
| BR36 | Critical fields: blood_type, urgency_level, lat, lng, search_radius_km, units_needed | `EditBloodRequest` tracked fields |
| BR37 | Changing a critical field on a BROADCASTED request triggers full re-broadcast (old responses cancelled) | Edit page logic |
| BR38 | Increasing only `units_needed` triggers a top-up broadcast for additional donors | Edit page logic |
| BR39 | Fields are disabled when request is FULFILLED or CANCELLED | Form field disabling |

---

## 6. Main End-to-End Flow

### Happy Path — Normal Urgency Request Fulfilled by Donor

```
Step  Actor          Action                                   System State
────  ─────          ──────                                   ────────────
 1    Organization   Logs into /org/{slug} panel              Authenticated, tenant resolved
 2    Organization   Navigates to Blood Requests → Create     Create form displayed
 3    Organization   Fills form:                              -
                     - Blood Type: A+
                     - Units Needed: 2
                     - Urgency: NORMAL
                     - Search Radius: 10 km
                     - Location: Map pin (lat/lng)
                     - Notes (EN): "Urgent surgery patient"
 4    Organization   Clicks "Create"                          Request saved (PENDING)
 5    System         afterCreate() → broadcastToEligibleDonors()
 6    System         validateLocation() passes (GPS present)
 7    System         findEligibleDonorsWithExpansion():
                     - Target donors = 2 × 2.0 = 4
                     - Search compatible types: A+, A-, O+, O-
                     - Eligible, within 10 km, not on cooldown
                     - Found 4 donors at initial radius
 8    System         scoreAndSelect():
                     - Level 1: Check DB cache (2 donors cached)
                     - Level 3: Rule-based scoring (2 donors)
                     - Epsilon-greedy: 80% exploit, 20% explore
                     - Budget: min(4, 20) = 4 selected
 9    System         Status → BROADCASTED, broadcasted_at set
                     actual_search_radius_km = 10
10    System         DispatchBloodRequestNotifications job queued
                     Batches: 1 batch of 4 donors
11    System         Job executes: re-validates eligibility,
                     sends BloodRequestMatchNotification
                     (database + broadcast channels)
12    Donor A        Receives notification in /donor panel
13    Donor A        Opens Blood Requests page                Sees A+ request, distance shown
14    Donor A        Clicks "Accept"                          -
15    System         BloodRequestActionService::accept():
                     - Request is active ✓
                     - Donor eligible ✓
                     - No other active responses ✓
                     - RequestResponse created (PENDING)
                     - QR code generated (32-char hex, 7-day expiry)
                     - DonorResponseNotification sent to org
16    Donor A        Downloads QR code (SVG)                  bloodbridge-qr-{id}.svg
17    Donor A        Visits organization with QR code         -
18    Organization   Opens Scan Donor QR page                 Scanner UI shown
19    Organization   Scans donor's QR code                    -
20    System         verifyQRCode():
                     - Rate limit check (< 30/min) ✓
                     - QR token valid ✓
                     - Not expired ✓
                     - Belongs to this org ✓
                     - Response is PENDING ✓
21    Organization   Clicks "Confirm Admission"               Response → ACCEPTED, verified_at set
22    Organization   Opens Responses tab on request            -
23    Organization   Selects "Medical Results" action          Modal opens
24    Organization   Fills medical results:
                     - Blood type verified: A+
                     - Eligibility: Eligible
25    System         Response → COMPLETED
                     total_donations incremented
                     verified_blood_type set to A+
                     Check: 1 completed < 2 units needed
26    [Donor B repeats steps 12-25]                           -
27    System         2 completed = 2 units needed
                     Request → FULFILLED, fulfilled_at set
                     CancelExcessResponsesJob dispatched
28    System         Remaining PENDING responses → NOT_NEEDED
                     QR codes revoked
                     ResponseNotNeededNotification sent
29    Organization   Sees request status: FULFILLED            Dashboard stats updated
30    Admin          Views request in /admin panel             Full infolist visible
```

---

## 7. Alternative Flows

### AF1 — Critical Urgency Request

| Step | Difference from Main Flow |
|------|--------------------------|
| 3 | Urgency set to CRITICAL |
| 7 | Initial radius = `search_radius_km × 3 = 30 km`; target donors = `2 × 2.5 = 5`; cooldown = 30 min |
| 8 | Budget cap = `20 × 1.5 = 30` |
| 34 | Stale responses marked UNREACHABLE after 8h instead of 48h |

### AF2 — Radius Expansion Required

| Step | Description |
|------|------------|
| 7a | Initial radius finds only 2 donors (target is 4) |
| 7b | Expand to 15 km → finds 1 more (total 3) |
| 7c | Expand to 20 km → finds 1 more (total 4) — target met |
| 7d | `actual_search_radius_km = 20`, `expansion_steps = 2` |

### AF3 — Governorate Fallback (No GPS)

| Step | Description |
|------|------------|
| 3 | Organization does not provide lat/lng coordinates |
| 6 | `validateLocation()` checks governorate fallback |
| 7 | Donor matching uses governorate instead of Haversine radius |

### AF4 — UNKNOWN Blood Type Donors as Fallback (Normal Only)

| Step | Description |
|------|------------|
| 7 | After full expansion, target not met with compatible types |
| 7a | System includes donors with `blood_type = UNKNOWN` |
| 7b | UNKNOWN donors scored and added to selection pool |

### AF5 — Donor Ignores Request

| Step | Description |
|------|------------|
| 14 | Donor clicks "Ignore/Decline" instead of Accept |
| 15 | `BloodRequestActionService::ignore()` → Response status = IGNORED, QR revoked |
| 16 | Donor can later accept if request is still active |

### AF6 — Donor Cancels Acceptance

| Step | Description |
|------|------------|
| 16 | After accepting, donor clicks "Cancel Acceptance" |
| 17 | Confirmation dialog shown |
| 18 | `BloodRequestActionService::cancel()` → Response record deleted, QR revoked |
| 19 | Donor slot freed; donor can accept other requests |

### AF7 — Organization Edits Broadcasted Request (Critical Field Change)

| Step | Description |
|------|------------|
| post-9 | Org edits blood_type from A+ to B+ on BROADCASTED request |
| | Old PENDING responses cancelled (NOT_NEEDED) |
| | Full re-broadcast with new blood type compatibility |
| | New donors notified |

### AF8 — Organization Edits Broadcasted Request (Units Increase Only)

| Step | Description |
|------|------------|
| post-9 | Org increases units_needed from 2 to 5 |
| | Top-up broadcast for additional 3 units worth of donors |
| | Existing responses preserved |

### AF9 — Donor Marked as No-Show

| Step | Description |
|------|------------|
| 22 | Organization marks donor as No-Show instead of Medical Results |
| | Response → NO_SHOW |
| | No-show penalty applied in future scoring (denominator penalty) |

### AF10 — Medical Results: Temporary Ineligibility

| Step | Description |
|------|------------|
| 24 | Eligibility: Temporary, Reason: Low hemoglobin, Delay: 2 weeks |
| 25 | Response → DECLINED, `is_eligible = false`, `next_eligible_date` = today + 14 days |
| | `EligibilityLog` created (type: LAB_VERIFICATION) |
| | `DonorIneligibilityNotification` sent to donor |

### AF11 — Medical Results: Permanent Ineligibility

| Step | Description |
|------|------------|
| 24 | Eligibility: Permanent, Reason: Chronic disease detected |
| 25 | Response → DECLINED, `chronic_disease = true`, `is_eligible = false`, `next_eligible_date = null` |
| | `EligibilityLog` created with `is_permanent = true` |
| | `DonorIneligibilityNotification` sent with permanent flag |
| | Donor redirected to IneligibleDonor page on next login |

### AF12 — ML Scoring Active (FastAPI Available)

| Step | Description |
|------|------------|
| 8 | `ml_scoring_enabled = true`, FastAPI healthy |
| 8a | Level 1: Check DB cache (some cached) |
| 8b | Level 2: FastAPI XGBoost scoring for uncached donors |
| 8c | Cold-start detection from ML model response |
| 8d | Cache updated in `donor_predictive_scores` |

### AF13 — Partial Fulfillment

| Step | Description |
|------|------------|
| 25 | 1 of 2 units completed; request remains BROADCASTED |
| 26 | Request continues to accept donor responses for remaining 1 unit |
| 27 | Second completion → FULFILLED |

---

## 8. Exception/Error Flows

### EF1 — No Eligible Donors Found

| Step | Description |
|------|------------|
| 7 | Progressive expansion reaches MAX_SEARCH_RADIUS_KM (25 km), zero eligible donors found |
| 8 | `scoreAndSelect()` receives empty donor list, returns empty selection |
| 9 | Status still transitions to BROADCASTED (broadcast "completed" with 0 notifications) |
| 10 | Organization sees request with 0 responses; may need to re-broadcast or cancel |

### EF2 — Broadcast Failure (Exception)

| Step | Description |
|------|------------|
| 5 | `broadcastToEligibleDonors()` throws exception |
| 6 | Request remains in PENDING status |
| 7 | Organization shown error notification |
| 8 | Request can be re-saved to retry broadcast |

### EF3 — FastAPI Circuit Breaker Opens

| Step | Description |
|------|------------|
| 8 | FastAPI fails 3 consecutive times |
| | Circuit breaker state → OPEN |
| | File cache keys updated: `fastapi_circuit:state = open`, `fastapi_circuit:failures = 3` |
| | Scoring falls through to Level 3 (rule-based PHP) — no donor scoring failure |
| | After 120s recovery window: state → HALF_OPEN → single test request |

### EF4 — Donor Accepts After Request Fulfilled

| Step | Description |
|------|------------|
| 14 | Donor clicks Accept on a request that was just fulfilled (race condition) |
| 15 | `BloodRequestActionService::accept()` checks `isActive()` — request not active |
| 16 | Error response: "This request is no longer active" |

### EF5 — Donor Accepts While Holding Active Response

| Step | Description |
|------|------------|
| 14 | Donor already has a PENDING response to another request |
| 15 | `accept()` checks for existing active responses — found |
| 16 | Error: "You already have an active response to another request" |

### EF6 — Ineligible Donor Attempts to Accept

| Step | Description |
|------|------------|
| 14 | Donor's `is_eligible = false` or `next_eligible_date > today` |
| 15 | Accept button is disabled in UI; if bypassed, `accept()` returns eligibility error |

### EF7 — QR Code Expired

| Step | Description |
|------|------------|
| 19 | Organization scans QR code after 7-day expiry |
| 20 | `QRCodeService::validate()` detects expiration |
| 21 | Error: "QR code has expired" |
| | Donor must re-accept (if request still active) to get new QR |

### EF8 — QR Scan Rate Limit Exceeded

| Step | Description |
|------|------------|
| 19 | Organization has scanned 30 QR codes in the last minute |
| 20 | Rate limiter blocks the request |
| 21 | Error: "Too many scan attempts. Please try again in X seconds" |

### EF9 — QR Code Belongs to Different Organization

| Step | Description |
|------|------------|
| 19 | Organization scans a QR code for a request owned by a different org |
| 20 | `validate()` checks organization ownership — mismatch |
| 21 | Error: "This QR code does not belong to your organization" |

### EF10 — Request Expires Before Fulfillment

| Step | Description |
|------|------------|
| post-9 | 48 hours pass without fulfillment |
| | `blood-requests:expire` command runs |
| | Status → EXPIRED |
| | `CancelExcessResponsesJob`: PENDING responses → NOT_NEEDED |
| | `ResponseNotNeededNotification` sent to donors |

### EF11 — Stale Response Cleanup

| Step | Description |
|------|------------|
| post-15 | Donor accepted but 48h passed (normal) or 8h (critical) without appearing |
| | `blood:cleanup-stale-responses` command runs |
| | Response → UNREACHABLE |

### EF12 — Duplicate Notification Prevention

| Step | Description |
|------|------------|
| 11 | `DispatchBloodRequestNotifications` job re-validates that donor has not already responded |
| | If donor already responded between scoring and dispatch, notification is skipped |

### EF13 — Organization Not Approved

| Step | Description |
|------|------------|
| 1 | Organization with `approval_status = PENDING` or `REJECTED` tries to access panel |
| 2 | `CheckOrganizationApproved` middleware redirects to PendingApproval page |
| 3 | Cannot create blood requests |

### EF14 — Request Cancelled by Organization

| Step | Description |
|------|------------|
| post-9 | Organization cancels a BROADCASTED request |
| | Status → CANCELLED |
| | Pending responses → NOT_NEEDED via `CancelExcessResponsesJob` |

---

## 9. Detailed Test Cases

### Legend

- **Priority:** P1 (Critical) / P2 (High) / P3 (Medium) / P4 (Low)
- **Severity:** S1 (Blocker) / S2 (Critical) / S3 (Major) / S4 (Minor)
- **Status:** Not Executed (default)
- **Actual Result:** To be filled during execution

---

### 9.1 Request Creation — Positive Scenarios

#### TC-CR-001: Create Blood Request with All Valid Fields

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-001 |
| **Module / Feature** | Blood Request Creation |
| **Scenario Title** | Successful creation with all fields populated |
| **Objective** | Verify that an organization can create a blood request with all valid data and it transitions to BROADCASTED |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED; user is authenticated; < 5 requests created today |
| **Test Data** | Blood Type: A+, Units: 3, Urgency: NORMAL, Radius: 15 km, Lat: 31.9539, Lng: 35.9106, Notes (EN): "Surgery scheduled tomorrow", Notes (AR): "عملية جراحية مقررة غداً", Address (EN): "Al-Bashir Hospital" |
| **Steps** | 1. Login as organization user → `/org/{slug}`<br>2. Navigate to Blood Requests → Create<br>3. Select Blood Type: A+<br>4. Enter Units Needed: 3<br>5. Select Urgency: NORMAL<br>6. Set search radius: 15 km<br>7. Pin location on map (lat: 31.9539, lng: 35.9106)<br>8. Enter address (EN/AR)<br>9. Enter additional notes (EN/AR)<br>10. Click "Create" |
| **Expected Result** | - Request saved in DB with all fields<br>- Status = BROADCASTED<br>- `broadcasted_at` is set<br>- `actual_search_radius_km` is recorded<br>- Success notification shown to org user<br>- Eligible donors receive `BloodRequestMatchNotification` |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Core happy path. Must pass for system to function. |

---

#### TC-CR-002: Create Critical Urgency Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-002 |
| **Module / Feature** | Blood Request Creation |
| **Scenario Title** | Successful creation of CRITICAL urgency request |
| **Objective** | Verify CRITICAL urgency uses ×3 radius multiplier and ×2.5 donor target |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED; eligible donors exist within 30 km |
| **Test Data** | Blood Type: O-, Units: 2, Urgency: CRITICAL, Radius: 10 km |
| **Steps** | 1. Login as organization user<br>2. Create blood request with Urgency: CRITICAL, Radius: 10 km<br>3. Click "Create" |
| **Expected Result** | - Request created and BROADCASTED<br>- Effective initial search radius = 10 × 3 = 30 km<br>- Target donors = 2 × 2.5 = 5<br>- Notification budget = 20 × 1.5 = 30<br>- Notification cooldown = 30 min (vs 2h for normal) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Verify multipliers by checking `actual_search_radius_km` and notification count in DB. |

---

#### TC-CR-003: Create Request with Minimum Valid Values

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-003 |
| **Module / Feature** | Blood Request Creation |
| **Scenario Title** | Boundary: minimum units (1), minimum radius (1 km) |
| **Objective** | Verify minimum boundary values are accepted |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED |
| **Test Data** | Blood Type: B+, Units: 1, Urgency: NORMAL, Radius: 1 km, No GPS, No notes |
| **Steps** | 1. Login as organization user<br>2. Create request with minimum values, no optional fields<br>3. Click "Create" |
| **Expected Result** | - Request created successfully<br>- Units = 1, radius = 1<br>- Governorate fallback used for matching if no GPS<br>- Status = BROADCASTED |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Tests lower boundary and optional field handling. |

---

#### TC-CR-004: Create Request with Maximum Valid Values

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-004 |
| **Module / Feature** | Blood Request Creation |
| **Scenario Title** | Boundary: maximum units (100), maximum radius (100 km) |
| **Objective** | Verify maximum boundary values are accepted |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED |
| **Test Data** | Blood Type: AB+, Units: 100, Urgency: CRITICAL, Radius: 100 km |
| **Steps** | 1. Login as organization user<br>2. Create request with Units: 100, Radius: 100 km<br>3. Click "Create" |
| **Expected Result** | - Request created successfully<br>- Target donors = 100 × 2.5 = 250 (capped by notification budget)<br>- System handles large donor pool without performance degradation |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | May hit notification budget cap (30 for critical). Verify system stability. |

---

#### TC-CR-005: Create Request for Each Blood Type

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-005 |
| **Module / Feature** | Blood Request Creation |
| **Scenario Title** | Verify all 9 blood types can be selected and broadcast correctly |
| **Objective** | Ensure compatibility matrix is applied correctly per blood type |
| **Actor / Role** | Organization User |
| **Preconditions** | Donors of all blood types exist in system |
| **Test Data** | Iterate: O+, O-, A+, A-, B+, B-, AB+, AB-, UNKNOWN |
| **Steps** | For each blood type:<br>1. Create request with that blood type<br>2. Verify donors notified match the compatibility matrix<br>3. Verify correct compatible donor types are queried |
| **Expected Result** | - O+ request notifies O+, O- donors<br>- AB+ request notifies all 8 types<br>- UNKNOWN request: no compatible donors by default (system creates it but may find 0) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | Critical for donor safety — wrong blood type matching is a blocker. |

---

### 9.2 Request Creation — Negative / Validation Scenarios

#### TC-CR-010: Create Request Without Blood Type

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-010 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Missing required field: blood_type |
| **Objective** | Verify form rejects submission when blood type is not selected |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED |
| **Test Data** | Blood Type: (empty), Units: 2, Urgency: NORMAL |
| **Steps** | 1. Login as organization user<br>2. Open create form<br>3. Leave blood type unselected<br>4. Fill other required fields<br>5. Click "Create" |
| **Expected Result** | - Form validation error on blood_type field<br>- Request NOT created in DB<br>- Error message displayed: "The blood type field is required" |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-CR-011: Create Request with Units = 0

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-011 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Boundary violation: units_needed below minimum (0) |
| **Objective** | Verify units_needed rejects values below 1 |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED |
| **Test Data** | Units: 0 |
| **Steps** | 1. Open create form<br>2. Enter Units Needed: 0<br>3. Fill other required fields<br>4. Click "Create" |
| **Expected Result** | - Validation error: "The units needed must be at least 1"<br>- Request NOT saved |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-CR-012: Create Request with Units = 101

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-012 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Boundary violation: units_needed above maximum (101) |
| **Objective** | Verify units_needed rejects values above 100 |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization is APPROVED |
| **Test Data** | Units: 101 |
| **Steps** | 1. Enter Units Needed: 101<br>2. Click "Create" |
| **Expected Result** | - Validation error: "The units needed must not be greater than 100" |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-CR-013: Create Request with Negative Units

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-013 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Invalid input: negative units_needed |
| **Objective** | Verify system rejects negative values |
| **Actor / Role** | Organization User |
| **Test Data** | Units: -5 |
| **Steps** | 1. Enter Units Needed: -5<br>2. Click "Create" |
| **Expected Result** | - Validation error<br>- Request NOT saved |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-CR-014: Create Request with Non-Numeric Units

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-014 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Invalid input: non-numeric units_needed |
| **Objective** | Verify system rejects non-numeric input |
| **Actor / Role** | Organization User |
| **Test Data** | Units: "abc" |
| **Steps** | 1. Enter Units Needed: "abc"<br>2. Click "Create" |
| **Expected Result** | - Validation error: "The units needed must be a number" |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | HTML input type=number may prevent this at UI level; test via API. |

---

#### TC-CR-015: Create Request with Radius = 0

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-015 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Boundary violation: search_radius_km below minimum |
| **Objective** | Verify radius rejects 0 |
| **Actor / Role** | Organization User |
| **Test Data** | Radius: 0 |
| **Steps** | 1. Enter Search Radius: 0<br>2. Click "Create" |
| **Expected Result** | - Validation error: "The search radius must be at least 1" |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | — |

---

#### TC-CR-016: Create Request with Radius = 101

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-016 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Boundary violation: search_radius_km above maximum |
| **Objective** | Verify radius rejects values above 100 |
| **Actor / Role** | Organization User |
| **Test Data** | Radius: 101 |
| **Steps** | 1. Enter Search Radius: 101<br>2. Click "Create" |
| **Expected Result** | - Validation error: "The search radius must not be greater than 100" |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | — |

---

#### TC-CR-017: Exceed Daily Request Limit

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-017 |
| **Module / Feature** | Blood Request Creation — Rate Limiting |
| **Scenario Title** | Organization exceeds org_max_requests_per_day (5) |
| **Objective** | Verify system enforces daily request limit |
| **Actor / Role** | Organization User |
| **Preconditions** | Organization has already created 5 requests today |
| **Test Data** | 6th request attempt |
| **Steps** | 1. Create 5 blood requests successfully<br>2. Attempt to create 6th request |
| **Expected Result** | - 6th request is rejected<br>- Error message about daily limit |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Verify limit resets at midnight. |

---

#### TC-CR-018: Create Request with Only Lat (Missing Lng)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CR-018 |
| **Module / Feature** | Blood Request Creation — Validation |
| **Scenario Title** | Partial GPS: lat provided without lng |
| **Objective** | Verify both lat and lng must be provided together |
| **Actor / Role** | Organization User |
| **Test Data** | Lat: 31.95, Lng: (empty) |
| **Steps** | 1. Set lat via map/field without lng<br>2. Click "Create" |
| **Expected Result** | - Validation error or governorate fallback used<br>- No partial GPS stored |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | — |

---

### 9.3 Broadcasting and Donor Matching

#### TC-BC-001: Progressive Radius Expansion

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-001 |
| **Module / Feature** | Broadcasting — Radius Expansion |
| **Scenario Title** | Verify radius expands by 5 km steps until target is met |
| **Objective** | Ensure progressive expansion logic works correctly |
| **Actor / Role** | System |
| **Preconditions** | 2 eligible donors at 10 km, 2 at 18 km; request with radius 10 km, units 3 (target = 6 donors) |
| **Test Data** | Blood Type: O+, Units: 3, Radius: 10 km |
| **Steps** | 1. Create request<br>2. System broadcasts<br>3. Observe expansion steps |
| **Expected Result** | - Step 1: 10 km → 2 donors (< 6 target)<br>- Step 2: 15 km → 2 donors (< 6)<br>- Step 3: 20 km → 4 donors (may stop or continue to 25 km max)<br>- `actual_search_radius_km` reflects final radius<br>- `expansion_steps` recorded correctly |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-BC-002: Max Radius Cap at 25 km

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-002 |
| **Module / Feature** | Broadcasting — Radius Expansion |
| **Scenario Title** | Expansion stops at MAX_SEARCH_RADIUS_KM (25 km) |
| **Objective** | Verify radius never exceeds 25 km regardless of donor shortage |
| **Actor / Role** | System |
| **Preconditions** | Only 1 eligible donor at 30 km; request radius 10 km, units 5 |
| **Test Data** | Low donor density area |
| **Steps** | 1. Create request needing 10 donors (5 × 2.0)<br>2. Observe expansion |
| **Expected Result** | - Expansion reaches 25 km and stops<br>- Whatever donors found within 25 km are selected<br>- Request still BROADCASTED even if target not met |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-BC-003: Blood Type Compatibility — A+ Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-003 |
| **Module / Feature** | Broadcasting — Donor Matching |
| **Scenario Title** | A+ request matches only A+, A-, O+, O- donors |
| **Objective** | Verify correct compatibility matrix application |
| **Actor / Role** | System |
| **Preconditions** | Donors of all 8 blood types within radius |
| **Test Data** | Request blood type: A+ |
| **Steps** | 1. Create A+ request<br>2. Check which donors received notifications |
| **Expected Result** | - Only A+, A-, O+, O- donors notified<br>- B+, B-, AB+, AB- donors NOT notified |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Blood type safety — critical patient safety test. |

---

#### TC-BC-004: Blood Type Compatibility — O- Request (Most Restrictive)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-004 |
| **Module / Feature** | Broadcasting — Donor Matching |
| **Scenario Title** | O- request matches only O- donors |
| **Objective** | Verify most restrictive compatibility |
| **Actor / Role** | System |
| **Test Data** | Request blood type: O- |
| **Steps** | 1. Create O- request<br>2. Verify only O- donors notified |
| **Expected Result** | - Only O- donors receive notifications<br>- All other types excluded |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-BC-005: Blood Type Compatibility — AB+ Request (Universal Recipient)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-005 |
| **Module / Feature** | Broadcasting — Donor Matching |
| **Scenario Title** | AB+ request matches all 8 blood types |
| **Objective** | Verify universal recipient compatibility |
| **Actor / Role** | System |
| **Test Data** | Request blood type: AB+ |
| **Steps** | 1. Create AB+ request<br>2. Verify all-type donors notified |
| **Expected Result** | - Donors of all 8 types (O+, O-, A+, A-, B+, B-, AB+, AB-) notified |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-BC-006: Notification Cooldown — Normal Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-006 |
| **Module / Feature** | Broadcasting — Cooldown |
| **Scenario Title** | Donor notified < 2h ago skipped for NORMAL request |
| **Objective** | Verify 2-hour cooldown for normal requests |
| **Actor / Role** | System |
| **Preconditions** | Donor received a notification 1 hour ago |
| **Test Data** | Blood Type: A+, Urgency: NORMAL |
| **Steps** | 1. Send notification to donor (record timestamp)<br>2. Create NORMAL request 1 hour later<br>3. Verify donor is excluded from matching |
| **Expected Result** | - Donor NOT included in eligible set<br>- After 2h cooldown passes, donor becomes eligible again |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-BC-007: Notification Cooldown — Critical Request (30 min)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-007 |
| **Module / Feature** | Broadcasting — Cooldown |
| **Scenario Title** | CRITICAL request has shorter cooldown (30 min) |
| **Objective** | Verify critical requests use reduced cooldown |
| **Actor / Role** | System |
| **Preconditions** | Donor notified 45 minutes ago |
| **Test Data** | Urgency: CRITICAL |
| **Steps** | 1. Donor notified 45 min ago<br>2. Create CRITICAL request<br>3. Check if donor is included |
| **Expected Result** | - Donor IS included (45 min > 30 min cooldown) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-BC-008: UNKNOWN Donors as Fallback — Normal Only

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-008 |
| **Module / Feature** | Broadcasting — Fallback Logic |
| **Scenario Title** | UNKNOWN blood type donors included for NORMAL requests when target not met |
| **Objective** | Verify UNKNOWN fallback behavior and CRITICAL exclusion |
| **Actor / Role** | System |
| **Preconditions** | Few compatible donors; some UNKNOWN donors in range |
| **Test Data** | Two requests: one NORMAL, one CRITICAL, both with low compatible donor count |
| **Steps** | 1. Create NORMAL request with insufficient compatible donors<br>2. Verify UNKNOWN donors are included<br>3. Create CRITICAL request with same conditions<br>4. Verify UNKNOWN donors are NOT included |
| **Expected Result** | - NORMAL: UNKNOWN donors added to pool after compatible types exhausted<br>- CRITICAL: UNKNOWN donors never included (safety) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Patient safety implication for CRITICAL requests. |

---

#### TC-BC-009: Donor Already Responded — Excluded from Matching

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-009 |
| **Module / Feature** | Broadcasting — Deduplication |
| **Scenario Title** | Donor who already responded to this request is not re-notified |
| **Objective** | Prevent duplicate notifications |
| **Actor / Role** | System |
| **Preconditions** | Donor has existing response (any status) to this request |
| **Test Data** | Re-broadcast scenario after edit |
| **Steps** | 1. Request is BROADCASTED, donor responded<br>2. Org edits request (triggers re-broadcast)<br>3. Check if donor is re-notified |
| **Expected Result** | - Donor with existing response is excluded from re-broadcast matching |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Exception: donor with IGNORED response CAN be re-included per AF5. |

---

#### TC-BC-010: Zero Eligible Donors Found

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-BC-010 |
| **Module / Feature** | Broadcasting — Edge Case |
| **Scenario Title** | No eligible donors exist within max radius |
| **Objective** | Verify graceful handling when no donors match |
| **Actor / Role** | System |
| **Preconditions** | No eligible donors of compatible type within 25 km |
| **Test Data** | Rare blood type (O-) in area with no O- donors |
| **Steps** | 1. Create O- request<br>2. Observe broadcast process |
| **Expected Result** | - Status still → BROADCASTED<br>- 0 notifications sent<br>- No errors or exceptions<br>- Org sees request with 0 responses |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

### 9.4 Scoring Waterfall

#### TC-SC-001: Level 1 — Cached Score Used

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-001 |
| **Module / Feature** | Donor Scoring |
| **Scenario Title** | Donor with fresh cached score (< 7 days) uses Level 1 |
| **Objective** | Verify DB cache is the first scoring source |
| **Actor / Role** | System |
| **Preconditions** | `donor_predictive_scores` record exists, updated 3 days ago |
| **Test Data** | Donor with cached score = 0.85 |
| **Steps** | 1. Trigger broadcast<br>2. Observe scoring source for this donor |
| **Expected Result** | - Score = 0.85 from cache<br>- `ScoringResult.source = "cache"`<br>- FastAPI not called for this donor |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-SC-002: Level 2 — FastAPI ML Scoring

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-002 |
| **Module / Feature** | Donor Scoring |
| **Scenario Title** | No cache, ML enabled and healthy → FastAPI scoring |
| **Objective** | Verify ML scoring fallback when cache is stale/missing |
| **Actor / Role** | System |
| **Preconditions** | `ml_scoring_enabled = true`, FastAPI running, no cached score |
| **Test Data** | Donor without cached score |
| **Steps** | 1. Ensure no `donor_predictive_scores` record<br>2. Trigger broadcast<br>3. Observe scoring |
| **Expected Result** | - FastAPI called with donor features<br>- Score returned (0–1)<br>- `ScoringResult.source = "ml"`<br>- Cache updated in `donor_predictive_scores` |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | Requires FastAPI sidecar running. |

---

#### TC-SC-003: Level 3 — Rule-Based PHP Scoring

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-003 |
| **Module / Feature** | Donor Scoring |
| **Scenario Title** | No cache, ML disabled → rule-based scoring |
| **Objective** | Verify rule-based formula: (acceptance_rate × 0.50) + (recency × 0.30) + (loyalty × 0.20) |
| **Actor / Role** | System |
| **Preconditions** | `ml_scoring_enabled = false`, no cache |
| **Test Data** | Donor with: 8/10 acceptance rate, last donation 5 days ago, 7 total donations |
| **Steps** | 1. Trigger broadcast<br>2. Calculate expected score manually:<br>   - Acceptance: 8/10 = 0.80 × 0.50 = 0.40<br>   - Recency (5d ≤ 7d): 1.0 × 0.30 = 0.30<br>   - Loyalty: min(7/10, 1.0) = 0.7 × 0.20 = 0.14<br>   - Total: 0.84<br>3. Verify system score matches |
| **Expected Result** | - Score ≈ 0.84<br>- `ScoringResult.source = "rule_based"` |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-SC-004: Level 4 — Neutral 0.5 Fallback

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-004 |
| **Module / Feature** | Donor Scoring |
| **Scenario Title** | All scoring levels fail → neutral 0.5 |
| **Objective** | Verify system never fails to score a donor |
| **Actor / Role** | System |
| **Preconditions** | Cache miss, ML disabled or circuit open, rule-based scoring throws exception |
| **Test Data** | Donor with corrupted/missing response history |
| **Steps** | 1. Force all scoring levels to fail (mock/simulate)<br>2. Verify donor receives neutral score |
| **Expected Result** | - Score = 0.5<br>- `ScoringResult::neutral()` used<br>- Donor still included in selection |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | Robustness test. |

---

#### TC-SC-005: Epsilon-Greedy Selection

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-005 |
| **Module / Feature** | Donor Scoring — Selection |
| **Scenario Title** | Verify 80/20 exploit/explore split |
| **Objective** | Ensure cold-start donors get exploration slots |
| **Actor / Role** | System |
| **Preconditions** | 20 scored donors; 4 are cold-start (< 5 responses) |
| **Test Data** | Budget = 20, exploration_ratio = 0.20 |
| **Steps** | 1. Score 20 donors<br>2. Observe selection buckets |
| **Expected Result** | - 16 exploitation slots (top-scored non-cold-start)<br>- 4 exploration slots (cold-start + bottom epsilon%)<br>- Cold-start donors guaranteed exploration placement |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | — |

---

#### TC-SC-006: Circuit Breaker Opens After 3 FastAPI Failures

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-006 |
| **Module / Feature** | Circuit Breaker |
| **Scenario Title** | FastAPI fails 3 times → circuit opens → falls back to rule-based |
| **Objective** | Verify circuit breaker protects against cascading failures |
| **Actor / Role** | System |
| **Preconditions** | `ml_scoring_enabled = true`, FastAPI is down |
| **Test Data** | 3 consecutive broadcast attempts |
| **Steps** | 1. Trigger broadcast (FastAPI fails → failure count = 1, uses rule-based)<br>2. Trigger another broadcast (fails again → count = 2)<br>3. Trigger third (fails → count = 3 → circuit OPEN)<br>4. Verify cache keys: `fastapi_circuit:state = open`<br>5. Trigger fourth → FastAPI not called at all, direct rule-based |
| **Expected Result** | - After 3 failures: circuit OPEN<br>- Subsequent requests skip FastAPI entirely<br>- No donor scoring failures — all fall back to Level 3 |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-SC-007: Circuit Breaker Recovery (Half-Open)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SC-007 |
| **Module / Feature** | Circuit Breaker |
| **Scenario Title** | After 120s recovery, circuit enters HALF_OPEN and tests FastAPI |
| **Objective** | Verify recovery window transitions |
| **Actor / Role** | System |
| **Preconditions** | Circuit is OPEN, FastAPI restarted |
| **Test Data** | Wait 120+ seconds after circuit opened |
| **Steps** | 1. Open circuit (3 failures)<br>2. Travel forward 121 seconds<br>3. Trigger new broadcast<br>4. Verify circuit state transitions |
| **Expected Result** | - State → HALF_OPEN<br>- Single test request to FastAPI<br>- If success: state → CLOSED, failure count reset<br>- If failure: state → OPEN, new recovery window |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | Use `Carbon::setTestNow()` for time travel. |

---

### 9.5 Donor Response — Accept

#### TC-DR-001: Donor Accepts Request Successfully

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-001 |
| **Module / Feature** | Donor Response — Accept |
| **Scenario Title** | Eligible donor accepts an active blood request |
| **Objective** | Verify full accept flow: response creation, QR generation, org notification |
| **Actor / Role** | Donor |
| **Preconditions** | Donor is eligible, has no active responses, request is BROADCASTED |
| **Test Data** | Eligible A+ donor, active A+ request |
| **Steps** | 1. Login as donor → `/donor`<br>2. Navigate to Blood Requests page<br>3. See compatible A+ request in table<br>4. Click "Accept" button<br>5. Observe success notification |
| **Expected Result** | - `RequestResponse` created with status = PENDING<br>- `responded_at` set to now<br>- `verification_qr_code` generated (32-char hex)<br>- `qr_code_expires_at` set to responded_at + 7 days<br>- `DonorResponseNotification` sent to organization<br>- Success notification shown to donor with QR download hint |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Core happy path for donor interaction. |

---

#### TC-DR-002: Donor Cannot Accept While Holding Active Response

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-002 |
| **Module / Feature** | Donor Response — Accept |
| **Scenario Title** | Donor with existing PENDING response cannot accept another |
| **Objective** | Verify single-active-response constraint |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has PENDING response to Request A |
| **Test Data** | Donor tries to accept Request B |
| **Steps** | 1. Donor accepts Request A (PENDING response exists)<br>2. Donor views Request B (also compatible)<br>3. Donor clicks "Accept" on Request B |
| **Expected Result** | - Error: already has an active response<br>- No new response created<br>- Request A response unaffected |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Also test with ACCEPTED status (after QR scan) — should also block. |

---

#### TC-DR-003: Ineligible Donor Cannot Accept

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-003 |
| **Module / Feature** | Donor Response — Accept |
| **Scenario Title** | Donor with is_eligible=false attempts to accept |
| **Objective** | Verify eligibility check in accept action |
| **Actor / Role** | Donor |
| **Preconditions** | Donor `is_eligible = false`, `next_eligible_date` is 30 days from now |
| **Test Data** | Ineligible donor (recent donation) |
| **Steps** | 1. Login as ineligible donor<br>2. View blood requests (should see empty or disabled)<br>3. If UI bypassed, attempt accept via service |
| **Expected Result** | - Accept button disabled in UI<br>- If bypassed: error returned<br>- No response created |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-DR-004: Donor Accepts Already Fulfilled Request (Race Condition)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-004 |
| **Module / Feature** | Donor Response — Accept |
| **Scenario Title** | Request fulfilled between page load and accept click |
| **Objective** | Verify race condition handling |
| **Actor / Role** | Donor |
| **Preconditions** | Request is BROADCASTED when donor loads page; another donor fulfills it before accept |
| **Test Data** | Request with 1 unit needed, already fulfilled by Donor A |
| **Steps** | 1. Donor B loads page (request shown as active)<br>2. Meanwhile, Donor A completes donation → request FULFILLED<br>3. Donor B clicks "Accept" |
| **Expected Result** | - `isActive()` returns false<br>- Error: "This request is no longer active"<br>- No response created |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | Simulate with concurrent test or time-controlled scenario. |

---

#### TC-DR-005: Donor Accepts Request After Ignoring It

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-005 |
| **Module / Feature** | Donor Response — Accept After Ignore |
| **Scenario Title** | Donor ignores then later accepts same request |
| **Objective** | Verify IGNORED donors can re-accept |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has IGNORED response to this request |
| **Test Data** | Same request, same donor |
| **Steps** | 1. Donor ignores request (IGNORED status)<br>2. Later, donor views same request<br>3. Donor clicks "Accept" |
| **Expected Result** | - Previous IGNORED response updated to PENDING<br>- New QR code generated<br>- Org notified |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Per BloodRequests page: IGNORED donors see Accept button. |

---

### 9.6 Donor Response — Ignore / Cancel

#### TC-DR-010: Donor Ignores Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-010 |
| **Module / Feature** | Donor Response — Ignore |
| **Scenario Title** | Donor declines a blood request |
| **Objective** | Verify ignore flow and QR revocation |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has no response to this request; request is active |
| **Test Data** | Active request |
| **Steps** | 1. View blood request<br>2. Click "Ignore/Decline"<br>3. Confirm |
| **Expected Result** | - Response created with status = IGNORED<br>- No QR code generated<br>- If QR existed (unlikely), it's revoked |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | — |

---

#### TC-DR-011: Donor Cancels Accepted Response

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-011 |
| **Module / Feature** | Donor Response — Cancel |
| **Scenario Title** | Donor cancels after accepting (PENDING status) |
| **Objective** | Verify cancel flow: response deletion, QR revocation, slot freed |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has PENDING response with valid QR code |
| **Test Data** | Active response |
| **Steps** | 1. View blood requests<br>2. See "Cancel Acceptance" button<br>3. Click it, confirm in dialog<br>4. Observe results |
| **Expected Result** | - Response record deleted from DB<br>- QR code revoked<br>- Donor can now accept other requests<br>- Donor can re-accept this request if still active |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Cancel is only available from PENDING status, not ACCEPTED/COMPLETED. |

---

#### TC-DR-012: Cancel Not Available After QR Scan (ACCEPTED Status)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DR-012 |
| **Module / Feature** | Donor Response — Cancel |
| **Scenario Title** | Donor cannot cancel after being admitted (ACCEPTED) |
| **Objective** | Verify cancel button hidden after admission |
| **Actor / Role** | Donor |
| **Preconditions** | Response status = ACCEPTED (QR scanned, verified_at set) |
| **Test Data** | Admitted response |
| **Steps** | 1. Donor views their response (ACCEPTED)<br>2. Check for cancel button |
| **Expected Result** | - Cancel button NOT visible<br>- No cancel action available |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | — |

---

### 9.7 QR Code Verification

#### TC-QR-001: Download QR Code as SVG

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-001 |
| **Module / Feature** | QR Code — Download |
| **Scenario Title** | Donor downloads QR code after accepting |
| **Objective** | Verify QR SVG generation and download |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has PENDING response with valid QR code |
| **Test Data** | PENDING response, QR not expired, not verified |
| **Steps** | 1. View blood requests<br>2. Click "Download QR" action<br>3. File downloads |
| **Expected Result** | - SVG file downloaded as `bloodbridge-qr-{request_id}.svg`<br>- QR encodes the verification token<br>- File is valid SVG format |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | QR download only visible when: PENDING + QR valid + not expired + not verified. |

---

#### TC-QR-002: Successful QR Scan and Admission

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-002 |
| **Module / Feature** | QR Code — Scan |
| **Scenario Title** | Organization successfully scans donor QR and confirms admission |
| **Objective** | Verify complete scan-to-admission flow |
| **Actor / Role** | Organization |
| **Preconditions** | Donor has PENDING response, valid QR, org is request owner |
| **Test Data** | Valid 32-char hex QR token |
| **Steps** | 1. Login as organization<br>2. Navigate to Scan Donor QR page<br>3. Scan/enter QR code<br>4. System shows donor details and request info<br>5. Click "Confirm Admission" |
| **Expected Result** | - Response status → ACCEPTED<br>- `verified_at` set to current timestamp<br>- Org notification: "Donor arrived"<br>- Donor info displayed (name, blood type, request) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-QR-003: Scan Expired QR Code

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-003 |
| **Module / Feature** | QR Code — Validation |
| **Scenario Title** | QR code scanned after 7-day expiry |
| **Objective** | Verify expired QR rejection |
| **Actor / Role** | Organization |
| **Preconditions** | QR `qr_code_expires_at` < now |
| **Test Data** | QR created 8 days ago |
| **Steps** | 1. Scan expired QR code |
| **Expected Result** | - Error: "QR code has expired"<br>- No status change<br>- Donor must re-accept for new QR |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Use time travel to simulate. |

---

#### TC-QR-004: Scan QR Belonging to Different Organization

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-004 |
| **Module / Feature** | QR Code — Authorization |
| **Scenario Title** | Org A scans QR code for Org B's request |
| **Objective** | Verify cross-organization QR rejection |
| **Actor / Role** | Organization A |
| **Preconditions** | QR code was generated for Org B's request |
| **Test Data** | Valid QR token from another org's request |
| **Steps** | 1. Org A opens scanner<br>2. Scans QR code from Org B's request |
| **Expected Result** | - Error: "This QR code does not belong to your organization"<br>- No status change |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Security-critical: prevents unauthorized admissions. |

---

#### TC-QR-005: Scan Invalid/Nonexistent QR Token

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-005 |
| **Module / Feature** | QR Code — Validation |
| **Scenario Title** | Random or fabricated QR token |
| **Objective** | Verify invalid token handling |
| **Actor / Role** | Organization |
| **Test Data** | Random string: "abc123def456" |
| **Steps** | 1. Enter invalid QR code string |
| **Expected Result** | - Error: "Invalid QR code"<br>- No DB changes |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-QR-006: QR Scan Rate Limit (30/min)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-006 |
| **Module / Feature** | QR Code — Rate Limiting |
| **Scenario Title** | 31st scan attempt within 1 minute is blocked |
| **Objective** | Verify rate limiter enforced per organization |
| **Actor / Role** | Organization |
| **Preconditions** | Organization scanned 30 QR codes in last 60 seconds |
| **Test Data** | 31st scan attempt |
| **Steps** | 1. Rapidly scan 30 QR codes (valid or invalid)<br>2. Attempt 31st scan |
| **Expected Result** | - 31st scan blocked<br>- Error: "Too many scan attempts. Please try again in X seconds"<br>- Rate limit logged as warning |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | Rate limit is per org, not per user. |

---

#### TC-QR-007: Scan QR for Already Verified Response

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-QR-007 |
| **Module / Feature** | QR Code — Duplicate Scan |
| **Scenario Title** | QR scanned for response already in ACCEPTED status |
| **Objective** | Verify re-scan prevention |
| **Actor / Role** | Organization |
| **Preconditions** | Response status = ACCEPTED, `verified_at` already set |
| **Test Data** | Previously verified QR code |
| **Steps** | 1. Scan same QR code again |
| **Expected Result** | - Error: donor already verified/admitted<br>- No duplicate status change |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | — |

---

### 9.8 Medical Results and Fulfillment

#### TC-MR-001: Mark Donor as Eligible — Donation Completed

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-001 |
| **Module / Feature** | Medical Results |
| **Scenario Title** | Donor passes lab tests and completes donation |
| **Objective** | Verify ACCEPTED → COMPLETED transition and fulfillment check |
| **Actor / Role** | Organization |
| **Preconditions** | Donor has ACCEPTED response (verified_at set) |
| **Test Data** | Blood type verified: A+, Eligibility: Eligible |
| **Steps** | 1. Open Responses tab for request<br>2. Select "Medical Results" action for donor<br>3. Select blood type: A+<br>4. Select eligibility: Eligible<br>5. Submit |
| **Expected Result** | - Response → COMPLETED<br>- `total_donations` incremented by 1 on DonorHealthProfile<br>- `verified_blood_type = A+` set on health profile<br>- If `donors_completed >= units_needed`: request → FULFILLED<br>- `EligibilityLog` created (type: LAB_VERIFICATION) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-MR-002: Mark Donor as Temporarily Ineligible

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-002 |
| **Module / Feature** | Medical Results |
| **Scenario Title** | Donor fails lab test — temporary exclusion |
| **Objective** | Verify temporary ineligibility flow |
| **Actor / Role** | Organization |
| **Preconditions** | Donor has ACCEPTED response |
| **Test Data** | Eligibility: Temporary, Reason: Low hemoglobin, Delay: 2 weeks |
| **Steps** | 1. Open Medical Results for donor<br>2. Eligibility: Temporary<br>3. Rejection reason: Low hemoglobin<br>4. Delay: 2 weeks<br>5. Submit |
| **Expected Result** | - Response → DECLINED<br>- `is_eligible = false` on DonorHealthProfile<br>- `next_eligible_date = today + 14 days`<br>- `EligibilityLog` created (is_permanent: false, rejection_reason: "Low hemoglobin")<br>- `DonorIneligibilityNotification` sent to donor |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-MR-003: Mark Donor as Permanently Ineligible

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-003 |
| **Module / Feature** | Medical Results |
| **Scenario Title** | Donor has chronic condition discovered at lab |
| **Objective** | Verify permanent ineligibility and middleware redirect |
| **Actor / Role** | Organization |
| **Preconditions** | Donor has ACCEPTED response |
| **Test Data** | Eligibility: Permanent, Reason: Chronic disease detected |
| **Steps** | 1. Open Medical Results<br>2. Eligibility: Permanent<br>3. Reason: Chronic disease detected<br>4. Submit |
| **Expected Result** | - Response → DECLINED<br>- `chronic_disease = true` on DonorHealthProfile<br>- `is_eligible = false`, `next_eligible_date = null`<br>- `EligibilityLog` (is_permanent: true)<br>- `DonorIneligibilityNotification` sent with permanent flag<br>- On next donor login: `CheckDonorIneligibility` redirects to IneligibleDonor page |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | — |

---

#### TC-MR-004: Blood Type Verification — First Time

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-004 |
| **Module / Feature** | Medical Results — Blood Type Verification |
| **Scenario Title** | Lab verifies donor blood type for the first time |
| **Objective** | Verify blood type set on health profile |
| **Actor / Role** | Organization |
| **Preconditions** | Donor `verified_blood_type = null` |
| **Test Data** | Lab-verified: B+ |
| **Steps** | 1. Open Medical Results<br>2. Select verified blood type: B+<br>3. Eligibility: Eligible<br>4. Submit |
| **Expected Result** | - `verified_blood_type = B+` set<br>- `verified_by_organization_id` set to this org<br>- `verified_at` set<br>- Future broadcasts use `verified_blood_type` for matching |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Blood type verification field is disabled if already verified. |

---

#### TC-MR-005: Mark Donor as No-Show

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-005 |
| **Module / Feature** | Response Management |
| **Scenario Title** | Donor accepted but never showed up |
| **Objective** | Verify no-show marking and scoring penalty |
| **Actor / Role** | Organization |
| **Preconditions** | Donor has PENDING response |
| **Test Data** | Donor who accepted but didn't appear |
| **Steps** | 1. Open Responses tab<br>2. Click "Mark No-Show" for donor<br>3. Confirm |
| **Expected Result** | - Response → NO_SHOW<br>- No-show contributes to scoring penalty in future broadcasts<br>- (acceptance_rate denominator includes no-show penalty) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-MR-006: Request Fulfillment Triggers Cleanup

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-MR-006 |
| **Module / Feature** | Fulfillment |
| **Scenario Title** | Last required unit completed → request FULFILLED → cleanup |
| **Objective** | Verify fulfillment cascade |
| **Actor / Role** | System |
| **Preconditions** | Request needs 2 units; 1 completed; 2 more donors are PENDING |
| **Test Data** | 2nd donor marked COMPLETED |
| **Steps** | 1. Mark 2nd donor as COMPLETED via Medical Results (eligible)<br>2. System checks: 2 completed = 2 needed<br>3. Observe cascading effects |
| **Expected Result** | - Request status → FULFILLED<br>- `fulfilled_at` set<br>- `CancelExcessResponsesJob` dispatched<br>- Remaining PENDING responses → NOT_NEEDED<br>- QR codes revoked for NOT_NEEDED responses<br>- `ResponseNotNeededNotification` sent to affected donors |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | End of happy path — critical lifecycle completion test. |

---

### 9.9 Request Edit and Re-broadcast

#### TC-ED-001: Edit Critical Field Triggers Full Re-broadcast

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-ED-001 |
| **Module / Feature** | Request Edit |
| **Scenario Title** | Changing blood type on BROADCASTED request triggers re-broadcast |
| **Objective** | Verify critical field edit behavior |
| **Actor / Role** | Organization |
| **Preconditions** | Request is BROADCASTED, has 3 PENDING responses |
| **Test Data** | Change blood_type from A+ to B+ |
| **Steps** | 1. Edit BROADCASTED request<br>2. Change Blood Type from A+ to B+<br>3. Save |
| **Expected Result** | - Old 3 PENDING responses cancelled (NOT_NEEDED)<br>- Full re-broadcast with B+ compatibility<br>- New eligible donors (B+, B-, O+, O-) notified<br>- `actual_search_radius_km` updated |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Critical fields: blood_type, urgency_level, lat, lng, search_radius_km, units_needed. |

---

#### TC-ED-002: Increase Units Triggers Top-Up Broadcast

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-ED-002 |
| **Module / Feature** | Request Edit |
| **Scenario Title** | Increasing units_needed on BROADCASTED request triggers supplementary broadcast |
| **Objective** | Verify top-up broadcast (not full re-broadcast) |
| **Actor / Role** | Organization |
| **Preconditions** | Request BROADCASTED, units_needed = 2, has 2 PENDING responses |
| **Test Data** | Increase units_needed from 2 to 5 |
| **Steps** | 1. Edit request<br>2. Change units from 2 to 5<br>3. Save |
| **Expected Result** | - Existing 2 responses preserved<br>- Top-up broadcast for additional 3 units worth of donors<br>- New donors notified, old responses untouched |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-ED-003: Edit Disabled for FULFILLED Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-ED-003 |
| **Module / Feature** | Request Edit |
| **Scenario Title** | Critical fields disabled on FULFILLED request |
| **Objective** | Verify edit restrictions on terminal states |
| **Actor / Role** | Organization |
| **Preconditions** | Request status = FULFILLED |
| **Test Data** | Fulfilled request |
| **Steps** | 1. Open edit page for FULFILLED request<br>2. Check field states |
| **Expected Result** | - blood_type, units_needed, urgency_level fields are disabled<br>- Only notes and non-critical fields editable |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S3 |
| **Notes** | Same for CANCELLED status. |

---

### 9.10 Expiry and Cleanup Commands

#### TC-EX-001: Request Expires After 48 Hours

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-EX-001 |
| **Module / Feature** | Expiry Command |
| **Scenario Title** | `blood-requests:expire` marks old requests as EXPIRED |
| **Objective** | Verify automatic expiry after 48h |
| **Actor / Role** | System (Scheduler) |
| **Preconditions** | Request created > 48h ago, status BROADCASTED, not fulfilled |
| **Test Data** | Request created 49 hours ago |
| **Steps** | 1. Create request (travel back 49h)<br>2. Run `php artisan blood-requests:expire`<br>3. Check request status |
| **Expected Result** | - Status → EXPIRED<br>- `CancelExcessResponsesJob` dispatched<br>- PENDING responses → NOT_NEEDED |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | Also test: PENDING request (not yet broadcasted) also expires. |

---

#### TC-EX-002: Stale PENDING Responses → UNREACHABLE (Normal)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-EX-002 |
| **Module / Feature** | Stale Response Cleanup |
| **Scenario Title** | PENDING response older than 48h for NORMAL request marked UNREACHABLE |
| **Objective** | Verify stale cleanup thresholds |
| **Actor / Role** | System (Scheduler) |
| **Preconditions** | PENDING response, `responded_at` > 48h ago, NORMAL urgency |
| **Test Data** | Response created 49 hours ago |
| **Steps** | 1. Set up PENDING response 49h old<br>2. Run `php artisan blood:cleanup-stale-responses`<br>3. Check response status |
| **Expected Result** | - Response status → UNREACHABLE |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-EX-003: Stale PENDING Responses → UNREACHABLE (Critical, 8h)

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-EX-003 |
| **Module / Feature** | Stale Response Cleanup |
| **Scenario Title** | PENDING response older than 8h for CRITICAL request marked UNREACHABLE |
| **Objective** | Verify shorter CRITICAL threshold |
| **Actor / Role** | System (Scheduler) |
| **Preconditions** | CRITICAL request, PENDING response `responded_at` 9h ago |
| **Test Data** | Critical response at 9 hours |
| **Steps** | 1. Set up PENDING response for CRITICAL request, 9h old<br>2. Run cleanup command<br>3. Check status |
| **Expected Result** | - Response → UNREACHABLE (9h > 8h threshold) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

#### TC-EX-004: Non-Stale Response Not Cleaned Up

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-EX-004 |
| **Module / Feature** | Stale Response Cleanup |
| **Scenario Title** | Fresh PENDING response (< threshold) not touched by cleanup |
| **Objective** | Verify cleanup only affects stale responses |
| **Actor / Role** | System |
| **Preconditions** | NORMAL request, PENDING response 24h old (< 48h threshold) |
| **Test Data** | Response at 24 hours |
| **Steps** | 1. Run cleanup command<br>2. Check response status |
| **Expected Result** | - Response remains PENDING<br>- Not marked as UNREACHABLE |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S3 |
| **Notes** | — |

---

### 9.11 Donor Panel — View and Filter

#### TC-DP-001: Donor Sees Only Compatible Requests

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DP-001 |
| **Module / Feature** | Donor Panel — Blood Requests |
| **Scenario Title** | A+ donor only sees requests compatible with A+ donation |
| **Objective** | Verify donor-side compatibility filtering |
| **Actor / Role** | Donor |
| **Preconditions** | Donor is A+; requests exist for A+, B+, O-, AB+ |
| **Test Data** | A+ donor |
| **Steps** | 1. Login as A+ donor<br>2. Navigate to Blood Requests<br>3. Observe listed requests |
| **Expected Result** | - Sees: A+ request (direct match), AB+ request (A+ can donate to AB+)<br>- Does NOT see: B+ request, O- request<br>- Uses `getCompatibleRecipientTypes()` for filtering |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S1 |
| **Notes** | Uses `verified_blood_type` if available, otherwise self-reported `blood_type`. |

---

#### TC-DP-002: Donor Sees Distance in Kilometers

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DP-002 |
| **Module / Feature** | Donor Panel — Blood Requests |
| **Scenario Title** | Distance column shows correct km based on Haversine |
| **Objective** | Verify distance calculation display |
| **Actor / Role** | Donor |
| **Preconditions** | Donor has GPS coords; request has GPS coords |
| **Test Data** | Donor at (31.95, 35.91), Request at (31.96, 35.92) |
| **Steps** | 1. Login as donor<br>2. View blood requests<br>3. Check distance column |
| **Expected Result** | - Distance shown in km (e.g., "1.3 km")<br>- Sorted by distance ASC |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P3 |
| **Severity** | S4 |
| **Notes** | — |

---

#### TC-DP-003: Ineligible Donor Sees Empty Request Table

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DP-003 |
| **Module / Feature** | Donor Panel — Blood Requests |
| **Scenario Title** | Donor with is_eligible=false sees no requests |
| **Objective** | Verify eligibility gate on request listing |
| **Actor / Role** | Donor |
| **Preconditions** | Donor `is_eligible = false` |
| **Test Data** | Ineligible donor |
| **Steps** | 1. Login as ineligible donor (not chronic — would redirect)<br>2. Navigate to Blood Requests |
| **Expected Result** | - Table is empty<br>- Informational message about ineligibility |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | Chronic disease donors redirected to IneligibleDonor page by middleware. |

---

#### TC-DP-004: Donor with Chronic Disease Redirected to Ineligible Page

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-DP-004 |
| **Module / Feature** | Donor Panel — Middleware |
| **Scenario Title** | Chronic disease donor blocked by CheckDonorIneligibility |
| **Objective** | Verify middleware redirect |
| **Actor / Role** | Donor |
| **Preconditions** | Donor health profile: `chronic_disease = true` |
| **Test Data** | Chronically ineligible donor |
| **Steps** | 1. Login as donor with chronic_disease = true<br>2. Attempt to navigate to any donor panel page |
| **Expected Result** | - Redirected to IneligibleDonor page<br>- Cannot access Blood Requests, History, or other pages |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | — |

---

### 9.12 Concurrent Scenarios

#### TC-CC-001: Multiple Donors Accept Simultaneously

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CC-001 |
| **Module / Feature** | Concurrency |
| **Scenario Title** | 5 donors click Accept at the same time |
| **Objective** | Verify system handles concurrent acceptances without data corruption |
| **Actor / Role** | Multiple Donors |
| **Preconditions** | 5 eligible donors, all viewing same active request |
| **Test Data** | Request with units_needed = 2 |
| **Steps** | 1. Simulate 5 concurrent accept requests<br>2. Verify all succeed (no duplicates, no errors)<br>3. Check DB state |
| **Expected Result** | - All 5 responses created (no uniqueness violation)<br>- Each donor has exactly 1 response<br>- Request not over-committed (may have more responses than units — this is by design, scored selection handles it) |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P1 |
| **Severity** | S2 |
| **Notes** | System allows more responses than units needed — excess managed later by organization. |

---

#### TC-CC-002: Donor Accepts While Request Being Edited

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CC-002 |
| **Module / Feature** | Concurrency |
| **Scenario Title** | Donor accepts while org is editing blood type |
| **Objective** | Verify edit re-broadcast handles in-flight acceptances |
| **Actor / Role** | Donor + Organization (simultaneous) |
| **Preconditions** | Request BROADCASTED, donor viewing request |
| **Test Data** | Donor accepts while org changes blood type |
| **Steps** | 1. Donor clicks Accept<br>2. Simultaneously, org changes blood type and saves<br>3. Check final state |
| **Expected Result** | - One of two outcomes (acceptable):<br>  a) Donor accept succeeds, then re-broadcast cancels it (NOT_NEEDED)<br>  b) Donor accept processed after edit, uses new blood type check<br>- No data corruption in either case |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | DB transaction in broadcast should prevent inconsistency. |

---

#### TC-CC-003: Two Fulfillment Events Fire Simultaneously

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-CC-003 |
| **Module / Feature** | Concurrency |
| **Scenario Title** | Two donors complete at the same time, both triggering fulfillment check |
| **Objective** | Verify no duplicate fulfillment or double cleanup |
| **Actor / Role** | System |
| **Preconditions** | Request needs 2 units, both donors marked COMPLETED in rapid succession |
| **Test Data** | 2 simultaneous COMPLETED updates |
| **Steps** | 1. Mark Donor A as COMPLETED<br>2. Nearly simultaneously mark Donor B as COMPLETED<br>3. Check request status and cleanup |
| **Expected Result** | - Request marked FULFILLED exactly once<br>- `CancelExcessResponsesJob` dispatched once (or idempotent if dispatched twice)<br>- `fulfilled_at` set once |
| **Actual Result** | — |
| **Status** | Not Executed |
| **Priority** | P2 |
| **Severity** | S2 |
| **Notes** | — |

---

## 10. API and Backend Validation Checks

### 10.1 Model-Level Validation

| # | Check | Expected Behavior |
|---|-------|-------------------|
| API-001 | `BloodRequest` created without `organization_id` | Database constraint violation / validation error |
| API-002 | `BloodRequest` with invalid `blood_type` enum value (e.g., 99) | Enum cast fails or validation rejects |
| API-003 | `BloodRequest` with invalid `status` transition (PENDING → FULFILLED directly) | Should only happen via service methods, not direct update |
| API-004 | `RequestResponse` created with duplicate `(blood_request_id, donor_id)` pair | Should be prevented by service logic (update existing IGNORED record) |
| API-005 | `RequestResponse` status set to invalid enum value | Enum cast error |
| API-006 | `DonorHealthProfile` with `weight = null` | Nullable field; eligibility calculation handles gracefully |
| API-007 | `verification_qr_code` uniqueness across responses | Statistically unique (32 hex chars = 2^128 space); no DB unique constraint needed |

### 10.2 Service-Level Validation

| # | Check | Expected Behavior |
|---|-------|-------------------|
| API-010 | `BloodRequestBroadcastService::broadcast()` called on FULFILLED request | Should not re-broadcast; check status guard |
| API-011 | `BloodRequestActionService::accept()` called with non-existent request ID | Exception or null check |
| API-012 | `QRCodeService::generate()` called for COMPLETED response | Should only generate for PENDING responses |
| API-013 | `QRCodeService::validate()` with empty string token | Returns error, no DB query with empty string |
| API-014 | `DonorScoringService::scoreAndSelect()` with empty donor collection | Returns empty selection, no errors |
| API-015 | `DispatchBloodRequestNotifications` job with 0 donors | Job completes without sending any notifications |

### 10.3 Database Consistency

| # | Check | Expected Behavior |
|---|-------|-------------------|
| DB-001 | `blood_request.organization_id` FK integrity | Cannot reference non-existent organization |
| DB-002 | `request_responses.blood_request_id` FK integrity | Cannot reference non-existent request |
| DB-003 | `request_responses.donor_id` FK integrity | Cannot reference non-existent donor |
| DB-004 | Soft-deleted request responses cascade | Verify soft-delete does not break relationships |
| DB-005 | `fulfilled_at` is null when status is not FULFILLED | Data consistency |
| DB-006 | `broadcasted_at` is null when status is PENDING | Data consistency |
| DB-007 | `actual_search_radius_km` is set when status is BROADCASTED | Broadcast must record final radius |
| DB-008 | `donors_completed` (computed) matches actual COMPLETED response count | Query consistency |
| DB-009 | `total_donations` on DonorHealthProfile matches COMPLETED responses across all requests | Cumulative counter integrity |

---

## 11. Security and Access Control Cases

#### TC-SEC-001: Donor Cannot Create Blood Request

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-001 |
| **Module / Feature** | Access Control |
| **Scenario Title** | Donor role attempts to access organization create request page |
| **Objective** | Verify BloodRequestPolicy blocks donors from creating |
| **Actor / Role** | Donor |
| **Steps** | 1. Login as donor<br>2. Attempt direct URL access to `/org/{slug}/blood-requests/create` |
| **Expected Result** | - 403 Forbidden or redirected to donor panel<br>- No request created |
| **Priority** | P1 |
| **Severity** | S1 |

---

#### TC-SEC-002: Organization Cannot Access Another Org's Requests

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-002 |
| **Module / Feature** | Access Control — Tenancy |
| **Scenario Title** | Org A tries to view Org B's request |
| **Objective** | Verify Filament tenancy isolation |
| **Actor / Role** | Organization A |
| **Steps** | 1. Login as Org A<br>2. Attempt direct URL to `/org/{org-b-slug}/blood-requests/{org-b-request-id}` |
| **Expected Result** | - 403 Forbidden or 404<br>- Tenant middleware blocks cross-org access |
| **Priority** | P1 |
| **Severity** | S1 |

---

#### TC-SEC-003: Admin Cannot Create or Edit Requests

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-003 |
| **Module / Feature** | Access Control |
| **Scenario Title** | Admin can view but not create/edit blood requests |
| **Objective** | Verify admin is read-only for requests |
| **Actor / Role** | Admin |
| **Steps** | 1. Login as admin<br>2. Navigate to Blood Requests in admin panel<br>3. Check for Create/Edit buttons |
| **Expected Result** | - No Create button visible<br>- No Edit action on request rows<br>- View/infolist available |
| **Priority** | P2 |
| **Severity** | S2 |

---

#### TC-SEC-004: Inactive User Blocked from All Panels

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-004 |
| **Module / Feature** | Access Control — User Status |
| **Scenario Title** | User with is_active=false cannot access any panel |
| **Objective** | Verify `canAccessPanel()` blocks inactive users |
| **Actor / Role** | Inactive User (any role) |
| **Steps** | 1. Login with inactive user credentials<br>2. Attempt to access panel |
| **Expected Result** | - Access denied<br>- Redirected to login or error page |
| **Priority** | P1 |
| **Severity** | S1 |

---

#### TC-SEC-005: Unapproved Organization Cannot Create Requests

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-005 |
| **Module / Feature** | Access Control — Organization Approval |
| **Scenario Title** | PENDING organization redirected to approval page |
| **Objective** | Verify `CheckOrganizationApproved` middleware |
| **Actor / Role** | Organization (PENDING status) |
| **Steps** | 1. Login as org user with PENDING approval_status<br>2. Attempt to navigate to Blood Requests |
| **Expected Result** | - Redirected to PendingApproval page<br>- Cannot access any org panel functionality |
| **Priority** | P1 |
| **Severity** | S1 |

---

#### TC-SEC-006: Organization Cannot Modify Another Org's Response

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-006 |
| **Module / Feature** | Access Control — Response Policy |
| **Scenario Title** | Org A tries to update response on Org B's request |
| **Objective** | Verify `RequestResponsePolicy::update()` |
| **Actor / Role** | Organization A |
| **Steps** | 1. Obtain Org B's response ID<br>2. Attempt to change status via direct API/URL |
| **Expected Result** | - 403 Forbidden<br>- Response unchanged |
| **Priority** | P1 |
| **Severity** | S1 |

---

#### TC-SEC-007: Donor Cannot View Other Donor's Responses

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-007 |
| **Module / Feature** | Access Control — Response Policy |
| **Scenario Title** | Donor A cannot see Donor B's response details |
| **Objective** | Verify `RequestResponsePolicy::view()` |
| **Actor / Role** | Donor A |
| **Steps** | 1. Attempt to access Donor B's response<br>2. Check visibility |
| **Expected Result** | - Only own responses visible<br>- Other donors' responses inaccessible |
| **Priority** | P2 |
| **Severity** | S2 |

---

#### TC-SEC-008: QR Token Brute-Force Prevention

| Field | Value |
|-------|-------|
| **Test Case ID** | TC-SEC-008 |
| **Module / Feature** | Security — QR Codes |
| **Scenario Title** | Attempting to guess QR tokens |
| **Objective** | Verify 32-char hex tokens are not guessable and rate limiting prevents brute force |
| **Actor / Role** | Attacker (Organization) |
| **Steps** | 1. Rapidly submit random QR codes<br>2. Hit rate limit at 30/min<br>3. Verify no valid token found |
| **Expected Result** | - Rate limit blocks after 30 attempts<br>- 2^128 token space makes guessing infeasible<br>- All invalid attempts logged |
| **Priority** | P2 |
| **Severity** | S2 |

---

## 12. UI/UX Validation Points

### 12.1 Organization Panel — Create/Edit Form

| # | Validation Point | Expected Behavior |
|---|-----------------|-------------------|
| UI-001 | Blood type dropdown shows all 9 options with labels | O+, O-, A+, A-, B+, B-, AB+, AB-, UNKNOWN |
| UI-002 | Urgency level defaults to NORMAL | Pre-selected on form load |
| UI-003 | Search radius defaults to 10 km | Pre-filled on form load |
| UI-004 | Map component allows pin placement and shows coordinates | Lat/lng update when pin moves |
| UI-005 | "Use My Location" button fills coordinates from browser GPS | Coordinates populated; address auto-fills if geocoding available |
| UI-006 | Units needed input accepts only integers 1-100 | Spinner/number input with min/max |
| UI-007 | Additional notes supports bilingual input (EN/AR) | Translatable textarea with locale tabs |
| UI-008 | Address field supports bilingual input | Translatable text field |
| UI-009 | Disabled fields on FULFILLED/CANCELLED requests show visual indicator | Gray/dimmed appearance |
| UI-010 | Success notification after create shows donor count and expansion info | "X donors notified, searched Y km radius" |

### 12.2 Organization Panel — Responses Table

| # | Validation Point | Expected Behavior |
|---|-----------------|-------------------|
| UI-020 | Status badges show correct colors per status | PENDING=warning, ACCEPTED=info, COMPLETED=success, DECLINED=danger, etc. |
| UI-021 | Blood type shows verification icon if lab-verified | Shield/check icon for verified_blood_type |
| UI-022 | Connectivity indicator: "Waiting" / "Delayed" / "Likely out of coverage" | Based on response age vs thresholds |
| UI-023 | Medical Results modal shows all required fields | Blood type verification, eligibility, rejection reason, delay |
| UI-024 | Conditional fields: rejection_reason shown only for temporary/permanent | Hidden when eligibility = eligible |
| UI-025 | Conditional fields: delay_duration shown only for temporary | Hidden for eligible and permanent |
| UI-026 | Custom date picker shown only when delay = "custom" | Hidden for preset durations |

### 12.3 Donor Panel — Blood Requests

| # | Validation Point | Expected Behavior |
|---|-----------------|-------------------|
| UI-030 | Requests sorted by distance (nearest first) | ASC distance_km |
| UI-031 | Urgency badge: NORMAL=blue, CRITICAL=red | Visual distinction |
| UI-032 | Response status column shows localized status | "No response", "Agree", "Attended", etc. in current locale |
| UI-033 | Accept button hidden for requests donor already responded to (except IGNORED) | Visibility rules per `BloodRequests.php` |
| UI-034 | Cancel confirmation dialog warns about losing QR code | "Are you sure? Your QR code will be revoked." |
| UI-035 | Download QR button only visible when QR is valid and not expired | Hidden for expired/verified/non-existent QR |

### 12.4 Admin Panel — Request Infolist

| # | Validation Point | Expected Behavior |
|---|-----------------|-------------------|
| UI-040 | Timeline section shows created_at, broadcasted_at, fulfilled_at | Chronological display |
| UI-041 | Search scope shows original vs actual radius and expansion steps | "10 km → 20 km (2 expansion steps)" |
| UI-042 | Location shows Google Maps link for coordinates | Clickable link to maps |
| UI-043 | Response counts: total, accepted, completed visible | Accurate aggregations |

### 12.5 Localization

| # | Validation Point | Expected Behavior |
|---|-----------------|-------------------|
| UI-050 | All labels/headings in Arabic when locale = ar | RTL layout, Arabic text |
| UI-051 | All labels/headings in English when locale = en | LTR layout, English text |
| UI-052 | Blood type labels localized | "فصيلة الدم" (AR) / "Blood Type" (EN) |
| UI-053 | Notification content in donor's preferred locale | `User::preferredLocale()` applied |
| UI-054 | Translatable fields (notes, address) show correct locale content | Display current locale's translation |

---

## 13. Notifications and Real-Time Update Checks

### 13.1 BloodRequestMatchNotification (Donor)

| # | Check | Expected Behavior |
|---|-------|-------------------|
| NOT-001 | Notification sent when donor matched to broadcast | Entry in `notifications` table for donor |
| NOT-002 | Title: "Critical Blood Donation Request" for CRITICAL | Urgency reflected in title |
| NOT-003 | Title: "Blood Donation Request" for NORMAL | Standard title |
| NOT-004 | Body includes: org name, units needed, blood type, distance | All contextual info present |
| NOT-005 | Notification links to donor Blood Requests page | `url` field points to correct route |
| NOT-006 | Icon: exclamation-triangle for CRITICAL, heart for NORMAL | Urgency-specific icon |
| NOT-007 | Delivered in donor's preferred locale (AR/EN) | Content matches `users.locale` |
| NOT-008 | Notification sent via both `database` and `broadcast` channels | Two delivery paths |

### 13.2 DonorResponseNotification (Organization)

| # | Check | Expected Behavior |
|---|-------|-------------------|
| NOT-010 | Sent when donor accepts request | Notification type indicates acceptance |
| NOT-011 | Sent when donor arrives (ACCEPTED) | Title: "Donor arrived" |
| NOT-012 | Sent when donation completed | Title: "Donation completed" |
| NOT-013 | Sent when donor declines (medical) | Title: "Donor declined" |
| NOT-014 | Sent when donor no-shows | Title: "Donor no-show" |
| NOT-015 | Body includes donor name, blood type (verified/self-reported), distance | Contextual info for org |

### 13.3 DonorIneligibilityNotification (Donor)

| # | Check | Expected Behavior |
|---|-------|-------------------|
| NOT-020 | Sent when lab results show temporary ineligibility | Reason included |
| NOT-021 | Sent when lab results show permanent ineligibility | Permanent flag set |
| NOT-022 | Includes next eligible date for temporary | Date shown in notification |
| NOT-023 | No next eligible date for permanent | Clearly states permanent |

### 13.4 ResponseNotNeededNotification (Donor)

| # | Check | Expected Behavior |
|---|-------|-------------------|
| NOT-030 | Sent when request fulfilled and donor response was PENDING | Reason: "fulfilled" |
| NOT-031 | Sent when request expired and donor response was PENDING | Reason: "expired" |
| NOT-032 | QR code revoked before notification sent | QR invalidated |

### 13.5 Notification Edge Cases

| # | Check | Expected Behavior |
|---|-------|-------------------|
| NOT-040 | Job re-validates eligibility before sending | If donor became ineligible after scoring, no notification |
| NOT-041 | Job skips donor who already responded to this request | No duplicate notification |
| NOT-042 | Notification batching: 100 donors per job chunk | Large broadcasts split correctly |
| NOT-043 | `NotificationService` logs success/failure for each notification | Log entries in application log |

---

## 14. Data Integrity and Audit Trail Checks

### 14.1 EligibilityLog Audit

| # | Check | Expected Behavior |
|---|-------|-------------------|
| AUD-001 | EligibilityLog created on donor registration | `check_type = TYPE_REGISTRATION (1)` |
| AUD-002 | EligibilityLog created when donor accepts request | `check_type = TYPE_REQUEST_ACCEPTANCE (2)` |
| AUD-003 | EligibilityLog created on profile update | `check_type = TYPE_PROFILE_UPDATE (3)` |
| AUD-004 | EligibilityLog created on lab verification | `check_type = TYPE_LAB_VERIFICATION (4)` |
| AUD-005 | Log captures `answers_snapshot` (serialized health data) | Full snapshot for audit trail |
| AUD-006 | Log records `organization_id` for lab verifications | Traceable to which org verified |
| AUD-007 | Log records `is_permanent` flag correctly | true for chronic, false for temporary |
| AUD-008 | Log records `rejection_reason` text | Human-readable reason preserved |

### 14.2 Blood Type Verification Audit

| # | Check | Expected Behavior |
|---|-------|-------------------|
| AUD-010 | `verified_blood_type` set only once | Cannot be changed after first verification |
| AUD-011 | `verified_by_organization_id` recorded | Which org performed the lab test |
| AUD-012 | `verified_at` timestamp recorded | When verification occurred |
| AUD-013 | After verification, broadcasts use `verified_blood_type` | Matching priority: verified > self-reported |

### 14.3 Response State Transitions

| # | Check | Expected Behavior |
|---|-------|-------------------|
| AUD-020 | PENDING → ACCEPTED only via QR scan (confirmAdmission) | Not settable directly |
| AUD-021 | ACCEPTED → COMPLETED only via Medical Results (eligible) | Not settable directly |
| AUD-022 | ACCEPTED → DECLINED only via Medical Results (temp/permanent) | Not settable directly |
| AUD-023 | PENDING → NO_SHOW only via Mark No-Show action | Not settable directly |
| AUD-024 | PENDING → UNREACHABLE only via cleanup command | Automated, not manual |
| AUD-025 | PENDING → NOT_NEEDED only via CancelExcessResponsesJob | Automated on fulfill/expire |
| AUD-026 | PENDING → IGNORED only via donor ignore action | Donor-initiated |
| AUD-027 | `responded_at` set when response created/accepted | Timestamp accuracy |
| AUD-028 | `verified_at` set when QR scanned | Timestamp accuracy |

### 14.4 Request State Transitions

| # | Check | Expected Behavior |
|---|-------|-------------------|
| AUD-030 | PENDING → BROADCASTED: `broadcasted_at` set, `actual_search_radius_km` set | Both fields populated |
| AUD-031 | BROADCASTED → FULFILLED: `fulfilled_at` set | Timestamp recorded |
| AUD-032 | BROADCASTED → EXPIRED: only via scheduled command | Automated |
| AUD-033 | BROADCASTED → CANCELLED: only via org action | Org-initiated |
| AUD-034 | No backward transitions (FULFILLED → BROADCASTED not possible) | State machine enforced |

### 14.5 Soft Delete Integrity

| # | Check | Expected Behavior |
|---|-------|-------------------|
| AUD-040 | Soft-deleted requests excluded from donor-facing queries | Not visible to donors |
| AUD-041 | Soft-deleted requests visible to org with "trashed" filter | Recoverable via admin |
| AUD-042 | Soft-deleted request responses preserved for audit | History maintained |

---

## 15. Coverage Summary

### 15.1 Test Case Count by Area

| Area | Positive | Negative | Boundary | Security | Concurrency | Total |
|------|----------|----------|----------|----------|-------------|-------|
| Request Creation | 5 | 9 | 4 | 2 | 0 | 20 |
| Broadcasting & Matching | 7 | 3 | 2 | 0 | 0 | 12 |
| Donor Scoring | 5 | 2 | 0 | 0 | 0 | 7 |
| Donor Response (Accept/Ignore/Cancel) | 5 | 4 | 0 | 0 | 1 | 10 |
| QR Code Verification | 3 | 4 | 1 | 1 | 0 | 9 |
| Medical Results & Fulfillment | 6 | 0 | 0 | 0 | 1 | 7 |
| Request Edit & Re-broadcast | 3 | 0 | 0 | 0 | 1 | 4 |
| Expiry & Cleanup | 3 | 1 | 0 | 0 | 0 | 4 |
| Donor Panel View & Filter | 4 | 1 | 0 | 0 | 0 | 5 |
| Security & Access Control | 0 | 0 | 0 | 8 | 0 | 8 |
| **Totals** | **41** | **24** | **7** | **11** | **3** | **86** |

### 15.2 Additional Validation Points

| Category | Count |
|----------|-------|
| API/Backend Validation Checks | 23 |
| UI/UX Validation Points | 35 |
| Notification Checks | 25 |
| Data Integrity / Audit Checks | 28 |
| **Grand Total (all checkpoints)** | **197** |

### 15.3 Priority Distribution

| Priority | Count | Percentage |
|----------|-------|-----------|
| P1 (Critical) | 32 | 37% |
| P2 (High) | 36 | 42% |
| P3 (Medium) | 14 | 16% |
| P4 (Low) | 4 | 5% |

### 15.4 Coverage by Actor

| Actor | Test Cases Involving |
|-------|---------------------|
| Organization | 38 |
| Donor | 28 |
| Admin | 6 |
| System (Scheduler/Queue) | 14 |

### 15.5 Coverage by BloodRequest Status Transition

| Transition | Covered By |
|-----------|------------|
| PENDING → BROADCASTED | TC-CR-001, TC-CR-002, TC-CR-003, TC-BC-001 |
| BROADCASTED → FULFILLED | TC-MR-006, TC-CC-003 |
| BROADCASTED → CANCELLED | TC-ED-001 (implicit), EF14 |
| BROADCASTED → EXPIRED | TC-EX-001 |
| PENDING → EXPIRED | TC-EX-001 |

### 15.6 Coverage by RequestResponse Status Transition

| Transition | Covered By |
|-----------|------------|
| (none) → PENDING | TC-DR-001 |
| PENDING → ACCEPTED | TC-QR-002 |
| ACCEPTED → COMPLETED | TC-MR-001 |
| ACCEPTED → DECLINED | TC-MR-002, TC-MR-003 |
| PENDING → NO_SHOW | TC-MR-005 |
| PENDING → IGNORED | TC-DR-010 |
| PENDING → UNREACHABLE | TC-EX-002, TC-EX-003 |
| PENDING → NOT_NEEDED | TC-MR-006, TC-EX-001 |
| IGNORED → PENDING | TC-DR-005 |

### 15.7 Risk Areas Requiring Special Attention

| Risk | Impact | Mitigation in Test Suite |
|------|--------|------------------------|
| Wrong blood type matching | Patient safety — transfusion reaction | TC-BC-003 through TC-BC-005 (all blood types), TC-CR-005 |
| Race conditions on fulfillment | Double cleanup, data corruption | TC-CC-001, TC-CC-002, TC-CC-003 |
| Cross-tenant data leakage | Organization sees another org's data | TC-SEC-002, TC-SEC-006, TC-QR-004 |
| Stale eligibility allowing ineligible donors | Unfit donor allowed to donate | TC-DR-003, TC-DP-003, TC-DP-004, NOT-040 |
| Circuit breaker stuck open | ML scoring never recovers | TC-SC-006, TC-SC-007 |
| QR code forgery/reuse | Unauthorized hospital admission | TC-QR-004, TC-QR-005, TC-QR-007, TC-SEC-008 |

---

**End of Document**

*This test suite covers the complete blood request lifecycle in BloodBridge, from creation through broadcasting, donor matching, response handling, QR verification, medical assessment, fulfillment, and expiry. All test cases are designed to be executable against the actual BloodBridge system architecture using Filament panels, Laravel queue jobs, and scheduled Artisan commands.*
