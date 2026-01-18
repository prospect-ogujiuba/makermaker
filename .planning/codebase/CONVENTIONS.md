# Coding Conventions

**Analysis Date:** 2026-01-18

## Naming Patterns

**Files:**
- PascalCase for class files: `MakermakerTypeRocketPlugin.php`, `Service.php`
- *Test.php suffix for tests: `BasicUnitTest.php`, `BasicFeatureTest.php`
- *Policy.php suffix for policies: `ServicePolicy.php`
- *Controller.php suffix for controllers: `ServiceController.php`
- kebab-case for config: `app.php`, `database.php`

**Functions:**
- camelCase for all methods: `init()`, `loadResources()`, `discoverPolicies()`
- No special prefix for async/private methods
- Lifecycle methods: `init()`, `activate()`, `deactivate()`, `uninstall()`
- CRUD methods: `index()`, `create()`, `show()`, `update()`, `edit()`, `destroy()`

**Variables:**
- camelCase for variables: `$policies`, `$resourceFiles`, `$modelName`
- snake_case acceptable in arrays: `$model_class`, `$policy_class`
- UPPER_SNAKE_CASE for constants: `MAKERMAKER_PLUGIN_DIR`, `GLOBAL_WPDB_PREFIX`

**Types:**
- PascalCase for classes: `Service`, `ServiceController`, `ServicePolicy`
- No I prefix for interfaces
- Namespace: `MakerMaker\` root, `MakerMaker\Tests\` for tests

## Code Style

**Formatting:**
- 4-space indentation (PHP standard)
- Opening braces on same line (PSR-12)
- Single quotes for strings: `'makermaker'`
- Double quotes for interpolation: `"plugins/{$file}"`

**Linting:**
- No explicit linter config (`.phpcs.xml` not present)
- Follow PSR-12 conventions implicitly
- TypeRocket patterns preferred

## Import Organization

**Order:**
1. PHP built-ins (`use Exception`)
2. Framework classes (`use TypeRocket\...`)
3. Plugin classes (`use MakerMaker\...`)

**Grouping:**
- Group related imports together
- One `use` statement per line
- No aliasing unless collision

**Path Aliases:**
- PSR-4: `MakerMaker\` → `app/`
- PSR-4: `MakerMaker\Tests\` → `tests/`
- No path aliases in TypeRocket imports

## Error Handling

**Patterns:**
- Throw exceptions, catch at controller boundaries
- TypeRocket Response for HTTP error formatting
- Whoops for development error display (`WP_DEBUG=true`)

**Error Types:**
- Validation errors: Throw with field-specific messages
- Authorization errors: Return false from policies
- System errors: Log and throw

**Logging:**
- Use TypeRocket Logger service
- Structured context: `Logger::error('message', ['context' => 'data'])`

## Logging

**Framework:**
- TypeRocket Logger (File/Slack/Mail drivers)
- Config: `config/logging.php`

**Patterns:**
- Log at service boundaries
- Include context data in structured format
- No console.log equivalent in PHP

## Comments

**When to Comment:**
- PHPDoc blocks for public methods
- Explain complex business logic
- Document non-obvious TypeRocket patterns

**PHPDoc:**
- Required for public API methods
- Use `@param`, `@return`, `@throws` tags
- Type hints preferred over doc types when possible

**Section Headers:**
```php
/*
|--------------------------------------------------------------------------
| Section Name
|--------------------------------------------------------------------------
|
| Description of the section's purpose.
|
*/
```

**TODO Comments:**
- Format: `// TODO: description`
- No username attribution (use git blame)

## Function Design

**Size:**
- Keep under 50 lines
- Extract private helpers for complex logic
- Single responsibility per method

**Parameters:**
- Max 3-4 parameters
- Use typed parameters (PHP 8.2+)
- Dependency injection via constructor or method

**Return Values:**
- Explicit return types on all public methods
- Return early for guard clauses
- Void methods for side effects only

## Module Design

**Exports:**
- One class per file
- Class name matches filename
- No barrel files in PHP (not applicable)

**Dependencies:**
- Inject via constructor
- Type-hint dependencies
- Avoid static calls except for helpers

## Controller Patterns

**REST Controllers:**
```php
public function index(Response $response): Response
{
    $items = Model::findAll()->get();
    return $response->withData($items);
}

public function show($id, Response $response): Response
{
    $item = Model::findById($id);
    return $response->withData($item);
}
```

**Web Controllers:**
```php
public function create(Fields $fields, Response $response): Response
{
    $data = $fields->getValidated();
    Model::create($data);
    return tr_redirect()->toAdmin('page')->withFlash('Created');
}
```

## Model Patterns

**Property Configuration:**
```php
protected $fillable = ['name', 'description', 'price'];
protected $guard = ['id', 'created_at', 'updated_at'];
protected $cast = ['price' => 'float', 'active' => 'boolean'];
protected $format = ['created_at' => 'Y-m-d H:i:s'];
```

**Relationships:**
```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class, 'category_id');
}

public function items(): HasMany
{
    return $this->hasMany(Item::class, 'parent_id');
}
```

## Policy Patterns

**Capability Methods:**
```php
public function index(AuthUser $user): bool
{
    return true; // Everyone can list
}

public function view(AuthUser $user, Model $model): bool
{
    return $user->ID === $model->created_by || $user->isCapable('manage_options');
}

public function create(AuthUser $user): bool
{
    return $user->isCapable('create_resources');
}
```

---

*Convention analysis: 2026-01-18*
*Update when patterns change*
