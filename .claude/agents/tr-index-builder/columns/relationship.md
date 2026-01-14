# Relationship Column Types

## Accessor Notation (for eager loaded)
When relationship is in model $with.

```php
'createdBy.user_nicename' => [
    'label' => 'Created By'
]
```

No callback needed - TypeRocket resolves automatically.

## Callback Pattern (for optional)
When relationship may be null.

```php
'category_id' => [
    'label' => 'Category',
    'callback' => function($value, $item) {
        return $item->category
            ? esc_html($item->category->name)
            : '<span class="text-muted">N/A</span>';
    }
]
```

## Common Patterns

### Type/Category
```php
'type_id' => [
    'label' => 'Type',
    'callback' => function($value, $item) {
        return $item->type
            ? esc_html($item->type->name)
            : '<span class="text-muted">N/A</span>';
    }
]
```

### Audit User
```php
'createdBy.user_nicename' => [
    'label' => 'Created By'
],
'updatedBy.user_nicename' => [
    'label' => 'Updated By'
]
```

### Parent (self-referential)
```php
'parent_id' => [
    'label' => 'Parent',
    'callback' => function($value, $item) {
        return $item->parentCategory
            ? esc_html($item->parentCategory->name)
            : '<span class="text-muted">Root</span>';
    }
]
```

## Relationship Method Naming

| FK Column | Method | Accessor |
|-----------|--------|----------|
| type_id | type() | $item->type |
| category_id | category() | $item->category |
| created_by | createdBy() | $item->createdBy |
| parent_id | parentCategory() | $item->parentCategory |
