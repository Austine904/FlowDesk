# AGENTS.md — FlowDesk Organizational Management System

## 1. PROJECT OVERVIEW

- **System name:** FlowDesk — Organizational Management System
- **Purpose:** Modular, single-org deployment management system for small businesses (garages, clinics, shops)
- **Stack:** CodeIgniter 4 (v4.x, PHP 8.2+), MySQL 5.7+ / MariaDB (`flowdesk`), Tailwind CSS (CLI-compiled), jQuery 3.7, DataTables 1.13.6, FullCalendar 6.1, Chart.js (lazy-loaded), SweetAlert2, Select2, Font Awesome / Bootstrap Icons
- **Web server:** Apache with mod_rewrite (XAMPP)
- **Local URL:** `http://localhost/FlowDesk/`
- **Local path:** `C:\xampp\htdocs\FlowDesk`
- **GitHub:** `https://github.com/Austine904/FlowDesk`
- **UI Framework:** Tailwind CSS (CLI-compiled) — **Bootstrap fully removed from layout**
- **Tailwind CLI:** `C:\xampp\htdocs\FlowDesk\tailwindcss.exe`
- **Tailwind Build:** `.\tailwindcss.exe -i public\assets\css\input.css -o public\assets\css\tailwind.css --minify` (re-run after adding new utility classes to any view)
- **Tailwind Watch:** `npm run watch:css` (requires Node.js; configured via `package.json` scripts) — watches for file changes and auto-rebuilds
- **Source CSS:** `public/assets/css/input.css`
- **Compiled output:** `public/assets/css/tailwind.css` — loaded via `base_url('public/assets/css/tailwind.css')`
- **Tailwind config:** `tailwind.config.js` with `content: ['./app/Views/**/*.php', './public/assets/js/**/*.js']`
- **Font:** Inter (Google Fonts, weights 300-700)

### Design System
- Page background: `bg-gray-50`
- Sidebar background: `bg-slate-900` with `bg-indigo-600` active state
- Cards: `bg-white rounded-xl shadow-sm border border-gray-200`
- Primary button: `bg-indigo-600 hover:bg-indigo-700 text-white`
- Secondary button: `bg-white border border-gray-300 hover:bg-gray-50 text-gray-700`
- Danger button: `bg-red-600 hover:bg-red-700 text-white`
- Table header: `bg-gray-50 text-gray-500 text-xs font-medium uppercase tracking-wider`
- Success badge: `text-emerald-600 bg-emerald-50`
- Warning badge: `text-amber-600 bg-amber-50`
- Danger badge: `text-red-600 bg-red-50`
- Input: `rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent`
- Custom DataTables styles defined inline in `layouts/main.php`

---

## 2. ARCHITECTURE

```
C:\xampp\htdocs\FlowDesk\
├── app/
│   ├── Config/           # App, Database, Routes, Filters, JobStatus
│   ├── Controllers/      # 21 controllers (root namespace, no Admin/ subdirectory)
│   ├── Database/
│   │   ├── Migrations/   # Empty
│   │   └── Seeds/        # Empty
│   ├── Filters/
│   │   └── AuthFilter.php
│   ├── Helpers/
│   │   ├── settings_helper.php   # org_setting() + log_activity() — globally available
│   │   └── activity_helper.php   # timeAgo() — used by dashboard
│   ├── Models/            # 20 models
│   └── Views/
│       ├── admin/         # dashboard, users, invoices, settings, inventory, suppliers, forms, jobs, lpos, petty_cash, reports, profile
│       ├── calendar/      # calendar, modals
│       ├── customers/     # customers, modals
│       ├── errors/        # unauthorized, html/, cli/
│       ├── job/           # index, modals
│       ├── jobs/          # add, edit (legacy, Bootstrap)
│       ├── layouts/       # main
│       ├── mechanic/      # jobs (Bootstrap)
│       ├── partials/      # sidebar
│       ├── sublets/       # index, form, _details, modals
│       ├── user/          # add_step1/2/3, preview, success, getLastId, failure (Bootstrap)
│       ├── vehicles/      # index, add (Bootstrap), edit (Bootstrap), modals
│       ├── customer/      # layout, dashboard, jobs, invoices, invoice_view
│       └── *.php          # login, dashboard variants, welcome_message, job_intake_form (Bootstrap), mechanic_diagnosis_form (Bootstrap), forgot_password, reset_password
├── public/
│   ├── assets/
│   │   ├── css/           # input.css (tailwind source), tailwind.css (compiled output)
│   │   ├── js/            # 11 files (vehicles, job_intake, customers, calendar, sublets, inventory, suppliers, dashboard-refresh, notifications, global-search, datatable-config)
│   │   └── vendor/        # datatables/ (jquery.dataTables.min.css, jquery.dataTables.min.js)
│   └── css/               # 9 legacy per-module CSS files (orphaned — safe to delete)
├── uploads/
│   ├── users/             # Profile pictures
│   ├── job_card_photos/   # Job card photos
│   └── org/               # Org logo uploads (exists with .gitkeep)
├── vendor/                # Composer dependencies
├── writable/              # CI4 cache, logs, sessions
├── .htaccess              # RewriteBase /FlowDesk/
├── tailwindcss.exe        # Tailwind CLI binary
├── tailwind.config.js     # Tailwind configuration
├── package.json           # npm scripts (build:css, watch:css)
└── index.php              # Front controller
```

**Routing:** Defined in `app/Config/Routes.php`. Explicit routes only (no auto-routing). Route groups use the `filter` option for auth. Public routes are top-level. Protected routes are grouped by prefix (`admin`, `job_intake`, `mechanic`, `receptionist`, `customer`).

**Auth:** `AuthFilter` (`app/Filters/AuthFilter.php`) checks `session()->get('isLoggedIn')`. If role arguments are provided, validates `session()->get('role')` is in the allowed list (comma-separated, e.g., `auth:admin,receptionist`). Session keys: `user_id`, `user_name`, `role`, `company_id`, `profile_picture`, `isLoggedIn`. For customer portal, `customer_id` is also stored.

**Views:** Most admin views extend `layouts/main` via `$this->extend('layouts/main')` with `$this->section('content')`. Standalone views (login, forgot_password, reset_password, user registration wizard, error pages) do not extend the layout. Customer portal uses `customer/layout.php`.

**Models:** 20 models in `app/Models/`. Some controllers use `$db->table()` query builder directly for complex joins (InvoicesController, ReportsController).

**CSRF:** Globally enabled via `app/Config/Filters.php` (`$globals['before']` includes `'csrf'`). Every POST form must include `<?= csrf_field() ?>`. CSRF token injected as meta tags in `layouts/main.php` — JS reads them via `getCsrfMeta()` for all AJAX POSTs. AJAX setup in main layout auto-appends CSRF token and refreshes on response header.

**File uploads:** `uploads/users/` (profile pictures), `uploads/job_card_photos/` (job card photos), `uploads/org/` (org logo). All validated by MIME type + extension. `.htaccess` files block PHP execution in upload directories.

---

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

**Note:** `customers` has no `deleted_at` column — no soft-delete. Has functional `store()` and `update()` routes/methods.

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
| diagnosis_category | varchar(100) | NULL, optional structured category for reporting |
| initial_damage_notes | text | NULL |
| job_status | varchar(30) | NOT NULL, default 'Awaiting Diagnosis' |
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
| completed_at | datetime | NULL, set once on first transition to Completed |

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
| is_stocked | tinyint(1) | NOT NULL DEFAULT 0 — 0=catalog only, 1=tracked |
| quantity_in_hand | decimal(10,2) | NOT NULL DEFAULT 0.00 |
| reorder_level | decimal(10,2) | NOT NULL DEFAULT 0.00 |
| unit | varchar(20) | NOT NULL DEFAULT 'piece' |

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

#### lpos
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| lpo_no | varchar(30) | NOT NULL, UNIQUE |
| supplier_id | int(10) unsigned | FK → suppliers.id |
| job_card_id | int(10) unsigned | NULL, FK → job_cards.id, ON DELETE SET NULL |
| raised_by | int(10) unsigned | FK → users.id |
| lpo_date | date | NOT NULL |
| expected_date | date | NULL |
| status | enum('Draft','Sent','Partially Received','Received','Cancelled') | NOT NULL, default 'Draft' |
| notes | text | NULL |
| total_amount | decimal(12,2) | NOT NULL DEFAULT 0.00 |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |

