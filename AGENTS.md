# AGENTS.md — FlowDesk Organizational Management System

## 1. PROJECT OVERVIEW

- **System name:** FlowDesk — Organizational Management System
- **Purpose:** Modular, single-org deployment management system for small businesses (garages, clinics, shops)
- **Stack:** CodeIgniter 4 (v4.x, PHP 8.2+), MySQL 5.7+ / MariaDB (`flowdesk`), Bootstrap 5.3, jQuery 3.6, DataTables 1.13, FullCalendar 6.1, Chart.js, SweetAlert2, Select2, Font Awesome / Bootstrap Icons
- **Web server:** Apache with mod_rewrite (XAMPP)
- **Local URL:** `http://localhost/FlowDesk/`
- **Local path:** `C:\xampp\htdocs\FlowDesk`
- **GitHub:** `https://github.com/Austine904/FlowDesk`

## 2. ARCHITECTURE

```
C:\xampp\htdocs\FlowDesk\
├── app/
│   ├── Config/           # App, Database, Routes, Filters, JobStatus, etc.
│   ├── Controllers/      # 15 controllers in root namespace (no Admin/ subdirectory)
│   ├── Database/
│   │   ├── Migrations/   # Empty
│   │   └── Seeds/        # Empty
│   ├── Filters/
│   │   └── AuthFilter.php
│   ├── Helpers/
│   │   ├── settings_helper.php   # org_setting() — globally available
│   │   └── activity_helper.php   # timeAgo() — used by dashboard
│   ├── Models/            # 16 models
│   └── Views/
│       ├── admin/         # dashboard, users, invoices, settings, inventory, suppliers, forms
│       ├── calendar/      # calendar, modals
│       ├── customers/     # customers, modals
│       ├── errors/        # unauthorized, html/, cli/
│       ├── job/           # index, modals, mechanic_diagnosis_form
│       ├── jobs/          # add, edit
│       ├── layouts/       # main
│       ├── mechanic/      # jobs
│       ├── partials/      # sidebar
│       ├── sublets/       # index, form, _details, modals
│       ├── user/          # add_step1/2/3, preview, success, getLastId, failure
│       ├── vehicles/      # index, add, edit, modals
│       └── *.php          # login, dashboard variants, welcome_message
├── public/
│   ├── assets/js/         # vehicles.js, job_intake.js, customers.js, calendar.js, sublets.js
│   └── css/               # Per-module CSS files
├── uploads/               # users/, job_card_photos/, org/ — gitignored
├── vendor/                # Composer dependencies
├── writable/              # CI4 cache, logs, sessions
├── .htaccess              # RewriteBase /FlowDesk/
└── index.php              # Front controller
```

**Routing:** Defined in `app/Config/Routes.php`. Explicit routes only (no auto-routing). Route groups use the `filter` option for auth. Public routes are top-level. Protected routes are grouped by prefix (`admin`, `job_intake`, `mechanic`, `receptionist`, `customer`).

**Auth:** `AuthFilter` (`app/Filters/AuthFilter.php`) checks `session()->get('isLoggedIn')`. If role arguments are provided, validates `session()->get('role')` is in the allowed list. Session keys: `user_id`, `user_name`, `role`, `company_id`, `profile_picture`, `isLoggedIn`.

**Views:** Most views extend `layouts/main` via `$this->extend('layouts/main')` with `$this->section('content')`. Standalone views (user registration wizard, admin/add_user) do not extend the layout.

**Models:** 16 models in `app/Models/`. Some controllers use `$db->table()` query builder directly for complex joins and aggregations (DashboardController, InvoicesController, JobIntake).

**CSRF:** Globally enabled via `app/Config/Filters.php` (`$globals['before']` includes `'csrf'`). Every POST form must include `<?= csrf_field() ?>`. CSRF token injected as meta tags in `layouts/main.php` — JS reads them via `getCsrfMeta()` for all AJAX POSTs. AJAX setup in main layout auto-appends CSRF token and refreshes on response.

**File uploads:** `uploads/users/` (profile pictures), `uploads/job_card_photos/` (job card photos), `uploads/org/` (org logo). Directory is gitignored (contains `.gitkeep`).

**Helper:** `app/Helpers/settings_helper.php` provides `org_setting(string $key, $default = null)` — available globally in all controllers and views. Caches results statically (one DB query per request).

**Helper:** `app/Helpers/activity_helper.php` provides `timeAgo($datetime)` — returns human-readable relative time.

## 3. DATABASE

**Database name:** `flowdesk` (lowercase). Schema created manually (no migrations). All FK constraints defined at database level.

### Tables

#### migrations
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| version | bigint(20) | NOT NULL |
| class | varchar(255) | NOT NULL |
| group | varchar(255) | NOT NULL |
| namespace | varchar(255) | NOT NULL |
| time | int(11) | NOT NULL |
| batch | int(10) unsigned | NOT NULL |

#### users
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| company_id | varchar(20) | NOT NULL, UNIQUE |
| first_name | varchar(50) | NOT NULL |
| last_name | varchar(50) | NOT NULL |
| name | varchar(101) | NULL, STORED GENERATED (first_name + last_name) |
| email | varchar(255) | NULL |
| phone_number | varchar(20) | NULL |
| phone | varchar(20) | NULL |
| password | varchar(255) | NOT NULL |
| role | enum('admin','mechanic','receptionist','customer') | NOT NULL, default 'mechanic' |
| profile_picture | varchar(255) | NULL |
| date_of_employment | date | NULL |
| dob | date | NULL |
| national_id | varchar(50) | NULL |
| gender | enum('Male','Female','Other') | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |
| deleted_at | datetime | NULL (soft delete) |

