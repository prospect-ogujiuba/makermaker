# Reflective Actions Guide

## Zero-Config REST API Actions for Models

This guide shows how to add custom REST API action endpoints to your models using PHP 8 attributes with zero configuration.

---

## Quick Start

Add `#[Action]` attribute to any public method in your model:

```php
use MakerMaker\Http\Attributes\Action;
use TypeRocket\Models\Model;
use TypeRocket\Models\AuthUser;

class Service extends Model
{
    #[Action(capability: 'create', description: 'Duplicate this service')]
    public function duplicate(AuthUser $user, array $params): array
    {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';
        $copy->created_by = $user->ID;
        $copy->save();

        return [
            'success' => true,
            'message' => 'Service duplicated',
            'data' => $copy
        ];
    }
}
```

**Endpoint automatically available:**
```
POST /tr-api/rest/services/5/actions/duplicate
```

That's it! No registration, no configuration files, no boilerplate.

---

## Action Method Signature

All action methods must follow this signature:

```php
#[Action(...)]
public function actionName(AuthUser $user, array $params): array
{
    // Action logic
    return ['success' => true, 'data' => $result];
}
```

**Required:**
- `public` visibility
- Non-static
- Two parameters: `AuthUser $user`, `array $params`
- Return type: `array`

**Parameters:**
- `$user` - Current authenticated WordPress user
- `$params` - Request body parsed as JSON array

**Return Format:**
```php
return [
    'success' => true|false,       // Required
    'message' => 'Human message',  // Optional
    'data' => $anyData,            // Optional
    'errors' => [],                // Optional (for validation errors)
];
```

---

## Action Attribute Parameters

```php
#[Action(
    capability: string,      // Policy method to check (default: inferred)
    description: string,     // Human-readable description (default: '')
    requiresParams: bool,    // Whether params are required (default: false)
    requiresId: bool         // Whether specific record needed (default: true)
)]
```

### Capability

Specifies which policy method to check before executing action:

```php
// Explicitly set capability
#[Action(capability: 'create')]
public function duplicate(AuthUser $user, array $params): array { }

// Capability inferred from method name
#[Action(description: 'Duplicate service')]
public function duplicate(AuthUser $user, array $params): array { }
// Inferred: 'create' (because method starts with 'duplicate')
```

**Inference Rules:**
- `duplicate*`, `copy*`, `clone*`, `create*` → `create`
- `update*`, `modify*`, `edit*`, `change*` → `update`
- `archive*`, `restore*`, `activate*`, `deactivate*`, `toggle*` → `update`
- `delete*`, `remove*`, `destroy*` → `destroy`
- Default → `read`

### Description

Human-readable description shown in API documentation:

```php
#[Action(
    capability: 'update',
    description: 'Archive service without deleting from database'
)]
public function archive(AuthUser $user, array $params): array { }
```

### RequiresParams

Indicates whether action requires parameters in request body:

```php
#[Action(
    capability: 'update',
    description: 'Update pricing for all tiers',
    requiresParams: true  // Params expected in request body
)]
public function updatePricing(AuthUser $user, array $params): array
{
    if (empty($params['pricing'])) {
        throw new \Exception('Missing pricing data', 400);
    }
    // ...
}
```

### RequiresId

Whether action needs specific record (default: `true`):

```php
// Instance action (default)
#[Action(capability: 'create')]
public function duplicate(AuthUser $user, array $params): array
{
    // $this is the loaded record
    $copy = $this->replicate();
    // ...
}

// Collection action (future: actions without specific ID)
#[Action(capability: 'create', requiresId: false)]
public static function bulkCreate(AuthUser $user, array $params): array
{
    // Process multiple records
}
```

**Note:** Collection actions (requiresId: false) are planned for future release.

---

## Common Action Patterns

### 1. Duplicate/Copy

Create copy of existing record:

```php
#[Action(capability: 'create', description: 'Duplicate service with new SKU')]
public function duplicate(AuthUser $user, array $params): array
{
    // Create copy
    $copy = new self();
    $copy->fill($this->getAttributes());

    // Clear unique fields
    $copy->id = null;
    $copy->version = 1;
    $copy->created_at = null;

    // Generate new unique identifiers
    $copy->sku = $params['new_sku'] ?? ($this->sku . '-COPY');
    $copy->slug = $params['new_slug'] ?? ($this->slug . '-copy');
    $copy->name = $params['new_name'] ?? ($this->name . ' (Copy)');

    // Set audit fields
    $copy->created_by = $user->ID;
    $copy->updated_by = $user->ID;

    // Save
    if (!$copy->save()) {
        throw new \Exception('Failed to duplicate', 500);
    }

    return [
        'success' => true,
        'message' => 'Record duplicated successfully',
        'data' => $copy
    ];
}
```

