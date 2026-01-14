# External Integrations

**Analysis Date:** 2026-01-07

## APIs & External Services

**Payment Processing:**
- Not integrated - No Stripe, PayPal, or Square detected

**Email/SMS:**
- Mailgun - Transactional emails (optional)
  - SDK/Client: TypeRocket MailgunMailDriver
  - Auth: `TYPEROCKET_MAILGUN_*` env vars (`config/mail.php`)
  - Configuration: API key, domain, region

- WordPress Native Mail - Default option
  - SDK/Client: WordPressMailDriver (`config/mail.php`)

**External APIs:**
- Not detected - No third-party API clients

## Data Storage

**Databases:**
- MySQL/MariaDB - Via WordPress wpdb
  - Connection: WordPress `wp-config.php`
  - Client: TypeRocket ORM (Eloquent-style)
  - Migrations: `database/migrations/*.sql` (18 files)
  - Table prefix: `srvc_` for all custom tables

**File Storage:**
- WordPress media library only
  - No external file storage (S3, etc.)

**Caching:**
- Not configured - No Redis or external cache

## Authentication & Identity

**Auth Provider:**
- WordPress native authentication
  - Implementation: TypeRocket AuthUser
  - Token storage: WordPress session cookies
  - Capability-based authorization via policies

**OAuth Integrations:**
- Not detected

## Monitoring & Observability

**Error Tracking:**
- Not configured - No Sentry or similar

**Logging:**
- Slack Integration - Optional (`config/logging.php`)
  - Webhook: `TYPEROCKET_LOG_SLACK_WEBHOOK_URL`
  - Uses SlackLogger class

- File Logging - Default (`config/logging.php`)
  - Uses FileLogger (daily rotation)
  - Path: WordPress uploads directory

**Analytics:**
- Not detected

## CI/CD & Deployment

**Hosting:**
- WordPress standard hosting
  - No specific platform detected

**CI Pipeline:**
- Not configured in plugin
  - Test scripts in `composer.json`

## Environment Configuration

**Development:**
- Required env vars: None plugin-specific (inherits WordPress)
- Secrets location: WordPress `wp-config.php`
- Local development: Standard LAMP/LEMP stack

**Staging:**
- Not documented

**Production:**
- Secrets management: WordPress configuration
- Database: Same as WordPress

## Webhooks & Callbacks

**Incoming:**
- Not detected

**Outgoing:**
- Slack logging (optional) - `config/logging.php`
  - Endpoint: Configured via env var
  - Verification: None (outgoing only)

## CDN & Static Assets

**Static CDN:**
- Bootstrap Icons - Admin UI (`app/MakermakerTypeRocketPlugin.php`)
  - URL: `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css`

**REST API:**
- ReflectiveRestWrapper - Zero-config REST
  - Endpoint: `/tr-api/rest/{resource}/{id?}/actions/{action?}`
  - Features: Full-text search, field filtering, sorting, pagination
  - Location: `app/MakermakerTypeRocketPlugin.php` (lines 153-170)

---

*Integration audit: 2026-01-07*
*Update when adding/removing external services*
