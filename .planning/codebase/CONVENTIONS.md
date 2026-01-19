# Coding Conventions

**Analysis Date:** 2026-01-19

## Naming Patterns

**Files:**
- Models: `PascalCase.php` (e.g., `Product.php`, `ServicePrice.php`)
- Controllers: `{Model}Controller.php` (e.g., `ProductController.php`)
- Policies: `{Model}Policy.php` (e.g., `ProductPolicy.php`)
- Fields (validators): `{Model}Fields.php` (e.g., `ProductFields.php`)
- Migrations: `{timestamp}.{action}_{table}_table.sql` (e.g., `1705678900.create_products_table.sql`)
- Resources: `{snake_case}.php` (e.g., `product.php`, `service_price.php`)
- Views: `{plural_snake}/index.php`, `{plural_snake}/form.php`

**Classes:**
- Models: `PascalCase` matching filename
- Controllers: `{Model}Controller`
- Policies: `{Model}Policy`
- Fields: `{Model}Fields`

**Functions:**
- Helper functions: `snake_case` with `mm_` prefix for plugin-specific (e.g., `mm_table()`, `mm_search_columns()`)
- Procedural wrappers: `camelCase` for TypeRocket compat (e.g., `tryDatabaseOperation()`, `toTitleCase()`)
- Validation callbacks: `camelCase` (e.g., `checkIntRange()`, `checkSelfReference()`)

**Variables:**
- Model instances: `$camelCase` matching singular model (e.g., `$product`, `$servicePrice`)
- Plural collections: `$camelCasePlural` (e.g., `$products`, `$servicePrices`)
- Database columns: `snake_case` (e.g., `created_at`, `category_id`, `is_active`)

**Types/Namespaces:**
- Plugin namespace: `MakerMaker\`
- Core library namespace: `MakermakerCore\`
- Models: `MakerMaker\Models\`
- Controllers: `MakerMaker\Controllers\`
- Policies: `MakerMaker\Auth\`
- Fields: `MakerMaker\Http\Fields\`

## Code Style

**Formatting:**
- PSR-12 style implied by TypeRocket conventions
- 4-space indentation
- Opening brace on same line for classes/methods

**Linting:**
- No explicit linter configured
- Rely on IDE and Pest for catching issues

## Import Organization

**Order:**
1. PHP built-in classes
2. TypeRocket framework classes
3. MakermakerCore (vendor) classes
4. MakerMaker (plugin) classes

**Example:**
```php
<?php

namespace MakerMaker\Controllers;

use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;
use MakermakerCore\Helpers\AuthorizationHelper;
use MakermakerCore\Helpers\AuditTrailHelper;
use MakerMaker\Http\Fields\ProductFields;
use MakerMaker\Models\Product;
use MakerMaker\View;
```

## Error Handling

**Controller Pattern:**
```php
public function create(ProductFields $fields, Product $product, Response $response, AuthUser $user)
{
    AuthorizationHelper::authorize($product, 'create', $response);
    AuditTrailHelper::setCreateAuditFields($product, $user);

    $success = tryDatabaseOperation(
        fn() => $product->save($fields),
        $response,
        'Product created successfully',
        $fields
    );

    if ($success) {
        return tr_redirect()->toPage('product', 'index')->withFlash('Product created');
    }
    return tr_redirect()->back()->withErrors($response->getErrors());
}
```

**Database Operations:**
- Always wrap in `tryDatabaseOperation()` helper
- Helper sanitizes error messages (hides SQL details)
- Returns boolean success status

**Validation Errors:**
- Return error string from callback validators: `return ' must be between 1 and 100';`
- Return `true` for valid input

**REST API Errors:**
- Throw exceptions with HTTP status code: `throw new \Exception("Not found", 404);`
- ReflectiveRestWrapper catches and formats as JSON

## Model Conventions

**Properties:**
```php
class Product extends Model
{
    protected $resource = 'products';         // Table name (plural snake_case)

    protected $fillable = [                   // Mass-assignable fields
        'title',
        'description',
        'category_id',
    ];

    protected $guard = [                      // Protected from mass assignment
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
    ];

    protected $private = [                    // Hidden from API responses
        'created_by',
        'updated_by',
    ];

    protected $cast = ['metadata' => 'array'];     // Type casting
    protected $format = ['metadata' => 'json_encode'];  // Format on save
    protected $with = ['category'];                // Eager load relations
}
```

**Relationships:**
```php
public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}
```

**Custom Actions (REST):**
```php
use MakermakerCore\Attributes\Action;

#[Action(capability: 'create', description: 'Duplicate this product')]
public function duplicate(AuthUser $user, array $params): array
{
    // Clone logic
    return ['success' => true, 'data' => $newRecord];
}
```

## Controller Conventions

**Standard Methods:**
- `index()` - List view
- `add()` - Create form view
- `create()` - Handle create POST
- `edit()` - Edit form view
- `update()` - Handle update PUT
- `show()` - Single record
- `destroy()` - Handle delete DELETE

## Policy Conventions

**Standard Methods:**
```php
class ProductPolicy extends Policy
{
    public function create(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_products');
    }

    public function read(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_products');
    }

    public function update(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_products');
    }

    public function destroy(AuthUser $auth, $object)
    {
        return $auth->isCapable('manage_products');
    }
}
```

**Capability Naming:** `manage_{plural_snake}` (e.g., `manage_products`)

## Migration Conventions

**Format:** Raw SQL with Up/Down sections
```sql
-- Description: Create products table
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}products` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}products`;
```

**Standard Columns:**
- `id` BIGINT UNSIGNED AUTO_INCREMENT
- `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
- `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP
- `deleted_at` DATETIME DEFAULT NULL (soft delete)
- `created_by` BIGINT UNSIGNED NOT NULL
- `updated_by` BIGINT UNSIGNED NOT NULL

---

*Convention analysis: 2026-01-19*
