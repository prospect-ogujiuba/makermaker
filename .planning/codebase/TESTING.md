# Testing Patterns

**Analysis Date:** 2026-01-07

## Test Framework

**Runner:**
- Pest 2.34+ (`composer.json`)
- Config: `phpunit.xml` in project root

**Assertion Library:**
- Pest built-in `expect()` fluent API
- PHPUnit assertions also available
- Custom expectations via Brain Monkey

**Run Commands:**
```bash
composer test              # Run all tests with Pest
composer test:unit         # Unit tests only (@group unit)
composer test:quick        # Unit + smoke tests with --testdox
composer test:ci           # With coverage (minimum 85%)
composer test:all          # All test suites explicitly
composer test:affected     # Exclude slow,quarantine groups
```

## Test File Organization

**Location:**
- All tests in `tests/` directory
- Organized by test type (Unit, Integration, Feature, Acceptance)
- Factories in `tests/Factories/`

**Naming:**
- `*Test.php` suffix for test classes
- Descriptive names: `BasicUnitTest.php`, `ReflectiveRestApiTest.php`
- Auto-grouped by directory location

**Structure:**
```
tests/
├── Pest.php                # Global setup, custom expectations
├── bootstrap.php           # WordPress/DB initialization
├── Factories/
│   └── ServiceFactory.php  # Test data factories
├── Unit/
│   └── BasicUnitTest.php
├── Integration/
│   ├── BasicIntegrationTest.php
│   ├── ReflectiveRestApiTest.php
│   └── ServiceStackTest.php
├── Feature/
│   └── BasicFeatureTest.php
└── Acceptance/
    └── BasicAcceptanceTest.php
```

## Test Structure

**Suite Organization:**
```php
// Pest syntax (tests/Unit/BasicUnitTest.php)
it('works', function () {
    expect(true)->toBeTrue();
});

// PHPUnit compatibility (tests/Integration/ReflectiveRestApiTest.php)
class ReflectiveRestApiTest extends \PHPUnit\Framework\TestCase {
    public function test_something() {
        expect($result)->toBeInstanceOf(Service::class);
    }
}
```

**Patterns:**
- Brain Monkey setup/teardown in `tests/Pest.php`
- Database transactions for integration tests
- Factory pattern for test data creation
- Reflection testing for internal method verification

## Mocking

**Framework:**
- Brain Monkey 2.6 - WordPress function mocking
- Mockery 1.6 - Object mocking

**Patterns:**
```php
// WordPress function mocking (tests/Pest.php)
uses()
    ->beforeEach(function () {
        Monkey\setUp();
    })
    ->afterEach(function () {
        Monkey\tearDown();
        Mockery::close();
    })
    ->in('Unit', 'Feature', 'Acceptance');

// Object mocking (tests/Integration/ReflectiveRestApiTest.php)
$request = $this->createMock(Request::class);
$request->method('getQuery')->willReturn(null);
```

**What to Mock:**
- WordPress functions (via Brain Monkey)
- External service calls
- Database for unit tests

**What NOT to Mock:**
- Models in integration tests
- Pure utility functions
- Internal class dependencies (use real implementations)

## Fixtures and Factories

**Test Data:**
```php
// Factory pattern (tests/Factories/ServiceFactory.php)
class ServiceFactory {
    public static function create(array $overrides = []): Service {
        // Create prerequisites
        // Merge defaults with overrides
        // Save and return
    }

    public static function createScenario(string $scenario, array $overrides = []) {
        // Create specific test scenarios
    }

    public static function createMany(int $count, array $overrides = []): array {
        // Bulk creation
    }
}
```

**Location:**
- Factory classes: `tests/Factories/`
- Inline fixtures for simple cases

## Coverage

**Requirements:**
- Minimum: 85% (enforced by `composer test:ci`)
- Source includes: `app/` directory only

**Configuration:**
- Configured in `phpunit.xml`
- Excludes: test files, vendor

**View Coverage:**
```bash
composer test:ci
# Coverage report generated
```

## Test Types

**Unit Tests:**
- Scope: Single function/class in isolation
- Mocking: All WordPress functions via Brain Monkey
- Location: `tests/Unit/`
- Speed: Fast (<100ms per test)

**Integration Tests:**
- Scope: Multiple components with database
- Mocking: External boundaries only
- Location: `tests/Integration/`
- Isolation: Database transactions (START TRANSACTION / ROLLBACK)

**Feature Tests:**
- Scope: Full feature behavior
- Mocking: Minimal (real implementations)
- Location: `tests/Feature/`

**Acceptance Tests:**
- Scope: End-to-end user flows
- Location: `tests/Acceptance/`
- Status: Basic structure in place

## Common Patterns

**Async Testing:**
```php
it('should handle async operation', function () {
    $result = asyncFunction();
    expect($result)->toBe('expected');
});
```

**Error Testing:**
```php
it('should throw on invalid input', function () {
    expect(fn() => invalidCall())->toThrow(\InvalidArgumentException::class);
});
```

**Database Testing:**
```php
// Transaction-based isolation (tests/Pest.php)
uses()
    ->beforeEach(function () {
        global $wpdb;
        $wpdb->query('START TRANSACTION');
    })
    ->afterEach(function () {
        global $wpdb;
        $wpdb->query('ROLLBACK');
    })
    ->in('Integration');
```

**Custom Expectations:**
```php
// tests/Pest.php
expect()->extend('toCallWordPressFunction', function (string $function) {
    Brain\Monkey\Functions\expect($function);
    return $this;
});

expect()->extend('toHaveWordPressAction', function (string $action) {
    Brain\Monkey\Actions\expectAdded($action);
    return $this;
});
```

**Snapshot Testing:**
- Not currently used

## Database Testing

**Migration Execution:**
- Runs once per test session (not per-test)
- Parses SQL files in chronological order
- Replaces `{!!prefix!!}` with WordPress table prefix
- Handled in `tests/bootstrap.php`

**Test Database:**
- Same database as WordPress (uses transactions for isolation)
- Environment variables: `TEST_DB_NAME`, `TEST_DB_USER`, `TEST_DB_PASSWORD`, `TEST_DB_HOST`
- Defaults to local development database

---

*Testing analysis: 2026-01-07*
*Update when test patterns change*
