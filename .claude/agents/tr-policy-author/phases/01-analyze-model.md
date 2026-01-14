# Phase 1: Analyze Model Handoff

<purpose>
Parse model handoff from tr-model-builder to identify authorization requirements.
</purpose>

<input>
model_handoff.yaml containing:
- class: Model class name
- fillable: Mass-assignable fields
- guarded: Protected fields
- relationships: belongsTo, hasMany associations
</input>

<extraction_tasks>

## 1. Identify Ownership Fields

Look for these in fillable or guarded:
- `created_by` - Creator ownership
- `user_id` - User assignment
- `assigned_to` - Task/item assignment
- `owner_id` - Explicit ownership

```yaml
ownership_analysis:
  field: null | "created_by" | "user_id" | "assigned_to"
  pattern: "none" | "creator" | "assigned" | "dual"
```

## 2. Check Relationship Sensitivity

Analyze relationships for:
- User relationships (belongsTo User) → suggests ownership access
- Parent relationships → may need hierarchical authorization

## 3. Detect Data Sensitivity

From casts and field names:
- `is_private`, `visibility` → access levels
- `metadata` with sensitive data → careful authorization

## 4. Extract Capability Hints

From handoff capabilities section:
```yaml
capabilities:
  manage: manage_services  # Admin-level
  edit: edit_services      # Editor-level (optional)
  custom:                  # Custom capabilities (optional)
    - assign_equipment
    - export_equipment
```

Default: `manage_{plural_entity}` if not specified

</extraction_tasks>

<output>
Pass to Phase 2:
```yaml
analysis:
  entity: Equipment
  ownership_field: null | "created_by"
  has_user_relationship: false
  capabilities_hint:
    manage: "manage_services"
    custom: []
  access_pattern_suggestion: "admin_only" | "ownership" | "public_read"
```
</output>
