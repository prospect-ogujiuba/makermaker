# Number Field Types

## Integer number()
For INT, BIGINT columns.

```php
$form->number('stock_quantity')
    ->setLabel('Stock Quantity')
    ->setHelp('Current quantity in stock')
    ->setAttribute('min', '0')
    ->setAttribute('max', '10000')
    ->setAttribute('step', '1')
    ->setDefault('0')
```

**Attributes:**
- `step`: '1' for integers
- `min`: Usually '0' for positive
- `max`: Based on business rules

## Decimal number()
For DECIMAL, FLOAT columns.

```php
$form->number('unit_price')
    ->setLabel('Unit Price')
    ->setHelp('Price per unit in CAD')
    ->setAttribute('min', '0')
    ->setAttribute('step', '0.01')
    ->setAttribute('placeholder', '0.00')
```

**Attributes:**
- `step`: '0.01' for currency
- `step`: '0.001' for precise decimals

## Common Number Fields

**Price/Cost:**
```php
$form->number('price')
    ->setLabel('Price')
    ->setAttribute('min', '0')
    ->setAttribute('step', '0.01')
    ->setAttribute('placeholder', '0.00')
```

**Quantity:**
```php
$form->number('quantity')
    ->setLabel('Quantity')
    ->setAttribute('min', '0')
    ->setAttribute('step', '1')
    ->setDefault('1')
```

**Sort Order:**
```php
$form->number('sort_order')
    ->setLabel('Sort Order')
    ->setHelp('Lower numbers appear first')
    ->setAttribute('min', '0')
    ->setAttribute('step', '1')
    ->setDefault('0')
```

**Percentage:**
```php
$form->number('discount_percent')
    ->setLabel('Discount %')
    ->setAttribute('min', '0')
    ->setAttribute('max', '100')
    ->setAttribute('step', '0.01')
```
