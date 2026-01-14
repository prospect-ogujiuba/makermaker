# Index Patterns

## Required Indexes

### Foreign Key Indexes
Every FK column must have an index:
```sql
KEY `idx_{table}__{fk_column}` (`{fk_column}`),
```

### Audit Indexes
Standard on every table:
```sql
KEY `idx_{table}__deleted_at` (`deleted_at`),
KEY `idx_{table}__created_by` (`created_by`),
KEY `idx_{table}__updated_by` (`updated_by`),
```

## Unique Indexes

### SKU/Slug
```sql
UNIQUE KEY `uq_{table}__sku` (`sku`),
UNIQUE KEY `uq_{table}__slug` (`slug`),
```

### Composite Unique
```sql
UNIQUE KEY `uq_service_equipment` (`service_id`, `equipment_id`),
```

## Query Optimization Indexes

### Boolean Flags
```sql
KEY `idx_{table}__is_active` (`is_active`),
KEY `idx_{table}__is_featured` (`is_featured`),
```

### Status/Enum
```sql
KEY `idx_{table}__status` (`status`),
```

### Composite for Common Queries
```sql
KEY `idx_{table}__active_lookup` (`is_active`, `type_id`, `deleted_at`),
```

## Index Naming

| Type | Pattern | Example |
|------|---------|---------|
| Regular | `idx_{table}__{column(s)}` | `idx_service__type_id` |
| Unique | `uq_{table}__{column(s)}` | `uq_service__sku` |
| Composite | `idx_{table}__{purpose}` | `idx_service__active_lookup` |

## Performance Notes

- Index FK columns (prevents table scans on JOINs)
- Composite index column order matters
- Don't over-index (write performance cost)
- JSON columns cannot be indexed
