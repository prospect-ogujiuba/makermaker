# Relationship Field Types

## belongsTo Select
For foreign key columns (*_id).

```php
$form->select('service_type_id')
    ->setLabel('Service Type')
    ->setHelp('Select the service type')
    ->setModelOptions(ServiceType::class, 'name', 'id', 'Select Type')
    ->markLabelRequired()
```

**setModelOptions() parameters:**
1. `Model::class` - Related model class
2. `'name'` - Display field (usually 'name' or 'title')
3. `'id'` - Value field (primary key)
4. `'Select Type'` - Default option text

## Import Required

```php
use MakerMaker\Models\ServiceType;
use MakerMaker\Models\Category;
```

## Common Relationship Patterns

**Type/Category (required):**
```php
$form->select('type_id')
    ->setLabel('Type')
    ->setModelOptions(Type::class, 'name', 'id', 'Select Type')
    ->markLabelRequired()
```

**Parent (optional, self-referential):**
```php
$form->select('parent_id')
    ->setLabel('Parent Category')
    ->setHelp('Leave empty for root category')
    ->setModelOptions(Category::class, 'name', 'id', 'None (Root)')
```

**Owner (optional):**
```php
$form->select('assigned_to')
    ->setLabel('Assigned To')
    ->setModelOptions(WPUser::class, 'user_nicename', 'ID', 'Unassigned')
```

## Search Select (for large datasets)
When model has many records, use search select:

```php
$form->search('customer_id')
    ->setLabel('Customer')
    ->setHelp('Search by name or email')
    ->setModelOptions(Customer::class, 'name', 'id')
```

## Multiple Relationships (belongsToMany)
For junction table relationships:

```php
$form->items('equipment_ids')
    ->setLabel('Equipment')
    ->setHelp('Select related equipment')
    ->setModelOptions(Equipment::class, 'name', 'id')
```

Note: Requires separate handling in controller.
