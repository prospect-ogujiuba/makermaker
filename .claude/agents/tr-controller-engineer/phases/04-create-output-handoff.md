# Phase 4: Create Output Handoff

<purpose>
Produce controller_handoff.yaml for downstream agents (tr-form-designer, tr-index-builder).
</purpose>

## Handoff Location

Output: `.planning/handoffs/controller_handoff.yaml`

## Required Sections

### 1. metadata
```yaml
metadata:
  from_stage: "controller-phase"
  to_stage: "form-phase|index-phase"
  timestamp: "{current_timestamp}"
  version: "1.0"
```

### 2. schema
Document controller structure for downstream agents:
```yaml
schema:
  controller_class: "{Entity}Controller"
  model_class: "{Entity}"
  namespace: "MakerMaker\\Controllers"
  methods: [index, add, create, edit, update, show, destroy, indexRest, showRest]
  helpers_used: [list with purpose for each]
  routes:
    admin_prefix: "{entity_plural}"
    rest_prefix: "{entity_plural}"
  rest_endpoints: [method, path, controller_method, auth_required]
```

### 3. discovery_hints
Inform form and index phases what they need:
```yaml
discovery_hints:
  - hint: "Form needs SKU field as readonly after creation"
    condition: "AutoCodeHelper in helpers_used"
  - hint: "Index needs soft delete filter toggle"
    condition: "DeleteHelper in helpers_used"
  - hint: "Form needs audit trail display section"
    condition: "AuditTrailHelper in helpers_used"
```

### 4. decisions
Reference upstream decisions:
```yaml
decisions:
  - decision: "{what was decided}"
    rationale: "Building on: migration_handoff, model_handoff, policy_handoff"
    impact: ["effect on form", "effect on index"]
```

### 5. constraints
Hard rules for downstream:
```yaml
constraints:
  - constraint: "All CRUD methods call policy before action"
    enforcement: "hard"
  - constraint: "REST responses use RestHelper format"
    enforcement: "hard"
```

## Successor-Specific Data

### For tr-form-designer
- Fields that need readonly state (SKU after creation)
- Audit trail fields to display
- REST endpoints for AJAX submission

### For tr-index-builder
- Columns from fillable fields
- Soft delete filter needed
- Bulk actions (restore if soft delete)
- REST endpoint for data fetching
