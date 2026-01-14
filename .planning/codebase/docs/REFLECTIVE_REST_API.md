# Reflective REST API Architecture

## Overview

This document describes the zero-configuration reflective REST API wrapper that automatically enhances all TypeRocket Pro v6 custom resources with search, filtering, sorting, pagination, and action capabilities.

**Design Goal**: ZERO per-model configuration beyond existing `mm_create_custom_resource()` calls.

---

## TypeRocket REST Architecture Discovery

### How TypeRocket Handles REST Endpoints

1. **Route Registration**
   - TypeRocket registers a catch-all route in `System::loadRoutes()`:
     ```php
     Route::new()->get()->put()->post()->delete()
         ->match('tr-api/rest/([^/]+)/?([^/]+)?', ['resource', 'id'])
         ->do([RestController::class, 'rest']);
     ```

2. **RestController Flow**
   - `RestController::rest($resource, Request $request, $id)` receives all REST requests
   - Maps HTTP methods to controller actions:
     - GET (no ID) → `indexRest()`
     - GET (with ID) → `showRest()`
     - POST → `create()`
     - PUT → `update()`
     - DELETE → `destroy()`
   - Looks up controller from `Registry::getCustomResource($resource)`
   - Dispatches to appropriate controller method via Responder

3. **Resource Registration**
   - `mm_create_custom_resource()` calls `Registry::addCustomResource($slug, ['controller' => $fqcn])`
   - Slug format: pluralized kebab-case (e.g., `services`, `pricing-tiers`)
   - Registry stores mapping: `$slug => ['controller' => $fqcn]`

