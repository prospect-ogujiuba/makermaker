# Pattern: update() Method

<when>CRUD actions include 'update'</when>

## Template

```php
/**
 * Update existing {entity}
 */
public function update({ENTITY} ${entity}, {ENTITY}Fields $fields, Response $response, AuthUser $user)
{
    // 1. Authorization check (MANDATORY)
    AuthorizationHelper::authorize(${entity}, 'update', $response);

    // 2. Auto-generate SKU/slug (if applicable)
    {AUTO_CODE_LOGIC}

    // 3. Set audit fields (MANDATORY)
    AuditTrailHelper::setUpdateAuditFields(${entity}, $user);

    // 4. Save with validation
    ${entity}->save($fields);

    // 5. Check for errors
    if (${entity}->getErrors()) {
        if (RestHelper::isRestRequest()) {
            return RestHelper::errorResponse($response, ${entity}->getErrors(), '{ENTITY} update failed');
        }
        $response->flashNext('{ENTITY} update failed', 'error');
        return tr_redirect()->back()->withErrors(${entity}->getErrors());
    }

    // 6. Success response
    if (RestHelper::isRestRequest()) {
        return RestHelper::successResponse($response, ${entity}, '{ENTITY} updated successfully');
    }

    $response->flashNext('{ENTITY} updated successfully', 'success');
    return tr_redirect()->toPage('{entity}', 'edit', ${entity}->getID());
}
```

## Notes
- Model comes before Fields in signature (existing record)
- Redirects back to edit page on success (not index)
- Same auto-code logic as create
