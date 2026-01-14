# Common Query Scopes

## Purpose
Reusable query methods for common data access patterns.

## Naming Conventions
- `get*` - Returns collection
- `find*` - Returns single record
- `search` - Keyword filtering
- `is*` - Returns boolean

## Standard Scopes

### getActive
```php
/**
 * Get all active {entities}
 */
public function getActive()
{
    return $this->where('is_active', 1)
        ->where('deleted_at', '=', null)
        ->orderBy('name', 'ASC')
        ->findAll();
}
```

### findBySlug
```php
/**
 * Find {entity} by slug
 */
public function findBySlug($slug)
{
    return $this->where('slug', $slug)
        ->where('deleted_at', '=', null)
        ->first();
}
```

### findBySKU
```php
/**
 * Find {entity} by SKU
 */
public function findBySKU($sku)
{
    return $this->where('sku', $sku)
        ->where('deleted_at', '=', null)
        ->first();
}
```

### search
```php
/**
 * Search {entities} by keyword
 */
public function search($keyword)
{
    return $this->where('name', 'LIKE', "%{$keyword}%")
        ->orWhere('description', 'LIKE', "%{$keyword}%")
        ->where('deleted_at', '=', null)
        ->findAll();
}
```

### getByCategory
```php
/**
 * Get {entities} by category
 */
public function getByCategory($categoryId)
{
    return $this->where('category_id', $categoryId)
        ->where('deleted_at', '=', null)
        ->orderBy('name', 'ASC')
        ->findAll();
}
```

## When to Include
- Only add scopes that will be commonly used
- Basic CRUD uses TypeRocket's query builder
- Don't over-engineer - add as needed
