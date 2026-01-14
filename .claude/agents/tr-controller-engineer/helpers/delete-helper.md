# Helper: DeleteHelper

<when>destroy() method - always</when>

## Location
`MakermakerCore\Helpers\DeleteHelper`

## Dependency Check

```php
// Returns null if no dependencies, Response with 409 if blocked
if ($error = DeleteHelper::checkDependencies($model, 'relationshipName', $response)) {
    return $error;
}
```

## Execute Delete

```php
// Handles soft delete and response formatting
return DeleteHelper::executeDelete($model, $response);
```

## Multiple Relationships

```php
$relationships = ['services', 'prices', 'coverage'];
foreach ($relationships as $relationship) {
    if ($error = DeleteHelper::checkDependencies($model, $relationship, $response)) {
        return $error;
    }
}
```

## Decision Logic
See decisions/dependency-checking.md for when to check.
