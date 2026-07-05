# AGENTS.md — FlowDesk Organizational Management System

## 1. PROJECT OVERVIEW

FlowDesk is a modular, single-org deployment management system for small businesses — a garage/shop management system for tracking job cards, vehicles, customers, users, sublets, and calendar events. Built as a single CodeIgniter 4 application with role-based dashboards.

**Stack:**
- Framework: CodeIgniter 4 (v4.x, PHP 8.2+)
- Database: MySQL 5.7+ / MariaDB (`flowdesk`)
- Frontend: Bootstrap 5.3, jQuery 3.6, DataTables 1.13, FullCalendar 6.1, Chart.js, SweetAlert2, Select2, Font Awesome / Bootstrap Icons
- Web server: Apache with mod_rewrite (XAMPP)

**Local dev URL:** `http://localhost/FlowDesk/`
**Local path:** `C:\xampp\htdocs\FlowDesk`
**GitHub:** `https://github.com/Austine904/FlowDesk`

## 2. ARCHITECTURE

```
C:\xampp\htdocs\FlowDesk\
├── app/
│   ├── Config/          # CI4 config files (App, Database, Routes, Filters, etc.)
│   ├── Controllers/     # 11 controllers (no Admin/ subdirectory)
│   ├── Database/
│   │   ├── Migrations/  # EMPTY — no migration files exist
│   │   └── Seeds/       # EMPTY — no seed files exist
│   ├── Filters/
│   │   └── AuthFilter.php
│   └── Views/
│       ├── admin/       # dashboard.php, users.php, add_user.php, users/user_list.php
│       ├── calendar/    # calendar.php, modals.php
│       ├── customers/   # customers.php, modals.php
│       ├── errors/      # unauthorized.php, html/ and cli/ error pages
│       ├── job/         # index.php, modals.php
│       ├── jobs/        # add.php, edit.php
│       ├── layouts/     # main.php
│       ├── partials/    # sidebar.php
│       ├── sublets/     # index.php, form.php, _details.php, modals.php
│       ├── user/        # add_step1/2/3.php, preview.php, success.php, getLastId.php
│       ├── vehicles/    # index.php, add.php, edit.php, modals.php
│       ├── mechanic/    # jobs.php (mechanic job list)
│       └── *.php        # login.php, dashboard variants (receptionist, mechanic, customer), welcome_message.php
├── public/
│   ├── assets/js/       # vehicles.js, job_intake.js, customers.js, calendar.js, sublets.js
│   └── css/             # Per-module CSS files
├── uploads/             # User-uploaded files (profile pictures, job card photos) — gitignored
├── vendor/              # Composer dependencies
├── writable/            # CI4 cache, logs, sessions
├── .htaccess            # RewriteBase /FlowDesk/
└── index.php            # Front controller
```

**Routing:** Defined in `app/Config/Routes.php`. Explicit routes only (no auto-routing). Route groups use the `filter` option for auth. Public routes are top-level. Protected routes are grouped by prefix (`admin`, `job_intake`, `mechanic`, `receptionist`, `customer`).

**Auth:** `AuthFilter` (`app/Filters/AuthFilter.php`) checks `session()->get('isLoggedIn')` and, if role arguments are provided on the filter, validates `session()->get('role')` is in the allowed list. Some controller methods also duplicate this check manually. Login writes these session keys: `user_id`, `user_name`, `role`, `company_id`, `profile_picture`, `isLoggedIn`. Logout destroys the session and redirects to `/login`.

**Views:** Most views extend `layouts/main` using `$this->extend('layouts/main')` with `$this->section('content')`. The layout includes the sidebar (`partials/sidebar.php`), Bootstrap, DataTables, and other global assets. Standalone views (e.g. `admin/add_user.php`, the user registration wizard) do not extend the layout.

**No models.** All database access uses `$db->table('table_name')->...` query builder calls or raw `$db->query('SELECT ...')` directly in controllers.

**CSRF:** Globally enabled in `app/Config/Filters.php` (`$globals['before']` includes `'csrf'`). Every POST form must include `<?= csrf_field() ?>`. AJAX POST requests must pass the CSRF token.

