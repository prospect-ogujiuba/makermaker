# Test Fixtures Pattern

<purpose>
Test data setup and teardown for integration tests.
</purpose>

<database_fixtures>
```php
beforeEach(function () {
    $this->equipment = Equipment::create([
        'name' => 'Test Equipment',
        'sku' => 'TEST-001',
        'equipment_type_id' => 1,
    ]);
});

afterEach(function () {
    Equipment::where('sku', 'TEST-001')->delete();
});
```
</database_fixtures>

<mock_wordpress>
```php
beforeEach(function () {
    if (!function_exists('current_user_can')) {
        function current_user_can($cap) {
            return true;
        }
    }

    if (!function_exists('get_current_user_id')) {
        function get_current_user_id() {
            return 1;
        }
    }
});
```
</mock_wordpress>

<mock_authuser>
```php
$auth = Mockery::mock(AuthUser::class);
$auth->shouldReceive('isCapable')
    ->with('manage_services')
    ->andReturn(true);
$auth->ID = 1;
```
</mock_authuser>

<cleanup>
Always clean up test data in afterEach to prevent state leakage.
</cleanup>
