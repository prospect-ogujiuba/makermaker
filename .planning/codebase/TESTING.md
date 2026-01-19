# Testing

**Analysis Date:** 2026-01-19

## Framework

**Primary:** Pest 2.34 (built on PHPUnit 10.5)

**Mocking:**
- Brain\Monkey 2.6 - WordPress function mocking
- Mockery 1.6 - PHP mocking

## Configuration

**phpunit.xml:**
- Bootstrap: `tests/bootstrap.php`
- Coverage source: `./app`
- Test suites: Unit, Integration, Feature, Acceptance

**tests/Pest.php:**
- MockeryPHPUnitIntegration for all suites
- Brain\Monkey setUp/tearDown for all suites
- Auto-assign PHPUnit groups based on directory
- Custom expectations for WordPress functions

## Test Structure

```
tests/
├── Acceptance/              # End-to-end tests
│   └── BasicAcceptanceTest.php
├── Feature/                 # Feature tests
│   └── BasicFeatureTest.php
├── Integration/             # Integration tests
│   └── BasicIntegrationTest.php
├── Unit/                    # Unit tests
│   └── BasicUnitTest.php
├── Pest.php                 # Pest configuration
└── bootstrap.php            # Test bootstrap
```

## Running Tests

```bash
# All tests
composer test

# Unit tests only
composer test:unit

# With coverage (85% requirement)
composer test:ci

# Via Pest directly
./vendor/bin/pest

# Specific suite
./vendor/bin/pest --group=unit
./vendor/bin/pest --group=integration
./vendor/bin/pest --group=feature
./vendor/bin/pest --group=acceptance
```

## Mocking Patterns

**WordPress Functions (Brain\Monkey):**
```php
use Brain\Monkey\Functions;

it('calls add_action', function () {
    Functions\expect('add_action')
        ->once()
        ->with('init', \Mockery::type('callable'));

    // Code that calls add_action
});
```

**WordPress Actions:**
```php
use Brain\Monkey\Actions;

it('adds the init action', function () {
    Actions\expectAdded('init')->once();

    // Code that adds action
});
```

**WordPress Filters:**
```php
use Brain\Monkey\Filters;

it('adds the content filter', function () {
    Filters\expectAdded('the_content')->once();

    // Code that adds filter
});
```

**Custom Pest Expectations (from tests/Pest.php):**
```php
expect($subject)->toCallWordPressFunction('add_action');
expect($subject)->toHaveWordPressAction('init');
expect($subject)->toHaveWordPressFilter('the_content');
```

## Coverage

**Target:** 85% (CI requirement)

**Coverage Source:** `./app` directory

**Run with coverage:**
```bash
composer test:ci
```

## Test Examples

**Unit Test (Pure PHP):**
```php
<?php

it('formats price correctly', function () {
    $product = new Product(['price' => 1999]);

    expect($product->formattedPrice())->toBe('$19.99');
});
```

**Integration Test (With WordPress Mocks):**
```php
<?php

use Brain\Monkey\Functions;

it('saves product to database', function () {
    Functions\when('current_user_can')->justReturn(true);

    $product = new Product(['title' => 'Test']);
    $product->save();

    expect($product->id)->not->toBeNull();
});
```

**Feature Test (Full Request):**
```php
<?php

use Brain\Monkey\Functions;

it('creates product via controller', function () {
    Functions\when('current_user_can')->justReturn(true);
    Functions\when('wp_redirect')->justReturn(true);

    $controller = new ProductController();
    $response = $controller->create(/* mock fields */);

    expect($response)->toBeRedirect();
});
```

## Current State

**Note:** Current tests are placeholders only:
```php
it('works', function () {
    expect(true)->toBeTrue();
});
```

Real tests need to be implemented for actual coverage.

---

*Testing analysis: 2026-01-19*
