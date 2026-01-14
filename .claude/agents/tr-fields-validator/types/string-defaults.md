# String Field Type Defaults

<purpose>
Default validation rules for string/text column types.
</purpose>

<varchar_fields>
| Column Type | Default Rule |
|-------------|--------------|
| varchar(32) | max:32 |
| varchar(64) | max:64 |
| varchar(128) | max:128 |
| varchar(255) | max:255 |
| varchar(512) | max:512 |
</varchar_fields>

<text_fields>
```php
$rules['description'] = '';
$rules['long_text'] = '';
$rules['notes'] = '';
```

TEXT columns: empty string (no constraint).
</text_fields>

<identity_fields>
```php
// SKU - typically optional unique
$rules['sku'] = "?unique:sku:{$wpdb_prefix}{table}@id:{$id}|max:64";

// Slug - typically required unique
$rules['slug'] = "unique:slug:{$wpdb_prefix}{table}@id:{$id}|required|max:64";

// Code - config entities
$rules['code'] = "unique:code:{$wpdb_prefix}{table}@id:{$id}|required|max:32";

// Name - often unique
$rules['name'] = "unique:name:{$wpdb_prefix}{table}@id:{$id}|required|max:128";
```
</identity_fields>

<contact_fields>
```php
$rules['email'] = '?email|max:255';
$rules['phone'] = '?max:32';
$rules['website'] = '?url|max:512';
```
</contact_fields>
