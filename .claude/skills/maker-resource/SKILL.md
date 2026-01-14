---
name: maker-resource
description: Orchestrated TypeRocket resource creation with staged context loading.
---

<core_concept>
TypeRocket MVC resources with 7-file structure: migration, model, policy, fields, controller, form, index.

Each phase loads only the context it needs. Decisions flow forward via handoffs.
</core_concept>

<intake>
What would you like to do?

1. Create new resource
2. Modify existing resource
3. Add relationship between resources

**Provide entity name and details.**
</intake>

<routing>
| Response | Workflow |
|----------|----------|
| 1, "create", "new" | workflows/create-resource.md |
| 2, "modify", "update" | workflows/modify-resource.md |
| 3, "relationship" | workflows/add-relationship.md |

**After routing, workflow handles staged context loading.**
</routing>

<workflows_index>
| File | Purpose |
|------|---------|
| workflows/create-resource.md | Full 7-file resource creation |
| workflows/modify-resource.md | Modify existing components |
| workflows/add-relationship.md | Add relationships |
</workflows_index>

<handoffs_index>
| Template | Transition |
|----------|------------|
| handoffs/requirements_handoff.yaml.template | intake -> migration |
| handoffs/migration_handoff.yaml.template | migration -> model |
| handoffs/model_handoff.yaml.template | model -> policy/fields |
| handoffs/policy_handoff.yaml.template | policy -> controller |
| handoffs/fields_handoff.yaml.template | fields -> controller/form |
| handoffs/controller_handoff.yaml.template | controller -> form/index |
</handoffs_index>
