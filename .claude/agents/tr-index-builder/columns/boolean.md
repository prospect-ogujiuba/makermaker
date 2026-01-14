# Boolean Column Types

## Bootstrap Icons (preferred)
For is_*, has_*, can_* columns.

```php
'is_active' => [
    'label' => 'Active',
    'sort' => true,
    'callback' => function($value) {
        return $value
            ? "<i class='bi bi-check' style='color: green;'></i>"
            : "<i class='bi bi-x' style='color: red;'></i>";
    }
]
```

## Badge Style
Alternative with text labels.

```php
'is_active' => [
    'label' => 'Active',
    'sort' => true,
    'callback' => function($value) {
        return $value
            ? '<span class="badge badge-success">Yes</span>'
            : '<span class="badge badge-secondary">No</span>';
    }
]
```

## Custom Labels
When Yes/No doesn't fit.

```php
'is_consumable' => [
    'label' => 'Consumable',
    'sort' => true,
    'callback' => function($value) {
        return $value
            ? '<span class="badge badge-info">Consumable</span>'
            : '<span class="badge badge-outline">Durable</span>';
    }
]
```

## Common Boolean Fields

| Field | Label | True | False |
|-------|-------|------|-------|
| is_active | Active | Yes/Green | No/Gray |
| is_featured | Featured | Yes | No |
| is_consumable | Consumable | Yes | No |
| has_warranty | Warranty | Yes | No |

## Consistent Icons

- True: `bi-check` (green)
- False: `bi-x` (red)