### 2. Archive/Restore

Soft delete without removing from database:

```php
#[Action(capability: 'update', description: 'Archive record (soft delete)')]
public function archive(AuthUser $user, array $params): array
{
    $this->is_active = 0;
    $this->updated_by = $user->ID;

    if (!$this->save(['is_active', 'updated_by'])) {
        throw new \Exception('Failed to archive', 500);
    }

    return [
        'success' => true,
        'message' => 'Record archived successfully',
        'data' => ['id' => $this->id, 'is_active' => $this->is_active]
    ];
}

#[Action(capability: 'update', description: 'Restore archived record')]
public function restore(AuthUser $user, array $params): array
{
    $this->is_active = 1;
    $this->deleted_at = null;
    $this->updated_by = $user->ID;

    if (!$this->save(['is_active', 'deleted_at', 'updated_by'])) {
        throw new \Exception('Failed to restore', 500);
    }

    return [
        'success' => true,
        'message' => 'Record restored successfully'
    ];
}
```

### 3. Status Toggle

Toggle boolean fields:

```php
#[Action(capability: 'update', description: 'Toggle active status')]
public function toggleActive(AuthUser $user, array $params): array
{
    $this->is_active = !$this->is_active;
    $this->updated_by = $user->ID;

    if (!$this->save(['is_active', 'updated_by'])) {
        throw new \Exception('Failed to toggle status', 500);
    }

    return [
        'success' => true,
        'message' => $this->is_active ? 'Activated' : 'Deactivated',
        'data' => ['id' => $this->id, 'is_active' => $this->is_active]
    ];
}

#[Action(capability: 'update', description: 'Toggle featured status')]
public function toggleFeatured(AuthUser $user, array $params): array
{
    $this->is_featured = !$this->is_featured;
    $this->updated_by = $user->ID;
    $this->save(['is_featured', 'updated_by']);

    return ['success' => true, 'data' => $this];
}
```

### 4. Batch Update

Update multiple related records:

```php
#[Action(
    capability: 'update',
    description: 'Update pricing for all tiers',
    requiresParams: true
)]
public function updatePricing(AuthUser $user, array $params): array
{
    if (empty($params['pricing'])) {
        throw new \Exception('Missing pricing data', 400);
    }

    $updated = [];
    $errors = [];

    foreach ($params['pricing'] as $tierCode => $priceData) {
        try {
            // Find current price record
            $price = ServicePrice::new()
                ->where('service_id', $this->id)
                ->where('pricing_tier_id', function($query) use ($tierCode) {
                    $query->select('id')
                        ->from(GLOBAL_WPDB_PREFIX . 'srvc_pricing_tiers')
                        ->where('code', $tierCode);
                })
                ->where('is_current', 1)
                ->first();

            if (!$price) {
                continue;
            }

            // Update amount
            if (isset($priceData['amount'])) {
                $price->amount = $priceData['amount'];
            }

            $price->updated_by = $user->ID;

            if ($price->save()) {
                $updated[] = $tierCode;
            } else {
                $errors[] = "Failed to update pricing for tier: {$tierCode}";
            }

        } catch (\Exception $e) {
            $errors[] = "Error updating tier {$tierCode}: " . $e->getMessage();
        }
    }

    return [
        'success' => empty($errors),
        'message' => count($updated) . ' pricing tier(s) updated',
        'data' => [
            'updated_tiers' => $updated,
            'errors' => $errors
        ]
    ];
}
```

### 5. Relationship Management

Add/remove related records:

```php
#[Action(
    capability: 'update',
    description: 'Add equipment to service',
    requiresParams: true
)]
public function addEquipment(AuthUser $user, array $params): array
{
    if (empty($params['equipment_id'])) {
        throw new \Exception('Missing equipment_id', 400);
    }

    // Check if equipment exists
    $equipment = Equipment::new()->findById($params['equipment_id']);
    if (!$equipment) {
        throw new \Exception('Equipment not found', 404);
    }

    // Create junction record
    $junction = new ServiceEquipment();
    $junction->service_id = $this->id;
    $junction->equipment_id = $params['equipment_id'];
    $junction->quantity = $params['quantity'] ?? 1;
    $junction->required = $params['required'] ?? 1;
    $junction->cost_included = $params['cost_included'] ?? 1;
    $junction->created_by = $user->ID;

    if (!$junction->save()) {
        throw new \Exception('Failed to add equipment', 500);
    }

    return [
        'success' => true,
        'message' => 'Equipment added to service',
        'data' => $junction
    ];
}

#[Action(
    capability: 'update',
    description: 'Remove equipment from service',
    requiresParams: true
)]
public function removeEquipment(AuthUser $user, array $params): array
{
    if (empty($params['equipment_id'])) {
        throw new \Exception('Missing equipment_id', 400);
    }

    $deleted = ServiceEquipment::new()
        ->where('service_id', $this->id)
        ->where('equipment_id', $params['equipment_id'])
        ->delete();

    return [
        'success' => $deleted > 0,
        'message' => $deleted ? 'Equipment removed' : 'Equipment not found'
    ];
}
```