#### next_of_kin
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| user_id | int(10) unsigned | FK → users.id |
| kin_first_name | varchar(50) | NOT NULL |
| kin_last_name | varchar(50) | NOT NULL |
| relationship | varchar(50) | NOT NULL |
| kin_phone_number | varchar(20) | NOT NULL |

#### customers
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(100) | NOT NULL |
| phone | varchar(20) | NOT NULL, UNIQUE |
| email | varchar(255) | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

**Note:** `customers` has no `deleted_at` column — no soft-delete.

#### vehicles
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| owner_id | int(10) unsigned | FK → customers.id |
| registration_number | varchar(20) | NOT NULL, UNIQUE |
| vin | varchar(17) | NULL, UNIQUE |
| make | varchar(50) | NOT NULL |
| model | varchar(50) | NOT NULL |
| year_of_manufacture | year(4) | NULL |
| engine_number | varchar(50) | NULL, UNIQUE |
| chassis_number | varchar(50) | NULL, UNIQUE |
| fuel_type | enum('Petrol','Diesel','Electric','Hybrid') | NULL |
| transmission | enum('Manual','Automatic','CVT','Semi-Automatic') | NULL |
| color | varchar(30) | NULL |
| mileage | int(11) | NULL |
| reported_problem | text | NULL |
| status | varchar(50) | NOT NULL, default 'On Job' |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### job_cards (primary transactional table)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_no | varchar(30) | NOT NULL, UNIQUE |
| customer_id | int(10) unsigned | FK → customers.id |
| vehicle_id | int(10) unsigned | FK → vehicles.id |
| date_in | date | NOT NULL |
| time_in | time | NULL |
| start_date | date | NULL |
| end_date | date | NULL |
| diagnosis | text | NULL |
| initial_damage_notes | text | NULL |
| job_status | varchar(30) | NOT NULL, default 'Awaiting Assignment' |
| mileage_in | int(11) | NULL |
| fuel_level | enum('Empty','1/4','1/2','3/4','Full') | NULL |
| estimated_labor_hours | decimal(8,2) | NULL |
| assigned_service_advisor_id | int(10) unsigned | FK → users.id |
| assigned_mechanic_id | int(10) unsigned | NULL, FK → users.id |
| job_summary | text | NULL |
| quote_amount | decimal(12,2) | NULL |
| quote_status | varchar(30) | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |

#### job_card_photos
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| file_path | varchar(255) | NOT NULL |
| file_name | varchar(255) | NULL |

#### job_card_parts_required
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| inventory_id | int(10) unsigned | FK → inventory.id |
| quantity_required | int(11) | NOT NULL, default 1 |
| unit_price_at_estimate | decimal(12,2) | NOT NULL, default 0.00 |

#### job_card_labor_tasks
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| task_name | varchar(255) | NOT NULL |
| estimated_hours | decimal(8,2) | NOT NULL, default 0.00 |
| rate_per_hour | decimal(10,2) | NOT NULL, default 0.00 |
| labor_cost | decimal(12,2) | GENERATED ALWAYS AS (estimated_hours * rate_per_hour) STORED |
| notes | text | NULL |

#### inventory
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(255) | NOT NULL |
| part_number | varchar(100) | NULL |
| unit_price | decimal(12,2) | NOT NULL, default 0.00 |

#### suppliers
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(255) | NOT NULL |

#### sublets
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| sublet_provider_id | int(10) unsigned | FK → suppliers.id |
| description | text | NOT NULL |
| cost | decimal(12,2) | NOT NULL, default 0.00 |
| status | enum('Pending','In Progress','Completed','Invoiced','Paid','Cancelled') | NOT NULL, default 'Pending' |
| date_sent | date | NOT NULL |
| date_returned | date | NULL |
| notes | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |

#### calendar_events
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| title | varchar(255) | NOT NULL |
| description | text | NULL |
| start_time | datetime | NOT NULL |
| end_time | datetime | NULL |
| all_day | tinyint(1) | NOT NULL, default 0 |
| event_type | varchar(50) | NOT NULL, default 'general' |
| color | varchar(7) | NULL, default '#007bff' |
| related_table | varchar(50) | NULL |
| related_id | int(10) unsigned | NULL |
| priority | enum('low','medium','high') | NOT NULL, default 'medium' |
| created_by_user_id | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### job_status_history
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| from_status | varchar(50) | NOT NULL |
| to_status | varchar(50) | NOT NULL |
| changed_by | int(10) unsigned | FK → users.id |
| notes | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### org_settings (single-row org-wide config, id always 1)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment, always 1 |
| org_name | varchar(255) | NOT NULL |
| org_logo | varchar(255) | NULL |
| org_address | text | NULL |
| org_phone | varchar(20) | NULL |
| org_email | varchar(255) | NULL |
| org_website | varchar(255) | NULL |
| currency | varchar(10) | NOT NULL, default 'KES' |
| currency_symbol | varchar(5) | NOT NULL, default 'KSh' |
| vat_rate | decimal(5,2) | NOT NULL, default 16.00 |
| default_labor_rate | decimal(10,2) | NOT NULL, default 1500.00 |
| invoice_prefix | varchar(10) | NOT NULL, default 'INV-' |
| invoice_due_days | int(11) | NOT NULL, default 14 |
| fin_year_start_month | tinyint(4) | NOT NULL, default 1 |
| created_at | datetime | NOT NULL |
| updated_at | datetime | NULL, on update |

