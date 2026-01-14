# Table Naming

## Pattern
All custom tables: `{!!prefix!!}srvc_{entity_plural}`

## Examples

| Entity | Plural | Table Name |
|--------|--------|------------|
| Service | services | `{!!prefix!!}srvc_services` |
| ServiceType | service_types | `{!!prefix!!}srvc_service_types` |
| Equipment | equipment | `{!!prefix!!}srvc_equipment` |
| ServicePrice | service_prices | `{!!prefix!!}srvc_service_prices` |

## Junction Tables (N:N)
Pattern: `{!!prefix!!}srvc_{entity1}_{entity2}`

| Relationship | Table Name |
|--------------|------------|
| Service ↔ Equipment | `{!!prefix!!}srvc_service_equipment` |
| Service ↔ Deliverable | `{!!prefix!!}srvc_service_deliverables` |

## Important

**Always use `{!!prefix!!}`** - never hard-code prefix.

The placeholder is replaced at runtime:
- `{!!prefix!!}` → `srvc_` (in production)

## WordPress Tables

Reference WordPress tables with same placeholder:
- `{!!prefix!!}users` - WordPress users table
- `{!!prefix!!}posts` - WordPress posts (for attachments)

Note: WordPress users table column is `ID` (uppercase).
