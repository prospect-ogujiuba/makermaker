# System Fields

## Purpose
Readonly audit fields shown only in edit forms (when $current_id exists).

## Standard System Fields

### ID
```php
$form->text('id')
    ->setLabel('ID')
    ->setHelp('System generated unique identifier')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)
```

### Version
```php
$form->text('version')
    ->setLabel('Version')
    ->setHelp('Optimistic locking version number')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)
```

### Timestamps
```php
$form->text('created_at')
    ->setLabel('Created At')
    ->setHelp('Record creation timestamp')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)

$form->text('updated_at')
    ->setLabel('Updated At')
    ->setHelp('Last update timestamp')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)
```

### Audit Users
```php
$form->text('created_by_user')
    ->setLabel('Created By')
    ->setHelp('User who originally created this record')
    ->setAttribute('value', $createdBy->user_nicename ?? 'System')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)

$form->text('updated_by_user')
    ->setLabel('Last Updated By')
    ->setHelp('User who last updated this record')
    ->setAttribute('value', $updatedBy->user_nicename ?? 'System')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)
```

### Soft Delete
```php
$form->text('deleted_at')
    ->setLabel('Deleted At')
    ->setHelp('Timestamp when soft-deleted, if applicable')
    ->setAttribute('readonly', true)
    ->setAttribute('name', false)
    ->setAttribute('disabled', true)
```

## Key Attributes

- `readonly`: Prevents editing
- `name: false`: Prevents form submission
- `disabled`: Grays out field (for deleted_at)

## Layout Pattern

Always use two-column rows for system fields:
```php
$form->row()
    ->withColumn($form->text('id')...)
    ->withColumn($form->text('version')...)
```