**File uploads:** User-uploaded files go to `uploads/users/` (profile pictures) and `uploads/job_card_photos/` (job card photos). The `uploads/` directory is gitignored (contains `.gitkeep`).

## 3. DATABASE

**Database name:** `flowdesk` (lowercase). No migrations exist — the schema was created manually and lives in the database only.

### Tables

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
| role | enum('admin','mechanic','receptionist','customer') | NOT NULL, INDEX, default 'mechanic' |
| profile_picture | varchar(255) | NULL |
| date_of_employment | date | NULL |
| dob | date | NULL |
| national_id | varchar(50) | NULL |
| gender | enum('Male','Female','Other') | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |
| updated_at | datetime | NULL, on update current_timestamp() |
| deleted_at | datetime | NULL, INDEX (soft delete) |

#### next_of_kin
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| user_id | int(10) unsigned | FK → users.id |
| kin_first_name | varchar(50) | NOT NULL |
| kin_last_name | varchar(50) | NOT NULL |
| relationship | varchar(50) | NOT NULL |
| kin_phone_number | varchar(20) | NOT NULL |

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
| status | varchar(50) | NOT NULL, INDEX, default 'On Job' |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

#### customers
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(100) | NOT NULL, INDEX |
| phone | varchar(20) | NOT NULL, UNIQUE |
| email | varchar(255) | NULL |
| address | text | NULL |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

**Note:** `customers` has no `deleted_at` column — no soft-delete support yet.

#### job_cards (the real transactional jobs table)
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| job_no | varchar(30) | NOT NULL, UNIQUE |
| customer_id | int(10) unsigned | FK → customers.id |
| vehicle_id | int(10) unsigned | FK → vehicles.id |
| date_in | date | NOT NULL, INDEX |
| time_in | time | NULL |
| start_date | date | NULL |
| end_date | date | NULL |
| diagnosis | text | NULL |
| initial_damage_notes | text | NULL |
| job_status | varchar(30) | NOT NULL, INDEX, default 'Awaiting Assignment' |
| mileage_in | int(11) | NULL |
| fuel_level | enum('Empty','1/4','1/2','3/4','Full') | NULL |
| estimated_labor_hours | decimal(8,2) | NULL |
| assigned_service_advisor_id | int(10) unsigned | FK → users.id |
| assigned_mechanic_id | int(10) unsigned | NULL, FK → users.id, INDEX idx_jc_mechanic |
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
| notes | text | NULL |

#### inventory
| Column | Type | Notes |
|--------|------|-------|
| id | int(10) unsigned | PK, auto_increment |
| name | varchar(255) | NOT NULL, INDEX |
| part_number | varchar(100) | NULL, INDEX |
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
| status | enum('Pending','In Progress','Completed','Invoiced','Paid','Cancelled') | NOT NULL, INDEX, default 'Pending' |
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
| start_time | datetime | NOT NULL, INDEX |
| end_time | datetime | NULL |
| all_day | tinyint(1) | NOT NULL, default 0 |
| event_type | varchar(50) | NOT NULL, default 'general' |
| color | varchar(7) | NULL, default '#007bff' |
| related_table | varchar(50) | NULL, INDEX |
| related_id | int(10) unsigned | NULL |
| priority | enum('low','medium','high') | NOT NULL, default 'medium' |
| created_by_user_id | int(10) unsigned | FK → users.id |
| created_at | datetime | NOT NULL, DEFAULT current_timestamp() |

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

**Note:** The `jobs` table exists alongside `job_cards` but is legacy/unused. All current operations use `job_cards`. Do not write to the `jobs` table.

### FK Relationships

| Source | Target | Constraint |
|--------|--------|------------|
| next_of_kin.user_id | users.id | fk_nok_user |
| vehicles.owner_id | customers.id | fk_vehicles_owner |
| job_cards.customer_id | customers.id | fk_jc_customer |
| job_cards.vehicle_id | vehicles.id | fk_jc_vehicle |
| job_cards.assigned_service_advisor_id | users.id | fk_jc_advisor |
| job_cards.assigned_mechanic_id | users.id | fk_jc_mechanic |
| job_card_photos.job_card_id | job_cards.id | fk_jcp_job_card |
| job_card_parts_required.job_card_id | job_cards.id | fk_jcpr_job_card |
| job_card_parts_required.inventory_id | inventory.id | fk_jcpr_inventory |
| job_card_labor_tasks.job_card_id | job_cards.id | fk_jclt_job_card |
| sublets.job_card_id | job_cards.id | fk_sublets_job_card |
| sublets.sublet_provider_id | suppliers.id | fk_sublets_provider |
| calendar_events.created_by_user_id | users.id | fk_ce_created_by |
| jobs.assigned_to | users.id | fk_jobs_assigned (legacy) |

