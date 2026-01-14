# Phase 1: Parse Migration Handoff

## Purpose
Extract schema information from migration_handoff.yaml to inform model configuration.

## Input Processing

Parse the migration handoff for:
```yaml
schema:
  entity: ""           # PascalCase entity name
  table: ""            # Full table name (srvc_{plural})

columns:
  - name: ""           # Column name
    type: ""           # SQL type (VARCHAR, BIGINT, JSON, etc.)
    primary: false
    auto_increment: false
    nullable: false
    unique: false
    default: null
    foreign_key: null  # Reference if FK

foreign_keys:
  - column: ""         # FK column
    references: ""     # Target table.column
    on_delete: ""      # CASCADE | RESTRICT | SET NULL

decisions: []          # Migration architectural decisions
discovery_hints: []    # Patterns to consider
```

## Column Classification

**System columns** (never fillable):
- id (primary key)
- version (optimistic locking)
- created_at, updated_at, deleted_at (timestamps)
- created_by, updated_by (audit trails)

**User columns** (fillable candidates):
- All other columns from migration

## Type Mapping

| SQL Type | PHP Type | Cast | Format |
|----------|----------|------|--------|
| VARCHAR | string | - | - |
| TEXT | string | - | - |
| BIGINT | int | int | - |
| INT | int | int | - |
| DECIMAL | float | float | - |
| BOOLEAN | bool | bool | - |
| JSON | array | array | json_encode |
| DATETIME | string | datetime | - |
| DATE | string | - | - |

## FK Detection

Identify foreign keys by:
1. Explicit `foreign_key` property in column
2. Column name ending in `_id`
3. `foreign_keys` section references

## Output

Produce intermediate state:
```yaml
parsed:
  entity: ServiceType
  table: srvc_service_types
  system_columns: [id, version, created_at, updated_at, deleted_at, created_by, updated_by]
  user_columns: [{name, type, nullable, unique, default, is_fk, fk_target}]
  foreign_keys: [{column, target_table, target_column, on_delete}]
  decisions_from_migration: []
```

## Next Phase
Proceed to Phase 2: Configure Properties with parsed schema.
