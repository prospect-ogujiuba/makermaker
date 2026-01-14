# Ownership-Based Authorization

<purpose>
Patterns for user-owned content where access depends on ownership.
</purpose>

<ownership_read>
```php
public function read(AuthUser $auth, $object)
{
    // Admins can view all
    if ($auth->isCapable('{manage_capability}')) {
        return true;
    }

    // List request (no specific object)
    if (!$object || !isset($object->id)) {
        return $auth->ID > 0;
    }

    // Check ownership
    if ($object->{ownership_field} && $object->{ownership_field} === $auth->ID) {
        return true;
    }

    return false;
}
```
</ownership_read>

<ownership_update>
```php
public function update(AuthUser $auth, $object)
{
    if ($auth->isCapable('{manage_capability}')) {
        return true;
    }

    if ($object->{ownership_field} && $object->{ownership_field} === $auth->ID) {
        return true;
    }

    return false;
}
```
</ownership_update>

<dual_ownership>
When entity has both `assigned_to` AND `created_by`:

```php
public function read(AuthUser $auth, $object)
{
    if ($auth->isCapable('{manage_capability}')) {
        return true;
    }

    if (!$object || !isset($object->id)) {
        return $auth->ID > 0;
    }

    // Check assignment first (takes priority)
    if ($object->assigned_to && $object->assigned_to === $auth->ID) {
        return true;
    }

    // Fall back to creator
    if ($object->created_by && $object->created_by === $auth->ID) {
        return true;
    }

    return false;
}
```
</dual_ownership>

<ownership_fields>
Common ownership field patterns:
- `created_by` - Creator ownership (most common)
- `user_id` - User assignment
- `assigned_to` - Task/work item assignment
- `owner_id` - Explicit ownership
</ownership_fields>
