# Pattern: create() Method

<when>CRUD actions include 'create'</when>

## Template

```php
/**
 * Create new {entity}
 */
public function create({ENTITY}Fields $fields, {ENTITY} ${entity}, Response $response, AuthUser $user)
{
    // 1. Authorization check (MANDATORY)
    AuthorizationHelper::authorize(${entity}, 'create', $response);

    // 2. Auto-generate SKU/slug (if applicable)
    {AUTO_CODE_LOGIC}

    // 3. Set audit fields (MANDATORY)
    AuditTrailHelper::setCreateAuditFields(${entity}, $user);

    // 4. Save with validation
    ${entity}->save($fields);

    // 5. Check for errors
    if (${entity}->getErrors()) {
        if (RestHelper::isRestRequest()) {
            return RestHelper::errorResponse($response, ${entity}->getErrors(), '{ENTITY} creation failed');
        }
        $response->flashNext('{ENTITY} creation failed', 'error');
        return tr_redirect()->back()->withErrors(${entity}->getErrors());
    }

    // 6. Success response
    if (RestHelper::isRestRequest()) {
        return RestHelper::successResponse($response, ${entity}, '{ENTITY} created successfully', 201);
    }

    $response->flashNext('{ENTITY} created successfully', 'success');
    return tr_redirect()->toPage('{entity}', 'index');
}
```

## Placeholders

- `{AUTO_CODE_LOGIC}`: Replace based on helper_decisions.auto_code.method
  - generateSkuAndSlug: `AutoCodeHelper::generateSkuAndSlug($fields);`
  - generateSlug: `AutoCodeHelper::generateSlug($fields);`
  - generateCode: `AutoCodeHelper::generateCode($fields);`
  - null: Remove line entirely
