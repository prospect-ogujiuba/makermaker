<overview>
TypeRocket MVC patterns for the Maker Framework. MakerMaker handles ALL business logic.
</overview>

<toc>
- #layer-separation
- #file-locations
- #handoff-chain
- #helper-usage
  - #authorization-helper
  - #audit-trail-helper
  - #rest-helper
  - #auto-code-helper
  - #delete-helper
  - #redirect-helper
- #naming-conventions
- #rest-api-pattern
</toc>

<!-- SECTION LOADING TRIGGERS:
  - #layer-separation: Always load (foundational)
  - #file-locations: Load when creating new files
  - #handoff-chain: Load for workflow orchestration
  - #helper-usage: Load when controller_handoff.helpers_used is populated
  - #naming-conventions: Load when generating entity names/paths
  - #rest-api-pattern: Load when controller_handoff.rest_endpoints is populated
-->

<section id="layer-separation">
## Strict Layer Separation

| Layer | Plugin | Responsibility |
|-------|--------|----------------|
| Business Logic | makermaker | Models, Controllers, Policies, Migrations |
| UI Rendering | makerblocks | Gutenberg blocks, React components |
| Templates | makerstarter | FSE templates, theme.json |

**NEVER put business logic in makerblocks. NEVER put UI in makermaker.**
</section>

<section id="file-locations">
## Standard File Paths

```
makermaker/
├── app/
│   ├── Auth/               # {Entity}Policy.php
│   ├── Controllers/        # {Entity}Controller.php
│   ├── Helpers/            # Cross-cutting utilities
│   ├── Http/Fields/        # {Entity}Fields.php
│   └── Models/             # {Entity}.php
├── database/
│   └── migrations/         # {timestamp}.{description}.sql
├── inc/
│   ├── resources/          # {entity}.php (registration)
│   └── routes/             # api.php, public.php
└── resources/
    └── views/
        └── {entity}/       # form.php, index.php
```
</section>

<section id="handoff-chain">
## Agent Handoff Chain

Agents execute in strict order. Each produces a handoff consumed by the next.

```
1. tr-migration-architect
   INPUT: entity name, fields, relationships
   OUTPUT: table_name, columns[], indexes[], foreign_keys[]

2. tr-model-builder
   INPUT: entity name, migration handoff
   OUTPUT: model_class, fillable[], guarded[], casts{}, relationships[]

3. tr-policy-author (parallel with 4)
   INPUT: entity name, model handoff
   OUTPUT: policy_class, capabilities[]

4. tr-fields-validator (parallel with 3)
   INPUT: entity name, model handoff
   OUTPUT: validation_rules{}, sanitization{}

5. tr-controller-engineer
   INPUT: entity name, model/policy/fields handoffs
   OUTPUT: controller_class, routes[], rest_endpoints[]

6. tr-form-designer (parallel with 7)
   INPUT: entity name, model/controller handoffs
   OUTPUT: form_structure{}

7. tr-index-builder (parallel with 6)
   INPUT: entity name, model/controller handoffs
   OUTPUT: table_columns[], filters[]
```
</section>

<section id="helper-usage">
## Helper Class Patterns

<!-- Load subsections based on controller_handoff.helpers_used[] -->

<subsection id="authorization-helper">
### AuthorizationHelper
```php
use MakerMaker\Helpers\AuthorizationHelper;

// In controller method - aborts with 403 if unauthorized
AuthorizationHelper::authorize($model, 'create', $response);
AuthorizationHelper::authorize($model, 'update', $response);
AuthorizationHelper::authorize($model, 'delete', $response);
```
</subsection>

<subsection id="audit-trail-helper">
### AuditTrailHelper
```php
use MakerMaker\Helpers\AuditTrailHelper;

// On create - sets created_by AND updated_by
AuditTrailHelper::setCreateAuditFields($model, $user);

// On update - sets updated_by only
AuditTrailHelper::setUpdateAuditFields($model, $user);
```
</subsection>

<subsection id="rest-helper">
### RestHelper
```php
use MakerMaker\Helpers\RestHelper;

// Check if request expects JSON
if (RestHelper::isRestRequest()) {
    return RestHelper::successResponse($response, $data, 'Message', 200);
    return RestHelper::errorResponse($response, $errors, 'Failed', 400);
    return RestHelper::deleteResponse($response, 'Deleted');
}
```
</subsection>

<subsection id="auto-code-helper">
### AutoCodeHelper
```php
use MakerMaker\Helpers\AutoCodeHelper;

// For entities with SKU (Service, Equipment)
AutoCodeHelper::generateSkuAndSlug($fields, 'name', '-');

// For entities without SKU
AutoCodeHelper::generateSlug($fields, 'name', '-');

// For config entities (types, tiers)
AutoCodeHelper::generateCode($fields, 'name', '-');
```
</subsection>

<subsection id="delete-helper">
### DeleteHelper
```php
use MakerMaker\Helpers\DeleteHelper;

// Check for dependent records before delete
$check = DeleteHelper::checkDependencies($model, 'prices', $response);
if ($check) return $check;

// Execute delete with proper response
return DeleteHelper::executeDelete($model, $response);
```
</subsection>

<subsection id="redirect-helper">
### RedirectHelper
```php
use MakerMaker\Helpers\RedirectHelper;

// After successful create
return RedirectHelper::afterCreate('service');

// After successful update
return RedirectHelper::afterUpdate('service', $model->id);
```
</subsection>

</section>

<section id="naming-conventions">
## Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Entity | PascalCase singular | `Service`, `Equipment` |
| Table | snake_case plural with prefix | `srvc_services`, `srvc_equipment` |
| Model | PascalCase singular | `Service.php` |
| Controller | PascalCase + Controller | `ServiceController.php` |
| Policy | PascalCase + Policy | `ServicePolicy.php` |
| Fields | PascalCase + Fields | `ServiceFields.php` |
| View folder | lowercase singular | `service/` |
| Resource file | lowercase singular | `service.php` |
| REST endpoint | lowercase plural | `/tr-api/rest/services/` |
</section>

<section id="rest-api-pattern">
## REST API Convention

All resources automatically get REST endpoints via ReflectiveRestWrapper:

```
GET    /tr-api/rest/{entities}/           # List all
GET    /tr-api/rest/{entities}/{id}       # Get one
POST   /tr-api/rest/{entities}/           # Create
PUT    /tr-api/rest/{entities}/{id}       # Update
DELETE /tr-api/rest/{entities}/{id}       # Delete
```

**Query parameters:**
- `?search=term` - Full-text search
- `?field=value` - Filter by field
- `?orderby=field&order=asc|desc` - Sorting
- `?per_page=20&page=1` - Pagination
</section>
