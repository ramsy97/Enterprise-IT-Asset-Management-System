# ITAMS Enterprise — Development Roadmap

| Phase | Scope | Deliverable | Status |
|---|---|---|---|
| 1 | Project initialization | Laravel 12 app, MySQL, Redis, Breeze, Tailwind, Git | Done |
| 2 | Database architecture | Migrations, models, relationships, seeders | In progress |
| 3 | Authentication & authorization | Login, roles, permissions, middleware, policies | Planned |
| 4 | Core asset management | Asset CRUD, category, location, QR code | Planned |
| 5 | Asset lifecycle | Assignment, maintenance, warranty, audit | Planned |
| 6 | Dashboard & reporting | Analytics, charts, PDF/Excel export | Planned |
| 7 | Notification system | Email notifications, queue worker, Redis | Planned |
| 8 | Testing & optimization | Feature tests, authorization tests, security review | Planned |

## Detailed Task Breakdown

### Phase 2 — Database
- [x] Spatie permission migrations published
- [ ] Create all domain migrations (assets, assignments, maintenance, licenses, audits, activity_logs)
- [ ] Extend users table (department, position, phone, is_active)
- [ ] Create models + relationships
- [ ] Create factories
- [ ] Create seeders (roles/permissions, admin user, categories, locations, demo assets)

### Phase 3 — Auth & RBAC
- [ ] Role-based login redirect (`/admin`, `/staff`, `/manager` dashboards)
- [ ] `role` middleware alias
- [ ] Permission middleware usage in routes
- [ ] Policies per model
- [ ] User + Role management (admin only)

### Phase 4 — Core Asset Module
- [ ] Asset code generator (`IT-CAT-NNNN`)
- [ ] Asset CRUD (Form Requests + AssetService)
- [ ] Category CRUD, Location CRUD
- [ ] QR generation on create (SimpleQrCode, cached)
- [ ] QR public scan page (asset detail + current holder + maintenance history)
- [ ] Search / filter / pagination

### Phase 5 — Lifecycle
- [ ] Assignment request → manager approval → assign → history + auto status update
- [ ] Maintenance schedule, calendar, technician, cost, types
- [ ] Warranty auto-calc + expiring-soon list
- [ ] Audit create → scan QR → condition check → evidence upload → report

### Phase 6 — Dashboard & Reporting
- [ ] Executive dashboard (KPIs + Chart.js: distribution, status, maintenance trend, warranty timeline)
- [ ] Staff dashboard, Manager dashboard
- [ ] Report center (asset / maintenance / audit / license)
- [ ] PDF export (DomPDF), Excel export (Laravel Excel)

### Phase 7 — Notifications
- [ ] Warranty expiring notification (30-day window) + scheduled command
- [ ] Maintenance reminder notification + scheduled command
- [ ] Assignment status notification
- [ ] Redis queue processing + in-app notification center

### Phase 8 — Testing & Optimization
- [ ] Feature tests (assets, assignments, maintenance, audits, licenses, reports)
- [ ] RBAC tests (role isolation)
- [ ] N+1 review, index review, cache review
- [ ] Security review (mass assignment, encryption, authorization)
