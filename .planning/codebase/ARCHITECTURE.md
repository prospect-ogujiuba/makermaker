# Architecture

**Analysis Date:** 2026-01-19

## Pattern Overview

**Overall:** Thin-Client MVC Plugin on TypeRocket Pro v6 Framework

**Key Characteristics:**
- Domain-specific MVC in plugin (`app/`), core scaffolding in vendor (`mxcro/makermaker-core`)
- Convention-over-configuration CRUD generation via Galaxy CLI
- Reflective REST API wrapper auto-enhances all custom resources
- Policy-based authorization with auto-discovery
- TypeRocket Pro handles routing, forms, ORM, admin UI

## Layers

**Entry Point Layer:**
- Purpose: WordPress plugin bootstrap and TypeRocket integration
- Location: `makermaker.php`
- Contains: Plugin header, constants, autoload, hook registration
- Depends on: TypeRocket Pro (`typerocket_loaded` action)
- Used by: WordPress plugin system

**Plugin Core Layer:**
- Purpose: Plugin initialization, resource loading, REST API setup
- Location: `app/MakermakerTypeRocketPlugin.php`
- Contains: Settings page, asset registration, policy discovery, REST wrapper init
- Depends on: TypeRocket\Pro\Register\BasePlugin, MakermakerCore
- Used by: Entry point via `typerocket_plugin_makermaker()`

**Controller Layer:**
- Purpose: Handle admin UI requests and REST API endpoints
- Location: `app/Controllers/`
- Contains: CRUD actions (index, add, create, edit, update, destroy), REST overrides
- Depends on: Models, Fields, View, MakermakerCore helpers
- Used by: TypeRocket routing, ReflectiveRestWrapper

**Model Layer:**
- Purpose: Data access, relationships, business logic
- Location: `app/Models/`
- Contains: Eloquent-style models with fillable/guard, relationships, bulk actions
- Depends on: TypeRocket\Models\Model, MakermakerCore traits
- Used by: Controllers, ReflectiveRestWrapper, Policies

**Authorization Layer:**
- Purpose: Capability-based access control
- Location: `app/Auth/`
- Contains: Policy classes with create/read/update/destroy methods
- Depends on: TypeRocket\Auth\Policy, AuthUser
- Used by: Controllers (via AuthorizationHelper), ReflectiveRestWrapper

**Fields/Validation Layer:**
- Purpose: Request validation and field filtering
- Location: `app/Http/Fields/`
- Contains: Validation rules, custom messages, fillable overrides
- Depends on: TypeRocket\Http\Fields
- Used by: Controllers on create/update

**View Layer:**
- Purpose: Admin UI templates (index tables, forms)
- Location: `resources/views/`
- Contains: PHP templates using TypeRocket form builder and tabs
- Depends on: TypeRocket Elements, MakermakerCore helpers (mm_table)
- Used by: Controllers via `View::new()`

**Resource Registration Layer:**
- Purpose: Register WordPress admin menus and capabilities
- Location: `inc/resources/`
- Contains: Custom resource definitions, capability grants
- Depends on: MakermakerCore helpers (mm_create_custom_resource)
- Used by: MakermakerTypeRocketPlugin::loadResources()

**Core Library Layer (Vendor):**
- Purpose: Shared scaffolding, REST wrapper, admin helpers
- Location: `vendor/mxcro/makermaker-core/src/`
- Contains: ReflectiveRestWrapper, ReflectiveTable, helpers, Galaxy commands
- Depends on: TypeRocket Pro
- Used by: All plugin layers

## Data Flow

**Admin CRUD Flow:**

1. User navigates to admin page -> TypeRocket routes to Controller action
2. Controller calls View::new() with form/model data
3. View renders TypeRocket form elements
4. Form submit -> Controller create/update action
5. Fields class validates request data
6. AuthorizationHelper checks Policy
7. AuditTrailHelper sets created_by/updated_by
8. Model::save() persists to database
9. Redirect with flash message

**REST API Flow:**

