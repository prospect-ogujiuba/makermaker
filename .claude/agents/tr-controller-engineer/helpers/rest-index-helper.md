# Helper: RestIndexHelper

<when>indexRest() and showRest() methods</when>

## Location
`MakermakerCore\Helpers\RestIndexHelper`

## Index Usage

```php
// Basic
RestIndexHelper::handleIndex($response, Model::class, 'plural_name');

// With query modifier
RestIndexHelper::handleIndex($response, Model::class, 'plural_name', function($query) {
    return $query->where('is_active', 1)->with(['relation']);
});
```

## Show Usage

```php
// Basic
RestIndexHelper::handleShow($response, $model, Model::class, 'singular_name');

// With eager loading
RestIndexHelper::handleShow($response, $model, Model::class, 'singular_name', ['relation1', 'relation2']);
```

## Behavior
- Handles pagination automatically
- Formats errors consistently
- Returns standard response envelope