4. **Default Controller Methods**
   - Controllers extend `TypeRocket\Controllers\Controller`
   - Methods receive dependency injection: `AuthUser`, `Response`, validated fields, models
   - Standard CRUD methods handle both REST and admin requests
   - No dedicated REST methods exist by default (`indexRest()`, `showRest()` don't exist)

### Current Limitations

- No built-in search capability across resources
- No field filtering (e.g., `?category_id=5`)
- No relationship filtering (e.g., `?category.slug=residential`)
- No pagination parameters
- No sorting/ordering
- No custom action endpoints
- Controllers must explicitly detect REST vs admin requests

---

## Design Decisions

### 1. Hook Strategy: Action-Based Interception

**Decision**: Use WordPress `parse_request` hook to intercept and enhance REST requests BEFORE TypeRocket's RestController processes them.

**Reasoning**:
- TypeRocket route matching happens in `parse_request` hook
- We can check for REST endpoint patterns early
- Allow normal CRUD to pass through unchanged
- Enhanced requests get handled by our wrapper
- Preserves TypeRocket's security and middleware

**Alternative Considered**: Filter `typerocket_rest` action
- **Rejected**: Fires AFTER RestController routing decided, too late to add endpoints

### 2. Endpoint Design

**Enhanced List Endpoint**:
```
GET /tr-api/rest/{resource}?search=term&field=value&orderby=name&order=asc&per_page=25&page=2
```

**Action Endpoint** (new):
```
POST /tr-api/rest/{resource}/{id}/actions/{action}
Body: { "param1": "value1" }
```

**Standard CRUD** (unchanged):
```
GET    /tr-api/rest/{resource}/{id}
POST   /tr-api/rest/{resource}
PUT    /tr-api/rest/{resource}/{id}
DELETE /tr-api/rest/{resource}/{id}
```

**Query Parameter Format**:
- `search={term}` - Full-text search across designated searchable fields
- `{field}={value}` - Exact match filter on fillable fields
- `{relationship}.{field}={value}` - Filter via relationship (future enhancement)
- `orderby={field}` - Sort field (default: id)
- `order={asc|desc}` - Sort direction (default: asc)
- `per_page={int}` - Items per page (default: 25, max: 100)
- `page={int}` - Page number (default: 1)

### 3. Model Introspection Strategy

**Filterable Fields**:
```php
$filterable = array_diff($model->getFillableFields(), $model->getGuardedFields());
```

**Searchable Fields** (text search targets):
```php
// Priority order:
1. Model method: $model->getSearchableFields() if exists
2. Fallback: String/text fields from $fillable with common names
   (name, title, description, slug, sku, email, etc.)
3. Exclude: IDs, dates, numeric fields, JSON fields
```

**Sortable Fields**:
```php
$sortable = array_merge($filterable, ['id', 'created_at', 'updated_at']);
```

**Why This Approach**:
- Safe by default (respects `$guarded`)
- No configuration needed
- Models can opt-in to custom behavior via method
- Prevents filtering on passwords, version, audit fields

### 4. Security & Authorization

**Policy Enforcement**:
```php
// Before any query execution
if (!$model->can('index')) {
    return Response::unauthorized('Unauthorized access');
}
```

**Query Validation**:
- Whitelist approach: only allow filtering on known-safe fields
- Sanitize all input values via TypeRocket's Field validators
- Prevent SQL injection via parameterized queries (TypeRocket Query Builder)
- Reject invalid field names immediately

**Rate Limiting** (future):
- Not implemented in v1
- Can add via WordPress transients or Redis

**Guarded Field Protection**:
```php
if (in_array($field, $model->getGuardedFields())) {
    throw new \InvalidArgumentException("Cannot filter on guarded field: {$field}");
}
```

### 5. Action Pattern Design

**Reflective Action Discovery** (PHP 8+ Attributes):

Zero-configuration action discovery using PHP 8 attributes. Models define actions with `#[Action]` attribute:

```php
use MakerMaker\Http\Attributes\Action;

class Service extends Model {
    /**
     * Duplicate service with new SKU
     */
    #[Action(capability: 'create', description: 'Duplicate this service')]
    public function duplicate(AuthUser $user, array $params): array {
        $new = $this->replicate();
        $new->sku = $new->sku . '-COPY';
        $new->created_by = $user->ID;
        $new->save();

        return ['success' => true, 'data' => $new];
    }

    /**
     * Archive service (mark as inactive)
     */
    #[Action(capability: 'update', description: 'Archive this service')]
    public function archive(AuthUser $user, array $params): array {
        $this->is_active = false;
        $this->save(['is_active']);

        return ['success' => true, 'message' => 'Service archived'];
    }

    /**
     * Update pricing for all tiers
     */
    #[Action(
        capability: 'update',
        description: 'Update pricing for all tiers',
        requiresParams: true
    )]
    public function updatePricing(AuthUser $user, array $params): array {
        // Batch update pricing
        return ['success' => true, 'data' => $updatedPrices];
    }
}
```

**Why This Pattern**:
- **Zero configuration**: Just add `#[Action]` attribute to method
- **Self-documenting**: Description in attribute, method signature defines parameters
- **Policy integration**: Automatic capability check via attribute
- **Type-safe**: Method must exist, enforced by PHP
- **Action logic stays with model**: Domain logic where it belongs
- **Automatic discovery**: No manual registration arrays needed
- **Convention over configuration**: Capability inferred from method name if not specified

**Discovery Process**:
1. `ReflectiveActionDiscovery` scans model methods via PHP Reflection
2. Methods with `#[Action]` attribute automatically become REST endpoints
3. Action name derived from method name: `updatePricing` → `update-pricing`
4. Capability inferred from method name if not specified:
   - `duplicate*`, `copy*`, `clone*` → `create`
   - `update*`, `modify*`, `edit*`, `archive*` → `update`
   - `delete*`, `remove*`, `destroy*` → `destroy`
   - Default → `read`
5. Results cached per model class for performance

**Backward Compatibility** (HasRestActions Interface):

Existing interface-based actions still work:

```php
class Service extends Model implements HasRestActions {
    public function getRestActions(): array {
        return [
            'duplicate' => [
                'method' => 'actionDuplicate',
                'description' => 'Duplicate this service',
                'capability' => 'create'
            ]
        ];
    }

    public function actionDuplicate(AuthUser $user, array $params): array {
        // Legacy implementation
        return ['success' => true, 'data' => $new];
    }
}
```

**Note**: If a model implements `HasRestActions`, the interface method takes precedence over reflective discovery. This ensures backward compatibility.

---

## Query Builder Implementation

### Reflective Query Construction

```php
class ReflectiveQueryBuilder {
    public function build(Model $model, Request $request): Results {
        $query = $model->query();

        // 1. Text Search
        if ($search = $request->getQuery('search')) {
            $this->applySearch($query, $model, $search);
        }

        // 2. Field Filters
        foreach ($request->getQuery() as $key => $value) {
            if ($this->isFilterableField($model, $key)) {
                $query->where($key, $value);
            }
        }

        // 3. Sorting
        $orderby = $request->getQuery('orderby', 'id');
        $order = $request->getQuery('order', 'asc');
        if ($this->isSortableField($model, $orderby)) {
            $query->orderBy($orderby, $order);
        }

        // 4. Pagination
        $perPage = min((int)$request->getQuery('per_page', 25), 100);
        $page = max((int)$request->getQuery('page', 1), 1);

        return $query->paginate($perPage, $page);
    }
}
```

### Search Implementation

```php
private function applySearch($query, $model, $searchTerm) {
    $searchable = $this->getSearchableFields($model);

    if (empty($searchable)) {
        return; // No searchable fields, skip
    }

    $query->where(function($q) use ($searchable, $searchTerm) {
        foreach ($searchable as $field) {
            $q->orWhere($field, 'LIKE', "%{$searchTerm}%");
        }
    });
}
```

---

## Wrapper Integration Points

### Single Initialization

In `MakermakerTypeRocketPlugin::init()`:

```php
public function init() {
    $this->loadResources();
    $this->setupSettingsPage();
    $this->registerAssets();

    // Initialize reflective REST wrapper (one line, zero config)
    \MakerMaker\Http\ReflectiveRestWrapper::init();
}
```

### Hook Registration

```php
class ReflectiveRestWrapper {
    public static function init() {
        add_action('parse_request', [self::class, 'handleRequest'], 5);
    }

    public static function handleRequest($wp) {
        // Check if this is a REST request
        if (!preg_match('#^tr-api/rest/([^/]+)(?:/([^/]+))?(?:/actions/([^/]+))?$#',
                        $wp->request, $matches)) {
            return; // Not our concern
        }

        [$full, $resource, $id, $action] = array_pad($matches, 4, null);

        // Let standard CRUD pass through
        if ($id && !$action) {
            return; // GET/PUT/DELETE /{resource}/{id}
        }

        if (!$id && $_SERVER['REQUEST_METHOD'] === 'POST') {
            return; // POST /{resource} (create)
        }

        // Handle enhanced list or actions
        if ($action) {
            self::handleAction($resource, $id, $action);
        } else {
            self::handleList($resource);
        }

        exit; // Prevent TypeRocket from processing
    }
}
```

---

## Response Format

### Successful List Response

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "sku": "SRV-INSTALL-001",
      "name": "Basic Installation",
      "category_id": 5,
      "serviceType": {
        "id": 2,
        "name": "Installation"
      }
    }
  ],
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
  "code": "INVALID_FILTER"
}
```

### Action Response

```json
{
  "success": true,
  "message": "Service duplicated successfully",
  "data": {
    "id": 47,
    "sku": "SRV-INSTALL-001-COPY",
    "name": "Basic Installation"
  }
}
```

---

## Edge Cases & Handling

### 1. Models with Complex Relationships

**Problem**: Eager loading all relationships can cause performance issues

**Solution**:
- Wrapper respects model's `$with` property
- Can add `?with=relationship` query param for selective loading (future)
- Relationship filtering planned for v2

### 2. Soft Deletes

**Handling**:
```php
$query->whereNull('deleted_at'); // Always exclude soft-deleted
```

**Future**: Add `?with_trashed=1` for admin users

### 3. JSON/Array Fields

**Filtering on JSON**: Not supported in v1
- Requires special query syntax
- Future: `?metadata[key]=value`

### 4. Polymorphic Relationships

**Not Supported in v1**: Too complex for automatic introspection
- Models with polymorphic relationships work, but can't filter via them

### 5. Performance with Large Datasets

**Considerations**:
- Always enforce max `per_page=100`
- Use database indices on commonly filtered fields
- Monitor slow queries via WordPress query log
- Future: Add query caching

### 6. Security Edge Cases

**SQL Injection**: Impossible (using Query Builder with parameterized queries)

**Unauthorized Field Access**: Protected via `$guarded` check

**Mass Assignment**: Not applicable (read-only queries)

**Information Disclosure**: Policy enforced before query execution

---

## Future Enhancements

### Phase 2 Features

1. **Relationship Filtering**
   ```
   GET /tr-api/rest/services?category.slug=residential
   ```

2. **Advanced Search Operators**
   ```
   GET /tr-api/rest/services?base_price[gte]=100&base_price[lte]=500
   ```

3. **Selective Relationship Loading**
   ```
   GET /tr-api/rest/services?with=prices,equipment
   ```

4. **Field Selection**
   ```
   GET /tr-api/rest/services?fields=id,name,sku
   ```

5. **Aggregations**
   ```
   GET /tr-api/rest/services/aggregate?group_by=category_id&count=id
   ```

6. **Bulk Actions**
   ```
   POST /tr-api/rest/services/bulk/archive
   Body: { "ids": [1, 2, 3] }
   ```

### Performance Optimizations

- Redis caching for frequent queries
- Query result caching per user
- Database query logging and optimization
- Index recommendations based on query patterns

### Developer Experience

- API documentation auto-generation from models
- Postman/OpenAPI spec generation
- GraphQL compatibility layer
- WebSocket support for real-time updates

---

## Testing Strategy

### Integration Tests

1. **Search Tests**: Verify text search across multiple fields
2. **Filter Tests**: Test exact match, multiple filters, invalid fields
3. **Sorting Tests**: ASC/DESC, multiple sort fields
4. **Pagination Tests**: Boundary conditions, invalid pages
5. **Security Tests**: Guarded fields, unauthorized access, policy enforcement
6. **Action Tests**: Execute actions, check authorization, validate responses
7. **Edge Case Tests**: Empty results, malformed queries, special characters

### Test Models

Use existing `Service` model (most complex) and `Equipment` (simpler) for comprehensive coverage.

### Performance Tests

- Measure query time with 1000+ records
- Test pagination efficiency
- Validate N+1 query prevention via eager loading

---

## Rollout Verification

### Checklist

- [ ] All 27 models automatically gain enhanced endpoints
- [ ] No breaking changes to existing CRUD operations
- [ ] Policies still enforced on all operations
- [ ] Search works across designated text fields
- [ ] Filtering respects guarded fields
- [ ] Pagination boundaries enforced
- [ ] Actions require interface implementation (opt-in)
- [ ] Error messages are clear and actionable
- [ ] No performance degradation on standard CRUD
- [ ] Tests pass with 100% coverage on critical paths

### Manual Verification Commands

```bash
# Test existing CRUD unchanged
curl http://site.test/tr-api/rest/services/1

# Test new search
curl "http://site.test/tr-api/rest/services?search=installation"

# Test filtering
curl "http://site.test/tr-api/rest/services?service_type_id=3&is_active=1"

# Test pagination
curl "http://site.test/tr-api/rest/services?per_page=10&page=2"

# Test sorting
curl "http://site.test/tr-api/rest/services?orderby=name&order=desc"

# Test on different model
curl "http://site.test/tr-api/rest/equipment?search=laser"

# Test action (if Service implements HasRestActions)
curl -X POST http://site.test/tr-api/rest/services/1/actions/duplicate
```

---

## Summary

This reflective REST API wrapper achieves zero-configuration enhancement of all TypeRocket custom resources through:

1. **Hook-based interception** of REST requests at `parse_request`
2. **Reflective introspection** of model metadata (`$fillable`, `$guarded`, `$with`)
3. **Safe-by-default filtering** respecting model security boundaries
4. **Interface-based actions** allowing opt-in custom functionality
5. **Backward compatibility** by passing through standard CRUD unchanged

**Zero ongoing maintenance**: Just register resources via `mm_create_custom_resource()` as always. All enhancements apply automatically.
