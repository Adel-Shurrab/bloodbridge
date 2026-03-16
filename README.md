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

</div>

---

## 📋 Table of Contents

- [About](#-about)
- [Key Features](#-key-features)
- [Technology Stack](#-technology-stack)
- [System Architecture](#-system-architecture)
- [Getting Started](#-getting-started)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Development](#-development)
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

## 🚀 Getting Started

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js & NPM
- SQLite (development) or MySQL/PostgreSQL (production)

### Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/bloodbridge.git
    cd bloodbridge
    ```

2. **Install dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database setup**

    ```bash
    # Create SQLite database (development)
    touch database/database.sqlite

    # Run migrations
    php artisan migrate

    # Seed the database (optional)
    php artisan db:seed
    ```

5. **Build frontend assets**

    ```bash
    npm run build
    ```

6. **Start the development server**

    ```bash
    # Using Laravel's built-in server
    php artisan serve

    # Or use the custom dev script (runs server, queue, and vite)
    composer dev
    ```

### Quick Setup

Alternatively, use the automated setup script:

```bash
composer setup
```

---

## ⚙️ Configuration

### Environment Variables

Key configuration options in `.env`:

```env
APP_NAME=BloodBridge
APP_ENV=local
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=sqlite
# OR for MySQL/PostgreSQL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=bloodbridge

# Queue Configuration
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Mail (configure for production)
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
```

### Application Settings

Most application settings are managed through the Filament admin panel under **Settings**, including:

- General application settings
- Notification preferences
- System defaults
- Feature toggles

### Geolocation Setup

For location-based features, ensure:

1. Governorate data is seeded (`php artisan db:seed --class=GovernorateSeeder`)
2. Spatial indexes are created (handled by migrations)

---

## 📖 Usage

### For Administrators

1. **Access the Admin Panel**: Navigate to `/admin`
2. **Manage Users**: Create users and set roles (Admin, Donor, Organization)
3. **Oversee Blood Requests**: Monitor all system requests and responses
4. **Review Analytics**: Access dashboard widgets for insights

### For Organizations

1. **Access Organization Panel**: Navigate to `/organization`
2. **Create Blood Requests**:
    - Specify blood type needed
    - Set urgency level (Normal, Urgent, Critical)
    - Provide location (GPS or governorate)
    - Add patient details
3. **Manage Responses**: Review donor responses and schedule appointments
4. **Track Statistics**: Monitor request fulfillment rates

### For Donors

1. **Access Donor Panel**: Navigate to `/donor`
2. **Complete Health Profile**: Ensure accurate medical information
3. **Respond to Requests**: View and respond to blood request notifications
4. **Track Donations**: View donation history and achievements
5. **View QR Code**: Access personal QR code for quick identification

### Public Pages

- **Home** (`/`) - Landing page
- **About** (`/about`) - Information about the platform
- **Contact** (`/contact`) - Contact form
- **Registration** (`/register_selection`) - Donor or Organization signup

---

## 📁 Project Structure

```
bloodbridge/
├── app/
│   ├── Console/          # Artisan commands
│   ├── Enums/            # Blood types, statuses, urgency levels
│   ├── Filament/
│   │   ├── Admin/        # Admin panel resources & pages
│   │   ├── Donor/        # Donor panel resources
│   │   └── Organization/ # Organization panel resources
│   ├── Http/             # Controllers, middleware, requests
│   ├── Jobs/             # Async notification dispatch jobs
│   ├── Models/           # Eloquent models
│   ├── Notifications/    # Notification classes
│   ├── Services/         # Business logic
│   │   ├── BloodRequestBroadcastService.php
│   │   └── QRCodeService.php
│   └── Settings/         # Persistent settings models
├── database/
│   ├── migrations/       # Database schema
│   ├── seeders/          # Data seeders
│   └── factories/        # Model factories
├── resources/
│   ├── views/            # Blade templates
│   └── css/              # Styles
├── routes/
│   ├── web.php           # Web routes
│   └── auth.php          # Authentication routes
├── public/               # Public assets
└── tests/                # Pest tests
```

---

## 👨‍💻 Development

### Running Tests

```bash
# Run all tests
php artisan test

# Or using Pest directly
./vendor/bin/pest
```

### Code Style

```bash
# Fix code style using Pint
./vendor/bin/pint
```

### Queue Worker (Development)

```bash
# Process queued jobs
php artisan queue:listen --tries=1
```

### Development Server with All Services

```bash
# Runs server, queue worker, and vite concurrently
composer dev
```

### Debugging

- Laravel Debugbar is available in development mode
- Use `php artisan pail` for real-time log monitoring

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

Contributions are welcome! This project is under active development.

### How to Contribute

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation as needed
- Use conventional commit messages

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- Admin panel powered by [Filament](https://filamentphp.com)
- Icons and UI components from [Heroicons](https://heroicons.com)
- Geolocation features using spatial databases

---

## 📞 Support

For support, questions, or suggestions:

- Open an issue on GitHub
- Contact the development team

---

<div align="center">

**Made with ❤️ for saving lives**

_Star ⭐ this repository if you find it helpful!_

</div>
