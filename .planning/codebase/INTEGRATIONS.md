# External Integrations

**Analysis Date:** 2026-01-18

## APIs & External Services

**Google Maps:**
- Google Maps JavaScript API - Map embedding
  - SDK/Client: TypeRocket built-in component
  - Auth: `TYPEROCKET_GOOGLE_MAPS_API_KEY` env var
  - Config: `config/external.php`

**Email/SMS:**
- Mailgun - Transactional email driver (optional)
  - SDK/Client: `TypeRocket\Pro\Utility\Mailers\MailgunMailDriver`
  - Auth: `TYPEROCKET_MAILGUN_API_KEY`, `TYPEROCKET_MAILGUN_DOMAIN`
  - Config: `config/mail.php`
- WordPress Native Mailer - Default email driver
  - SDK/Client: `TypeRocket\Pro\Utility\Mailers\WordPressMailDriver`

**CDN:**
- Bootstrap Icons v1.11.3 - Icon library
  - Loaded from jsdelivr CDN
  - Enqueued in: `app/MakermakerTypeRocketPlugin.php:136`

## Data Storage

**Databases:**
- WordPress Core Database - Primary data store
  - Connection: Via WordPress `$wpdb` global
  - Driver: `TypeRocket\Database\Connectors\WordPressCoreDatabaseConnector`
  - Config: `config/database.php`
- Alternative Database - Optional secondary connection
  - Env vars: `TYPEROCKET_ALT_DATABASE_*` (USER, PASSWORD, DATABASE, HOST)

**File Storage:**
- Storage Drive - Local file system storage
  - Driver: `TypeRocket\Pro\Utility\Drives\StorageDrive`
  - Path: `{plugin}/storage/`
- Uploads Drive - WordPress uploads integration
  - Driver: `TypeRocket\Pro\Utility\Drives\UploadsDrive`
- Root Drive - Plugin root access
  - Driver: `TypeRocket\Pro\Utility\Drives\RootDrive`
  - Config: `config/storage.php`

**Caching:**
- File-based cache - Default
  - Path: `TYPEROCKET_CACHE_FILE_FOLDER` env var
  - Config: `config/paths.php`

## Authentication & Identity

**Auth Provider:**
- WordPress User System - Native WordPress authentication
  - Implementation: Via `TypeRocket\Models\AuthUser`
  - Capabilities: WordPress role-based (`isCapable()`)
  - Session: WordPress cookies

**Authorization:**
- Policy-based - Custom policies in `app/Auth/*Policy.php`
  - Auto-discovered by `discoverPolicies()` in plugin init
  - Pattern: `{Model}Policy.php` maps to `{Model}.php`

## Monitoring & Observability

**Logging:**
- File Logger - Primary logging
  - Driver: `TypeRocket\Pro\Utility\Loggers\FileLogger`
  - Path: `TYPEROCKET_LOG_FILE_FOLDER` env var
  - Config: `config/logging.php`
- Slack Logger - Optional Slack notifications
  - Driver: `TypeRocket\Pro\Utility\Loggers\SlackLogger`
  - Webhook: `TYPEROCKET_LOG_SLACK_WEBHOOK_URL` env var
- Mail Logger - Email error notifications
  - Driver: `TypeRocket\Pro\Utility\Loggers\MailLogger`

**Error Tracking:**
- Whoops - Development error pages
  - Package: `filp/whoops 2.18.4`
  - Enabled: When `WP_DEBUG` is true
  - Config: `config/app.php` ErrorService

## Queue & Background Jobs

**Queue System:**
- WooCommerce Action Scheduler - Job processing
  - Driver: TypeRocket JobQueueRunner service
  - Config: `config/queue.php`
  - Retention: 30 days default
  - Install: `composer require woocommerce/action-scheduler`

## REST API

**Reflective REST Wrapper:**
- Zero-config CRUD endpoints
  - Base: `/tr-api/rest/{resource}/{id?}/actions/{action?}`
  - Features: Full-text search, field filtering, sorting, pagination
  - Model namespace: `\MakerMaker\Models\`
  - Config: `app/MakermakerTypeRocketPlugin.php:initReflectiveRestApi()`
  - Query modifier: User-based filtering via `setListQueryModifier()`

## Environment Configuration

**Development:**
- Required: WordPress installation, PHP 8.2+, Composer
- Env vars: Document via `.env.example` (not present)
- Debug: `WP_DEBUG=true` enables Whoops error pages

**Production:**
- Secrets: Via WordPress wp-config.php or server environment
- Debug: `WP_DEBUG=false` for production
- Logging: File or Slack based on config

---

*Integration audit: 2026-01-18*
*Update when adding/removing external services*
