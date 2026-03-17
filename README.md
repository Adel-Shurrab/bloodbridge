# 🩸 BloodBridge

<div align="center">

**A Modern Blood Donation Management Platform**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-FDAE4B?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDgiIGhlaWdodD0iNDgiIHZpZXdCb3g9IjAgMCA0OCA0OCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTI0IDQ4QzM3LjI1NDggNDggNDggMzcuMjU0OCA0OCAyNEM0OCAxMC43NDUyIDM3LjI1NDggMCAyNCAwQzEwLjc0NTIgMCAwIDEwLjc0NTIgMCAyNEMwIDM3LjI1NDggMTAuNzQ1MiA0OCAyNCA0OFoiIGZpbGw9IiNGRkQ3MDAiLz4KPC9zdmc+Cg==)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

_Connecting donors with those in need through intelligent matching and real-time notifications_

---

> **⚠️ Development Status**
>
> This project is currently **under active development**. Features are being continuously added and refined.
> Some functionalities may be incomplete or subject to change.
>
> **Note**: This is a **private repository**. For access and contribution details, please contact the development team.

</div>

---

## 📋 Table of Contents

- [About](#-about)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [System Architecture](#-system-architecture)
- [System Requirements](#-system-requirements)
- [Getting Started](#-getting-started)
- [Configuration](#-configuration)
- [Database Schema](#-database-schema)
- [API Endpoints](#-api-endpoints)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Development](#-development)
- [Troubleshooting](#-troubleshooting)
- [Known Issues](#-known-issues)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 About

**BloodBridge** is a comprehensive blood donation management platform designed to streamline the process of connecting blood donors with organizations and individuals in need. Built with modern PHP technologies and featuring an intuitive administrative interface, the system intelligently matches donors based on location, blood type compatibility, and availability.

### 🌟 Vision

To create a seamless, efficient, and life-saving bridge between blood donors and those in critical need through smart technology and real-time communication.

---

## ✨ Key Features

### 🏥 Blood Request Management

- **Smart Blood Request Creation** - Organizations can create detailed blood requests with urgency levels
- **Intelligent Donor Matching** - Advanced algorithm matches compatible donors based on:
    - Blood type compatibility (including Rh factor)
    - Geographic proximity using GPS coordinates and governorate data
    - Progressive radius expansion for critical requests
    - Donor availability and notification cooldown periods
- **Real-time Broadcasting** - Automatic notification dispatch to eligible donors
- **Request Tracking** - Complete lifecycle management from creation to fulfillment

### 👥 Multi-Panel System

#### 🔐 Admin Panel

- User management with enum-based role access (Admin / Donor / Organization)
- Blood request oversight and monitoring
- Donor and organization verification
- System-wide analytics and reporting
- Dashboard widgets for key metrics

#### 🩸 Donor Panel

- Personal profile management
- Health profile tracking
- Blood donation history
- QR code for quick identification
- Appointment scheduling
- Achievement system and gamification
- Blood request notifications

#### 🏢 Organization Panel

- Blood request creation and management
- Donor search and filtering
- Appointment coordination
- Organization-specific dashboards
- Request statistics and trends

### 🔔 Advanced Notification System

- **Job-Based Asynchronous Processing** - Non-blocking notification dispatch
- **Batch Processing** - Efficient handling of large donor pools
- **Multi-Channel Notifications** - Support for in-app, email, and external integrations
- **Smart Cooldown Management** - Prevents donor notification fatigue
- **Urgency-Based Prioritization** - Critical requests get expedited processing

### 📍 Location-Based Features

- **GPS Coordinate Support** - Precise location-based donor matching
- **Governorate Fallback** - Region-based matching when GPS unavailable
- **Spatial Indexing** - Optimized database queries for location searches
- **Progressive Radius Expansion** - Automatic search area expansion for critical needs

### 📊 Analytics & Reporting

- Real-time dashboard widgets
- Blood request statistics
- Donor engagement metrics
- Organization performance tracking
- Trend analysis using Flowframe Laravel Trend

### 🎫 QR Code System

- Secure donor identification
- Rate-limited generation
- Caching for performance
- Mobile-friendly display

### 🔒 Security Features

- **Role-Based Access Control** (single-role enum on `users.role`)
- Multi-tenancy support
- Secure authentication
- Data validation and sanitization
- Policy / guard-based resource access

---

## 🛠️ Technology Stack

### Backend

- **Framework**: [Laravel 12.x](https://laravel.com) - The latest PHP framework
- **PHP**: 8.3+ - Modern PHP with latest features
- **Database**: SQLite (Development) / MySQL/PostgreSQL (Production ready)
- **Queue System**: Database-backed job queue
- **Cache**: Database cache driver

### Frontend

- **Admin Interface**: [Filament 3.x](https://filamentphp.com) - Modern admin panel builder
- **CSS Framework**: [Tailwind CSS 3.x](https://tailwindcss.com) - Utility-first CSS
- **JavaScript**: Alpine.js - Lightweight reactive framework
- **Build Tool**: Vite - Next-generation frontend tooling

### Key Packages

- **filament/spatie-laravel-settings-plugin** - Persistent application settings
- **simplesoftwareio/simple-qrcode** - QR code generation
- **flowframe/laravel-trend** - Data trend analysis
- **dotswan/filament-map-picker** - Interactive map selection
- **pusher/pusher-php-server** - Real-time broadcasting support

### Development Tools

- **barryvdh/laravel-debugbar** - Comprehensive debugging
- **laravel/pint** - Code style fixer
- **pestphp/pest** - Modern testing framework
- **laravel/breeze** - Authentication scaffolding

---

## 🏗️ System Architecture

### Multi-Tenant Design

BloodBridge supports multiple organizations operating independently within the same system, each with isolated data and settings.

### Panel Structure

```
┌─────────────────────────────────────────────────┐
│                  BloodBridge                     │
├─────────────────┬──────────────┬────────────────┤
│   Admin Panel   │ Donor Panel  │  Org Panel     │
├─────────────────┼──────────────┼────────────────┤
│ • User Mgmt     │ • Profile    │ • Requests     │
│ • System Admin  │ • History    │ • Donors       │
│ • Analytics     │ • QR Code    │ • Analytics    │
│ • Permissions   │ • Responses  │ • Appointments │
└─────────────────┴──────────────┴────────────────┘
```

### Core Models

- **User** - System users (Admin, Donor, Organization staff)
- **Donor** - Blood donor profiles with health information
- **DonorHealthProfile** - Medical eligibility and restrictions
- **Organization** - Blood banks, hospitals, and NGOs
- **BloodRequest** - Blood donation requests
- **RequestResponse** - Donor responses to requests
- **Appointment** - Scheduled donation appointments
- **Achievement** - Gamification and donor motivation
- **EligibilityLog** - Donor eligibility tracking

### Blood Type Compatibility System

The platform includes a sophisticated blood type matching system that:

- Handles ABO and Rh factor compatibility
- Supports emergency scenarios with unknown blood types
- Implements fallback matching strategies
- Ensures safe blood type recommendations

---

## � System Requirements

### Minimum Requirements

- **PHP**: 8.3 or higher
- **Composer**: 2.4 or higher
- **Node.js**: 18.x or higher
- **NPM**: 9.x or higher
- **Database**: SQLite 3.26+ (development) or MySQL 8.0+ / PostgreSQL 12+ (production)
- **Disk Space**: At least 1GB for dependencies and assets

### Recommended Requirements

- **PHP**: 8.3+ with extensions: `curl`, `mbstring`, `sqlite3`, `mysql` or `pgsql`, `bcmath`, `json`
- **Node.js**: 20.x LTS
- **Database**: PostgreSQL 14+ (recommended for production)
- **Memory**: 2GB RAM minimum for development, 4GB+ for production
- **CPU**: Multi-core processor for queue processing

### Operating Systems

- **Linux**: Ubuntu 22.04+, CentOS 8+, Debian 12+
- **macOS**: 12+
- **Windows**: 10/11 with WSL2 recommended for development

---

## 🚀 Getting Started

### Prerequisites

Before starting, ensure you have all [system requirements](#-system-requirements) installed:

```bash
# Verify PHP version
php -v

# Verify Composer
composer --version

# Verify Node.js and NPM
node -v && npm -v
```

### Installation

#### Step 1: Clone the Repository

```bash
git clone https://github.com/your-organization/bloodbridge.git
cd bloodbridge
```

#### Step 2: Install PHP Dependencies

```bash
composer install
```

This will install all Laravel packages and their dependencies. You can use `--no-plugins` flag if you encounter any issues:

```bash
composer install --no-plugins
```

#### Step 3: Install Node.js Dependencies

```bash
npm install
```

#### Step 4: Environment Configuration

Copy the example environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

Your `.env` file is now created with a unique encryption key. Do not commit this file to version control.

#### Step 5: Database Setup

**For Development (SQLite):**

```bash
# Create SQLite database file
touch database/database.sqlite

# Run migrations
php artisan migrate

# (Optional) Seed initial data
php artisan db:seed
```

**For Production (MySQL/PostgreSQL):**

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bloodbridge
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:

```bash
php artisan migrate
php artisan db:seed
```

#### Step 6: Build Frontend Assets

```bash
npm run dev    # For development with watch mode
npm run build  # For production build
```

#### Step 7: Start the Application

```bash
# Using Laravel's built-in development server
php artisan serve

# The application will be available at http://localhost:8000
```

#### Step 8: Access the Admin Panel

Navigate to `http://localhost:8000/admin` and log in with your seeded admin credentials.

### Quick Setup Script

You can also use the automated setup script to run all steps:

```bash
composer setup
```

This will handle installation, environment setup, and database initialization automatically.

### Development Server with All Services

For local development with queue processing and live asset compilation:

```bash
composer dev
```

This command starts:
- Laravel development server (port 8000)
- Queue worker for job processing
- Vite dev server for asset compilation

**Note**: Requires `supervisor` or `tmux` to run multiple processes.

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME=BloodBridge
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# For MySQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bloodbridge
# DB_USERNAME=root
# DB_PASSWORD=

# For PostgreSQL
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=bloodbridge

# Queue Configuration
QUEUE_CONNECTION=database  # Options: database, redis, sqs, beanstalk

# Cache Configuration
CACHE_STORE=database       # Options: database, redis, memcached, array

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_NAME="BloodBridge"

# Session
SESSION_DRIVER=cookie

# Broadcasting (for real-time notifications)
BROADCAST_DRIVER=log       # Use 'pusher' for production WebSocket support
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1
```

### Application Settings

Most application settings are managed through the **Filament Admin Panel** under **Settings**:

1. Navigate to `/admin`
2. Go to **Settings** section
3. Configure:
   - General application settings
   - Notification preferences
   - System defaults
   - Feature toggles
   - Blood type matching rules

### Geolocation Setup

For location-based donor matching features:

1. **Seed governorate data:**
   ```bash
   php artisan db:seed --class=GovernorateSeeder
   ```

2. **Verify migration:** Ensure spatial indexes are created (handled automatically by migrations)

3. **Test location features:** Use the admin panel to create a test blood request with location data

### Cache Configuration

For optimal performance, configure cache:

```bash
# Clear all caches
php artisan cache:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🗄️ Database Schema

### Core Models & Relationships

#### Users
- **users** - System users (Admin, Donor, Organization)
  - `id` - Primary key
  - `email` - Unique email address
  - `password` - Hashed password
  - `role` - Enum: Admin, Donor, Organization
  - `phone` - Contact phone

#### Donors
- **donors** - Donor profiles
  - Relationship: `user_id` → users
  - Blood type and health information
  
  - **donor_health_profiles** - Detailed medical eligibility
    - Relationship: `donor_id` → donors
    - Eligibility status and restrictions

#### Organizations
- **organizations** - Blood banks, hospitals, NGOs
  - Relationship: `user_id` → users
  - Contact and location information

#### Blood Requests
- **blood_requests** - Donation requests
  - Relationship: `organization_id` → organizations
  - Blood type needed
  - Urgency level (Normal, Urgent, Critical)
  - Status (Pending, Matched, Fulfilled, Cancelled)
  - Location (GPS coordinates or governorate)
  
  - **request_responses** - Donor responses to requests
    - Relationship: `blood_request_id` → blood_requests
    - Relationship: `donor_id` → donors
    - Response status and timestamp

#### Appointments
- **appointments** - Scheduled donations
  - Relationship: `blood_request_id` → blood_requests
  - Relationship: `donor_id` → donors
  - Status and scheduling information

#### Other Models
- **governorates** - Geographic regions for location matching
- **eligibility_logs** - Tracking of donor eligibility decisions
- **achievements** - Gamification and donor rewards
- **notifications** - Persistent notification records

### Key Indexes

- `blood_requests(blood_type, status, created_at)`
- `donors(blood_type, governorate_id)`
- `donors(latitude, longitude)` - Spatial index for GPS matching
- `appointment_donors(appointment_id, donor_id)` - Unique constraint

---

## 🔌 API Endpoints

### Authentication

```
POST   /api/auth/login           - Login user
POST   /api/auth/logout          - Logout user
POST   /api/auth/register        - Register new account
POST   /api/auth/refresh         - Refresh auth token
```

### Blood Requests

```
GET    /api/blood-requests       - List all requests
POST   /api/blood-requests       - Create new request
GET    /api/blood-requests/{id}  - Get request details
PUT    /api/blood-requests/{id}  - Update request
DELETE /api/blood-requests/{id}  - Cancel request

GET    /api/blood-requests/{id}/responses  - Get request responses
```

### Donor Features

```
GET    /api/donors                    - List donors
GET    /api/donors/{id}               - Get donor profile
PUT    /api/donors/{id}               - Update donor profile
GET    /api/donors/{id}/health        - Get health profile
POST   /api/donors/{id}/responses     - Submit response to request

GET    /api/donors/{id}/appointments  - Get donor's appointments
GET    /api/donors/{id}/achievements  - Get donor achievements
```

### Organizations

```
GET    /api/organizations              - List organizations
POST   /api/organizations              - Create organization
GET    /api/organizations/{id}         - Get organization details
PUT    /api/organizations/{id}         - Update organization

GET    /api/organizations/{id}/requests  - Organization's requests
```

### Notifications

```
GET    /api/notifications           - List user notifications
PUT    /api/notifications/{id}/read  - Mark as read
DELETE /api/notifications/{id}       - Delete notification
```

### Search & Matching

```
GET    /api/donors/search?blood_type=O+&location=cairo    - Search compatible donors
GET    /api/compatibility/{donor_id}/{blood_type}         - Check blood compatibility
```

**Note**: Full API documentation with request/response examples is available in [API_DOCUMENTATION.md](API_DOCUMENTATION.md) (if available).

---

## 📖 Usage

### For Administrators

#### Accessing the Admin Panel

1. Navigate to `http://localhost:8000/admin`
2. Log in with admin credentials
3. You'll see the admin dashboard with key metrics

#### Managing Users

1. **Create a New User**
   - Go to **Users** → **Create**
   - Fill in email, password, and select role (Admin, Donor, or Organization)
   - Click **Create**

2. **Verify Users**
   - Navigate to **Users** list
   - Verify/unverify donor and organization accounts
   - Track user activity and engagement

#### Overseeing Blood Requests

1. **View All Requests**
   - Go to **Blood Requests**
   - Filter by status, blood type, or urgency
   - View request details and donor responses

2. **Monitor Matching**
   - Check request broadcasts
   - See which donors have been notified
   - Review response statistics

3. **Manage Analytics**
   - Access dashboard widgets
   - View key metrics (requests created, responses, fulfillment rate)
   - Generate reports

#### System Configuration

1. **Settings**
   - Click **Settings** in admin panel
   - Configure application defaults
   - Manage notification preferences
   - Enable/disable features

Example configuration:
```
- Blood Type Matching Rules: Enable strict/loose matching
- Notification Radius: Default search radius for donors
- Urgency Levels: Define urgency criteria
- System Announcements: Send broadcast messages
```

---

### For Organizations (Blood Banks, Hospitals, NGOs)

#### Accessing the Organization Panel

1. Register as an organization at `/register_selection`
2. Navigate to `http://localhost:8000/organization`
3. Complete organization profile (name, location, contact)

#### Creating Blood Requests

1. **Create New Request**
   - Click **New Blood Request**
   - Fill in request details:
     - **Blood Type Needed**: Select from ABO + Rh factor
     - **Quantity**: Number of units needed
     - **Urgency Level**:
       - **Normal**: Routine request (3-7 days)
       - **Urgent**: High priority (1-3 days)
       - **Critical**: Life-saving (immediate)
     - **Location**: Enter GPS coordinates or select governorate
     - **Patient Details**: Add demographic info (name, age, condition)
     - **Special Notes**: Medical requirements or restrictions

2. **Example Request Creation**
   ```
   Blood Type: O+
   Quantity: 3 units
   Urgency: Critical
   Location: Cairo (Latitude: 30.0444, Longitude: 31.2357)
   Patient: 45-year-old male, surgical emergency
   ```

3. **Submit Request**
   - Click **Submit**
   - System automatically notifies compatible donors
   - Request enters broadcast queue

#### Managing Donor Responses

1. **View Responses**
   - Go to **My Requests**
   - Click on a request to view responses
   - See donor status: Accepted, Pending, Declined

2. **Schedule Appointments**
   - Click **Schedule Appointment** next to donor response
   - Set date and time
   - Add appointment notes
   - Confirm scheduling

3. **Manage Appointments**
   - View calendar of scheduled appointments
   - Send reminders to donors
   - Mark as completed or cancelled

#### Analytics & Reporting

1. **Request Statistics**
   - Total requests created
   - Response rate (% of notifications that responded)
   - Fulfillment rate (% fulfilled)
   - Average response time

2. **Donor Database**
   - Search donors by blood type
   - Filter by location/availability
   - View donor history

---

### For Donors

#### Accessing the Donor Panel

1. Register as a donor at `/register_selection`
2. Navigate to `http://localhost:8000/donor`
3. Complete your profile

#### Setting Up Your Profile

1. **Basic Information**
   - Name, age, gender
   - Contact information
   - Blood type (verified by admin)

2. **Health Profile**
   - Medical history
   - Current medications
   - Previous donations
   - Any restrictions or allergies

3. **Location Information**
   - Enable GPS for location-based matching
   - Or select your governorate
   - This helps organizations find you when blood is needed

#### Responding to Blood Requests

1. **Receive Notifications**
   - When a compatible blood request is created, you'll be notified
   - Check **Notifications** dashboard
   - View match distance and urgency level

2. **Review Request Details**
   - Blood type needed
   - Patient information (age, condition)
   - Urgency level
   - Location and distance
   
3. **Submit Your Response**
   - Click **Accept** if you can donate
   - Click **Decline** with reason if you cannot
   - Your response is sent to the organization

4. **Schedule Appointment**
   - Once accepted, organization will contact you
   - View scheduled appointment in calendar
   - Receive reminders before appointment

#### Tracking Your Donations

1. **Donation History**
   - View all completed donations
   - Track donation dates
   - View blood units donated

2. **Health Profile Updates**
   - Update health information after donations
   - Log any post-donation side effects
   - Update eligibility status

#### Achievements & Gamification

1. **View Achievements**
   - First Donation badge
   - 5 Donations milestone
   - 10+ Donations level
   - Regular Donor status

2. **Leaderboard**
   - See top donors in your region
   - Track your ranking
   - Earn recognition

3. **QR Code**
   - Generate personal QR code for identification
   - Show at donation centers for quick recognition
   - Rate-limited to 10 per minute

#### Appointment Management

1. **View Upcoming Appointments**
   - Calendar view of scheduled donations
   - Appointment details and location
   - Organization contact information

2. **Manage Appointments**
   - Reschedule if needed
   - Cancel appointments with reason
   - Add notes about availability

---

### Public Pages

**Non-Authenticated Users:**

1. **Home** (`/`) - Landing page with overview
2. **About** (`/about`) - Information about BloodBridge
3. **Contact** (`/contact`) - Contact form for inquiries
4. **Registration** (`/register_selection`) - Choose role:
   - Register as Donor
   - Register as Organization
5. **Login** (`/login`) - Sign in to your account

---

## 📁 Project Structure

```
bloodbridge/
├── app/
│   ├── Console/              # Artisan commands
│   │   └── Commands/         # Custom CLI commands
│   ├── Constants/            # Application constants (e.g., coordinates)
│   ├── Contracts/            # Service interfaces
│   ├── Enums/                # PHP Enums
│   │   ├── AppointmentStatus.php
│   │   ├── BloodRequestStatus.php
│   │   ├── BloodType.php     # Blood type definitions with compatibility
│   │   └── Gender.php
│   ├── Events/               # Application events
│   ├── Filament/             # Admin & panel interfaces
│   │   ├── Admin/            # Admin panel resources & pages
│   │   │   ├── Resources/    # Filament CRUD resources
│   │   │   └── Pages/        # Admin custom pages
│   │   ├── Donor/            # Donor panel
│   │   └── Organization/     # Organization panel
│   ├── Http/                 # HTTP layer
│   │   ├── Controllers/      # Request handlers
│   │   ├── Middleware/       # HTTP middleware
│   │   └── Requests/         # Form request validation
│   ├── Jobs/                 # Queued jobs
│   │   └── DispatchNotifications.php
│   ├── Listeners/            # Event listeners
│   ├── Livewire/             # Livewire components (if used)
│   ├── Mail/                 # Mailable classes
│   ├── Models/               # Eloquent models
│   │   ├── User.php          # System user
│   │   ├── Donor.php         # Donor profile
│   │   ├── BloodRequest.php  # Blood request
│   │   └── ...
│   ├── Notifications/        # Notification classes
│   │   ├── BloodRequestMatchNotification.php
│   │   ├── DonorResponseNotification.php
│   │   └── ...
│   ├── Policies/             # Authorization policies
│   ├── Providers/            # Service providers
│   ├── Services/             # Business logic
│   │   ├── BloodRequestBroadcastService.php  # Core matching logic
│   │   ├── DonorEligibilityService.php       # Eligibility checks
│   │   └── QRCodeService.php                 # QR code generation
│   ├── Settings/             # Persistent settings models
│   └── View/                 # View components & traits
├── bootstrap/                # Application bootstrap
│   ├── app.php              # Bootstrap script
│   └── providers.php        # Service provider registration
├── config/                  # Configuration files
│   ├── app.php             # Application config
│   ├── auth.php            # Authentication config
│   ├── database.php        # Database connections
│   ├── filament.php        # Filament admin panel config
│   ├── mail.php            # Mail configuration
│   ├── queue.php           # Queue driver config
│   └── ...
├── database/
│   ├── migrations/          # Schema migrations
│   │   ├── 2024_01_01_000000_create_users_table.php
│   │   ├── 2024_01_02_000000_create_blood_requests_table.php
│   │   └── ...
│   ├── seeders/             # Database seeders
│   │   ├── DatabaseSeeder.php
│   │   ├── AdminUserSeeder.php
│   │   └── GovernorateSeeder.php
│   └── factories/           # Model factories for testing
│       ├── UserFactory.php
│       ├── BloodRequestFactory.php
│       └── ...
├── resources/
│   ├── views/               # Blade templates
│   │   ├── layouts/         # Layout templates
│   │   ├── pages/           # Page templates
│   │   ├── components/      # Reusable components
│   │   └── ...
│   └── css/                 # Stylesheets
│       └── app.css
├── routes/                  # Route definitions
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   ├── auth.php            # Authentication routes
│   └── console.php         # Console command routes
├── storage/
│   ├── app/                # File storage
│   ├── logs/               # Application logs
│   └── framework/          # Framework files
├── tests/                  # Test suite
│   ├── Feature/            # Feature tests
│   │   ├── NotificationClassesTest.php
│   │   └── ...
│   ├── Unit/               # Unit tests
│   └── TestCase.php        # Base test class
├── public/                 # Public assets
│   ├── index.php          # Application entry point
│   ├── assets/            # Static assets
│   └── build/             # Vite build output
├── .env.example           # Example environment file
├── artisan                # Artisan CLI tool
├── composer.json          # PHP dependencies
├── package.json           # Node dependencies
├── phpunit.xml            # PHPUnit configuration
├── tailwind.config.js     # Tailwind CSS configuration
├── vite.config.js         # Vite build configuration
└── README.md             # This file
```

### Key Files

| File | Purpose |
|------|---------|
| `app/Services/BloodRequestBroadcastService.php` | Core matching and notification algorithm |
| `app/Models/BloodRequest.php` | Blood request model with relationships |
| `app/Jobs/DispatchNotifications.php` | Async notification dispatcher |
| `database/migrations/` | Database schema definitions |
| `routes/web.php` | Web route definitions |
| `config/filament.php` | Admin panel configuration |

---

## 👨‍💻 Development

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/NotificationClassesTest.php

# Run with coverage report
php artisan test --coverage

# Using Pest directly
./vendor/bin/pest

# Watch mode (re-run on file changes)
./vendor/bin/pest --watch
```

### Code Quality

```bash
# Fix code style using Pint
./vendor/bin/pint

# Check without fixing
./vendor/bin/pint --test

# Fix specific file
./vendor/bin/pint app/Models/BloodRequest.php
```

### Queue Worker (Development)

```bash
# Process queued jobs with retry limit
php artisan queue:listen --tries=1

# Process specific queue
php artisan queue:listen notifications

# Monitor queue status
php artisan queue:monitor
```

### Development Tools

```bash
# Real-time log monitoring
php artisan pail

# Laravel Tinker (REPL)
php artisan tinker

# Optimize development environment
php artisan optimize:clear
```

### Database Migrations

```bash
# Create new migration
php artisan make:migration create_table_name

# Run pending migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Rollback all and re-run
php artisan migrate:refresh

# Refresh and seed
php artisan migrate:fresh --seed
```

### Debugging

- **Laravel Debugbar**: Available at the bottom of pages in development mode
  - Shows queries, routes, views, and performance metrics
  - Access configuration in `config/debugbar.php`

- **Pail Logs**: Monitor real-time logs with:
  ```bash
  php artisan pail
  ```

- **Browser DevTools**: Inspect network requests and WebSocket broadcasts

---

## 🔧 Troubleshooting

### Installation Issues

**Problem**: `composer install` fails with dependency conflicts

**Solution**:
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Try installation again
composer install -v
```

---

**Problem**: `npm install` fails with Node version

**Solution**:
```bash
# Check Node version
node -v

# Update npm
npm install -g npm@latest

# Clear npm cache
npm cache clean --force
rm package-lock.json
npm install
```

---

### Database Issues

**Problem**: `php artisan migrate` fails with "SQLSTATE[HY000]: General error"

**Solutions**:
```bash
# Ensure SQLite file has correct permissions
chmod 666 database/database.sqlite
chmod 755 database/

# Or for MySQL, verify connection:
php artisan tinker
# Then: DB::connection()->getPdo()
```

---

**Problem**: Migrations roll back unexpectedly

**Solutions**:
```bash
# Check migration status
php artisan migrate:status

# See which migrations actually ran
php artisan migrate:status --pending

# If corrupted, reset (⚠️ deletes data):
php artisan migrate:reset
php artisan migrate --seed
```

---

### Queue Issues

**Problem**: Jobs not processing

**Solutions**:
```bash
# Check if queue worker is running
ps aux | grep "queue:listen"

# Start queue worker
php artisan queue:listen --tries=1

# Check failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry {job-id}

# Flush all failed jobs
php artisan queue:flush
```

---

**Problem**: Notifications not appearing

**Solutions**:
1. Verify queue is processing:
   ```bash
   php artisan queue:listen notifications
   ```

2. Check notification broadcasts in config:
   ```php
   // config/broadcasting.php
   'default' => env('BROADCAST_DRIVER', 'log'),
   ```

3. Check database for unread notifications:
   ```bash
   php artisan tinker
   # Then: DB::table('notifications')->get()
   ```

---

### Performance Issues

**Problem**: Slow blood request matching

**Solution**: The matching algorithm can be optimized:

```bash
# Ensure indexes exist
php artisan migrate --force

# Clear caches
php artisan cache:clear
php artisan config:cache

# Check query performance
php artisan tinker
# DB::enableQueryLog();
# App\Models\BloodRequest::with('organization')->get();
# DB::getQueryLog();
```

---

**Problem**: High memory usage

**Solutions**:
```bash
# Increase PHP memory limit in .env or php.ini
php -d memory_limit=512M artisan queue:listen

# Or set in .env
PHP_MEMORY_LIMIT=512M php artisan queue:listen

# Check queue job size
php artisan tinker
# DB::table('jobs')->select(DB::raw('LENGTH(payload) as size'))->get();
```

---

### Asset Issues

**Problem**: CSS/JS not loading in development

**Solution**:
```bash
# Ensure Vite is running
npm run dev

# Or rebuild assets
npm run build

# Check Vite configuration
cat vite.config.js
```

---

**Problem**: Assets not updated after changes

**Solution**:
```bash
# Clear build cache
rm -rf public/build
npm run build

# Or in development
npm run dev
```

---

### Authentication Issues

**Problem**: Cannot log in to admin panel

**Solutions**:
1. Verify admin user exists:
   ```bash
   php artisan tinker
   # App\Models\User::where('role', 'admin')->first()
   ```

2. If missing, create one:
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```

3. Reset password:
   ```bash
   php artisan tinker
   # $user = App\Models\User::find(1);
   # $user->password = Hash::make('password');
   # $user->save();
   ```

---

**Problem**: Session not persisting

**Solutions**:
```bash
# Clear sessions
php artisan session:table
php artisan migrate

# Or use cookie sessions (edit .env)
SESSION_DRIVER=cookie

# Clear all sessions
php artisan tinker
# DB::table('sessions')->delete()
```

---

### API Issues

**Problem**: API endpoints return 401 Unauthorized

**Solution**: Ensure you're sending authentication token:
```bash
curl -H "Authorization: Bearer {token}" \
     -H "Accept: application/json" \
     http://localhost:8000/api/blood-requests
```

---

**Problem**: CORS errors in development

**Solution**: Update `config/cors.php`:
```php
'allowed_origins' => ['*'], // for development only
'supports_credentials' => true,
```

---

### General Debugging Tips

```bash
# Access Laravel Tinker (REPL)
php artisan tinker

# Inside Tinker:
User::all();  # List all users
BloodRequest::latest()->first();  # Latest blood request
DB::table('users')->count();  # Count table
Event::dispatch(new YourEvent());  # Test events
Bus::dispatch(new YourJob());  # Test jobs
```

---

## ⚠️ Known Issues & Limitations

### Current Limitations

1. **Single Role Per User**
   - Users can only have one role (Admin, Donor, or Organization)
   - A user cannot simultaneously be a donor and organization staff
   - **Workaround**: Create separate accounts for different roles

2. **Location-Based Matching**
   - GPS-based matching requires valid latitude/longitude
   - Fallback to governorate-based matching if coordinates unavailable
   - Database must be seeded with governorate data for region matching

3. **Real-time Notifications**
   - Broadcasting currently uses `log` driver in development
   - Production requires Pusher or similar WebSocket provider
   - See [Deployment](#-deployment) for production setup

4. **Queue Processing**
   - Notifications are sent asynchronously via queued jobs
   - Queue worker must be running for messages to be sent
   - Failed jobs are stored in `failed_jobs` table

5. **Concurrent Blood Request Creation**
   - High-volume simultaneous requests may create duplicate matches
   - **Workaround**: Implement request-level locking in future versions

### Known Bugs

1. **Health Profile Eligibility**
   - Eligibility status may not update immediately after profile changes
   - **Solution**: Run `php artisan queue:listen` to process pending jobs

2. **QR Code Generation**
   - Rate limiting (10 per minute) may reject rapid generation requests
   - **Solution**: Implement caching for frequently accessed codes

---

## 🚀 Deployment

### Pre-Deployment Checklist

- [ ] All tests passing (`php artisan test`)
- [ ] Code style fixed (`./vendor/bin/pint`)
- [ ] `.env` configured for production
- [ ] Database backups created
- [ ] Assets compiled (`npm run build`)
- [ ] SSL certificate obtained
- [ ] Mail service configured
- [ ] Broadcasting service set up (Pusher, etc.)

### Environment Preparation

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

CACHE_STORE=redis
SESSION_DRIVER=cookie
QUEUE_CONNECTION=redis

BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Deploying with Git

1. **Set up SSH key** on your server
2. **Clone repository**:
   ```bash
   git clone git@github.com:your-org/bloodbridge.git
   cd bloodbridge
   ```

3. **Install dependencies**:
   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci
   npm run build
   ```

4. **Configure environment**:
   ```bash
   cp .env.example .env
   # Edit .env with production values
   php artisan key:generate
   ```

5. **Set up database** (first deployment):
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Set permissions**:
   ```bash
   chown -R www-data:www-data storage bootstrap/cache public
   chmod -R 775 storage bootstrap/cache
   ```

7. **Optimize for production**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Web Server Configuration

#### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/bloodbridge/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache

```apache
<Directory /var/www/bloodbridge/public>
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [L]
    </IfModule>
</Directory>
```

### Queue Processing in Production

Use Supervisor to manage queue workers:

```ini
[program:bloodbridge-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/bloodbridge/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/bloodbridge-queue.log
```

### Database Backups

```bash
# Automated daily backup (add to crontab)
0 2 * * * mysqldump -u root -p bloodbridge > /backups/bloodbridge-$(date +\%Y\%m\%d).sql
```

### Monitoring

```bash
# Monitor queue
php artisan queue:monitor

# Monitor logs
tail -f storage/logs/laravel.log

# Check failed jobs
php artisan queue:failed
```

---

## 🗺️ Roadmap

### Planned Features

- [ ] Multi-language support (Arabic, English)
- [ ] Mobile application integration
- [ ] Telegram bot notifications
- [ ] WhatsApp integration for alerts
- [ ] Advanced donor analytics
- [ ] Blood inventory management
- [ ] Appointment reminders (SMS/Email)
- [ ] Donor eligibility calculator
- [ ] Campaign management for blood drives
- [ ] API for third-party integrations

### In Progress

- [x] Core blood request broadcasting
- [x] QR code system
- [x] Multi-panel architecture
- [x] Enum-based roles (Admin / Donor / Organization)
- [x] Location-based matching

---

## 🤝 Contributing

### Setting Up Your Development Environment

Ensure you have all [system requirements](#-system-requirements) installed first.

1. **Fork and Clone** (for private repo, contact maintainers for access):
   ```bash
   git clone https://github.com/your-org/bloodbridge.git
   cd bloodbridge
   git checkout -b feature/your-feature-name
   ```

2. **Set Up Local Environment**:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

3. **Create Feature Branch**:
   ```bash
   git checkout -b feature/amazing-feature
   # or for bug fixes
   git checkout -b fix/bug-description
   ```

### Development Workflow

#### Before Making Changes

1. **Ensure Tests Pass**:
   ```bash
   php artisan test
   ```

2. **Update Code Style**:
   ```bash
   ./vendor/bin/pint
   ```

3. **Stay Updated**:
   ```bash
   git pull origin main
   ```

#### Making Your Changes

1. **Code Quality Standards**:
   - Follow **PSR-12** coding standards
   - Use descriptive variable and function names
   - Add comments for complex logic
   - Write type hints for all methods

2. **Writing Tests**:
   - Write tests for new features
   - Update tests for changed functionality
   - Aim for >80% code coverage

   Example:
   ```php
   it('creates blood request with valid data', function () {
       $data = [
           'blood_type' => 'O+',
           'urgency' => 'critical',
       ];
       
       $response = $this->post('/api/blood-requests', $data);
       
       expect($response->status())->toBe(201);
   });
   ```

3. **Database Changes**:
   - Create migrations for schema changes
   - Name migrations clearly: `create_table_name` or `add_field_to_table`
   - Update seeders if needed

4. **Documentation**:
   - Update README.md if behavior changes
   - Add docblock comments to classes/methods:
     ```php
     /**
      * Create a new blood request and broadcast to compatible donors
      * @param array $data Request data
      * @return BloodRequest
      */
     public function create(array $data): BloodRequest
     ```

### Commit Guidelines

Use **conventional commits** for clarity:

```bash
# Feature
git commit -m "feat: add donor search filter by blood type"

# Bug fix
git commit -m "fix: resolve blood type compatibility check"

# Documentation
git commit -m "docs: update API endpoint examples"

# Tests
git commit -m "test: add blood request matching tests"

# Refactoring
git commit -m "refactor: simplify donor eligibility logic"
```

Format:
```
<type>(<scope>): <subject>

<body>

<footer>
```

Example:
```
feat(notifications): add urgency-based queue prioritization

Implement queue priority levels so critical blood requests
are processed before normal urgency requests.

Closes #123
```

### Submitting Changes

1. **Commit and Push**:
   ```bash
   git add .
   git commit -m "feat: describe your changes"
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request**:
   - Go to GitHub/GitLab
   - Open PR against `main` branch
   - Fill in PR template
   - Link related issues: "Closes #123"

3. **PR Description Template**:
   ```markdown
   ## Description
   Brief description of changes
   
   ## Type of Change
   - [ ] Bug fix
   - [ ] New feature
   - [ ] Breaking change
   - [ ] Documentation update
   
   ## Testing
   - [ ] Unit tests added
   - [ ] Existing tests pass
   - [ ] Integration tested
   
   ## Checklist
   - [ ] Code follows style guide
   - [ ] Documentation updated
   - [ ] No new warnings generated
   ```

### Code Review Process

1. **Automated Checks**:
   - Tests must pass
   - Code style must be fixed
   - No new warnings introduced

2. **Peer Review**:
   - At least 2 approvals required
   - Address feedback constructively
   - Request changes if needed

3. **Merge**:
   - Squash commits on merge (keeps history clean)
   - Delete feature branch after merge
   - Update CHANGELOG if applicable

### Reporting Issues

#### Bug Reports

Include:
- **Describe the bug**: What happened vs. what was expected
- **Steps to reproduce**: Exact steps to reproduce the issue
- **Environment**: PHP version, OS, browser, etc.
- **Screenshots/logs**: Any helpful debugging info

Example:
```
## Bug: Blood request notifications not sent

**Describe the bug**
When creating a critical blood request, no donor notifications are sent.

**Steps to reproduce**
1. Log in as organization
2. Create blood request with urgency=critical
3. Check Notifications (no message appears)

**Environment**
- PHP 8.3
- Laravel 12.0
- macOS 14.2

**Error logs**
```

#### Feature Requests

Include:
- **Description**: What feature is needed?
- **Use case**: Why is it needed?
- **Proposed solution**: How should it work?
- **Alternatives considered**: Other approaches?

Example:
```
## Feature: SMS Notifications for Critical Requests

**Use case**
Donors may miss in-app notifications. SMS alerts would increase response rates.

**Proposed solution**
Add SMS integration via Twilio for critical urgency requests.

**Alternatives**
- Email notifications (slower)
- Push notifications (requires mobile app)
```

### Development Guidelines

#### Architecture Principles

1. **Single Responsibility**: Each class has one reason to change
2. **Dependency Injection**: Inject dependencies, don't create them
3. **Loose Coupling**: Components should be independent
4. **High Cohesion**: Related functionality together

Examples:
```php
// ❌ Bad: Too many responsibilities
class BloodRequestController {
    public function create(Request $request) {
        // Validate
        // Save to database
        // Send notifications
        // Generate QR code
        // Log activity
    }
}

// ✅ Good: Separated concerns
class BloodRequestController {
    public function __construct(
        private BloodRequestService $service,
    ) {}
    
    public function create(Request $request) {
        return $this->service->create($request->validated());
    }
}
```

#### Error Handling

```php
// ❌ Bad: Generic exceptions
throw new Exception("Something went wrong");

// ✅ Good: Specific, meaningful exceptions
throw new DonorNotEligibleException(
    "Donor has insufficient wait time since last donation"
);
```

#### Testing Best Practices

```php
// ❌ Bad: Testing implementation details
test('setName sets the name property', function () {
    $donor = new Donor();
    $donor->setName('John');
    expect($donor->name)->toBe('John');
});

// ✅ Good: Testing behavior
test('donor can update their profile', function () {
    $donor = Donor::factory()->create();
    $response = $this->actingAs($donor)->put(
        "/donor/{$donor->id}", 
        ['name' => 'Jane']
    );
    expect($response->status())->toBe(200);
    expect($donor->refresh()->name)->toBe('Jane');
});
```

### Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Pest Testing](https://pestphp.com)
- [PSR-12 Coding Standards](https://www.php-fig.org/psr/psr-12/)
- [Conventional Commits](https://www.conventionalcommits.org/)

---

## �️ Roadmap

### In Development (Current)

- [x] Core blood request broadcasting system
- [x] Intelligent donor-request matching algorithm
- [x] Multi-panel architecture (Admin, Donor, Organization)
- [x] Enum-based role system (Admin / Donor / Organization)
- [x] Location-based donor matching (GPS + Governorate)
- [x] QR code identification system
- [x] Achievement/gamification system
- [x] Database-backed notification system
- [x] Async job processing for notifications
- [x] Health profile eligibility tracking
- [x] Appointment scheduling

### Short-term (Next 2-3 Months)

- [ ] Mobile-responsive Donor/Organization panels
- [ ] Advanced donor filtering (health conditions, availability)
- [ ] Appointment reminders (Email/SMS)
- [ ] Blood inventory management system
- [ ] Donation feedback forms
- [ ] Enhanced analytics and reporting
- [ ] User activity logging and audit trails
- [ ] Bulk donor import from CSV
- [ ] Notification preferences (frequency, channels)

### Medium-term (3-6 Months)

- [ ] Multi-language support (Arabic, English)
- [ ] Telegram bot for notifications
- [ ] WhatsApp integration for alerts
- [ ] Campaign management for blood drives
- [ ] Donor eligibility calculator
- [ ] Advanced statistical analysis (Flowframe)
- [ ] Email templates for other notifications
- [ ] Backend API v1.0 release
- [ ] Webhook support for third-party integrations
- [ ] Database query optimization and caching

### Long-term (6-12 Months)

- [ ] Mobile application (iOS/Android)
- [ ] Real-time broadcasting with Pusher integration
- [ ] Blood bank inventory sync
- [ ] Hospital EHR integration
- [ ] Machine learning for donor matching
- [ ] Advanced reporting and BI tools
- [ ] Public API for third-party developers
- [ ] Blockchain-based donation verification
- [ ] Multi-tenancy enhancements

---

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### What This Means

- ✅ Free to use, modify, and distribute
- ✅ Can be used for commercial projects
- ✅ Must include license notice
- ✅ Provided "as-is" without warranty

---

## 🙏 Acknowledgments

- **[Laravel](https://laravel.com)** - Modern PHP framework powering the backend
- **[Filament](https://filamentphp.com)** - Beautiful admin panel builder
- **[Tailwind CSS](https://tailwindcss.com)** - Utility-first CSS framework
- **[Pest](https://pestphp.com)** - Modern testing framework
- **[Simple QRCode](https://github.com/SimpleSoftwareIO/simple-qrcode)** - QR code generation
- **[Pusher](https://pusher.com)** - Real-time broadcasting capabilities

---

## 📞 Support & Contact

### Getting Help

**For Development/Technical Issues:**
- Open an issue on the repository
- Check existing issues before creating new ones
- Provide detailed reproduction steps and environment info

**For Access to Private Repository:**
- Contact the development team
- Submit your credentials and use case
- Await approval and access grant

**For Feature Requests:**
- Open an issue with label `enhancement`
- Describe your use case
- Link related issues

**For Bug Reports:**
- Open an issue with label `bug`
- Include error logs and screenshots
- Include reproduction steps

### Community

Connect with other contributors:
- GitHub Discussions (if enabled)
- Discord/Slack community (if available)
- Monthly community calls (schedule shared on repo)

---

## 🔐 Security

### Reporting Security Vulnerabilities

**Please do not open public issues for security vulnerabilities.**

Instead:
1. Email security concerns to the maintainers privately
2. Include reproduction steps and impact assessment
3. Allow 90 days for fixes before public disclosure
4. Your name and attribution (if desired) will be included in security patch notes

---

## 💡 Questions & FAQ

**Q: Can I use BloodBridge for production blood donation management?**
> Possibly, but this is under active development. Thoroughly test all features and conduct security audits before production use.

**Q: Is there a demo version?**
> Currently, the project is private. Contact the development team for demo access.

**Q: How do I integrate with our blood bank system?**
> API endpoints are available. See [API Endpoints](#-api-endpoints) section for details. Full integration documentation coming soon.

**Q: Can I deploy on Windows?**
> Deployment is possible but not recommended. Use Linux servers for production. Development on Windows works excellently with WSL2.

**Q: What database works best?**
> PostgreSQL 14+ is recommended for production. MySQL 8.0+ and SQLite work but have limitations at scale.

**Q: How do I report issues?**
> See [Support & Contact](#-support--contact) section for guidelines on reporting bugs, security issues, and feature requests.

**Q: Can I contribute to this project?**
> Yes! See the [Contributing](#-contributing) section for guidelines and setup instructions. This is a private repository, so contact maintainers for access.

---

<div align="center">

## 🩸 Made with ❤️ for Saving Lives

**BloodBridge** — Connecting donors with those in need through technology and compassion.

[⭐ Star this repository](https://github.com/your-org/bloodbridge) | [📖 Docs](README.md) | [🐛 Report Bug](https://github.com/your-org/bloodbridge/issues) | [💡 Request Feature](https://github.com/your-org/bloodbridge/issues) | [📞 Contact](mailto:contact@bloodbridge.dev)

---

### Last Updated

December 2024 • Built by dedicated developers committed to saving lives through blood donation

</div>
