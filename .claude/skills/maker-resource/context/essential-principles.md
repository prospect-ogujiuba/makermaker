# Essential Principles for TypeRocket Resources

This context is loaded by Stage 1 (Requirements Gathering) to inform proper resource creation.

## Layer Separation

MakerMaker handles ALL business logic. MakerBlocks handles ONLY UI. Never mix.

- **MakerMaker** (`wp-content/plugins/makermaker`): TypeRocket MVC, data models, controllers, policies
- **MakerBlocks** (`wp-content/plugins/makerblocks`): Gutenberg blocks, FSE templates, UI components

A block may query MakerMaker data via REST API but never contains business logic.

## File Locations

Standard locations within MakerMaker plugin:

| Component | Path | Purpose |
|-----------|------|---------|
| Migrations | `database/migrations/` | DATA TRUTH - SQL schema definitions |
| Models | `app/Models/` | Eloquent-style models with relationships |
| Controllers | `app/Controllers/` | CRUD + REST endpoints |
| Policies | `app/Auth/` | Authorization capabilities |
| Fields | `app/Http/Fields/` | Validation rules and field definitions |
| Forms | `resources/views/{entity}/form.php` | Admin form views |
| Index | `resources/views/{entity}/index.php` | Admin list views |
| Registration | `inc/resources/{entity}.php` | Resource registration |

## Handoff Chain

Strict order - each agent produces a handoff that feeds the next:

```
migration → model → policy+fields (parallel) → controller → form+index (parallel)
```

Never skip steps. Each handoff contains:
- Schema decisions
- Rationale for choices
- Constraints for downstream agents

## Table Naming Conventions

- **Prefix**: `srvc_` (required for all custom tables)
- **Pattern**: `srvc_{plural_entity}`
- **Examples**:
  - Entity "Service" → table `srvc_services`
  - Entity "Equipment" → table `srvc_equipment`
  - Entity "ServiceType" → table `srvc_service_types`

Junction tables for belongsToMany: `srvc_{entity1}_{entity2}` (alphabetical order)

## Standard Audit Fields

ALL tables must include these audit columns:

```sql
created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
created_by BIGINT UNSIGNED,
updated_by BIGINT UNSIGNED,
deleted_at DATETIME NULL,
version INT UNSIGNED DEFAULT 1
```

- `created_at`/`updated_at`: Automatic timestamps
- `created_by`/`updated_by`: User ID who made changes (FK to wp_users)
- `deleted_at`: Soft delete marker (NULL = active)
- `version`: Optimistic locking counter

## Helper Classes

Use these helpers in controllers for consistent behavior:

### AuthorizationHelper
```php
AuthorizationHelper::authorize($model, 'action', $response)
```
Checks user capability for action on model instance.

### AuditTrailHelper
```php
AuditTrailHelper::setCreateAuditFields($model, $user)
AuditTrailHelper::setUpdateAuditFields($model, $user)
```
Sets created_by/updated_by automatically.

### RestHelper
```php
RestHelper::isRestRequest()
RestHelper::successResponse($data, $message, $code)
RestHelper::errorResponse($message, $code, $errors)
```
Handles REST API request detection and response formatting.

### AutoCodeHelper
```php
AutoCodeHelper::generateSlug($model, 'name', 'slug')
AutoCodeHelper::generateSkuAndSlug($model, 'name', 'sku', 'slug', 'PREFIX')
```
Auto-generates slugs and SKUs from name field.

### DeleteHelper
```php
DeleteHelper::checkDependencies($model, ['relationship1', 'relationship2'])
DeleteHelper::executeDelete($model, $response, $softDelete = true)
```
Checks for dependent records before deletion; handles soft delete.

### RedirectHelper
```php
RedirectHelper::afterCreate($model, 'success message')
RedirectHelper::afterUpdate($model, 'success message')
```
Handles post-action redirects with flash messages.
