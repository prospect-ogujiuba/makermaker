<staged_context>

## Context Loading Architecture

This workflow loads only relationship-specific reference sections.

## Stage 0: Skill Router

NO REFERENCES LOADED

- Determine relationship type from user intent

## Stage 1: Relationship Definition

NO REFERENCES LOADED

- Gather source entity, target entity, relationship type
- OUTPUT: relationship_handoff.yaml

## Stage 2: Migration Phase

CONTEXT LOADED:

- migration-templates.md#foreign-keys (for belongsTo, belongsToMany)
- relationship_handoff.yaml

## Stage 3: Model Phase

CONTEXT LOADED:

- typerocket-patterns.md#relationships (always)
- relationship_handoff.yaml
- migration_handoff.yaml

## Stage 4: Optional UI Updates

CONTEXT LOADED (if needed):

- Handoffs only - no references
  </staged_context>

<objective>
Add a relationship between two TypeRocket resources.
</objective>

<process>
## Stage 1: Define Relationship

Collect from user:

- **Source entity**: The entity getting the relationship method
- **Target entity**: The related entity
- **Relationship type**:
  - `belongsTo` - Source has foreign key to target (many-to-one)
  - `hasMany` - Target has foreign key to source (one-to-many)
  - `belongsToMany` - Junction table (many-to-many)
- **Cascade behavior**: SET NULL, CASCADE, RESTRICT

**Output relationship_handoff.yaml:**

```yaml
handoff:
  metadata:
    from_stage: "relationship-definition"
    to_stage: "migration-phase"
  schema:
    source_entity: "{Source}"
    target_entity: "{Target}"
    relationship_type: "belongsTo" | "hasMany" | "belongsToMany"
    cascade_delete: "SET NULL" | "CASCADE" | "RESTRICT"
  decisions:
    - decision: "Cascade behavior"
      rationale: "..."
      impact: ["..."]
```

<handoff_validation stage="relationship">
Required: source_entity, target_entity, relationship_type
Verify: Both entities exist as TypeRocket resources
</handoff_validation>

## Stage 2: Migration Phase

<discovery_trigger_evaluation>
Only load migration-templates.md#foreign-keys section.
No other migration sections needed for relationship changes.

Trigger conditions:

- relationship_type == "belongsTo" → Create FK on source table
- relationship_type == "belongsToMany" → Create junction table
- relationship_type == "hasMany" → No migration (inverse of belongsTo)
  </discovery_trigger_evaluation>

**For belongsTo:**

Invoke `tr-migration-architect` with:

```yaml
input_handoff: relationship_handoff.yaml
context_section: migration-templates.md#foreign-keys
migration_type: "add_foreign_key"
```

Output SQL:

```sql
ALTER TABLE prfx_{source}s
ADD COLUMN {target}_id BIGINT UNSIGNED NULL,
ADD INDEX idx_{target}_id ({target}_id),
ADD FOREIGN KEY fk_{source}_{target} ({target}_id)
    REFERENCES prfx_{target}s(id) ON DELETE {cascade};
```

**For hasMany:**
No migration on source. Verify target has the foreign key.

**For belongsToMany:**

Invoke `tr-migration-architect` with:

```yaml
input_handoff: relationship_handoff.yaml
context_section: migration-templates.md#foreign-keys
migration_type: "create_junction_table"
```

Output SQL:

```sql
CREATE TABLE prfx_{source}_{target} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    {source}_id BIGINT UNSIGNED NOT NULL,
    {target}_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_{source}_{target} ({source}_id, {target}_id),
    INDEX idx_{source}_id ({source}_id),
    INDEX idx_{target}_id ({target}_id),
    FOREIGN KEY fk_{source} ({source}_id) REFERENCES prfx_{source}s(id) ON DELETE CASCADE,
    FOREIGN KEY fk_{target} ({target}_id) REFERENCES prfx_{target}s(id) ON DELETE CASCADE
);
```

<handoff_validation stage="migration">
Required: migration_handoff.yaml with SQL definition
Verify: FK references valid existing table
</handoff_validation>

## Stage 3: Model Updates

<discovery_trigger_evaluation>
Only load typerocket-patterns.md#relationships section.
No other model sections needed.
</discovery_trigger_evaluation>

Update BOTH models:

Invoke `tr-model-builder` with:

```yaml
input_handoffs:
  - relationship_handoff.yaml
  - migration_handoff.yaml
context_section: typerocket-patterns.md#relationships
models_to_update:
  - source: "{Source}"
  - target: "{Target}"
```

**Source Model method:**

```php
public function {target}()
{
    return $this->belongsTo({Target}::class, '{target}_id');
}
```

**Target Model (inverse) method:**

```php
public function {source}s()
{
    return $this->hasMany({Source}::class, '{target}_id');
}
```

**For belongsToMany (both models):**

```php
public function {targets}()
{
    return $this->belongsToMany({Target}::class, 'prfx_{source}_{target}', '{source}_id', '{target}_id');
}
```

<handoff_validation stage="model">
Required: Both models updated with relationship methods
Verify: Method names follow naming convention (singular for belongsTo, plural for hasMany)
</handoff_validation>

## Stage 4: Optional UI Updates

<discovery_trigger_evaluation>
Only if user wants relationship in UI:

- Form: no reference, use ui_hints from fields_handoff
- Validation: no reference, handoff context sufficient
- REST: no reference, handoff context sufficient
  </discovery_trigger_evaluation>

If relationship should be:

- **Displayed in form**: Invoke tr-form-designer (no reference section needed)
- **Validated**: Invoke tr-fields-validator (no reference section needed)
- **Exposed in REST**: Invoke tr-controller-engineer (no reference section needed)

## Stage 5: Verification

1. Test relationship in both directions
2. Verify eager loading works (`$with` array if configured)
3. Test cascade delete behavior
4. Check REST API includes relationship data (if exposed)
   </process>

<relationship_matrix>
| Type | Source Method | Target Method | Migration Location | Context Loaded |
|------|---------------|---------------|-------------------|----------------|
| belongsTo | belongsTo() | hasMany() | Source table (FK) | migration-templates.md#foreign-keys, typerocket-patterns.md#relationships |
| hasMany | hasMany() | belongsTo() | Target table (FK) | typerocket-patterns.md#relationships only |
| belongsToMany | belongsToMany() | belongsToMany() | Junction table | migration-templates.md#foreign-keys, typerocket-patterns.md#relationships |
</relationship_matrix>

<handoff_templates>
Reference for relationship handoffs:

- handoffs/relationship_handoff.yaml.template
  </handoff_templates>

<success_criteria>

- [ ] Migration creates proper foreign key or junction table
- [ ] Source model has relationship method
- [ ] Target model has inverse relationship method
- [ ] Eager loading configured if needed ($with)
- [ ] Cascade behavior correct
- [ ] REST API includes related data (if requested)
      </success_criteria>
