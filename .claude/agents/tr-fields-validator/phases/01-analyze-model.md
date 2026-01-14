# Phase 1: Analyze Model Handoff

<purpose>
Parse model handoff from tr-model-builder to extract validation requirements.
</purpose>

<input>
model_handoff.yaml containing:
- class: Model class name
- fillable: Fields requiring validation
- guarded: Protected fields (skip validation)
- casts: Type casting definitions
- relationships: belongsTo associations (FK validation)
</input>

<extraction_tasks>

## 1. Identify Field Types

Map each fillable field to validation category:
- String fields → max length from schema
- Foreign keys → numeric validation
- Boolean/flags → numeric min:0 max:1
- ENUMs → callback:checkInList

## 2. Check Nullability

From schema information:
- Non-nullable → required rule
- Nullable → ? prefix (optional)

## 3. Extract Uniqueness Constraints

Fields needing unique validation:
- `sku` - typically optional unique
- `slug` - required unique
- `name` - often required unique
- `code` - config entities unique

## 4. Identify Relationships

From relationships array:
- belongsTo → FK needs numeric validation
- Check constraint for nullable vs required

</extraction_tasks>

<output>
Pass to Phase 2:
```yaml
analysis:
  entity: Equipment
  table: srvc_equipment
  fields:
    - name: sku
      type: varchar(64)
      nullable: true
      unique: true
    - name: name
      type: varchar(128)
      nullable: false
      unique: true
  foreign_keys:
    - field: equipment_type_id
      required: true
  enums:
    - field: status
      model: Equipment
```
</output>
