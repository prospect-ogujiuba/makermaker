# Foreign Key Constraints

## Three-Part Pattern

For each foreign key:

### 1. Column Definition

```sql
`parent_id` bigint(20) NOT NULL,
```

Or nullable:

```sql
`parent_id` bigint(20) DEFAULT NULL,
```

### 2. Index

```sql
KEY `idx_{table}__parent_id` (`parent_id`),
```

### 3. Constraint

```sql
CONSTRAINT `fk_{table}__parent` FOREIGN KEY (`parent_id`) REFERENCES `{!!prefix!!}prfx_parents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
```

## Naming Conventions

| Element    | Pattern                      | Example                |
| ---------- | ---------------------------- | ---------------------- |
| Index      | `idx_{table}__{column}`      | `idx_service__type_id` |
| Constraint | `fk_{table}__{relationship}` | `fk_service__type`     |

## Cascade Behaviors

### CASCADE

Delete/update related records.

```sql
ON DELETE CASCADE ON UPDATE CASCADE
```

Use when: Child records meaningless without parent.

### RESTRICT

Prevent delete if children exist.

```sql
ON DELETE RESTRICT ON UPDATE CASCADE
```

Use when: Force explicit handling of children.

### SET NULL

Set FK to NULL when parent deleted.

```sql
ON DELETE SET NULL ON UPDATE CASCADE
```

Use when: Child records can exist independently.
Requires: Column must be nullable.

## WordPress User FK

Special case for audit columns:

```sql
CONSTRAINT `fk_{table}__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
```

Note: WordPress users table uses uppercase `ID`.
