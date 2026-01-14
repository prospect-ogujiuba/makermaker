# Test Structure Pattern

<purpose>
Basic Pest test file structure for TypeRocket components.
</purpose>

<template>
```php
<?php

use MakerMaker\Models\{Entity};

describe('{Entity} Model', function () {

    beforeEach(function () {
        $this->entity = new {Entity}();
    });

    it('has correct table name', function () {
        expect($this->entity->getResource())->toBe('{table}');
    });

    it('has fillable attributes', function () {
        $fillable = $this->entity->getFillable();
        expect($fillable)->toContain('name');
    });

    it('guards audit fields', function () {
        $guarded = $this->entity->getGuarded();
        expect($guarded)->toContain('id');
    });

});
```
</template>

<grouping>
Use `describe()` blocks for logical groupings:
- Model attributes
- Relationships
- Query scopes
- Business logic
</grouping>

<setup_teardown>
```php
beforeEach(function () {
    // Runs before each test
});

afterEach(function () {
    // Cleanup after each test
});
```
</setup_teardown>
