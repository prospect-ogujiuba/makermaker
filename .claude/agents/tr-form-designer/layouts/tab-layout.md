# Tab Layout

## Initialization
```php
$tabs = tr_tabs()
    ->setFooter($form->save('Save {Entity}'))
    ->layoutLeft();
```

**layoutLeft():** Tabs on left, content on right (default)
**layoutTop():** Tabs on top, content below

## Tab Structure
```php
$tabs->tab('Tab Name', 'icon-name', [
    // Array of fieldsets
])->setDescription('Tab description');
```

## Common Icons
- `admin-settings` - Overview, main settings
- `admin-generic` - Configuration, general
- `performance` - Metrics, performance
- `info` - Information, system
- `networking` - Relationships, connections
- `admin-network` - Network, linked items

## Standard Tab Order

1. **Overview** - Core entity info
2. **Settings/Config** - Flags, options
3. **Content** - Long text, descriptions
4. **Relationships** - Linked entities
5. **System** - Audit fields (always last)

## Tab Pattern
```php
$tabs->tab('Overview', 'admin-settings', [
    $form->fieldset(
        'Details',
        'Core information',
        [
            // Fields
        ]
    )
])->setDescription('Primary information');

$tabs->tab('Settings', 'admin-generic', [
    $form->fieldset(
        'Configuration',
        'Settings and options',
        [
            // Fields
        ]
    )
])->setDescription('Configuration options');
```

## Conditional System Tab
```php
if (isset($current_id)) {
    $tabs->tab('System', 'info', [
        $form->fieldset(
            'System Info',
            'System metadata',
            [
                // Readonly audit fields
            ]
        )
    ])->setDescription('System information');
}
```
