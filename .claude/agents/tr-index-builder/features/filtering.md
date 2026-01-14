# Table Filtering

## Automatic Filtering
TypeRocket tables support URL-based filtering automatically.

No explicit filter configuration needed in index view.

## Controller Handling
Filters handled by controller `index()` method:

```php
public function index()
{
    $query = Model::query();

    if ($status = request('status')) {
        $query->where('status', $status);
    }

    if ($type = request('type_id')) {
        $query->where('type_id', $type);
    }

    return view('resource.index', [
        'items' => $query->paginate()
    ]);
}
```

## Common Filter Fields

| Field | Filter Type | URL Param |
|-------|-------------|-----------|
| status | Enum select | ?status=active |
| type_id | Relationship | ?type_id=5 |
| is_active | Boolean | ?is_active=1 |
| search | Text search | ?search=keyword |

## Soft Delete Filter

If controller uses DeleteHelper:
```php
if (request('show_deleted')) {
    $query->withTrashed();
}
```

## Filter UI
Filter forms typically in index view header:
- Status dropdown
- Type dropdown
- Search input
- "Show deleted" checkbox

## Note
Index builder focuses on table output.
Filter UI is separate view concern.
