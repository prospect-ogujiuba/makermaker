# Codebase Structure

**Analysis Date:** 2026-01-07

## Directory Layout

```
makermaker/
├── makermaker.php          # Plugin entry point
├── uninstall.php           # Cleanup hook
├── index.php               # Direct access blocker
├── app/                    # Business logic layer
│   ├── Auth/              # Authorization policies (23)
│   ├── Controllers/       # HTTP handlers (23)
│   ├── Helpers/           # Cross-cutting utilities
│   ├── Http/Fields/       # Validation rules (26)
│   └── Models/            # Data entities (24)
├── inc/                    # Infrastructure
│   ├── resources/         # Resource registration
│   └── routes/            # Endpoint definitions
├── database/               # Data persistence
│   ├── migrations/        # SQL schema files (18)
│   └── docs/              # Schema documentation
├── resources/              # Asset source
│   ├── js/               # JavaScript source
│   ├── sass/             # SCSS source
│   └── views/            # Blade templates (24 dirs)
├── public/                 # Compiled assets
│   ├── admin/            # Admin JS/CSS
│   └── front/            # Frontend JS/CSS
├── tests/                  # Test suites
│   ├── Unit/             # Unit tests
│   ├── Integration/      # Integration tests
│   ├── Feature/          # Feature tests
│   ├── Acceptance/       # Acceptance tests
│   └── Factories/        # Test data factories
├── config/                 # Configuration files
├── vendor/                 # Composer dependencies
└── galaxy/                 # Galaxy CLI helpers
```

## Directory Purposes

**app/**
- Purpose: Core business logic
- Contains: PHP classes (models, controllers, policies, fields, helpers)
- Key files: `MakermakerTypeRocketPlugin.php` (main plugin class), `View.php` (view wrapper)
- Subdirectories: `Auth/`, `Controllers/`, `Helpers/`, `Http/Fields/`, `Models/`

**app/Auth/**
- Purpose: Authorization policies
- Contains: 23 `*Policy.php` files
- Key files: `ServicePolicy.php`, `ContactSubmissionPolicy.php`
- Pattern: `{Model}Policy` → `Models\{Model}`

**app/Controllers/**
- Purpose: HTTP request handlers
- Contains: 23 `*Controller.php` files
- Key files: `ServiceController.php`, `ContactSubmissionController.php`
- Pattern: CRUD methods + REST endpoints

**app/Models/**
- Purpose: Data entities with relationships
- Contains: 24 model classes
- Key files: `Service.php`, `ServicePrice.php`, `Equipment.php`, `ContactSubmission.php`
- Pattern: Eloquent-style ORM

**app/Http/Fields/**
- Purpose: Form validation rules
- Contains: 26 `*Fields.php` files
- Key files: `ServiceFields.php`, `ServicePriceFields.php`
- Pattern: `rules()` method returns validation array

**app/Helpers/**
- Purpose: Utility functions
- Contains: `ServiceCatalogHelper.php` (2183 lines), `TeamHelper.php`
- Pattern: Static methods for cross-model operations

**inc/resources/**
- Purpose: WordPress admin menu registration
- Contains: `service.php`, `contact_submission.php`, `team.php`
- Pattern: `mm_create_custom_resource()` calls

**inc/routes/**
- Purpose: HTTP endpoint definitions
- Contains: `api.php`, `public.php`
- Pattern: TypeRocket route collection

**database/migrations/**
- Purpose: SQL schema definitions
- Contains: 18 SQL files
- Naming: `{timestamp}.{description}.sql`
- Prefix convention: `srvc_` for all tables

**resources/views/**
- Purpose: Blade-style templates
- Contains: 24 directories (one per entity)
- Structure: `{entity}/index.php`, `{entity}/form.php`

**tests/**
- Purpose: Test suites
- Contains: Unit, Integration, Feature, Acceptance directories
- Key files: `Pest.php` (config), `bootstrap.php` (setup), `Factories/ServiceFactory.php`

**config/**
- Purpose: Configuration files
- Contains: `mail.php`, `logging.php`
- Pattern: Return arrays for TypeRocket config

## Key File Locations

**Entry Points:**
- `makermaker.php` - Plugin bootstrap
- `app/MakermakerTypeRocketPlugin.php` - Main plugin class

**Configuration:**
- `composer.json` - PHP dependencies
- `package.json` - npm dependencies
- `webpack.mix.js` - Asset compilation
- `phpunit.xml` - Test configuration
- `config/mail.php` - Email configuration
- `config/logging.php` - Logging configuration

**Core Logic:**
- `app/Models/Service.php` - Main service entity
- `app/Controllers/ServiceController.php` - Service CRUD
- `app/Helpers/ServiceCatalogHelper.php` - Business utilities

**Testing:**
- `tests/Pest.php` - Test setup and custom expectations
- `tests/bootstrap.php` - WordPress/DB initialization
- `tests/Factories/ServiceFactory.php` - Test data factory

**Documentation:**
- `CLAUDE.md` - Architecture guide

## Naming Conventions

**Files:**
- PascalCase.php: PHP classes (`Service.php`, `ServiceController.php`)
- kebab-case.sql: Migrations (`1758895156.create_services_table.sql`)
- kebab-case.php: Views, resources (`service.php`, `form.php`)

**Directories:**
- PascalCase: Class directories (`Controllers/`, `Models/`)
- lowercase: Asset directories (`js/`, `sass/`, `views/`)
- snake_case: View subdirectories (`service_prices/`, `coverage_areas/`)

**Special Patterns:**
- `*Controller.php`: Controller classes
- `*Policy.php`: Authorization policies
- `*Fields.php`: Validation classes
- `*Helper.php`: Utility classes
- `*.test.php` or `*Test.php`: Test files

## Where to Add New Code

**New Feature (Model/CRUD):**
- Model: `app/Models/{Entity}.php`
- Controller: `app/Controllers/{Entity}Controller.php`
- Policy: `app/Auth/{Entity}Policy.php`
- Fields: `app/Http/Fields/{Entity}Fields.php`
- Views: `resources/views/{entity}/index.php`, `form.php`
- Resource: `inc/resources/{entity}.php`
- Migration: `database/migrations/{timestamp}.create_{entity}_table.sql`
- Tests: `tests/Unit/{Entity}Test.php`, `tests/Integration/`

**New Helper Method:**
- Location: `app/Helpers/ServiceCatalogHelper.php` (or new helper class)
- Tests: `tests/Unit/Helpers/`

**New Route:**
- API: `inc/routes/api.php`
- Public: `inc/routes/public.php`

**Utilities:**
- Shared helpers: `app/Helpers/`
- Type definitions: Within model files

## Special Directories

**vendor/**
- Purpose: Composer dependencies
- Source: Auto-generated by `composer install`
- Key package: `vendor/mxcro/makermaker-core/`
- Committed: No (gitignored)

**public/**
- Purpose: Compiled assets
- Source: Generated by `npm run dev/prod`
- Contains: `mix-manifest.json` for cache-busting
- Committed: Yes (compiled output)

**node_modules/**
- Purpose: npm dependencies
- Source: Auto-generated by `npm install`
- Committed: No (gitignored)

---

*Structure analysis: 2026-01-07*
*Update when directory structure changes*
