# 🩸 BloodBridge - Centralized Blood Donation Platform

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4-F59E0B)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Status](https://img.shields.io/badge/status-in%20development-yellow)](https://github.com/Adel-Shurrab/bloodbridge)

> **🚧 Project Status:** Active Development  
> Connecting blood donors with healthcare organizations to save lives across Palestine

---

## 🎯 Problem Statement

During critical blood shortages and medical emergencies, finding compatible blood donors can take hours of manual phone calls and coordination. This delay can be life-threatening. Traditional methods are:

- **Slow**: Manual calling of potential donors one by one
- **Inefficient**: No centralized database of eligible donors
- **Unreliable**: No automated eligibility checking
- **Fragmented**: Each hospital maintains separate donor lists

**BloodBridge** solves this by creating a centralized platform that instantly matches urgent blood requests with eligible donors based on medical criteria, blood type compatibility, and geographic proximity.

---

## 💡 The Solution

A comprehensive web platform that:

1. **Automates donor screening** using medical eligibility algorithms
2. **Matches donors instantly** with blood requests based on type and location  
3. **Notifies eligible donors** in real-time via multiple channels
4. **Ensures compliance** through comprehensive audit logging
5. **Verifies organizations** to maintain platform credibility

---

## ✨ Key Features

### 👤 For Blood Donors

- **🔐 Secure Registration**
  - Create verified donor profiles with comprehensive medical history
  - Privacy-focused data handling with encrypted sensitive information
  
- **🩺 Intelligent Eligibility System**
  - Automated screening based on:
    - Medical history questionnaire (chronic diseases, recent surgeries)
    - Last donation date (enforces 56-day minimum interval between donations)
    - Age requirements (18-65 years)
    - Weight requirements (minimum 50kg)
    - Health conditions and medications
  - Real-time eligibility status updates
  - Clear explanations when ineligible
  
- **📍 Location-Based Matching**
  - Get notified for blood requests in your governorate/city
  - Distance-based prioritization for urgent requests
  - Flexible notification preferences
  
- **📊 Donation History Dashboard**
  - Track all your past donations
  - View impact statistics (lives saved, units donated)
  - Downloadable donation certificates
  - Eligibility countdown timer
  
- **🔔 Smart Notifications**
  - Real-time alerts for matching blood type requests
  - Email notifications with request details
  - SMS alerts for critical/emergency requests (planned)
  - In-app notification center
  
- **✅ Availability Toggle**
  - Control when you receive donation requests
  - Temporary unavailability mode (travel, illness)
  - Automatic reactivation scheduling

### 🏥 For Healthcare Organizations

- **🆘 Urgent Request System**
  - Post blood needs with urgency levels:
    - **Critical**: Life-threatening, immediate need (within hours)
    - **Urgent**: Needed within 24 hours
    - **Normal**: Scheduled procedures, within week
  - Specify required units and blood type
  - Optional patient case details
  
- **✓ Organization Verification**
  - Multi-step approval process ensures only legitimate healthcare facilities
  - Document verification:
    - Healthcare facility license
    - Blood bank certification
    - Official authorization letters
  - Admin review and approval workflow
  - Verified badge display
  
- **🎯 Automatic Donor Matching**
  - System automatically filters eligible donors by:
    - Blood type compatibility (exact match + universal donors)
    - Geographic proximity (same city/governorate)
    - Current availability status
    - Last donation date (must meet 56-day interval)
  - Sorted by match quality and distance
  
- **📋 Request Management Dashboard**
  - Track all active and past blood requests
  - View donor responses and confirmations
  - Update request status (open/fulfilled/cancelled)
  - Download response reports
  
- **📄 Document Management**
  - Upload and manage licenses and certificates
  - Renewal reminders for expiring documents
  - Secure document storage
  
- **📈 Analytics & Insights**
  - Request fulfillment rates
  - Average response times
  - Donor engagement metrics
  - Geographic coverage analysis

### 🛡️ For System Administrators

- **👥 User Management**
  - Monitor donor and organization accounts
  - View user activity and engagement
  - Handle reported issues and disputes
  - Ban/suspend problematic accounts
  
- **✅ Organization Approval Workflow**
  - Review verification documents submitted by organizations
  - Approve/reject applications with feedback
  - Request additional documentation
  - Track verification history
  
- **📊 Comprehensive Audit Logs**
  - Track all critical platform actions:
    - Donation records (who, when, where, what type)
    - Blood request creations and modifications
    - Organization approvals/rejections
    - Donor profile changes
    - Admin actions
  - Exportable audit trails for compliance
  - Searchable and filterable logs
  
- **📈 Platform Analytics Dashboard**
  - **User Metrics:**
    - Total active donors by blood type
    - New registrations (daily/weekly/monthly)
    - Donor retention rates
    - Average donations per donor
  - **Request Metrics:**
    - Successful donations completed
    - Average fulfillment time
    - Request-to-donation conversion rate
    - Urgent vs normal request ratio
  - **Geographic Insights:**
    - Donor distribution by governorate
    - Coverage gaps identification
    - High-demand areas
  - **System Health:**
    - Platform uptime
    - Error rates
    - Performance metrics

---

## 🏗️ Technical Architecture

### Tech Stack

```
Backend Framework:
├── Laravel 12 (PHP 8.2+)
├── Filament v4 (Admin Panel Framework)
├── Livewire 3 (Reactive Components)
└── MySQL 8.0 (Relational Database)

Frontend Stack:
├── Blade Templating Engine
├── Tailwind CSS 3
├── Alpine.js (for interactivity)
└── Vite (Asset Bundling & HMR)

Authentication & Authorization:
├── Laravel Sanctum (API tokens)
├── Multi-Guard Authentication (Admin/Donor/Organization)
├── Spatie Permission (Role-Based Access Control)
└── Two-Factor Authentication (planned)

Testing & Quality:
├── PHPUnit (Unit & Feature Tests)
├── Laravel Dusk (Browser Tests - planned)
├── PHP CodeSniffer (Code Standards)
└── Larastan (Static Analysis)
```

### Design Patterns & Architecture

- **Repository Pattern**: Abstracted data access layer for testability
- **Service Layer**: Business logic separation from controllers
- **Observer Pattern**: Event-driven notifications and audit logging
- **Policy-Based Authorization**: Granular permission control at model level
- **Form Request Validation**: Centralized input validation and sanitization
- **Resource Controllers**: RESTful API endpoints
- **Job Queues**: Asynchronous notification processing
- **Event Broadcasting**: Real-time updates (planned)

### Security Features

- **SQL Injection Prevention**: PDO prepared statements and Eloquent ORM
- **CSRF Protection**: Laravel's built-in token system for all forms
- **XSS Prevention**: Blade template automatic escaping
- **Password Security**: Bcrypt hashing with cost factor 12
- **Rate Limiting**: API throttling and login attempt limits
- **Audit Logging**: Complete action traceability for compliance
- **Data Encryption**: Sensitive medical data encrypted at rest
- **Session Security**: Secure, HTTP-only cookies
- **Input Sanitization**: HTML Purifier for user-generated content
- **File Upload Validation**: Strict MIME type checking for documents

---

## 📊 Database Schema

### Core Entities

#### **Users (Donors)**
```
- id, name, email, password (hashed)
- phone, national_id (encrypted)
- blood_type, rh_factor
- date_of_birth, gender
- governorate, city, address
- weight, height
- last_donation_date
- is_available (boolean)
- medical_history (JSON)
- created_at, updated_at
```

#### **Organizations (Healthcare Facilities)**
```
- id, name, type (hospital, clinic, blood_bank)
- license_number, registration_number
- address, governorate, city
- phone, email
- verification_status (pending, approved, rejected)
- verification_documents (JSON)
- verified_at, verified_by_admin_id
- created_at, updated_at
```

#### **Blood Requests**
```
- id, organization_id
- blood_type, rh_factor
- units_needed
- urgency_level (critical, urgent, normal)
- patient_case_description (optional)
- location (governorate, city, hospital)
- status (open, fulfilled, cancelled)
- expires_at
- created_at, updated_at, fulfilled_at
```

#### **Donation Records**
```
- id, donor_id, organization_id
- blood_request_id (nullable)
- donation_date
- blood_type, units_donated
- donation_location
- notes
- created_at, updated_at
```

#### **Donor Responses** (to Blood Requests)
```
- id, blood_request_id, donor_id
- response_type (accepted, declined, maybe)
- response_message (optional)
- confirmed_donation (boolean)
- responded_at
- created_at, updated_at
```

#### **Audit Logs**
```
- id, user_id, admin_id (nullable)
- action_type (enum)
- entity_type, entity_id
- old_values (JSON), new_values (JSON)
- ip_address, user_agent
- created_at
```

### Relationships

- User (Donor) **has many** Donation Records
- User (Donor) **has many** Donor Responses
- Organization **has many** Blood Requests
- Blood Request **has many** Donor Responses
- Blood Request **has one** Donation Record (when fulfilled)

---

## 🚀 Installation & Setup

### Prerequisites

- **PHP** 8.2 or higher
- **Composer** 2.x
- **MySQL** 8.0 or **MariaDB** 10.3+
- **Node.js** 18+ & **NPM** 9+
- **Web Server**: Apache/Nginx
- **Optional**: Redis (for caching & queues)

### Step-by-Step Installation

#### 1. **Clone the Repository**
```bash
git clone https://github.com/Adel-Shurrab/bloodbridge.git
cd bloodbridge
```

#### 2. **Install PHP Dependencies**
```bash
composer install
```

#### 3. **Install NPM Dependencies**
```bash
npm install
```

#### 4. **Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

#### 5. **Configure Database**

Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bloodbridge
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 6. **Run Migrations & Seeders**
```bash
# Create database tables
php artisan migrate

# Seed with sample data (optional, for development)
php artisan db:seed
```

#### 7. **Build Frontend Assets**
```bash
# For development (with hot reload)
npm run dev

# For production (minified)
npm run build
```

#### 8. **Create Storage Symlink**
```bash
php artisan storage:link
```

#### 9. **Start Development Server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 🔧 Configuration

### Admin Panel Access

After installation, create an admin account:

```bash
php artisan make:filament-user
```

Access admin panel at: `http://localhost:8000/admin`

### Email Configuration

For email notifications, configure mail settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bloodbridge.ps
MAIL_FROM_NAME="BloodBridge"
```

### Queue Configuration (for Notifications)

For production, use Redis or database queues:

```env
QUEUE_CONNECTION=redis

# Or use database
QUEUE_CONNECTION=database
php artisan queue:table
php artisan migrate
```

Start queue worker:
```bash
php artisan queue:work
```

### File Storage

Configure file storage for documents in `config/filesystems.php`:
```php
'disks' => [
    'documents' => [
        'driver' => 'local',
        'root' => storage_path('app/documents'),
        'visibility' => 'private',
    ],
],
```

---

## 📈 System Workflow

### 1. Blood Request Flow

```
Organization creates urgent request
    ↓
System validates organization status (must be verified)
    ↓
System identifies eligible donors:
    - Matching blood type (or universal donors)
    - In same governorate/city
    - Available (is_available = true)
    - Eligible by donation date (≥ 56 days since last donation)
    ↓
Notifications sent to matched donors:
    - Email with request details
    - In-app notification
    - SMS for critical requests (planned)
    ↓
Donors respond (accept/decline)
    ↓
Organization confirms donation with specific donor
    ↓
System logs donation record
    ↓
Donor eligibility updated (next eligible date = today + 56 days)
```

### 2. Organization Verification Flow

```
Organization registers
    ↓
Status: Pending
    ↓
Organization uploads verification documents:
    - Healthcare facility license
    - Blood bank certification
    - Authorization letters
    ↓
Admin receives notification
    ↓
Admin reviews documents in admin panel
    ↓
Admin decision:
    ├─ Approve → Status: Approved
    │              Automated email notification
    │              Organization can create requests
    │
    └─ Reject  → Status: Rejected
                   Email with rejection reason
                   Organization can resubmit
```

### 3. Donor Eligibility Check

```
New donor registers
    ↓
System evaluates eligibility:
    ├─ Age check (18-65 years)
    ├─ Weight check (≥ 50kg)
    ├─ Medical history review:
    │   - Chronic diseases
    │   - Recent surgeries
    │   - Current medications
    │   - Infectious diseases
    └─ Last donation date (if any)
    ↓
Eligibility determined:
    ├─ Eligible → Can accept donation requests
    └─ Ineligible → Reason displayed, retry date shown
```

---

## 🌍 Impact & Scope

### Target Audience

**Designed to serve:**
- **500+ registered blood donors** across Palestine
- **20+ healthcare organizations** (hospitals, clinics, blood banks)
- **All Palestinian governorates** (Gaza Strip, West Bank)

### Key Performance Indicators

**Time Efficiency:**
- Reduce donor search time: **hours → minutes**
- Automate **70%** of manual eligibility screening
- Average response time: **< 30 minutes** for urgent requests

**System Efficiency:**
- Centralize previously **fragmented donor databases**
- **100% audit trail** for regulatory compliance
- **Real-time** donor-request matching

**Healthcare Impact:**
- Enable **faster emergency responses**
- Reduce **blood shortage incidents**
- Improve **donor retention** through engagement

---

## 🛣️ Roadmap

### ✅ Completed Features

- [x] Basic Laravel + Filament setup
- [x] Multi-guard authentication (Admin/Donor/Organization)
- [x] Donor registration with medical history
- [x] Organization verification workflow
- [x] Blood request creation and management
- [x] Basic donor-request matching algorithm
- [x] Audit logging system
- [x] Admin dashboard with analytics

### 🚧 In Progress

- [ ] Email notification system
- [ ] Donor response workflow
- [ ] Enhanced matching algorithm (distance-based)
- [ ] Document upload and verification
- [ ] Comprehensive test coverage

### 📋 Planned Features

#### **Phase 1: Core Enhancements** (Next 2-4 weeks)
- [ ] SMS notifications via Twilio integration
- [ ] Advanced analytics dashboard
- [ ] Donation certificate generation (PDF)
- [ ] Multi-language support (Arabic/English)
- [ ] Enhanced search and filtering

#### **Phase 2: User Experience** (1-2 months)
- [ ] Mobile-responsive design improvements
- [ ] Progressive Web App (PWA) support
- [ ] Push notifications
- [ ] Donor rewards/gamification system
- [ ] Social sharing features

#### **Phase 3: Advanced Features** (2-3 months)
- [ ] Mobile application (React Native)
- [ ] Blood bank inventory tracking
- [ ] Appointment scheduling system
- [ ] Integration with Ministry of Health systems
- [ ] Real-time chat between donors and organizations

#### **Phase 4: Scale & Optimization** (3-6 months)
- [ ] API for third-party integrations
- [ ] Advanced reporting and BI dashboards
- [ ] Machine learning for donor prediction
- [ ] Multi-region deployment
- [ ] Load balancing and caching optimization

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=DonorEligibilityTest

# Run with coverage
php artisan test --coverage

# Run feature tests only
php artisan test --testsuite=Feature
```

### Test Coverage Goals

- **Unit Tests**: Core business logic (eligibility, matching)
- **Feature Tests**: API endpoints, workflows
- **Browser Tests**: Critical user journeys (planned)

---

## 🤝 Contributing

Contributions to improve BloodBridge are welcome! This project is under active development.

### Development Guidelines

1. **Fork the repository**
2. **Create feature branch**: `git checkout -b feature/amazing-feature`
3. **Follow code standards**: PSR-12, Laravel best practices
4. **Write tests** for new features
5. **Commit changes**: `git commit -m 'feat: add amazing feature'`
6. **Push to branch**: `git push origin feature/amazing-feature`
7. **Open Pull Request**

### Code Style

- Follow **PSR-12** coding standards
- Use **meaningful variable/function names**
- Add **DocBlocks** for classes and methods
- Write **clear commit messages** (follow Conventional Commits)

### Pull Request Process

1. Update documentation (README, inline comments)
2. Add tests for new features
3. Ensure all tests pass (`php artisan test`)
4. Update `CHANGELOG.md`
5. Request review from maintainers

---

## 📜 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Adel A. A. Shurrab**

- 📧 Email: adelshurrab2003@gmail.com
- 💼 LinkedIn: [linkedin.com/in/adel-shurrab](https://linkedin.com/in/adel-shurrab)
- 💻 GitHub: [@Adel-Shurrab](https://github.com/Adel-Shurrab)
- 📍 Location: Khan Yunis, Palestine

### Contributors

Special thanks to all contributors who have helped shape BloodBridge! (2 contributors)

---

## 🙏 Acknowledgments

- **University College of Ability Development (PRCS)** - For project support and guidance
- **Palestinian Red Crescent Society** - For domain expertise in blood donation processes
- **Laravel Community** - For the excellent framework and ecosystem
- **Filament Team** - For the powerful admin panel framework
- **Open Source Community** - For the tools and libraries that make this possible

---

## 📞 Support & Contact

### For Healthcare Organizations

Interested in joining BloodBridge as a verified organization?
- Email: organizations@bloodbridge.ps (planned)
- Phone: +972-XXX-XXXX (to be announced)

### For Donors

Need help with your account or have questions?
- Email: support@bloodbridge.ps (planned)
- FAQ: [Documentation](https://github.com/Adel-Shurrab/bloodbridge/wiki) (coming soon)

### For Developers

Want to contribute or report issues?
- GitHub Issues: [Report a bug](https://github.com/Adel-Shurrab/bloodbridge/issues)
- Discussions: [Join the conversation](https://github.com/Adel-Shurrab/bloodbridge/discussions)

---

## 📈 Project Statistics

![GitHub Stars](https://img.shields.io/github/stars/Adel-Shurrab/bloodbridge?style=social)
![GitHub Forks](https://img.shields.io/github/forks/Adel-Shurrab/bloodbridge?style=social)
![GitHub Issues](https://img.shields.io/github/issues/Adel-Shurrab/bloodbridge)
![GitHub Pull Requests](https://img.shields.io/github/issues-pr/Adel-Shurrab/bloodbridge)
![Last Commit](https://img.shields.io/github/last-commit/Adel-Shurrab/bloodbridge)

---

<div align="center">

**⭐ If you find this project helpful or interesting, please consider giving it a star!**

*Built with ❤️ in Palestine to save lives through technology*

**🩸 Every donation counts. Every minute matters. 🩸**

[⬆ Back to Top](#-bloodbridge---centralized-blood-donation-platform)

</div>
