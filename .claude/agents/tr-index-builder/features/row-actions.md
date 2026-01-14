# Row Actions

## Placement
Only on primary display column:
```php
'name' => [
    'label' => 'Name',
    'sort' => true,
    'actions' => ['edit', 'view', 'delete']
]
```

## Standard Actions

### Edit
Links to edit form.
- Requires: `edit()` controller method
- Route: `/{resource}/{id}/edit`

### View
Links to detail view.
- Requires: `show()` controller method
- Route: `/{resource}/{id}`

### Delete
Triggers delete confirmation.
- Requires: `destroy()` controller method
- Route: `DELETE /{resource}/{id}`

## Conditional Inclusion

Only include supported actions:
```php
// Controller has: edit, destroy (no show)
'actions' => ['edit', 'delete']

// Controller has: edit, show (no destroy)
'actions' => ['edit', 'view']

// Full CRUD
'actions' => ['edit', 'view', 'delete']
```

## Custom Actions

For workflow entities:
```php
// If controller has approve() method
'actions' => ['edit', 'view', 'approve', 'delete']
```

## Action Detection

Check controller_handoff.methods:
```yaml
methods:
  - index   # No action
  - add     # No action
  - create  # No action
  - edit    # → 'edit' action
  - update  # No action
  - show    # → 'view' action
  - destroy # → 'delete' action
```

## Primary Column Selection

1. First choice: `name`
2. Second: `title`
3. Third: `subject`
4. Fallback: First text column in fillable
