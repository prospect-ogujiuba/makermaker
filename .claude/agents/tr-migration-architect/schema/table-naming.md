# Table Naming

## Pattern
All custom tables: `{!!prefix!!}{entity_plural}`

## Examples

| Entity | Plural | Table Name |
|--------|--------|------------|
| Service | services | `{!!prefix!!}services` |
| ServiceType | service_types | `{!!prefix!!}service_types` |
| Equipment | equipment | `{!!prefix!!}equipment` |
| ServicePrice | service_prices | `{!!prefix!!}service_prices` |

## Junction Tables (N:N)
Pattern: `{!!prefix!!}{entity1}_{entity2}`

| Relationship | Table Name |
|--------------|------------|
| Service ↔ Equipment | `{!!prefix!!}service_equipment` |
| Service ↔ Deliverable | `{!!prefix!!}service_deliverables` |

## Important

**Always use `{!!prefix!!}`** - never hard-code prefix.

The placeholder is replaced at runtime:
- `{!!prefix!!}` → `` (in production)

## WordPress Tables

Reference WordPress tables with same placeholder:
- `{!!prefix!!}users` - WordPress users table
- `{!!prefix!!}posts` - WordPress posts (for attachments)

Note: WordPress users table column is `ID` (uppercase).
