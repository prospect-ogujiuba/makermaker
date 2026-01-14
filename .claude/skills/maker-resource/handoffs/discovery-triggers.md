# Discovery Triggers Reference

How handoff content triggers section loading across all maker skills.

## How Discovery Works

Agents load sections based on handoff content, not upfront. When an agent receives a handoff, it evaluates trigger conditions against handoff fields to determine which reference sections to load.

```
Handoff received → Evaluate triggers → Load matching sections → Execute phase
```

## Trigger Format

```yaml
trigger: "condition expression"
load: "reference-file.md"
section: "section-id"
```

Conditions are evaluated against handoff YAML fields using simple expressions:
- `field contains "value"` - String/array contains substring or element
- `field.length > 0` - Array has elements
- `field == true` - Boolean check
- `field.subfield == "value"` - Nested field comparison

---

## Maker-Resource Triggers

### Requirements → Migration Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | migration-templates.md | #sql-templates |
| features.has_sku == true | migration-templates.md | #sku-columns |
| features.soft_delete == true | migration-templates.md | #soft-delete-columns |
| relationships.length > 0 | migration-templates.md | #foreign-keys |

### Migration → Model Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | typerocket-patterns.md | #model-patterns |
| decisions contains "soft delete" | typerocket-patterns.md | #soft-delete-trait |
| columns contains type="JSON" | typerocket-patterns.md | #json-casting |
| foreign_keys.length > 0 | typerocket-patterns.md | #relationships |
| columns contains unique=true | typerocket-patterns.md | #fillable-and-guarded |

### Model → Policy Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (no reference loading) | - | - |
| Context: model_handoff.yaml only | | |

### Model → Fields Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (no reference loading) | - | - |
| Context: model_handoff.yaml only | | |

### Policy/Fields → Controller Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | controller-patterns.md | #structure |
| (always) | controller-patterns.md | #crud-methods |
| fields_handoff.validation_rules.length > 0 | controller-patterns.md | #validation-integration |
| model_handoff.helpers_used contains "AutoCodeHelper" | controller-patterns.md | #helper-integration |
| rest_endpoints.length > 0 | controller-patterns.md | #rest-methods |

### Controller → Form Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| fields_handoff.ui_hints exists | - | (use ui_hints directly) |
| model_handoff.relationships.length > 0 | - | (inform dropdown generation) |

### Controller → Index Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (no reference loading) | - | - |
| Context: model_handoff, controller_handoff | | |

---

## Maker-Block Triggers

### Requirements → Architecture Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | block-api.md | #block-json |
| (always) | block-api.md | #index-js |
| (always) | block-api.md | #render-php |
| attributes.length > 0 | block-api.md | #attributes |

### Architecture → React Component Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | react-patterns.md | #hooks |
| data_source.dynamic == true | react-patterns.md | #fetch-patterns |
| ui_features contains "loading_states" | react-patterns.md | #loading-states |
| ui_features contains "filters" | component-library.md | #filter-components |
| ui_features contains "pagination" | component-library.md | #pagination-components |
| attributes contains type="array" | react-patterns.md | #state-management |

### Architecture → Editor UX Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | block-api.md | #edit-js |
| attributes.length > 0 | block-api.md | #inspector-controls |
| supports.align == true | block-api.md | #supports |

---

## Maker-Template Triggers

### Requirements → Template Building Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| (always) | fse-patterns.md | #block-markup |
| (always) | fse-patterns.md | #template-hierarchy |
| sections contains "header" OR "footer" | fse-patterns.md | #template-parts |
| makerblocks.length > 0 | fse-patterns.md | #makerblocks |
| sections contains "query" | fse-patterns.md | #query-blocks |
| template_type in ["page", "single", "archive"] | fse-patterns.md | #layout-blocks |

### Template → Theme Integration Phase

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| theme_needs_handoff.new_colors_needed.length > 0 | theme-json.md | #color-palette |
| theme_needs_handoff.new_typography_needed.length > 0 | theme-json.md | #typography |
| theme_needs_handoff.custom_template_registration == true | theme-json.md | #custom-templates |
| theme_needs_handoff.new_spacing_needed.length > 0 | theme-json.md | #spacing |
| (any theme integration needed) | theme-json.md | #schema |

---

## Cross-Skill Triggers

When skills interact (e.g., maker-block creates a block that maker-template uses):

### Maker-Block → Maker-Template

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| requirements_handoff.makerblocks contains newly_created_block | fse-patterns.md | #makerblocks |

### Maker-Resource → Maker-Block

| Trigger Condition | Reference | Section |
|-------------------|-----------|---------|
| block requires REST endpoint from resource | block-api.md | #render-php |
| controller_handoff.rest_endpoints.length > 0 | react-patterns.md | #fetch-patterns |

---

## Trigger Evaluation Examples

### Example 1: Migration → Model with Soft Delete

**migration_handoff.yaml:**
```yaml
decisions:
  - decision: "Soft delete via deleted_at column"
    rationale: "Business requirement to maintain history"
columns:
  - name: "metadata"
    type: "JSON"
```

**Trigger evaluation:**
```
decisions contains "soft delete" → TRUE → Load #soft-delete-trait
columns contains type="JSON" → TRUE → Load #json-casting
```

**Result:** Model phase loads typerocket-patterns.md sections: #model-patterns, #soft-delete-trait, #json-casting

### Example 2: Architecture → React Component with Filters

**architecture_handoff.yaml:**
```yaml
data_source:
  endpoint: "/tr-api/rest/services/"
  dynamic: true
ui_features:
  - filters
  - loading_states
  - pagination
```

**Trigger evaluation:**
```
data_source.dynamic == true → TRUE → Load #fetch-patterns
ui_features contains "loading_states" → TRUE → Load #loading-states
ui_features contains "filters" → TRUE → Load #filter-components
ui_features contains "pagination" → TRUE → Load #pagination-components
```

**Result:** React component phase loads react-patterns.md (#hooks, #fetch-patterns, #loading-states) and component-library.md (#filter-components, #pagination-components)

### Example 3: Template → Theme Integration (No Changes Needed)

**theme_needs_handoff.yaml:**
```yaml
new_colors_needed: []
new_typography_needed: []
custom_template_registration: false
```

**Trigger evaluation:**
```
new_colors_needed.length > 0 → FALSE
new_typography_needed.length > 0 → FALSE
custom_template_registration == true → FALSE
```

**Result:** Theme integration phase skipped entirely (no sections loaded)

---

## Implementation Notes

1. **Trigger evaluation happens at phase start** - Before any work begins, agent reads handoff and evaluates all triggers for its phase

2. **Sections are additive** - Multiple matching triggers load multiple sections; no duplication if same section triggered twice

3. **Base context always loaded** - Each phase has base context (the handoff itself) plus conditional sections

4. **Discovery hints in references** - Reference files contain XML comments documenting which triggers activate each section

5. **Trigger specificity matters** - Conditions should be specific enough to be unambiguous: "contains soft delete" not "has delete"
