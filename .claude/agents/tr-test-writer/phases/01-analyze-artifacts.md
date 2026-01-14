# Phase 1: Analyze Artifacts

<purpose>
Parse handoffs from artifact agents to identify what needs testing.
</purpose>

<input>
Handoff YAML specifying test target:
```yaml
test_target:
  type: model|controller|policy|fields
  entity: Equipment
  file: app/Models/Equipment.php
  methods_to_test:
    - findAll
    - findById
    - create
  relationships:
    - belongsTo: EquipmentType
    - hasMany: ServiceEquipment
```
</input>

<extraction_tasks>

## 1. Identify Component Type
- Model → test fillable, guarded, relationships
- Controller → test CRUD actions, authorization
- Policy → test capability checks
- Fields → test validation rules, sanitization

## 2. List Public Methods
Read source file, extract public method signatures.

## 3. Identify Dependencies
- Related models (for mocking)
- WordPress functions to mock
- TypeRocket base class methods

## 4. Note Relationships
For models, identify relationship methods to test.

</extraction_tasks>

<output>
Pass to Phase 2:
```yaml
analysis:
  entity: Equipment
  component: Model
  public_methods:
    - findAll
    - findById
    - equipmentType
  dependencies:
    - EquipmentType
  mock_needed:
    - current_user_can
```
</output>
