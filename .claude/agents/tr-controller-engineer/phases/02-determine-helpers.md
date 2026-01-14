# Phase 2: Determine Helpers

<purpose>
Decision tree to determine which helpers are needed based on controller_spec from Phase 1.
</purpose>

## Helper Decision Tree

For each helper, evaluate conditions from controller_spec:

### AuthorizationHelper (ALWAYS required)
```
Decision: INCLUDE
Reason: All mutating methods require policy enforcement
Methods: create, update, destroy
```

### AuditTrailHelper (ALWAYS required)
```
Decision: INCLUDE
Reason: All entities track created_by/updated_by
Methods: create, update
```

### RestHelper (ALWAYS required)
```
Decision: INCLUDE
Reason: All controllers support dual web/REST responses
Methods: create, update, destroy
```

### AutoCodeHelper (CONDITIONAL)
```
IF controller_spec.model.has_sku == true:
  IF controller_spec.model.has_slug == true:
    Decision: generateSkuAndSlug
  ELSE:
    Decision: generateSku (check for manufacturer prefix)
ELSE IF controller_spec.model.has_slug == true:
  Decision: generateSlug
ELSE IF 'code' in controller_spec.model.fillable:
  Decision: generateCode
ELSE:
  Decision: SKIP
```

### DeleteHelper (CONDITIONAL)
```
IF controller_spec.model.relationships.hasMany.length > 0:
  Decision: INCLUDE with dependency checks
  Relationships: [list from model relationships]
ELSE IF controller_spec.model.relationships.belongsToMany.length > 0:
  Decision: INCLUDE with dependency checks
ELSE:
  Decision: INCLUDE (simple delete, no dependency check)
```

### RestIndexHelper (ALWAYS for REST)
```
Decision: INCLUDE
Methods: indexRest, showRest
With: controller_spec.model.relationships (for eager loading)
```

## Output: helper_decisions

```yaml
helper_decisions:
  authorization: true
  audit_trail: true
  rest_helper: true
  auto_code:
    enabled: true|false
    method: "generateSkuAndSlug|generateSlug|generateCode|null"
  delete_helper:
    enabled: true
    check_dependencies: true|false
    relationships: ["relation1", "relation2"]
  rest_index: true
```

## Triggers for Phase 3

<triggers>
- condition: "helper_decisions.auto_code.enabled == true"
  loads: ["helpers/auto-code-helper.md", "decisions/sku-vs-slug.md"]
- condition: "helper_decisions.delete_helper.check_dependencies == true"
  loads: ["helpers/delete-helper.md", "decisions/dependency-checking.md"]
</triggers>