### 6. Approval Workflow

Implement approval/rejection flows:

```php
#[Action(capability: 'update', description: 'Approve pending record')]
public function approve(AuthUser $user, array $params): array
{
    if ($this->approval_status === 'approved') {
        throw new \Exception('Already approved', 400);
    }

    $this->approval_status = 'approved';
    $this->approved_by = $user->ID;
    $this->approved_at = date('Y-m-d H:i:s');
    $this->updated_by = $user->ID;

    if (!$this->save()) {
        throw new \Exception('Failed to approve', 500);
    }

    return [
        'success' => true,
        'message' => 'Record approved successfully',
        'data' => $this
    ];
}

#[Action(
    capability: 'update',
    description: 'Reject pending record',
    requiresParams: true
)]
public function reject(AuthUser $user, array $params): array
{
    if (empty($params['reason'])) {
        throw new \Exception('Rejection reason required', 400);
    }

    $this->approval_status = 'rejected';
    $this->rejection_reason = $params['reason'];
    $this->rejected_by = $user->ID;
    $this->rejected_at = date('Y-m-d H:i:s');
    $this->updated_by = $user->ID;

    if (!$this->save()) {
        throw new \Exception('Failed to reject', 500);
    }

    return [
        'success' => true,
        'message' => 'Record rejected',
        'data' => $this
    ];
}
```

---

## Authorization & Security

### Policy Integration

Actions automatically check model policy before execution:

```php
// In app/Auth/ServicePolicy.php
class ServicePolicy extends Policy
{
    public function create(?AuthUser $user = null): bool
    {
        return $user && $user->can('create_services');
    }

    public function update(?AuthUser $user = null, Model $model = null): bool
    {
        return $user && $user->can('edit_services');
    }
}

// In model
#[Action(capability: 'create')]  // Checks ServicePolicy::create()
public function duplicate(AuthUser $user, array $params): array { }

#[Action(capability: 'update')]  // Checks ServicePolicy::update()
public function archive(AuthUser $user, array $params): array { }
```

If policy check fails, action returns 403 Unauthorized.

### Input Validation

Always validate parameters:

```php
#[Action(capability: 'update', requiresParams: true)]
public function updatePricing(AuthUser $user, array $params): array
{
    // Validate required params
    if (empty($params['pricing'])) {
        throw new \Exception('Missing pricing data', 400);
    }

    // Validate data types
    if (!is_array($params['pricing'])) {
        throw new \Exception('Pricing must be an array', 400);
    }

    // Validate ranges
    foreach ($params['pricing'] as $tier => $data) {
        if (isset($data['amount']) && $data['amount'] < 0) {
            throw new \Exception("Invalid amount for tier {$tier}", 400);
        }
    }

    // Process...
}
```

### Error Handling

Use exceptions for errors:

```php
#[Action(capability: 'create')]
public function duplicate(AuthUser $user, array $params): array
{
    try {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';

        if (!$copy->save()) {
            throw new \Exception('Failed to save duplicate', 500);
        }

        return ['success' => true, 'data' => $copy];

    } catch (\Exception $e) {
        throw new \Exception("Duplication failed: " . $e->getMessage(), 500);
    }
}
```

Exception codes map to HTTP status codes:
- `400` - Bad Request (validation error)
- `403` - Forbidden (unauthorized)
- `404` - Not Found
- `500` - Internal Server Error

---

## Testing Actions

### Unit Tests

```php
public function test_duplicate_action_creates_copy()
{
    $service = new Service();
    $service->id = 1;
    $service->sku = 'TEST-001';
    $service->name = 'Test Service';

    $user = $this->createMock(AuthUser::class);
    $user->ID = 1;

    $result = $service->duplicate($user, [
        'new_sku' => 'TEST-002'
    ]);

    expect($result['success'])->toBe(true);
    expect($result['data']->sku)->toBe('TEST-002');
}
```

### Integration Tests

```php
public function test_duplicate_action_via_api()
{
    $response = $this->post('/tr-api/rest/services/5/actions/duplicate', [
        'new_sku' => 'SRV-NEW-001'
    ]);

    expect($response->status())->toBe(200);
    expect($response->json('success'))->toBe(true);
    expect($response->json('data.sku'))->toBe('SRV-NEW-001');
}
```

