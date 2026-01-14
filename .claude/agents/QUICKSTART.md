# Makermaker Quickstart

## Create a TypeRocket Resource

**Prompt:**
```
Create a resource for {EntityName} with fields: {field list}
```

**Execution flow:**
```
tr-migration-architect → schema + migration
    ↓
tr-model-builder → ORM model + relationships
    ↓
PARALLEL:
├─ tr-policy-author → authorization
├─ tr-fields-validator → validation rules
└─ tr-index-builder → admin table view
    ↓
tr-controller-engineer → CRUD + REST endpoints
    ↓
tr-form-designer → admin form
```

## Prompt Tips

### Be specific about fields:
```
✓ "Equipment has name (string, required), serial_number (string, unique),
   status (enum: active/maintenance/retired), purchase_date (date)"

✗ "Equipment with some fields"
```

### Specify relationships:
```
✓ "Equipment belongs to a Service (required),
   Equipment belongs to a Category (optional)"

✗ "Equipment relates to services"
```

### Request specific patterns:
```
✓ "Controller needs soft delete with restore"
✓ "Form should use tabs for relationships"
✓ "Index needs bulk actions for status changes"
```

## Single-Agent Tasks

| Task | Prompt |
|------|--------|
| Add migration only | "Add migration for {entity}" |
| Add model only | "Add model for {table}" |
| Add controller method | "Add {action} method to {Entity}Controller" |
| Add validation rule | "Add {rule} validation to {field} in {Entity}Fields" |

## File Locations

```
app/Models/          → {Entity}.php
app/Controllers/     → {Entity}Controller.php
app/Auth/            → {Entity}Policy.php
app/Http/Fields/     → {Entity}Fields.php
database/migrations/ → {timestamp}.{description}.sql
resources/views/     → {entity}/form.php, index.php
inc/resources/       → {entity}.php (registration)
```
