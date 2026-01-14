# Policy Class Pattern

<purpose>
Base template for TypeRocket Policy classes.
</purpose>

<template>
```php
<?php

namespace MakerMaker\Auth;

use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class {Entity}Policy extends Policy
{
    public function create(AuthUser $auth, $object)
    {
        // Implementation
    }

    public function read(AuthUser $auth, $object)
    {
        // Implementation
    }

    public function update(AuthUser $auth, $object)
    {
        // Implementation
    }

    public function destroy(AuthUser $auth, $object)
    {
        // Implementation
    }
}
```
</template>

<auto_discovery>
TypeRocket automatically discovers policies using naming convention:
- Model: `MakerMaker\Models\Equipment`
- Policy: `MakerMaker\Auth\EquipmentPolicy`

Discovery happens in `MakermakerTypeRocketPlugin::discoverPolicies()`:
```php
$policyFiles = glob(MAKERMAKER_PLUGIN_DIR . '/app/Auth/*Policy.php');
foreach ($policyFiles as $file) {
    $policyName = basename($file, '.php');
    $modelName = str_replace('Policy', '', $policyName);
    // Auto-pairs EquipmentPolicy → Models\Equipment
}
```

No manual registration required.
</auto_discovery>

<auth_user_methods>
```php
// Capability checking
$auth->isCapable('manage_services')  // Check WordPress capability
$auth->isAdmin()                      // Is user administrator
$auth->can('publish_posts')           // Alias for isCapable

// User properties
$auth->ID                             // User ID (int)
$auth->user_login                     // Username
$auth->user_email                     // Email
```
</auth_user_methods>

<method_signature>
All CRUD methods receive:
- `AuthUser $auth` - Current user object
- `$object` - Model instance (or null for create/list)

Return: `bool` - true allows, false denies
</method_signature>