## 4. MODULES

### Auth (Login/Logout)
- **Status:** Complete
- **Controllers:** `LoginController`
- **Views:** `login.php`
- **What it does:** Login with `company_id` + `password`, session creation, role-based redirect, logout/destroy. Session keys: `user_id`, `user_name`, `role`, `company_id`, `profile_picture`, `isLoggedIn`.
- **Limitations:** Customer login has no dedicated post-login destination — `restrictTo('customer', 'customer_dashboard')` loads a standalone view, but no customer-specific functionality exists beyond that.

### Dashboard
- **Status:** Complete
- **Controllers:** `DashboardController`
- **Views:** `admin/dashboard.php`
- **What it does:** Admin dashboard with summary cards (users, vehicles, jobs in progress, pending LPOs), job status doughnut chart, revenue line chart, recent activity feed, quick action buttons. Separate dashboard views exist for `receptionist`, `mechanic`, and `customer` roles.
- **Limitations:** Revenue chart uses hardcoded mock data. Critical alerts section is static HTML.

### Users
- **Status:** Complete
- **Controllers:** `UsersController`
- **Views:** `admin/users.php`, `admin/add_user.php`, `admin/edit_user.php`, `admin/users/user_list.php`, plus user registration wizard views (`user/add_step1.php`, `add_step2.php`, `add_step3.php`, `preview.php`, `success.php`)
- **What it does:** CRUD with DataTable listing, multi-step add wizard, edit, soft-delete, bulk delete via AJAX. Role-based filters, `fetchUsers` JSON endpoint. Public registration wizard (multi-step form with no auth required).
- **Limitations:** Inconsistent session access — `__construct()` sets `$this->session` but `index()` and `add()` use `session()->` directly. Route `user/submit` references `UsersController::submit()` which does not exist.

### Vehicles
- **Status:** Partial
- **Controllers:** `VehicleController`
- **Views:** `vehicles/index.php`, `vehicles/add.php`, `vehicles/edit.php`, `vehicles/modals.php`
- **What it does:** DataTable listing with AJAX fetch, add/store, edit, delete, details JSON endpoint.
- **Limitations:** `index()` does not extend `layouts/main` (no sidebar/menu). Route has typo: `vechicles/edit/(:num)` (missing 'h'). `add()` returns a view and `store()` saves via AJAX — separate flow.

### Customers
- **Status:** Partial
- **Controllers:** `CustomersController`
- **Views:** `customers/customers.php`, `customers/modals.php`, `admin/forms/add_customer_form.php`, `admin/forms/edit_customer_form.php`
- **What it does:** DataTable listing with server-side processing (`load()`), details modal with vehicles and jobs, add/edit form views, bulk delete with transaction.
- **Limitations:** No `store()` or `update()` routes exist — forms cannot submit. No soft-delete column on the table.

### Job Intake
- **Status:** Complete
- **Controllers:** `JobIntake`
- **Views:** `job/index.php`, `job/modals.php`, `mechanic_diagnosis_form.php`
- **What it does:** Search customers/vehicles, create new customers+vehicles inline, create job card with photo uploads, validation, transaction safety, unique job number generation (`JOB-YYYYMMDD-NNN`). Includes mechanic diagnosis view, parts/tasks assignment, inventory search, dispatch mechanic assignment. Job status defaults to `Awaiting Assignment` unless a mechanic is assigned at intake (then `Awaiting Diagnosis`).
- **Limitations:** None.

