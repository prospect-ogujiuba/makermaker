# Action Migration Guide

## Migrating from HasRestActions Interface to Reflective Attributes

This guide explains how existing `HasRestActions` interface implementations continue to work and how to migrate to the new attribute-based reflective actions (optional).

---

## Backward Compatibility Guarantee

**Both approaches work side-by-side:**

1. **Interface-based actions** (HasRestActions) - Still fully supported
2. **Attribute-based actions** (#[Action]) - New zero-config approach

**Priority:** If model implements `HasRestActions`, the interface method takes precedence over reflective discovery.

---

## Current Interface Approach (Still Works)

Your existing code continues to work without changes:

```php
use MakerMaker\Http\HasRestActions;
use TypeRocket\Models\Model;
use TypeRocket\Models\AuthUser;

class Service extends Model implements HasRestActions
{
    /**
     * Define REST actions via interface
     */
    public function getRestActions(): array
    {
        return [
            'duplicate' => [
                'method' => 'actionDuplicate',
                'description' => 'Duplicate this service',
                'capability' => 'create',
                'requires_id' => true
            ],
            'archive' => [
                'method' => 'actionArchive',
                'description' => 'Archive this service',
                'capability' => 'update'
            ],
            'updatePricing' => [
                'method' => 'actionUpdatePricing',
                'description' => 'Update pricing for all tiers',
                'capability' => 'update',
                'requires_params' => true
            ]
        ];
    }

    /**
     * Duplicate action implementation
     */
    public function actionDuplicate(AuthUser $user, array $params): array
    {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';
        $copy->created_by = $user->ID;
        $copy->save();

        return ['success' => true, 'data' => $copy];
    }

    /**
     * Archive action implementation
     */
    public function actionArchive(AuthUser $user, array $params): array
    {
        $this->is_active = 0;
        $this->save(['is_active']);

        return ['success' => true, 'message' => 'Service archived'];
    }

    /**
     * Update pricing action implementation
     */
    public function actionUpdatePricing(AuthUser $user, array $params): array
    {
        // Update pricing logic
        return ['success' => true, 'data' => $updatedPrices];
    }
}
```

**This code requires NO changes and will continue to work indefinitely.**

---

## New Attribute Approach (Recommended for New Code)

Equivalent implementation using PHP 8 attributes:

```php
use MakerMaker\Http\Attributes\Action;
use TypeRocket\Models\Model;
use TypeRocket\Models\AuthUser;

class Service extends Model
{
    /**
     * Duplicate this service
     */
    #[Action(capability: 'create', description: 'Duplicate this service')]
    public function duplicate(AuthUser $user, array $params): array
    {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';
        $copy->created_by = $user->ID;
        $copy->save();

        return ['success' => true, 'data' => $copy];
    }

    /**
     * Archive this service
     */
    #[Action(capability: 'update', description: 'Archive this service')]
    public function archive(AuthUser $user, array $params): array
    {
        $this->is_active = 0;
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
    public function updatePricing(AuthUser $user, array $params): array
    {
        // Update pricing logic
        return ['success' => true, 'data' => $updatedPrices];
    }
}
```

**Benefits of Migration:**
- **Less code:** ~40% reduction in lines of code
- **No interface:** Remove `implements HasRestActions` and `getRestActions()`
- **No registration array:** Action metadata in attribute, not separate array
- **Self-documenting:** Description lives with method, not separate mapping
- **Auto-discovery:** No manual registration needed
- **Cleaner method names:** `duplicate()` instead of `actionDuplicate()`

---

## Migration Strategies

### Strategy 1: Keep Everything As-Is (No Migration)

**When to use:**
- You have existing interface-based actions that work
- You don't want to change working code
- Your team prefers explicit registration arrays

**Action:** Do nothing. Your code continues to work.

### Strategy 2: New Actions Use Attributes (Hybrid Approach)

**When to use:**
- You want to try attributes without touching existing code
- You're adding new actions to existing models
- You want gradual adoption

**Example:**

```php
use MakerMaker\Http\HasRestActions;
use MakerMaker\Http\Attributes\Action;

class Service extends Model implements HasRestActions
{
    // Existing interface actions (still work)
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
        // Existing implementation
    }

    // NEW actions use attributes (but won't be discovered because interface exists)
    // NOTE: This WON'T work - interface takes precedence!
    #[Action(capability: 'update', description: 'New action')]
    public function newAction(AuthUser $user, array $params): array
    {
        return ['success' => true];
    }
}
```

**Important:** If model implements `HasRestActions`, reflective discovery is **skipped entirely**. You cannot mix both approaches on the same model. Choose one:

1. Keep `HasRestActions` → All actions via interface
2. Remove `HasRestActions` → All actions via attributes

### Strategy 3: Full Migration to Attributes

**When to use:**
- You want zero-config approach
- You're refactoring models anyway
- You want cleaner, more maintainable code

**Steps:**

1. **Add attribute to each action method:**

   ```php
   // Before
   public function actionDuplicate(AuthUser $user, array $params): array

   // After
   #[Action(capability: 'create', description: 'Duplicate service')]
   public function duplicate(AuthUser $user, array $params): array
   ```

2. **Rename methods** (optional but recommended):

   ```php
   // Before
   actionDuplicate → duplicate
   actionArchive → archive
   actionUpdatePricing → updatePricing
   ```

3. **Remove interface and registration:**

   ```php
   // Before
   class Service extends Model implements HasRestActions
   {
       public function getRestActions(): array { /* ... */ }
   }

   // After
   class Service extends Model
   {
       // No interface, no getRestActions()
   }
   ```

4. **Test actions still work:**

   ```bash
   # Test each action
   curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate
   curl -X POST http://site.test/tr-api/rest/services/5/actions/archive
   ```

---

## Side-by-Side Comparison

### Interface Approach

```php
use MakerMaker\Http\HasRestActions;

class Service extends Model implements HasRestActions
{
    // 1. Declare interface
    // 2. Implement getRestActions()
    public function getRestActions(): array
    {
        // 3. Manually register each action
        return [
            'duplicate' => [
                'method' => 'actionDuplicate',
                'description' => 'Duplicate service',
                'capability' => 'create'
            ]
        ];
    }

    // 4. Implement action method (different name)
    public function actionDuplicate(AuthUser $user, array $params): array
    {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';
        $copy->save();
        return ['success' => true, 'data' => $copy];
    }
}
```

**Lines of code:** ~18 lines for one action

### Attribute Approach

```php
use MakerMaker\Http\Attributes\Action;

class Service extends Model
{
    // 1. Add attribute
    // 2. Implement method (action name = method name)
    #[Action(capability: 'create', description: 'Duplicate service')]
    public function duplicate(AuthUser $user, array $params): array
    {
        $copy = $this->replicate();
        $copy->sku .= '-COPY';
        $copy->save();
        return ['success' => true, 'data' => $copy];
    }
}
```

**Lines of code:** ~10 lines for one action (44% reduction)

---

## Migration Checklist

### For Each Model with Actions:

- [ ] List all existing actions in `getRestActions()`
- [ ] Decide migration strategy (keep, hybrid, or full migration)
- [ ] If migrating:
  - [ ] Add `#[Action]` attribute to each action method
  - [ ] Copy capability and description from registration array to attribute
  - [ ] Rename methods (remove `action` prefix if desired)
  - [ ] Remove `implements HasRestActions`
  - [ ] Remove `getRestActions()` method
  - [ ] Test each action endpoint
  - [ ] Update any references to method names in code/tests

### Testing After Migration:

```bash
# 1. List available actions
curl http://site.test/tr-api/rest/services/5

# 2. Test each action
curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate
curl -X POST http://site.test/tr-api/rest/services/5/actions/archive
curl -X POST http://site.test/tr-api/rest/services/5/actions/update-pricing \
  -H "Content-Type: application/json" \
  -d '{"pricing": {"tier1": 100}}'

# 3. Verify authorization
curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate
# Should return 403 if user lacks 'create' capability
```

---

## Common Migration Scenarios

### Scenario 1: Simple CRUD Actions

**Before:**
```php
class Service extends Model implements HasRestActions
{
    public function getRestActions(): array
    {
        return [
            'duplicate' => ['method' => 'actionDuplicate', 'capability' => 'create'],
            'archive' => ['method' => 'actionArchive', 'capability' => 'update']
        ];
    }

    public function actionDuplicate(AuthUser $user, array $params): array { }
    public function actionArchive(AuthUser $user, array $params): array { }
}
```

**After:**
```php
class Service extends Model
{
    #[Action(capability: 'create')]
    public function duplicate(AuthUser $user, array $params): array { }

    #[Action(capability: 'update')]
    public function archive(AuthUser $user, array $params): array { }
}
```

**Saved:** 7 lines, no interface, no registration array

### Scenario 2: Complex Action with Params

**Before:**
```php
class Service extends Model implements HasRestActions
{
    public function getRestActions(): array
    {
        return [
            'updatePricing' => [
                'method' => 'actionUpdatePricing',
                'description' => 'Update pricing for all tiers',
                'capability' => 'update',
                'requires_params' => true
            ]
        ];
    }

    public function actionUpdatePricing(AuthUser $user, array $params): array
    {
        // Complex pricing update logic
    }
}
```

**After:**
```php
class Service extends Model
{
    #[Action(
        capability: 'update',
        description: 'Update pricing for all tiers',
        requiresParams: true
    )]
    public function updatePricing(AuthUser $user, array $params): array
    {
        // Same complex pricing update logic
    }
}
```

**Saved:** 8 lines, clearer attribute declaration

### Scenario 3: Multiple Actions

**Before:**
```php
class Service extends Model implements HasRestActions
{
    public function getRestActions(): array
    {
        return [
            'duplicate' => [
                'method' => 'actionDuplicate',
                'description' => 'Duplicate service',
                'capability' => 'create'
            ],
            'archive' => [
                'method' => 'actionArchive',
                'description' => 'Archive service',
                'capability' => 'update'
            ],
            'restore' => [
                'method' => 'actionRestore',
                'description' => 'Restore service',
                'capability' => 'update'
            ],
            'updatePricing' => [
                'method' => 'actionUpdatePricing',
                'description' => 'Update pricing',
                'capability' => 'update',
                'requires_params' => true
            ]
        ];
    }

    public function actionDuplicate(AuthUser $user, array $params): array { }
    public function actionArchive(AuthUser $user, array $params): array { }
    public function actionRestore(AuthUser $user, array $params): array { }
    public function actionUpdatePricing(AuthUser $user, array $params): array { }
}
```

**After:**
```php
class Service extends Model
{
    #[Action(capability: 'create', description: 'Duplicate service')]
    public function duplicate(AuthUser $user, array $params): array { }

    #[Action(capability: 'update', description: 'Archive service')]
    public function archive(AuthUser $user, array $params): array { }

    #[Action(capability: 'update', description: 'Restore service')]
    public function restore(AuthUser $user, array $params): array { }

    #[Action(
        capability: 'update',
        description: 'Update pricing',
        requiresParams: true
    )]
    public function updatePricing(AuthUser $user, array $params): array { }
}
```

**Saved:** ~20 lines for 4 actions

---

## Frequently Asked Questions

### Q: Do I need to migrate?

**A:** No. The interface approach is fully supported and will continue to work. Migration is optional for code cleanup benefits.

### Q: Can I use both approaches in the same model?

**A:** No. If a model implements `HasRestActions`, the interface method takes precedence and reflective discovery is skipped entirely.

### Q: What if I have many models to migrate?

**A:** Migrate incrementally:
1. Start with new models (use attributes from the start)
2. Migrate simple models first (1-2 actions)
3. Migrate complex models last
4. No deadline - take your time

### Q: Will action endpoint URLs change?

**A:** Action names may change if you rename methods:

**Before:** `/tr-api/rest/services/5/actions/duplicate` (from registration array)

**After:** `/tr-api/rest/services/5/actions/duplicate` (from method name, same URL)

If you rename `actionDuplicate` → `duplicate`, URL stays the same because action name is derived from method name.

If you rename `actionDuplicate` → `duplicateService`, URL becomes `/tr-api/rest/services/5/actions/duplicate-service`

### Q: How do I test after migration?

**A:** Use the same curl commands:

```bash
# Test discovery worked
curl http://site.test/tr-api/rest/services/5

# Test action execution
curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate

# Test authorization
curl -X POST http://site.test/tr-api/rest/services/5/actions/duplicate
# Should check policy capability
```

### Q: What about existing API consumers?

**A:** Action endpoints remain the same. If you keep the same action names, nothing breaks for API consumers.

### Q: How do I clear discovery cache during development?

**A:** Cache clears automatically between requests. For manual clearing:

```php
// In code
\MakerMaker\Http\ReflectiveActionDiscovery::clearCache();

// Or restart PHP-FPM/Apache
```

---

## Rollback Plan

If you migrate and need to rollback:

1. **Restore interface:**
   ```php
   class Service extends Model implements HasRestActions
   {
       public function getRestActions(): array { /* restore */ }
   }
   ```

2. **Restore action methods:**
   ```php
   // Change back
   public function duplicate(...) → public function actionDuplicate(...)
   ```

3. **Remove attributes:**
   ```php
   // Remove
   #[Action(...)]
   ```

4. **Test endpoints still work**

Git makes this easy:
```bash
git checkout main -- app/Models/Service.php
```

---

## Summary

**No Migration Required:**
- Interface approach (`HasRestActions`) still fully supported
- Existing code requires zero changes
- Migration is optional for cleaner code

**Migration Benefits:**
- 40%+ less code
- Zero configuration
- Self-documenting
- Cleaner method names
- Automatic discovery

**Recommendation:**
- **Existing models:** Keep as-is (no rush to migrate)
- **New models:** Use attributes from the start
- **Refactoring:** Migrate to attributes for cleanup

**Both approaches work. Choose what fits your workflow.**
