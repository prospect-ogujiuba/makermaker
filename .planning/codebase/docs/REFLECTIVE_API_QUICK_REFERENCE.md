# Reflective REST API - Quick Reference

## Overview

Zero-configuration REST API enhancement for all TypeRocket custom resources.

**Initialized in**: `app/MakermakerTypeRocketPlugin.php` → `initReflectiveRestApi()`

**Affects**: All 27 models automatically

---

## Query Syntax

### List Endpoints

```
GET /tr-api/rest/{resource}?{parameters}
```

### Available Parameters

| Parameter | Description | Example | Default |
|-----------|-------------|---------|---------|
| `search` | Full-text search | `?search=laser` | - |
| `{field}` | Filter by field value | `?is_active=1` | - |
| `orderby` | Sort field | `?orderby=name` | `id` |
| `order` | Sort direction | `?order=desc` | `asc` |
| `per_page` | Items per page (max 100) | `?per_page=25` | `25` |
| `page` | Page number | `?page=2` | `1` |

### Special Values

- `null` - Filter for NULL values: `?minimum_quantity=null`

---

## Common Queries

### Search Examples

```bash
# Search services
GET /tr-api/rest/services?search=installation

# Search equipment
GET /tr-api/rest/equipment?search=laser

# Search tickets
GET /tr-api/rest/tickets?search=billing
```

### Filter Examples

```bash
# Active services
GET /tr-api/rest/services?is_active=1

# Services by category
GET /tr-api/rest/services?category_id=5

# Open high-priority tickets
GET /tr-api/rest/tickets?status=open&priority=high

# Current CAD prices
GET /tr-api/rest/service-prices?currency=CAD&is_current=1
```

### Sort Examples

```bash
# Services by name
GET /tr-api/rest/services?orderby=name&order=asc

# Equipment by cost (descending)
GET /tr-api/rest/equipment?orderby=unit_cost&order=desc

# Recent tickets first
GET /tr-api/rest/tickets?orderby=created_at&order=desc
```

### Pagination Examples

```bash
# First 10 services
GET /tr-api/rest/services?per_page=10&page=1

# Next 10 services
GET /tr-api/rest/services?per_page=10&page=2

# Maximum allowed (100 per page)
GET /tr-api/rest/services?per_page=100
```

### Combined Examples

```bash
# Search + filter + sort
GET /tr-api/rest/services?search=laser&is_active=1&orderby=name

# Filter + paginate
GET /tr-api/rest/tickets?status=open&per_page=20&page=1

# Everything
GET /tr-api/rest/services?search=install&category_id=5&is_active=1&orderby=name&order=asc&per_page=25&page=1
```

---

## Response Format

### Successful Response

```json
{
  "success": true,
  "data": [ /* array of results */ ],
  "meta": {
    "total": 142,
    "per_page": 25,
    "current_page": 1,
    "last_page": 6,
    "from": 1,
    "to": 25
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Invalid filter field: password",
  "code": "BAD_REQUEST"
}
```

---

## Custom Actions (Zero-Config)

### Implementing Actions (Recommended: PHP 8 Attributes)

**Zero Configuration** - Just add `#[Action]` attribute to methods:

```php
use MakerMaker\Http\Attributes\Action;

class Service extends Model
{
    #[Action(capability: 'create', description: 'Duplicate service')]
    public function duplicate(AuthUser $user, array $params): array
    {
        $new = $this->replicate();
        $new->sku .= '-COPY';
        $new->created_by = $user->ID;
        $new->save();

        return ['success' => true, 'data' => $new];
    }

    #[Action(capability: 'update', description: 'Archive service')]
    public function archive(AuthUser $user, array $params): array
    {
        $this->is_active = 0;
        $this->save(['is_active']);

        return ['success' => true, 'message' => 'Service archived'];
    }

    #[Action(
        capability: 'update',
        description: 'Update pricing',
        requiresParams: true
    )]
    public function updatePricing(AuthUser $user, array $params): array
    {
        // Update pricing logic
        return ['success' => true, 'data' => $updatedPrices];
    }
}
```

**That's it!** Actions are automatically discovered and exposed via REST API.

### Action Attribute Parameters

```php
#[Action(
    capability: 'create',      // Policy method to check (default: inferred from method name)
    description: 'Description', // Human-readable description
    requiresParams: true,       // Whether action requires request body parameters
    requiresId: true            // Whether action needs specific record (default: true)
)]
```

### Calling Actions

```bash
POST /tr-api/rest/{resource}/{id}/actions/{action}
Content-Type: application/json

{"param1": "value1"}
```

**Examples:**

```bash
# Duplicate service (no params needed)
curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate

# Archive service
curl -X POST http://site.test/tr-api/rest/services/5/actions/archive

# Update pricing (with params)
curl -X POST http://site.test/tr-api/rest/services/5/actions/update-pricing \
  -H "Content-Type: application/json" \
  -d '{"pricing": {"tier1": 100, "tier2": 200}}'
```

