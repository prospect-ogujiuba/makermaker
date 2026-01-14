# hasMany Relationships

## Pattern
1:N relationship where related model has the foreign key.

## Template
```php
/** {Entity} has many {RelatedEntities} */
public function {relatedEntities}()
{
    return $this->hasMany({RelatedEntity}::class, '{foreign_key_on_related}');
}
```

## Naming Convention
- Method name: camelCase plural
- Foreign key: on related table, references this model

## Examples

```php
/** Service has many ServicePrices */
public function prices()
{
    return $this->hasMany(ServicePrice::class, 'service_id');
}

/** ServiceType has many Services */
public function services()
{
    return $this->hasMany(Service::class, 'service_type_id');
}

/** ServiceCategory has many Services */
public function services()
{
    return $this->hasMany(Service::class, 'category_id');
}
```

## Import Required
```php
use MakerMaker\Models\{RelatedModel};
```

## Eager Loading
Generally avoid in $with unless collection is small:
```php
// OK for small sets
protected $with = ['prices'];

// Avoid for large sets
// protected $with = ['services']; // Could be hundreds
```

## Usage
```php
$service = Service::find(1);
$prices = $service->prices; // Collection of ServicePrice
```
