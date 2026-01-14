# Coding Conventions

**Analysis Date:** 2026-01-07

## Naming Patterns

**Files:**
- PascalCase for PHP classes: `Service.php`, `ServiceController.php`
- `*Controller.php` for HTTP handlers
- `*Policy.php` for authorization
- `*Fields.php` for validation
- `*Helper.php` for utilities
- snake_case for views: `services/index.php`, `coverage_areas/form.php`

**Functions:**
- camelCase for all methods: `create()`, `update()`, `getActiveTeamMembers()`
- Boolean methods: prefix with `is` or `has` (`isActive()`, `hasPrerequisites()`)
- Getters: prefix with `get` (`getTeamMembersCount()`)
- REST endpoints: suffix with `Rest` (`indexRest()`, `showRest()`)

**Variables:**
- camelCase for variables: `$authUser`, `$serviceId`, `$priceMultiplier`
- snake_case for database columns: `created_at`, `updated_by`, `service_type_id`
- UPPER_SNAKE_CASE for constants: `GLOBAL_WPDB_PREFIX`, `MAKERMAKER_PLUGIN_DIR`

**Types:**
- PascalCase for interfaces and types
- No `I` prefix on interfaces
- Enums: PascalCase name, string values

## Code Style

**Formatting:**
- 4 spaces per indentation level
- Unix line endings (LF)
- UTF-8 charset with no BOM
- No closing PHP tags (`?>` omitted)
- 120 character line length (observed)

**Linting:**
- No explicit linter configured
- Follows PSR-12 style by convention
- Laravel Pint available via vendor (`vendor/pestphp/pest-plugin-arch/pint.json`)

## Import Organization

**Order:**
1. PHP namespace declaration
2. `use` statements for classes
3. Class definition

**Grouping:**
- TypeRocket framework classes first
- Local namespace classes (MakerMaker) second
- Blank line between groups

**Path Aliases:**
- `MakerMaker\` → `app/`
- `MakermakerCore\` → `vendor/mxcro/makermaker-core/`

## Error Handling

**Patterns:**
- Models collect errors via `$model->getErrors()`
- Controllers check errors after save
- REST: Return JSON via `RestHelper::errorResponse()`
- HTML: Flash message via `$response->flashNext()` + redirect

**Error Types:**
- Validation errors: Returned via TypeRocket Fields
- Authorization errors: Via `AuthorizationHelper::authorize()`
- Business logic errors: Custom messages in controllers

**Logging:**
- `error_log()` for debug (should be refactored)
- TypeRocket logging via `config/logging.php`

## Logging

**Framework:**
- TypeRocket logging system
- Channels: File, Slack (optional)

**Patterns:**
- Debug: `error_log()` (legacy, should migrate)
- Production: TypeRocket FileLogger/SlackLogger

**Where:**
- Controllers log errors on save failures
- Helpers log exceptions in critical operations

## Comments

**When to Comment:**
- Explain business logic
- Document complex algorithms
- Note edge cases and workarounds
- Section dividers for large files

**PHPDoc/TSDoc:**
- Required for public methods in helpers
- Optional for controllers (methods are self-documenting)
- Use `@param`, `@return`, `@throws` tags

**TODO Comments:**
- Format: `// TODO: description`
- No username required (use git blame)
- Avoid leaving stale TODOs

## Function Design

**Size:**
- Keep under 50 lines when possible
- Large controllers (~1000 lines) need refactoring
- Helpers can be longer (utility collections)

**Parameters:**
- Max 3-4 positional parameters
- Use dependency injection for services
- TypeRocket injects: `$model`, `$fields`, `$response`, `$user`

**Return Values:**
- Controllers: Response object or redirect
- Helpers: Static methods return typed values
- Models: Return boolean or self for chaining

## Module Design

**Exports:**
- One class per file
- Namespace mirrors directory structure
- No barrel files (direct imports)

**Barrel Files:**
- Not used in this codebase
- Each class imported directly

## Controller Pattern

**Standard CRUD:**
```php
public function create(EntityFields $fields, Entity $entity, Response $response, AuthUser $user)
{
    AuthorizationHelper::authorize($entity, 'create', $response);
    autoGenerateCode($fields, 'slug', 'name', '-');
    AuditTrailHelper::setCreateAuditFields($entity, $user);
    $entity->save($fields);

    if ($entity->getErrors()) {
        // Handle errors (REST or HTML)
    }

    // Success response (REST or HTML)
}
```

## Model Pattern

**Standard Model:**
```php
class Entity extends Model
{
    protected $resource = 'srvc_entities';
    protected $private = ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by'];
    protected $fillable = ['name', 'slug', ...];
    protected $guard = ['id', 'version', 'created_at', ...];
    protected $format = ['metadata' => 'json_encode'];
    protected $cast = ['metadata' => 'array'];
    protected $with = ['relationship1', 'relationship2'];
}
```

## Policy Pattern

**Standard Policy:**
```php
public function create(AuthUser $auth, $object)
{
    return $auth->isCapable('manage_entities');
}
```

## Fields Pattern

**Standard Validation:**
```php
class EntityFields extends Fields
{
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'slug' => 'unique:field:slug@id:' . ($this->id ?? ''),
        ];
    }
}
```

---

*Convention analysis: 2026-01-07*
*Update when patterns change*
