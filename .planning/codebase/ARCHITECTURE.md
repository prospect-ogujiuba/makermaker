# Architecture

**Analysis Date:** 2026-01-18

## Pattern Overview

**Overall:** TypeRocket Pro v6 WordPress Plugin with MVC + Reflective REST API

**Key Characteristics:**
- Single entry point via `typerocket_loaded` action (priority 9)
- Plugin-based initialization extending TypeRocket's BasePlugin
- Zero-config REST API via ReflectiveRestWrapper
- Policy-based authorization with auto-discovery
- Migrations as schema source of truth

## Layers

**HTTP/Routing Layer:**
- Purpose: Request routing and endpoint registration
- Contains: Route files, TypeRocket global route collection
- Location: `inc/routes/api.php`, `inc/routes/public.php`
- Depends on: Controllers, TypeRocket Router
- Used by: WordPress request lifecycle

**Controller Layer:**
- Purpose: HTTP request handling, input validation, response formatting
- Contains: API controllers (REST), Web controllers (forms)
- Location: `app/Controllers/Api/V1/*.php`, `app/Controllers/Web/*.php`
- Depends on: Models, Policies, TypeRocket Response
- Used by: Routes

**Model Layer:**
- Purpose: Data persistence, relationships, validation rules
- Contains: Active Record models extending TypeRocket Model
- Location: `app/Models/*.php`
- Depends on: Database (TypeRocket Query Builder)
- Used by: Controllers, REST API Wrapper

**Authorization Layer:**
- Purpose: Access control for CRUD operations
- Contains: Policy classes with capability methods
- Location: `app/Auth/*Policy.php`
- Depends on: AuthUser, Models
- Used by: Controllers, ReflectiveRestWrapper

**Data Layer:**
- Purpose: Schema definitions and migrations
- Contains: Migration files (source of truth)
- Location: `database/migrations/*.php`
- Depends on: TypeRocket Migration system
- Used by: Plugin activation/deactivation

## Data Flow

**REST API Request:**
1. HTTP Request → `/tr-api/rest/{resource}/{id}?search=term`
2. WordPress parse_request → TypeRocket route matching
3. ReflectiveRestWrapper intercepts (configured in plugin init)
4. Model introspection: searchable/filterable fields detected
5. Query building: search + filtering + sorting + pagination
6. Authorization check: Policy::index($user)
7. Response: JSON via TypeRocket\Http\Response

**Admin CRUD Request:**
1. WordPress admin page hook
2. Form rendering via `Helper::form()->setGroup()`
3. POST submission → Controller::create/update/destroy
4. Model validation via Fields class
5. Model save with fillable/guard protection
6. Redirect response with flash messages

**Plugin Lifecycle:**
1. `register_activation_hook` → `$plugin->activate()` → `migrateUp()`
2. `typerocket_loaded` (priority 9) → `$plugin->init()`
3. `deactivate_{plugin}` → flush_rewrite_rules
4. `uninstall.php` → `$plugin->uninstall()` → `migrateDown()`

**State Management:**
- Database-backed via WordPress + custom tables
- No in-memory session state
- Each request is stateless

## Key Abstractions

**BasePlugin Extension:**
- Purpose: Plugin initialization and lifecycle management
- Examples: `MakermakerTypeRocketPlugin`
- Pattern: Template Method (init, routes, policies, activate, uninstall)
- Location: `app/MakermakerTypeRocketPlugin.php`

**Model (Active Record):**
- Purpose: Data access with relationships and casting
- Examples: Models in `app/Models/`
- Pattern: Active Record with Query Builder
- Properties: `$fillable`, `$guard`, `$cast`, `$format`, `$with`

**Policy (Authorization):**
- Purpose: Define access rules per model action
- Examples: Policies in `app/Auth/*Policy.php`
- Pattern: Policy object with capability methods
- Methods: `index()`, `view()`, `create()`, `update()`, `delete()`

**ReflectiveRestWrapper:**
- Purpose: Zero-config REST API enhancement
- Examples: Auto-registered at `/tr-api/rest/{resource}`
- Pattern: Decorator wrapping TypeRocket resources
- Source: `vendor/mxcro/makermaker-core`

**Fields (Validation):**
- Purpose: Define validation rules and field configuration
- Examples: Fields in `app/Http/Fields/*.php`
- Pattern: Fluent builder for validation chains
- Location: `app/Http/Fields/`

## Entry Points

**Plugin Bootstrap:**
- Location: `makermaker.php`
- Triggers: WordPress plugin activation
- Responsibilities: Define constants, register autoloader, hook `typerocket_loaded`

**Plugin Class:**
- Location: `app/MakermakerTypeRocketPlugin.php`
- Triggers: `typerocket_loaded` action (priority 9)
- Responsibilities: Load config, resources, routes, policies, REST wrapper

**Routes:**
- Location: `inc/routes/api.php`, `inc/routes/public.php`
- Triggers: TypeRocket route registration
- Responsibilities: Define endpoint mappings

**CLI Entry:**
- Location: `galaxy/galaxy-makermaker-config.php`
- Triggers: `php galaxy` command
- Responsibilities: CRUD scaffolding via Galaxy CLI

## Error Handling

**Strategy:** Exception bubbling with boundary catches

**Patterns:**
- Controllers catch and transform exceptions to HTTP responses
- Models throw validation exceptions
- Whoops handles uncaught exceptions in development (`WP_DEBUG=true`)
- Policies return boolean (no exceptions)

**Error Services:**
- ErrorService configured in `config/app.php`
- File/Slack/Mail logging via `config/logging.php`

## Cross-Cutting Concerns

**Logging:**
- TypeRocket Logger service
- Drivers: File (default), Slack (webhook), Mail
- Config: `config/logging.php`

**Validation:**
- Fields classes in `app/Http/Fields/`
- Applied at controller level before model save
- Pipe-delimited rules: `required|string|min:3|max:255`

**Authentication:**
- WordPress native via AuthUser
- Checked in policies: `$user->isCapable('manage_options')`
- No custom auth middleware

**Asset Management:**
- Laravel Mix compilation
- Versioned via `mix-manifest.json`
- Enqueued in plugin init via WordPress hooks

---

*Architecture analysis: 2026-01-18*
*Update when major patterns change*
