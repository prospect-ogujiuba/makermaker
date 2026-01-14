# Phase 1: Analyze Handoffs

## Purpose
Parse and merge information from 3 input handoffs to inform form design.

## Input Sources

### 1. model_handoff.yaml
Extract:
- `fillable`: Fields that need form inputs
- `relationships`: belongsTo needs select dropdowns
- `casts`: JSON→array means repeater, bool→toggle
- `with`: Eager loads for relationship handling

### 2. fields_handoff.yaml
Extract:
- `ui_hints`: Specific input types and configs
- `validation_rules`: Determine required fields
- `decisions`: Context for UI choices

### 3. controller_handoff.yaml
Extract:
- `helpers_used`: Affects field behavior
  - AutoCodeHelper → SKU/slug readonly
  - AuditTrailHelper → System tab info
- `variables`: Form variables ($form, $current_id, etc.)

## Field Mapping

For each fillable field, determine:
```yaml
field:
  name: ""
  db_type: ""           # From model column_types
  ui_type: ""           # From fields_handoff or inferred
  is_required: false    # From validation_rules
  is_readonly: false    # If auto-generated
  relationship: null    # If foreign key
  cast_type: null       # If has cast
```

## Required Field Detection

Field is required if validation_rules contains `required|...`:
```yaml
validation_rules:
  name: "required|string|max:255"  # Required
  notes: "nullable|string"          # Not required
```

## Auto-Generated Field Detection

Field is auto-generated if:
- Controller uses AutoCodeHelper AND field is 'sku' or 'slug'
- Set readonly after creation ($current_id exists)

## Output

Merged field specification:
```yaml
fields:
  - name: sku
    db_type: VARCHAR(64)
    ui_type: text
    is_required: false
    is_readonly: true
    hint: "Auto-generated"
  - name: type_id
    db_type: BIGINT UNSIGNED
    ui_type: select
    is_required: true
    relationship:
      model: ServiceType
      display: name
```

## Next Phase
Proceed to Phase 2: Determine Layout.
