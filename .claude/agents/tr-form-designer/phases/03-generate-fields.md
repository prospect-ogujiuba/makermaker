# Phase 3: Generate Fields

## Purpose
Generate TypeRocket field code for each form field.

## Field Type Triggers

| DB Type | Load Module |
|---------|-------------|
| VARCHAR, TEXT | fields/text.md |
| BIGINT *_id | fields/relationship.md |
| ENUM | fields/select.md |
| INT, DECIMAL | fields/number.md |
| BOOLEAN | fields/special.md |
| DATE, DATETIME | fields/date.md |
| JSON | fields/special.md |
| *_image_id | fields/media.md |
| System fields | fields/system.md |

## Database Type to Field Mapping

```
VARCHAR(1-128)      → text()
VARCHAR(129+), TEXT → textarea()
LONGTEXT            → editor()
INT, BIGINT         → number() step=1
DECIMAL, FLOAT      → number() step=0.01
BOOLEAN, TINYINT(1) → toggle()
ENUM                → select() with getEnumValues()
DATE                → date()
DATETIME            → date() or datetime()
JSON                → repeater() or builder()
*_id (FK)           → select() with setModelOptions()
```

## Field Configuration Pattern

For each field:
```php
$form->{type}('{field_name}')
    ->setLabel('{Human Label}')
    ->setHelp('{Help text}')
    ->setAttribute('{attr}', '{value}')
    ->markLabelRequired()  // if required
```

## Special Field Handling

### Auto-generated (SKU, Slug)
```php
$form->text('sku')
    ->setLabel('SKU')
    ->setHelp('Auto-generated if left blank')
    ->setAttribute('placeholder', 'Auto-generated')
```

### Relationship Select
```php
$form->select('type_id')
    ->setLabel('Type')
    ->setModelOptions(Type::class, 'name', 'id', 'Select Type')
    ->markLabelRequired()
```

### ENUM Select
```php
$form->select('status')
    ->setLabel('Status')
    ->setOptions(array_merge(
        ['Select Status' => NULL],
        DatabaseHelper::getEnumValues('table', 'status')
    ))
```

## Output

Generated field code strings ready for assembly.

## Next Phase
Proceed to Phase 4: Output Form.
