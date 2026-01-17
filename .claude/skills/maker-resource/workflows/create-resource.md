<staged_context>

## Context Loading Architecture

This workflow uses staged discovery - context is loaded just-in-time based on handoff content.
Reference handoffs/discovery-triggers.md for trigger conditions.

## Stage 0: Skill Router (~500 tokens)

NO REFERENCES LOADED

- Essential principles from SKILL.md only (naming, table prefix, audit fields)
- Determine workflow route based on user intent

## Stage 1: Requirements Gathering (~2k tokens)

NO REFERENCES LOADED

- Gather entity name, fields, relationships, features
- OUTPUT: requirements_handoff.yaml

## Stage 2: Migration Phase

CONTEXT LOADED (based on requirements_handoff triggers):

- migration-templates.md#sql-templates (always)
- migration-templates.md#sku-columns (if features.has_sku == true)
- migration-templates.md#soft-delete-columns (if features.soft_delete == true)
- migration-templates.md#foreign-keys (if relationships.length > 0)
- requirements_handoff.yaml

AGENT: tr-migration-architect
OUTPUT: migration_handoff.yaml

## Stage 3: Model Phase

CONTEXT LOADED (based on migration_handoff triggers):

- typerocket-patterns.md#model-patterns (always)
- typerocket-patterns.md#soft-delete-trait (if decisions contains "soft delete")
- typerocket-patterns.md#json-casting (if columns contains type="JSON")
- typerocket-patterns.md#relationships (if foreign_keys.length > 0)
- migration_handoff.yaml

AGENT: tr-model-builder
OUTPUT: model_handoff.yaml

## Stage 4A/4B: Policy + Fields (Parallel)

CONTEXT LOADED:

- model_handoff.yaml only
- No reference files needed

AGENTS: tr-policy-author, tr-fields-validator (parallel)
OUTPUT: policy_handoff.yaml, fields_handoff.yaml

## Stage 5: Controller Phase

CONTEXT LOADED (based on handoff triggers):

- controller-patterns.md#structure (always)
- controller-patterns.md#crud-methods (always)
- controller-patterns.md#validation-integration (if fields_handoff.validation_rules.length > 0)
- controller-patterns.md#helper-integration (if model_handoff.helpers_used contains "AutoCodeHelper")
- controller-patterns.md#rest-methods (if rest_endpoints.length > 0)
- model_handoff.yaml
- policy_handoff.yaml
- fields_handoff.yaml

AGENT: tr-controller-engineer
OUTPUT: controller_handoff.yaml

## Stage 6A/6B: Form + Index (Parallel)

CONTEXT LOADED:

- model_handoff.yaml (relationships for dropdowns)
- controller_handoff.yaml
- fields_handoff.yaml (ui_hints for form layout)
- No reference files needed

AGENTS: tr-form-designer, tr-index-builder (parallel)
OUTPUT: form file, index file

## Stage 7: Registration + Verification

- Create inc/resources/{entity}.php
- Verify all 7 files exist
- Test REST endpoints and admin interface
  </staged_context>

<objective>
Create a complete TypeRocket CRUD resource with all 7 standard files plus registration.
</objective>

<process>
## Stage 1: Requirements Gathering

Collect from user:

- **Entity name** (singular, PascalCase): e.g., "Service", "Equipment"
- **Fields** with types: string, int, text, json, datetime, decimal, boolean
- **Relationships**: belongsTo, hasMany, belongsToMany
- **Features**:
  - has_sku: Boolean (determines if AutoCodeHelper generates SKU)
  - soft_delete: Boolean (default true)
  - audit_trail: Boolean (default true)

**Output requirements_handoff.yaml:**

```yaml
handoff:
  metadata:
    from_stage: "requirements-gathering"
    to_stage: "migration-phase"
  schema:
    entity_name: "{Entity}"
    entity_plural: "{Entities}"
    table_prefix: "prfx_"
    fields: [...]
    relationships: [...]
  features:
    has_sku: false
    soft_delete: true
    audit_trail: true
```

<handoff_validation stage="requirements">
Required: entity_name, fields array with at least 1 field
Verify: entity_name is PascalCase, no reserved words
</handoff_validation>

## Stage 2: Migration Phase

<discovery_trigger_evaluation>
Evaluate requirements_handoff against triggers:

- features.has_sku == true → load #sku-columns
- features.soft_delete == true → load #soft-delete-columns
- relationships.length > 0 → load #foreign-keys
  Always load: #sql-templates
  </discovery_trigger_evaluation>

Invoke `tr-migration-architect` with:

```yaml
input_handoff: requirements_handoff.yaml
context_sections: [evaluated trigger results]
```

**Wait for migration_handoff.yaml containing:**

- table: prfx\_{entities}
- columns: [{name, type, sql_definition}...]
- indexes: [{name, columns, type, rationale}...]
- foreign_keys: [{column, references, on_delete, rationale}...]
- decisions: [{decision, rationale, alternatives, impact}...]

<handoff_validation stage="migration">
Required: table, columns array with id column
Verify: audit columns present (created_at, updated_at, deleted_at if soft_delete)
</handoff_validation>

## Stage 3: Model Phase

