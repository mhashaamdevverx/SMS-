# Excellence School System

A full-stack **School Management System** built with **Laravel 13 (`v13.25.0`)** and **PHP 8.3+**, providing an integrated public institutional website alongside dedicated, role-secured **Admin**, **Teacher / Class Incharge**, and **Student** portals.

---

## Tech Stack & Versions

### Backend
* **PHP:** `^8.3` required (verified on **PHP 8.5.7 NTS x64**)
* **Framework:** Laravel Framework **`v13.25.0`** (`^13.17`)
* **PDF Generation:** `barryvdh/laravel-dompdf` **`v3.1.2`** (`dompdf/dompdf` **`v3.1.6`**)
* **CLI / REPL:** `laravel/tinker` **`v3.0.2`**
* **Testing & Dev Tools:** `phpunit/phpunit` `12.5.33`, `mockery/mockery` `1.6.13`, `fakerphp/faker` `v1.24.1`, `laravel/pint` `v1.30.5`, `laravel/pail` `v1.2.7`, `nunomaduro/collision` `v8.9.5`

### Frontend
* **Templating:** Laravel Blade (Server-Side Rendered)
* **CSS Framework:** Bootstrap **`5.3.3`** (CDN) + modular custom CSS in `public/css/`
* **Icons & Typography:** Bootstrap Icons **`1.11.3`** (CDN), Font Awesome **`6.4.0`** (CDN), Google Fonts (`Inter`, `Plus Jakarta Sans`, `Outfit`, `DM Sans`, `Space Grotesk`)
* **Charts & Visualizations:** Chart.js **`4.4.9`** (CDN) + custom inline SVG spline charts
* **JavaScript:** Pure Vanilla ES6+ (`public/js/`) using native `fetch()` API and `window.print()` / `@media print` layouts
* **Asset Tooling Note:** `package.json` / `vite.config.js` declare Vite (`^8.0.0`) and Tailwind CSS (`^4.0.0`) scaffolding, but Blade views currently load static `public/css/`, `public/js/`, and CDN assets directly (no Node/Vite build step is required to run the app).

### Database & Infrastructure
* **Database:** MySQL (port `3306`, `utf8mb4` / `utf8mb4_unicode_ci`; **MySQL 8.0+** or **5.7.8+** recommended for `JSON` and `ENUM` columns; SQLite fallback supported in `config/database.php`)
* **Schema Scale:** 59 migrations, 29 Eloquent models
* **Sessions / Cache / Queue:** `file` or `database` sessions, `database` cache store (`cache` & `cache_locks`), `database` queue connection, `local` / `public` filesystem disk (`storage/app/public`), `log` / SMTP mailer

---

## Core Modules & Implemented Features

### 1. Public Institutional Website
* **Informational Pages:** Landing (`/`), About (`/about`), Admissions (`/admissions`), Academics (`/academics`), Careers (`/careers`), Contact (`/contact`), Why Choose Us (`/why-choose-us`), and Support (`/support`).
* **Admissions Inquiry Submission (`POST /inquiry/submit`):** Validates guardian details, 13-digit Student B-Form and Guardian CNIC (auto-formatted as `XXXXX-XXXXXXX-X`), generates a tracking application number (`AdmissionApplication`), and notifies Admins.
* **Careers 4-Step Application Wizard (`POST /career/submit`):** Collects personal, education, experience, and reference details (`CareerApplication`), generates tracking numbers, and notifies Admins.
* **Campus Tour Booking (`POST /tour/schedule`):** Schedules parent campus visit requests.

### 2. Authentication & Role-Based Access Control (RBAC)
* **Role-Verified Authentication:** Login validates email (`@gmail.com` format), password (Bcrypt, 12 rounds), and selected account type (`student`, `teacher`, `admin`).
* **Teacher Signup Request Flow (`/signup`):** Verified teachers submit account access requests (`TeacherAccessRequest`) for Admin approval before activation.
* **Password Management:** First-login mandatory/dismissible password update prompt (`/account/password`), admin-assisted password reset requests (`/forgot-password`), and token-based reset (`/reset-password/{token}`).
* **Middleware Security:** `RequireAuthentication` and `RequireRolePermission` enforce per-module permissions (`*.view`, `*.create`, `*.update`, `*.delete`, `results.download`) across 4 primary roles (`admin`, `class-incharge`, `teacher`, `student`) plus scoped class access via `App\Support\InchargeAccess`.

### 3. Admin & Management Portal (`/admin/*` & Management Routes)
* **Analytics Dashboard (`/dashboard`, `/admin/dashboard`):** Live summary metrics and Chart.js visualizations (weekly attendance trends, fee collection status doughnut chart, and class enrollment bar chart).
* **Academic & User Records:** Full CRUD with server-side pagination (10 records/page) for Students (`/list`, `/add-student`, `/students/{student}`), Teachers (`/teachers`), Courses (`/courses`), Subjects (`/subjects`), Classes (`/classes`), Sections (`/sections`), and Users (`/users`), plus auto roll-number info API (`/api/student-roll-info`).
* **Roles, Permissions & Incharge Management (`/roles`):** Create/edit custom roles, toggle granular permissions, assign Class Incharges (`/roles/assign-incharge`), and approve/reject teacher signup requests.
* **Admissions & Careers Review (`/admin/admissions`, `/admin/careers`):** Review submitted applications, update statuses, or remove entries.
* **Attendance Tracking (`/attendance`):** Daily class/section attendance recording and date/record deletion.
* **Examinations, Results & Roll Slips:**
  * Result entry by individual student or bulk class (`/results`, `/add-result`, `/add-result/class`, `/add-result/student-result`) and A4 PDF result card download (`/students/{student}/results/download`).
  * Exam Hall Seating Arrangements (`/arrangements`, `/add-arrangement`).
  * Exam Roll Slips (`/roll-slips`, `/issue-roll-slips`) with JSON datesheets and single/class-wide PDF downloads (`/roll-slips/{rollSlip}/download`, `/roll-slips/class-download`).
