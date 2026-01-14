# Fillable and Guard Properties

## $fillable Array

Mass-assignable fields - include:
- User-editable fields (name, description, etc.)
- Foreign keys (service_type_id, category_id)
- Boolean flags (is_active, is_featured)
- JSON data fields (metadata, specs)

```php
protected $fillable = [
    'sku',
    'name',
    'description',
    'service_type_id',
    'category_id',
    'is_active',
    'metadata',
];
```

## $guard Array

Protected fields - always include:
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

## Rules

| Column Type | Fillable | Guard |
|-------------|----------|-------|
| Primary key (id) | NO | YES |
| Version | NO | YES |
| Timestamps (*_at) | NO | YES |
| Audit (*_by) | NO | YES |
| User data | YES | NO |
| Foreign keys | YES | NO |
| Boolean flags | YES | NO |
