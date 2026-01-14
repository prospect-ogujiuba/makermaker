# Phase 4: Output Index

## Purpose
Generate and write complete index.php file.

## File Structure

```php
<?php

/**
 * {Model} Index View
 */

use {Namespace}\Models\{Model};

$table = tr_table({Model}::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    // Bulk actions if any
]);

$table->setColumns([
    // Column definitions
], '{primary_column}')->setOrder('{sort_column}', '{sort_dir}')->render();

$table;
```

## Assembly Steps

1. **PHP tag and docblock**
   ```php
   <?php

   /**
    * Equipment Index View
    */
   ```

2. **Use statement**
   ```php
   use MakerMaker\Models\Equipment;
   ```

3. **Table instantiation**
   ```php
   $table = tr_table(Equipment::class);
   ```

4. **Bulk actions**
   ```php
   $table->setBulkActions(tr_form()->useConfirm(), []);
   ```

5. **Column configuration**
   Assemble all column definitions from Phase 2.

6. **Render chain**
   ```php
   ], 'name')->setOrder('id', 'DESC')->render();
   ```

7. **Output**
   ```php
   $table;
   ```

## Output Path

Write to: `resources/views/{entity}/index.php`
- Use lowercase, underscored entity name
- Example: `equipment/index.php`, `service_types/index.php`

## Formatting Rules

- 4-space indentation
- Single quotes for strings (except HTML)
- Double quotes for HTML attributes
- No trailing comma on last array element
- Align array keys when practical

## Completion Checklist

- [ ] Use statement for model
- [ ] All selected columns configured
- [ ] Row actions on primary column
- [ ] Bulk actions (empty or configured)
- [ ] Default ordering set
- [ ] File written to correct path
