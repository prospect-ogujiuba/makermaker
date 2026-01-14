# Phase 2: Select Columns

## Purpose
Apply column selection rules and load appropriate column modules.

## Column Priority Order

1. Primary column (with actions)
2. Identifier columns (sku, slug, code)
3. Relationship columns (*_id)
4. Boolean columns (is_*, has_*)
5. Enum columns (status, type)
6. Numeric columns (price, quantity)
7. Timestamp columns (*_at)
8. Audit columns (created_by)
9. ID column (always last)

## Column Module Triggers

| Column Type | Load Module |
|-------------|-------------|
| Primary text | columns/text.md |
| *_at, *_date | columns/date.md |
| is_*, boolean | columns/boolean.md |
| *_id FK | columns/relationship.md |
| Row actions | columns/actions.md |

## Selection Rules

**Maximum 8-10 columns** for readability.

**Always include:**
- Primary column (name, title, subject)
- ID column

**Exclude:**
- JSON fields (specs, metadata)
- TEXT/LONGTEXT fields
- Internal flags

**Priority selection:**
If >10 candidates, select highest priority.

## Column Configuration Pattern

For each selected column:
```php
'column_name' => [
    'label' => 'Display Label',
    'sort' => true,           // If sortable
    'actions' => [...],       // Only on primary
    'callback' => function()  // If formatting needed
]
```

## Output

Selected columns with configurations:
```yaml
columns:
  - name: name
    config:
      label: Name
      sort: true
      actions: [edit, view, delete]
  - name: sku
    config:
      label: SKU
      sort: true
      callback: code_wrap
  - name: type_id
    config:
      label: Type
      callback: relationship
  - name: id
    config:
      label: ID
      sort: true
```

## Next Phase
Proceed to Phase 3: Configure Features.
