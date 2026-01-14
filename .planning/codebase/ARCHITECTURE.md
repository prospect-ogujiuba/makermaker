# Architecture

**Analysis Date:** 2026-01-07

## Pattern Overview

**Overall:** Thin Client MVC Plugin (TypeRocket Pro v6 dependent)

**Key Characteristics:**
- Domain-specific implementation on framework foundation
- Dependency injection from mu-plugin (ORM, routing, auth)
- Policy-based authorization with auto-discovery
- Dual-mode endpoints (REST JSON + HTML forms)
- Zero-config reflective REST API

## Layers

**Presentation Layer:**
- Purpose: Render admin UI templates
- Contains: Blade-style templates (`resources/views/`)
- Depends on: TypeRocket form helpers
- Used by: Controller view methods

**Controller Layer:**
- Purpose: Handle HTTP requests, orchestrate responses
- Contains: CRUD handlers, REST endpoints (`app/Controllers/`)
- Depends on: Models, Fields, Policies, Helpers
- Used by: TypeRocket routes

**Validation Layer:**
- Purpose: Validate incoming form data
- Contains: Field rule definitions (`app/Http/Fields/`)
- Depends on: TypeRocket validation engine
- Used by: Controllers via dependency injection

**Model Layer:**
- Purpose: Data access and business entities
- Contains: Eloquent-style models (`app/Models/`)
- Depends on: TypeRocket ORM
- Used by: Controllers, Helpers

**Authorization Layer:**
- Purpose: Permission checking
- Contains: Policy classes (`app/Auth/`)
- Depends on: TypeRocket AuthUser
- Used by: Controllers via AuthorizationHelper

**Helper Layer:**
- Purpose: Cross-cutting utilities
- Contains: Static utility classes (`app/Helpers/`)
- Depends on: Models
- Used by: Controllers, Views

**Resource Layer:**
- Purpose: WordPress admin menu registration
- Contains: Resource definitions (`inc/resources/`)
- Depends on: makermaker-core helpers
- Used by: Plugin initialization

**Route Layer:**
- Purpose: HTTP endpoint definitions
- Contains: Route collections (`inc/routes/`)
- Depends on: TypeRocket routing
- Used by: Plugin initialization

## Data Flow

**HTTP Request Lifecycle:**

1. Request arrives at WordPress
2. TypeRocket route matching (`inc/routes/api.php` or `public.php`)
3. Controller action invoked (`app/Controllers/{Entity}Controller.php`)
4. Field validation auto-executed (`app/Http/Fields/{Entity}Fields.php`)
5. Authorization check (`AuthorizationHelper` → `app/Auth/{Entity}Policy.php`)
6. Model operation (`app/Models/{Entity}.php`)
7. Database query (tables prefixed `srvc_`)
8. Audit trail set (`AuditTrailHelper`: created_by, updated_by, timestamps)
9. Response formatting (`RestHelper` for JSON, Blade for HTML)
10. HTTP Response

**State Management:**
- Database-backed: All state in MySQL via TypeRocket ORM
- Audit trail: `created_by`, `updated_by`, `created_at`, `updated_at`
- Soft deletes: `deleted_at` field on all models
- Optimistic locking: `version` field auto-incremented

## Key Abstractions

**Model:**
- Purpose: Domain entity representation
- Examples: `app/Models/Service.php`, `app/Models/Equipment.php`, `app/Models/ServicePrice.php`
- Pattern: Eloquent-style ORM with eager loading, soft deletes, casting

**Controller:**
- Purpose: HTTP request handling
- Examples: `app/Controllers/ServiceController.php`, `app/Controllers/ContactSubmissionController.php`
- Pattern: CRUD methods with dual REST/HTML response

**Policy:**
- Purpose: Authorization rules
- Examples: `app/Auth/ServicePolicy.php`, `app/Auth/EquipmentPolicy.php`
- Pattern: Capability-based (`$auth->isCapable('manage_services')`)

**Fields:**
- Purpose: Input validation rules
- Examples: `app/Http/Fields/ServiceFields.php`, `app/Http/Fields/ServicePriceFields.php`
- Pattern: TypeRocket ValidatorFields with `rules()` method

**Helper:**
- Purpose: Utility functions
- Examples: `app/Helpers/ServiceCatalogHelper.php`, `app/Helpers/TeamHelper.php`
- Pattern: Static methods for cross-cutting concerns

## Entry Points

**Plugin Activation:**
- Location: `makermaker.php`
- Triggers: Plugin activation in WordPress
- Responsibilities: Define constants, setup autoloading, hook TypeRocket

**Main Plugin Class:**
- Location: `app/MakermakerTypeRocketPlugin.php`
- Triggers: `typerocket_loaded` action (priority 9)
- Responsibilities: Load resources, routes, policies; init REST API; register assets

**Asset Entry Points:**
- Frontend: `resources/js/front.js` → `public/front/front.js`
- Admin: `resources/js/admin.js` → `public/admin/admin.js`

## Error Handling

**Strategy:** Throw exceptions, catch at controller level, format response

**Patterns:**
- Models return errors via `$model->getErrors()`
- Controllers check errors after save operations
- REST: Return JSON error via `RestHelper::errorResponse()`
- HTML: Flash message + redirect back with errors

## Cross-Cutting Concerns

**Logging:**
- File logging via TypeRocket FileLogger (`config/logging.php`)
- Optional Slack integration via webhook
- Debug logging via `error_log()` (should be refactored)

**Validation:**
- TypeRocket Fields at controller level
- Auto-validated via dependency injection
- Custom callbacks for enum validation

**Authentication:**
- WordPress native authentication
- TypeRocket AuthUser injected into controllers
- Capability-based authorization via policies

**Audit Trail:**
- `AuditTrailHelper::setCreateAuditFields()` on create
- `AuditTrailHelper::setUpdateAuditFields()` on update
- Tracks `created_by`, `updated_by`, timestamps

---

*Architecture analysis: 2026-01-07*
*Update when major patterns change*
