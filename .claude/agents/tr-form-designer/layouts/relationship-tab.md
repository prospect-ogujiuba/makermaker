# Relationship Tab

## Purpose
Display related entities in edit forms (hasMany, belongsToMany).

## Conditional Loading
Only show when editing existing record:
```php
if (isset($current_id)) {
    // Relationship tab content
}
```

## Pattern
```php
if (isset($current_id) && $model->hasRelationships()) {
    $relationshipTabs = tr_tabs()->layoutTop();

    // For each hasMany relationship
    if ($model->relatedItems()->exists()) {
        $items = $model->relatedItems()
            ->select('id', 'name', 'status')
            ->take(100)
            ->get();

        $relationshipTabs->tab('Related Items', 'networking', [
            $form->fieldset(
                'Related Items',
                'Items linked to this record',
                $items->count() > 0
                    ? $items->map(function($item) use ($form) {
                        return $form->row()
                            ->withColumn(
                                $form->text('item_' . $item->id)
                                    ->setLabel('Item')
                                    ->setAttribute('value', $item->name)
                                    ->setAttribute('readonly', true)
                                    ->setAttribute('name', false)
                            );
                    })->toArray()
                    : [
                        $form->text('no_items')
                            ->setLabel('No related items found')
                            ->setAttribute('readonly', true)
                            ->setAttribute('name', false)
                    ]
            )
        ])->setDescription('Items using this record');
    }

    $tabs->tab('Relationships', 'admin-network', [
        $relationshipTabs->render()
    ])->setDescription('Related entities');
}
```

## Key Points

- **Limit results:** `->take(100)` prevents performance issues
- **Readonly display:** All fields are readonly, name=false
- **Nested tabs:** Use `tr_tabs()->layoutTop()` inside main tabs
- **Empty state:** Show "No items found" when empty
- **Dynamic IDs:** Append entity ID to field names to ensure uniqueness
