# Numeric Field Type Defaults

<purpose>
Default validation rules for numeric column types.
</purpose>

<integer_fields>
| Column Type | Default Rule |
|-------------|--------------|
| bigint | numeric |
| int | numeric |
| smallint | numeric |
| tinyint (bool) | numeric\|min:0\|max:1 |
</integer_fields>

<decimal_fields>
```php
// decimal(10,2) - currency
$rules['price'] = 'required|numeric|min:0.01';
$rules['cost'] = '?numeric|min:0.01';

// decimal(12,2) - large amounts
$rules['quantity'] = '?numeric|min:0.01|max:999999.99';

// decimal(8,2) - hours/time
$rules['hours'] = '?numeric|min:0.01|max:9999.99';
$rules['duration'] = '?numeric|min:0.01|max:9999.99';
```
</decimal_fields>

<percentage_fields>
```php
$rules['percentage'] = 'numeric|min:0|max:100';
$rules['discount_rate'] = '?numeric|min:0|max:100';
```
</percentage_fields>

<order_fields>
```php
$rules['display_order'] = 'numeric|min:0';
$rules['sort_order'] = '?numeric|min:0';
```

Allow zero for ordering fields.
</order_fields>
