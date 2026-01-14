# Date Field Type Defaults

<purpose>
Default validation rules for date/time column types.
</purpose>

<timestamp_fields>
```php
// Framework-managed timestamps - no validation
$rules['created_at'] = '';
$rules['updated_at'] = '';
$rules['deleted_at'] = '';
```

TypeRocket handles these automatically.
</timestamp_fields>

<custom_date_fields>
```php
// Custom date fields - framework validates format
$rules['start_date'] = '';
$rules['end_date'] = '';
$rules['scheduled_at'] = '';
```

Date validation handled by framework.
Use empty string unless business rules apply.
</custom_date_fields>

<date_business_rules>
```php
// If business rules need enforcement, use conditional
protected function conditionalValidation()
{
    $data = $this->getFields();
    $start = $data['start_date'] ?? null;
    $end = $data['end_date'] ?? null;

    if ($start && $end && strtotime($end) < strtotime($start)) {
        $this->setError('end_date', 'End date must be after start date.');
    }
}
```
</date_business_rules>
