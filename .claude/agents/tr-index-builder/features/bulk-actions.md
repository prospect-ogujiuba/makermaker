# Bulk Actions

## Default (No Handlers)
```php
$table->setBulkActions(tr_form()->useConfirm(), []);
```

## With Actions
Only if controller has corresponding handlers.

```php
$table->setBulkActions(tr_form()->useConfirm(), [
    'delete' => 'Delete Selected',
    'activate' => 'Activate Selected',
    'deactivate' => 'Deactivate Selected'
]);
```

## Common Bulk Actions

| Key | Label | Controller Method |
|-----|-------|-------------------|
| delete | Delete Selected | bulkDelete() |
| activate | Activate Selected | bulkActivate() |
| deactivate | Deactivate Selected | bulkDeactivate() |
| mark_resolved | Mark Resolved | bulkMarkResolved() |

## Contact Submissions Example
```php
$table->setBulkActions(tr_form()->useConfirm(), [
    'mark_in_progress' => 'Mark In Progress',
    'mark_resolved' => 'Mark Resolved',
    'delete' => 'Delete'
]);
```

## Rules

1. **Only include if controller supports**
   - Check for bulk handler methods
   - Don't assume capabilities

2. **Naming conventions**
   - Keys: snake_case (matches method)
   - Labels: Title Case

3. **Always use confirm**
   - `tr_form()->useConfirm()` is required
   - Prevents accidental operations

4. **Common includes**
   - Always include 'delete' if soft delete supported
   - Status transitions for workflow entities