### Jobs (Job Cards Management)
- **Status:** Partial
- **Controllers:** `JobsController`
- **Views:** `admin/jobs/jobs_list.php`, `jobs/add.php`, `jobs/edit.php`, `job/index.php`, `job/modals.php`
- **What it does:** DataTable listing (with vehicle join), add form, edit form, update, soft delete, dispatch mechanic assignment via job details modal.
- **Limitations:** `create()` method is referenced in routes (`admin/jobs/create`) but does not exist in `JobsController`.

### Calendar
- **Status:** Complete
- **Controllers:** `CalendarController`
- **Views:** `calendar/calendar.php`, `calendar/modals.php`
- **What it does:** FullCalendar integration, fetching job events by date range (drop-offs, estimated completions, completed), custom calendar event creation with validation, drag-drop event date update.
- **Limitations:** `updateEventDate()` is referenced in routes but does not exist in the controller. No event edit or delete endpoints.

### Sublets
- **Status:** Complete
- **Controllers:** `SubletsController`
- **Views:** `sublets/index.php`, `sublets/form.php`, `sublets/_details.php`, `sublets/modals.php`
- **What it does:** DataTable with server-side processing, status filter, add/edit form with validation, details modal, single and bulk delete, joins with `job_cards` and `suppliers`.
- **Limitations:** `fetchSublets()`, `edit()`, and `update()` are referenced in routes but do not exist. `add($id)` handles both add and edit form loading; `save()` handles both create and update.

### Inventory
- **Status:** Not built
- **Controllers:** None
- **Views:** None
- **What it does:** The `inventory` table exists and is used by JobIntake for parts lookup, but there is no CRUD UI, no routes, and no controller.

### Finance (Invoices, LPOs, Petty Cash)
- **Status:** Not built
- **Controllers:** None
- **Views:** None
- **What it does:** No invoices, LPO, or petty cash tables or code exist in the application (LPOs and Petty Cash have no DB tables at all). Sidebar links and dashboard quick action buttons exist but lead to 404s.

### Suppliers
- **Status:** Not built
- **Controllers:** None
- **Views:** None
- **What it does:** The `suppliers` table exists and is used by Sublets, but there is no CRUD UI, no routes, and no controller.

### Reports
- **Status:** Not built
- **Controllers:** None
- **Views:** None
- **What it does:** Sidebar link exists. No implementation.

### Settings
- **Status:** Not built
- **Controllers:** None
- **Views:** None
- **What it does:** Sidebar link exists. No implementation.

## 5. ROLES & PERMISSIONS

| Role | Route Group | Description |
|------|-------------|-------------|
| **admin** | `/admin/*` (filter: `auth:admin`) | Full access to all modules. Can manage users, customers, inventory, suppliers, invoices, calendar, LPOs, petty cash, reports, settings. |
| **receptionist** | `/receptionist/` (filter: `auth:receptionist`), `/job_intake/*` (filter: `auth:admin,receptionist`) | Can perform job intake (create job cards, search customers/vehicles). Has own dashboard view. |
| **mechanic** | `/mechanic/` (filter: `auth:mechanic`) | Has own dashboard with stat cards and assigned jobs list. Can view assigned jobs, perform diagnosis (add parts/tasks, submit diagnosis), and search inventory parts. |
| **customer** | `/customer/` (filter: `auth:customer`) | Has own dashboard view. No customer-facing functionality exists beyond the standalone dashboard view. |

**AuthFilter arguments:** Route groups pass role arguments to the filter, e.g. `['filter' => 'auth:admin']` or `['filter' => 'auth:admin,receptionist']`. If no arguments are provided, the filter only checks `isLoggedIn`.

**Sidebar visibility:** The sidebar (`partials/sidebar.php`) conditionally shows admin-only links (`users`, `customers`, `inventory`, `suppliers`, `invoices`, `calendar`, `LPOs`, `petty cash`, `reports`, `settings`) when `$role == 'admin'`. All roles see `Dashboard`, `Jobs`, `Vehicles`, and `Sublets`.

### Mechanic Workflow (Full Lifecycle)

Intake → Awaiting Assignment → Dispatch (admin assigns mechanic) → Awaiting Diagnosis → Mechanic Diagnosis → Diagnosis Complete → Admin Approval → In Progress → [Awaiting Parts / Sublets] → Quality Check → Ready for Invoice → Paid → Completed

