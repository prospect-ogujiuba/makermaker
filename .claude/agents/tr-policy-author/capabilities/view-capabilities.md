# View Capabilities

<purpose>
Read and index capability patterns for different access levels.
</purpose>

<public_read>
```php
public function read(AuthUser $auth, $object)
{
    return true;
}
```

Use for:
- Public catalog browsing
- Service listings
- Equipment catalogs
</public_read>

<logged_in_read>
```php
public function read(AuthUser $auth, $object)
{
    return $auth->ID > 0;
}
```

Use for:
- Internal dashboards
- Member-only content
</logged_in_read>

<index_vs_show>
```php
public function read(AuthUser $auth, $object)
{
    // Index request (listing)
    if (!$object || !isset($object->id)) {
        return $auth->ID > 0;
    }

    // Show request (specific item)
    return $auth->isCapable('{capability}');
}
```

Use when index and show have different access levels.
</index_vs_show>
