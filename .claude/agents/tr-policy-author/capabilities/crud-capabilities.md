# CRUD Capabilities

<purpose>
Standard CRUD method implementations for policy classes.
</purpose>

<create_method>
```php
public function create(AuthUser $auth, $object)
{
    return $auth->isCapable('{capability}');
}
```

Notes:
- $object typically null for create
- Single capability check
</create_method>

<read_method_admin_only>
```php
public function read(AuthUser $auth, $object)
{
    return $auth->isCapable('{capability}');
}
```
</read_method_admin_only>

<update_method>
```php
public function update(AuthUser $auth, $object)
{
    return $auth->isCapable('{capability}');
}
```
</update_method>

<destroy_method>
```php
public function destroy(AuthUser $auth, $object)
{
    return $auth->isCapable('{capability}');
}
```

Notes:
- Often requires higher capability than update
- Consider soft delete implications
</destroy_method>
