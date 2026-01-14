# belongsTo Relationships

## Pattern
N:1 relationship where this model has the foreign key.

## Template
```php
/** {Entity} belongs to a {RelatedEntity} */
public function {relatedEntity}()
{
    return $this->belongsTo({RelatedEntity}::class, '{foreign_key_column}');
}
```

## Naming Convention
- Method name: camelCase singular
- Foreign key: `{related_entity}_id`

## Examples

```php
/** Service belongs to a ServiceType */
public function serviceType()
{
    return $this->belongsTo(ServiceType::class, 'service_type_id');
}

/** Service belongs to a ServiceCategory */
public function category()
{
    return $this->belongsTo(ServiceCategory::class, 'category_id');
}

/** ServicePrice belongs to a PricingTier */
public function pricingTier()
{
    return $this->belongsTo(PricingTier::class, 'pricing_tier_id');
}
```

## Import Required
```php
use MakerMaker\Models\{RelatedModel};
```

## Eager Loading
Good candidates for $with - usually displayed in lists:
```php
protected $with = ['serviceType', 'category'];
```
