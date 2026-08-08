# ITAMS Enterprise — Application Architecture

## 1. Design Principles

- **Layered architecture**: `Controller → Service → (Repository) → Model`
- **Fat models, thin controllers, business logic in services**
- **Form Request validation** for every user-facing input
- **Policy authorization** enforced in addition to role/permission middleware
- **Events & Listeners** for cross-cutting concerns (notifications, logging)
- **Queued jobs** for anything slow (email, QR render, exports)
- **Cache-first reads** backed by Redis for dashboard KPIs and lookups

## 2. Folder Architecture

```
app/
├── Console/
│   └── Commands/                  # artisan commands (reminders, warranty check)
├── Enums/
│   ├── AssetStatus.php            # available|assigned|maintenance|retired
│   ├── AssignmentStatus.php       # pending|approved|rejected|returned
│   ├── MaintenanceType.php        # preventive|repair|replacement
│   └── AuditStatus.php            # verified|need_repair|missing
├── Events/                        # domain events
│   ├── AssetRegistered.php
│   ├── AssetAssigned.php
│   ├── MaintenanceScheduled.php
│   └── AuditCompleted.php
├── Exports/                       # Maatwebsite\Excel export classes
│   ├── AssetsExport.php
│   ├── MaintenancesExport.php
│   ├── AuditsExport.php
│   └── LicensesExport.php
├── Http/
│   ├── Controllers/               # resource controllers (thin)
│   │   ├── Admin/                 # dashboard, users, roles, settings
│   │   ├── Auth/
│   │   ├── Assets/                # AssetController, CategoryController, ...
│   │   ├── MaintenanceController.php
│   │   ├── AuditController.php
│   │   ├── LicenseController.php
│   │   ├── ReportController.php
│   │   ├── AssignmentController.php
│   │   └── QrController.php       # QR rendering / scan lookup
│   ├── Middleware/
│   │   ├── EnsureUserHasRole.php
│   │   └── TrackActivity.php      # writes activity_logs
│   ├── Requests/                  # Form Requests (validation)
│   │   ├── StoreAssetRequest.php
│   │   ├── UpdateAssetRequest.php
│   │   ├── StoreAssignmentRequest.php
│   │   ├── StoreMaintenanceRequest.php
│   │   ├── StoreAuditRequest.php
│   │   ├── StoreLicenseRequest.php
│   │   ├── StoreCategoryRequest.php
│   │   ├── StoreLocationRequest.php
│   │   └── UpdateUserRequest.php
├── Jobs/                          # queued jobs
│   ├── SendWarrantyReminderJob.php
│   ├── SendMaintenanceReminderJob.php
│   └── SendAssignmentNotificationJob.php
├── Listeners/
│   ├── NotifyOnAssetRegistered.php
│   └── LogActivity.php
├── Mail/                          # Mailable classes
│   ├── WarrantyExpiringMail.php
│   ├── MaintenanceReminderMail.php
│   └── AssignmentStatusMail.php
├── Models/
│   ├── User.php                   # extends Authenticatable + HasRoles
│   ├── Asset.php
│   ├── AssetCategory.php
│   ├── AssetLocation.php
│   ├── AssetAssignment.php
│   ├── MaintenanceRecord.php
│   ├── SoftwareLicense.php
│   ├── AuditRecord.php
│   ├── Notification (Laravel built-in)
│   └── ActivityLog.php
├── Notifications/                 # Laravel Notification classes
│   ├── WarrantyExpiringNotification.php
│   ├── MaintenanceReminderNotification.php
│   └── AssignmentStatusNotification.php
├── Policies/
│   ├── AssetPolicy.php
│   ├── AssetAssignmentPolicy.php
│   ├── MaintenanceRecordPolicy.php
│   ├── SoftwareLicensePolicy.php
│   ├── AuditRecordPolicy.php
│   ├── UserPolicy.php
│   └── RolePolicy.php
├── Providers/
│   └── AppServiceProvider.php     # gates, route model binding, observer registration
├── Services/                      # business logic layer
│   ├── AssetService.php           # CRUD + asset code generation
│   ├── QrCodeService.php          # QR render/generate + cache
│   ├── AssignmentService.php      # request → approve → assign → history
│   ├── MaintenanceService.php
│   ├── WarrantyService.php        # expiry calculation + reminders
│   ├── AuditService.php
│   ├── LicenseService.php
│   ├── DashboardService.php       # KPI + chart data (cached)
│   ├── ReportService.php          # report datasets
│   └── ExportService.php          # wraps Excel/PDF export orchestration
├── Observers/
│   └── AssetObserver.php          # auto QR + activity log on create
├── Traits/
│   ├── HasActivityLogging.php
│   └── GeneratesAssetCode.php
├── Support/
│   └── ActivityLogger.php

config/
├── permission.php                 # Spatie RBAC config
├── qrcode.php                     # QR styling
└── itams.php                      # app-wide config (warranty threshold days, etc.)

database/
├── migrations/
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── RolePermissionSeeder.php
│   ├── AdminUserSeeder.php
│   ├── CategorySeeder.php
│   ├── LocationSeeder.php
│   ├── AssetSeeder.php
│   └── DemoDataSeeder.php
└── factories/
    ├── AssetFactory.php
    ├── AssetCategoryFactory.php
    ├── AssetLocationFactory.php
    ├── AssetAssignmentFactory.php
    ├── MaintenanceRecordFactory.php
    ├── SoftwareLicenseFactory.php
    └── AuditRecordFactory.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php          # authenticated shell (sidebar + topbar)
│   │   └── guest.blade.php
│   ├── components/
│   │   ├── sidebar.blade.php
│   │   ├── topbar.blade.php
│   │   ├── status-badge.blade.php
│   │   ├── kpi-card.blade.php
│   │   ├── page-header.blade.php
│   │   ├── card.blade.php
│   │   ├── modal.blade.php
│   │   └── pagination.blade.php
│   ├── auth/                      # Breeze + custom login/register
│   ├── dashboard/                 # role-specific dashboards
│   ├── assets/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── show.blade.php
│   │   └── partials/forms.blade.php
│   ├── assignments/
│   ├── maintenance/
│   ├── audits/
│   ├── licenses/
│   ├── reports/
│   ├── admin/
│   │   ├── users/
│   │   ├── roles/
│   │   └── settings/
│   └── pdf/                       # dompdf templates
│       ├── asset-report.blade.php
│       ├── maintenance-report.blade.php
│       ├── audit-report.blade.php
│       └── license-report.blade.php

routes/
├── web.php                        # public + authenticated routes
├── auth.php                       # Breeze auth routes
└── admin.php                      # admin-scoped routes

tests/
├── Feature/
│   ├── Auth/
│   ├── Rbac/
│   ├── AssetManagementTest.php
│   ├── AssignmentTest.php
│   ├── MaintenanceTest.php
│   ├── AuditTest.php
│   ├── LicenseTest.php
│   └── ReportTest.php
└── Unit/
    └── Services/
```

## 3. Request Flow

```
Browser → Routes → Middleware (auth + role/permission) → Controller
       → FormRequest (validate) → Service (business logic)
       → Model/Eloquent → MySQL
       → Event → Listener → Queue → Notification/Email (Redis)
       → View (Blade + Tailwind + Alpine.js) → Response
```

## 4. Cross-Cutting Concerns

| Concern | Mechanism |
|---|---|
| Authorization | Spatie roles/permissions + custom Policies |
| Validation | Form Requests |
| Audit trail | `TrackActivity` middleware + `AssetObserver` + events |
| Notifications | Laravel Notification + Mail, queued via Redis |
| Caching | Redis for dashboard KPIs & QR codes |
| Security | Eloquent (SQLi-safe), CSRF, hashed passwords, route model binding |
