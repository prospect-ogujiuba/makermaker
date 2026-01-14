# Phase 1: Parse Requirements

## Purpose
Extract entity specification from requirements handoff.

## Input Structure

```yaml
handoff:
  schema:
    entity_name: ""        # PascalCase (Service)
    entity_plural: ""      # Plural for table (services)
    table_prefix: "srvc_"

    fields:
      - name: ""           # snake_case column
        type: ""           # string | int | text | json | etc.
        nullable: false
        default: null
        unique: false
        indexed: false

    relationships:
      - type: ""           # belongsTo | hasMany | belongsToMany
        target: ""         # Target entity
        foreign_key: ""    # FK column
        cascade: ""        # restrict | cascade | set_null

    features:
      has_sku: false
      soft_delete: true
      audit_trail: true

  decisions: []
  constraints: []
  discovery_hints: []
```

## Extraction Process

1. **Entity identification**
   - entity_name → Class name
   - entity_plural → Table suffix

2. **Field mapping**
   - Map abstract types to SQL types
   - Identify required vs nullable
   - Note unique constraints

3. **Relationship analysis**
   - belongsTo → FK column on this table
   - hasMany → FK on related table (no column here)
   - belongsToMany → Junction table needed

4. **Feature flags**
   - has_sku → Add sku column with unique index
   - soft_delete → Add deleted_at column
   - audit_trail → Add timestamp and user columns

## Output

Parsed specification:
```yaml
parsed:
  table_name: srvc_services
  entity: Service
  columns: []        # Mapped to SQL types
  relationships: []  # FK requirements
  indexes: []        # Required indexes
  features: {}       # Active features
```

## Next Phase
Proceed to Phase 2: Design Schema.
