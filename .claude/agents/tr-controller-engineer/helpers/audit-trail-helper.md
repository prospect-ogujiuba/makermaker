# Helper: AuditTrailHelper

<when>create() and update() methods</when>

## Location
`MakermakerCore\Helpers\AuditTrailHelper`

## Usage

```php
// In create() - sets both created_by and updated_by
AuditTrailHelper::setCreateAuditFields($model, $user);

// In update() - sets only updated_by
AuditTrailHelper::setUpdateAuditFields($model, $user);
```

## Behavior
- Sets user IDs before save()
- Timestamps handled by TypeRocket/database
- Call AFTER authorization, BEFORE save()
