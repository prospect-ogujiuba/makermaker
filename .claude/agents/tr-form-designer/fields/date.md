# Date Field Types

## date()
For DATE columns.

```php
$form->date('purchase_date')
    ->setLabel('Purchase Date')
    ->setHelp('Date of purchase')
    ->setFormat('yy-mm-dd')
    ->setAttribute('placeholder', 'YYYY-MM-DD')
```

**Format:** 'yy-mm-dd' (jQuery datepicker format)

## datetime()
For DATETIME, TIMESTAMP columns (full timestamp).

```php
$form->datetime('scheduled_at')
    ->setLabel('Scheduled Date/Time')
    ->setHelp('When this is scheduled')
```

**Usually:** date() is sufficient for most forms

## time()
For TIME columns, scheduling.

```php
$form->time('start_time')
    ->setLabel('Start Time')
    ->setHelp('Preferred start time')
```

## Common Date Fields

**Purchase/Transaction:**
```php
$form->date('purchase_date')
    ->setLabel('Purchase Date')
```

**Expiration:**
```php
$form->date('expiration_date')
    ->setLabel('Expiration Date')
    ->setHelp('When this expires')
```

**Scheduling:**
```php
$form->date('scheduled_date')
    ->setLabel('Scheduled Date')

$form->time('scheduled_time')
    ->setLabel('Scheduled Time')
```

**Range (paired):**
```php
$form->row()
    ->withColumn(
        $form->date('start_date')
            ->setLabel('Start Date')
    )
    ->withColumn(
        $form->date('end_date')
            ->setLabel('End Date')
    )
```
