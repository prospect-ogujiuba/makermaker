# Helper: AutoCodeHelper

<when>Entity has SKU, slug, or code fields that need auto-generation</when>

## Location
`MakermakerCore\Helpers\AutoCodeHelper`

## Methods

```php
// For inventory entities (Service, Equipment) - has both SKU and slug
AutoCodeHelper::generateSkuAndSlug($fields);

// For most entities (categories, types) - slug only
AutoCodeHelper::generateSlug($fields);

// For config entities (tiers, models) - code only
AutoCodeHelper::generateCode($fields);
```

## Behavior
- Checks if fields already have values
- Only generates if empty
- Derives from name field
- Call AFTER authorization, BEFORE audit trail

## Decision Logic
See decisions/sku-vs-slug.md for selection criteria.
