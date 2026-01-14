# String Validation Rules

<purpose>
Validation patterns for text/string fields.
</purpose>

<max_length>
```php
$rules['name'] = 'required|max:128';
$rules['short_desc'] = 'required|max:512';
$rules['long_desc'] = '';  // TEXT field, no max
```

Always match max to schema varchar length.
Use empty string for TEXT fields.
</max_length>

<min_length>
```php
$rules['password'] = 'required|min:8|max:255';
$rules['code'] = 'required|min:2|max:32';
```

Use min for minimum character requirements.
</min_length>

<email_validation>
```php
$rules['email'] = '?email|max:255';
$rules['contact_email'] = 'required|email|max:255';
```

TypeRocket validates email format.
Always include max for varchar constraint.
</email_validation>

<url_validation>
```php
$rules['website'] = '?url|max:512';
$rules['homepage'] = 'required|url|max:512';
```

Validates URL format, not existence.
Standard max:512 for URL fields.
</url_validation>

<phone_validation>
```php
$rules['phone'] = '?max:32';
$rules['phone_ext'] = '?max:50';
```

No built-in phone validation.
Use max constraint only.
</phone_validation>
