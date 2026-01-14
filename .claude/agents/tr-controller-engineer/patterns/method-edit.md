# Pattern: edit() Method

<when>Always included - admin edit form</when>

## Template

```php
/**
 * Display edit form for {entity}
 */
public function edit({ENTITY} ${entity}, AuthUser $user)
{
    $current_id = ${entity}->getID();
    $createdBy = ${entity}->createdBy;
    $updatedBy = ${entity}->updatedBy;

    $form = tr_form(${entity})->useErrors()->useOld()->useConfirm();
    return View::new('{entity_plural}.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
}
```

## Also: add() Method

```php
/**
 * Display add form for new {entity}
 */
public function add(AuthUser $user)
{
    $form = tr_form({ENTITY}::class)->useErrors()->useOld()->useConfirm();
    return View::new('{entity_plural}.form', compact('form', 'user'));
}
```

## Also: show() Method

```php
/**
 * Show single {entity}
 */
public function show({ENTITY} ${entity})
{
    return ${entity};
}
```
