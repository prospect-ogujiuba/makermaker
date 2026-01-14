# WordPress User Relationships

## Standard Audit Relationships
All models include these for audit trail:

```php
use TypeRocket\Models\WPUser;

/** Created by WP user */
public function createdBy()
{
    return $this->belongsTo(WPUser::class, 'created_by');
}

/** Updated by WP user */
public function updatedBy()
{
    return $this->belongsTo(WPUser::class, 'updated_by');
}
```

## Import Required
```php
use TypeRocket\Models\WPUser;
```

## Other WP Relationships

### Featured Image (WPPost)
```php
use TypeRocket\Models\WPPost;

/** Entity has a featured image */
public function featuredImage()
{
    return $this->belongsTo(WPPost::class, 'featured_image_id');
}
```

### Owner (WPUser)
For entities owned by a specific user:
```php
/** Entity belongs to owner */
public function owner()
{
    return $this->belongsTo(WPUser::class, 'user_id');
}
```

## Eager Loading
Audit relationships rarely need eager loading:
```php
// Don't include in $with unless displaying user names in list
// protected $with = ['createdBy', 'updatedBy'];
```

## Notes
- WPUser maps to WordPress `wp_users` table
- WPPost maps to WordPress `wp_posts` table
- These are TypeRocket's built-in WordPress model wrappers
