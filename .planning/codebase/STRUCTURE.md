# Directory Structure

**Analysis Date:** 2026-01-19

## Root Layout

```
makermaker/
├── app/                    # Application code (MVC)
├── config/                 # TypeRocket configuration
├── database/               # Migrations
├── galaxy/                 # CLI commands
├── inc/                    # Resources and routes
├── public/                 # Compiled assets
├── resources/              # Source assets and views
├── tests/                  # Test suites
├── vendor/                 # Composer dependencies
├── .planning/              # GSD planning documents
├── makermaker.php          # Plugin entry point
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── phpunit.xml             # Test configuration
└── webpack.mix.js          # Asset build config
```

## Key Locations

### Application (`app/`)

```
app/
├── MakermakerTypeRocketPlugin.php   # Main plugin class
├── View.php                          # View helper
├── Auth/                             # Policies (empty scaffold)
├── Controllers/                      # Controllers (empty scaffold)
├── Http/
│   └── Fields/                       # Validation classes (empty scaffold)
└── Models/                           # Models (empty scaffold)
```

**Add new code:**
- Models: `app/Models/{PascalCase}.php`
- Controllers: `app/Controllers/{Model}Controller.php`
- Policies: `app/Auth/{Model}Policy.php`
- Field validators: `app/Http/Fields/{Model}Fields.php`

### Configuration (`config/`)

```
config/
├── app.php          # Extensions, services, debug, template engine
├── components.php   # UI components
├── cookies.php      # Cookie settings
├── database.php     # Database connections
├── editor.php       # Editor settings
├── external.php     # External APIs (Google Maps)
├── galaxy.php       # CLI commands
├── logging.php      # Log channels
├── mail.php         # Mail drivers
├── paths.php        # Directory paths
├── queue.php        # Job queue settings
├── rapid.php        # Rapid mode settings
├── storage.php      # Storage drivers
└── urls.php         # URL patterns
```

### Database (`database/`)

```
database/
└── migrations/      # SQL migration files (empty scaffold)
```

**Add migrations:** `database/migrations/{timestamp}.{action}_{table}_table.sql`

### Resources (`inc/`)

```
inc/
├── resources/       # Custom resource definitions (empty scaffold)
└── routes/
    ├── api.php      # API routes
    └── public.php   # Public routes
```

**Add resources:** `inc/resources/{snake_case}.php`

### Views (`resources/views/`)

```
resources/views/
├── admin/           # Admin-specific views
│   └── {resource}/
│       ├── index.php
│       └── form.php
├── settings.php     # Plugin settings page
└── ...
```

**Add views:** `resources/views/{plural_snake}/index.php`, `resources/views/{plural_snake}/form.php`

### Assets (`resources/js/`, `resources/sass/`)

```
resources/
├── js/
│   ├── admin.js     # Admin JavaScript
│   └── front.js     # Frontend JavaScript
└── sass/
    ├── admin.scss   # Admin styles
    └── front.scss   # Frontend styles
```

### Compiled Assets (`public/`)

```
public/
├── admin/
│   ├── admin.css
│   └── admin.js
├── front/
│   ├── front.css
│   └── front.js
└── mix-manifest.json
```

### Tests (`tests/`)

```
tests/
├── Acceptance/      # End-to-end tests
├── Feature/         # Feature tests
├── Integration/     # Integration tests
├── Unit/            # Unit tests
├── Pest.php         # Pest configuration
└── bootstrap.php    # Test bootstrap
```

**Add tests:** `tests/{Suite}/{Feature}Test.php`

## Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Model | PascalCase | `Product.php` |
| Controller | {Model}Controller | `ProductController.php` |
| Policy | {Model}Policy | `ProductPolicy.php` |
| Fields | {Model}Fields | `ProductFields.php` |
| Migration | {timestamp}.{action}_{table}_table.sql | `1705678900.create_products_table.sql` |
| Resource | snake_case | `product.php` |
| View dir | plural_snake | `products/` |
| Table | plural_snake | `products` |

## File Placement Guide

**When adding a new CRUD resource:**

1. Migration: `database/migrations/{timestamp}.create_{table}_table.sql`
2. Model: `app/Models/{Model}.php`
3. Controller: `app/Controllers/{Model}Controller.php`
4. Policy: `app/Auth/{Model}Policy.php`
5. Fields: `app/Http/Fields/{Model}Fields.php`
6. Resource: `inc/resources/{snake}.php`
7. Views: `resources/views/{plural}/index.php`, `resources/views/{plural}/form.php`

**Or use Galaxy CLI:** `php galaxy make:crud {Model}`

---

*Structure analysis: 2026-01-19*
