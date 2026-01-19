# Codebase Concerns

**Analysis Date:** 2026-01-19

## Tech Debt

**Empty Application Layer:**
- Issue: No models, controllers, policies, or migrations exist. Empty scaffold directories.
- Files: `app/Models/`, `app/Controllers/`, `app/Auth/`, `database/migrations/`
- Impact: Plugin provides no domain functionality; purely scaffolding
- Fix approach: Implement domain models, controllers, and migrations using Galaxy CLI

**Unused Route Imports:**
- Issue: Route files import controllers that don't exist
- Files: `inc/routes/api.php` (imports `MakerMaker\Controllers\Api\V1\ServiceController`), `inc/routes/public.php` (imports `MakerMaker\Controllers\Web\ServiceController`)
- Impact: Potential autoload errors if routes are actually defined
- Fix approach: Either create referenced controllers or remove imports

**Bootstrap References Wrong Plugin File:**
- Issue: Test bootstrap tries to load `plugin.php` but main file is `makermaker.php`
- Files: `tests/bootstrap.php:12`
- Impact: Plugin may not load correctly during integration tests
- Fix approach: Change `plugin.php` to `makermaker.php` in bootstrap

**Empty Resources Directory:**
- Issue: `inc/resources/` directory exists but is empty; loadResources() scans it
- Files: `app/MakermakerTypeRocketPlugin.php:54-73`, `inc/resources/`
- Impact: Unnecessary filesystem operations on every request
- Fix approach: Add resources or remove directory and loader code

**Missing Storage Directory:**
- Issue: Config references storage paths that don't exist
- Files: `config/paths.php:21` references `storage/`, `config/paths.php:31` references `storage/logs`, `config/paths.php:41` references `storage/cache`
- Impact: File logging/caching will fail without these directories
- Fix approach: Create `storage/`, `storage/logs/`, `storage/cache/` directories with appropriate permissions

## Known Bugs

**No functional bugs detected** - codebase is minimal scaffold with no business logic.

## Security Considerations

**No Input Sanitization/Escaping Visible:**
- Risk: Plugin code contains no explicit calls to `esc_*`, `sanitize_*`, or `wp_nonce` functions
- Files: All PHP files in plugin (excluding vendor)
- Current mitigation: TypeRocket framework likely handles this internally
- Recommendations: When adding custom code, explicitly use WordPress sanitization/escaping functions

**CDN Bootstrap Icons:**
- Risk: External CDN dependency loads from `cdn.jsdelivr.net`
- Files: `app/MakermakerTypeRocketPlugin.php:136`
- Current mitigation: None
- Recommendations: Consider self-hosting Bootstrap Icons or using Subresource Integrity (SRI)

**Static Security Seed:**
- Risk: Hardcoded seed value in config
- Files: `config/app.php:74` - `'seed' => 'seed_5f85f2eedfdfb'`
- Current mitigation: None
- Recommendations: Move to environment variable for production

## Performance Bottlenecks

**No significant performance concerns** - codebase is minimal scaffold.

**Potential Future Concern - ReflectiveRestWrapper:**
- Problem: Automatic REST API for all models via reflection
- Files: `app/MakermakerTypeRocketPlugin.php:153-170`
- Cause: Reflection and dynamic routing can be slower than explicit routes
- Improvement path: Monitor performance as models are added; consider explicit routes for high-traffic endpoints

## Fragile Areas

**TypeRocket Core Dependency:**
- Files: `makermaker.php:54` - hooks into `typerocket_loaded` (priority 9)
- Why fragile: Plugin entirely depends on TypeRocket Pro being loaded first; no fallback
- Safe modification: Test with TypeRocket updates; maintain compatible versions
- Test coverage: None

**MakermakerCore External Dependency:**
- Files: `composer.json:8` - `"mxcro/makermaker-core": "dev-master"`
- Why fragile: Depends on `dev-master` branch which can change unexpectedly
- Safe modification: Pin to specific version or commit hash for production
- Test coverage: None

## Scaling Limits

**No scaling concerns** - plugin is scaffold without data storage patterns.

## Dependencies at Risk

**dev-master Core Dependency:**
- Risk: `mxcro/makermaker-core` pinned to `dev-master` - unstable
- Impact: Breaking changes can occur without warning
- Migration plan: Pin to stable version tag when available

**TypeRocket Pro License:**
- Risk: Commercial dependency on TypeRocket Pro
- Impact: License renewal required; vendor lock-in
- Migration plan: None - tightly coupled to TypeRocket architecture

## Missing Critical Features

**No Domain Logic:**
- Problem: No models, migrations, controllers, or business logic
- Blocks: All application functionality

**No Real Tests:**
- Problem: Only placeholder tests exist (`expect(true)->toBeTrue()`)
- Files: `tests/Unit/BasicUnitTest.php`, `tests/Feature/BasicFeatureTest.php`, `tests/Integration/BasicIntegrationTest.php`, `tests/Acceptance/BasicAcceptanceTest.php`
- Blocks: Test coverage requirements (85% CI target will fail)

**No Storage Directories:**
- Problem: Required directories for logging and caching don't exist
- Blocks: File logging, caching functionality

## Test Coverage Gaps

**100% Coverage Gap:**
- What's not tested: Entire application - only placeholder tests
- Files: All `app/`, `config/`, `inc/` files
- Risk: Any added functionality has zero test safety net
- Priority: High - blocks CI pipeline (85% coverage requirement)

**MakermakerTypeRocketPlugin Class:**
- What's not tested: Plugin initialization, activation, deactivation, routes, policies
- Files: `app/MakermakerTypeRocketPlugin.php`
- Risk: Plugin lifecycle issues undetected
- Priority: High

---

*Concerns audit: 2026-01-19*
