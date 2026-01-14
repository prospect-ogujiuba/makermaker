# Policy Test Category

<purpose>
Test patterns for TypeRocket policy classes.
</purpose>

<capability_tests>
```php
describe('{Entity}Policy', function () {

    beforeEach(function () {
        $this->policy = new {Entity}Policy();
    });

    describe('create permission', function () {

        it('allows users with capability', function () {
            $auth = Mockery::mock(AuthUser::class);
            $auth->shouldReceive('isCapable')
                ->with('manage_{entities}')
                ->andReturn(true);

            $result = $this->policy->create($auth, null);
            expect($result)->toBeTrue();
        });

        it('denies users without capability', function () {
            $auth = Mockery::mock(AuthUser::class);
            $auth->shouldReceive('isCapable')
                ->with('manage_{entities}')
                ->andReturn(false);

            $result = $this->policy->create($auth, null);
            expect($result)->toBeFalse();
        });

    });

});
```
</capability_tests>

<ownership_tests>
```php
describe('ownership-based access', function () {

    it('allows owner to update', function () {
        $auth = Mockery::mock(AuthUser::class);
        $auth->ID = 5;
        $auth->shouldReceive('isCapable')->andReturn(false);

        $entity = new {Entity}();
        $entity->created_by = 5;

        $result = $this->policy->update($auth, $entity);
        expect($result)->toBeTrue();
    });

    it('denies non-owner', function () {
        $auth = Mockery::mock(AuthUser::class);
        $auth->ID = 5;
        $auth->shouldReceive('isCapable')->andReturn(false);

        $entity = new {Entity}();
        $entity->created_by = 10;

        $result = $this->policy->update($auth, $entity);
        expect($result)->toBeFalse();
    });

});
```
</ownership_tests>

<crud_methods>
Test all four CRUD methods:
- create($auth, $object)
- read($auth, $object)
- update($auth, $object)
- destroy($auth, $object)
</crud_methods>
