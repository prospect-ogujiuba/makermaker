# Phase 4: Create Handoff

## Purpose
Produce migration_handoff.yaml for tr-model-builder.

## Handoff Structure

```yaml
handoff:
  metadata:
    from_stage: "migration-phase"
    to_stage: "model-phase"
    created_at: "{timestamp}"

  schema:
    entity: ""              # PascalCase entity name
    table: ""               # Full table name

    columns:
      - name: ""            # Column name
        type: ""            # SQL type
        primary: false
        auto_increment: false
        nullable: false
        unique: false
        default: null
        foreign_key: null   # FK reference if applicable

    indexes:
      - name: ""            # Index name
        columns: []         # Columns
        rationale: ""       # Why this index

    foreign_keys:
      - column: ""          # FK column
        references: ""      # Target table.column
        on_delete: ""       # CASCADE | RESTRICT | SET NULL
        rationale: ""       # Cascade behavior reason

  decisions:
    - decision: ""
      rationale: ""
      alternatives: []
      impact: []

  constraints: []           # Hard constraints for model
  discovery_hints: []       # Patterns for model to consider
```

## Decision Documentation

Document every architectural decision:

### Index Decisions
```yaml
- decision: "Composite index on (type_id, is_active)"
  rationale: "Common query pattern filters by type and status"
  impact: ["Model queries should use this column order"]
```

### FK Cascade Decisions
```yaml
- decision: "CASCADE delete on service_type_id"
  rationale: "Services should be removed when type is deleted"
  impact: ["Model relationships inherit cascade behavior"]
```

### Nullable Decisions
```yaml
- decision: "description nullable"
  rationale: "Optional field, not required for valid record"
  impact: ["Form field not required", "Validation allows null"]
```

## Discovery Hints for Model

Provide guidance for model builder:
```yaml
discovery_hints:
  - hint: "JSON metadata column exists"
    for_agent: "tr-model-builder"
    action: "Add array cast for metadata"
  - hint: "Soft delete via deleted_at"
    for_agent: "tr-model-builder"
    action: "Model needs soft delete handling"
```

## Completion Checklist

- [ ] All columns documented with types
- [ ] All indexes explained with rationale
- [ ] FK relationships defined with cascade behavior
- [ ] Decisions documented with alternatives
- [ ] Discovery hints provided for model builder
