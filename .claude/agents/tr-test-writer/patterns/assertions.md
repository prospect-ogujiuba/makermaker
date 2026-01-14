# Assertion Patterns

<purpose>
Common Pest assertion patterns for TypeRocket tests.
</purpose>

<basic_assertions>
```php
expect($value)->toBe('exact');
expect($value)->toEqual('loose');
expect($value)->toBeTrue();
expect($value)->toBeFalse();
expect($value)->toBeNull();
expect($value)->not->toBeNull();
```
</basic_assertions>

<array_assertions>
```php
expect($array)->toContain('value');
expect($array)->toHaveCount(3);
expect($array)->toBeArray();
expect($array)->toHaveKey('name');
```
</array_assertions>

<instance_assertions>
```php
expect($object)->toBeInstanceOf(Equipment::class);
expect($relation)->toBeInstanceOf(BelongsTo::class);
```
</instance_assertions>

<exception_assertions>
```php
expect(fn() => $action())
    ->toThrow(ValidationException::class);

expect(fn() => $action())
    ->not->toThrow(Exception::class);
```
</exception_assertions>

<mock_assertions>
```php
$mock = Mockery::mock(AuthUser::class);
$mock->shouldReceive('isCapable')
    ->with('manage_services')
    ->andReturn(true);
```
</mock_assertions>
