# Phase 4: Output Form

## Purpose
Assemble and write complete form view file.

## File Structure

```php
<?php
use MakerMaker\Models\{RelatedModels};
use MakerMaker\Helpers\DatabaseHelper;

$form->open();

echo to_resource('{entity}', 'index', 'Back To {Entities}');

$tabs = tr_tabs()
    ->setFooter($form->save('Save {Entity}'))
    ->layoutLeft();

$tabs->tab('Overview', 'admin-settings', [
    // Fieldsets with fields
])->setDescription('Primary information');

// Additional tabs...

if (isset($current_id)) {
    $tabs->tab('System', 'info', [
        // System fieldset
    ])->setDescription('System information');
}

$tabs->render();

$form->close();
```

## Assembly Steps

1. **Use statements**
   - Import related models for setModelOptions()
   - Import DatabaseHelper if ENUM fields exist

2. **Form open**
   - `$form->open();`

3. **Back button**
   - `echo to_resource('{entity}', 'index', 'Back To {Entities}');`

4. **Tab initialization**
   - `$tabs = tr_tabs()->setFooter($form->save('Save {Entity}'))->layoutLeft();`

5. **Content tabs**
   - Assemble fieldsets with generated fields
   - Use `$form->row()->withColumn()` for column layout

6. **System tab (conditional)**
   - Wrap in `if (isset($current_id)) {}`
   - Include readonly audit fields

7. **Render and close**
   - `$tabs->render();`
   - `$form->close();`

## Output Path

Write to: `resources/views/{entity}/form.php`
- Use lowercase, underscored entity name
- Example: `service_types/form.php`, `equipment/form.php`

## Completion Checklist

- [ ] Use statements for all related models
- [ ] All fillable fields included
- [ ] Required fields marked
- [ ] Relationships have select dropdowns
- [ ] System tab conditional on $current_id
- [ ] Proper tab/fieldset structure
- [ ] File written to correct path
