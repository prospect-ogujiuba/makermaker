# Special Field Types

## toggle()
For BOOLEAN, TINYINT(1), is_* fields.

```php
$form->toggle('is_active')
    ->setLabel('Active')
    ->setHelp('Enable or disable this item')
    ->setText('Active', 'Inactive')
```

**setText():** Custom on/off labels (optional)

## repeater()
For JSON metadata, key-value pairs.

```php
$form->repeater('metadata')
    ->setLabel('Additional Attributes')
    ->setHelp('Custom key-value metadata')
    ->setFields([
        $form->text('key')
            ->setLabel('Attribute Name'),
        $form->text('value')
            ->setLabel('Value'),
    ])
    ->setTitle('Metadata Entry')
    ->confirmRemove()
```

**Requires:**
- Model cast: `'metadata' => 'array'`
- Model format: `'metadata' => 'json_encode'`

## builder()
For complex JSON with component types.

```php
$form->builder('content_blocks')
    ->setLabel('Content Blocks')
    ->setHelp('Add flexible content sections')
    ->setComponents([
        'text_block' => [
            'label' => 'Text Block',
            'fields' => [
                $form->text('title')->setLabel('Title'),
                $form->textarea('content')->setLabel('Content')
            ]
        ],
        'image_block' => [
            'label' => 'Image Block',
            'fields' => [
                $form->image('image')->setLabel('Image'),
                $form->text('caption')->setLabel('Caption')
            ]
        ]
    ])
```

## color()
For color picker fields.

```php
$form->color('brand_color')
    ->setLabel('Brand Color')
    ->setHelp('Select brand color')
    ->setDefault('#ffffff')
```

## swatches()
For predefined color options.

```php
$form->swatches('theme_color')
    ->setLabel('Theme Color')
    ->setOptions([
        'Red' => '#ff0000',
        'Blue' => '#0000ff',
        'Green' => '#00ff00',
    ])
```
