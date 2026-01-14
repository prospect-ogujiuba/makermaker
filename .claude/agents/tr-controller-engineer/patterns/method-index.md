# Pattern: index() Method

<when>Always included - admin list view</when>

## Template

```php
/**
 * Display list view for {entity_plural}
 */
public function index()
{
    return View::new('{entity_plural}.index');
}
```

## Notes
- No parameters needed
- Returns View instance
- View file at: resources/views/{entity_plural}/index.php
