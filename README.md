# PEO Management System

A workflow management platform for the **Provincial Engineer's Office (PEO) of the Province of Bukidnon**. Built with Laravel 12, it digitizes the end-to-end lifecycle of contractor work requests and concrete pouring approvals — replacing paper-based triplicate forms with a structured, role-gated review pipeline backed by automated email notifications and PDF/Excel report generation.

---

## Table of Contents

- [Overview](#overview)
- [User Roles](#user-roles)
- [Core Modules](#core-modules)
  - [Work Requests](#work-requests)
  - [Concrete Pouring](#concrete-pouring)
  - [Memos](#memos)
  - [Reports](#reports)
  - [Notifications](#notifications)
- [Review Pipelines](#review-pipelines)
  - [Work Request Pipeline](#work-request-pipeline)
  - [Concrete Pouring Pipeline](#concrete-pouring-pipeline)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Setup](#setup)
- [Local Development](#local-development)
- [Environment Variables](#environment-variables)
- [PDF & Excel Generation](#pdf--excel-generation)
- [Email Notifications](#email-notifications)
- [License](#license)

---

## Overview

The PEO Management System is a specialized civil construction administration platform. It replaces the office's manual, paper-based triplicate forms with a fully digital workflow that includes:

- **Multi-step review pipelines** with role-based turn enforcement (each reviewer can only act when it's their assigned step)
- **Digital signatures** collected via on-screen drawing pad or uploaded image, embedded directly into generated PDFs
- **Automated email + in-app notifications** at every pipeline transition
- **Activity audit logs** capturing every state change, who made it, when, and from what IP address
- **PDF generation** that faithfully reproduces the official provincial government document layouts
- **Excel exports** for reporting and data analysis

---

## User Roles

The system defines nine roles, each with a scoped dashboard and restricted route access enforced via middleware:

| Role | Description |
|------|-------------|
| `admin` | Receives submitted requests, assigns reviewers, manages users and employees, views all records, generates reports, performs bulk actions |
| `contractor` | Submits work requests and concrete pouring requests, tracks status, views memos |
| `site_inspector` | Reviews and inspects work requests at the site inspection step |
| `surveyor` | Performs survey review step for work requests |
| `resident_engineer` | Reviews both work requests and concrete pouring requests |
| `mtqa` | Materials Testing and Quality Assurance — performs MTQA check on work requests, makes **final approve/disapprove decision** on concrete pouring requests, can print approved work requests |
| `engineeriv` | Engineer IV review step in the work request pipeline |
| `engineeriii` | Engineer III recommending-approval step in the work request pipeline |
| `provincial_engineer` | Makes the **final approve/reject decision** on work requests; notes concrete pouring requests before MTQA decision |

New users self-register as contractors. All other roles are created by an admin.

---

## Core Modules

### Work Requests

Contractors submit work requests for planned construction or maintenance activities. Each request captures:

- Contract number, project name, and location
- Requested work start date and time
- Pay item details (item number, description, equipment, estimated quantity, unit)
- Full description of work requested
- Contractor's preferred Resident Engineer (optional pre-fill for admin)

**Admin actions:** assign any combination of the seven reviewer roles, re-assign if needed, edit or delete submissions, manually override status, export to PDF/Excel.

**Contractor actions:** create, edit (while still in `submitted` status), view status and current review step, print the completed form.

**Reviewer actions:** each reviewer sees only their assigned requests when it is their turn. Reviewers fill in findings, recommendations, and a digital signature. Submitting their step automatically advances the pipeline to the next assigned reviewer.

**MTQA special access:** MTQA users can view and print/download all approved work requests — not just those they reviewed — so they can always produce printed copies on demand.

### Concrete Pouring

Contractors submit concrete pouring requests linked to an approved work request (or standalone). Each record includes:

- Project name, location, contractor, and part of structure to be poured
- Estimated volume (cubic meters), station limits/section
- Scheduled pouring date and time
- A **20-item compliance checklist** (concrete vibrator, slump cones, rebar sizes, falseworks adequacy, etc.)

The checklist drives a live **checklist progress percentage** visible throughout the system. Admin can perform **bulk approve/disapprove** on unassigned requests.

### Memos

Admins can compose and send internal memos to any combination of recipients:

- **Recipient scope:** All users, by role, by department, or specific individuals
- **Status lifecycle:** Draft → Scheduled (future send) → Sent, with a Cancel option for scheduled memos
- **File attachments:** multiple files up to 10 MB each, stored on disk with a remove-attachment action
- **Read tracking:** each recipient's read timestamp is recorded; admins see per-memo read rates and aggregate analytics
- **Email delivery:** every sent memo triggers a branded email to each recipient with a direct link to the memo

Memo types include: Announcement, Birthday Greeting, Holiday Greeting, Policy Update, Event Invitation, Performance Notice, and General Memo.

### Reports

A dedicated reports module (`/admin/reports`) provides four views:

| Report | Contents |
|--------|----------|
| **Overview** | High-level summary cards across all three modules with user breakdown by role |
| **Work Requests** | Total/approved/rejected/pending counts, approval rate, contractor breakdown, reviewer step breakdown, monthly trend, detailed record table |
| **Concrete Pourings** | Total/approved/disapproved/pending counts, total and average estimated volume, average checklist completion, contractor breakdown, per-item checklist compliance rates, monthly trend |
| **Memos** | Total/sent/draft/scheduled counts, read rate analysis per memo, type breakdown, monthly trend |

Every report supports **date range presets** (this month, last month, last 7/30 days, this year, last quarter) or a custom date range. All four reports can be exported as **PDF** or **Excel (.xlsx)**.

### Notifications

Every significant action triggers both an **in-app notification** (visible in the bell icon in the sidebar) and an **email** to the relevant parties:

- Contractor submits → admins notified
- Admin assigns reviewers → each reviewer notified (active turn vs. queued)
- Reviewer completes step → next reviewer notified
- Provincial Engineer decides → contractor and MTQA notified
- MTQA decides on concrete pouring → contractor and all reviewers notified
- Memo sent → all recipients notified

Unread notification counts are surfaced in the sidebar for work requests and concrete pourings separately. A full notifications page supports filtering by read/unread status and type.

---

## Review Pipelines

### Work Request Pipeline

```
Contractor submits
        ↓
Admin assigns reviewers (any subset, in order)
        ↓
Site Inspector  →  Surveyor  →  Resident Engineer  →  MTQA
        →  Engineer IV  →  Engineer III  →  Provincial Engineer (FINAL)
```

Steps are **skipped automatically** if no reviewer was assigned for that role. The pipeline always progresses to the next *assigned* step. The Provincial Engineer is always the final decision-maker (Approved / Rejected). Once approved, MTQA is notified and can print the completed form.

### Concrete Pouring Pipeline

```
Contractor submits
        ↓
Admin assigns reviewers
        ↓
Resident Engineer  →  Provincial Engineer (note)  →  ME/MTQA (FINAL)
```

The Provincial Engineer submits a note (not a decision) then the request advances to ME/MTQA for the final Approve / Disapprove decision. Admin can also bypass the pipeline entirely via bulk approve/disapprove for unassigned requests.

---

## Technology Stack

| Layer | Technology |
|-------|------------|
| Language | PHP 8.2 |
| Framework | Laravel 12 |
| Frontend | Blade templates, Tailwind CSS, Alpine.js |
| Build tool | Vite |
| PDF generation | `setasign/fpdf` + `setasign/fpdi` |
| Excel generation | `phpoffice/phpspreadsheet` |
| Email | Laravel Mail (SMTP) |
| Database | MySQL (any Laravel-supported driver) |
| Optional | Puppeteer (frontend PDF tooling) |

---

## Project Structure

```
app/
├── Enums/
│   └── Role.php                        # Nine role constants
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                      # Admin-only controllers
│   │   │   ├── AdminController.php     # Dashboard
│   │   │   ├── WorkRequestController.php
│   │   │   ├── AdminConcretePouringController.php
│   │   │   ├── MemoController.php
│   │   │   ├── AdminReportsController.php
│   │   │   ├── UserManagementController.php
│   │   │   ├── EmployeeManagementController.php
│   │   │   └── WorkRequestLogController.php
│   │   ├── Reviewer/                   # Reviewer-only controllers
│   │   │   ├── ReviewerController.php
│   │   │   ├── ReviewerWorkRequestController.php
│   │   │   ├── ReviewerConcretePouringController.php
│   │   │   └── ReviewerMemoController.php
│   │   ├── User/                       # Contractor-only controllers
│   │   │   ├── UserController.php
│   │   │   ├── UserWorkRequestController.php
│   │   │   ├── UserConcretePouringController.php
│   │   │   └── UserMemoController.php
│   │   ├── Auth/                       # Laravel Breeze auth
│   │   ├── NotificationController.php
│   │   └── ProfileController.php
│   └── Middleware/
│       └── RoleMiddleware.php          # role:admin, role:contractor, etc.
├── Models/
│   ├── WorkRequest.php                 # Full review pipeline, REVIEW_STEPS const
│   ├── ConcretePouring.php             # 20-item checklist, approve/disapprove
│   ├── WorkRequestLog.php              # Audit log for work requests
│   ├── ConcretePouringLog.php          # Audit log for concrete pourings
│   ├── Memo.php                        # Memo lifecycle and scopes
│   ├── MemoRecipient.php               # Per-recipient read tracking
│   ├── Notification.php                # In-app notifications
│   ├── User.php
│   └── Employee.php                    # Employee profile linked to user
├── Services/
│   ├── WorkRequestPdf.php              # FPDF subclass — work request form
│   ├── ConcretePouringPdf.php          # FPDF subclass — pouring form
│   ├── WorkRequestNotificationService.php
│   ├── ConcretePouringNotificationService.php
│   ├── NotificationService.php         # Backward-compat facade
│   ├── WorkRequestExcelService.php
│   └── Reports/
│       ├── ReportPdfService.php        # Multi-module PDF reports
│       └── ReportExcelService.php      # Multi-module Excel reports
├── Mail/                               # 12 Mailable classes
└── Imports/
    └── EmployeesImport.php             # Bulk import via CSV

resources/views/
├── admin/          # Admin Blade templates
├── reviewer/       # Reviewer Blade templates
├── user/           # Contractor Blade templates
├── emails/         # Email templates
├── notifications/  # Notification page
└── layouts/        # Sidebar layouts per role group
```

---

## Setup

**1. Install PHP dependencies**
```bash
composer install
```

**2. Copy environment file**
```bash
cp .env.example .env
```

**3. Generate application key**
```bash
php artisan key:generate
```

**4. Configure your database** in `.env` (see [Environment Variables](#environment-variables)), then run migrations:
```bash
php artisan migrate --force
```

**5. Install and build frontend assets**
```bash
npm install
npm run build
```

Or use the provided setup script which runs all of the above:
```bash
composer run setup
```

**6. Link the storage disk** (required for signature images and memo attachments)
```bash
php artisan storage:link
```

**7. Seed an admin user** (optional — create one manually via the database or a seeder)
```bash
php artisan db:seed
```

---

## Local Development

Start the backend development server:
```bash
php artisan serve
```

Start the Vite dev server for hot-reloading:
```bash
npm run dev
```

Both must be running simultaneously during development.

---

## Environment Variables

Key variables to configure in `.env`:

```env
# Application
APP_NAME="PEO Management System"
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=peo_management
DB_USERNAME=root
DB_PASSWORD=

# Mail (required for email notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@peo.bukidnon.gov.ph"
MAIL_FROM_NAME="PEO Management System"

# Queue (set to 'database' or 'redis' for async email)
QUEUE_CONNECTION=sync
```

If `QUEUE_CONNECTION` is set to anything other than `sync`, run the queue worker to process emails asynchronously:
```bash
php artisan queue:work
```

---

## PDF & Excel Generation

### PDF Reports
PDFs are generated using `setasign/fpdf` — a pure-PHP PDF library with no external dependencies. Both the Work Request form and the Concrete Pouring form are pixel-accurate reproductions of the official provincial government paper forms, including:

- Province of Bukidnon seal and office logo in the header
- All reviewer signature blocks with embedded digital signature images
- Checklist items with rendered checkboxes
- Approval and "noted by" blocks with date fields

Digital signatures are stored either as base64-encoded PNG data URIs (drawn on-screen) or as storage-relative file paths (uploaded images). The PDF service resolves either format to a temporary file that FPDF can embed, then removes the temp file after rendering.

### Excel Reports
Excel files are generated using `phpoffice/phpspreadsheet` with branded styling (orange header band, alternating row shading, conditional color-coding for status columns). Each report produces a multi-sheet workbook: a summary sheet, one or more breakdown sheets, and a detailed records sheet.

---

## Email Notifications

The following events trigger email delivery:

| Event | Recipients |
|-------|-----------|
| Work request submitted | All admins |
| Reviewers assigned | Each assigned reviewer (action-required or heads-up) |
| Review step advanced | Next reviewer in the pipeline |
| Provincial Engineer decides | Contractor + MTQA (if approved) |
| Concrete pouring submitted | All admins |
| Concrete pouring reviewers assigned | Each assigned reviewer |
| Concrete pouring step advanced | Next reviewer |
| MTQA approves/disapproves | Contractor + all assigned reviewers |
| Memo sent | All memo recipients |
| User account created | New user (includes generated password) |

All mail classes live in `app/Mail/`. Email templates are in `resources/views/emails/`.

---

## License

This project is licensed under the [MIT License](LICENSE).