#### invoices
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| invoice_no | varchar(30) | NOT NULL, UNIQUE |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| customer_id | int(10) unsigned | FK → customers.id |
| invoice_date | date | NOT NULL |
| due_date | date | NOT NULL |
| parts_total | decimal(12,2) | NOT NULL, default 0.00 |
| labor_total | decimal(12,2) | NOT NULL, default 0.00 |
| sublet_total | decimal(12,2) | NOT NULL, default 0.00 |
| subtotal | decimal(12,2) | NOT NULL, default 0.00 |
| vat_rate | decimal(5,2) | NOT NULL, default 16.00 |
| vat_amount | decimal(12,2) | NOT NULL, default 0.00 |
| discount | decimal(12,2) | NOT NULL, default 0.00 |
| grand_total | decimal(12,2) | NOT NULL, default 0.00 |
| amount_paid | decimal(12,2) | NOT NULL, default 0.00 |
| balance_due | decimal(12,2) | GENERATED ALWAYS AS (grand_total - amount_paid) STORED |
| status | enum('Draft','Sent','Partially Paid','Paid','Overdue','Cancelled') | NOT NULL, default 'Draft' |
| notes | text | NULL |
| created_by | int(10) unsigned | FK → users.id |

#### payments
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| invoice_id | int(10) unsigned | FK → invoices.id, ON DELETE CASCADE |
| amount | decimal(12,2) | NOT NULL |
| payment_method | enum('Cash','M-Pesa','Bank Transfer','Insurance','Credit') | NOT NULL |
| reference_no | varchar(100) | NULL |
| payment_date | date | NOT NULL |
| received_by | int(10) unsigned | FK → users.id |
| notes | text | NULL |

#### jobs (legacy/unused table)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_name | varchar(255) | NOT NULL |
| description | text | NULL |
| status | enum('pending','completed','cancelled') | NOT NULL |
| assigned_to | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL |
| updated_at | datetime | NULL |
| deleted_at | datetime | NULL |

**Note:** The `jobs` table is legacy/unused. All current operations use `job_cards`. Do not write to `jobs`.

### FK Relationships

| Source | Target | Constraint |
|--------|--------|------------|
| next_of_kin.user_id | users.id | fk_nok_user |
| vehicles.owner_id | customers.id | fk_vehicles_owner |
| job_cards.customer_id | customers.id | fk_jc_customer |
| job_cards.vehicle_id | vehicles.id | fk_jc_vehicle |
| job_cards.assigned_service_advisor_id | users.id | fk_jc_advisor |
| job_cards.assigned_mechanic_id | users.id | fk_jc_mechanic |
| job_status_history.job_card_id | job_cards.id | fk_jsh_job_card |
| job_status_history.changed_by | users.id | fk_jsh_changed_by |
| job_card_photos.job_card_id | job_cards.id | fk_jcp_job_card |
| job_card_parts_required.job_card_id | job_cards.id | fk_jcpr_job_card |
| job_card_parts_required.inventory_id | inventory.id | fk_jcpr_inventory |
| job_card_labor_tasks.job_card_id | job_cards.id | fk_jclt_job_card |
| sublets.job_card_id | job_cards.id | fk_sublets_job_card |
| sublets.sublet_provider_id | suppliers.id | fk_sublets_provider |
| calendar_events.created_by_user_id | users.id | fk_ce_created_by |
| invoices.job_card_id | job_cards.id | fk_inv_job_card |
| invoices.customer_id | customers.id | fk_inv_customer |
| invoices.created_by | users.id | fk_inv_created_by |
| payments.invoice_id | invoices.id | fk_pay_invoice (ON DELETE CASCADE) |
| payments.received_by | users.id | fk_pay_received_by |
| jobs.assigned_to | users.id | fk_jobs_assigned (legacy) |

### Generated Columns (never include in $allowedFields)

- `users.name` — `GENERATED ALWAYS AS (first_name + last_name) STORED`
- `job_card_labor_tasks.labor_cost` — `GENERATED ALWAYS AS (estimated_hours * rate_per_hour) STORED`
- `invoices.balance_due` — `GENERATED ALWAYS AS (grand_total - amount_paid) STORED`

## 4. MODELS

All models are in `app/Models/`. All extend `CodeIgniter\Model`.

| Model | File | Table | Key Custom Methods |
|-------|------|-------|--------------------|
| UserModel | `app/Models/UserModel.php` | `users` | `getByCompanyId(string): ?array` — find user by company_id; `getByRole(string): array` — find all users by role; `getLastCompanyIdNumber(string): int` — get highest numeric suffix for a company_id prefix |
| NextOfKinModel | `app/Models/NextOfKinModel.php` | `next_of_kin` | `getByUserId(int): ?array` — get next-of-kin for a user |
| CustomerModel | `app/Models/CustomerModel.php` | `customers` | `searchByPhoneOrName(string): array` — search customers by phone or name; `getWithVehicleCount(): array` — list all customers with vehicle count |
| VehicleModel | `app/Models/VehicleModel.php` | `vehicles` | `getByOwner(int): array` — get vehicles by owner_id; `getByRegistration(string): ?array` — find vehicle by registration_number; `searchByTerm(string): array` — search by reg, VIN, or chassis number |
| JobCardModel | `app/Models/JobCardModel.php` | `job_cards` | `getWithDetails(int): ?array` — single job card with joins; `getByStatus(string): array` — filter by job_status; `getAssignedToMechanic(int): array` — jobs assigned to a mechanic; `getRecentJobs(int): array` — most recent jobs; `generateJobNo(): string` — generate next JOB-YYYYMMDD-NNN; `getStatusHistory(int): array` — delegate to JobStatusHistoryModel |
| JobCardPhotoModel | `app/Models/JobCardPhotoModel.php` | `job_card_photos` | `getByJobCard(int): array` — get photos for a job card |
| JobCardPartModel | `app/Models/JobCardPartModel.php` | `job_card_parts_required` | `getByJobCard(int): array` — get parts with inventory details; `deleteByJobCard(int): void` — delete all parts for a job card |
| JobCardLaborModel | `app/Models/JobCardLaborModel.php` | `job_card_labor_tasks` | `getByJobCard(int): array` — get tasks with computed labor_cost; `deleteByJobCard(int): void` — delete all tasks for a job card |
| InventoryModel | `app/Models/InventoryModel.php` | `inventory` | `search(string): array` — search by name or part_number |
| SupplierModel | `app/Models/SupplierModel.php` | `suppliers` | `getAll(): array` — all suppliers ordered by name |
| SubletModel | `app/Models/SubletModel.php` | `sublets` | `getWithDetails(int|null): array` — sublet with job_no, reg_number, provider_name joins |
| CalendarEventModel | `app/Models/CalendarEventModel.php` | `calendar_events` | `getUpcoming(int): array` — upcoming events; `getByDateRange(string, string): array` — events within a date range |
| JobStatusHistoryModel | `app/Models/JobStatusHistoryModel.php` | `job_status_history` | `getByJobCard(int): array` — status history for a job card with username |
| OrgSettingsModel | `app/Models/OrgSettingsModel.php` | `org_settings` | `getSettings(): array` — fetch single row (id=1); `updateSettings(array): bool` — update row (id=1) |
| InvoiceModel | `app/Models/InvoiceModel.php` | `invoices` | `generateInvoiceNo(): string` — next INV-YYYYMM-NNN; `generateFromJobCard(int, int): array` — create invoice from job card totals (idempotent); `getWithDetails(int|null): array` — invoice with customer/job/creator joins; `updateAmountPaid(int): void` — recalculate amount_paid and status from payments |
| PaymentModel | `app/Models/PaymentModel.php` | `payments` | `getByInvoice(int): array` — payments for an invoice with received_by name |