#### lpo_items
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| lpo_id | int(10) unsigned | FK → lpos.id, ON DELETE CASCADE |
| inventory_id | int(10) unsigned | FK → inventory.id |
| quantity_ordered | decimal(10,2) | NOT NULL DEFAULT 1.00 |
| quantity_received | decimal(10,2) | NOT NULL DEFAULT 0.00 |
| unit_price | decimal(12,2) | NOT NULL DEFAULT 0.00 |
| line_total | decimal(12,2) | GENERATED ALWAYS AS (quantity_ordered * unit_price) STORED |

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

**Note:** The `jobs` table is legacy/unused. All current operations use `job_cards`. Never write to `jobs`.

#### petty_cash
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| transaction_date | date | NOT NULL |
| type | enum('Income','Expense') | NOT NULL |
| category | varchar(100) | NOT NULL |
| description | text | NOT NULL |
| amount | decimal(12,2) | NOT NULL |
| reference_no | varchar(100) | NULL |
| recorded_by | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

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
| lpos.supplier_id | suppliers.id | fk_lpo_supplier |
| lpos.job_card_id | job_cards.id | fk_lpo_job_card (ON DELETE SET NULL) |
| lpos.raised_by | users.id | fk_lpo_raised_by |
| lpo_items.lpo_id | lpos.id | fk_lpo_items_lpo (ON DELETE CASCADE) |
| lpo_items.inventory_id | inventory.id | fk_lpo_items_inventory |
| jobs.assigned_to | users.id | fk_jobs_assigned (legacy) |
| petty_cash.recorded_by | users.id | fk_pc_recorded_by |
| activity_log.user_id | users.id | fk_al_user |

### activity_log

| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| user_id | int(10) unsigned | FK → users.id |
| action | varchar(100) | NOT NULL |
| entity_type | varchar(50) | NOT NULL (e.g. 'job_card', 'invoice', 'lpo', 'user', 'petty_cash') |
| entity_id | int(10) unsigned | NULL |
| description | text | NOT NULL |
| ip_address | varchar(45) | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

**Indexes:** `idx_al_user` (user_id), `idx_al_entity` (entity_type, entity_id), `idx_al_created_at` (created_at)

### Generated Columns (never include in $allowedFields)

- `users.name` — `GENERATED ALWAYS AS (first_name + last_name) STORED`
- `job_card_labor_tasks.labor_cost` — `GENERATED ALWAYS AS (estimated_hours * rate_per_hour) STORED`
- `invoices.balance_due` — `GENERATED ALWAYS AS (grand_total - amount_paid) STORED`
- `lpo_items.line_total` — `GENERATED ALWAYS AS (quantity_ordered * unit_price) STORED`

---

## 4. MODELS

All models are in `app/Models/`. All extend `CodeIgniter\Model`.

| # | Model | File | Table | Key Custom Methods |
|---|-------|------|-------|--------------------|
| 1 | UserModel | `app/Models/UserModel.php` | `users` | `getByCompanyId(string): ?array` — find user by company_id; `getByRole(string): array` — find all users by role; `getLastCompanyIdNumber(string): int` — get highest numeric suffix for a company_id prefix |
| 2 | NextOfKinModel | `app/Models/NextOfKinModel.php` | `next_of_kin` | `getByUserId(int): ?array` — get next-of-kin for a user |
| 3 | CustomerModel | `app/Models/CustomerModel.php` | `customers` | `searchByPhoneOrName(string): array` — search customers by phone or name; `getWithVehicleCount(): array` — list all customers with vehicle count. $useTimestamps=true (createdField only, no updatedField) |
| 4 | VehicleModel | `app/Models/VehicleModel.php` | `vehicles` | `getByOwner(int): array` — get vehicles by owner_id; `getByRegistration(string): ?array` — find vehicle by registration_number; `searchByTerm(string): array` — search by reg, VIN, or chassis number |
| 5 | JobCardModel | `app/Models/JobCardModel.php` | `job_cards` | `getWithDetails(int): ?array` — single job card with joins; `getByStatus(string): array` — filter by job_status; `getAssignedToMechanic(int): array` — jobs assigned to a mechanic; `getRecentJobs(int): array` — most recent jobs; `generateJobNo(): string` — generate next JOB-YYYYMMDD-NNN; `getStatusHistory(int): array` — delegate to JobStatusHistoryModel |
| 6 | JobCardPhotoModel | `app/Models/JobCardPhotoModel.php` | `job_card_photos` | `getByJobCard(int): array` — get photos for a job card |
| 7 | JobCardPartModel | `app/Models/JobCardPartModel.php` | `job_card_parts_required` | `getByJobCard(int): array` — get parts with inventory details; `deleteByJobCard(int): void` — delete all parts for a job card |
| 8 | JobCardLaborModel | `app/Models/JobCardLaborModel.php` | `job_card_labor_tasks` | `getByJobCard(int): array` — get tasks with computed labor_cost; `deleteByJobCard(int): void` — delete all tasks for a job card |
| 9 | InventoryModel | `app/Models/InventoryModel.php` | `inventory` | `search(string): array` — search by name or part_number (returns stock fields); `getLowStock(): array` — items where is_stocked=1 AND qty ≤ reorder; `incrementStock(int, float): void` — add quantity to stock (called from LpoController receive); `decrementStock(int, float): void` — subtract quantity (min 0), called from JobIntake::save_diagnosis() |
| 10 | SupplierModel | `app/Models/SupplierModel.php` | `suppliers` | `getAll(): array` — all suppliers ordered by name |
| 11 | SubletModel | `app/Models/SubletModel.php` | `sublets` | `getWithDetails(int|null): array` — sublet with job_no, reg_number, provider_name joins |
| 12 | CalendarEventModel | `app/Models/CalendarEventModel.php` | `calendar_events` | `getUpcoming(int): array` — upcoming events; `getByDateRange(string, string): array` — events within a date range. `$updatedField = ''` (fixed). |
| 13 | JobStatusHistoryModel | `app/Models/JobStatusHistoryModel.php` | `job_status_history` | `getByJobCard(int): array` — status history for a job card with username |
| 14 | OrgSettingsModel | `app/Models/OrgSettingsModel.php` | `org_settings` | `getSettings(): array` — fetch single row (id=1); `updateSettings(array): bool` — update row (id=1) |
| 15 | InvoiceModel | `app/Models/InvoiceModel.php` | `invoices` | `generateInvoiceNo(): string` — next INV-YYYYMM-NNN; `generateFromJobCard(int, int, float): array` — create invoice from job card totals, accepts optional $discount (default 0.00), idempotent; `getWithDetails(int|null): array` — invoice with customer/job/creator joins; `updateAmountPaid(int): void` — recalculate amount_paid and status from payments; `getOverdueCount(): int`; `getOverdueTotal(): float`; `getOutstandingBalance(): float` |
| 16 | PaymentModel | `app/Models/PaymentModel.php` | `payments` | `getByInvoice(int): array` — payments for an invoice with received_by name; `getMonthlyRevenue(int): array` — monthly revenue totals for last N months; `getThisMonthRevenue(): float` — revenue for current month |
| 17 | LpoModel | `app/Models/LpoModel.php` | `lpos` | `generateLpoNo(): string` — next LPO-YYYYMM-NNN; `getWithDetails(int|null): array` — LPO with supplier, user, job joins; `recalculateTotal(int): void` — SUM line_total from lpo_items |
| 18 | LpoItemModel | `app/Models/LpoItemModel.php` | `lpo_items` | `getByLpo(int): array` — items with inventory name/part_number/unit; `deleteByLpo(int): void` — delete all items for an LPO |
| 19 | PettyCashModel | `app/Models/PettyCashModel.php` | `petty_cash` | `getWithDetails(int\|null): array` — joined with users; `getSummary(): array` — total income/expense/balance; `getSummaryByPeriod(string, string): array` — filtered summary; `getByCategory(string\|null): array` — category totals; `getRunningBalance(): array` — running balance in PHP |
| 20 | ActivityLogModel | `app/Models/ActivityLogModel.php` | `activity_log` | `log(int, string, string, int, string): int` — create activity log entry with IP; `getRecent(int): array` — recent activity with user name; `getByUser(int, int): array` — activity for a user; `getByEntity(string, int): array` — activity for a specific entity; `getByPeriod(string, string): array` — activity in date range |

