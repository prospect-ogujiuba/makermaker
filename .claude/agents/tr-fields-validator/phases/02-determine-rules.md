# Phase 2: Determine Validation Rules

<purpose>
Map analyzed fields to appropriate validation rules.
</purpose>

<trigger_loads>
Based on field types from Phase 1:

- String fields → @rules/string-rules.md
- Numeric fields → @rules/numeric-rules.md
- Unique fields → @rules/unique-rules.md
- Required fields → @rules/required.md
- Custom validation → @rules/custom-rules.md

Based on column types:

- varchar/text → @types/string-defaults.md
- int/decimal → @types/numeric-defaults.md
- date/datetime → @types/date-defaults.md
- FK columns → @types/relationship-defaults.md
  </trigger_loads>

<rule_mapping>

## Standard Mapping

```
Field Type → Rules
───────────────────────────────────
varchar(N), non-null    → required|max:N
varchar(N), nullable    → ?max:N
varchar + unique        → unique:col:table@id:$id
bigint FK, non-null     → required|numeric
bigint FK, nullable     → ?numeric
tinyint (bool)          → numeric|min:0|max:1
ENUM                    → callback:checkInList:Model
TEXT                    → '' (empty)
JSON                    → '' (framework handles)
```

## Composition Order

1. Optional marker (?) if nullable
2. Unique rule if applicable
3. Required if non-nullable
4. Type-specific rules (max, numeric, etc.)

Example: `?unique:sku:table@id:$id|max:64`

</rule_mapping>

<output>
Pass to Phase 3:
```yaml
rules:
  sku: "?unique:sku:{$wpdb_prefix}prfx_equipment@id:{$id}|max:64"
  name: "unique:name:{$wpdb_prefix}prfx_equipment@id:{$id}|required|max:128"
  equipment_type_id: "required|numeric"
  status: "callback:checkInList:Equipment"
  description: ""
```
</output>