## 5. MODULES

### Auth (Login/Logout)
- **Status:** Complete
- **Controller:** `LoginController`
- **Routes:** `GET /login`, `POST /login/auth`, `GET /logout`
- **Views:** `login.php`
- **Key functionality:** Login with `company_id` + `password` (hashed with `password_hash`), session creation, role-based dashboard redirect. Logout destroys session and redirects to `/login`.

### Dashboard
- **Status:** Complete
- **Controller:** `DashboardController`
- **Routes:** `/admin/dashboard`, `/receptionist/`, `/mechanic/`, `/customer/`, `/unauthorized`
- **Views:** `admin/dashboard.php`, `mechanic_dashboard.php`, `receptionist_dashboard.php`, `customer_dashboard.php`
- **Key functionality:** Admin dashboard with summary cards (user count, active vehicles, active jobs, pending LPOs), job status doughnut chart (Chart.js), revenue line chart (last 6 months), recent activity feed, quick action buttons. Mechanic dashboard shows assigned job stats and recent jobs list. Real revenue data from `payments` table. Real outstanding balance from `invoices.balance_due`.

### Users
- **Status:** Complete
- **Controller:** `UsersController`
- **Routes:** `/admin/users/*`, public registration wizard `/user/*`
- **Views:** `admin/users.php`, `admin/add_user.php`, `admin/edit_user.php`, `admin/users/user_list.php`, `user/add_step1.php`, `user/add_step2.php`, `user/add_step3.php`, `user/preview.php`, `user/success.php`, `user/failure.php`, `user/getLastId.php`
- **Key functionality:** DataTable listing with AJAX fetch, role filter, multi-step registration wizard (public, no auth), add/edit, soft delete, bulk action (AJAX). Generates `company_id` prefix-based auto-increment. Password hashing on create/update.

### Vehicles
- **Status:** Complete
- **Controller:** `VehicleController`
- **Routes:** `/admin/vehicles/*` (note typo route `vechicles/edit/(:num)`)
- **Views:** `vehicles/index.php`, `vehicles/add.php`, `vehicles/edit.php`, `vehicles/modals.php`
- **Key functionality:** DataTable listing with AJAX fetch, add/store, edit/update, soft delete, details JSON endpoint. Vehicle status defaults to `On Job`.

### Customers
- **Status:** Complete
- **Controller:** `CustomersController`
- **Routes:** `/admin/customers/*`
- **Views:** `customers/customers.php`, `customers/modals.php`
- **Key functionality:** DataTable with server-side processing (`load()`), details modal with vehicles, jobs, and invoices tabs, add/edit form views, bulk delete with transaction.

### Job Intake
- **Status:** Complete
- **Controller:** `JobIntake`
- **Routes:** `/job_intake/*` (filter: auth:admin,receptionist)
- **Views:** `job/index.php`, `job/modals.php`
- **Key functionality:** Search customers/vehicles, create new customers + vehicles inline, create job card with photo uploads, validation, transaction safety, unique job number generation (`JOB-YYYYMMDD-NNN`). Job status defaults to `Awaiting Assignment` (or `Awaiting Diagnosis` if mechanic assigned at intake).

### Jobs / Job Cards
- **Status:** Complete
- **Controller:** `JobsController`
- **Routes:** `/admin/jobs/*`
- **Views:** `admin/jobs/jobs_list.php`, `jobs/add.php`, `jobs/edit.php`, `job/index.php`, `job/modals.php`
- **Key functionality:** DataTable listing with vehicle join, add form, edit form, update, soft delete, dispatch mechanic assignment (`assign_mechanic`), role-gated status state machine with transition history tracking. Detail modal includes status history tab, mechanic info, and invoice tab.

