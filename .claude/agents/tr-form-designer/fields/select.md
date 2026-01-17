# Select Field Types

## ENUM Select

For ENUM columns, status fields.

```php
$form->select('status')
    ->setLabel('Status')
    ->setHelp('Current status')
    ->setOptions(array_merge(
        ['Select Status' => NULL],
        DatabaseHelper::getEnumValues('prfx_table', 'status')
    ))
    ->markLabelRequired()
```

**Always:**

- Merge with NULL default option
- Use DatabaseHelper::getEnumValues()

## Static Options Select

For predefined option lists.

```php
$form->select('priority')
    ->setLabel('Priority')
    ->setOptions([
        'Select Priority' => NULL,
        'Low' => 'low',
        'Medium' => 'medium',
        'High' => 'high',
    ])
```

## Radio Buttons

For small option sets (2-4 choices).

```php
$form->radio('type')
    ->setLabel('Type')
    ->setOptions([
        'Option A' => 'a',
        'Option B' => 'b',
    ])
```

## Checkbox

For multiple selections.

```php
$form->checkbox('features')
    ->setLabel('Features')
    ->setOptions([
        'Feature 1' => 'feature_1',
        'Feature 2' => 'feature_2',
    ])
```

Note: Checkboxes store as array, need JSON cast.
