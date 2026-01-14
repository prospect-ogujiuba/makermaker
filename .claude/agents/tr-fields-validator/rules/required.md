# Required Field Rules

<purpose>
Rules for required vs optional field validation.
</purpose>

<required_rule>
```php
$rules['name'] = 'required|max:128';
```

Use when:
- Column is NOT NULL in schema
- Business logic requires value
</required_rule>

<optional_prefix>
```php
$rules['sku'] = '?max:64';
$rules['email'] = '?email|max:255';
```

Use ? prefix when:
- Column is nullable in schema
- Field is optional in business logic
- Empty/null values are valid
</optional_prefix>

<composition>
Order matters:
1. ? prefix (if optional)
2. Other rules after

Correct: `?email|max:255`
Wrong: `email|?max:255`
</composition>
