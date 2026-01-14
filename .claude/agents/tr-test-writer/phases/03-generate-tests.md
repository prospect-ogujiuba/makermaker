# Phase 3: Generate Tests

<purpose>
Generate Pest test files from test plan.
</purpose>

<trigger_loads>
Based on test plan from Phase 2:
- Always → @patterns/test-structure.md
- Always → @patterns/assertions.md
- If integration tests → @patterns/fixtures.md
</trigger_loads>

<output_structure>

## File Location
`tests/{Component}/{Entity}Test.php`

Examples:
- `tests/Models/EquipmentTest.php`
- `tests/Controllers/EquipmentControllerTest.php`
- `tests/Auth/EquipmentPolicyTest.php`

## Test Organization
```php
describe('{Entity} {Component}', function () {
    beforeEach(function () {
        // Setup
    });

    describe('feature group', function () {
        it('test case', function () {
            // Assertion
        });
    });
});
```

</output_structure>

<handoff_output>
```yaml
tests:
  entity: Equipment
  component: Model
  file: tests/Models/EquipmentTest.php
  test_count: 12
  coverage:
    - table_name
    - fillable_attributes
  requires_database: false
next_step: code-reviewer
```
</handoff_output>
