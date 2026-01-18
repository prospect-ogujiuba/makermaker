# Technology Stack

**Analysis Date:** 2026-01-18

## Languages

**Primary:**
- PHP 8.2+ - All application code (`composer.json`)

**Secondary:**
- JavaScript (ES6) - Build tooling and frontend (`package.json`)
- TypeScript 3.6 - Optional typing support (`package.json`)
- SCSS - Stylesheets (`resources/sass/`)

## Runtime

**Environment:**
- WordPress 5.x+ (platform dependency)
- PHP 8.2+ required (`composer.json` engines)
- TypeRocket Pro v6 framework

**Package Manager:**
- Composer (PHP) - `composer.json`
- npm (Node.js) - `package.json`
- Lockfile: None committed (both in `.gitignore`)

## Frameworks

**Core:**
- TypeRocket Pro v6 - MVC framework for WordPress (`composer.json`)
- MakerMaker Core dev-master - Custom scaffolding library (`vendor/mxcro/makermaker-core`)

**Testing:**
- Pest v2.36.0 - Expressive test framework (`composer.json`)
- PHPUnit 10.5.36 - Test runner backend (`vendor/composer/installed.json`)
- Brain Monkey 2.6.2 - WordPress function mocking (`composer.json`)
- Mockery 1.6.12 - Object mocking (`composer.json`)

**Build/Dev:**
- Laravel Mix 4.0.7 - Asset compilation (`package.json`, `webpack.mix.js`)
- Webpack (via Mix) - Bundling
- Sass 1.15.2 - CSS preprocessing (`package.json`)

## Key Dependencies

**Critical:**
- typerocket/professional - Core MVC framework, admin UI, REST API
- mxcro/makermaker-core - ReflectiveRestWrapper, resource registration helpers

**Infrastructure:**
- symfony/console v7.4.3 - CLI commands
- symfony/finder v7.4.3 - File discovery
- filp/whoops 2.18.4 - Error handling UI
- nunomaduro/collision v8.5.0 - CLI error formatting

**Testing:**
- pestphp/pest-plugin-arch v2.7.0 - Architecture testing
- ta-tikoma/phpunit-architecture-test 0.8.5 - Structural assertions

## Configuration

**Environment:**
- Via `typerocket_env()` function calls
- No `.env.example` documented (in `.gitignore`)
- Key vars: `TYPEROCKET_DATABASE_*`, `TYPEROCKET_MAILGUN_*`, `TYPEROCKET_GOOGLE_MAPS_API_KEY`

**Build:**
- `webpack.mix.js` - Asset compilation targets
- `phpunit.xml` - Test runner configuration
- `composer.json` - Autoloading (PSR-4: `MakerMaker\` → `app/`)

## Platform Requirements

**Development:**
- Any platform with PHP 8.2+ and Node.js
- WordPress installation for integration tests
- Composer and npm

**Production:**
- WordPress plugin deployment
- TypeRocket Pro license active
- PHP 8.2+ with WordPress 5.x+

---

*Stack analysis: 2026-01-18*
*Update after major dependency changes*