**Mechanic-specific routes:**
```
GET  /mechanic/                 -> DashboardController::mechanic
GET  /mechanic/dashboard        -> DashboardController::mechanic
GET  /mechanic/jobs             -> JobIntake::mechanic_jobs
GET  /mechanic/jobs/(:num)      -> JobIntake::mechanic_view/$1
POST /mechanic/save_diagnosis   -> JobIntake::save_diagnosis
GET  /mechanic/search_parts     -> JobIntake::search_parts
```

**Dispatch route (admin):**
```
POST /admin/jobs/assign_mechanic/(:num) -> JobsController::assign_mechanic/$1
```

## 6. CODING CONVENTIONS

- **Controllers** extend `BaseController` (which extends `CodeIgniter\Controller`). Database access is via `\Config\Database::connect()` stored as `$this->db` in `__construct()` or called inline.
- **No models** — all database queries use `$db->table('table_name')->...` query builder or raw `$db->query('SELECT ...')`.
- **AJAX responses** return JSON via `$this->response->setJSON($data)` or `$this->respond($data)` (using `CodeIgniter\API\ResponseTrait`).
- **DataTables** endpoints use the `fetch` pattern (`/admin/{module}/fetch`) for basic AJAX data and the `load` pattern (`/admin/customers/load`, `/admin/sublets/load`) for server-side processing.
- **Pagination** uses CI4's `$query->paginate(10)` and passes `$pager` to the view.
- **Views** are loaded with `return view('path', $data)`. Views that need the sidebar use `$this->extend('layouts/main')` with `$this->section('content')`.
- **File uploads** use `$file->getRandomName()` and `$file->move()`. Destination paths: `uploads/users/` for profile pictures, `uploads/job_card_photos/` for job card photos.
- **Soft deletes** use a `deleted_at` datetime column (on `users`, `job_cards`, `jobs`). `customers` does not have this column.
- **CSRF** is globally enabled — every POST form must include `<?= csrf_field() ?>`. AJAX POST requests must pass the CSRF token.
- **No CSRF exemption** is configured for any route.

## 7. ROUTE PATTERNS

### Public routes (no auth filter)
```
GET  /                             -> Home::index
GET  /login                        -> LoginController::index
POST /login/auth                   -> LoginController::auth
GET  /logout                       -> LoginController::logout
GET  /unauthorized                 -> DashboardController::unauthorized
```

