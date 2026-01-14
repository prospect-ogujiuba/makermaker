# Default Sorting

## Syntax
```php
->setOrder('column_name', 'direction')
```

## Direction
- `'ASC'` - Ascending (A-Z, oldest first)
- `'DESC'` - Descending (Z-A, newest first)

## Common Patterns

### Newest First (Default)
For most CRUD resources.
```php
->setOrder('id', 'DESC')
```

### Alphabetical
For configuration/lookup tables.
```php
->setOrder('name', 'ASC')
```

### Recent Activity
For submissions, logs, messages.
```php
->setOrder('created_at', 'DESC')
```

### Priority-Based
For tasks, issues, queue items.
```php
->setOrder('priority', 'ASC')
```

### Custom Date
For scheduled items.
```php
->setOrder('effective_date', 'DESC')
```

## Decision Logic

| Entity Type | Sort Column | Direction |
|-------------|-------------|-----------|
| Most resources | id | DESC |
| Contact forms | created_at | DESC |
| Submissions | created_at | DESC |
| Config tables | name | ASC |
| Lookup tables | name | ASC |
| Historical | date field | DESC |
| Workflow items | status/priority | ASC |

## Full Chain
```php
$table->setColumns([
    // columns
], 'primary_column')->setOrder('id', 'DESC')->render();
```
