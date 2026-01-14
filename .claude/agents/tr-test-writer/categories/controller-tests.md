# Controller Test Category

<purpose>
Test patterns for TypeRocket controller classes.
</purpose>

<unit_tests>
```php
describe('{Entity}Controller', function () {

    beforeEach(function () {
        $this->controller = new {Entity}Controller();
    });

    it('has index action', function () {
        expect(method_exists($this->controller, 'index'))->toBeTrue();
    });

    it('has create action', function () {
        expect(method_exists($this->controller, 'create'))->toBeTrue();
    });

    it('has store action', function () {
        expect(method_exists($this->controller, 'store'))->toBeTrue();
    });

    it('has edit action', function () {
        expect(method_exists($this->controller, 'edit'))->toBeTrue();
    });

    it('has update action', function () {
        expect(method_exists($this->controller, 'update'))->toBeTrue();
    });

    it('has destroy action', function () {
        expect(method_exists($this->controller, 'destroy'))->toBeTrue();
    });

});
```
</unit_tests>

<validation_tests>
```php
describe('store validation', function () {

    it('accepts valid data', function () {
        $data = ['name' => 'Test', 'type_id' => 1];
        expect(fn() => $this->controller->validateStore($data))
            ->not->toThrow(Exception::class);
    });

    it('rejects invalid data', function () {
        $data = ['name' => ''];
        expect(fn() => $this->controller->validateStore($data))
            ->toThrow(ValidationException::class);
    });

});
```
</validation_tests>

<authorization_tests>
```php
describe('authorization', function () {

    it('checks policy before store', function () {
        // Mock unauthorized user
        $auth = Mockery::mock(AuthUser::class);
        $auth->shouldReceive('isCapable')->andReturn(false);

        expect(fn() => $this->controller->store())
            ->toThrow(UnauthorizedException::class);
    });

});
```
</authorization_tests>
