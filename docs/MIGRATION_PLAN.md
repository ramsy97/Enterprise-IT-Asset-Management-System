# ITAMS Enterprise — Migration Plan

Migration order matters for foreign-key integrity. Migrations run in filename order.

| # | Migration | Table(s) | Purpose |
|---|---|---|---|
| 1 | `0001_01_01_000000_create_users_table` | users, password_reset_tokens, sessions | Core users + auth (Laravel default, extended later) |
| 2 | `0001_01_01_000001_create_cache_table` | cache, cache_locks | Laravel default |
| 3 | `0001_01_01_000002_create_jobs_table` | jobs, job_batches, failed_jobs | Queue worker tables |
| 4 | `2026_xx_create_permission_tables` | roles, permissions, model_has_roles, model_has_permissions, role_has_permissions | Spatie RBAC |
| 5 | `2026_xx_create_asset_categories_table` | asset_categories | Master data: categories |
| 6 | `2026_xx_create_asset_locations_table` | asset_locations | Master data: locations |
| 7 | `2026_xx_create_assets_table` | assets | Core asset registry (FK → categories, locations, users) |
| 8 | `2026_xx_create_asset_assignments_table` | asset_assignments | Assignment lifecycle (FK → assets, users) |
| 9 | `2026_xx_create_maintenance_records_table` | maintenance_records | Maintenance lifecycle (FK → assets, users) |
| 10 | `2026_xx_create_software_licenses_table` | software_licenses | License registry |
| 11 | `2026_xx_create_audit_records_table` | audit_records | Audit lifecycle (FK → assets, users) |
| 12 | `2026_xx_create_activity_logs_table` | activity_logs | Audit trail |
| 13 | `2026_xx_add_profile_fields_to_users_table` | users (alter) | department, position, phone, is_active |

## Data Integrity Notes

- `assets.asset_category_id`, `assets.asset_location_id` → `restrictOnDelete` (a category with assets cannot be deleted)
- `asset_assignments.asset_id` → `restrictOnDelete`
- `maintenance_records.asset_id` → `restrictOnDelete`
- `audit_records.asset_id` → `restrictOnDelete`
- `activity_logs.user_id` → `nullOnDelete` (keep history after user removal)
- `assets.current_holder_id` → `nullOnDelete` (snapshot, real history lives in asset_assignments)

## Rollback Strategy

- `php artisan migrate:rollback` reverses in reverse filename order, dropping FKs before parents.
- Seeders are idempotent (`updateOrCreate` for roles/permissions; category/location seeders use `firstOrCreate`).
- Demo data seeder is skippable via flag `php artisan db:seed --class=DemoDataSeeder`.
