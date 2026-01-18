# Codebase Concerns

**Analysis Date:** 2026-01-18

## Tech Debt

**Missing dependency lock files:**
- Issue: No `composer.lock` or `package-lock.json` committed
- Files: `composer.json`, `package.json` (both lockfiles in `.gitignore`)
- Why: Possibly intentional for flexibility, but creates reproducibility issues
- Impact: Non-reproducible builds, CI may get different versions, security vulnerabilities untracked
- Fix approach: Remove lock files from `.gitignore`, commit after `composer install` and `npm install`

**Core dependency on dev-master:**
- Issue: `mxcro/makermaker-core` pinned to `dev-master`
- File: `composer.json`
- Why: Rapid development of core library
- Impact: Breaking changes can silently appear, builds non-deterministic
- Fix approach: Tag stable releases, pin to specific version `^1.0` or `dev-master#hash`

**Old npm dependencies:**
- Issue: Laravel Mix v4, TypeScript 3.6, ts-loader v6 (all from 2019)
- File: `package.json`
- Why: Initial scaffolding, never upgraded
- Impact: Potential security vulnerabilities, missing features, build tool deprecation
- Fix approach: Upgrade to Laravel Mix 6+, TypeScript 5.x

## Known Bugs

**Test bootstrap references wrong file:**
- Symptoms: Integration tests may not load plugin correctly
- Trigger: Run integration tests requiring plugin
- File: `tests/bootstrap.php:12` - references `plugin.php` but actual file is `makermaker.php`
- Workaround: Tests still pass due to Brain Monkey mocking
- Root cause: Template copy/paste error
- Fix: Change `'/../plugin.php'` to `'/../makermaker.php'`

## Security Considerations

**Hardcoded security seed:**
- Risk: Static seed value shared across all deployments
- File: `config/app.php:74` - `'seed' => 'seed_5f85f2eedfdfb'`
- Current mitigation: None
- Recommendations: Move to environment variable `typerocket_env('TYPEROCKET_SEED')`

**CDN asset without SRI:**
- Risk: Supply chain attack via compromised CDN
- File: `app/MakermakerTypeRocketPlugin.php:136`
- Current mitigation: None (trusting jsdelivr CDN)
- Recommendations: Add integrity hash to `wp_enqueue_style()` call

**Missing .env.example:**
- Risk: Developers may hardcode secrets or miss required configuration
- Current mitigation: Config files document env vars in comments
- Recommendations: Create `.env.example` with all `typerocket_env()` variables

## Performance Bottlenecks

No significant performance issues detected. Codebase is minimal scaffolding.

**Potential concern - Policy discovery on every request:**
- File: `app/MakermakerTypeRocketPlugin.php:75-102`
- Problem: `glob()` and `class_exists()` calls on each request
- Measurement: Not measured, likely <1ms with few policies
- Cause: Dynamic discovery pattern
- Improvement path: Cache discovered policies in transient or static property

## Fragile Areas

**Policy auto-discovery:**
- File: `app/MakermakerTypeRocketPlugin.php:75-102`
- Why fragile: Relies on naming conventions (`{Model}Policy.php` ↔ `{Model}.php`)
- Common failures: Typos in filenames, missing model, orphaned policy
- Safe modification: Add validation logging for skipped policies
- Test coverage: None

**Dynamic resource loading:**
- File: `app/MakermakerTypeRocketPlugin.php:54-73`
- Why fragile: `glob()` returns `false` on error, not empty array
- Common failures: Permission issues, broken symlinks
- Safe modification: Add explicit `!== false` check
- Test coverage: None

## Scaling Limits

Not applicable at current development stage.

## Dependencies at Risk

**mxcro/makermaker-core:**
- Risk: Internal dev-master dependency, no public releases
- Impact: Breaking changes, versioning issues
- Migration plan: Tag stable releases, use semantic versioning

**laravel-mix v4:**
- Risk: Deprecated, v6 is current
- Impact: Build issues on newer Node.js versions
- Migration plan: Upgrade to Laravel Mix 6 or switch to Vite

## Missing Critical Features

**No actual tests:**
- Problem: Only trivial placeholder tests exist (`expect(true)->toBeTrue()`)
- Files: All `tests/*/*.php` files contain single placeholder test
- Current workaround: None
- Blocks: CI/CD pipeline provides false confidence
- Implementation: Add tests for plugin init, policy discovery, REST wrapper

**No .env.example:**
- Problem: Required environment variables undocumented
- Current workaround: Read config files to discover env vars
- Blocks: Developer onboarding, deployment documentation
- Implementation: Create `.env.example` from config file analysis

## Test Coverage Gaps

**Plugin initialization:**
- What's not tested: `MakermakerTypeRocketPlugin::init()` and all private methods
- Risk: Silent failures during plugin activation
- Priority: High
- Difficulty: Requires Brain Monkey setup for WordPress hooks

**Policy discovery:**
- What's not tested: `discoverPolicies()` with various directory states
- Risk: Policies not registered, authorization failures
- Priority: Medium
- Difficulty: File system mocking needed

**REST API wrapper:**
- What's not tested: `initReflectiveRestApi()` and query modifier
- Risk: REST endpoints return wrong data or fail authorization
- Priority: High
- Difficulty: Requires integration test with WordPress

**Asset registration:**
- What's not tested: `registerAssets()` with missing manifest
- Risk: Assets not loaded, broken admin UI
- Priority: Low
- Difficulty: Requires mocking `file_get_contents`

---

*Concerns audit: 2026-01-18*
*Update as issues are fixed or new ones discovered*