1. Request hits `/tr-api/rest/{resource}/{id?}/actions/{action?}`
2. ReflectiveRestWrapper::handleRequest() intercepts (parse_request hook, priority 5)
3. Resolves resource config from TypeRocket Registry
4. Gets model from controller class name convention
5. Model::can() checks Policy authorization
6. For list: ReflectiveQueryBuilder applies search/filter/sort/pagination
7. For actions: ActionDispatcher invokes model methods with #[Action] attribute
8. JSON response with data/meta/message

**State Management:**
- Database-backed via TypeRocket ORM (WordPress $wpdb)
- Form state via TypeRocket form builder (useErrors, useOld, useConfirm)
- Flash messages via TypeRocket redirect system

## Key Abstractions

**BasePlugin:**
- Purpose: Plugin lifecycle (activate, deactivate, uninstall, migrations)
- Examples: `app/MakermakerTypeRocketPlugin.php`
- Pattern: Template method - subclasses override init(), routes(), policies()

**Model:**
- Purpose: Active Record pattern for database tables
- Examples: Generated via `php galaxy make:crud {Name}`
- Pattern: TypeRocket Model with fillable/guard, relationships, scopes

**Policy:**
- Purpose: Authorize CRUD operations per model
- Examples: `app/Auth/{Model}Policy.php`
- Pattern: Method per capability (create, read, update, destroy)

**Fields:**
- Purpose: Request validation and filtering
- Examples: `app/Http/Fields/{Model}Fields.php`
- Pattern: Rules-based validation with unique/required/max constraints

**Controller:**
- Purpose: Handle requests and coordinate responses
- Examples: `app/Controllers/{Model}Controller.php`
- Pattern: RESTful resource controller with admin UI methods

**ReflectiveRestWrapper:**
- Purpose: Zero-config REST API enhancement for all resources
- Examples: `vendor/mxcro/makermaker-core/src/Rest/ReflectiveRestWrapper.php`
- Pattern: Request interceptor with convention-based model resolution

**ReflectiveTable:**
- Purpose: Auto-generate admin tables from model fillable
- Examples: Used via `mm_table(Model::class)` in index views
- Pattern: Introspection-based UI generation

## Entry Points

**WordPress Plugin Load:**
- Location: `makermaker.php`
- Triggers: WordPress plugin activation, admin load
- Responsibilities: Define constants, hook `typerocket_loaded`

**TypeRocket Loaded Hook:**
- Location: `typerocket_plugin_makermaker()` function
- Triggers: `typerocket_loaded` action (priority 9)
- Responsibilities: Autoload, instantiate MakermakerTypeRocketPlugin

**Plugin Init:**
- Location: `MakermakerTypeRocketPlugin::init()`
- Triggers: Called by BasePlugin after WordPress ready
- Responsibilities: Load resources, setup settings, register assets, init REST wrapper

**Routes Registration:**
- Location: `MakermakerTypeRocketPlugin::routes()`
- Triggers: TypeRocket route loading phase
- Responsibilities: Include `inc/routes/api.php` and `inc/routes/public.php`

**REST API Interception:**
- Location: `ReflectiveRestWrapper::handleRequest()`
- Triggers: WordPress `parse_request` action (priority 5)
- Responsibilities: Handle `/tr-api/rest/` requests before TypeRocket routing

**Galaxy CLI:**
- Location: `galaxy/galaxy_makermaker` (shell script)
- Triggers: CLI execution `php galaxy make:crud`
- Responsibilities: CRUD scaffolding via MakermakerCore\Commands\Crud

## Error Handling

**Strategy:** TypeRocket error system with Whoops in debug mode

**Patterns:**
- Controller: tryDatabaseOperation() helper wraps save operations
- Validation: Fields class redirects back with errors on failure
- REST API: ReflectiveRestWrapper catches exceptions, returns JSON error response
- Debug mode: Whoops PHP error pages (config/app.php `errors.whoops`)
- Production: TypeRocket ErrorService handles exceptions

## Cross-Cutting Concerns

**Logging:** TypeRocket logging service, configurable in `config/logging.php`

**Validation:** Fields classes with rules() method, processed on import

**Authentication:** WordPress capabilities via AuthUser::isCapable()

**Audit Trail:** AuditTrailHelper sets created_by/updated_by automatically

**Soft Deletes:** Models support deleted_at via guard columns

---

*Architecture analysis: 2026-01-19*
