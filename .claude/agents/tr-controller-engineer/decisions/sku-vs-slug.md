# Decision: SKU vs Slug AutoCode Pattern

<purpose>
Determine which AutoCodeHelper method to use based on entity characteristics.
</purpose>

## Decision Tree

```
IF 'sku' in model.fillable:
  IF 'slug' in model.fillable:
    → AutoCodeHelper::generateSkuAndSlug($fields)
    Examples: Service, Equipment (inventory entities)
  ELSE:
    → Check for manufacturer field
    IF 'manufacturer' in model.fillable:
      → autoGenerateCode($fields, 'sku', 'name', '-', $fields['manufacturer'], 'prefix', true)
    ELSE:
      → AutoCodeHelper::generateSku($fields)

ELSE IF 'slug' in model.fillable:
  → AutoCodeHelper::generateSlug($fields)
  Examples: ServiceCategory, EquipmentType (taxonomy entities)

ELSE IF 'code' in model.fillable:
  → AutoCodeHelper::generateCode($fields)
  Examples: ServiceTier, PricingModel (config entities)

ELSE:
  → No auto-generation needed
```

## Rationale

- **SKU + Slug**: Inventory items need both machine ID (SKU) and URL-friendly ID (slug)
- **SKU only**: Items with external references (manufacturer codes)
- **Slug only**: Hierarchical/categorical items for URL routing
- **Code only**: Configuration entities for internal reference
