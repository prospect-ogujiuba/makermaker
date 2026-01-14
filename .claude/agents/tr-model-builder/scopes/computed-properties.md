# Computed Properties

## Purpose
Derived values and formatted outputs.

## Naming Conventions
- `get*` - Returns computed value
- `is*`/`has*` - Returns boolean
- `getFormatted*` - Returns display-formatted value

## Common Patterns

### Formatted Currency
```php
/**
 * Get formatted unit cost
 */
public function getFormattedCost($currency = 'CAD')
{
    if ($this->unit_cost === null) {
        return 'Cost not available';
    }
    return ServiceCatalogHelper::formatCurrency($this->unit_cost, $currency);
}
```

### Full Name (Composite)
```php
/**
 * Get full display name
 */
public function getFullName()
{
    $parts = [$this->name];
    if ($this->manufacturer) {
        $parts[] = "({$this->manufacturer}";
        if ($this->model) {
            $parts[] = $this->model . ")";
        } else {
            $parts[count($parts) - 1] .= ")";
        }
    }
    return implode(' ', $parts);
}
```

### Boolean Checks
```php
/**
 * Check if entity is consumable
 */
public function isConsumable()
{
    return (bool)$this->is_consumable;
}

/**
 * Check if entity has children
 */
public function hasChildren()
{
    return $this->childCategories()->count() > 0;
}

/**
 * Check if entity is root (no parent)
 */
public function isRoot()
{
    return $this->parent_id === null;
}
```

### Status Display
```php
/**
 * Get status label
 */
public function getStatusLabel()
{
    if ($this->deleted_at) {
        return 'Deleted';
    }
    return $this->is_active ? 'Active' : 'Inactive';
}
```

## Guidelines
- Keep business logic in helpers, not models
- Computed properties for display only
- Use sparingly - only frequently accessed values
