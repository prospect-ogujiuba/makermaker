# Media Field Types

## image()
For image uploads, featured images.

```php
$form->image('featured_image_id')
    ->setLabel('Featured Image')
    ->setHelp('Upload or select image (recommended: 800x600px)')
    ->markLabelRequired()
```

**Stores:** Attachment ID (BIGINT) in database

**Use for:**
- featured_image_id
- *_image_id
- photo, avatar, logo

## gallery()
For multiple image uploads.

```php
$form->gallery('image_gallery')
    ->setLabel('Image Gallery')
    ->setHelp('Multiple images for this item')
```

**Stores:** JSON array of attachment IDs
**Requires:** Model cast to array

## file()
For document uploads, PDFs, files.

```php
$form->file('manual_pdf')
    ->setLabel('Manual PDF')
    ->setHelp('Upload PDF manual (max 10MB)')
```

**Stores:** Attachment ID (BIGINT)

**Use for:**
- *_pdf
- *_file
- document, attachment

## Background Image
For CSS background images.

```php
$form->background('header_background')
    ->setLabel('Header Background')
    ->setHelp('Background image for header area')
```

## Media with Button
For optional media with clear option.

```php
$form->image('logo_id')
    ->setLabel('Logo')
    ->setHelp('Company logo')
    ->setButton('Select Logo')
```
