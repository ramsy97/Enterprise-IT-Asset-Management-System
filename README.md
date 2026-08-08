# ITAMS Enterprise

IT Asset Management System for tracking company IT assets across their full lifecycle — registration, assignment, maintenance, warranty, licenses, and audits — with role-based dashboards for Administrators, IT Staff, and Managers.

Built with **Laravel 12**, **MySQL**, **Redis**, and **Tailwind CSS**.

## Features

- **Asset management** — register, edit, retire assets with categories, locations, brands, purchase info, and auto-generated QR codes
- **Assignments** — request/approve/reject/return workflow for asset handovers
- **Maintenance** — schedule preventive & repair maintenance with calendar and status tracking
- **Warranty tracking** — expiring & expired warranty alerts
- **License management** — software license inventory and usage tracking
- **Audits** — periodic asset audit records with verification workflow
- **Reports** — asset, maintenance, audit, and license reports with Excel/PDF export
- **Role-based dashboards** — ADMIN, IT STAFF, and MANAGER with KPIs and charts
- **Admin console** — user management, role/permission management, and app settings
- **Activity logging** — audit trail of key actions

## Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- Redis
- Node.js 18+ & npm (for asset compilation)

## Installation

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Configure .env (database & redis credentials)
#    DB_CONNECTION=mysql, DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    CACHE_STORE=redis, SESSION_DRIVER=redis, QUEUE_CONNECTION=redis

# 4. Database
php artisan migrate --seed

# 5. Serve
npm run build
php artisan serve
```

Queue worker is required for background jobs (e.g., email reminders):

```bash
php artisan queue:listen
```

## Roles

| Role | Scope |
| --- | --- |
| ADMIN | Full access — users, roles, settings, all modules |
| IT STAFF | Day-to-day asset, assignment, maintenance, and license operations |
| MANAGER | Read-focused dashboards, reports, and approval visibility |

Seed with `php artisan db:seed` to create default roles, permissions, and an administrator account.

## Testing

```bash
composer test
```

## Architecture

See [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md), [docs/DATABASE_ERD.md](docs/DATABASE_ERD.md), and [docs/MIGRATION_PLAN.md](docs/MIGRATION_PLAN.md) for design details.
