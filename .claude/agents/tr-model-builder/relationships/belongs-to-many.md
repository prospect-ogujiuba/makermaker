# belongsToMany Relationships

## Pattern
N:N relationship through a junction (pivot) table.

## Template
```php
/** {Entity} belongs to many {RelatedEntities} */
public function {relatedEntities}()
{
    return $this->belongsToMany(
        {RelatedEntity}::class,
        GLOBAL_WPDB_PREFIX . 'srvc_{junction_table}',
        '{this_foreign_key}',
        '{related_foreign_key}'
    );
}
```

## Naming Convention
- Method name: camelCase plural
- Junction table: `srvc_{entity1}_{entity2}` (alphabetical or logical order)

## Important
Always use `GLOBAL_WPDB_PREFIX` for junction tables:
```php
GLOBAL_WPDB_PREFIX . 'srvc_service_equipment'
```

## Examples

```php
/** Service belongs to many Equipment */
public function equipment()
{
    return $this->belongsToMany(
        Equipment::class,
        GLOBAL_WPDB_PREFIX . 'srvc_service_equipment',
        'service_id',
        'equipment_id'
    );
}

/** Service belongs to many Deliverables */
public function deliverables()
{
    return $this->belongsToMany(
        Deliverable::class,
        GLOBAL_WPDB_PREFIX . 'srvc_service_deliverables',
        'service_id',
        'deliverable_id'
    );
}

/** Service has addon services (self-referential N:N) */
public function addonServices()
{
    return $this->belongsToMany(
        Service::class,
        GLOBAL_WPDB_PREFIX . 'srvc_service_addons',
        'service_id',
        'addon_service_id'
    );
}
```

## Import Required
```php
use MakerMaker\Models\{RelatedModel};
```

## Eager Loading
Generally avoid - collections can be large:
```php
// Use explicit loading instead
$service->load('equipment');
```