### Mechanic Flow
- **Status:** Complete
- **Controller:** `JobIntake` (mechanic methods), `DashboardController` (mechanic dashboard)
- **Routes:** `/mechanic/*` (filter: auth:mechanic)
- **Views:** `mechanic/jobs.php`, `job/mechanic_diagnosis_form.php`
- **Key functionality:** Mechanic dashboard with stat cards (total jobs, awaiting diagnosis, in progress, completed) and recent assigned jobs list. Mechanic jobs list with DataTable, mechanic diagnosis view with parts/labor search and assignment, diagnosis form submission (`save_diagnosis`), inventory parts search (`search_parts`).

### Status Machine
- **Status:** Complete
- **Config:** `app/Config/JobStatus.php`
- **Key functionality:** Role-gated status transitions defined per status per role. Every transition logged in `job_status_history` with from/to status, changed_by user, timestamp, and optional notes. Endpoints: `POST /admin/jobs/update_status/(:num)`, `POST /mechanic/jobs/update_status/(:num)`, `GET /admin/jobs/status_history/(:num)`.

Transition map:

| Current Status | Admin Transitions | Mechanic Transitions | Receptionist Transitions |
|---|---|---|---|
| Awaiting Assignment | Awaiting Diagnosis, Cancelled | — | — |
| Awaiting Diagnosis | Diagnosis Complete, On Hold, Cancelled | Diagnosis Complete | — |
| Diagnosis Complete | Approved, Quote Sent, Rework, On Hold, Cancelled | — | — |
| Quote Sent | Approved, Cancelled | — | — |
| Approved | In Progress, On Hold, Cancelled | In Progress | — |
| In Progress | Quality Check, Awaiting Parts, On Hold, Cancelled, Rework | Quality Check, Awaiting Parts | — |
| Awaiting Parts | In Progress, On Hold, Cancelled | In Progress | — |
| Quality Check | Ready for Invoice, Rework, On Hold | Rework | — |
| Ready for Invoice | Paid, On Hold | — | Paid |
| Paid | Completed | — | — |
| On Hold | Awaiting Diagnosis, In Progress, Cancelled | — | — |
| Rework | In Progress, Cancelled | In Progress | — |
| Completed | — | — | — |
| Cancelled | — | — | — |

### Calendar
- **Status:** Complete
- **Controller:** `CalendarController`
- **Routes:** `/admin/calendar/*`
- **Views:** `calendar/calendar.php`, `calendar/modals.php`
- **Key functionality:** FullCalendar integration fetching job events (drop-offs, estimated completions, completed) and custom calendar events. Custom event creation with validation. Drag-drop event date update.

### Sublets
- **Status:** Complete
- **Controller:** `SubletsController`
- **Routes:** `/admin/sublets/*`
- **Views:** `sublets/index.php`, `sublets/form.php`, `sublets/_details.php`, `sublets/modals.php`
- **Key functionality:** DataTable with server-side processing, status filter, add/edit form with validation, details modal with job and supplier info, single and bulk delete. `add($id)` handles both add and edit form loading. `save()` handles both create and update.

### Org Settings
- **Status:** Complete
- **Controller:** `SettingsController`
- **Routes:** `/admin/settings` (GET), `/admin/settings/update` (POST)
- **Views:** `admin/settings/index.php`
- **Model:** `OrgSettingsModel`
- **Key functionality:** Single-row org-wide configuration. Manages org name, logo (upload to `uploads/org/`), address, contact info, currency, currency symbol, VAT rate, default labor rate, invoice prefix, invoice due days, financial year start month. Pre-fills form, updates via `updateSettings()`.

### Invoices
- **Status:** Complete
- **Controller:** `InvoicesController`
- **Routes:** `/admin/invoices/*`
- **Views:** `admin/invoices/index.php`, `admin/invoices/view.php`
- **Models:** `InvoiceModel`, `PaymentModel`
- **Key functionality:** Full invoice lifecycle. `generateFromJobCard()` sums parts (qty × unit_price_at_estimate), labor (generated `labor_cost`), and sublet costs (non-cancelled), applies VAT from org settings. Idempotent — returns existing invoice if one exists. Invoice view shows full breakdown with line items, totals, payment history, and inline payment form. Payments recorded with method (Cash, M-Pesa, Bank Transfer, Insurance, Credit). Each payment calls `updateAmountPaid()` to recalculate status. When invoice becomes Paid, job status auto-updates to Paid with history entry. Server-side DataTable processing. Overdue marking (`markOverdue`).

### Inventory
- **Status:** Complete
- **Controller:** `InventoryController`
- **Routes:** `/admin/inventory/*` (all under admin group); `/mechanic/inventory/search` (mechanic group)
- **Views:** `admin/inventory/index.php`, `admin/inventory/form.php`
- **Key functionality:** DataTable with server-side processing (`load()`), add/edit forms with validation, delete with referential integrity check (cannot delete if referenced in `job_card_parts_required`), `fetch($id)` JSON endpoint, `search()` endpoint used by mechanic diagnosis form. Mechanics access search via `/mechanic/inventory/search`.

### Suppliers
- **Status:** Complete
- **Controller:** `SuppliersController`
- **Routes:** `/admin/suppliers/*` (all under admin group)
- **Views:** `admin/suppliers/index.php`, `admin/suppliers/form.php`
- **Key functionality:** DataTable with server-side processing (`load()`), add/edit forms with validation, delete with referential integrity check (cannot delete if referenced in sublets), `getAll()` JSON endpoint used by sublets form dropdown.

### LPOs
- **Status:** Not built — no DB table, no controller, no routes
- **Sidebar link:** Leads to 404

### Petty Cash
- **Status:** Not built — no DB table, no controller, no routes
- **Sidebar link:** Leads to 404

### Reports
- **Status:** Not built — no controller, no routes
- **Sidebar link:** Leads to 404

### Customer Portal
- **Status:** Not built — customer role dashboard is a stub view
- **Routes:** `/customer/` loads `customer_dashboard.php` — no customer-facing functionality