* **Fee Management & Vouchers (`/fee-structure`, `/admin/fees`):** Class fee structures, student fee collection status updates (`Paid`/`Pending`), and printable fee vouchers (`/fee-structure/voucher/{feeCollection}`).
* **Teacher Payroll & Leave Approvals (`/admin/salaries`, `/admin/leaves`):** Manage monthly teacher salaries, generate downloadable payslips (`/admin/salaries/{id}/payslip`), and approve/reject student & teacher leave requests.

### 4. Teacher & Class Incharge Portal (`/teacher/*`)
* **Dedicated Pages:** `dashboard`, `profile`, `classes`, `incharge`, `coteachers`, `students`, `results`, `outlines`, `salary`, `attendance`, `tests`, `slips`, `stdleaves`, `leaves`.
* **Daily Punch-In / Punch-Out Attendance (`/teacher/punch-in`, `/teacher/punch-out`):** Timestamped attendance logs with a 12-month yearly Excel-style matrix view.
* **Academic & Administrative Workflows:** Upload downloadable syllabus/course outline files (`/teacher/upload-outline`), enter/update monthly test session marks (`/teacher/test-marks/update`), apply for teacher leave (`/teacher/apply-leave`), review assigned class student leaves (for Class Incharges), and download salary payslips (`/teacher/salary-slip/{id}`).

### 5. Student Portal (`/student/*`)
* **Dedicated Pages:** `dashboard`, `profile`, `fees`, `academics`, `attendance`, `results`, `slips`, `leaves`, `tests`, `outlines`, `teachers`.
* **Self-Service Features:** View attendance & term results, view/download Monthly Test Session reports (`/student/test-sessions`, `/student/test-sessions/{month}/download`), download teacher-uploaded course outlines (`/student/outlines/{id}/download`), view exam roll slips, pay fees & download fee vouchers (`/student/pay-fee/{feeCollection}`, `/student/fee-voucher/{feeCollection}/download`), and submit student leave applications (`/student/apply-leave`).

### 6. In-App Notifications & Announcements
* Polling API (`/api/notifications`, `/api/notifications/mark-read`, `/api/notifications/clear`) and Admin broadcast announcements (`/api/announcements/publish`) backed by the `app_notifications` table (`AppNotification`).

---

## Requirements & Deployment Setup

### Server & PHP Requirements
* **PHP:** `>= 8.3`
* **Required PHP Extensions:** `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `iconv`, `json`, `libxml`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `phar`, `session`, `tokenizer`, `xml`, `xmlwriter`, `zip`, `zlib`, `bcmath`, `opcache`
* **Web Server:** Apache (`mod_rewrite` enabled, `public/.htaccess` included) or Nginx with DocumentRoot pointed to **`public/`**
* **Health Check Endpoint:** `GET /up`
* **Client Network Note:** Frontend layouts load Bootstrap, Icons, Font Awesome, Chart.js, and Google Fonts from public CDNs; end-user browsers require internet connectivity unless those assets are self-hosted.

### Linux MySQL Case-Sensitivity Note
* `2026_08_21_171013_create_students_table.php` creates the table as `students` (lowercase), while `App\Models\Student` and several migrations/commands query `Students` (capital `S`).
* On **Linux MySQL/MariaDB**, configure `lower_case_table_names=1` in MySQL configuration prior to database initialization (or normalize table references) to avoid `Table 'Students' doesn't exist` errors.

### Installation & Deployment Steps

1. **Install PHP Dependencies**
   ```bash
   # Development
   composer install

   # Production
   composer install --no-dev --optimize-autoloader
   ```

2. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update `.env` for your target environment:
   * `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain.com`
   * `DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT=3306`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   * `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`, and `MAIL_*` SMTP credentials if email delivery is enabled

3. **Run Database Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Link Public Storage & Set Permissions**
   ```bash
   php artisan storage:link
   ```
   Ensure `storage/` and `bootstrap/cache/` are writable by the web server user so uploaded profile images, course outlines, logs, and compiled views work properly.

5. **Optimize Caches (Production)**
   ```bash
   php artisan optimize
   ```

---

## Maintenance Commands & Tests

### Custom Artisan Commands (`routes/console.php`)
* `php artisan school:inspect-users` — List all users and assigned role slugs
* `php artisan school:verify-sync` — Verify `@gmail.com` formatting, user/teacher/student synchronization, and data integrity
* `php artisan school:test-pagination` — Validate 10-records-per-page pagination boundary cases

### Automated Test Suite
```bash
php artisan test
```
Includes feature tests in `tests/Feature/PortalRoleAccessTest.php` and `tests/Feature/EmailMigrationAndValidationTest.php`.

---

## License

This project uses the [Laravel framework](https://laravel.com), open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
