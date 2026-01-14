# Phase 2: Configure Model Properties

## Purpose
Determine all model property arrays based on parsed schema.

## Property Decision Tree

### $fillable Array
```php
protected $fillable = [
    // All user_columns from Phase 1
];
```
Include: All user-editable fields, foreign keys, flags, data fields
Exclude: id, version, audit fields (*_at, *_by)

### $guard Array
```php
protected $guard = [
    'id',
    'version',
    'created_at',
    'updated_at',
    'deleted_at',
    'created_by',
    'updated_by'
];
```
Standard for all models.

### $private Array
```php
protected $private = [
    "created_at",
    "updated_at",
    "deleted_at",
    "created_by",
    "updated_by"
];
```
Exclude from REST API responses.

### $cast Array
**Load:** `@properties/cast.md` if JSON/boolean/decimal columns exist

Triggers:
- JSON column detected -> cast to 'array'
- BOOLEAN column detected -> cast to 'bool'
- DECIMAL column detected -> cast to 'float'

### $format Array
**Load:** `@properties/format.md` if nullable FKs or JSON columns exist

Triggers:
- JSON column -> 'json_encode'
- Nullable FK column -> 'convertEmptyToNull'
- Nullable numeric -> 'convertEmptyToNull'

### $with Array
**Load:** `@properties/with.md` if relationships exist

Criteria for eager loading:
- Type/category relationships (always displayed)
- Parent relationships (for hierarchy)
- NOT: hasMany collections (too expensive)

## Module Triggers

| Condition | Load Module |
|-----------|-------------|
| Any user columns | properties/fillable-guard.md |
| JSON columns | properties/cast.md |
| Nullable FKs | properties/format.md |
| Audit fields | properties/private.md (standard) |
| FK columns | properties/with.md |

## Output

Property configuration:
```yaml
properties:
  fillable: [sku, name, description, service_type_id, is_active, metadata]
  guard: [id, version, created_at, updated_at, deleted_at, created_by, updated_by]
  private: [created_at, updated_at, deleted_at, created_by, updated_by]
  cast:
    metadata: array
    is_active: bool
  format:
    metadata: json_encode
    service_type_id: convertEmptyToNull
  with: [serviceType]
```

## Next Phase
Proceed to Phase 3: Define Relationships.
