# Helper: AuthorizationHelper

<when>EVERY mutating method (create, update, destroy)</when>

## Location
`MakermakerCore\Helpers\AuthorizationHelper`

## Usage

```php
AuthorizationHelper::authorize($model, 'create', $response);
AuthorizationHelper::authorize($model, 'update', $response);
AuthorizationHelper::authorize($model, 'destroy', $response);
```

## Behavior
- Calls `$model->can($action)` via policy
- Aborts with 403 if unauthorized
- Must be FIRST line after method signature

## Actions
- `create` - Creating new record
- `update` - Modifying existing record
- `destroy` - Deleting record
