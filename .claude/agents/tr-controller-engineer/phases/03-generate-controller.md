# Phase 3: Generate Controller

<purpose>
Generate the controller class using controller_spec and helper_decisions from previous phases.
</purpose>

## Triggers: Load Required Patterns

<triggers>
- condition: "always"
  loads: ["patterns/method-index.md", "patterns/method-edit.md"]
- condition: "crud_actions contains 'create'"
  loads: ["patterns/method-create.md"]
- condition: "crud_actions contains 'update'"
  loads: ["patterns/method-update.md"]
- condition: "crud_actions contains 'destroy'"
  loads: ["patterns/method-destroy.md"]
- condition: "rest_endpoints.enabled == true"
  loads: ["patterns/method-rest-index.md", "patterns/method-rest-show.md"]
- condition: "helper_decisions.auto_code.enabled == true"
  loads: ["helpers/auto-code-helper.md", "decisions/sku-vs-slug.md"]
- condition: "helper_decisions.delete_helper.check_dependencies == true"
  loads: ["helpers/delete-helper.md", "decisions/dependency-checking.md"]
</triggers>

## Class Structure

```php
<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\{ENTITY}Fields;
use MakerMaker\Models\{ENTITY};
use MakermakerCore\Helpers\{
    AuditTrailHelper,
    RestHelper,
    AuthorizationHelper,
    AutoCodeHelper,      // if auto_code.enabled
    DeleteHelper,
    RestIndexHelper
};
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class {ENTITY}Controller extends Controller
{
    // Methods in order:
    // 1. index()
    // 2. add()
    // 3. create()
    // 4. edit()
    // 5. update()
    // 6. show()
    // 7. destroy()
    // 8. indexRest()
    // 9. showRest()
}
```

## DI Conventions

TypeRocket auto-injects by type:
- `{Entity}Fields $fields` - Validated field data
- `{Entity} ${entity}` - Model instance from route
- `Response $response` - TypeRocket response
- `AuthUser $user` - Current authenticated user

Parameter order:
1. Fields class (if accepting input)
2. Model instance (if editing/viewing)
3. Response object
4. AuthUser (if needed)

## Method Generation Order

1. **View methods first**: index, add, edit, show
2. **Mutating methods**: create, update, destroy
3. **REST endpoints**: indexRest, showRest

For each method:
1. Load corresponding pattern from patterns/method-*.md
2. Apply helper integrations from helper_decisions
3. Substitute placeholders: {ENTITY}, {entity}, {entity_plural}

## Import Organization

Group imports alphabetically within categories:
1. Fields (MakerMaker\Http\Fields)
2. Models (MakerMaker\Models)
3. Helpers (MakermakerCore\Helpers)
4. View (MakerMaker\View)
5. TypeRocket (TypeRocket\Controllers, TypeRocket\Http, TypeRocket\Models)

## Output

Complete controller file at: `app/Controllers/{Entity}Controller.php`
