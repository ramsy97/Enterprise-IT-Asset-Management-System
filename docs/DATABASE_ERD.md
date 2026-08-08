# ITAMS Enterprise — Database ERD

## 1. Entities & Relationships

```
┌─────────────────────┐      ┌──────────────────────┐      ┌─────────────────────┐
│     users           │      │    assets            │      │ asset_categories    │
├─────────────────────┤      ├──────────────────────┤      ├─────────────────────┤
│ id                  │      │ id                   │      │ id                  │
│ name                │      │ asset_code (unique)  │      │ name (unique)       │
│ email (unique)      │      │ asset_name           │      │ description         │
│ password            │      │ asset_category_id ───┼──┐   │ created_at          │
│ department          │      │ brand                │  │   └─────────────────────┘
│ position            │      │ model                │  │
│ phone               │      │ serial_number        │  │
│ is_active           │      │ purchase_date        │  │
│ timestamps          │      │ purchase_price       │  │
└───────┬─────────────┘      │ asset_location_id ───┼──┼───┐
        │                    │ status               │  │   │
        │  (Spatie RBAC      │ warranty_expires_at   │  │   │
        │   roles/permissions│ qr_path              │  │   │
        │   via model_has_*) │ current_holder_id ────┼──┼───┼───┐
        │                    │ notes                │  │   │   │
        │                    │ timestamps            │  │   │   │
        │                    └──────────────────────┘  │   │   │
        │                       ┌──────────────────────┘   │   │
        │                       │  ┌────────────────────────┘   │
        │                       ▼  ▼                            ▼
        │  ┌───────────────────────────────┐  ┌─────────────────────────────┐
        │  │  asset_locations              │  │  asset_assignments          │
        │  ├───────────────────────────────┤  ├─────────────────────────────┤
        │  │ id                            │  │ id                          │
        │  │ name (unique)                 │  │ asset_id ───────────────────┤
        │  │ building / floor / room       │  │ employee_id (users.id) ─────┤
        │  │ city                          │  │ assigned_by (users.id)       │
        │  │ timestamps                    │  │ approved_by (users.id)       │
        │  └───────────────────────────────┘  │ request_date                │
        │                                    │ approved_at / rejected_at    │
        │                                    │ assigned_date                │
        │                                    │ return_date                  │
        │                                    │ status (pending/approved/    │
        │                                    │         rejected/returned)   │
        │                                    │ notes                        │
        │                                    │ timestamps                   │
        │                                    └─────────────────────────────┘

┌────────────────────────────┐   ┌──────────────────────────────┐
│ maintenance_records        │   │  audit_records               │
├────────────────────────────┤   ├──────────────────────────────┤
│ id                         │   │ id                           │
│ asset_id ──────────────────┤   │ asset_id ────────────────────┤
│ scheduled_date             │   │ audit_batch_id               │
│ completed_date (nullable)  │   │ audited_by (users.id) ───────┤
│ type (preventive/repair/   │   │ audit_date                   │
│       replacement)         │   │ status (verified/            │
│ technician (users.id) ─────┤   │         need_repair/missing)  │
│ cost (decimal)             │   │ condition                    │
│ description                │   │ findings / notes             │
│ status (scheduled/in       │   │ location_match (bool)        │
│         progress/          │   │ evidence_path                │
│         completed/         │   │ timestamps                   │
│         cancelled)         │   └──────────────────────────────┘
│ timestamps                 │
└────────────────────────────┘

┌────────────────────────────┐   ┌──────────────────────────────┐
│ software_licenses          │   │ activity_logs                │
├────────────────────────────┤   ├──────────────────────────────┤
│ id                         │   │ id                           │
│ software_name              │   │ user_id (nullable)           │
│ vendor                     │   │ log_type (asset/maintenance/ │
│ license_key (encrypted)    │   │   audit/license/user/auth/…) │
│ total_licenses (int)       │   │ description                  │
│ used_licenses (int)        │   │ properties (json)            │
│ purchase_date              │   │ ip_address                   │
│ purchase_cost              │   │ created_at                   │
│ expires_at                 │   └──────────────────────────────┘
│ timestamps                 │
└────────────────────────────┘

┌────────────────────────────┐
│ notifications (Laravel)    │
├────────────────────────────┤
│ id, type, notifiable_id    │
│ data (json), read_at, …    │
└────────────────────────────┘

Spatie RBAC tables:
┌────────────────────────────┐   ┌──────────────────────────────┐
│ roles                      │   │ permissions                  │
│ id, name, guard_name, …    │   │ id, name, guard_name, …      │
└────────────────────────────┘   └──────────────────────────────┘
   role_has_permissions             model_has_roles
   model_has_permissions            (user_id, role_id / permission_id)
```

## 2. Relationship Summary

| Model A | Relation | Model B | Notes |
|---|---|---|---|
| User | `hasMany` | AssetAssignment (employee) | assignments as employee |
| User | `hasMany` | AssetAssignment (assignedBy) | assignments created by |
| User | `hasMany` | AuditRecord | audits performed by |
| User | `hasMany` | MaintenanceRecord (technician) | assigned technician |
| Asset | `belongsTo` | AssetCategory | category |
| Asset | `belongsTo` | AssetLocation | location |
| Asset | `hasMany` | AssetAssignment | full history |
| Asset | `hasOne` | AssetAssignment | current active assignment |
| Asset | `hasMany` | MaintenanceRecord | maintenance history |
| Asset | `hasMany` | AuditRecord | audit history |
| Asset | `belongsTo` | User (currentHolder) | current holder snapshot |

## 3. Status Enums

- **AssetStatus**: `available`, `assigned`, `maintenance`, `retired`
- **AssignmentStatus**: `pending`, `approved`, `rejected`, `returned`
- **MaintenanceType**: `preventive`, `repair`, `replacement`
- **MaintenanceStatus**: `scheduled`, `in_progress`, `completed`, `cancelled`
- **AuditStatus**: `verified`, `need_repair`, `missing`

## 4. Indexing & Integrity

- Unique: `assets.asset_code`, `asset_categories.name`, `asset_locations.name`, `users.email`
- Composite indexes on high-query tables (`assets.asset_category_id`, `assets.status`, `assets.warranty_expires_at`, `maintenance_records.scheduled_date`)
- Foreign keys with `restrictOnDelete` on business-critical relations; `cascadeOnDelete` only for logs/history
- `license_key` stored encrypted (`Crypt::encryptString`)
