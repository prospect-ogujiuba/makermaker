# Phase 3: Generate Policy

<purpose>
Generate TypeRocket Policy class using selected patterns.
</purpose>

<trigger_loads>
Based on capability_design from Phase 2:
- Always → @patterns/policy-class.md
- Always → @patterns/capability-method.md
- If ownership_based → @capabilities/ownership-checks.md
</trigger_loads>

<generation_steps>

## 1. Policy Class Structure

```php
<?php

namespace MakerMaker\Auth;

use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

class {Entity}Policy extends Policy
{
    // CRUD methods
}
```

## 2. Method Generation by Pattern

**admin_only**: All methods check primary capability
**public_read**: read() returns true, others check capability
**ownership_based**: read/update check ownership OR capability

## 3. Custom Methods

Add any custom methods from capability_design.custom_methods

</generation_steps>

<output_files>

## 1. Policy File
Path: `app/Auth/{Entity}Policy.php`

## 2. Policy Handoff
Path: `{entity}-policy-handoff.yaml`

```yaml
policy:
  entity: Equipment
  file: app/Auth/EquipmentPolicy.php
  namespace: MakerMaker\Auth
  capabilities_used:
    - manage_services
  methods: [create, read, update, destroy]
  ownership_based: false
  custom_methods: []
next_step: controller
```

</output_files>

<auto_discovery_note>
TypeRocket auto-discovers policies via naming convention:
- Model: `MakerMaker\Models\Equipment`
- Policy: `MakerMaker\Auth\EquipmentPolicy`

No manual registration required.
</auto_discovery_note>