---

## 5. MODULES

### Auth (Login/Logout)
- **Status:** Complete
- **Controller:** `LoginController`
- **Routes:** `GET /login`, `POST /login/auth`, `GET /logout`
- **Views:** `login.php` (Tailwind)
- **Key functionality:** Login with `company_id` + `password` (hashed with `password_hash`), session creation, role-based dashboard redirect. Validation uses `$this->validate()` with inline rules. Logout destroys session and redirects to `/login`. Customer login stores `customer_id` in session from `customers` table lookup.

### Dashboard
- **Status:** Complete — Tailwind
- **Controller:** `DashboardController`
- **Routes:** `/admin/dashboard` (admin), `/receptionist/`, `/mechanic/`, `/customer/`, `/unauthorized`
- **Views:** `admin/dashboard.php` (Tailwind), `mechanic_dashboard.php` (Bootstrap), `receptionist_dashboard.php` (Bootstrap), `customer_dashboard.php` (Bootstrap)
- **Key functionality:** Admin dashboard with summary cards (total jobs, monthly revenue, outstanding balance, petty cash balance), job status doughnut chart (Chart.js, lazy-loaded), revenue line chart (last 6 months, real data from `payments` table), recent activity feed (from `activity_log` table), recent jobs list, upcoming events, alerts (low stock, pending LPOs, overdue invoices), quick action buttons, auto-refresh every 30s via JS. Controller refactored into private methods (`buildStatsData`, `buildJobStatusChartData`, `buildRevenueData`, `buildAlertsData`, `buildUpcomingEvents`, `buildRecentActivity`).

### Users
- **Status:** Complete — Tailwind (list); Bootstrap (registration wizard)
- **Controller:** `UsersController`
- **Routes:** `/admin/users/*`, public registration wizard `/user/*`
- **Views:** `admin/users.php` (Tailwind), `admin/add_user.php` (Tailwind), `admin/edit_user.php` (Tailwind), `admin/users/user_list.php` (Tailwind), `user/add_step1.php` (Bootstrap), `user/add_step2.php` (Bootstrap), `user/add_step3.php` (Bootstrap), `user/preview.php` (Bootstrap), `user/success.php` (Bootstrap), `user/failure.php` (Bootstrap), `user/getLastId.php`
- **Key functionality:** DataTable listing with AJAX fetch, role filter, multi-step registration wizard (public, no auth), add/edit (Tailwind), soft delete, bulk action (AJAX). Generates `company_id` prefix-based auto-increment. Password hashing on create/update.

### Vehicles
- **Status:** Complete — DataTable (Tailwind); add/edit forms (Bootstrap, legacy)
- **Controller:** `VehicleController`
- **Routes:** `/admin/vehicles/*`
- **Views:** `vehicles/index.php` (Tailwind), `vehicles/modals.php` (Tailwind), `vehicles/add.php` (Bootstrap), `vehicles/edit.php` (Bootstrap)
- **Key functionality:** DataTable listing with AJAX fetch, add/store (via modal or legacy form), edit/update (via modal or legacy form), soft delete, details JSON endpoint. Vehicle status defaults to `On Job`. Controller passes `$customers` array to the index view for owner dropdown.

### Customers
- **Status:** Complete — Tailwind
- **Controller:** `CustomersController`
- **Routes:** `/admin/customers/*`
- **Views:** `customers/customers.php` (Tailwind), `customers/modals.php` (Tailwind), `admin/forms/add_customer_form.php` (Tailwind), `admin/forms/edit_customer_form.php` (Tailwind)
- **Key functionality:** DataTable with server-side processing (`load()`), details modal with vehicles, jobs, and invoices tabs, add/edit via AJAX modals (Tailwind with icons, section headers, helper text), bulk delete with transaction. `store()` and `update()` controller methods exist with proper validation and routing.

### Job Intake
- **Status:** Complete — Tailwind
- **Controller:** `JobIntake`
- **Routes:** `/job_intake/*` (filter: auth:admin,receptionist)
- **Views:** `job/index.php` (Tailwind), `job/modals.php` (Tailwind), `job_intake_form.php` (Bootstrap, legacy)
- **Key functionality:** Search customers/vehicles, create new customers + vehicles inline, create job card with photo uploads, validation, transaction safety, unique job number generation (`JOB-YYYYMMDD-NNN`). Job status set dynamically: `'Awaiting Diagnosis'` if mechanic assigned at intake, `'Awaiting Assignment'` otherwise. Entry point for all new job cards (no separate "Add Job" button in Jobs list).

### Jobs / Job Cards
- **Status:** Complete — Tailwind (detail modal); Bootstrap (jobs_list.php, legacy forms)
- **Controller:** `JobsController`
- **Routes:** `/admin/jobs/*`
- **Views:** `admin/jobs/jobs_list.php` (Bootstrap), `jobs/add.php` (Bootstrap, legacy), `jobs/edit.php` (Bootstrap, legacy), `job/index.php` (Tailwind, detail modal), `job/modals.php` (Tailwind)
- **Key functionality:** DataTable listing with vehicle join, AJAX detail modal (Tailwind with tabs: Overview, Parts & Labor, Photos, LPOs & Invoice, Status History), dispatch mechanic assignment (`assign_mechanic` with status history logging), role-gated status state machine with transition history tracking. "Add New Job" button removed from list view — replaced with link to Job Intake.

### Mechanic Flow
- **Status:** Complete — Bootstrap (all views)
- **Controller:** `JobIntake` (mechanic methods), `DashboardController` (mechanic dashboard)
- **Routes:** `/mechanic/*` (filter: auth:mechanic)
- **Views:** `mechanic/jobs.php` (Bootstrap), `mechanic_dashboard.php` (Bootstrap), `mechanic_diagnosis_form.php` (Bootstrap)
- **Key functionality:** Mechanic dashboard with stat cards (total jobs, awaiting diagnosis, in progress, completed) and recent assigned jobs list. Mechanic jobs list with DataTable, mechanic diagnosis view with parts/labor search and assignment, diagnosis form submission (`save_diagnosis`), inventory parts search (`search_parts`).

### Status Machine
- **Status:** Complete
- **Config:** `app/Config/JobStatus.php`
- **Key functionality:** Role-gated status transitions defined per status per role. Every transition logged in `job_status_history` with from/to status, changed_by user, timestamp, and optional notes. Endpoints: `POST /admin/jobs/update_status/(:num)`, `POST /mechanic/jobs/update_status/(:num)`, `GET /admin/jobs/status_history/(:num)`. Status colors defined in config (`$statusColors` array) — used by dashboard chart.

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
- **Status:** Complete — Tailwind
- **Controller:** `CalendarController`
- **Routes:** `/admin/calendar/*`
- **Views:** `calendar/calendar.php` (Tailwind), `calendar/modals.php` (Tailwind)
- **Key functionality:** FullCalendar integration fetching job events (drop-offs, estimated completions, completed) and custom calendar events. Custom event creation with validation. Drag-drop event update route exists but `updateEventDate()` method is not implemented.

### Sublets
- **Status:** Complete — Tailwind
- **Controller:** `SubletsController`
- **Routes:** `/admin/sublets/*`
- **Views:** `sublets/index.php` (Tailwind with modals), `sublets/form.php` (Tailwind, inline modal), `sublets/_details.php` (Tailwind), `sublets/modals.php` (Tailwind)
- **Key functionality:** DataTable with server-side processing, status filter, add/edit via inline modals (not separate pages), details modal with job and supplier info, single and bulk delete. `add($id)` handles both add and edit form loading. `save()` handles both create and update.

### Org Settings
- **Status:** Complete — Tailwind
- **Controller:** `SettingsController`
- **Routes:** `/admin/settings` (GET), `/admin/settings/update` (POST)
- **Views:** `admin/settings/index.php` (Tailwind)
- **Model:** `OrgSettingsModel`
- **Key functionality:** Single-row org-wide configuration. Manages org name, logo (upload to `uploads/org/`), address, contact info, currency, currency symbol, VAT rate, default labor rate, invoice prefix, invoice due days, financial year start month. Pre-fills form, updates via `updateSettings()`.

