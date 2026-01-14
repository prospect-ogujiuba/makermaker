# Decision: REST vs Web Response Pattern

<purpose>
Document the dual-response pattern where same action handles both web and REST requests.
</purpose>

## Pattern

Every mutating method checks request type and responds appropriately:

```php
if (RestHelper::isRestRequest()) {
    return RestHelper::successResponse($response, $data, 'Message', 200);
}

$response->flashNext('Message', 'success');
return tr_redirect()->toPage('resource', 'action');
```

## Web Response
- Flash message for user feedback
- Redirect to appropriate page
- Form errors repopulated via `->withErrors()`

## REST Response
- JSON envelope: `{success: bool, data: mixed, message: string}`
- HTTP status code: 200, 201, 400, 403, 409
- No redirects

## Rationale

Single controller serves:
- Admin UI (traditional form posts)
- Gutenberg blocks (AJAX/fetch requests)
- External API consumers

## Detection

`RestHelper::isRestRequest()` checks:
- Accept header contains `application/json`
- X-Requested-With header is `XMLHttpRequest`
- Request has `_rest` parameter
