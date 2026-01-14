# Pattern: showRest() Method

<when>REST endpoints enabled</when>

## Template (basic)

```php
/**
 * REST endpoint: show single {entity}
 */
public function showRest({ENTITY} ${entity}, Response $response)
{
    return RestIndexHelper::handleShow($response, ${entity}, {ENTITY}::class, '{entity}');
}
```

## Template (with eager loading)

```php
public function showRest({ENTITY} ${entity}, Response $response)
{
    return RestIndexHelper::handleShow($response, ${entity}, {ENTITY}::class, '{entity}', [{WITH_RELATIONSHIPS}]);
}
```

## Notes
- Model auto-injected from route parameter
- Last parameter is array of relationships to eager load
- Use same relationships as indexRest for consistency
