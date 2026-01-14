# Row Actions Column

## Standard Actions
Place only on primary display column.

```php
'name' => [
    'label' => 'Name',
    'sort' => true,
    'actions' => ['edit', 'view', 'delete']
]
```

## Available Actions

| Action | Controller Method | Description |
|--------|-------------------|-------------|
| edit | edit() | Edit form |
| view | show() | Detail view |
| delete | destroy() | Delete/soft delete |

## Conditional Actions

Only include actions the controller supports:
- If no `show()` method: `['edit', 'delete']`
- If no `destroy()` method: `['edit', 'view']`

## Action Requirements

**edit:**
- Controller has `edit()` method
- Links to edit form

**view:**
- Controller has `show()` method
- Links to detail view

**delete:**
- Controller has `destroy()` method
- Triggers delete confirmation

## Primary Column Selection

Select first text column as primary:
1. `name` (most common)
2. `title` (for posts/pages)
3. `subject` (for messages/submissions)
4. First VARCHAR column in fillable

## ID Column (no actions)

ID is always sortable, never has actions:
```php
'id' => [
    'label' => 'ID',
    'sort' => true
]
```
