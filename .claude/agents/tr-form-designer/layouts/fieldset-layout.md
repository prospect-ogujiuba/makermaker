# Fieldset Layout

## Basic Fieldset
```php
$form->fieldset(
    'Section Title',
    'Section description or help text',
    [
        // Array of field rows
    ]
)
```

## Parameters
1. **Title:** Section heading
2. **Description:** Help text below heading
3. **Fields:** Array of form fields or rows

## Grouping Guidelines

### Identity Fieldset
```php
$form->fieldset(
    'Identification',
    'Core identity information',
    [
        // name, sku, slug, code
    ]
)
```

### Classification Fieldset
```php
$form->fieldset(
    'Classification',
    'Category and type information',
    [
        // type_id, category_id, status
    ]
)
```

### Details Fieldset
```php
$form->fieldset(
    'Details',
    'Detailed information',
    [
        // description, notes, content
    ]
)
```

### Configuration Fieldset
```php
$form->fieldset(
    'Configuration',
    'Settings and options',
    [
        // is_active, sort_order, flags
    ]
)
```

### Pricing Fieldset
```php
$form->fieldset(
    'Pricing',
    'Price and cost information',
    [
        // price, cost, discount
    ]
)
```

### Metadata Fieldset
```php
$form->fieldset(
    'Additional Data',
    'Custom metadata and attributes',
    [
        // metadata repeater
    ]
)
```

## Multiple Fieldsets in Tab
```php
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset('Identity', 'Core info', [...]),
    $form->fieldset('Classification', 'Types', [...]),
    $form->fieldset('Details', 'Description', [...]),
])
```
