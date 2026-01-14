# Phase 3: Generate Fields Class

<purpose>
Generate TypeRocket Fields class with validation rules.
</purpose>

<template>
```php
<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class {Entity}Fields extends Fields
{
    protected $run = true;

    protected function fillable()
    {
        return [];
    }

    protected function rules()
    {
        $request = Request::new();
        $route_args = $request->getDataGet('route_args');
        $id = $route_args[0] ?? null;
        $wpdb_prefix = GLOBAL_WPDB_PREFIX;

        $rules = [];

        // Generated rules here

        return $rules;
    }

    protected function messages()
    {
        return [];
    }
}
```
</template>

<output_files>

## 1. Fields Class
Path: `app/Http/Fields/{Entity}Fields.php`

## 2. Fields Handoff
Path: `{entity}-fields-handoff.yaml`

```yaml
validation:
  model: Equipment
  file: app/Http/Fields/EquipmentFields.php
  rules_count: 12
  has_custom_messages: false
  has_conditional_logic: false
  validated_fields:
    - sku
    - name
    - equipment_type_id
  enum_fields:
    - status
next_step: controller
```

</output_files>

<conditional_validation>
For interdependent field rules:

```php
protected function conditionalValidation()
{
    $data = $this->getFields();
    $type = $data['contact_type'] ?? null;

    if ($type === 'quote_request') {
        if (empty($data['service_id'])) {
            $this->setError('service_id', 'Quote requests must specify a service.');
        }
    }
}
```
</conditional_validation>
