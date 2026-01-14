# Custom Validation Rules

<purpose>
Callback and custom validation patterns.
</purpose>

<enum_validation>
```php
$rules['status'] = 'callback:checkInList:Equipment';
$rules['contact_type'] = 'callback:checkInList:ContactSubmission';
$rules['skill_level'] = '?callback:checkInList:Service';
```

TypeRocket reads ENUM values from database schema.
Model name must match exact class name.
Use ? prefix for nullable ENUMs.
</enum_validation>

<custom_callback>
```php
$rules['field'] = 'callback:methodName:arg1:arg2';

protected function methodName($value, $arg1, $arg2)
{
    if (!valid($value)) {
        $this->setError('field', 'Custom error message.');
        return false;
    }
    return true;
}
```
</custom_callback>

<conditional_validation>
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

Use for interdependent field rules.
Runs after basic rules validation.
</conditional_validation>
