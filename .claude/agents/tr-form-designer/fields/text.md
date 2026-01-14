# Text Field Types

## text()
For VARCHAR(1-128), names, SKUs, slugs, short strings.

```php
$form->text('name')
    ->setLabel('Name')
    ->setHelp('Enter the name')
    ->setAttribute('maxlength', '128')
    ->setAttribute('placeholder', 'e.g., Example Name')
    ->markLabelRequired()
```

Common attributes:
- `maxlength`: Match VARCHAR length
- `placeholder`: Example or hint
- `readonly`: For auto-generated

## textarea()
For VARCHAR(129+), TEXT, notes, brief descriptions.

```php
$form->textarea('description')
    ->setLabel('Description')
    ->setHelp('Brief description')
    ->setAttribute('rows', '4')
    ->setAttribute('maxlength', '1000')
```

Row guidelines:
- Short notes: 2-3 rows
- Descriptions: 4-5 rows
- Long content: 6-8 rows

## editor()
For LONGTEXT, rich content, formatted descriptions.

```php
$form->editor('content')
    ->setLabel('Content')
    ->setHelp('Full content with formatting')
    ->setSetting('media_buttons', false)
    ->setSetting('textarea_rows', 8)
    ->setSetting('teeny', true)
    ->setSetting('quicktags', ['buttons' => 'strong,em,ul,ol,li'])
```

Settings:
- `teeny`: Simplified toolbar
- `media_buttons`: Enable/disable media upload
- `textarea_rows`: Editor height
- `quicktags`: Toolbar buttons

## url()
For URL fields, links, external references.

```php
$form->url('website')
    ->setLabel('Website')
    ->setHelp('Full URL including https://')
    ->setAttribute('placeholder', 'https://example.com')
```