### Invoices
- **Status:** Complete — Tailwind
- **Controller:** `InvoicesController`
- **Routes:** `/admin/invoices/*`
- **Views:** `admin/invoices/index.php` (Tailwind), `admin/invoices/view.php` (Tailwind)
- **Models:** `InvoiceModel`, `PaymentModel`
- **Key functionality:** Full invoice lifecycle. `generateFromJobCard()` sums parts (qty × unit_price_at_estimate), labor (generated `labor_cost`), and sublet costs (non-cancelled), applies VAT from org settings. Idempotent — returns existing invoice if one exists. Invoice view shows full breakdown with line items, totals, payment history, and inline payment form. Payments recorded with method (Cash, M-Pesa, Bank Transfer, Insurance, Credit). Each payment calls `updateAmountPaid()` to recalculate status. When invoice becomes Paid, job status auto-updates to Paid with history entry. Server-side DataTable processing. Overdue marking (`markOverdue`).

### Inventory
- **Status:** Complete — Tailwind
- **Controller:** `InventoryController`
- **Routes:** `/admin/inventory/*` (admin group); `/mechanic/inventory/search` (mechanic group)
- **Views:** `admin/inventory/index.php` (Tailwind with inline modals), `admin/inventory/form.php` (Tailwind, inline modal)
- **Key functionality:** DataTable with server-side processing (`load()`), add/edit via inline modals (not separate pages), delete with referential integrity check (cannot delete if referenced in `job_card_parts_required`), `fetch($id)` JSON endpoint, `search()` endpoint used by mechanic diagnosis form. Mechanics access search via `/mechanic/inventory/search`. Stock tracking fields (is_stocked, quantity_in_hand, reorder_level, unit) with stock status badges (In Stock, Low Stock, Out of Stock, Catalog Only). Low stock alert on dashboard and inventory page.

### Suppliers
- **Status:** Complete — Tailwind
- **Controller:** `SuppliersController`
- **Routes:** `/admin/suppliers/*` (admin group)
- **Views:** `admin/suppliers/index.php` (Tailwind with inline modals), `admin/suppliers/form.php` (Tailwind, inline modal)
- **Key functionality:** DataTable with server-side processing (`load()`), add/edit via inline modals (not separate pages), delete with referential integrity check (cannot delete if referenced in sublets), `getAll()` JSON endpoint (`suppliers/all`) used by sublets form dropdown.

### LPOs
- **Status:** Complete — Tailwind
- **Controller:** `LpoController`
- **Routes:** `/admin/lpos/*` (admin group)
- **Models:** `LpoModel`, `LpoItemModel`
- **Views:** `admin/lpos/index.php` (Tailwind), `admin/lpos/form.php` (Tailwind), `admin/lpos/view.php` (Tailwind), `admin/lpos/receive.php` (Tailwind)
- **Key functionality:** Full LPO lifecycle — create/edit (Draft), send (Sent), receive items (Partially Received → Received can increment inventory stock), cancel. DataTable listing with server-side processing. Line items with inventory search + stock status display. LPO number auto-generation (LPO-YYYYMM-NNN). Status transitions: Draft→Sent, Draft→Cancelled, Sent→Partially Received, Sent→Received, Sent→Cancelled, Partially Received→Received, Partially Received→Cancelled. All operations wrapped in DB transactions. Receive flow auto-increments stocked inventory quantities. Dashboard pending LPO count from real data.

### Petty Cash
- **Status:** Complete — Tailwind
- **Controller:** `PettyCashController`
- **Routes:** `/admin/pettycash/*` (admin group)
- **Views:** `admin/petty_cash/index.php` (Tailwind), `admin/petty_cash/form.php` (Tailwind), `admin/petty_cash/ledger.php` (Tailwind)
- **Key functionality:** Track day-to-day income/expense transactions. Summary cards (Total Income, Total Expenses, Current Balance). DataTable listing with server-side processing, add/edit forms, delete, date range filter (`pettycash/filter`) with AJAX summary update, category breakdown table, running balance ledger with print support. Running balance calculated in PHP, not DB.

### Reports
- **Status:** Complete — Tailwind
- **Controller:** `ReportsController`
- **Routes:** `/admin/reports/*` (7 routes under admin group)
- **Views:** `admin/reports/index.php` (Tailwind), `admin/reports/financial.php` (Tailwind), `admin/reports/operational.php` (Tailwind), `admin/reports/inventory.php` (Tailwind), `admin/reports/customers.php` (Tailwind), `admin/reports/staff.php` (Tailwind)
- **Key functionality:** Read-only aggregation and visualization. Financial reports (revenue trends, payment methods, outstanding invoices, aging, petty cash, LPO spend, discount tracking). Operational reports (jobs by status, completed per period, turnaround time, mechanic performance, overdue jobs, diagnosis categories, sublet spend). Inventory reports (stock levels, low stock alerts, most used parts, inventory value, parts spend per job). Customer reports (top customers by revenue, visit frequency, outstanding balances, new customers per month). Staff reports (advisor performance, activity log). CSV export for all 5 categories via `admin/reports/export/{category}/csv`.

### Profile Page
- **Status:** Complete — Tailwind
- **Controller:** `ProfileController`
- **Routes:** `/admin/profile` (GET), `/admin/profile/update` (POST) — filter: `auth:admin,receptionist,mechanic`
- **Views:** `admin/profile.php` (Tailwind)
- **Key functionality:** Two-card layout — profile info (first name, last name, email, phone, read-only company_id + role, avatar upload) + password change (current password, new password, confirm). Sidebar user name links to profile.

### Forgot Password
- **Status:** Stub (no email delivery)
- **Controller:** `ForgotPasswordController`
- **Routes:** `GET /forgot-password`, `POST /forgot-password/send`, `GET /reset-password/(:any)`, `POST /reset-password/process`
- **Views:** `forgot_password.php` (Tailwind), `reset_password.php` (Tailwind)
- **Key functionality:** User enters company_id, system looks up user, generates base64-encoded reset token, logs reset URL (no actual email sent). Reset page allows setting new password.

### Global Search
- **Status:** Complete
- **Controller:** `SearchController`
- **Route:** `GET /admin/search?q=...` (filter: auth:admin)
- **Key functionality:** Searches `job_cards.job_no`, `customers.name`/`customers.phone`, `vehicles.registration_number`/`vin`/`chassis_number`. Returns JSON with `url`, `icon`, `label`, `subtext`. Triggered from the global search bar in the topbar (dashboard only, centered, 300ms debounce).

### Customer Portal
- **Status:** Complete
- **Controller:** `CustomerPortal`
- **Routes:** `/customer/*` (filter: auth:customer)
- **Views:** `customer/layout.php` (Tailwind), `customer/dashboard.php` (Tailwind), `customer/jobs.php` (Tailwind), `customer/invoices.php` (Tailwind), `customer/invoice_view.php` (Tailwind)
- **Key functionality:** Customer login stores `customer_id` in session. Dashboard shows active/completed jobs count, outstanding invoices count, recent jobs. Jobs page lists all customer jobs with vehicle info. Invoices page lists all invoices with balance/status. Invoice detail shows full breakdown. Separate customer layout (no admin sidebar).

---

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

**Note:** The `job_cards.job_status` column has a DB default of `Awaiting Diagnosis`, but the actual status at creation time is set programmatically in `JobIntake::create_job_card()` — `Awaiting Diagnosis` if a mechanic is assigned at intake, `Awaiting Assignment` otherwise.

**On Hold** and **Rework** are non-terminal states accessible from multiple statuses. **Cancelled** is a terminal state accessible from most active statuses.

**Notes:**
- All status transitions are enforced server-side via `app/Config/JobStatus.php`
- Every transition is logged in `job_status_history`
- Transition options are available as buttons in the admin job details modal and mechanic diagnosis page
- Invoice auto-generation happens when status moves to `Ready for Invoice` via `InvoicesController::generate()`

---

## 7. FINANCIAL FLOW

