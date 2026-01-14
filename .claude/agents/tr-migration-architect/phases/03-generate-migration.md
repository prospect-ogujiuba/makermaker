# Phase 3: Generate Migration

## Purpose
Generate SQL migration file from schema design.

## File Structure

```sql
-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_{table}` (
  -- Primary key
  `id` bigint(20) NOT NULL AUTO_INCREMENT,

  -- Custom columns
  ...

  -- Audit columns
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,

  -- Primary key
  PRIMARY KEY (`id`),

  -- Unique constraints
  ...

  -- Indexes
  KEY `idx_{table}__deleted_at` (`deleted_at`),
  ...

  -- Foreign key constraints
  CONSTRAINT `fk_{table}__created_by` FOREIGN KEY (`created_by`)
    REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  ...

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='{description}';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_{table}`;
```

## SQL Generation Rules

### Column Order
1. id (primary key)
2. Custom columns (alphabetical or logical)
3. version
4. Timestamp columns
5. Audit user columns

### Index Order
1. UNIQUE KEY constraints
2. KEY for FK columns
3. KEY for query columns
4. Composite KEY indexes

### Constraint Order
1. CHECK constraints
2. FOREIGN KEY constraints

## File Naming

Pattern: `{timestamp}.create_{table}_table.sql`
- Timestamp: Unix timestamp (10 digits)
- Table: snake_case plural

## Output Path

`database/migrations/{filename}`

## Validation Checklist

Before writing file:
- [ ] {!!prefix!!} used everywhere
- [ ] All audit columns present
- [ ] All FK columns have indexes
- [ ] Up and Down sections complete
- [ ] Valid SQL syntax

## Next Phase
Proceed to Phase 4: Create Handoff.
