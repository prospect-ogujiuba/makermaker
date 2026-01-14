# Phase 2: Define Capabilities

<purpose>
Select capability naming and access pattern based on model analysis.
</purpose>

<trigger_loads>
Based on analysis from Phase 1, load relevant capability modules:
- If ownership_field present → @capabilities/ownership-checks.md
- If custom capabilities → @capabilities/crud-capabilities.md
- Always → @capabilities/view-capabilities.md
</trigger_loads>

<access_pattern_selection>

## Pattern Decision Tree

```
Has ownership_field?
├── YES → "ownership_based"
│   └── Check admin override needed?
│       ├── YES → Admin sees all, users see own
│       └── NO → Strict ownership (rare)
├── NO
│   └── Public read needed?
│       ├── YES → "public_read"
│       │   └── Read: true, CUD: manage capability
│       └── NO → "admin_only"
│           └── All CRUD: manage capability
```

## Capability Naming Resolution

1. Use explicit from handoff if provided
2. Default pattern: `manage_{plural_entity_snake}`
3. Custom actions: preserve from handoff

</access_pattern_selection>

<output>
Pass to Phase 3:
```yaml
capability_design:
  primary_capability: "manage_services"
  access_pattern: "admin_only" | "ownership_based" | "public_read"
  ownership_field: null | "created_by" | "assigned_to"
  admin_override: true | false
  custom_methods:
    - name: "assign"
      capabilities: ["manage_contact_submissions", "edit_users"]
```
</output>
