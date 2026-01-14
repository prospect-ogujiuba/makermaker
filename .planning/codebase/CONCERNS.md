# Codebase Concerns

**Analysis Date:** 2026-01-07

## Tech Debt

**God-Class Helper:**
- Issue: `app/Helpers/ServiceCatalogHelper.php` (2183 lines) handles multiple unrelated concerns
- Why: Rapid development without refactoring
- Impact: Hard to test, maintain, and understand; high cognitive load
- Fix approach: Split into focused helpers: `PricingHelper`, `EquipmentHelper`, `DeliveryHelper`, `BundleHelper`

**Large Controller:**
- Issue: `app/Controllers/ContactSubmissionController.php` (1125 lines) with 15+ methods
- Why: Single controller handling complex workflow
- Impact: Methods exceed 150 lines, hard to navigate
- Fix approach: Extract workflow methods to service classes

**Outdated Frontend Dependencies:**
- Issue: `package.json` dependencies from 2019
- Files: `laravel-mix@4.0.7`, `typescript@3.6.4`, `sass-loader@7.1.0`, `ts-loader@6.2.0`
- Why: Not prioritized during development
- Impact: Security vulnerabilities, deprecated APIs
- Fix approach: Update all devDependencies to current versions

**dev-master Dependency:**
- Issue: `composer.json` uses `"mxcro/makermaker-core": "dev-master"`
- Why: Internal package, no versioning setup
- Impact: Non-deterministic builds
- Fix approach: Tag stable versions, pin to specific version

## Known Bugs

**Debug Logging in Production:**
- Symptoms: `error_log()` scattered throughout controllers
- Trigger: Any controller action
- Files: `app/Controllers/ContactSubmissionController.php` (12+ locations)
- Workaround: None (logs written to PHP error log)
- Root cause: Debug code not removed
- Fix: Replace with structured logging via TypeRocket

## Security Considerations

**Unvalidated Query Parameters:**
- Risk: Direct `$_GET` access without sanitization before database queries
- Files: `app/Controllers/ContactSubmissionController.php` (lines 169-174, 903-907)
- Current mitigation: TypeRocket ORM parameter binding
- Recommendations: Add explicit validation/sanitization via `intval()`, `sanitize_text_field()`

**Unvalidated POST Parameters:**
- Risk: Direct `$_POST` access without validation
- Files: `app/Controllers/ContactSubmissionController.php` (lines 370, 959-960)
- Current mitigation: None
- Recommendations: Validate all POST data through TypeRocket Fields or WordPress sanitization functions

**Missing Rate Limiting:**
- Risk: Comment says "3 per hour" but code has different limit
- File: `app/Controllers/ContactSubmissionController.php` (line 625)
- Current mitigation: Some rate limiting in place
- Recommendations: Centralize rate limit configuration, add consistent enforcement

## Performance Bottlenecks

**Aggressive Eager Loading:**
- Problem: `$with` loads 10+ relationships by default
- File: `app/Models/Service.php` (lines 62-74)
- Measurement: Every query loads all relationships even for index views
- Cause: Convenience over performance
- Improvement path: Remove default `$with`, selectively load in controllers

**Missing Pagination:**
- Problem: REST endpoints return all results without pagination
- Files: `app/Controllers/*Controller.php` (indexRest methods)
- Measurement: Large tables will return unbounded result sets
- Cause: Not implemented during initial development
- Improvement path: Add limit/offset parameters to REST queries

## Fragile Areas

**Large Helper Class:**
- File: `app/Helpers/ServiceCatalogHelper.php`
- Why fragile: 70+ methods with interrelated logic, no tests
- Common failures: Changes to one method affect others unexpectedly
- Safe modification: Write tests first, then refactor
- Test coverage: No unit tests currently

**Contact Submission Workflow:**
- File: `app/Controllers/ContactSubmissionController.php`
- Why fragile: Complex state machine, many conditional paths
- Common failures: Status transitions, bulk operations
- Safe modification: Add integration tests before changes
- Test coverage: Minimal

## Scaling Limits

**No Identified Hard Limits:**
- Plugin follows standard WordPress/TypeRocket patterns
- Database queries use standard ORM
- No obvious bottlenecks beyond eager loading issue

## Dependencies at Risk

**Frontend Build Chain:**
- Risk: Laravel Mix 4.x is outdated (current is v6.x)
- File: `package.json`
- Impact: Webpack vulnerabilities, deprecated plugins
- Migration plan: Update to Laravel Mix 6.x or switch to Vite

**TypeScript Version:**
- Risk: TypeScript 3.6.4 (current is 5.x)
- File: `package.json`
- Impact: Missing modern TS features, potential compatibility issues
- Migration plan: Update to TypeScript 5.x with loader updates

## Missing Critical Features

**README Documentation:**
- Problem: No `README.md` for project overview
- Current workaround: `CLAUDE.md` provides architecture info
- Blocks: New developer onboarding
- Implementation complexity: Low

**Test Coverage for Helpers:**
- Problem: `ServiceCatalogHelper.php` (2183 lines) has no tests
- Current workaround: Manual testing
- Blocks: Confident refactoring
- Implementation complexity: Medium-High (many methods to test)

**Structured Logging:**
- Problem: Using `error_log()` instead of proper logging
- Current workaround: Check PHP error log
- Blocks: Log aggregation, filtering, monitoring
- Implementation complexity: Low (TypeRocket logging exists)

## Test Coverage Gaps

**ServiceCatalogHelper:**
- What's not tested: 70+ utility methods for pricing, equipment, delivery
- Files: `app/Helpers/ServiceCatalogHelper.php`
- Risk: Core business logic changes break silently
- Priority: High
- Difficulty to test: Medium (many static methods)

**Controller Authorization:**
- What's not tested: Policy checks in controller methods
- Files: `app/Controllers/*.php`
- Risk: Authorization bypass
- Priority: High
- Difficulty to test: Medium (need mock AuthUser)

**REST API Endpoints:**
- What's not tested: Full request/response cycle
- Files: `app/Controllers/*Controller.php` (indexRest, showRest methods)
- Risk: API breaks without detection
- Priority: Medium
- Difficulty to test: Low-Medium (REST test utilities exist)

**Pricing Calculations:**
- What's not tested: Complex pricing logic
- File: `app/Helpers/ServiceCatalogHelper.php`
- Risk: Financial calculation errors
- Priority: High
- Difficulty to test: Medium

---

*Concerns audit: 2026-01-07*
*Update as issues are fixed or new ones discovered*
