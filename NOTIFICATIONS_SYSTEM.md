# BloodBridge Notification System - Complete Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Notification Types](#notification-types)
4. [Delivery Channels](#delivery-channels)
5. [Complete Notification Flow](#complete-notification-flow)
6. [Notification Service Layer](#notification-service-layer)
7. [Dispatch Job](#dispatch-job)
8. [Error Handling & Logging](#error-handling--logging)
9. [Real-time Broadcasting](#real-time-broadcasting)
10. [Creating New Notifications](#creating-new-notifications)
11. [Testing](#testing)

---

## System Overview

The BloodBridge notification system is a **centralized, production-grade** notification platform that handles all user communications through a service layer. The system ensures:

- **Consistent error handling** across all notifications
- **Real-time delivery** via Reverb WebSocket broadcasting
- **Database persistence** for history and in-app notifications
- **Email delivery** (optional, system announcements)
- **Retry mechanisms** for critical notifications
- **Comprehensive logging** for monitoring and debugging
- **Batch processing** with failure aggregation
- **Type-safe** enum-based notification tracking

### Key Principle
> **All notifications must flow through `NotificationService`** to guarantee error handling, logging, and monitoring.

---

## Architecture

### System Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                  NOTIFICATION SOURCES                            │
│  (Controllers, Listeners, Events, Scheduled Tasks)              │
└────┬──────────────────────────────────────────────────────────┬─┘
     │                                                          │
     │ Create notification instance                            │
     │                                                          │
┌────▼──────────────────────────────────────────────────────────▼──┐
│                   NOTIFICATION SERVICE                            │
│         (Centralized Routing & Error Handling)                   │
│                                                                  │
│  Methods:                                                        │
│  - send()             → Single user                             │
│  - sendBatch()        → Multiple users                          │
│  - sendWithRetry()    → With retry mechanism                    │
└────┬──────────────────────────────────────────────┬──────────┬──┘
     │                                              │          │
     │                                              │          │
┌────▼──────────┐  ┌─────────────┐  ┌──────────────▼─┐  ┌─────▼─┐
│  Notification │  │   via()     │  │  toDatabase()  │  │toBroadcast
│   Instance    │  │             │  │                │  │
│               │  │ Returns:    │  │ Returns:       │  │Returns
│ • __construct │  │ ['database' │  │ Array with     │  │Broadcast
│ • via()       │  │  'broadcast'│  │ title, body,   │  │Message
│ • toDatabase()│  │  'mail']    │  │ actions        │  │with data
│ • toBroadcast│  │             │  │                │  │
│ • toMail()    │  └─────────────┘  └────────────────┘  └────────┘
└───────────────┘       (conditional)        (optional)  (optional)
       │
       │ (Implements Notification interface)
       └──────────────────┬──────────────────────────┐
                          │                          │
           ┌──────────────▼────────────────┐  ┌─────▼──────────────┐
           │   Database Notifications     │  │  Broadcast         │
           │   (In-app History)           │  │  (Real-time)       │
           │                              │  │                    │
           │ Stored in:                   │  │ Sent via Reverb:   │
           │ notifications table          │  │ WebSocket channel  │
           │                              │  │ (User-specific)    │
           │ Filament alerts triggered    │  │                    │
           │ on admin panel load          │  │ Client receives    │
           │                              │  │ via Livewire       │
           └──────────────────────────────┘  └────────────────────┘
                    (Async Queue)                   (Real-time Push)
```

### Component Interaction

```
DispatchBloodRequestNotifications Job
    │
    └─→ Fetches eligible donors
        │
        └─→ For each donor:
            │
            ├─→ Create BloodRequestMatchNotification
            │
            ├─→ NotificationService::send()
            │   │
            │   ├─→ Notification::notify($user)
            │   │   │
            │   │   ├─→ via($user)
            │   │   │   └─→ Returns ['database', 'broadcast']
            │   │   │
            │   │   ├─→ toDatabase($user)
            │   │   │   └─→ Returns Array (persisted to DB)
            │   │   │
            │   │   └─→ toBroadcast($user)
            │   │       └─→ Returns BroadcastMessage (WebSocket)
            │   │
            │   └─→ Log success/failure
            │
            └─→ Return [success, error, notifiable_id]
```

---

## Notification Types

The system defines 5 core notification types through the `NotificationType` enum in `app/Enums/NotificationType.php`.

### 1. Blood Request Match Notification

**Class:** `App\Notifications\BloodRequestMatchNotification`

**Purpose:** Notifies donors when their blood type matches a pending blood request

**When Triggered:**
- Dispatched by `DispatchBloodRequestNotifications` job
- Fires when donor eligibility matches request blood type

**Recipient:** Donor (User)

**Constructor:**
```php
new BloodRequestMatchNotification(
    BloodRequest $bloodRequest,
    ?float $distance = null  // Distance in kilometers
)
```

**Notification Content:**

Channel: `['database', 'broadcast']`

**Title Logic:**
- CRITICAL urgency → "Critical Blood Donation Request"
- Normal urgency → "Blood Donation Request"

**Body Format:**
```
{organization_name} needs {units_needed} unit(s) of blood type {blood_type}
Note: Your blood type will be determined at the hospital (if UNKNOWN)
Distance: {distance} km (if available)
```

**Icons:**
- CRITICAL → `heroicon-o-exclamation-triangle` (danger color)
- Normal → `heroicon-o-heart` (primary color)

**Database Message:** Filament notification structure with view action linking to donor blood request page

**Broadcast Message:** BroadcastMessage object with above data

---

### 2. Donor Response Notification

**Class:** `App\Notifications\DonorResponseNotification`

**Purpose:** Notifies organization admin when donor responds to a blood request

**When Triggered:**
- Dispatched when donor accepts, arrives, completes, or declines donation
- Uses `RequestResponse` model to track response status

**Recipient:** Organization Admin (User)

**Constructor:**
```php
new DonorResponseNotification(RequestResponse $response)
```

**Notification Content:**

Channel: `['database', 'broadcast']`

**Title By Status:**
| Status | Title |
|--------|-------|
| PENDING | "New donor accepted donation request" |
| ACCEPTED | "Donor arrived at hospital" |
| COMPLETED | "Donation completed" |
| DECLINED | "Donation medically declined" |
| NO_SHOW | "Donor did not show up" |

**Body Format:**
```
{donor_name} - Blood Type: {blood_type}
Distance: {distance} km (if available)
```

**Icons by Status:**
| Status | Icon | Color |
|--------|------|-------|
| COMPLETED | `heroicon-o-check-circle` | success |
| DECLINED/NO_SHOW | `heroicon-o-x-circle` | danger |
| ACCEPTED | `heroicon-o-user` | info |
| PENDING | `heroicon-o-user` | warning |

**Database Message:** Filament notification with "View Response" action linking to blood request details page

**Broadcast Message:** BroadcastMessage object with status-specific styling

---

### 3. Response Not Needed Notification

**Class:** `App\Notifications\ResponseNotNeededNotification`

**Purpose:** Thanks donor and informs them their response is no longer needed (request fulfilled by others)

**When Triggered:**
- Dispatched by `CancelExcessResponsesJob`
- When blood request has enough donor responses

**Recipient:** Donor (User)

**Constructor:**
```php
new ResponseNotNeededNotification(RequestResponse $response)
```

**Notification Content:**

Channel: `['database', 'broadcast']`

**Fixed Title:**
```
"Thank you for your noble initiative 🤍"
```

**Fixed Body:**
```
"The required blood units have been secured thanks to other donors. 
We apologize for canceling your appointment, and we hope you will 
join us in saving another life soon."
```

**Icon:** `heroicon-o-heart` (danger color - red heart)

**Database Message:** Filament notification with "View History" action linking to donor history page

**Broadcast Message:** BroadcastMessage with gratitude message

---

### 4. Donor Ineligibility Notification

**Class:** `App\Notifications\DonorIneligibilityNotification`

**Purpose:** Informs donor they're marked ineligible or excluded from donation (temporary or permanent)

**When Triggered:**
- Dispatched when health profile eligibility changes
- Medical staff marks donor as ineligible/excluded

**Recipient:** Donor (User)

**Constructor:**
```php
new DonorIneligibilityNotification(
    string $eligibilityStatus,        // 'temporary' or 'permanent'
    ?string $rejectionReason,         // See rejection reasons below
    mixed $nextEligibleDate,          // Carbon date or null
    ?string $organizationName         // Organization that marked ineligible
)
```

**Rejection Reasons Mapped:**
| Code | Label |
|------|-------|
| `low_hemoglobin` | Low Hemoglobin |
| `underweight` | Underweight |
| `recent_illness` | Recent illness / Antibiotics |
| `low_blood_pressure` | Low Blood Pressure |
| `other_temp` | Other temporary medical reasons |
| `blood_virus` | Presence of blood viruses (HCV/HBV/HIV) |
| `chronic_disease` | Chronic disease preventing donation |
| `heart_disease` | Heart Diseases |
| `cancer` | Medical history of cancer |
| `other_perm` | Other permanent medical reasons |

**Notification Content:**

Channel: `['database', 'broadcast']`

**Titles:**
- Temporary: "Temporarily ineligible for donation"
- Permanent: "Permanently excluded from blood donation"

**Body Format (Temporary):**
```
{organization} reported that you are temporarily ineligible to donate blood
Due to: {rejection_reason}
Expected eligibility date: {next_eligible_date}
```

**Body Format (Permanent):**
```
{organization} reported your permanent exclusion from blood donation
Due to: {rejection_reason}
```

**Icon:**
- Temporary → `heroicon-o-clock` (warning color)
- Permanent → `heroicon-o-x-circle` (danger color)

**Database Message:** Filament notification with reason and date info

**Broadcast Message:** BroadcastMessage with status-appropriate styling

---

### 5. System Announcement Notification

**Class:** `App\Notifications\SystemAnnouncement`

**Purpose:** System-wide announcements sent to all users (e.g., maintenance, policy updates)

**When Triggered:**
- Dispatched via announcement creation/update
- Broadcasts to all users in system

**Recipient:** All Users

**Constructor:**
```php
new SystemAnnouncement(Announcement $announcement)
```

**Notification Content:**

Channel: `['database', 'broadcast']` + optional `['mail']`

**Title & Body:**
- Pulled from `Announcement` model properties
- `$announcement->title`
- `$announcement->body` (HTML safe)

**Icon:** `heroicon-o-megaphone` (primary color)

**Conditional Email:** 
- Only sent if `$announcement->send_via_email` is true
- Email subject: `Important Announcement: {title} - {site_name}`
- Email includes HTML body with "View Details" action

**Database Message:** Filament notification with announcement content

**Broadcast Message:** BroadcastMessage for all connected users

---

## Delivery Channels

The Laravel notification system supports multiple delivery methods:

### Database Channel
**Location:** `notifications` table (Laravel standard)

**What's Stored:**
```php
[
    'id'              => UUID,
    'notifiable_*'    => Polymorphic relation (User, Admin, etc.),
    'type'            => Notification class name,
    'data'            => Serialized array [
        'title'       => string,
        'body'        => string,
        'icon'        => string,
        'iconColor'   => string,
        'actions'     => array,
        'duration'    => int (milliseconds),
        'format'      => 'filament'
    ],
    'read_at'         => timestamp (null = unread),
    'created_at'      => timestamp
]
```

**Purpose:**
- Persistent notification history
- In-app notification center
- Filament alert display on admin/donor panels
- Marks sent notifications with timestamps

**Access in App:**
```php
// Get unread notifications
$user->unreadNotifications;

// Get all notifications
$user->notifications;

// Mark as read
$notification->markAsRead();
```

---

### Broadcast Channel
**Transport:** Reverb WebSocket server (Laravel's real-time messaging)

**Connection Details:**
```
Host: 127.0.0.1
Port: 8080
Protocol: WebSocket (ws://)
Transport: HTTP for upgrades
```

**What's Sent:**
```
Channel: private-App.Models.User.{user_id}
Event: Illuminate\Notifications\Events\BroadcastNotificationCreated
Data: {
    'id'       => UUID,
    'type'     => NotificationClass,
    'data'     => {title, body, icon, iconColor, actions, ...}
}
```

**Purpose:**
- Real-time notification delivery
- Instant UI updates without polling
- Desktop/mobile push notifications possible
- Live notifications in Livewire components

**Client-Side (Livewire):**
```php
// In blade component
@if($notification)
    <x-filament-notifications::notification
        :notification="$notification"
    />
@endif
```

---

### Mail Channel
**Transport:** Mail service (configured in `.env`)

**Who Uses It:**
- `CustomVerifyEmail` - Email verification
- `CustomResetPassword` - Password reset
- `SystemAnnouncement` - Optional announcement emails

**What's Sent:**
- Formatted HTML email
- Action buttons as clickable links
- Branded with site name and logo

**Example (System Announcement Email):**
```
From: noreply@bloodbridge.test
Subject: Important Announcement: {title} - BloodBridge

Hello {user_name},

{announcement_body}

[View Details Button]

Thank you for using BloodBridge Team
```

---

## Complete Notification Flow

### Flow 1: Blood Request Match Notification

```
[Blood Request Created in Admin]
        ↓
[Trigger: Event Listener or Manual]
        ↓
[Find eligible donors matching blood type]
        ↓
[Create DispatchBloodRequestNotifications job]
    Input: blood_request_id, donor_data[user_id => distance]
        ↓
[Job Queued in Redis/Database]
        ↓
[Queue Worker Picks Up Job]
        ↓
[Job::handle()]
    ├─ Fetch BloodRequest model
    ├─ Fetch Donor User models (eager loaded with health profile)
    ├─ For each donor in chunks of 10:
    │   ├─ Check if still eligible (is_eligible flag)
    │   ├─ Check if eligibility date not passed
    │   ├─ Check if didn't already respond to this request
    │   └─ Create BloodRequestMatchNotification
    │       └─ Call NotificationService::send()
    │
    └─ NotificationService::send()
        ├─ Call $user->notify($notification)
        │   ├─ Call notification->via($user)
        │   │   └─ Returns ['database', 'broadcast']
        │   ├─ Call notification->toDatabase($user)
        │   │   └─ Returns array from Filament notification
        │   │       └─ Store in notifications table
        │   └─ Call notification->toBroadcast($user)
        │       └─ Returns BroadcastMessage
        │           └─ Broadcast to private-App.Models.User.{user_id}
        │
        └─ Log result (success or failure)
                ↓
[Notification appears in user dashboard]
[Real-time update via Reverb WebSocket]
```

### Flow 2: Donor Response Notification

```
[Donor accepts/completes/declines donation]
        ↓
[Update RequestResponse model status]
        ↓
[Trigger: Event or Direct Call]
        ↓
[DonorResponseNotification instantiated]
    Input: RequestResponse model
        ↓
[NotificationService::send($orgAdmin, $notification)]
        ↓
[via() returns ['database', 'broadcast']]
        ↓
[toDatabase() + toBroadcast()]
        ↓
[Admin sees real-time notification]
```

### Flow 3: System Announcement Notification

```
[Admin creates/edits Announcement]
        ↓
[Event: AnnouncementCreated dispatched]
        ↓
[Listener: SendAnnouncementNotification]
        ↓
[NotificationService::sendBatch()]
    Input: All active users, new SystemAnnouncement
        ↓
[For each user:]
    ├─ notify($user)
    ├─ via() checks announcement->send_via_email
    ├─ Sends to database
    ├─ Sends to broadcast channel
    └─ Sends email (if enabled)
        ↓
[Batch result logged]
    success: {count},
    failed: {count},
    failures: {user_id => error message}
```

---

## Notification Service Layer

**File:** `app/Services/NotificationService.php`

The `NotificationService` is the **single point of entry** for all notification sending.

### Purpose
1. Centralized error handling
2. Consistent logging for monitoring
3. Dependency injection for testing
4. Ability to add filters/middleware
5. Tracking delivery status

### Method 1: `send()`

**Single notification to one user**

```php
$service = app(NotificationService::class);

$result = $service->send(
    $user,                                    // Recipient
    new BloodRequestMatchNotification(...),   // Notification instance
    NotificationType::BLOOD_REQUEST_MATCH     // Optional: for logging
);

// Returns:
[
    'success'       => bool,
    'error'         => ?string,
    'notifiable_id' => mixed  // User ID
]
```

**Error Handling:**
- Catches `Throwable` exceptions
- Logs full context (file, line, exception class)
- Returns error message without throwing

**Logging:**
```
✓ Success:
  [notifiable_type] => 'User'
  [notifiable_id] => 123
  [notification_type] => 'BloodRequestMatchNotification'
  [enum_type] => 'blood_request_match'

✗ Failure:
  Same fields +
  [error] => 'Exception message'
  [error_class] => 'ExceptionClass'
  [error_code] => 0
  [file] => 'path/to/file.php'
  [line] => 123
```

### Method 2: `sendBatch()`

**Send same notification to multiple users**

```php
$result = $service->sendBatch(
    [$user1, $user2, $user3, ...],           // Iterable of users
    new BloodRequestMatchNotification(...),   // Same notification for all
    NotificationType::BLOOD_REQUEST_MATCH     // Optional
);

// Returns:
[
    'success'    => 2,           // Count successful
    'failed'     => 1,           // Count failed
    'total'      => 3,           // Total attempted
    'failures'   => [            // Map of failed notifications
        123 => 'Error message',
        456 => 'Another error'
    ]
]
```

**Processing:**
- Iterates through each notifiable
- Calls `send()` for each one
- Aggregates results
- Logs batch summary and individual failures

**Important:** Batch continues on failure - doesn't stop at first error

---

### Method 3: `sendWithRetry()`

**Send with automatic retry on failure**

```php
$result = $service->sendWithRetry(
    $user,                                    // Recipient
    new BloodRequestMatchNotification(...),   // Notification
    int $maxRetries = 3,                      // Retry attempts
    int $delayMs = 100,                       // Delay between retries (milliseconds)
    NotificationType::BLOOD_REQUEST_MATCH     // Optional
);

// Returns:
[
    'success'   => bool,
    'attempts'  => int,      // 1-3
    'error'     => ?string
]
```

**Retry Pattern:**
```
Attempt 1: Try send
    ✓ Success? Return with attempts=1
    ✗ Failed? Wait 100ms, continue

Attempt 2: Try send
    ✓ Success? Return with attempts=2
    ✗ Failed? Wait 100ms, continue

Attempt 3: Try send
    ✓ Success? Return with attempts=3
    ✗ Failed? Return error
```

**Use Cases:**
- Critical notifications that must be delivered
- Temporary network failures
- Transient database connection issues

---

## Dispatch Job

**File:** `app/Jobs/DispatchBloodRequestNotifications.php`

Handles sending notifications to potentially hundreds of donors efficiently.

### Overview

```php
class DispatchBloodRequestNotifications implements ShouldQueue
```

**Key Features:**
- Async queue-based processing
- Batch payload handling (MAX_BATCH_SIZE = 100)
- Eager loading to prevent N+1 queries
- Fresh data fetching for up-to-date eligibility

### Constructor

```php
public function __construct(
    public int $bloodRequestId,
    public array $donorData  // Format: ['user_id' => distance_in_km, ...]
)
```

**Parameters:**
- `bloodRequestId`: ID of blood request
- `donorData`: Array mapping user IDs to distance (in km)
  - Distance is optional, can be null
  - Used to show proximity to hospital in notification

### Handler Method

```php
public function handle(): void
```

**Processing Steps:**

1. **Fetch Blood Request**
   ```php
   $bloodRequest = BloodRequest::query()->find($this->bloodRequestId);
   ```
   - Fresh data from database
   - Contains: blood_type, units_needed, urgency_level, organization

2. **Extract User IDs**
   ```php
   $userIds = array_keys($this->donorData);
   ```

3. **Eager Load Users with Relations**
   ```php
   User::with('donor.healthProfile')
       ->whereIn('id', $userIds)
       ->chunk(10, function(...) { ... })
   ```
   - Why `chunk(10)`? → Prevent loading all users into memory
   - Why eager load? → Prevent N+1 queries
   - Loads: User → Donor → HealthProfile relations

4. **For Each User Chunk:**

   a) **Find eligible donors**
   ```php
   $alreadyRespondedDonorIds = RequestResponse::query()
       ->where('blood_request_id', $bloodRequest->id)
       ->whereIn('donor_id', $donorIds)
       ->pluck('donor_id')
       ->unique();
   ```
   - Get donors who already responded to THIS request
   - Skip them (don't notify same person twice)

   b) **Check eligibility for each donor**
   ```php
   $isStillEligible = $healthProfile->is_eligible
       && (
           is_null($healthProfile->next_eligible_date)
           || $healthProfile->next_eligible_date->startOfDay()->isPast()
           || $healthProfile->next_eligible_date->startOfDay()->isToday()
       );
   ```
   - `is_eligible` flag: Not permanently excluded
   - `next_eligible_date`: Has passed or is today (eligible again)
   - `null` date: No restriction

   c) **Skip if conditions not met**
   ```php
   if (!$isStillEligible) continue;
   if ($donorId && $alreadyRespondedDonorIds->contains($donorId)) continue;
   ```

   d) **Send notification**
   ```php
   app(NotificationService::class)->send(
       $user,
       new BloodRequestMatchNotification($bloodRequest, $distance),
       NotificationType::BLOOD_REQUEST_MATCH
   );
   ```
   - Passes to service layer for consistent handling
   - Service returns success/failure (job doesn't throw)
   - Continues processing other donors on failure

### Batch Dispatch Method

```php
public static function dispatchBatches(
    int $bloodRequestId,
    array $donorData
): void
```

**Usage:**
```php
// Instead of:
DispatchBloodRequestNotifications::dispatch($bloodRequestId, $donorData);

// Use for large donor lists:
DispatchBloodRequestNotifications::dispatchBatches($bloodRequestId, $donorData);
```

**What it does:**
- Chunks `$donorData` into groups of 100 (MAX_BATCH_SIZE)
- Creates separate job for each chunk
- Prevents queue message payload limits

**Why needed:**
- Most queues limit message size (64-256KB)
- Large donor lists could exceed limit
- Splitting into chunks = smaller payloads

---

## Error Handling & Logging

### Error Handling Strategy

The system uses **graceful degradation**:

```
❌ Notification fails
    ↓
✓ Exception caught and logged
    ↓
✓ Operation returns error, doesn't throw
    ↓
✓ Batch processing continues (doesn't stop)
    ↓
✓ Admin sees error in logs/monitoring
```

### Private Method: `logFailure()`

```php
private function logFailure(
    mixed $notifiable,
    object $notification,
    Throwable $exception,
    ?NotificationType $type = null
): void
```

**Context Logged:**
```
notifiable_type      => 'User'
notifiable_id        => 123
notification_type    => 'BloodRequestMatchNotification'
notification_class   => 'App\Notifications\BloodRequestMatchNotification'
enum_type            => 'blood_request_match'
error                => 'Notification target [user@example.com] is invalid'
error_class          => 'Throwable'
error_code           => 0
file                 => '/app/services/NotificationService.php'
line                 => 67
```

### Log Levels Used

| Method | Log Level | When |
|--------|-----------|------|
| `send()` (success) | INFO | Notification sent successfully |
| `send()` (failure) | ERROR | Exception thrown during send |
| `sendBatch()` | INFO | Batch completed (summary) |
| `sendBatch()` (failures) | WARNING | Individual failures in batch |
| `sendWithRetry()` (retry success) | INFO | Succeeded after retry |
| `sendWithRetry()` (all failed) | ERROR | Failed all retry attempts |

### Log File

All notifications logged to `storage/logs/laravel.log`

**Search Examples:**
```bash
# Find all notification errors
grep "Notification failed" storage/logs/laravel.log

# Find specific notification type
grep "BloodRequestMatchNotification" storage/logs/laravel.log

# Find batch summaries
grep "Batch notification completed" storage/logs/laravel.log
```

---

## Real-time Broadcasting

### Reverb Server Configuration

**File:** `.env`
```
BROADCAST_DRIVER=reverb
REVERB_APP_ID=bloodbridge
REVERB_APP_KEY=your-app-key
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

**Start Reverb Server:**
```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

### Client Connection (Livewire)

Livewire automatically connects to Reverb. In blade components:

```blade
<!-- Notification bell/list in header -->
<livewire:notifications-list />
```

```php
// In Livewire component
use Livewire\Component;

class NotificationsList extends Component
{
    public function render()
    {
        return view('livewire.notifications-list', [
            'notifications' => auth()->user()->unreadNotifications,
        ]);
    }
}
```

### Real-time Updates

When notification is broadcast:

1. **Backend sends**
   ```php
   broadcast(new BroadcastNotificationCreated($notification))
   ```

2. **Reverb delivers to channel**
   ```
   private-App.Models.User.123
   ```

3. **Client receives** (Livewire listens automatically)
   ```javascript
   Echo.private('App.Models.User.123')
       .notification((notification) => {
           // Update UI
           console.log(notification);
       });
   ```

4. **UI updates** without page reload
   - Notification badge increments
   - Notification appears in list
   - Toast alert appears (if configured)

### Filament Admin Integration

In Filament admin panels:

```php
// Filament automatically displays notifications
// In navigation bar - shows unread notification count
// In dashboard - displays notifications from database

// Mark read
$notification->markAsRead();

// Access in Livewire component
$this->user->unreadNotifications
```

Filament's `DatabaseNotifications` widget automatically:
- Fetches from `notifications` table
- Displays in admin panel
- Provides mark-as-read functionality

---

## Creating New Notifications

### Step 1: Create Notification Class

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Your\Model;

/**
 * MyCustomNotification
 * 
 * Description of what this notifies about and when it's sent.
 * Delivery: Database + Broadcast
 * 
 * Triggered by: How this notification gets dispatched
 * Recipient: Who receives this
 */
class MyCustomNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Delivery channels for this notification
     */
    public function via(object $notifiable): array
    {
        // 'mail' is optional
        return ['database', 'broadcast'];
    }

    /**
     * Build Filament notification structure
     */
    private function buildFilamentNotification(object $notifiable): FilamentNotification
    {
        return FilamentNotification::make()
            ->title(__('Notification Title'))
            ->body(__('Notification body text'))
            ->icon('heroicon-o-information-circle')
            ->iconColor('info')
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label(__('View'))
                    ->url(route('some.route'))
                    ->button()
                    ->markAsRead(),
            ]);
    }

    /**
     * Database notification (persisted to notifications table)
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->buildFilamentNotification($notifiable)->getDatabaseMessage();
    }

    /**
     * Broadcast notification (real-time WebSocket)
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return $this->buildFilamentNotification($notifiable)->getBroadcastMessage();
    }
}
```

### Step 2: Add to NotificationType Enum (if core notification)

**File:** `app/Enums/NotificationType.php`

```php
enum NotificationType: string
{
    // ... existing cases
    case MY_CUSTOM_NOTIFICATION = 'my_custom_notification';

    public function getLabel(): string
    {
        return match ($this) {
            // ...
            self::MY_CUSTOM_NOTIFICATION => __('My Custom Notification'),
        };
    }

    public function getNotificationClass(): string
    {
        return match ($this) {
            // ...
            self::MY_CUSTOM_NOTIFICATION => 'App\\Notifications\\MyCustomNotification',
        };
    }
}
```

### Step 3: Dispatch Through Service Layer

```php
use App\Services\NotificationService;
use App\Enums\NotificationType;
use App\Notifications\MyCustomNotification;

// Single notification
app(NotificationService::class)->send(
    $user,
    new MyCustomNotification($model),
    NotificationType::MY_CUSTOM_NOTIFICATION
);

// Batch
app(NotificationService::class)->sendBatch(
    $users,
    new MyCustomNotification($model),
    NotificationType::MY_CUSTOM_NOTIFICATION
);

// With retry
app(NotificationService::class)->sendWithRetry(
    $user,
    new MyCustomNotification($model),
    maxRetries: 3,
    delayMs: 100,
    type: NotificationType::MY_CUSTOM_NOTIFICATION
);
```

### Step 4: Test

```php
it('sends my custom notification', function () {
    $user = User::factory()->create();
    $model = Model::factory()->create();
    
    $notification = new MyCustomNotification($model);
    
    // Test channels
    expect($notification->via($user))->toContain('database')
        ->and($notification->via($user))->toContain('broadcast');
    
    // Test content
    $database = $notification->toDatabase($user);
    expect($database)->toHaveKey('title');
    
    $broadcast = $notification->toBroadcast($user);
    expect($broadcast)->not->toBeNull();
});
```

---

## Testing

### Test Structure

The notification system has **36 comprehensive tests** across 4 files:

#### File 1: `tests/Feature/NotificationServiceTest.php` (12 tests)
Tests the NotificationService layer:
- Single notification sending
- Error handling and responses
- Type parameter tracking
- Batch sending (multiple users)
- Batch failure aggregation
- Batch continued processing on failures
- Retry mechanism
- Retry stops on success
- Retry delay timing
- Database persistence
- Batch result structure
- Null notification type handling

#### File 2: `tests/Feature/NotificationDispatchTest.php` (5 tests)
Tests the DispatchBloodRequestNotifications job:
- Job instantiation
- Job queuing
- Handle method existence
- Multiple jobs queue independently
- Blood request processing

#### File 3: `tests/Unit/NotificationTypeEnumTest.php` (10 tests)
Tests NotificationType enum:
- All 5 enum cases exist
- Correct class mappings for each type
- Enum values are strings
- Enum values are unique
- Enum creation from value string
- Invalid values return null

#### File 4: `tests/Feature/NotificationClassesTest.php` (9 tests)
Tests notification class structure:
- BloodRequestMatch channels configured
- BloodRequestMatch database message format
- BloodRequestMatch filament notification builds
- BloodRequestMatch toDatabase method
- BloodRequestMatch toBroadcast method
- Notification database content has title
- Broadcast notification returns data
- SystemAnnouncement class exists
- SystemAnnouncement has required methods

### Running Tests

```bash
# All notification tests
php artisan test tests/Feature/NotificationServiceTest.php \
  tests/Feature/NotificationDispatchTest.php \
  tests/Unit/NotificationTypeEnumTest.php \
  tests/Feature/NotificationClassesTest.php

# Specific test file
php artisan test tests/Feature/NotificationServiceTest.php

# Specific test
php artisan test tests/Feature/NotificationServiceTest.php \
  --filter="can_send_notification"

# With coverage
php artisan test --coverage

# Watch mode (with Pest)
./vendor/bin/pest --watch
```

### Test Patterns Used

**Pest Framework (Modern Laravel Testing):**
```php
// Instead of PHPUnit class methods:
// public function testExample() { ... }

// Use Pest function syntax:
it('sends notification to user', function () {
    $user = User::factory()->create();
    $result = app(NotificationService::class)->send(
        $user,
        new TestNotification()
    );
    
    expect($result['success'])->toBeTrue();
});
```

### Key Test Assertions

```php
// Service results
expect($result['success'])->toBeTrue();
expect($result['error'])->toBeNull();
expect($result['notifiable_id'])->toBe($user->id);

// Batch results
expect($results['success'])->toBe(2);
expect($results['failed'])->toBe(1);
expect($results['total'])->toBe(3);
expect($results['failures'])->toHaveKey($failedUserId);

// Notification content
expect($notification->via($user))->toContain('database');
expect($db)->toHaveKey('title');
expect($broadcast)->not->toBeNull();

// Enum mappings
expect($enum->getLabel())->toBe(__('Expected Label'));
expect($enum->getNotificationClass())->toBe('App\\Notifications\\SomeNotification');
```

---

## Summary

### Architecture Principles

1. **Service-Oriented**: All notifications → NotificationService
2. **Error Resilient**: Catch and log, don't throw
3. **Async by Default**: Queue-based processing
4. **Real-time Ready**: WebSocket broadcast support
5. **Type-Safe**: Enum-based notification types
6. **Fully Logged**: Every send attempt logged
7. **Highly Testable**: Dependency injection ready
8. **Production-Grade**: Batch processing, retry logic, N+1 prevention

### Key Files

| File | Purpose |
|------|---------|
| `app/Services/NotificationService.php` | Central dispatcher |
| `app/Enums/NotificationType.php` | Type definitions |
| `app/Jobs/DispatchBloodRequestNotifications.php` | Async job handler |
| `app/Notifications/*Notification.php` | Notification classes (5 total) |
| `tests/Feature/NotificationServiceTest.php` | Service tests |
| `tests/Feature/NotificationDispatchTest.php` | Job tests |
| `tests/Unit/NotificationTypeEnumTest.php` | Enum tests |
| `tests/Feature/NotificationClassesTest.php` | Notification tests |

### Quick Reference

**Send Notification:**
```php
app(NotificationService::class)->send(
    $recipient,
    new SomeNotification(...),
    NotificationType::SOME_TYPE
);
```

**Send to Many:**
```php
app(NotificationService::class)->sendBatch(
    $recipients,
    new SomeNotification(...),
    NotificationType::SOME_TYPE
);
```

**With Retries:**
```php
app(NotificationService::class)->sendWithRetry(
    $recipient,
    new SomeNotification(...),
    maxRetries: 3,
    delayMs: 100
);
```

**Check Logs:**
```bash
tail -f storage/logs/laravel.log | grep Notification
```

**Start Broadcasting:**
```bash
php artisan reverb:start --host=127.0.0.1 --port=8080
```

**Run Tests:**
```bash
php artisan test tests/Feature/NotificationServiceTest.php ...
```