### Legacy Interface (Backward Compatible)

If you prefer the old interface pattern, it still works:

```php
use MakerMaker\Http\HasRestActions;

class Service extends Model implements HasRestActions
{
    public function getRestActions(): array
    {
        return [
            'duplicate' => [
                'method' => 'actionDuplicate',
                'description' => 'Duplicate service',
                'capability' => 'create'
            ]
        ];
    }

    public function actionDuplicate(AuthUser $user, array $params): array
    {
        return ['success' => true, 'data' => $new];
    }
}
```

**Note**: Interface method takes precedence if both exist.

---

## All Resources

### Service Catalog

- `/tr-api/rest/services`
- `/tr-api/rest/service-categories`
- `/tr-api/rest/service-types`
- `/tr-api/rest/complexity-levels`
- `/tr-api/rest/service-addons`
- `/tr-api/rest/service-relationships`
- `/tr-api/rest/service-bundles`
- `/tr-api/rest/bundle-items`
- `/tr-api/rest/service-coverages`
- `/tr-api/rest/service-equipment`

### Pricing

- `/tr-api/rest/service-prices`
- `/tr-api/rest/pricing-models`
- `/tr-api/rest/pricing-tiers`
- `/tr-api/rest/price-records`
- `/tr-api/rest/currency-rates`

### Equipment & Deliverables

- `/tr-api/rest/equipment`
- `/tr-api/rest/deliverables`
- `/tr-api/rest/service-deliverables`
- `/tr-api/rest/delivery-methods`
- `/tr-api/rest/service-delivery`
- `/tr-api/rest/coverage-areas`

### Support

- `/tr-api/rest/tickets`
- `/tr-api/rest/ticket-categories`
- `/tr-api/rest/ticket-comments`
- `/tr-api/rest/ticket-attachments`
- `/tr-api/rest/teams`
- `/tr-api/rest/contact-submissions`

---

## Security

### Filterable Fields

Only fields in model's `$fillable` AND NOT in `$guard` can be filtered.

**Protected by default:**
- `id`
- `version`
- `created_at`, `updated_at`, `deleted_at`
- `created_by`, `updated_by`
- `password`, `token`, `secret`, `hash`

### Authorization

All queries check model policy `can('index')` before execution.

Actions check specified capability (e.g., `can('create')`, `can('update')`).

---

## Troubleshooting

### "Resource not found"

Resource must be registered via `mm_create_custom_resource()`.

### "Invalid filter field"

Field must be in `$fillable` and NOT in `$guard`.

### "Unauthorized"

User lacks required capability. Check model policy.

### Search returns nothing

Check model has searchable text fields or implement `getSearchableFields()`.

---

## Files Reference

**Core Implementation:**
- `app/Http/ReflectiveRestWrapper.php` - Main wrapper (260 lines)
- `app/Http/ReflectiveQueryBuilder.php` - Query builder (350 lines)
- `app/Http/ActionDispatcher.php` - Action handler (152 lines)
- `app/Http/HasRestActions.php` - Action interface (36 lines)

**Integration:**
- `app/MakermakerTypeRocketPlugin.php` - Initialization (1 line: `ReflectiveRestWrapper::init()`)

**Tests:**
- `tests/Integration/ReflectiveRestApiTest.php` - 20+ test cases

**Documentation:**
- `database/docs/REFLECTIVE_REST_API.md` - Architecture
- `database/docs/ROLLOUT.md` - Deployment guide
- `database/docs/REFLECTIVE_API_QUICK_REFERENCE.md` - This file

---

## curl Examples Cheat Sheet

```bash
# Basic list
curl http://site.test/tr-api/rest/services

# Search
curl "http://site.test/tr-api/rest/services?search=laser"

# Filter single field
curl "http://site.test/tr-api/rest/services?is_active=1"

# Filter multiple fields
curl "http://site.test/tr-api/rest/services?category_id=5&is_featured=1"

# Sort
curl "http://site.test/tr-api/rest/services?orderby=name&order=desc"

# Paginate
curl "http://site.test/tr-api/rest/services?per_page=10&page=2"

# Combined
curl "http://site.test/tr-api/rest/services?search=install&is_active=1&orderby=name&per_page=20"

# Execute action
curl -X POST http://site.test/tr-api/rest/services/1/actions/duplicate

# Action with params
curl -X POST http://site.test/tr-api/rest/services/1/actions/duplicate \
  -H "Content-Type: application/json" \
  -d '{"notes":"test"}'
```

---

## Development Tips

### Add Custom Searchable Fields

```php
class Service extends Model
{
    public function getSearchableFields(): array
    {
        return ['name', 'sku', 'short_desc', 'long_desc', 'custom_field'];
    }
}
```

### Test Queries

Enable debug mode to see query errors:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### Check Available Filters

Model's `$fillable` minus `$guard` = filterable fields.

---

**For complete details**: See `database/docs/REFLECTIVE_REST_API.md`
