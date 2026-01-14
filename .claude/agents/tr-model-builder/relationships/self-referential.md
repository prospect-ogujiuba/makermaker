# Self-Referential Relationships

## Pattern
Model relates to itself (parent/child hierarchy).

## Template
```php
/** {Entity} belongs to parent {Entity} */
public function parent{Entity}()
{
    return $this->belongsTo({Entity}::class, 'parent_id');
}

/** {Entity} has many child {Entities} */
public function child{Entities}()
{
    return $this->hasMany({Entity}::class, 'parent_id');
}
```

## Examples

```php
/** ServiceCategory belongs to parent ServiceCategory */
public function parentCategory()
{
    return $this->belongsTo(ServiceCategory::class, 'parent_id');
}

/** ServiceCategory has many child ServiceCategories */
public function childCategories()
{
    return $this->hasMany(ServiceCategory::class, 'parent_id');
}
```

## Naming Convention
- Parent: `parent{Entity}` singular
- Children: `child{Entities}` plural

## Eager Loading
Parent is good for eager loading (breadcrumbs):
```php
protected $with = ['parentCategory'];
```

Children should NOT be eager loaded (recursive explosion).

## Query Helpers

```php
/** Get root categories (no parent) */
public function getRoots()
{
    return $this->where('parent_id', null)
        ->where('deleted_at', null)
        ->orderBy('name')
        ->findAll();
}

/** Get full ancestry path */
public function getAncestors()
{
    $ancestors = [];
    $current = $this->parentCategory;
    while ($current) {
        array_unshift($ancestors, $current);
        $current = $current->parentCategory;
    }
    return $ancestors;
}
```
