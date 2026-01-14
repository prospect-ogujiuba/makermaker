# Phase 2: Plan Tests

<purpose>
Determine which test categories and patterns needed.
</purpose>

<trigger_loads>
Based on component type from Phase 1:
- Model → @categories/model-tests.md
- Controller → @categories/controller-tests.md
- Policy → @categories/policy-tests.md
</trigger_loads>

<test_strategy>

## Unit vs Integration

**Unit Tests (no database):**
- Model attribute access
- Policy capability checks (mocked)
- Field validation rules structure
- Controller method existence

**Integration Tests (database):**
- Model CRUD operations
- Relationship loading
- Controller full request cycle

## Edge Cases to Include
- Null/empty inputs
- Invalid IDs (0, negative, non-existent)
- Unauthorized access attempts
- Boundary values

</test_strategy>

<output>
Pass to Phase 3:
```yaml
test_plan:
  unit_tests:
    - table_name
    - fillable_attributes
    - relationship_methods
  integration_tests:
    - crud_operations
    - eager_loading
  edge_cases:
    - null_values
    - invalid_ids
  mocks_required:
    - AuthUser
    - current_user_can
```
</output>
