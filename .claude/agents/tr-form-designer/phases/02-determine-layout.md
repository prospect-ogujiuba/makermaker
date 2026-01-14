# Phase 2: Determine Layout

## Purpose
Decide tab structure, fieldset groupings, and column arrangements.

## Layout Decision Tree

### Tab Layout Triggers

**Use tabs when:**
- More than 6 fillable fields
- Multiple logical groupings exist
- Relationships tab needed
- Settings/configuration section needed

**Single fieldset when:**
- 6 or fewer fields
- Simple entity (lookup table)

### Standard Tab Structure

**Load:** `@layouts/tab-layout.md`

```
Overview tab (always first)
├── Core details fieldset
└── Optional: Additional info fieldset

Settings tab (if flags/config exist)
├── Configuration fieldset
└── Status/flags fieldset

Relationships tab (if $current_id && hasMany)
└── Related entities display

System tab (always last, conditional)
└── Audit fields (readonly)
```

## Field Grouping Logic

### Group by semantic purpose:

**Identity fields:**
- name, sku, slug, title, code

**Classification fields:**
- *_type_id, *_category_id, status

**Description fields:**
- description, notes, content, *_desc

**Numeric fields:**
- price, cost, quantity, *_amount

**Date fields:**
- *_date, *_at, scheduled_*, expires_*

**Boolean flags:**
- is_*, has_*, can_*

**Metadata:**
- metadata, specs, attributes, options (JSON)

## Column Layout

**Load:** `@layouts/row-column.md`

**Two columns (50/50):**
- Related pairs (name + sku)
- Start/end dates
- Min/max values
- Short text fields

**Full width:**
- Textarea, editor
- Descriptions
- Repeaters
- Images (sometimes)

## Output

Layout specification:
```yaml
layout:
  tabs:
    - name: Overview
      icon: admin-settings
      fieldsets:
        - title: Details
          fields: [name, sku, type_id, description]
    - name: Settings
      icon: admin-generic
      fieldsets:
        - title: Configuration
          fields: [is_active, sort_order, metadata]
    - name: System
      icon: info
      conditional: $current_id
      fieldsets:
        - title: System Info
          fields: [id, version, created_at, updated_at, created_by, updated_by]
```

## Next Phase
Proceed to Phase 3: Generate Fields.
