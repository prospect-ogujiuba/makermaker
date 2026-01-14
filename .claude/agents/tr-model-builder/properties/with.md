# Eager Loading ($with)

## Purpose
Prevent N+1 queries by loading relationships automatically.

## Syntax
```php
protected $with = [
    'relationshipName',
    'nested.relationship',
];
```

## What to Eager Load

**Good candidates:**
- Type/category (always displayed in lists)
- Parent relationship (for hierarchy display)
- Frequently accessed belongsTo

**Avoid eager loading:**
- hasMany collections (can be expensive)
- Rarely accessed relationships
- Large nested trees

## Examples

```php
// Service model - type always shown
protected $with = [
    'serviceType',
    'category',
];

// ServiceCategory model - needs parent for breadcrumb
protected $with = [
    'parentCategory',
];

// ServicePrice model - needs tier and model info
protected $with = [
    'pricingTier',
    'pricingModel',
];

// Complex: Service with nested pricing
protected $with = [
    'serviceType',
    'category',
    'prices.pricingTier',
    'prices.pricingModel',
];
```

## Nested Relationships

Use dot notation for nested eager loading:
```php
'prices.pricingTier'     // Load prices, then each price's tier
'category.parentCategory' // Load category, then its parent
```

## Performance Guidelines

| Relationship Type | Eager Load? |
|-------------------|-------------|
| belongsTo (type, category) | Usually YES |
| belongsTo (parent) | YES if displayed |
| hasMany (small set) | Maybe |
| hasMany (large set) | NO |
| belongsToMany | Rarely |

## REST API Impact

$with affects REST responses - eager loaded relationships are included:
```json
{
  "id": 1,
  "name": "Service A",
  "serviceType": {
    "id": 5,
    "name": "Consulting"
  }
}
```
