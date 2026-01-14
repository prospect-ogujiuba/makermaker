# Helper: RestHelper

<when>All CRUD methods (create, update, destroy)</when>

## Location
`MakermakerCore\Helpers\RestHelper`

## Detection

```php
if (RestHelper::isRestRequest()) {
    // Return JSON response
}
```

## Response Methods

```php
// Success with data
RestHelper::successResponse($response, $data, 'Message', 201);

// Error with validation errors
RestHelper::errorResponse($response, $errors, 'Message', 400);

// Delete confirmation
RestHelper::deleteResponse($response, 'Message');
```

## Status Codes
- 200: Success (read, update, delete)
- 201: Created
- 400: Validation error
- 403: Unauthorized
- 409: Conflict (dependencies)
