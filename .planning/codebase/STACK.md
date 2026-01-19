# Technology Stack

**Analysis Date:** 2026-01-19

## Languages

**Primary:**
- PHP ^8.2 - Backend logic, WordPress integration, TypeRocket framework

**Secondary:**
- JavaScript ES6+ - Frontend assets (`resources/js/`)
- SCSS - Stylesheets (`resources/sass/`)
- TypeScript 3.6.4 (devDependency, available but not actively used)

## Runtime

**Environment:**
- PHP 8.2+
- WordPress (required host platform)
- TypeRocket Pro v6 (framework layer)

**Package Manager:**
- Composer (PHP dependencies)
- npm (frontend assets)
- Lockfiles: Not committed (`.gitignore`)

## Frameworks

**Core:**
- TypeRocket Pro v6 - WordPress MVC framework
  - `TypeRocket\Pro\Register\BasePlugin` - Plugin base class
  - `TypeRocket\Core\System` - System utilities
  - `TypeRocket\Database` - ORM layer
  - `TypeRocket\Http` - Request/Response handling

**Frontend Build:**
- Laravel Mix 4.0.7 - Asset compilation
- Webpack (via Laravel Mix)
- Sass 1.15.2 - CSS preprocessing

**Testing:**
- Pest 2.34 - Test runner (built on PHPUnit 10.5)
- Brain\Monkey 2.6 - WordPress function mocking
- Mockery 1.6 - PHP mocking

## Key Dependencies

**Critical (require):**
- `mxcro/makermaker-core` dev-master - Core scaffolding library
  - ReflectiveRestWrapper - Zero-config REST API
  - Galaxy CLI commands (Crud scaffolding)
  - Admin helpers (tables, forms, filters)

**Infrastructure (TypeRocket Pro provides):**
- Tachyon template engine
- Database migrations
- Job queue (Action Scheduler)
- Mail drivers (WordPress, Mailgun, Log)
- Logging drivers (File, Slack, Mail)

**Dev Dependencies:**
- `phpunit/phpunit` ^10.5
- `pestphp/pest` ^2.34
- `brain/monkey` ^2.6
- `mockery/mockery` ^1.6

**Frontend (devDependencies):**
- `laravel-mix` ^4.0.7
- `cross-env` ^5.1
- `sass` ^1.15.2
- `lodash` ^4.17.13

## Configuration

**Environment Variables:**
- `WP_DEBUG` - Debug mode toggle
- `TYPEROCKET_DATABASE_DEFAULT` - DB driver (default: `wp`)
- `TYPEROCKET_ALT_DATABASE_*` - Alternative DB connection
- `TYPEROCKET_MAIL_DEFAULT` - Mail driver (default: `wp`)
- `TYPEROCKET_MAILGUN_*` - Mailgun credentials
- `TYPEROCKET_LOG_DEFAULT` - Log driver (default: `stack`)
- `TYPEROCKET_LOG_SLACK_WEBHOOK_URL` - Slack logging
- `TYPEROCKET_GOOGLE_MAPS_API_KEY` - Maps integration
- `TYPEROCKET_GALAXY_LOAD_WP` - Galaxy CLI WordPress loading

**Plugin Constants (defined in `makermaker.php`):**
```php
MAKERMAKER_PLUGIN_DIR       // Plugin directory path
MAKERMAKER_PLUGIN_URL       // Plugin URL
GLOBAL_WPDB_PREFIX          // WordPress table prefix
TYPEROCKET_PLUGIN_MAKERMAKER_VIEWS_PATH  // Views directory
```

**Configuration Files:**
- `config/app.php` - Extensions, services, template engine
- `config/database.php` - Database drivers
- `config/mail.php` - Mail drivers
- `config/logging.php` - Log channels
- `config/queue.php` - Action Scheduler settings
- `config/storage.php` - File storage drivers
- `config/paths.php` - Directory paths
- `config/external.php` - External APIs (Google Maps)
- `config/galaxy.php` - CLI commands

## Build Commands

**PHP:**
```bash
composer install          # Install dependencies
composer test             # Run all tests (Pest)
composer test:unit        # Unit tests only
composer test:ci          # With 85% coverage requirement
```

**Assets:**
```bash
npm install               # Install frontend dependencies
npm run dev               # Development build
npm run watch             # Watch mode
npm run prod              # Production build (versioned)
```

**Galaxy CLI:**
```bash
php galaxy make:crud Product --template=standard
```

## Platform Requirements

**Development:**
- PHP 8.2+
- Composer 2.x
- Node.js (for asset compilation)
- WordPress installation with TypeRocket Pro

**Production:**
- PHP 8.2+
- WordPress 6.x (assumed)
- MySQL/MariaDB (via WordPress)

---

*Stack analysis: 2026-01-19*