### Profile Page
- **Status:** Not built — sidebar link `/admin/profile` has no controller or route
- **Sidebar footer:** Profile link leads to 404

## 6. JOB WORKFLOW

### Complete Lifecycle

```
Intake (receptionist/admin)
    │
    ▼
Awaiting Assignment
    │  Admin: assign mechanic (dispatch)
    ▼
Awaiting Diagnosis
    │  Mechanic: submit diagnosis (parts + labor + notes)
    ▼
Diagnosis Complete
    │  Admin: approve or send quote
    ├────────────────────┐
    ▼                    ▼
Approved             Quote Sent
    │                    │  Admin: approve quote
    │                    ▼
    │               Approved
    └────────────────────┘
    │  Admin or Mechanic: start work
    ▼
In Progress
    │  Mechanic or Admin: can move to Quality Check or Awaiting Parts
    ├────────────────────┐
    ▼                    ▼
Quality Check        Awaiting Parts
    │                    │  Mechanic or Admin: move back when parts arrive
    │                    ▼
    │               In Progress
    └────────────────────┘
    │  Admin: Ready for Invoice (or Rework if issues)
    ▼
Ready for Invoice ──► auto-generates Invoice (Draft)
    │  Admin: accepts payment
    ▼
Paid
    │  Admin: mark completed
    ▼
Completed
```

**On Hold** and **Rework** are non-terminal states accessible from multiple statuses. **Cancelled** is a terminal state accessible from most active statuses.

**Notes:**
- All status transitions are enforced server-side via `app/Config/JobStatus.php`
- Every transition is logged in `job_status_history`
- Transition options are available as buttons in the admin job details modal and mechanic diagnosis page
- Invoice auto-generation happens when status moves to `Ready for Invoice` via `InvoicesController::generate()`

## 7. FINANCIAL FLOW

- **Invoice auto-generation:** When a job reaches `Ready for Invoice`, admin clicks "Generate Invoice" which calls `InvoiceModel::generateFromJobCard()`. This is idempotent — if an invoice already exists for the job card, it returns the existing one.
- **Invoice calculation:**
  - `parts_total` = SUM(quantity_required × unit_price_at_estimate) from `job_card_parts_required`
  - `labor_total` = SUM(labor_cost) from `job_card_labor_tasks` (labor_cost is a generated column: estimated_hours × rate_per_hour)
  - `sublet_total` = SUM(cost) from `sublets` where status != 'Cancelled'
  - `subtotal` = parts_total + labor_total + sublet_total
  - `vat_amount` = subtotal × (vat_rate / 100) — vat_rate from `org_setting('vat_rate', 16)`
  - `grand_total` = subtotal + vat_amount
- **Invoice statuses:** Draft → Sent → Partially Paid → Paid → Overdue — or Cancelled
- **Balance due:** `invoices.balance_due` is a generated column — never set manually
- **Payments:** Recorded against an invoice via `InvoicesController::recordPayment()`. Each payment triggers `InvoiceModel::updateAmountPaid()` which recalculates `amount_paid` and adjusts `status` (Partially Paid / Paid).
- **Auto job status update:** When `updateAmountPaid()` sets invoice status to `Paid`, the associated job card status is updated to `Paid` with a status history entry.
- **Revenue reporting:** Dashboard computes revenue from `payments.payment_date` (last 6 months) and outstanding balance from `invoices.balance_due` (non-Paid, non-Cancelled).

## 8. ROLES AND PERMISSIONS

| Role | Route Groups | Capabilities |
|------|-------------|--------------|
| **admin** | `/admin/*` (filter: `auth:admin`) | Full access to all modules — users, customers, vehicles, jobs, sublets, invoices, calendar, settings. All status transitions available. |
| **receptionist** | `/receptionist/` (filter: `auth:receptionist`), `/job_intake/*` (filter: `auth:admin,receptionist`) | Own dashboard view. Can perform job intake (create customers, vehicles, job cards). No admin modules. |
| **mechanic** | `/mechanic/*` (filter: `auth:mechanic`) | Own dashboard with assigned job stats. Can view assigned jobs, submit diagnosis (add parts/labor tasks), search inventory parts. Limited role-gated status transitions. |
| **customer** | `/customer/` (filter: `auth:customer`) | Stub dashboard view only. No functional routes. |

**AuthFilter behavior:** Route groups pass role arguments to the filter. If no arguments, filter only checks `isLoggedIn`. Invalid role redirects to `/unauthorized`.

## 9. ROUTE PATTERNS

### Public (no auth filter)
```
GET  /                          -> Home::index
GET  /login                     -> LoginController::index
POST /login/auth                -> LoginController::auth
GET  /logout                    -> LoginController::logout
GET  /unauthorized              -> DashboardController::unauthorized
```

### User Registration Wizard (no auth)
```
GET  /user/add_step1            -> UsersController::addStep1
POST /user/add_step1            -> UsersController::add_step1
GET  /user/add_step2            -> UsersController::addStep2
POST /user/add_step2            -> UsersController::add_step2
GET  /user/add_step3            -> UsersController::addStep3
POST /user/add_step3            -> UsersController::addUserStep3
POST /user/addUserStep3         -> UsersController::addUserStep3
GET  /user/preview              -> UsersController::preview
GET  /user/saveUser             -> UsersController::saveUser
POST /save-step-data/(:num)     -> UsersController::saveStepData/$1
POST /final-submit              -> UsersController::finalSubmit
GET  /user/getLastId            -> UsersController::getLastId
POST /user/submit               -> UsersController::submit
GET  /user/success              -> UsersController::success
GET  /user/failure              -> UsersController::failure
```

