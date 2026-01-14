# Capability Method Patterns

<purpose>
Individual method patterns for different authorization scenarios.
</purpose>

<simple_capability>
```php
public function {method}(AuthUser $auth, $object)
{
    return $auth->isCapable('{capability}');
}
```
</simple_capability>

<multi_capability>
```php
public function {method}(AuthUser $auth, $object)
{
    return $auth->isCapable('{primary_capability}')
        || $auth->isCapable('{secondary_capability}');
}
```
</multi_capability>

<admin_or_owner>
```php
public function {method}(AuthUser $auth, $object)
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
</admin_or_owner>

<capability_naming>
**Standard WordPress Capability Conventions:**

Admin-level (full control):
- `manage_{plural_entity}` - Primary admin capability
- `manage_options` - WordPress core admin

Editor-level (edit existing):
- `edit_{plural_entity}` - Can edit but not delete

Author-level (own content):
- `edit_own_{entity}` - Edit own items only

Entity-specific:
- `manage_services` - Service catalog entities
- `manage_contact_submissions` - Contact form management

**Default selection:**
- Simple entities: `manage_{entity}` for all CRUD
- User-owned: `manage_{entity}` admin, ownership checks for read/update
- Public read: `true` for read, `manage_{entity}` for CUD
</capability_naming>
