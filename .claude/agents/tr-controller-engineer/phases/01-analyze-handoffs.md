# Phase 1: Analyze Handoffs

<purpose>
Parse and merge 3 input handoffs (model, policy, fields) into a unified controller specification.
</purpose>

## Input Handoffs

You receive THREE handoffs that must be synthesized:

### 1. model_handoff.yaml (from tr-model-builder)
Location: .planning/handoffs/model_handoff.yaml

Extract:
- `schema.class`: Model class name for DI injection
- `schema.namespace`: Full namespace for imports
- `schema.fillable`: Fields for mass assignment (detect SKU/slug needs)
- `schema.relationships`: For eager loading and dependency checks
- `schema.with`: Default eager loads for REST endpoints

### 2. policy_handoff.yaml (from tr-policy-author)
Location: .planning/handoffs/policy_handoff.yaml

Extract:
- `schema.policy_class`: Policy class for authorization
- `schema.capabilities`: CRUD action to capability mapping
- `schema.ownership_field`: Field for ownership checks (e.g., "created_by")
- `schema.admin_override`: Whether admins bypass ownership

### 3. fields_handoff.yaml (from tr-fields-validator)
Location: .planning/handoffs/fields_handoff.yaml

Extract:
- `schema.validation_rules`: Field validation rules
- `schema.sanitization`: WordPress sanitizer mappings
- `schema.fields_class`: Fields class name for DI injection

## Merge Logic

Produce unified specification:

```yaml
controller_spec:
  entity:
    name: "{PascalCase from model}"
    plural: "{lowercase plural}"
    namespace: "MakerMaker"

  model:
    class: "{from model_handoff.schema.class}"
    fillable: ["{from model_handoff}"]
    relationships: {from model_handoff}
    has_sku: "{true if 'sku' in fillable}"
    has_slug: "{true if 'slug' in fillable}"

  policy:
    class: "{from policy_handoff.schema.policy_class}"
    actions: [create, read, update, delete]
    ownership_field: "{from policy_handoff}"

  fields:
    class: "{from fields_handoff.schema.fields_class}"
    validation: {from fields_handoff}
```

## Triggers for Next Phase

Based on merged spec, determine what to load in phase 2:

<triggers>
- condition: "model.has_sku == true OR model.has_slug == true"
  signal: "needs_auto_code_helper"
- condition: "model.relationships.hasMany.length > 0"
  signal: "needs_dependency_check"
- condition: "policy.ownership_field != null"
  signal: "needs_ownership_filter"
</triggers>

## Output

Pass controller_spec to Phase 2 for helper determination.
