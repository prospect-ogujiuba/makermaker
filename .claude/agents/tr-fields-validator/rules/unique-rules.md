# Unique Validation Rules

<purpose>
Unique constraint validation with update support.
</purpose>

<syntax>
```
unique:column:table@id_column:current_id_value
```

- column: Field being validated
- table: Full table name with prefix
- @id_column: Primary key column (usually 'id')
- current_id_value: ID to exclude (for updates)
  </syntax>

<required_unique>

```php
$rules['name'] = "unique:name:{$wpdb_prefix}prfx_equipment@id:{$id}|required|max:128";
$rules['slug'] = "unique:slug:{$wpdb_prefix}prfx_services@id:{$id}|required|max:64";
```

Order: unique first, then required, then max.
</required_unique>

<optional_unique>

```php
$rules['sku'] = "?unique:sku:{$wpdb_prefix}prfx_equipment@id:{$id}|max:64";
$rules['code'] = "?unique:code:{$wpdb_prefix}prfx_types@id:{$id}|max:32";
```

Use ? prefix for nullable unique fields.
Without ?, empty values fail unique check.
</optional_unique>

<update_context>

```php
$request = Request::new();
$route_args = $request->getDataGet('route_args');
$id = $route_args[0] ?? null;
$wpdb_prefix = GLOBAL_WPDB_PREFIX;
```

Always get ID from route to exclude current record.
</update_context>