### Job Intake (filter: auth:admin,receptionist)
```
GET  /job_intake/                      -> JobIntake::index
GET  /job_intake/search                -> JobIntake::search
POST /job_intake/create_job_card       -> JobIntake::create_job_card
GET  /job_intake/create_job_card       -> JobIntake::create_job_card
POST /job_intake/fetch_vehicle_details -> JobIntake::fetch_vehicle_details
POST /job_intake/fetch_customer_details-> JobIntake::fetch_customer_details
```

### Admin (filter: auth:admin)
```
GET    /admin/                              -> DashboardController::admin
GET    /admin/dashboard                     -> DashboardController::admin
GET    /admin/users                         -> UsersController::index
GET    /admin/users/add                     -> UsersController::add
GET    /admin/users/(:num)                  -> UsersController::details/$1
GET    /admin/users/edit/(:num)             -> UsersController::edit/$1
POST   /admin/users/update/(:num)           -> UsersController::update/$1
GET    /admin/users/delete/(:num)           -> UsersController::delete/$1
POST   /admin/users/bulk_action             -> UsersController::bulk_action
GET    /admin/users/fetch/(:num)            -> UsersController::details/$1
GET    /admin/users/fetch                   -> UsersController::fetchUsers
GET    /admin/vehicles                      -> VehicleController::index
GET    /admin/vehicles/fetch                -> VehicleController::fetchVehicles
GET    /admin/vehicles/fetch/(:num)         -> VehicleController::fetchVehicles
GET    /admin/vechicles/edit/(:num)         -> VehicleController::edit/$1
POST   /admin/vehicles/store                -> VehicleController::store
POST   /admin/vehicles/update/(:num)        -> VehicleController::update/$1
POST   /admin/vehicles/delete/(:num)        -> VehicleController::delete/$1
POST   /admin/vehicles/add                  -> VehicleController::add
GET    /admin/vehicles/edit/(:num)          -> VehicleController::edit/$1
GET    /admin/vehicles/delete/(:num)        -> VehicleController::delete/$1
GET    /admin/vehicles/details/(:num)       -> VehicleController::details/$1
GET    /admin/jobs                          -> JobsController::index
GET    /admin/jobs/fetch                    -> JobsController::fetchJobs
GET    /admin/jobs/add                      -> JobsController::add
POST   /admin/jobs/create                   -> JobsController::create
GET    /admin/jobs/(:num)                   -> JobsController::details/$1
GET    /admin/jobs/edit/(:num)              -> JobsController::edit/$1
POST   /admin/jobs/update/(:num)            -> JobsController::update/$1
GET    /admin/jobs/delete/(:num)            -> JobsController::delete/$1
POST   /admin/jobs/bulk_action              -> JobsController::bulk_action
POST   /admin/jobs/assign_mechanic/(:num)   -> JobsController::assign_mechanic/$1
POST   /admin/jobs/update_status/(:num)     -> JobsController::update_status/$1
GET    /admin/jobs/status_history/(:num)    -> JobsController::status_history/$1
GET    /admin/job_intake                    -> JobIntake::index
GET    /admin/job_intake/search             -> JobIntake::search
POST   /admin/job_intake/create_job_card    -> JobIntake::create_job_card
GET    /admin/job_intake/create_job_card    -> JobIntake::create_job_card
POST   /admin/job_intake/fetch_vehicle_details  -> JobIntake::fetch_vehicle_details
POST   /admin/job_intake/fetch_customer_details -> JobIntake::fetch_customer_details
GET    /admin/customers                     -> CustomersController::index
POST   /admin/customers/load                -> CustomersController::load
GET    /admin/customers/load                -> CustomersController::load
GET    /admin/customers/details/(:num)      -> CustomersController::details/$1
GET    /admin/customers/add                 -> CustomersController::add
GET    /admin/customers/edit/(:num)         -> CustomersController::edit/$1
POST   /admin/customers/bulk_action         -> CustomersController::bulk_action
GET    /admin/settings                      -> SettingsController::index
POST   /admin/settings/update               -> SettingsController::update
GET    /admin/invoices                      -> InvoicesController::index
GET    /admin/invoices/load                 -> InvoicesController::load
GET    /admin/invoices/view/(:num)          -> InvoicesController::view/$1
GET    /admin/invoices/generate/(:num)      -> InvoicesController::generate/$1
POST   /admin/invoices/record_payment/(:num)-> InvoicesController::recordPayment/$1
GET    /admin/invoices/mark_overdue         -> InvoicesController::markOverdue
GET    /admin/calendar                      -> CalendarController::index
GET    /admin/calendar/getEvents            -> CalendarController::getEvents
GET    /admin/calendar/addEvent             -> CalendarController::addEvent
POST   /admin/calendar/addEvent             -> CalendarController::addEvent
POST   /admin/calendar/updateEventDate      -> CalendarController::updateEventDate
GET    /admin/sublets                       -> SubletsController::index
GET    /admin/sublets/add                   -> SubletsController::add
POST   /admin/sublets/load                  -> SubletsController::load
GET    /admin/sublets/edit/(:num)           -> SubletsController::add/$1
POST   /admin/sublets/save                  -> SubletsController::save
GET    /admin/sublets/details/(:num)        -> SubletsController::details/$1
POST   /admin/sublets/delete/(:num)         -> SubletsController::delete/$1
POST   /admin/sublets/bulkAction            -> SubletsController::bulkAction
GET    /admin/sublets/fetch                 -> SubletsController::fetchSublets
GET    /admin/sublets/fetch/(:num)          -> SubletsController::fetchSublets/$1
GET    /admin/sublets/(:num)                -> SubletsController::details/$1
GET    /admin/sublets/(:num)/edit           -> SubletsController::edit/$1
POST   /admin/sublets/(:num)/update         -> SubletsController::update/$1
```

