# Type Casting ($cast)

## Purpose
Convert database values to PHP types on retrieval.

## Syntax
```php
protected $cast = [
    'field_name' => 'type',
];
```

## Type Mappings

| SQL Type | Cast To | Example |
|----------|---------|---------|
| JSON | 'array' | metadata, specs, settings |
| BOOLEAN/TINYINT(1) | 'bool' | is_active, is_featured |
| DECIMAL | 'float' | price, unit_cost |
| INT/BIGINT | 'int' | quantity, sort_order |
| DATETIME | 'datetime' | scheduled_at |

## Example

```php
protected $cast = [
    'metadata' => 'array',
    'specs' => 'array',
    'is_active' => 'bool',
    'is_consumable' => 'bool',
    'unit_cost' => 'float',
    'price' => 'float',
    'sort_order' => 'int',
];
```

## When to Cast

**Always cast:**
- JSON columns -> 'array' (enables $model->metadata['key'])
- Boolean columns -> 'bool' (enables if($model->is_active))

**Optionally cast:**
- Decimals -> 'float' (for arithmetic)
- Integers -> 'int' (type safety)
- Datetimes -> 'datetime' (Carbon instance)

## Notes
- Cast happens on read (DB -> PHP)
- Inverse happens automatically on write
- Array cast enables direct array access
