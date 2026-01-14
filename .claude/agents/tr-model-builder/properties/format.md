# Format Transformations ($format)

## Purpose
Transform PHP values before storage in database.

## Syntax
```php
protected $format = [
    'field_name' => 'transformation',
];
```

## Transformations

| Transformation | Use Case | Effect |
|----------------|----------|--------|
| 'json_encode' | Array/object fields | PHP array -> JSON string |
| 'convertEmptyToNull' | Nullable fields | Empty string -> NULL |

## Examples

```php
protected $format = [
    // JSON encoding for array fields
    'metadata' => 'json_encode',
    'specs' => 'json_encode',
    'settings' => 'json_encode',

    // Empty-to-null for optional foreign keys
    'parent_id' => 'convertEmptyToNull',
    'service_type_id' => 'convertEmptyToNull',
    'category_id' => 'convertEmptyToNull',

    // Empty-to-null for optional numerics
    'unit_cost' => 'convertEmptyToNull',
    'quantity' => 'convertEmptyToNull',
];
```

## convertEmptyToNull

Critical for:
- Nullable foreign keys (prevents "0" being inserted)
- Nullable numeric fields (prevents "" errors)
- Optional decimal fields

Form submits empty string "" for blank fields. Without this:
- FK constraint fails (no record with id=0)
- Integer column errors (can't cast "" to int)

## json_encode

Pair with $cast['field'] = 'array':
- Write: PHP array -> JSON string (via $format)
- Read: JSON string -> PHP array (via $cast)