- **Invoice auto-generation:** When a job reaches `Ready for Invoice`, admin clicks "Generate Invoice" which calls `InvoiceModel::generateFromJobCard()`. This is idempotent — if an invoice already exists for the job card, it returns the existing one.
- **Invoice calculation:**
  - `parts_total` = SUM(quantity_required × unit_price_at_estimate) from `job_card_parts_required`
  - `labor_total` = SUM(labor_cost) from `job_card_labor_tasks` (labor_cost is a generated column: estimated_hours × rate_per_hour)
  - `sublet_total` = SUM(cost) from `sublets` where status != 'Cancelled'
  - `subtotal` = parts_total + labor_total + sublet_total
  - `vat_amount` = subtotal × (vat_rate / 100) — vat_rate from `org_setting('vat_rate', 16)`
  - `grand_total` = subtotal + vat_amount - discount
- **Invoice statuses:** Draft → Sent → Partially Paid → Paid → Overdue — or Cancelled
- **Balance due:** `invoices.balance_due` is a generated column — never set manually
- **Payments:** Recorded against an invoice via `InvoicesController::recordPayment()`. Each payment triggers `InvoiceModel::updateAmountPaid()` which recalculates `amount_paid` and adjusts `status` (Partially Paid / Paid). Fully transactional.
- **Auto job status update:** `InvoiceModel::updateAmountPaid()` now sets the associated job card status to `Paid` with a `job_status_history` entry when invoice becomes fully paid.
- **Revenue reporting:** Dashboard computes revenue from `payments.payment_date` (last 6 months) and outstanding balance from `invoices.balance_due` (non-Paid, non-Cancelled).

---

## 8. ROLES AND PERMISSIONS

| Role | Route Groups | Capabilities |
|------|-------------|--------------|
| **admin** | `/admin/*` (filter: `auth:admin`) | Full access to all modules — users, customers, vehicles, jobs, sublets, invoices, inventory, suppliers, LPOs, calendar, settings, reports, petty cash, global search. All status transitions available. |
| **receptionist** | `/receptionist/` (filter: `auth:receptionist`), `/job_intake/*` (filter: `auth:admin,receptionist`) | Own dashboard view. Can perform job intake (create customers, vehicles, job cards). No admin modules. Sidebar: Dashboard, Operations (Jobs, Vehicles, Calendar, Sublets). |
| **mechanic** | `/mechanic/*` (filter: `auth:mechanic`) | Own dashboard with assigned job stats. Can view assigned jobs, submit diagnosis (add parts/labor tasks), search inventory parts. Limited role-gated status transitions. Sidebar: Dashboard, Operations (Jobs, Vehicles, Calendar — no Sublets). |
| **customer** | `/customer/` (filter: `auth:customer`) | Customer portal with dashboard (active/completed job counts, outstanding invoices), jobs list, invoices list, invoice detail. Sidebar: none (separate layout). |

**AuthFilter behavior:** Route groups pass role arguments to the filter. If no arguments, filter only checks `isLoggedIn`. Invalid role redirects to `/unauthorized`. Multiple roles supported via comma: `auth:admin,receptionist`.

---

## 9. ROUTE PATTERNS

### Public (no auth filter)
```
GET  /                             -> LoginController::index
GET  /login                        -> LoginController::index
POST /login/auth                   -> LoginController::auth
GET  /logout                       -> LoginController::logout
GET  /unauthorized                 -> DashboardController::unauthorized
GET  /forgot-password              -> ForgotPasswordController::index
POST /forgot-password/send         -> ForgotPasswordController::sendResetLink
GET  /reset-password/(:any)        -> LoginController::resetPassword/$1
POST /reset-password/process       -> LoginController::processResetPassword
```

### User Registration Wizard (no auth)
```
GET  /user/add_step1               -> UsersController::addStep1
POST /user/add_step1               -> UsersController::add_step1
GET  /user/add_step2               -> UsersController::addStep2
POST /user/add_step2               -> UsersController::add_step2
GET  /user/add_step3               -> UsersController::addStep3
POST /user/add_step3               -> UsersController::addUserStep3
POST /user/addUserStep3            -> UsersController::addUserStep3
GET  /user/preview                 -> UsersController::preview
GET  /user/saveUser                -> UsersController::saveUser
GET  /user/getLastId               -> UsersController::getLastId
GET  /user/success                 -> UsersController::success
GET  /user/failure                 -> UsersController::failure
```

### Job Intake (filter: auth:admin,receptionist)
```
GET  /job_intake/                  -> JobIntake::index
GET  /job_intake/search            -> JobIntake::search
POST /job_intake/create_job_card   -> JobIntake::create_job_card
GET  /job_intake/create_job_card   -> JobIntake::create_job_card
```

