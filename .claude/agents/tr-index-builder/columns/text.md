# Text Column Types

## Primary Column (with actions)
First text column, usually 'name', 'title', 'subject'.

```php
'name' => [
    'label' => 'Name',
    'sort' => true,
    'actions' => ['edit', 'view', 'delete']
]
```

## Simple Sortable
Standard text columns.

```php
'manufacturer' => [
    'label' => 'Manufacturer',
    'sort' => true
]
```

## Identifier Column (sku, slug, code)
Wrap in `<code>` tags for monospace display.

```php
'sku' => [
    'label' => 'SKU',
    'sort' => true,
    'callback' => function($value) {
        return $value
            ? "<code>{$value}</code>"
            : '<span class="text-muted">N/A</span>';
    }
]
```

## Truncated Text
For longer descriptions shown in table.

```php
'description' => [
    'label' => 'Description',
    'callback' => function($value) {
        return esc_html(wp_trim_words($value, 15));
    }
]
```

## Email with Link
```php
'email' => [
    'label' => 'Email',
    'sort' => true,
    'callback' => function($value) {
        return '<a href="mailto:' . esc_attr($value) . '">'
            . esc_html($value) . '</a>';
    }
]
```

## N/A Fallback
Always handle null values:
```php
return $value ?: '<span class="text-muted">N/A</span>';
```
