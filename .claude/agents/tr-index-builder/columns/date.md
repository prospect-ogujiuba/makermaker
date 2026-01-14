# Date Column Types

## Timestamp Column
For created_at, updated_at.

```php
'created_at' => [
    'label' => 'Created',
    'sort' => true,
    'callback' => function($value) {
        return date('M j, Y g:i A', strtotime($value));
    }
]
```

## Date Only
For date fields without time.

```php
'purchase_date' => [
    'label' => 'Purchased',
    'sort' => true,
    'callback' => function($value) {
        return $value
            ? date('M j, Y', strtotime($value))
            : '<span class="text-muted">N/A</span>';
    }
]
```

## Relative Date
For "time ago" display.

```php
'last_activity' => [
    'label' => 'Last Active',
    'sort' => true,
    'callback' => function($value) {
        return human_time_diff(strtotime($value)) . ' ago';
    }
]
```

## Date Formats

| Format | Output |
|--------|--------|
| 'M j, Y' | Jan 15, 2026 |
| 'M j, Y g:i A' | Jan 15, 2026 3:45 PM |
| 'Y-m-d' | 2026-01-15 |
| 'F j, Y' | January 15, 2026 |

## Standard Format
Use `'M j, Y g:i A'` for consistency.
