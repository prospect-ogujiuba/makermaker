# External Integrations

**Analysis Date:** 2026-01-19

## Database

**Primary:** WordPress Database via TypeRocket ORM

**Connection:**
- Driver: `TypeRocket\Database\Connectors\WordPressCoreDatabaseConnector`
- Uses WordPress `$wpdb` global
- Table prefix: `GLOBAL_WPDB_PREFIX` constant

**Configuration:** `config/database.php`
```php
'default' => typerocket_env('TYPEROCKET_DATABASE_DEFAULT', 'wp'),
'drivers' => [
    'wp' => [
        'driver' => '\TypeRocket\Database\Connectors\WordPressCoreDatabaseConnector',
    ],
]
```

**Alternate Connection (optional):**
```php
'alt' => [
    'driver' => '\TypeRocket\Database\Connectors\CoreDatabaseConnector',
    'username' => typerocket_env('TYPEROCKET_ALT_DATABASE_USER'),
    'password' => typerocket_env('TYPEROCKET_ALT_DATABASE_PASSWORD'),
    'database' => typerocket_env('TYPEROCKET_ALT_DATABASE_DATABASE'),
    'host' => typerocket_env('TYPEROCKET_ALT_DATABASE_HOST'),
]
```

## REST API

**TypeRocket REST API:**
- Base: `/tr-api/rest/{resource}/{id?}/actions/{action?}`
- Authentication: WordPress nonce + capabilities
- Format: JSON

**ReflectiveRestWrapper Enhancements:**
- Zero-config for all custom resources
- Full-text search: `?search=term`
- Field filtering: `?field=value`
- Sorting: `?orderby=field&order=asc|desc`
- Pagination: `?page=1&per_page=10`
- Custom actions: `/actions/{action}`

**Configuration:** `app/MakermakerTypeRocketPlugin.php:153-170`
```php
ReflectiveRestWrapper::setModelNamespace('\\MakerMaker\\Models\\');
ReflectiveRestWrapper::setListQueryModifier(function ($model, $resource, $user) {
    // Custom filtering per resource
});
ReflectiveRestWrapper::init();
```

## Mail

**Configuration:** `config/mail.php`

**Drivers:**
- `wp` - WordPress `wp_mail()` (default)
- `log` - File logging (debug)
- `mailgun` - Mailgun API

**Environment Variables:**
```
TYPEROCKET_MAIL_DEFAULT=wp
TYPEROCKET_MAILGUN_SECRET=
TYPEROCKET_MAILGUN_DOMAIN=
TYPEROCKET_MAILGUN_ENDPOINT=api.mailgun.net
```

## Logging

**Configuration:** `config/logging.php`

**Channels:**
- `file` - File logs in `storage/logs/`
- `slack` - Slack webhook
- `mail` - Email alerts
- `stack` - Multiple channels (default)

**Environment Variables:**
```
TYPEROCKET_LOG_DEFAULT=stack
TYPEROCKET_LOG_SLACK_WEBHOOK_URL=
TYPEROCKET_LOG_CHANNEL=
```

## Queue/Jobs

**Configuration:** `config/queue.php`

**Driver:** WordPress Action Scheduler
- Default queue: `typerocket`
- Single/recurring job support

## Storage

**Configuration:** `config/storage.php`

**Drivers:**
- `local` - Local filesystem (default)
- `s3` - Amazon S3 (not configured)

**Paths:**
```
storage/           # Base storage
storage/logs/      # Log files
storage/cache/     # Cache files
```

## External APIs

**Google Maps (Optional):**
- Configuration: `config/external.php`
- Environment: `TYPEROCKET_GOOGLE_MAPS_API_KEY`

**Bootstrap Icons CDN:**
- URL: `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css`
- Location: `app/MakermakerTypeRocketPlugin.php:136`

## WordPress Integration

**Hooks:**
- `typerocket_loaded` (priority 9) - Plugin initialization
- `parse_request` (priority 5) - REST API interception
- `wp_enqueue_scripts` - Frontend assets
- `admin_enqueue_scripts` - Admin assets
- `register_activation_hook` - Plugin activation
- `delete_plugin` - Plugin deletion

**Capabilities:**
- Custom capabilities per resource: `manage_{resource}`
- WordPress user roles integration via `AuthUser::isCapable()`

**Admin Pages:**
- Plugin settings page via `BasePlugin::pluginSettingsPage()`
- Custom resource pages via TypeRocket Registry

## Authentication

**Method:** WordPress Capabilities

**Implementation:**
- Policies check `AuthUser::isCapable('{capability}')`
- REST API validates via WordPress nonce
- Admin pages use standard WordPress capability checks

**Custom Query Filtering:**
- `ReflectiveRestWrapper::setListQueryModifier()` for user-based data filtering
- Example: Non-admins only see their own submissions

---

*Integrations analysis: 2026-01-19*
