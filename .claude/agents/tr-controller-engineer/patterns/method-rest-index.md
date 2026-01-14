# Pattern: indexRest() Method

<when>REST endpoints enabled</when>

## Template (basic)

```php
/**
 * REST endpoint: list {entity_plural}
 */
public function indexRest(Response $response)
{
    return RestIndexHelper::handleIndex($response, {ENTITY}::class, '{entity_plural}');
}
```

## Template (with eager loading)

```php
public function indexRest(Response $response)
{
    return RestIndexHelper::handleIndex($response, {ENTITY}::class, '{entity_plural}', function($query) {
        return $query->with([{WITH_RELATIONSHIPS}]);
    });
}
```

## Template (with query filter)

```php
public function indexRest(Response $response)
{
    return RestIndexHelper::handleIndex($response, {ENTITY}::class, '{entity_plural}', function($query) {
        return $query->where('is_active', 1);
    });
}
```

## Notes
- Use eager loading from model_handoff.schema.with
- RestIndexHelper handles pagination, errors, response format
