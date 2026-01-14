# Phase 1: Analyze Handoffs

## Purpose
Extract column and feature requirements from model and controller handoffs.

## Input Sources

### model_handoff.yaml
Extract:
- `class`: Model class for tr_table()
- `namespace`: Full namespace for use statement
- `fillable`: Available display columns
- `relationships`: For accessor columns
- `with`: Default eager loads

### controller_handoff.yaml
Extract:
- `methods`: Available actions (edit, show, destroy)
- `helpers_used`: Feature toggles
  - DeleteHelper → soft delete filter
  - BulkActionHelper → bulk operations

## Column Candidate Analysis

From fillable, categorize each field:
```yaml
columns:
  - name: name
    type: text
    primary: true      # First text column
  - name: sku
    type: identifier   # Needs <code> callback
  - name: type_id
    type: relationship # Needs accessor callback
  - name: is_active
    type: boolean      # Needs icon callback
  - name: created_at
    type: timestamp    # Needs date callback
```

## Column Type Detection

| Pattern | Type |
|---------|------|
| *_id (not id) | relationship |
| is_*, has_*, can_* | boolean |
| *_at, *_date | timestamp |
| sku, slug, code | identifier |
| price, cost, *_amount | currency |
| JSON, TEXT | exclude |
| created_by, updated_by | audit |
| id | id_column |
| Other VARCHAR | text |

## Feature Detection

| Controller Element | Feature |
|--------------------|---------|
| destroy method | delete row action |
| edit method | edit row action |
| show method | view row action |
| DeleteHelper | soft delete filter |
| Bulk* methods | bulk actions |

## Output

Analysis result:
```yaml
analysis:
  model:
    class: Equipment
    namespace: MakerMaker\Models
  columns:
    candidates: [name, sku, type_id, is_active, created_at, id]
    excluded: [metadata, specs, description]
  features:
    row_actions: [edit, view, delete]
    bulk_actions: []
    soft_delete: true
```

## Next Phase
Proceed to Phase 2: Select Columns.
