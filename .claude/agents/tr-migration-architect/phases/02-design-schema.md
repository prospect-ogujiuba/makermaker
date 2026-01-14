# Phase 2: Design Schema

## Purpose
Design complete table structure with proper types, indexes, and constraints.

## Module Triggers

| Design Element | Load Module |
|----------------|-------------|
| Table naming | schema/table-naming.md |
| Audit columns | schema/standard-columns.md |
| FK columns | schema/foreign-keys.md |
| Index design | schema/indexes.md |
| CHECK constraints | schema/constraints.md |
| Column types | patterns/column-types.md |
| Defaults | patterns/nullable-defaults.md |

## Schema Design Steps

### 1. Table Name
**Load:** `@schema/table-naming.md`

Pattern: `{!!prefix!!}srvc_{entity_plural}`

### 2. Standard Columns
**Load:** `@schema/standard-columns.md`

Always include:
- id (PRIMARY KEY)
- version (optimistic locking)
- created_at, updated_at, deleted_at
- created_by, updated_by

### 3. Custom Columns
**Load:** `@patterns/column-types.md`

Map each field to SQL type.

### 4. Foreign Keys
**Load:** `@schema/foreign-keys.md`

For each belongsTo relationship:
- Add FK column
- Plan index
- Define constraint

### 5. Indexes
**Load:** `@schema/indexes.md`

Plan:
- FK indexes (required)
- Unique indexes (slug, sku)
- Query indexes (is_active, status)
- Composite indexes (common query patterns)

### 6. Constraints
**Load:** `@schema/constraints.md`

Add CHECK constraints for:
- Positive values
- Range validation
- Date ordering

## Output

Complete schema design:
```yaml
schema:
  table: srvc_services
  columns:
    - name: id
      definition: "bigint(20) NOT NULL AUTO_INCREMENT"
    # ... all columns
  indexes:
    - name: idx_service__type_id
      columns: [type_id]
  constraints:
    - name: fk_service__type
      definition: "FOREIGN KEY..."
```

## Next Phase
Proceed to Phase 3: Generate Migration.