### Admin (filter: auth:admin)
```
# Dashboard
GET    /admin/                              -> DashboardController::admin
GET    /admin/dashboard                     -> DashboardController::admin
GET    /admin/search                        -> SearchController::index

# Users
GET    /admin/users                         -> UsersController::index
GET    /admin/users/add                     -> UsersController::add
GET    /admin/users/(:num)                  -> UsersController::details/$1
GET    /admin/users/edit/(:num)             -> UsersController::edit/$1
POST   /admin/users/update/(:num)           -> UsersController::update/$1
GET    /admin/users/delete/(:num)           -> UsersController::delete/$1
POST   /admin/users/bulk_action             -> UsersController::bulk_action
GET    /admin/users/fetch/(:num)            -> UsersController::details/$1
GET    /admin/users/fetch                   -> UsersController::fetchUsers

# Vehicles
GET    /admin/vehicles                      -> VehicleController::index
GET    /admin/vehicles/fetch                -> VehicleController::fetchVehicles
GET    /admin/vehicles/fetch/(:num)         -> VehicleController::fetchVehicles
POST   /admin/vehicles/add                  -> VehicleController::add
GET    /admin/vehicles/edit/(:num)          -> VehicleController::edit/$1
POST   /admin/vehicles/store                -> VehicleController::store
POST   /admin/vehicles/update/(:num)        -> VehicleController::update/$1
POST   /admin/vehicles/delete/(:num)        -> VehicleController::delete/$1
GET    /admin/vehicles/details/(:num)       -> VehicleController::details/$1

# Jobs
GET    /admin/jobs                          -> JobsController::index
GET    /admin/jobs/fetch                    -> JobsController::fetchJobs
GET    /admin/jobs/add                      -> JobsController::add
GET    /admin/jobs/(:num)                   -> JobsController::details/$1
GET    /admin/jobs/view/(:num)              -> JobsController::view/$1
POST   /admin/jobs/update/(:num)            -> JobsController::update/$1
POST   /admin/jobs/delete/(:num)            -> JobsController::delete/$1
POST   /admin/jobs/assign_mechanic/(:num)   -> JobsController::assign_mechanic/$1
POST   /admin/jobs/update_status/(:num)     -> JobsController::update_status/$1
GET    /admin/jobs/status_history/(:num)    -> JobsController::status_history/$1

# Customers
GET    /admin/customers                     -> CustomersController::index
POST   /admin/customers/load                -> CustomersController::load
GET    /admin/customers/load                -> CustomersController::load
GET    /admin/customers/details/(:num)      -> CustomersController::details/$1
GET    /admin/customers/add                 -> CustomersController::add
GET    /admin/customers/edit/(:num)         -> CustomersController::edit/$1
POST   /admin/customers/store               -> CustomersController::store
POST   /admin/customers/update/(:num)       -> CustomersController::update/$1
POST   /admin/customers/bulk_action         -> CustomersController::bulk_action

# Calendar
GET    /admin/calendar                      -> CalendarController::index
GET    /admin/calendar/getEvents            -> CalendarController::getEvents
GET    /admin/calendar/addEvent             -> CalendarController::addEvent
POST   /admin/calendar/addEvent             -> CalendarController::addEvent
POST   /admin/calendar/updateEventDate      -> CalendarController::updateEventDate

# Settings
GET    /admin/settings                      -> SettingsController::index
POST   /admin/settings/update               -> SettingsController::update

# Profile (also accessible by receptionist, mechanic)
GET    /admin/profile                       -> ProfileController::index
POST   /admin/profile/update                -> ProfileController::update

# Invoices
GET    /admin/invoices                      -> InvoicesController::index
GET    /admin/invoices/load                 -> InvoicesController::load
GET    /admin/invoices/view/(:num)          -> InvoicesController::view/$1
GET    /admin/invoices/generate/(:num)      -> InvoicesController::generate/$1
POST   /admin/invoices/record_payment/(:num)-> InvoicesController::recordPayment/$1
GET    /admin/invoices/mark_overdue         -> InvoicesController::markOverdue

# Inventory
GET    /admin/inventory                     -> InventoryController::index
POST   /admin/inventory/load                -> InventoryController::load
GET    /admin/inventory/add                 -> InventoryController::add
POST   /admin/inventory/create              -> InventoryController::create
GET    /admin/inventory/edit/(:num)         -> InventoryController::edit/$1
POST   /admin/inventory/update/(:num)       -> InventoryController::update/$1
POST   /admin/inventory/delete/(:num)       -> InventoryController::delete/$1
GET    /admin/inventory/fetch/(:num)        -> InventoryController::fetch/$1
GET    /admin/inventory/search              -> InventoryController::search

# Suppliers
GET    /admin/suppliers                     -> SuppliersController::index
POST   /admin/suppliers/load                -> SuppliersController::load
GET    /admin/suppliers/add                 -> SuppliersController::add
POST   /admin/suppliers/create              -> SuppliersController::create
GET    /admin/suppliers/edit/(:num)         -> SuppliersController::edit/$1
POST   /admin/suppliers/update/(:num)       -> SuppliersController::update/$1
POST   /admin/suppliers/delete/(:num)       -> SuppliersController::delete/$1
GET    /admin/suppliers/all                 -> SuppliersController::getAll

# Sublets
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

# LPOs
GET    /admin/lpos                          -> LpoController::index
POST   /admin/lpos/load                     -> LpoController::load
GET    /admin/lpos/add                      -> LpoController::add
POST   /admin/lpos/create                   -> LpoController::create
GET    /admin/lpos/view/(:num)              -> LpoController::view/$1
GET    /admin/lpos/edit/(:num)              -> LpoController::edit/$1
POST   /admin/lpos/update/(:num)            -> LpoController::update/$1
POST   /admin/lpos/update_status/(:num)     -> LpoController::updateStatus/$1
GET    /admin/lpos/receive/(:num)           -> LpoController::receive/$1
POST   /admin/lpos/process_receive/(:num)   -> LpoController::processReceive/$1
POST   /admin/lpos/delete/(:num)            -> LpoController::delete/$1

# Reports
GET    /admin/reports                       -> ReportsController::index
GET    /admin/reports/financial             -> ReportsController::financial
GET    /admin/reports/operational           -> ReportsController::operational
GET    /admin/reports/inventory             -> ReportsController::inventory
GET    /admin/reports/customers             -> ReportsController::customers
GET    /admin/reports/staff                 -> ReportsController::staff
GET    /admin/reports/export/(:seg)/(:seg)  -> ReportsController::export/$1/$2

# Petty Cash
GET    /admin/pettycash                     -> PettyCashController::index
POST   /admin/pettycash/load                -> PettyCashController::load
GET    /admin/pettycash/add                 -> PettyCashController::add
POST   /admin/pettycash/create              -> PettyCashController::create
GET    /admin/pettycash/edit/(:num)         -> PettyCashController::edit/$1
POST   /admin/pettycash/update/(:num)       -> PettyCashController::update/$1
POST   /admin/pettycash/delete/(:num)       -> PettyCashController::delete/$1
GET    /admin/pettycash/ledger              -> PettyCashController::ledger
POST   /admin/pettycash/filter              -> PettyCashController::filter
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
GET  /mechanic/inventory/search      -> InventoryController::search
```

### Receptionist (filter: auth:receptionist)
```
GET /receptionist/              -> DashboardController::receptionist
GET /receptionist/dashboard     -> DashboardController::receptionist
```

### Customer (filter: auth:customer)
```
GET /customer/                   -> CustomerPortal::dashboard
GET /customer/dashboard          -> CustomerPortal::dashboard
GET /customer/jobs               -> CustomerPortal::jobs
GET /customer/invoices           -> CustomerPortal::invoices
GET /customer/invoice/(:num)     -> CustomerPortal::invoice/$1
```

---

## 10. MODAL RESTRUCTURE

- **Inventory:** Add/edit are now inline modals — `admin/inventory/index.php` loads `admin/inventory/form.php` via AJAX into `actionModal`. Route pages (`/admin/inventory/add`, `/admin/inventory/edit/:num`) still exist but are not linked from the UI.
- **Suppliers:** Add/edit are now inline modals — same pattern as Inventory.
- **Sublets:** Add/edit are now inline modals — `sublets/index.php` loads `sublets/form.php` via AJAX into `actionModal`.
- **Customers:** Add/edit are now inline modals — `customers/customers.php` loads `admin/forms/add_customer_form.php` or `edit_customer_form.php` via AJAX into `actionModal`.
- **Vehicles:** Detail modal uses AJAX; add/edit still has legacy Bootstrap form pages.
- **Jobs:** Detail modal is AJAX-loaded with tabs (Overview, Parts & Labor, Photos, LPOs & Invoice, Status History).
- **All modals** use vanilla JS `openModal()`/`closeModal()` — no Bootstrap JS dependency.

---

## 11. UI MIGRATION STATUS

### Tailwind — All views (79+ views migrated)

All views are now Tailwind. No Bootstrap CSS/JS is loaded from any view.

- `layouts/main.php` — Bootstrap removed, Tailwind layout, CSRF, topbar with search bar, notification bell, user dropdown
- `partials/sidebar.php` — Dark theme (`bg-slate-900`), role-based filtering, sectioned nav with SVG icons, user footer with profile link + logout
- `login.php` — Standalone centered Tailwind card, clean form with password toggle
- `admin/dashboard.php` — Full layout: stat cards, revenue chart (lazy-loaded Chart.js), doughnut chart, skeleton loading, empty state, activity timeline, upcoming events, alerts (low stock/LPO/overdue), quick actions, auto-refresh
- `customers/*` + `admin/forms/*` — DataTable + detail modal + add/edit modals (Tailwind with icons, section headers)
- `vehicles/*` — Tailwind DataTable + detail modal + add/edit forms
- `job/*` — Tailwind job intake + detail modal with tabs
- `sublets/*` — Full Tailwind (index, form, _details, modals)
- `admin/inventory/*` — Tailwind DataTable + inline modals
- `admin/suppliers/*` — Tailwind DataTable + inline modals
- `admin/invoices/*` — Tailwind (index, view)
- `admin/lpos/*` — Tailwind (index, form, view, receive)
- `admin/petty_cash/*` — Tailwind (index, form, ledger)
- `admin/settings/index.php` — Tailwind
- `admin/reports/*` — Tailwind (6 files)
- `admin/profile.php` — Tailwind
- `admin/users.php` + `admin/add_user.php` + `admin/edit_user.php` + `admin/users/user_list.php` — Tailwind
- `admin/jobs/jobs_list.php` — Tailwind DataTable markup
- `calendar/*` — Tailwind
- `forgot_password.php`, `reset_password.php` — Tailwind
- `customer/*` — Tailwind (5 files)
- `user/*` — Tailwind (add_step1, add_step2, add_step3, preview, success, failure, getLastId)
- `jobs/add.php` — Tailwind (legacy `jobs` table)
- `job_intake_form.php` — Tailwind
- `pagination/tailwind_pagination.php`
- Error views (all Tailwind)

No Bootstrap views remain. 14 previously Bootstrap views converted to Tailwind:
`user/add_step1.php`, `user/add_step2.php`, `user/add_step3.php`, `user/preview.php`, `user/success.php`, `user/failure.php`, `user/getLastId.php`, `job_intake_form.php`, `vehicles/add.php`, `vehicles/edit.php`, `jobs/add.php`, `admin/jobs/jobs_list.php`, `pagination/bootstrap_pagination.php` (rewritten to Tailwind), and `jobs/edit.php` (did not exist — route/controller method never created).

---

## 12. JS CONVENTIONS

