# Technology Stack

**Analysis Date:** 2026-01-07

## Languages

**Primary:**
- PHP 8.2+ - All application code (`composer.json`)

**Secondary:**
- JavaScript - Frontend assets, build scripts (`package.json`)
- SQL - Database migrations (`database/migrations/*.sql`)

## Runtime

**Environment:**
- PHP 8.2+ (required via `composer.json`)
- WordPress (mu-plugin dependency: TypeRocket Pro v6)
- No direct Node.js runtime (build tooling only)

**Package Manager:**
- Composer - PHP dependencies
- Lockfile: `composer.lock` present
- npm - Frontend build tooling
- Lockfile: `package-lock.json` present

## Frameworks

**Core:**
- TypeRocket Pro v6 - WordPress plugin framework (MU-plugin)
  - ORM (Eloquent-style models)
  - Routing
  - Forms/validation
  - Authorization policies
  - REST API wrapper

**Testing:**
- Pest 2.34 - Primary test runner (`composer.json`)
- PHPUnit 10.5 - Underlying test framework
- Brain Monkey 2.6 - WordPress function mocking
- Mockery 1.6 - Object mocking

**Build/Dev:**
- Laravel Mix 4.0.7 - Asset compilation (`webpack.mix.js`)
- Webpack - Bundling (via Laravel Mix)
- Sass 1.15.2 - CSS preprocessing
- TypeScript 3.6.4 - Optional JS type checking

## Key Dependencies

**Critical:**
- `mxcro/makermaker-core` (dev-master) - CRUD scaffolding, helpers, Galaxy CLI (`composer.json`)
- `TypeRocket\Pro\Register\BasePlugin` - Plugin base class
- `MakermakerCore\Rest\ReflectiveRestWrapper` - Zero-config REST API

**PHP Infrastructure:**
- Symfony Console - CLI utilities
- Symfony Finder - File operations
- Symfony String - String manipulation
- Symfony Process - Process execution

**Frontend:**
- Lodash 4.17.13 - JS utilities (`package.json`)
- Vue Template Compiler 2.6.10 - Vue component support

## Configuration

**Environment:**
- Via `typerocket_env()` helper function
- Config files: `config/mail.php`, `config/logging.php`
- No `.env` in plugin (uses WordPress configuration)

**Build:**
- `webpack.mix.js` - Asset pipeline configuration
- `composer.json` - PHP autoloading and scripts
- `package.json` - npm scripts (watch, dev, prod, hot)
- `phpunit.xml` - Test runner configuration

## Platform Requirements

**Development:**
- Any platform with PHP 8.2+ and Composer
- Node.js/npm for asset compilation
- MySQL/MariaDB database (WordPress)

**Production:**
- WordPress 6.x installation
- TypeRocket Pro v6 as mu-plugin
- PHP 8.2+ with standard extensions
- MySQL 5.7+ or MariaDB 10.3+

---

*Stack analysis: 2026-01-07*
*Update after major dependency changes*
