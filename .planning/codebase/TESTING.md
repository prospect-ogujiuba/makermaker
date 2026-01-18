# Testing Patterns

**Analysis Date:** 2026-01-18

## Test Framework

**Runner:**
- Pest v2.36.0 (expressive test framework)
- PHPUnit 10.5.36 (underlying engine)
- Config: `phpunit.xml` in project root

**Assertion Library:**
- Pest built-in expectations: `expect($value)->toBe()`
- PHPUnit assertions available: `$this->assertTrue()`

**Run Commands:**
```bash
composer test                    # Run all tests
composer test:unit               # Unit tests only (--group=unit)
composer test:quick              # Unit + smoke tests with --testdox
composer test:ci                 # All tests with 85% coverage minimum
composer test:all                # All suites: Unit,Integration,Feature,Acceptance
composer test:affected           # Exclude slow/quarantine groups
```

## Test File Organization

**Location:**
- `tests/Unit/` - Isolated unit tests
- `tests/Integration/` - Module integration tests
- `tests/Feature/` - Business logic tests
- `tests/Acceptance/` - End-to-end tests

**Naming:**
- `*Test.php` suffix for all test files
- PascalCase matching tested class: `ServiceTest.php`
- Group by functionality: `BasicUnitTest.php`

**Structure:**
```
tests/
├── Unit/
│   └── BasicUnitTest.php
├── Integration/
│   └── BasicIntegrationTest.php
├── Feature/
│   └── BasicFeatureTest.php
├── Acceptance/
│   └── BasicAcceptanceTest.php
├── Pest.php              # Global config and helpers
└── bootstrap.php         # Test environment setup
```

## Test Structure

**Suite Organization:**
```php
<?php

it('describes expected behavior', function () {
    // arrange
    $input = createTestData();

    // act
    $result = performAction($input);

    // assert
    expect($result)->toBe($expected);
});

it('handles error case', function () {
    expect(fn() => failingAction())->toThrow(Exception::class);
});
```

**Patterns:**
- Use `beforeEach` for per-test setup (configured globally in `Pest.php`)
- Use `afterEach` for cleanup (Brain Monkey teardown)
- Arrange/Act/Assert pattern encouraged
- One assertion focus per test

## Mocking

**Framework:**
- Brain Monkey 2.6.2 - WordPress function mocking
- Mockery 1.6.12 - Object mocking
- Integration via `MockeryPHPUnitIntegration` trait

**Patterns:**
```php
use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery;

// Mock WordPress function
Functions\expect('get_option')
    ->once()
    ->with('my_option')
    ->andReturn('value');

// Mock object
$mock = Mockery::mock(Service::class);
$mock->shouldReceive('method')->andReturn('result');
```

**What to Mock:**
- WordPress functions (via Brain Monkey)
- External API calls
- Database interactions in unit tests
- File system operations

**What NOT to Mock:**
- Pure functions without side effects
- Simple data transformations
- The class under test

## Fixtures and Factories

**Test Data:**
```php
// Factory function pattern
function createTestService(array $overrides = []): array
{
    return array_merge([
        'id' => 1,
        'name' => 'Test Service',
        'price' => 99.99,
        'active' => true,
    ], $overrides);
}

// Usage
it('calculates total correctly', function () {
    $service = createTestService(['price' => 50.00]);
    expect($service['price'])->toBe(50.00);
});
```

**Location:**
- Factory functions: Define in test file or `tests/Factories/`
- Fixtures: `tests/Fixtures/` for static data files

## Coverage

**Requirements:**
- CI minimum: 85% line coverage
- Command: `composer test:ci` runs `pest --coverage --min=85`

**Configuration:**
- Coverage source: `./app` directory only
- Excludes: test files, config files, vendor

**View Coverage:**
```bash
composer test:ci                  # Run with coverage check
# HTML report: coverage/index.html (if configured)
```

## Test Types

**Unit Tests:**
- Scope: Single function/class in isolation
- Mocking: All external dependencies
- Speed: Fast (<100ms each)
- Directory: `tests/Unit/`

**Integration Tests:**
- Scope: Multiple modules together
- Mocking: Only external services
- Setup: May require WordPress environment
- Directory: `tests/Integration/`

**Feature Tests:**
- Scope: Business logic flows
- Mocking: External boundaries only
- Setup: Plugin loaded, may use test database
- Directory: `tests/Feature/`

**Acceptance Tests:**
- Scope: Full user flows
- Mocking: None (real system)
- Setup: Full WordPress environment
- Directory: `tests/Acceptance/`

## Common Patterns

**Async Testing:**
```php
it('handles async operation', function () {
    // Not applicable - PHP is synchronous
    // Use promises/generators if needed
});
```

**Error Testing:**
```php
it('throws on invalid input', function () {
    expect(fn() => validateInput(null))->toThrow(InvalidArgumentException::class);
});

it('throws with specific message', function () {
    expect(fn() => process([]))->toThrow('Input cannot be empty');
});
```

**WordPress Function Mocking:**
```php
use Brain\Monkey\Functions;

it('uses WordPress option', function () {
    Functions\expect('get_option')
        ->once()
        ->with('my_setting', 'default')
        ->andReturn('custom_value');

    $result = getMyOption();

    expect($result)->toBe('custom_value');
});
```

**Custom Expectations:**
```php
// Defined in tests/Pest.php
expect($value)->toCallWordPressFunction('add_action');
expect($value)->toHaveWordPressAction('init');
expect($value)->toHaveWordPressFilter('the_content');
```

## Test Bootstrap

**Setup File:** `tests/bootstrap.php`
```php
// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load WordPress (for integration tests)
if (file_exists(__DIR__ . '/../../../../wp-config.php')) {
    require_once __DIR__ . '/../../../../wp-config.php';
}

// Initialize Brain Monkey
Brain\Monkey\setUp();

// Define test constant
define('MAKERMAKER_PLUGIN_TESTS', true);
```

**Pest Config:** `tests/Pest.php`
- Applies MockeryPHPUnitIntegration to all test directories
- Sets up Brain Monkey before/after each test
- Auto-assigns PHPUnit groups by directory
- Defines custom expect() extensions

---

*Testing analysis: 2026-01-18*
*Update when test patterns change*
