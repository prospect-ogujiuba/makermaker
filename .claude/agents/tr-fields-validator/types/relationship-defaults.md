# Relationship Field Type Defaults

<purpose>
Default validation rules for foreign key columns.
</purpose>

<required_fk>
```php
// Non-nullable foreign key
$rules['category_id'] = 'required|numeric';
$rules['service_type_id'] = 'required|numeric';
$rules['equipment_type_id'] = 'required|numeric';
```

Use required|numeric for non-nullable FK columns.
</required_fk>

<optional_fk>
```php
// Nullable foreign key
$rules['parent_id'] = '?numeric';
$rules['assigned_to'] = '?numeric';
$rules['manager_id'] = '?numeric';
```

Use ?numeric for nullable FK columns.
Don't use min:1 on self-referencing (allows NULL).
</optional_fk>

<user_references>
```php
// WordPress user references
$rules['user_id'] = '?numeric|min:0';
$rules['created_by'] = '?numeric|min:0';
$rules['updated_by'] = '?numeric|min:0';
```

Allow zero with min:0 for optional user fields.
</user_references>

<media_references>
```php
// WordPress media IDs
$rules['featured_image_id'] = '?numeric|min:0';
$rules['attachment_id'] = '?numeric|min:0';
```

Allow zero (no selection) with min:0.
</media_references>
