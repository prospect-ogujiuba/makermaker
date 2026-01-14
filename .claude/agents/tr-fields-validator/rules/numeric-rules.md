# Numeric Validation Rules

<purpose>
Validation patterns for numeric fields.
</purpose>

<basic_numeric>
```php
$rules['quantity'] = 'required|numeric';
$rules['count'] = '?numeric';
```

Use numeric for all number types (int, decimal, float).
</basic_numeric>

<min_max_constraints>
```php
$rules['price'] = 'required|numeric|min:0.01';
$rules['quantity'] = '?numeric|min:0.01|max:999999.99';
$rules['hours'] = '?numeric|min:0.01|max:9999.99';
$rules['percentage'] = 'numeric|min:0|max:100';
```

Match decimal precision to schema.
</min_max_constraints>

<boolean_fields>
```php
$rules['is_active'] = 'numeric|min:0|max:1';
$rules['is_featured'] = 'numeric|min:0|max:1';
```

Use numeric with min:0 max:1 for tinyint booleans.
NOT 'boolean' rule.
</boolean_fields>

<display_order>
```php
$rules['display_order'] = 'numeric|min:0';
$rules['sort_order'] = '?numeric|min:0';
```

Allow zero for order fields.
</display_order>

<wordpress_ids>
```php
$rules['featured_image_id'] = '?numeric|min:0';
$rules['user_id'] = '?numeric|min:0';
```

Allow zero (no selection) with min:0.
</wordpress_ids>