---

## Troubleshooting

### "Action not found"

**Cause:** Action method doesn't have `#[Action]` attribute or method name doesn't match URL.

**Solution:**
1. Ensure method has `#[Action]` attribute
2. Check action name derivation: `updatePricing` → `update-pricing`
3. Clear discovery cache: `ReflectiveActionDiscovery::clearCache()`

### "Unauthorized to perform action"

**Cause:** User lacks required capability in policy.

**Solution:**
1. Check policy method matches action capability
2. Verify user has required WordPress capabilities
3. Test with `can()` method: `$model->can('create', $user)`

### "Invalid JSON in request body"

**Cause:** Malformed JSON in POST body.

**Solution:**
```bash
# Correct
curl -X POST /tr-api/rest/services/5/actions/duplicate \
  -H "Content-Type: application/json" \
  -d '{"new_sku": "TEST-002"}'

# Incorrect (missing quotes)
curl -X POST ... -d '{new_sku: TEST-002}'
```

### Actions not discovered

**Cause:** Model uses `HasRestActions` interface which takes precedence.

**Solution:**
- Remove `implements HasRestActions` if using attributes
- Or keep interface and migrate actions gradually

---

## Migration from HasRestActions Interface

### Before (Interface)

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
        // Logic
        return ['success' => true];
    }
}
```

### After (Attributes)

```php
use MakerMaker\Http\Attributes\Action;

class Service extends Model
{
    #[Action(capability: 'create', description: 'Duplicate service')]
    public function duplicate(AuthUser $user, array $params): array
    {
        // Same logic
        return ['success' => true];
    }
}
```

**Benefits:**
- 8 lines → 4 lines (50% less code)
- No interface required
- No registration array
- Self-documenting (description with method)
- Automatic action name derivation

---

## Best Practices

### 1. Keep Actions Simple

One action = one responsibility:

```php
// Good: Focused action
#[Action(capability: 'update')]
public function archive(AuthUser $user, array $params): array
{
    $this->is_active = 0;
    $this->save(['is_active']);
    return ['success' => true];
}

// Bad: Multiple responsibilities
#[Action(capability: 'update')]
public function archiveAndNotifyAndLog(AuthUser $user, array $params): array
{
    $this->is_active = 0;
    $this->save();
    $this->sendNotification();
    $this->logAction();
    return ['success' => true];
}
```

### 2. Return Consistent Data

Always return standardized response:

```php
return [
    'success' => true|false,
    'message' => 'Human-readable message',
    'data' => $result,         // Actual data
    'errors' => []             // Array of errors if any
];
```

### 3. Use Descriptive Names

```php
// Good
#[Action(capability: 'create')]
public function duplicate(AuthUser $user, array $params): array { }

#[Action(capability: 'update')]
public function updatePricing(AuthUser $user, array $params): array { }

// Bad
#[Action(capability: 'create')]
public function action1(AuthUser $user, array $params): array { }

#[Action(capability: 'update')]
public function doStuff(AuthUser $user, array $params): array { }
```

### 4. Document Complex Actions

```php
/**
 * Update pricing for all pricing tiers
 *
 * Batch updates pricing across multiple tiers. Creates price history records
 * and invalidates old pricing. Requires pricing approval workflow.
 *
 * @param AuthUser $user Current user
 * @param array $params Expected format:
 *   [
 *     'pricing' => [
 *       'tier_code' => ['amount' => 100.00, 'setup_fee' => 50.00],
 *       ...
 *     ]
 *   ]
 * @return array Response with updated tiers and errors
 * @throws \Exception if pricing data invalid
 */
#[Action(
    capability: 'update',
    description: 'Update pricing for all tiers',
    requiresParams: true
)]
public function updatePricing(AuthUser $user, array $params): array
{
    // Implementation
}
```

### 5. Log Important Actions

```php
#[Action(capability: 'destroy')]
public function permanentDelete(AuthUser $user, array $params): array
{
    // Log before destructive action
    error_log("User {$user->ID} permanently deleting Service {$this->id}");

    $this->delete();

    return [
        'success' => true,
        'message' => 'Permanently deleted'
    ];
}
```

---

## Summary

**Zero-config reflective actions:**

1. Add `#[Action]` attribute to method
2. Follow method signature: `public function name(AuthUser $user, array $params): array`
3. Return standardized response: `['success' => bool, 'data' => mixed]`
4. That's it - action automatically available via REST API

**Endpoint pattern:**
```
POST /tr-api/rest/{resource}/{id}/actions/{action-name}
```

**No configuration files, no registration, no boilerplate.**