- **All JS functions used as DataTable inline onclick handlers must be `window.`-scoped** — Views assign `window.openModal = function(url, title) {...}` or define functions directly on `window` to make them accessible from DataTable-rendered HTML.
- **`openModal(url, title)` / `closeModal(id)`** — Defined globally in `layouts/main.php` or individual view modals. Loads URL into modal via AJAX, handles loading spinner.
- **`BASE_URL`** — Defined globally in `layouts/main.php` as `var BASE_URL = '<?= base_url() ?>'`.
- **`getCsrfMeta()`** — Returns `{name, hash}` object from meta tags, used by `$.ajaxSetup` for all AJAX POSTs.
- **CSRF auto-refresh** — `$.ajaxSetup` injects CSRF token into POST/PUT/DELETE and refreshes from `X-CSRF-TOKEN` response header.
- **Asset paths** — Always use `base_url('public/assets/...')` not `base_url('assets/...')`.

### Asset files in use (`public/assets/js/`)

| File | Purpose |
|------|---------|
| `vehicles.js` | Vehicle DataTable, detail modal, form handling |
| `job_intake.js` | Job intake form, customer/vehicle search |
| `customers.js` | Customer DataTable, detail modal, bulk actions |
| `calendar.js` | FullCalendar integration, event CRUD |
| `sublets.js` | Sublet DataTable, modals, form handling |
| `inventory.js` | Inventory DataTable, search, stock display |
| `suppliers.js` | Supplier DataTable, modals |
| `datatable-config.js` | Global DataTables defaults (language, styling) |
| `dashboard-refresh.js` | Auto-refresh dashboard every 30s |
| `notifications.js` | Notification bell polling (stubbed) |
| `global-search.js` | Global search bar with debounced AJAX |

---

## 13. CODING CONVENTIONS

- **Controllers** extend `BaseController` (extends `CodeIgniter\Controller`).
- **Models** extend `CodeIgniter\Model` with `$allowedFields`, `$returnType = 'array'`, `$useSoftDeletes` and `$useTimestamps` as appropriate.
- **All DB access via Models** — no `$db->table()` queries unless doing complex aggregations/joins across tables (ReportsController).
- **AJAX responses** use `$this->respond()` (via `CodeIgniter\API\ResponseTrait`) or `$this->response->setJSON()`.
- **DataTables server-side processing** uses the `load()` pattern with `draw`, `start`, `length`, `search`, `order` parameters and responds with `recordsTotal`, `recordsFiltered`, `data`.
- **Pagination** uses CI4's `$model->paginate(10)` and passes `$model->pager` to view.
- **Views with sidebar** use `$this->extend('layouts/main')` + `$this->section('content')`. Standalone views (login, forgot_password, user wizard) do not.
- **File uploads** use `$file->getRandomName()` + `$file->move()` to `uploads/{subfolder}/`. Validated by MIME type + extension. Upload directories have `.htaccess` blocking script execution.
- **Soft deletes** use `deleted_at` datetime column on `users` and `job_cards`. `customers` does not have soft-delete.
- **CSRF** globally enabled — every POST form must include `<?= csrf_field() ?>`. AJAX POSTs use `getCsrfMeta()` JS function from main layout which reads meta tags and refreshes token from response header.
- **No CSRF exemption** configured for any route.
- **Generated columns** (`users.name`, `job_card_labor_tasks.labor_cost`, `invoices.balance_due`, `lpo_items.line_total`) — never include in `$allowedFields`.
- **`org_setting($key, $default)`** available globally via `settings_helper.php` — calls `OrgSettingsModel::getSettings()` once per request (static cache).
- **`log_activity($action, $entity_type, $entity_id, $description)`** available globally via `settings_helper.php` — logs to `activity_log` table with current user and IP. Skips silently if no user session.
- **ReportsController is read-only** — all methods only run SELECT queries. No inserts or updates in ReportsController.
- **CSV export** via `admin/reports/export/{report}/csv` — direct output, no view file. Available for all 5 report categories.
- **Asset paths must include `public/`** — Always use `base_url('public/assets/...')` not `base_url('assets/...')` to reference CSS/JS assets.
- **Tailwind CLI rebuild required** — After adding new utility classes to any view, the Tailwind CSS must be rebuilt: `.\tailwindcss.exe -i public\assets\css\input.css -o public\assets\css\tailwind.css --minify`

---

## 14. GOTCHAS

1. **`job_cards` is the real transactional table** — the `jobs` table is legacy/unused. Never write to `jobs`.
2. **Always `UsersController`** (with 's'), not `UserController`.
3. **Always `App\Controllers\CustomersController`**, not `Admin\CustomersController` — there is no `Admin\` subdirectory in Controllers.
4. **Running balance in PettyCashModel::getRunningBalance() is calculated in PHP** — never add a running_balance column to the DB.
5. **`registration_number`** is the correct `vehicles` column (not `vehicle_number`).
6. **`baseURL`** (`app/Config/App.php`) must be `http://localhost/FlowDesk/` and **`.htaccess RewriteBase`** must be `/FlowDesk/`. Both must match. Change both when deploying.
7. **Database name is `flowdesk`** (lowercase) — set in `.env` as `database.default.database = flowdesk`. Overrides value in `app/Config/Database.php`.
8. **`users.name` is a STORED GENERATED column** — never write to it, never include in `$allowedFields` of `UserModel`.
9. **`job_card_labor_tasks.labor_cost` is a STORED GENERATED column** — never set it manually.
10. **`invoices.balance_due` is a STORED GENERATED column** — never set it manually.
11. **`org_settings` always has exactly one row (id=1)** — use `OrgSettingsModel::getSettings()` and `updateSettings()` only.
12. **`InvoiceModel::updateAmountPaid()`** must be called after every payment insert to keep `amount_paid` and `status` in sync.
13. **`InvoiceModel::generateFromJobCard()` is idempotent** — safe to call multiple times, returns existing invoice if one exists.
14. **CSRF meta tags are in `layouts/main.php` head** — `getCsrfMeta()` reads `csrf-name` and `csrf-token` for all AJAX POSTs. Token auto-refreshes from response header.
15. **No auto-routing** — every route is explicitly defined in `Routes.php`. Adding a new controller method requires a corresponding route entry.
16. **`job_cards.job_status` DB default is `Awaiting Diagnosis`** — but the status at creation is set programmatically: `Awaiting Diagnosis` if mechanic assigned, `Awaiting Assignment` otherwise.
17. **`completed_at` on `job_cards`** is set ONCE on the first transition to `Completed`. If a job cycles Completed → Rework → Completed again, `completed_at` is NOT overwritten.
18. **`diagnosis_category` on `job_cards`** is optional — used for structured job type reporting. Set via the mechanic diagnosis form dropdown.
19. **`CustomerModel` now has `$useTimestamps = true`** with `createdField = 'created_at'` and `updatedField = ''` (no updated_at column).
20. **`InvoiceModel::generateFromJobCard()` now accepts optional `$discount`** (float, default 0.00).
21. **`log_activity()`** is a global helper in `settings_helper.php`. Logged actions include: `status_change`, `payment_recorded`, `lpo_created`, `lpo_received`, `job_created`, `diagnosis_saved`, `user_created`, `petty_cash_entry`.
22. **Stock is decremented on diagnosis save** — `InventoryModel::decrementStock()` is called in `JobIntake::save_diagnosis()` for each part where `is_stocked = 1`.
23. **`CustomersController::index()` no longer has an AJAX branch** — DataTables server-side processing uses the `load()` method exclusively, which returns JSON directly.
24. **Asset paths must use `base_url('public/assets/...')` not `base_url('assets/...')`** — The `public/` segment is required in the URL because CI4's `base_url()` points to the project root.
25. **JS functions must be `window.`-scoped for DataTable inline click handlers** — Views assign `window.openModal = openModal` or define functions directly on `window` to make them accessible from DataTable-rendered HTML.
26. **Tailwind CLI must be re-run after adding new utility classes** — Run `.\tailwindcss.exe -i public\assets\css\input.css -o public\assets\css\tailwind.css --minify` to regenerate. You can also use `npm run watch:css` for auto-rebuild.
27. **Inventory/Suppliers/Sublets add/edit are now modals** — The standalone form routes still exist but are not linked from the UI.
28. **Job Intake is the only entry point for new job cards** — The "Add New Job" button has been removed from the admin jobs list.
29. **`uploads/org/` directory exists** — Created with `.gitkeep`. Org logo uploads work.
30. **Status colors use Bootstrap hex values** — `#007bff`, `#6c757d`, `#dc3545`, etc. Not migrated to Tailwind color tokens (stored in `JobStatus.php` config).
31. **`public/css/` contains 9 orphaned CSS files** — Legacy per-module Bootstrap stylesheets. Safe to delete.
32. **Forgot password does not send email** — Reset link is only logged. No mail transport configured.
33. **Chart.js is lazy-loaded** — Not included in the layout head. Dashboard views must call `loadChartJS().then(function(Chart) { ... })` instead of `new Chart(...)` directly.
34. **Calendar drag-drop implemented** — `CalendarController::updateEventDate()` handles `cal-*`, `job-in-*`, `job-est-*`, and `job-comp-*` event ID prefixes. Dragging job events updates `job_cards.date_in`/`time_in` or `end_date` accordingly.

