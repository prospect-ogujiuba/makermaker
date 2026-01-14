# Pattern: destroy() Method

<when>CRUD actions include 'destroy'</when>

## Template (with dependency check)

```php
/**
 * Delete {entity}
 */
public function destroy({ENTITY} ${entity}, Response $response)
{
    // 1. Authorization check (MANDATORY)
    AuthorizationHelper::authorize(${entity}, 'destroy', $response);

    // 2. Check dependencies (if has relationships)
    {DEPENDENCY_CHECK_LOGIC}

    // 3. Execute delete
    return DeleteHelper::executeDelete(${entity}, $response);
}
```

## Dependency Check Variants

**Single relationship:**
```php
if ($error = DeleteHelper::checkDependencies(${entity}, 'relationshipName', $response)) {
    return $error;
}
```

**Multiple relationships:**
```php
$relationships = ['services', 'prices', 'coverage'];
foreach ($relationships as $relationship) {
    if ($error = DeleteHelper::checkDependencies(${entity}, $relationship, $response)) {
        return $error;
    }
}
```

**No dependencies:**
Remove dependency check section entirely.
