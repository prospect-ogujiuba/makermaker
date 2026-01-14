<overview>
TypeRocket controller patterns for MakerMaker CRUD operations.
</overview>

<toc>
- #controller-structure
- #crud-methods
  - #index-method
  - #add-method
  - #create-method
  - #edit-method
  - #update-method
  - #show-method
  - #destroy-method
- #rest-methods
  - #index-rest
  - #show-rest
- #response-patterns
- #dependency-injection
- #authorization-flow
</toc>

<!-- SECTION LOADING TRIGGERS:
  - #controller-structure: Always load when generating controllers
  - #crud-methods: Load when controller_handoff.methods contains CRUD operations
  - #rest-methods: Load when controller_handoff.rest_endpoints is populated
  - #response-patterns: Load when generating REST responses
  - #dependency-injection: Load when understanding method signatures
  - #authorization-flow: Load when policy_handoff.capabilities is populated
-->

<section id="controller-structure">
## Standard Controller Structure

```php
<?php
namespace MakerMaker\Controllers;

use MakerMaker\Helpers\AuthorizationHelper;
use MakerMaker\Helpers\AuditTrailHelper;
use MakerMaker\Helpers\AutoCodeHelper;
use MakerMaker\Helpers\DeleteHelper;
use MakerMaker\Helpers\RedirectHelper;
use MakerMaker\Helpers\RestHelper;
use MakerMaker\Helpers\RestIndexHelper;
use MakerMaker\Http\Fields\{Entity}Fields;
use MakerMaker\Models\{Entity};
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class {Entity}Controller extends Controller
{
    // Index, Add, Create, Edit, Update, Show, Destroy
    // Plus REST variants: indexRest, showRest
}
```
</section>

<section id="crud-methods">
## Standard CRUD Methods

<subsection id="index-method">
### index() - List view
```php
public function index()
{
    return \MakerMaker\View::new('views', '{entity}.index', [
        'items' => {Entity}::all(),
    ]);
}
```
</subsection>

<subsection id="add-method">
### add() - Create form
```php
public function add()
{
    return \MakerMaker\View::new('views', '{entity}.form', [
        'form' => tr_form({Entity}::class)->useErrors()->useOld()->useConfirm(),
    ]);
}
```
</subsection>

<subsection id="create-method">
### create() - Store new record
```php
public function create({Entity}Fields $fields, {Entity} ${entity}, Response $response, AuthUser $user)
{
    // 1. Authorization
    AuthorizationHelper::authorize(${entity}, 'create', $response);

    // 2. Auto-generate codes (choose appropriate method)
    AutoCodeHelper::generateSlug($fields, 'name', '-');
    // OR: AutoCodeHelper::generateSkuAndSlug($fields, 'name', '-');

    // 3. Audit trail
    AuditTrailHelper::setCreateAuditFields(${entity}, $user);

    // 4. Save with validation
    ${entity}->save($fields);

    // 5. Handle errors
    if (${entity}->getErrors()) {
        if (RestHelper::isRestRequest()) {
            return RestHelper::errorResponse($response, ${entity}->getErrors(), 'Failed to create {entity}', 400);
        }
        $response->flashNext('Failed to create {entity}', 'error');
        return tr_redirect()->back()->withErrors(${entity}->getErrors());
    }

    // 6. Success response
    if (RestHelper::isRestRequest()) {
        return RestHelper::successResponse($response, ${entity}, '{Entity} created', 201);
    }
    $response->flashNext('{Entity} created successfully', 'success');
    return RedirectHelper::afterCreate('{entity}');
}
```
</subsection>

<subsection id="edit-method">
### edit() - Edit form
```php
public function edit({Entity} ${entity})
{
    return \MakerMaker\View::new('views', '{entity}.form', [
        'form' => tr_form(${entity})->useErrors()->useOld()->useConfirm(),
        '{entity}' => ${entity},
    ]);
}
```
</subsection>