---

## 15. PENDING / NOT BUILT

- **Forgot password email delivery** — Controller exists, routes exist, but no mail transport configured. Reset link is only logged.
- **Profile page for customers** — Admin/receptionist/mechanic profile exists. Customer has no profile page.
- **Customer portal enhancements** — Dashboard, jobs, invoices exist. No job tracking, no feedback, no communication features.

---

## 16. PAYMENTS HUB — PHASE A COMPLETE

### New Table: `receipts`
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| receipt_no | varchar(30) | NOT NULL, UNIQUE, format: RCT-YYYYMM-001 |
| payment_id | int(10) unsigned | FK → payments.id, ON DELETE CASCADE, UNIQUE |
| invoice_id | int(10) unsigned | FK → invoices.id |
| customer_id | int(10) unsigned | FK → customers.id |
| job_card_id | int(10) unsigned | FK → job_cards.id |
| receipt_date | date | NOT NULL |
| amount_paid | decimal(12,2) | NOT NULL |
| payment_method | varchar(30) | NOT NULL |
| reference_no | varchar(100) | NULL |
| balance_after | decimal(12,2) | NOT NULL, default 0.00 |
| org_name | varchar(255) | NOT NULL — snapshot at issuance |
| org_address | text | NULL — snapshot |
| org_phone | varchar(20) | NULL — snapshot |
| org_email | varchar(255) | NULL — snapshot |
| org_logo | varchar(255) | NULL — snapshot |
| vat_rate | decimal(5,2) | NOT NULL, default 16.00 — snapshot |
| parts_total | decimal(12,2) | NOT NULL |
| labor_total | decimal(12,2) | NOT NULL |
| sublet_total | decimal(12,2) | NOT NULL |
| subtotal | decimal(12,2) | NOT NULL |
| vat_amount | decimal(12,2) | NOT NULL |
| grand_total | decimal(12,2) | NOT NULL |
| amount_in_words | varchar(500) | NULL |
| received_by_name | varchar(101) | NOT NULL — snapshot of user name |
| notes | text | NULL |
| generated_by | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

**FKs:** fk_receipt_payment, fk_receipt_invoice, fk_receipt_customer, fk_receipt_job_card, fk_receipt_generated_by

**Design rationale:** Org settings (name, address, phone, email, logo, vat_rate) are snapshotted at time of receipt issuance. If org settings are later updated, existing receipts retain the values that were in effect when they were generated. This makes receipts immutable audit records.

### New Model: `ReceiptModel` (`app/Models/ReceiptModel.php`)
- `generateReceiptNo(): string` — next RCT-YYYYMM-NNN
- `generateFromPayment(int $payment_id, int $generated_by): int` — idempotent; creates receipt snapshot from payment + invoice + org_settings; returns receipt_id
- `getWithDetails($id): array` — single receipt with customer_name, customer_phone, job_no, registration_number, invoice_no
- `getByInvoice(int $invoice_id): array` — all receipts for an invoice
- `getByPaymentId(int $payment_id): ?array` — receipt for a specific payment

### New Helper: `number_to_words()`
- **File:** `app/Helpers/settings_helper.php`
- **Signature:** `number_to_words(float $amount, string $currency = 'Kenya Shillings'): string`
- **Output:** `"KENYA SHILLINGS FIVE THOUSAND ONLY"` or `"KENYA SHILLINGS FIVE THOUSAND AND 50/100 ONLY"`
- **Range:** Supports up to millions

### New Controller: `PaymentsController` (`app/Controllers/PaymentsController.php`)
- `index()` — Unified payments listing with summary stats (total received, transactions, outstanding, avg payment) + payment method breakdown cards
- `load()` — DataTables server-side endpoint with joins to invoices, customers, job_cards, users, receipts; supports search by customer, invoice, job, reference; supports date range filter via GET params
- `filter()` — AJAX endpoint for date-range-filtered summary cards and method breakdown

### New View: `admin/payments/index.php`
- Extends `layouts/main`
- Summary cards row (4 cards)
- Payment method breakdown (5 mini cards: Cash, M-Pesa, Bank Transfer, Insurance, Credit)
- Date range filter form
- DataTable with columns: Date, Customer, Invoice, Job No, Amount, Method, Reference, Received By, Receipt (Print/Generate), Actions (View Invoice)

### New View: `admin/invoices/receipt.php`
- Standalone (no layout wrapper) — print-optimized
- Print and Close buttons (hidden on print via `@media print`)
- Full receipt layout: org header, receipt meta, customer info, payment details with amount-in-words, invoice summary table, received-by footer

### Updated Controller: `InvoicesController`
- `recordPayment()` now auto-generates a receipt after successful payment via `ReceiptModel::generateFromPayment()`. The receipt_id is stored in the session flash and a "Print Receipt" button appears in the success message.
- `printReceipt($receipt_id)` — renders the standalone receipt view
- `generateReceipt($payment_id)` — generates a receipt on demand for older payments that don't have one yet

### Updated View: `admin/invoices/view.php`
- Success flash now shows a "Print Receipt" button when receipt_id is in flash data
- Payment history table now includes a "Receipt" column with Print (if receipt exists) or Generate (if not) buttons

### Routes Added
```
# Inside admin group (auth:admin):
GET  admin/payments                    -> PaymentsController::index
GET  admin/payments/load               -> PaymentsController::load
POST admin/payments/filter             -> PaymentsController::filter
GET  admin/invoices/receipt/(:num)     -> InvoicesController::printReceipt/$1
GET  admin/invoices/generate_receipt/(:num) -> InvoicesController::generateReceipt/$1
```

### Sidebar Update
The Finance section in `app/Views/partials/sidebar.php` now includes a Payments entry between Invoices and Petty Cash:
- Invoices → **Payments** → Petty Cash

### Financial Flow (Post-Phase A)
```
Job Card → Diagnosis → Ready for Invoice → Generate Invoice →
Record Payment → Receipt Auto-Generated → Print Receipt
                                              ↓
                                      View Payments (admin/payments)
                                      unified listing across all invoices
```

### Payments Hub Phases
- **Phase A (DONE):** Receipts infrastructure, unified payments listing, receipt generation
- **Phase B (PENDING):** Accounts table (bank, M-Pesa, cash tracking)
- **Phase C (PENDING):** Expenses table (overhead beyond petty cash)
- **Phase D (PENDING):** Supplier payments (accounts payable)
- **Phase E (PENDING):** Credit notes and refunds

### Gotchas (Receipts)
1. **Org settings are snapshots** — receipts.org_name, org_address, etc. are captured at issuance time. Never regenerate receipt from current org_settings.
2. **generateFromPayment() is idempotent** — calling it a second time for the same payment_id returns the existing receipt_id without creating a duplicate. This is safe.
3. **Unique constraint on payment_id** — the `uk_receipt_payment` unique key enforces one receipt per payment. This is enforced at the DB level.
4. **Standalone receipt view** — `admin/invoices/receipt.php` is a standalone HTML page (no `$this->extend('layouts/main')` wrapper) — it has its own `<html>`, `<head>`, and `<body>` tags optimized for `@media print`.
5. **balance_after calculation** — calculated as `invoice.grand_total - invoice.amount_paid` at the moment of receipt generation. This represents the remaining balance after the current payment is applied.
