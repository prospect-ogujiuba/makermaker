# Database Migration Order

Based on the foreign key dependencies in the schema, here is the required migration order:

## Phase 1: Foundation Tables (No Dependencies)
These tables have no foreign key dependencies and must be created first:

1. **srvc_complexity** - Referenced by srvc_service
2. **srvc_pricing_model** - Referenced by srvc_service_price
3. **srvc_pricing_tier** - Referenced by srvc_service_price
4. **srvc_delivery_method** - Referenced by srvc_service_delivery_method
5. **srvc_coverage_area** - Referenced by srvc_service_coverage
6. **srvc_deliverable** - Referenced by srvc_service_deliverable
7. **srvc_equipment** - Referenced by srvc_service_equipment
8. **srvc_service_type** - Referenced by srvc_attribute_definition and srvc_service

## Phase 2: Hierarchical Tables
Tables that can reference themselves or other Phase 1 tables:

9. **srvc_category** - Self-referencing (parent_id), no other dependencies

## Phase 3: Dependent Foundation Tables
Tables that depend on Phase 1 or Phase 2 tables:

10. **srvc_attribute_definition** - References srvc_service_type
11. **srvc_bundle** - No dependencies (standalone)

## Phase 4: Core Entity Tables
Main service entity that depends on multiple foundation tables:

12. **srvc_service** - References:
    - srvc_category
    - srvc_service_type 
    - srvc_complexity

## Phase 5: Service-Related Junction/Detail Tables
All remaining tables that reference srvc_service:

13. **srvc_service_price** - References:
    - srvc_service
    - srvc_pricing_tier
    - srvc_pricing_model

14. **srvc_service_addon** - References:
    - srvc_service (both service_id and addon_service_id)

15. **srvc_service_attribute_value** - References:
    - srvc_service
    - srvc_attribute_definition

16. **srvc_service_category_secondary** - References:
    - srvc_service
    - srvc_category

17. **srvc_service_coverage** - References:
    - srvc_service
    - srvc_coverage_area

18. **srvc_service_deliverable** - References:
    - srvc_service
    - srvc_deliverable

19. **srvc_service_delivery_method** - References:
    - srvc_service
    - srvc_delivery_method

20. **srvc_service_equipment** - References:
    - srvc_service
    - srvc_equipment

21. **srvc_service_relation** - References:
    - srvc_service (both service_id and related_service_id)

22. **srvc_bundle_item** - References:
    - srvc_bundle
    - srvc_service

## Key Migration Considerations

### Self-Referencing Tables
- **srvc_category**: Has self-referencing foreign key (parent_id). Create table first, then add data starting with root categories.
- **srvc_service_addon**: References srvc_service twice. Ensure all services exist before creating addon relationships.
- **srvc_service_relation**: References srvc_service twice. Create after all services exist.

### Circular Dependencies
No true circular dependencies exist in this schema, but some tables have bidirectional relationships through junction tables.

### Data Population Order
After table creation, populate data in the same order, being especially careful with:
1. Root categories before child categories
2. All services before service relationships/addons
3. Service types before attribute definitions

### Triggers and Constraints
The schema includes several triggers for data validation:
- Category self-reference prevention
- Service addon self-reference prevention  
- Service relation self-reference prevention
- Price overlap validation

These triggers should be created after the table structure is in place but before data population.