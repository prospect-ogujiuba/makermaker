# Nullable and Default Patterns

## Required Fields (NOT NULL)

Use for:
- Name/title (core identity)
- Required relationships (type_id)
- Boolean flags with known default
- Timestamps (created_at, updated_at)
- Audit fields (created_by, updated_by)

```sql
`name` varchar(64) NOT NULL,
`type_id` bigint(20) NOT NULL,
`is_active` tinyint(1) NOT NULL DEFAULT 1,
```

## Optional Fields (DEFAULT NULL)

Use for:
- Description/notes
- Optional relationships
- Optional dates
- Extended attributes
- soft delete marker

```sql
`description` text DEFAULT NULL,
`parent_id` bigint(20) DEFAULT NULL,
`deleted_at` datetime DEFAULT NULL,
`metadata` json DEFAULT NULL,
```

## Default Values

### Boolean Defaults
```sql
-- Default active
`is_active` tinyint(1) NOT NULL DEFAULT 1,

-- Default inactive
`is_featured` tinyint(1) NOT NULL DEFAULT 0,
```

### Numeric Defaults
```sql
-- Zero default
`quantity` int(11) NOT NULL DEFAULT 0,

-- Version tracking
`version` int(11) NOT NULL DEFAULT 1,
```

### String Defaults
```sql
-- Currency code
`currency` char(3) NOT NULL DEFAULT 'CAD',

-- Unit of measure
`unit` varchar(16) DEFAULT 'each',
```

### Enum Defaults
```sql
`status` enum('draft','active') NOT NULL DEFAULT 'draft',
```

### Timestamp Defaults
```sql
-- Auto-set on create
`created_at` datetime NOT NULL DEFAULT current_timestamp(),

-- Auto-update
`updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
```

## Decision Guide

| Scenario | Nullable | Default |
|----------|----------|---------|
| Core identity | NO | None |
| Optional data | YES | NULL |
| Boolean flag | NO | 0 or 1 |
| Auto-generated | NO | Expression |
| Optional FK | YES | NULL |
| Required FK | NO | None |