<subsection id="update-method">
### update() - Save changes
```php
public function update({Entity}Fields $fields, {Entity} ${entity}, Response $response, AuthUser $user)
{
    // 1. Authorization
    AuthorizationHelper::authorize(${entity}, 'update', $response);

    // 2. Audit trail (update only)
    AuditTrailHelper::setUpdateAuditFields(${entity}, $user);

    // 3. Save with validation
    ${entity}->save($fields);

    // 4. Handle errors
    if (${entity}->getErrors()) {
        if (RestHelper::isRestRequest()) {
            return RestHelper::errorResponse($response, ${entity}->getErrors(), 'Failed to update {entity}', 400);
        }
        $response->flashNext('Failed to update {entity}', 'error');
        return tr_redirect()->back()->withErrors(${entity}->getErrors());
    }

    // 5. Success response
    if (RestHelper::isRestRequest()) {
        return RestHelper::successResponse($response, ${entity}, '{Entity} updated', 200);
    }
    $response->flashNext('{Entity} updated successfully', 'success');
    return RedirectHelper::afterUpdate('{entity}', ${entity}->id);
}
```
</subsection>

<subsection id="show-method">
### show() - Single view (admin)
```php
public function show({Entity} ${entity})
{
    return \MakerMaker\View::new('views', '{entity}.show', [
        '{entity}' => ${entity},
    ]);
}
```
</subsection>

<subsection id="destroy-method">
### destroy() - Delete record
```php
public function destroy({Entity} ${entity}, Response $response)
{
    // 1. Authorization
    AuthorizationHelper::authorize(${entity}, 'delete', $response);

    // 2. Check dependencies (if entity has children)
    $check = DeleteHelper::checkDependencies(${entity}, 'childRelation', $response);
    if ($check) return $check;

    // 3. Execute delete
    return DeleteHelper::executeDelete(${entity}, $response);
}
```
</subsection>

</section>

<section id="rest-methods">
## REST API Methods

<subsection id="index-rest">
### indexRest() - JSON list
```php
public function indexRest(Response $response)
{
    return RestIndexHelper::handleIndex($response, {Entity}::class, '{entities}', function($query) {
        // Optional query modifier
        return $query->where('is_active', 1)->orderBy('name', 'asc');
    });
}
```
</subsection>

<subsection id="show-rest">
### showRest() - JSON single
```php
public function showRest(Response $response, {Entity} ${entity})
{
    return RestIndexHelper::handleShow($response, ${entity}, {Entity}::class, '{entity}');
}
```
</subsection>

</section>

<section id="response-patterns">
## Response Patterns

### REST Success Response
```json
{
    "status": "success",
    "message": "{Entity} created",
    "data": { /* entity data */ }
}
```

### REST Error Response
```json
{
    "status": "error",
    "message": "Failed to create {entity}",
    "errors": {
        "name": ["The name field is required."],
        "sku": ["The sku has already been taken."]
    }
}
```

### REST Delete Response
```json
{
    "status": "success",
    "message": "{Entity} deleted"
}
```

### Admin Flash Messages
```php
$response->flashNext('Success message', 'success');
$response->flashNext('Error message', 'error');
$response->flashNext('Warning message', 'warning');
```
</section>

<section id="dependency-injection">
## Dependency Injection

TypeRocket automatically injects these types:

| Type | Injected As |
|------|-------------|
| `{Entity}Fields $fields` | Validated form data |
| `{Entity} ${entity}` | Model instance (from route ID) |
| `Response $response` | HTTP response builder |
| `AuthUser $user` | Current authenticated user |

**Method signature order:** Fields first, then Model, then Response, then AuthUser.
</section>

<section id="authorization-flow">
## Authorization Flow

```
Request
   ↓
AuthorizationHelper::authorize(${entity}, 'action', $response)
   ↓
Finds {Entity}Policy via auto-discovery
   ↓
Calls Policy->{action}($user, ${entity})
   ↓
Returns true → continues
Returns false → aborts with 403
```

**Standard actions:** create, read, update, delete

**Custom actions:** Add method to Policy, call with matching action name.
</section>
