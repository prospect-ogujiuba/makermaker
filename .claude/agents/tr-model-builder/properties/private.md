# REST API Privacy ($private)

## Purpose
Exclude fields from REST API responses.

## Standard Configuration
```php
protected $private = [
    "created_at",
    "updated_at",
    "deleted_at",
    "created_by",
    "updated_by"
];
```

## What to Exclude

**Always exclude:**
- Audit timestamps (created_at, updated_at, deleted_at)
- Audit user IDs (created_by, updated_by)

**Sometimes exclude:**
- Internal flags (is_migrated, legacy_id)
- Sensitive data (api_key, password_hash)
- Large fields rarely needed (full_html, raw_data)

## REST API Behavior

ReflectiveRestWrapper uses $private to filter responses:
```php
// Fields in $private are stripped from:
GET /tr-api/rest/services          // List
GET /tr-api/rest/services/{id}     // Detail
```

## Example: Extended Privacy

```php
protected $private = [
    // Standard audit fields
    "created_at",
    "updated_at",
    "deleted_at",
    "created_by",
    "updated_by",
    // Sensitive data
    "api_key",
    "internal_notes",
];
```

## Notes
- $private only affects REST API responses
- Internal PHP code can still access all fields
- Does NOT affect form/admin views
