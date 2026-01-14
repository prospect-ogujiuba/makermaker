<staged_context>
## Context Loading Architecture

This workflow loads context based on modification scope - only affected components get context.

## Stage 0: Skill Router
NO REFERENCES LOADED
- Determine modification type from user intent

## Stage 1: Scope Determination
NO REFERENCES LOADED
- Identify what's being modified
- Map modification to affected components
- OUTPUT: modification_scope.yaml

## Stage 2+: Component-Specific Stages
CONTEXT LOADED based on scope - see Modification Routing Matrix below.
Only load references/handoffs needed for affected components.
</staged_context>

<objective>
Modify an existing TypeRocket resource by updating specific components.
</objective>

<process>
## Stage 1: Determine Modification Scope

Ask user what they want to modify:
- **Add field**: New column to existing table
- **Remove field**: Remove column (careful: data loss)
- **Change field type**: Alter column definition
- **Add validation**: New rules to Fields class
- **Modify controller**: Business logic changes
- **Update form**: UI layout changes
- **Update index**: Table column changes

**Output modification_scope.yaml:**
```yaml
modification:
  type: "add_field" | "remove_field" | "change_type" | "add_validation" | "controller_logic" | "form_layout" | "index_columns"
  entity: "{Entity}"
  details:
    field_name: "..."
    field_type: "..."
    additional_context: "..."
  affected_components:
    - migration
    - model
    - fields
    - form
    # (populated based on type)
```

## Stage 2: Route to Affected Components

<modification_routing_matrix>
| Modification | Components Affected | Context Loaded |
|--------------|---------------------|----------------|
| Add field | migration, model, fields, form | migration-templates.md#alter-table, typerocket-patterns.md#fillable-and-guarded |
| Remove field | migration, model, fields, form | migration-templates.md#alter-table |
| Change field type | migration, model, fields | migration-templates.md#alter-table, typerocket-patterns.md#casts |
| Add validation | fields only | (no references - handoff only) |
| Controller logic | controller only | controller-patterns.md (section based on change type) |
| Form layout | form only | (no references - handoff only) |
| Index columns | index only | (no references - handoff only) |
</modification_routing_matrix>

## Stage 3: Execute Modifications by Type

### For Field Modifications (add/remove/change)

<discovery_trigger_evaluation>
Field changes cascade through multiple components:
- Migration: Load migration-templates.md#alter-table
- Model: Load typerocket-patterns.md#fillable-and-guarded
- If JSON field: Load typerocket-patterns.md#json-casting
- If relationship field: Load typerocket-patterns.md#relationships
</discovery_trigger_evaluation>

**Cascade sequence:**
```
1. tr-migration-architect (ALTER TABLE)
        ↓
2. tr-model-builder (update $fillable, $casts)
        ↓
3. tr-fields-validator (update validation rules)
        ↓
4. tr-form-designer (update form layout)
        ↓
5. tr-index-builder (if field should appear in list)
```

Each stage receives only:
- Previous stage handoff (what changed)
- Current file content (to modify)
- Relevant reference section (if needed)

### For Validation-Only Changes

<discovery_trigger_evaluation>
No reference sections needed.
Context: model_handoff (fillable fields), current Fields file
</discovery_trigger_evaluation>

**Single agent:** tr-fields-validator
```yaml
input:
  entity: "{Entity}"
  current_file: "app/Fields/{Entity}Fields.php"
  modification: {validation rule changes}
```

### For Controller Logic Changes

<discovery_trigger_evaluation>
Load only relevant controller-patterns.md section based on change type:
- CRUD method change → #crud-methods
- REST method change → #rest-methods
- Helper integration → #helper-integration
- Response format → #response-patterns
</discovery_trigger_evaluation>

**Single agent:** tr-controller-engineer
```yaml
input:
  entity: "{Entity}"
  current_file: "app/Controllers/{Entity}Controller.php"
  modification: {logic changes}
  context_section: [based on change type]
```

### For Form/Index Layout Changes

<discovery_trigger_evaluation>
No reference sections needed.
Context: current file content, model_handoff (for available fields)
</discovery_trigger_evaluation>

**Single agent:** tr-form-designer OR tr-index-builder
```yaml
input:
  entity: "{Entity}"
  current_file: "app/Views/{entities}/form.php" | "app/Views/{entities}/index.php"
  modification: {layout changes}
```

## Stage 4: Cross-Check Consistency

After modifications, verify consistency across components:
- Model $fillable matches migration columns
- Fields rules cover all fillable fields
- Form displays all user-editable fields
- Controller handles all validated fields

<handoff_validation stage="cross_check">
Required: All modified files pass syntax check
Verify: No orphaned references between components
</handoff_validation>
</process>

<field_modification_flow>
Adding a field requires updates to:
1. Migration (new ALTER TABLE or new migration file)
2. Model ($fillable, possibly $cast)
3. Fields (validation rules)
4. Form (new form element)
5. Index (if field should appear in list)

Context loaded per component (NOT all upfront):
- Migration: migration-templates.md#alter-table
- Model: typerocket-patterns.md#fillable-and-guarded (+ #json-casting if JSON)
- Fields: no reference (handoff provides context)
- Form: no reference (handoff provides context)
- Index: no reference (handoff provides context)
</field_modification_flow>

<handoff_templates>
Reference for modification handoffs:
- handoffs/modification_scope.yaml.template
- handoffs/field_change_handoff.yaml.template
</handoff_templates>

<success_criteria>
- [ ] Modification applied to all affected files
- [ ] No syntax errors in modified files
- [ ] Validation still passes
- [ ] REST API still responds
- [ ] Admin interface still functional
</success_criteria>
