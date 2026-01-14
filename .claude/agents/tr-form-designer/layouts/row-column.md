# Row/Column Layout

## Two-Column Row (50/50)
```php
$form->row()
    ->withColumn(
        $form->text('name')
            ->setLabel('Name')
    )
    ->withColumn(
        $form->text('sku')
            ->setLabel('SKU')
    )
```

## Single Column (Full Width)
```php
$form->textarea('description')
    ->setLabel('Description')
    ->setAttribute('rows', '4')
```

Or with explicit row for alignment:
```php
$form->row()
    ->withColumn(
        $form->textarea('description')
            ->setLabel('Description')
    )
    ->withColumn()  // Empty column for spacing
```

## When to Use Two Columns

**Good for:**
- Related pairs (name + sku)
- Start/end (start_date + end_date)
- Min/max (min_value + max_value)
- Short text fields
- Selects side by side
- Toggle pairs

**Bad for:**
- Textarea
- Editor
- Repeater
- Long descriptions

## Empty Column
```php
$form->row()
    ->withColumn(
        $form->text('field')
    )
    ->withColumn()  // Maintains spacing
```

## Mixed Layout Example
```php
[
    // Two columns
    $form->row()
        ->withColumn($form->text('name'))
        ->withColumn($form->text('sku')),

    // Two columns
    $form->row()
        ->withColumn($form->select('type_id'))
        ->withColumn($form->toggle('is_active')),

    // Full width
    $form->textarea('description'),

    // Full width
    $form->repeater('metadata'),
]
```
