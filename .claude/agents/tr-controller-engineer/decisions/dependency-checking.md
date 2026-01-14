# Decision: Dependency Checking Before Delete

<purpose>
Determine when and how to check for dependent records before allowing deletion.
</purpose>

## When to Check

```
IF model.relationships.hasMany.length > 0:
  → Check each hasMany relationship
  Examples: Service hasMany Prices, Category hasMany Services

IF model.relationships.belongsToMany.length > 0:
  → Check pivot table for existing associations
  Examples: Service belongsToMany Equipment

IF model.relationships.hasOne.length > 0:
  → Usually skip (delete cascades or nullable)
```

## How to Check

**Single relationship:**
```php
if ($error = DeleteHelper::checkDependencies($entity, 'relationshipName', $response)) {
    return $error;
}
```

**Multiple relationships:**
```php
$relationships = ['prices', 'bookings', 'reviews'];
foreach ($relationships as $relationship) {
    if ($error = DeleteHelper::checkDependencies($entity, $relationship, $response)) {
        return $error;
    }
}
```

## Response on Block

DeleteHelper returns 409 Conflict with message listing dependent records.

## Alternative: Cascade Delete

If migration uses `onDelete('cascade')`, skip dependency check for that relationship.
