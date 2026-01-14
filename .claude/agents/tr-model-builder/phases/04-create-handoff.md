# Phase 4: Create Output Handoff

## Purpose
Produce model_handoff.yaml for downstream agents.

## Handoff Structure

```yaml
handoff:
  metadata:
    from_stage: "model-phase"
    to_stage: "policy-phase|fields-phase|controller-phase"
    created_at: "{timestamp}"

  model:
    class_name: "{EntityName}"
    namespace: "MakerMaker\\Models"
    table_name: "srvc_{entity_plural}"
    file_path: "app/Models/{EntityName}.php"

  properties:
    fillable: []
    guard: []
    private: []
    cast: {}
    format: {}
    with: []

  relationships:
    - name: "{relationshipName}"
      type: "belongsTo|hasMany|belongsToMany"
      model: "{RelatedModel}"
      method: "{methodName}"
      foreign_key: "{fk_column}"

  query_scopes:
    - name: "{scopeName}"
      description: "{what it does}"

  computed_properties:
    - name: "{propertyName}"
      description: "{what it returns}"

  rest_api:
    endpoint: "/tr-api/rest/{resource}"
    auto_generated: true
    excluded_fields: []

  decisions:
    - decision: "{what you decided}"
      rationale: "{why}"
      impact: ["{downstream effect}"]

  validation_hints:
    required_fields: []
    unique_fields: []

  authorization_hints:
    capabilities: ["manage_{entity_plural}", "edit_{entity_plural}", "delete_{entity_plural}"]

  discovery_hints:
    - hint: "{pattern suggestion}"
      for_agent: "{which agent}"
```

## Decision Documentation

Document all model-specific decisions:
- Eager loading choices
- Cast type selections
- Accessor/mutator additions
- Query scope inclusions

## Downstream Agent Usage

| Agent | Uses From Handoff |
|-------|-------------------|
| tr-policy-author | model.class_name, authorization_hints |
| tr-fields-validator | properties.fillable, properties.cast, validation_hints |
| tr-controller-engineer | model.*, properties.*, relationships.* |

## File Output

1. **Model file**: `app/Models/{EntityName}.php`
2. **Handoff file**: Pass to orchestrator or next agent

## Completion Checklist

- [ ] Model class written with all properties
- [ ] All relationships defined with PHPDoc
- [ ] model_handoff.yaml produced
- [ ] Decisions documented
- [ ] Discovery hints added for downstream agents