### User registration wizard (no auth)
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
POST /save-step-data/(:num)        -> UsersController::saveStepData/$1
POST /final-submit                 -> UsersController::finalSubmit
GET  /user/getLastId               -> UsersController::getLastId
POST /user/submit                  -> UsersController::submit (NOT IMPLEMENTED)
GET  /user/success                 -> UsersController::success
GET  /user/failure                 -> UsersController::failure
```

### Job Intake group (filter: auth:admin,receptionist)
```
GET  /job_intake/                     -> JobIntake::index
GET  /job_intake/search               -> JobIntake::search
POST /job_intake/create_job_card      -> JobIntake::create_job_card
GET  /job_intake/create_job_card      -> JobIntake::create_job_card
POST /job_intake/fetch_vehicle_details -> JobIntake::fetch_vehicle_details
POST /job_intake/fetch_customer_details -> JobIntake::fetch_customer_details
```

### Admin group (filter: auth:admin)
```
GET    /admin/                                 -> DashboardController::admin
GET    /admin/dashboard                        -> DashboardController::admin
GET    /admin/users                            -> UsersController::index
GET    /admin/users/add                        -> UsersController::add
GET    /admin/users/(:num)                     -> UsersController::details/$1
GET    /admin/users/edit/(:num)                -> UsersController::edit/$1
POST   /admin/users/update/(:num)              -> UsersController::update/$1
GET    /admin/users/delete/(:num)              -> UsersController::delete/$1
POST   /admin/users/bulk_action                -> UsersController::bulk_action
GET    /admin/users/fetch/(:num)               -> UsersController::details/$1
GET    /admin/users/fetch                      -> UsersController::fetchUsers
GET    /admin/vehicles                         -> VehicleController::index
GET    /admin/vehicles/fetch                   -> VehicleController::fetchVehicles
GET    /admin/vehicles/fetch/(:num)            -> VehicleController::fetchVehicles
GET    /admin/vechicles/edit/(:num)            -> VehicleController::edit/$1 (TYPO — "vechicles")
POST   /admin/vehicles/store                   -> VehicleController::store
POST   /admin/vehicles/update/(:num)           -> VehicleController::update/$1
POST   /admin/vehicles/delete/(:num)           -> VehicleController::delete/$1
POST   /admin/vehicles/add                     -> VehicleController::add
GET    /admin/vehicles/edit/(:num)             -> VehicleController::edit/$1
GET    /admin/vehicles/delete/(:num)           -> VehicleController::delete/$1
GET    /admin/vehicles/details/(:num)          -> VehicleController::details/$1
GET    /admin/jobs                             -> JobsController::index
GET    /admin/jobs/fetch                       -> JobsController::fetchJobs
GET    /admin/jobs/add                         -> JobsController::add
POST   /admin/jobs/create                      -> JobsController::create (NOT IMPLEMENTED)
GET    /admin/jobs/(:num)                      -> JobsController::details/$1
GET    /admin/jobs/edit/(:num)                 -> JobsController::edit/$1
POST   /admin/jobs/update/(:num)               -> JobsController::update/$1
GET    /admin/jobs/delete/(:num)               -> JobsController::delete/$1
POST   /admin/jobs/bulk_action                 -> JobsController::bulk_action
GET    /admin/job_intake                       -> JobIntake::index
GET    /admin/job_intake/search                -> JobIntake::search
POST   /admin/job_intake/create_job_card       -> JobIntake::create_job_card
GET    /admin/job_intake/create_job_card       -> JobIntake::create_job_card
POST   /admin/job_intake/fetch_vehicle_details -> JobIntake::fetch_vehicle_details
POST   /admin/job_intake/fetch_customer_details-> JobIntake::fetch_customer_details
GET    /admin/customers                        -> CustomersController::index
POST   /admin/customers/load                   -> CustomersController::load
GET    /admin/customers/load                   -> CustomersController::load
GET    /admin/customers/details/(:num)         -> CustomersController::details/$1
GET    /admin/customers/add                    -> CustomersController::add
GET    /admin/customers/edit/(:num)            -> CustomersController::edit/$1
POST   /admin/customers/bulk_action            -> CustomersController::bulk_action
GET    /admin/calendar                         -> CalendarController::index
GET    /admin/calendar/getEvents               -> CalendarController::getEvents
GET    /admin/calendar/addEvent                -> CalendarController::addEvent
POST   /admin/calendar/addEvent                -> CalendarController::addEvent
POST   /admin/calendar/updateEventDate         -> CalendarController::updateEventDate (NOT IMPLEMENTED)
GET    /admin/sublets                          -> SubletsController::index
GET    /admin/sublets/add                      -> SubletsController::add
POST   /admin/sublets/load                     -> SubletsController::load
GET    /admin/sublets/edit/(:num)              -> SubletsController::add/$1
POST   /admin/sublets/save                     -> SubletsController::save
GET    /admin/sublets/details/(:num)           -> SubletsController::details/$1
POST   /admin/sublets/delete/(:num)            -> SubletsController::delete/$1
POST   /admin/sublets/bulkAction               -> SubletsController::bulkAction
GET    /admin/sublets/fetch                    -> SubletsController::fetchSublets (NOT IMPLEMENTED)
GET    /admin/sublets/fetch/(:num)             -> SubletsController::fetchSublets/$1 (NOT IMPLEMENTED)
GET    /admin/sublets/(:num)                   -> SubletsController::details/$1
GET    /admin/sublets/(:num)/edit              -> SubletsController::edit/$1 (NOT IMPLEMENTED)
POST   /admin/sublets/(:num)/update            -> SubletsController::update/$1 (NOT IMPLEMENTED)
```

### Role-specific dashboard groups
```
GET /receptionist/  (filter: auth:receptionist) -> DashboardController::receptionist
GET /mechanic/      (filter: auth:mechanic)     -> DashboardController::mechanic
GET /customer/      (filter: auth:customer)     -> DashboardController::customer
```

## 8. SIDEBAR NAVIGATION

All links rendered in `app/Views/partials/sidebar.php`.

| Link | Route | Visible To | Built? |
|------|-------|------------|--------|
| Dashboard | `/admin/dashboard` | All roles | Yes |
| Jobs | `/admin/jobs` | All roles | Yes (dispatch mechanic assignment added) |
| Users | `/admin/users` | Admin only | Yes |
| Customers | `/admin/customers` | Admin only | Partial — no store/update routes |
| Vehicles | `/admin/vehicles` | All roles | Partial |
| Sublets | `/admin/sublets` | All roles | Yes |
| Inventory | `/admin/inventory` | Admin only | **No** — no controller/routes |
| Suppliers | `/admin/suppliers` | Admin only | **No** — no controller/routes |
| Invoices | `/admin/invoices` | Admin only | **No** — no controller/routes, no DB table |
| Calendar | `/admin/calendar` | Admin only | Yes (partial — missing updateEventDate) |
| LPOs | `/admin/lpos` | Admin only | **No** — no controller/routes, no DB table |
| Petty Cash | `/admin/pettycash` | Admin only | **No** — no controller/routes, no DB table |
| Reports | `/admin/reports` | Admin only | **No** — no controller/routes |
| Settings | `/admin/settings` | Admin only | **No** — no controller/routes |
| Profile (footer) | `/admin/profile` | All roles | **No** — no controller/routes |

## 9. KNOWN LIMITATIONS

1. **No models** — all database access is raw query builder calls in controllers.
2. **No migrations** — the database schema was created manually (likely via phpMyAdmin or direct SQL). `app/Database/Migrations/` is empty. Schema changes must be applied directly.
3. **No seed data** — `app/Database/Seeds/` is empty.
4. **Customers has no soft-delete** — the `customers` table lacks a `deleted_at` column.
5. **Sidebar links for Inventory, Suppliers, Invoices, LPOs, Petty Cash, Reports, Settings, and Profile** are not backed by controllers, routes, or (for some) database tables. Clicking these links returns a 404.
6. **`jobs` table is legacy/unused** — `job_cards` is the real transactional jobs table.
7. **Customer role has no post-login redirect** — after login, a customer user's redirect falls through in `LoginController` without a dedicated destination.
8. **`JobsController::create()` referenced in route but does not exist.**
9. **`CalendarController::updateEventDate()` referenced in route but does not exist.**
10. **`SubletsController::fetchSublets()`, `edit()`, `update()` referenced in routes but do not exist.**
11. **`UsersController::submit()` referenced in route but does not exist.**
12. **`VehicleController` route has a typo** — `vechicles/edit/(:num)` instead of `vehicles/edit/(:num)`.
13. **`CustomersController` has no `store()` or `update()` routes** — customer add/edit forms cannot be submitted.

14. **Vehicles index view** does not extend `layouts/main`, so it renders without the sidebar.

## 10. GOTCHAS

1. **Always use `job_cards`, not `jobs`**, for transactional job data. The `jobs` table is legacy and unused.
2. **Always use `UsersController`** (with 's'), not `UserController`.
3. **Always use `App\Controllers\CustomersController`**, not `Admin\CustomersController` — there is no `Admin\` subdirectory in Controllers.
4. **`registration_number`** is the correct column name in the `vehicles` table (not `vehicle_number`).
5. **CSRF is globally enabled.** Every POST form must include `<?= csrf_field() ?>`. AJAX POST requests must pass the CSRF token (via `meta` tag or header). There are no CSRF exemptions configured.
6. **`baseURL`** (`app/Config/App.php`) must be `http://localhost/FlowDesk/` and **`.htaccess RewriteBase`** must be `/FlowDesk/`. Both must match. Change both when deploying elsewhere.
7. **Database name is `flowdesk`** (all lowercase). Set in `.env` as `database.default.database = flowdesk`. The value in `app/Config/Database.php` is overridden by `.env`.
8. **`users.name` is a STORED GENERATED column** (concatenation of `first_name` + `last_name`). Some code references `first_name`/`last_name` directly — use whichever is appropriate for the query context.
9. **No auto-routing** — every route is explicitly defined in `Routes.php`. Adding a new controller method requires a corresponding route entry.
