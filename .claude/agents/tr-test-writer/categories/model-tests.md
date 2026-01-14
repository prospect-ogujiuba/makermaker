# Model Test Category

<purpose>
Test patterns for TypeRocket model classes.
</purpose>

<unit_tests>
```php
describe('{Entity} Model', function () {

    it('has correct table name', function () {
        $model = new {Entity}();
        expect($model->getResource())->toBe('{table}');
    });

    it('has fillable attributes', function () {
        $model = new {Entity}();
        $fillable = $model->getFillable();
        expect($fillable)->toContain('name');
        expect($fillable)->toContain('sku');
    });

    it('guards audit fields', function () {
        $model = new {Entity}();
        $guarded = $model->getGuarded();
        expect($guarded)->toContain('id');
        expect($guarded)->toContain('created_at');
    });

});
```
</unit_tests>

<relationship_tests>
```php
describe('{Entity} Relationships', function () {

    it('belongs to type', function () {
        $model = new {Entity}();
        $relation = $model->entityType();
        expect($relation)->toBeInstanceOf(BelongsTo::class);
    });

    it('has many children', function () {
        $model = new {Entity}();
        $relation = $model->children();
        expect($relation)->toBeInstanceOf(HasMany::class);
    });

});
```
</relationship_tests>

<integration_tests>
```php
describe('{Entity} Database', function () {

    it('persists to database', function () {
        $model = {Entity}::create([...]);
        $found = {Entity}::find($model->id);
        expect($found)->not->toBeNull();
    });

    it('eager loads relationships', function () {
        $model = {Entity}::with('type')->find(1);
        expect($model->type)->not->toBeNull();
    });

});
```
</integration_tests>