<discovery_trigger_evaluation>
Evaluate migration_handoff against triggers:

- decisions contains "soft delete" → load #soft-delete-trait
- columns contains type="JSON" → load #json-casting
- foreign_keys.length > 0 → load #relationships
  Always load: #model-patterns
  </discovery_trigger_evaluation>

Invoke `tr-model-builder` with:

```yaml
input_handoff: migration_handoff.yaml
context_sections: [evaluated trigger results]
```

**Wait for model_handoff.yaml containing:**

- class: {Entity}
- fillable: [...]
- guarded: [id, created_at, updated_at]
- casts: {...}
- relationships: [{type, method, target}...]
- decisions: [{decision, rationale, alternatives, impact}...]

<handoff_validation stage="model">
Required: class, fillable array, guarded array
Verify: fillable matches migration columns (excluding auto-managed)
</handoff_validation>

## Stage 4: Policy + Fields (PARALLEL)

<discovery_trigger_evaluation>
Both agents receive model_handoff.yaml only.
No reference sections loaded - handoff provides complete context.
</discovery_trigger_evaluation>

Invoke in parallel:

**tr-policy-author:**

```yaml
input_handoff: model_handoff.yaml
capability_prefix: "manage_{entities}"
```

**tr-fields-validator:**

```yaml
input_handoff: model_handoff.yaml
```

**Wait for both handoffs:**

- policy_handoff.yaml: capabilities[], ownership_field, decisions[]
- fields_handoff.yaml: validation_rules{}, sanitization{}, ui_hints{}

<handoff_validation stage="policy_fields">
Required: Both handoffs received before proceeding
Verify: validation_rules cover all fillable fields
</handoff_validation>

## Stage 5: Controller Phase

<discovery_trigger_evaluation>
Evaluate merged handoffs against triggers:

- fields_handoff.validation_rules.length > 0 → load #validation-integration
- model_handoff.helpers_used contains "AutoCodeHelper" → load #helper-integration
- rest_endpoints.length > 0 → load #rest-methods
  Always load: #structure, #crud-methods
  </discovery_trigger_evaluation>

Invoke `tr-controller-engineer` with:

```yaml
input_handoffs:
  - model_handoff.yaml
  - policy_handoff.yaml
  - fields_handoff.yaml
context_sections: [evaluated trigger results]
has_sku: [from requirements]
```

**Wait for controller_handoff.yaml containing:**

- class: {Entity}Controller
- methods: [index, add, create, edit, update, show, destroy, indexRest, showRest]
- helpers_used: [...]
- decisions: [...]

<handoff_validation stage="controller">
Required: class, methods array with core CRUD methods
Verify: REST methods present if API exposure needed
</handoff_validation>

## Stage 6: Form + Index (PARALLEL)

<discovery_trigger_evaluation>
Form designer uses ui_hints from fields_handoff for layout decisions.
Index builder uses model_handoff for column selection.
No reference sections loaded.
</discovery_trigger_evaluation>

Invoke in parallel:

**tr-form-designer:**

```yaml
input_handoffs:
  - model_handoff.yaml (relationships for dropdowns)
  - controller_handoff.yaml
  - fields_handoff.yaml (ui_hints)
```

**tr-index-builder:**

```yaml
input_handoffs:
  - model_handoff.yaml
  - controller_handoff.yaml
```

**Output:** Form and index view files created directly (no handoff needed for final stage).

## Stage 7: Registration + Verification

Create `inc/resources/{entity}.php`:

```php
<?php
$resource = mm_create_custom_resource('{Entity}', '{Entity}Controller', '{Entities}')
    ->setIcon('bookmark')
    ->setPosition(10);
```

**Verification Checklist:**

1. Check all 7 files exist
2. Verify REST endpoint: `GET /tr-api/rest/{entities}/`
3. Verify admin page loads without errors
4. Test create/read/update/delete cycle
   </process>

<agent_sequence>

```
Stage 1: Requirements
        ↓
Stage 2: tr-migration-architect
        ↓
Stage 3: tr-model-builder
        ↓
Stage 4: tr-policy-author ←──┬──→ tr-fields-validator
                             │
                             ↓
Stage 5: tr-controller-engineer
                             ↓
Stage 6: tr-form-designer ←──┴──→ tr-index-builder
                             ↓
Stage 7: Registration + Verify
```

</agent_sequence>

<handoff_templates>
Reference these templates for handoff structure:

- handoffs/requirements_handoff.yaml.template
- handoffs/migration_handoff.yaml.template
- handoffs/model_handoff.yaml.template
- handoffs/policy_handoff.yaml.template
- handoffs/fields_handoff.yaml.template
- handoffs/controller_handoff.yaml.template
  </handoff_templates>

<success_criteria>

- [ ] Migration file created with proper SQL
- [ ] Model file with $fillable, $guard, $cast, relationships
- [ ] Policy file with create/read/update/delete methods
- [ ] Fields file with validation rules
- [ ] Controller with all CRUD + REST methods
- [ ] Form view with proper field layout
- [ ] Index view with columns and actions
- [ ] Resource registered in inc/resources/
- [ ] REST API responds to requests
- [ ] Admin interface functional
      </success_criteria>
