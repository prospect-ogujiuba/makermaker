# Phase 3: Configure Features

## Purpose
Configure bulk actions, sorting, filtering based on controller capabilities.

## Feature Module Triggers

| Feature | Load Module | Condition |
|---------|-------------|-----------|
| Bulk operations | features/bulk-actions.md | Controller has bulk handlers |
| Default sorting | features/sorting.md | Always |
| Row actions | features/row-actions.md | Controller methods exist |
| Filtering | features/filtering.md | Optional |

## Bulk Actions

**Load:** `@features/bulk-actions.md`

Only include if controller has handlers:
```php
$table->setBulkActions(tr_form()->useConfirm(), [
    'delete' => 'Delete Selected',
    // Only if controller has bulkDelete()
]);
```

Default (no handlers):
```php
$table->setBulkActions(tr_form()->useConfirm(), []);
```

## Sorting

**Load:** `@features/sorting.md`

Default patterns:
- Most resources: `->setOrder('id', 'DESC')`
- Submissions: `->setOrder('created_at', 'DESC')`
- Config tables: `->setOrder('name', 'ASC')`

## Row Actions

**Load:** `@features/row-actions.md`

Based on controller methods:
- `edit` method → 'edit' action
- `show` method → 'view' action
- `destroy` method → 'delete' action

Standard: `['edit', 'view', 'delete']`

## Primary Column

Specify in setColumns second parameter:
```php
$table->setColumns([...], 'name')
```

Usually: 'name', 'title', 'subject' (first text column)

## Output

Feature configuration:
```yaml
features:
  bulk_actions: []
  ordering:
    column: id
    direction: DESC
  row_actions: [edit, view, delete]
  primary_column: name
```

## Next Phase
Proceed to Phase 4: Output Index.
