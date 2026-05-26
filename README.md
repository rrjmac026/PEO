# PEO Management System

## Overview

This repository is a Laravel 12 application built for a Provincial Engineer's Office (PEO) workflow. It manages:

- Contractor work requests
- Concrete pouring approvals and reporting
- Memo creation, delivery, and read tracking
- Role-based reviewing and approval workflows
- PDF generation for reports and printable forms

The app is organized into three main user domains:

- **Admin**: receive work requests, assign reviewers, manage employees/users, review concrete pouring records, and generate reports.
- **Contractor**: submit work requests, upload concrete pouring records, view memos, and track status.
- **Reviewer**: review and inspect work requests and concrete pourings, make decisions, and approve final requests.

## What this codebase is about

This project is a specialized Laravel application for civil construction administration. It supports an approval workflow that includes:

- Work request submission and contractor intake
- Multi-step review by site inspectors, surveyors, resident engineers, MTQA, Engineer IV, Engineer III, and Provincial Engineer
- Concrete pouring logging with bulk approval/disapproval, reports, calendar view, and printable records
- Memo management for sending notices and attachments to users
- Role-based permissions and notification handling

## Key components

- `app/Models/WorkRequest.php`: defines the full review pipeline for contractor work requests and their approval status.
- `app/Models/ConcretePouring.php`: stores concrete pouring records, approvals, and logs.
- `app/Models/Memo.php`: handles memo content, recipient tracking, and read state.
- `routes/web.php`: defines the admin, contractor, and reviewer routes.
- `app/Http/Controllers/*`: implements CRUD operations, assignments, review steps, reports, and PDF exports.
- `resources/views/`: contains Blade templates for the user interface and printable documents.

## Technology stack

- PHP 8.2
- Laravel 12
- Tailwind CSS + Alpine.js
- Vite for frontend asset building
- `phpoffice/phpspreadsheet` for Excel export support
- `setasign/fpdf` and `setasign/fpdi` for PDF generation
- `puppeteer` for optional frontend PDF tooling

## Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Copy environment file:

```bash
cp .env.example .env
```

3. Generate app key:

```bash
php artisan key:generate
```

4. Run database migrations:

```bash
php artisan migrate --force
```

5. Install frontend dependencies:

```bash
npm install
```

6. Build frontend assets:

```bash
npm run build
```

You can also use the provided script:

```bash
composer run setup
```

## Local development

Start the backend and frontend development server:

```bash
php artisan serve
npm run dev
```

If using the `dev` script:

```bash
npm run dev
```

## Notes

- The application uses role-based middleware to separate admin, contractor, and reviewer functionality.
- Reports can be exported to PDF and Excel.
- Work requests and concrete pourings include detailed approval logs and status tracking.

## License

This project uses the MIT license.
