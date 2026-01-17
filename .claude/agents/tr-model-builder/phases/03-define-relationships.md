# Phase 3: Define Relationships

## Purpose

Generate all relationship methods based on foreign keys and migration hints.

## Relationship Detection

### From Foreign Keys

Each FK column creates a belongsTo relationship:

- `service_type_id` -> `serviceType()` belongsTo ServiceType

### From Migration Hints

Check `discovery_hints` for:

- hasMany relationships (reverse FKs)
- belongsToMany (junction tables)
- Self-referential (parent_id)

## Module Triggers

| FK Pattern                 | Load Module                       |
| -------------------------- | --------------------------------- |
| `*_id` -> other table      | relationships/belongs-to.md       |
| Other table has FK to this | relationships/has-many.md         |
| Junction table exists      | relationships/belongs-to-many.md  |
| `parent_id` column         | relationships/self-referential.md |
| `created_by`, `updated_by` | relationships/wp-user.md          |

## Standard Relationships

All models include:

```php
/** Created by WP user */
public function createdBy()
{
    return $this->belongsTo(WPUser::class, 'created_by');
}

/** Updated by WP user */
public function updatedBy()
{
    return $this->belongsTo(WPUser::class, 'updated_by');
}
```

## Relationship Naming

| Type          | Convention         | Example                     |
| ------------- | ------------------ | --------------------------- |
| belongsTo     | camelCase singular | serviceType, parentCategory |
| hasMany       | camelCase plural   | prices, childCategories     |
| belongsToMany | camelCase plural   | equipment, deliverables     |

## Query Scopes (Optional)

**Load:** `@scopes/common-scopes.md` if needed

Common patterns:

- `getActive()` - Active, non-deleted records
- `findBySlug()` - Lookup by slug
- `search()` - Keyword search

**Load:** `@scopes/computed-properties.md` if needed

Common patterns:

- `getFormattedX()` - Display formatting
- `isX()` - Boolean checks
- `getFullName()` - Composite display name

## Output

Relationship configuration:

```yaml
relationships:
  - name: serviceType
    type: belongsTo
    model: ServiceType
    foreign_key: service_type_id
  - name: prices
    type: hasMany
    model: ServicePrice
    foreign_key: service_id
  - name: equipment
    type: belongsToMany
    model: Equipment
    junction: prfx_service_equipment
    this_fk: service_id
    related_fk: equipment_id
  - name: createdBy
    type: belongsTo
    model: WPUser
    foreign_key: created_by
  - name: updatedBy
    type: belongsTo
    model: WPUser
    foreign_key: updated_by

query_scopes: []
computed_properties: []
```

## Next Phase

Proceed to Phase 4: Create Handoff.