### Mechanic (filter: auth:mechanic)
```
GET  /mechanic/                      -> DashboardController::mechanic
GET  /mechanic/dashboard             -> DashboardController::mechanic
GET  /mechanic/jobs                  -> JobIntake::mechanic_jobs
GET  /mechanic/jobs/(:num)           -> JobIntake::mechanic_view/$1
POST /mechanic/save_diagnosis        -> JobIntake::save_diagnosis
GET  /mechanic/search_parts          -> JobIntake::search_parts
POST /mechanic/jobs/update_status/(:num) -> JobsController::update_status/$1
```

### Receptionist (filter: auth:receptionist)
```
GET /receptionist/ -> DashboardController::receptionist
```

### Customer (filter: auth:customer)
```
GET /customer/ -> DashboardController::customer
```

## 10. CODING CONVENTIONS

- **Controllers** extend `BaseController` (extends `CodeIgniter\Controller`).
- **Models** extend `CodeIgniter\Model` with `$allowedFields`, `$returnType = 'array'`, `$useSoftDeletes` and `$useTimestamps` as appropriate.
- **All DB access via Models** — no `$db->table()` queries unless doing complex aggregations/joins across tables (DashboardController revenue queries, InvoicesController line item queries).
- **AJAX responses** use `$this->respond()` (via `CodeIgniter\API\ResponseTrait`) or `$this->response->setJSON()`.
- **DataTables server-side processing** uses the `load()` pattern with `draw`, `start`, `length`, `search`, `order` parameters and responds with `recordsTotal`, `recordsFiltered`, `data`.
- **Pagination** uses CI4's `$model->paginate(10)` and passes `$model->pager` to view.
- **Views with sidebar** use `$this->extend('layouts/main')` + `$this->section('content')`.
- **File uploads** use `$file->getRandomName()` + `$file->move()` to `uploads/{subfolder}/`.
- **Soft deletes** use `deleted_at` datetime column on `users` and `job_cards`. `customers` does not have soft-delete.
- **CSRF** globally enabled — every POST form must include `<?= csrf_field() ?>`. AJAX POSTs use `getCsrfMeta()` JS function from main layout which reads meta tags and refreshes token from response header.
- **No CSRF exemption** configured for any route.
- **Generated columns** (`users.name`, `job_card_labor_tasks.labor_cost`, `invoices.balance_due`) — never include in `$allowedFields`.
- **`org_setting($key, $default)`** available globally via `settings_helper.php` — calls `OrgSettingsModel::getSettings()` once per request (static cache).

## 11. GOTCHAS

1. **`job_cards` is the real transactional table** — the `jobs` table is legacy/unused. Never write to `jobs`.
2. **Always `UsersController`** (with 's'), not `UserController`.
3. **Always `App\Controllers\CustomersController`**, not `Admin\CustomersController` — there is no `Admin\` subdirectory in Controllers.
4. **`registration_number`** is the correct `vehicles` column (not `vehicle_number`).
5. **`baseURL`** (`app/Config/App.php`) must be `http://localhost/FlowDesk/` and **`.htaccess RewriteBase`** must be `/FlowDesk/`. Both must match. Change both when deploying.
6. **Database name is `flowdesk`** (lowercase) — set in `.env` as `database.default.database = flowdesk`. Overrides value in `app/Config/Database.php`.
7. **`users.name` is a STORED GENERATED column** — never write to it, never include in `$allowedFields` of `UserModel`.
8. **`job_card_labor_tasks.labor_cost` is a STORED GENERATED column** — never set it manually.
9. **`invoices.balance_due` is a STORED GENERATED column** — never set it manually.
10. **`org_settings` always has exactly one row (id=1)** — use `OrgSettingsModel::getSettings()` and `updateSettings()` only.
11. **`InvoiceModel::updateAmountPaid()`** must be called after every payment insert to keep `amount_paid` and `status` in sync.
12. **`InvoiceModel::generateFromJobCard()` is idempotent** — safe to call multiple times, returns existing invoice if one exists.
13. **CSRF meta tags are in `layouts/main.php` head** — `getCsrfMeta()` reads `csrf-name` and `csrf-token` for all AJAX POSTs. Token auto-refreshes from response header.
14. **No auto-routing** — every route is explicitly defined in `Routes.php`. Adding a new controller method requires a corresponding route entry.
15. **Route typo exists:** `vechicles/edit/(:num)` (missing 'h') is an active route alongside correct `vehicles/edit/(:num)`.
16. **Sidebar conditional visibility** — admin-only links (`users`, `customers`, `inventory`, `suppliers`, `invoices`, `calendar`, `LPOs`, `petty cash`, `reports`, `settings`) render when `$role == 'admin'`. All roles see `Dashboard`, `Jobs`, `Vehicles`, and `Sublets`.

## 12. PENDING / NOT BUILT

- **LPOs module** — No DB table, no controller, no routes. Sidebar link and dashboard quick action card lead to 404.
- **Petty Cash module** — No DB table, no controller, no routes. Sidebar link and dashboard quick action card lead to 404.
- **Reports module** — No controller, no routes. Sidebar link leads to 404.
- **Customer portal** — Customer role exists and can log in, but the dashboard is a stub view with no customer-facing functionality (no job tracking, no invoice viewing).
- **Profile page** — Sidebar footer links to `/admin/profile` which has no route. Leads to 404.
- **Forgot password flow** — Login page has a "Forgot Password?" link with no corresponding route or functionality.
- **Calendar event update by drag-drop** — Route exists (`POST /admin/calendar/updateEventDate`) but the method `CalendarController::updateEventDate()` is not implemented.
