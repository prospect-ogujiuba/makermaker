<overview>
SQL migration templates for TypeRocket/MakerMaker. All migrations are raw SQL files.
</overview>

<toc>
- #standard-table-template
- #field-type-mappings
- #index-strategies
- #foreign-key-patterns
- #junction-table-template
- #alter-table-patterns
- #migration-naming
</toc>

<!-- SECTION LOADING TRIGGERS:
  - #standard-table-template: Load when creating new entity migration
  - #field-type-mappings: Load when requirements_handoff.fields contains types to map
  - #index-strategies: Load when migration_handoff.indexes is populated
  - #foreign-key-patterns: Load when requirements_handoff.relationships contains belongsTo
  - #junction-table-template: Load when requirements_handoff.relationships contains belongsToMany
  - #alter-table-patterns: Load when modifying existing tables
  - #migration-naming: Always load for file naming
-->

<section id="standard-table-template">
## Standard Table Creation

```sql
CREATE TABLE IF NOT EXISTS {!!prefix!!}{plural_entity} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Entity-specific fields here
    sku VARCHAR(64) NULL,
    slug VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,

    -- Foreign keys (if any)
    parent_id BIGINT UNSIGNED NULL,

    -- Status and metadata
    is_active TINYINT(1) DEFAULT 1,
    metadata JSON NULL,

    -- Audit fields (REQUIRED ON ALL TABLES)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at DATETIME NULL,
    version INT UNSIGNED DEFAULT 1,

    -- Indexes
    UNIQUE KEY unique_sku (sku),
    UNIQUE KEY unique_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_parent_id (parent_id),

    -- Foreign key constraints
    FOREIGN KEY fk_{entity}_parent (parent_id)
        REFERENCES {!!prefix!!}{plural_entity}(id) ON DELETE SET NULL
);
```

**Prefix placeholder:** `{!!prefix!!}` is replaced with `prfx_` at runtime.

</section>

<section id="field-type-mappings">
## PHP to SQL Type Mappings

| PHP Type        | SQL Type        | Notes             |
| --------------- | --------------- | ----------------- |
| string (short)  | VARCHAR(64)     | SKU, slug, code   |
| string (medium) | VARCHAR(255)    | Names, titles     |
| string (long)   | TEXT            | Descriptions      |
| int             | INT             | Regular integers  |
| int (id)        | BIGINT UNSIGNED | Foreign keys, IDs |
| float           | DECIMAL(10,2)   | Currency, prices  |
| bool            | TINYINT(1)      | Flags, is_active  |
| datetime        | DATETIME        | Timestamps        |
| json            | JSON            | Metadata, config  |
| enum            | VARCHAR(32)     | Status values     |

</section>

<section id="index-strategies">
## Index Strategies

**Always index:**

- Primary key (automatic)
- Foreign keys
- Fields used in WHERE clauses
- Fields used in ORDER BY
- `deleted_at` (soft delete queries)
- `is_active` (status filtering)

**Index types:**

```sql
-- Unique constraint
UNIQUE KEY unique_sku (sku)

-- Regular index
INDEX idx_status (status)

-- Composite index (order matters)
INDEX idx_status_created (status, created_at)

-- Foreign key index
INDEX idx_parent_id (parent_id)
```

**Composite index rule:** Put equality conditions first, then range conditions.

</section>

<section id="foreign-key-patterns">
## Foreign Key Patterns

**belongsTo (many-to-one):**

```sql
-- Add column and FK to child table
{target}_id BIGINT UNSIGNED NULL,
INDEX idx_{target}_id ({target}_id),
FOREIGN KEY fk_{entity}_{target} ({target}_id)
    REFERENCES {!!prefix!!}{targets}(id) ON DELETE SET NULL
```

**ON DELETE options:**

- `SET NULL` - Keep record, clear relationship (default for optional)
- `CASCADE` - Delete dependent records (use for required relationships)
- `RESTRICT` - Prevent delete if dependents exist
</section>

<section id="junction-table-template">
## Junction Table (belongsToMany)

```sql
CREATE TABLE IF NOT EXISTS {!!prefix!!}{source}_{target} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    {source}_id BIGINT UNSIGNED NOT NULL,
    {target}_id BIGINT UNSIGNED NOT NULL,

    -- Optional: junction-specific fields
    quantity INT UNSIGNED DEFAULT 1,
    notes TEXT NULL,
    sort_order INT DEFAULT 0,

    -- Audit (minimal for junction)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Prevent duplicates
    UNIQUE KEY unique_{source}_{target} ({source}_id, {target}_id),

    -- Foreign keys with CASCADE (junction records should die with parents)
    INDEX idx_{source}_id ({source}_id),
    INDEX idx_{target}_id ({target}_id),
    FOREIGN KEY fk_jct_{source} ({source}_id)
        REFERENCES {!!prefix!!}{sources}(id) ON DELETE CASCADE,
    FOREIGN KEY fk_jct_{target} ({target}_id)
        REFERENCES {!!prefix!!}{targets}(id) ON DELETE CASCADE
);
```

</section>

<section id="alter-table-patterns">
## ALTER TABLE Patterns

**Add column:**

```sql
ALTER TABLE {!!prefix!!}{table}
ADD COLUMN new_field VARCHAR(255) NULL AFTER existing_field;
```

**Add index:**

```sql
ALTER TABLE {!!prefix!!}{table}
ADD INDEX idx_new_field (new_field);
```

**Add foreign key:**

```sql
ALTER TABLE {!!prefix!!}{table}
ADD COLUMN {target}_id BIGINT UNSIGNED NULL,
ADD INDEX idx_{target}_id ({target}_id),
ADD FOREIGN KEY fk_{table}_{target} ({target}_id)
    REFERENCES {!!prefix!!}{targets}(id) ON DELETE SET NULL;
```

**Drop column (careful!):**

```sql
ALTER TABLE {!!prefix!!}{table}
DROP COLUMN old_field;
```

</section>

<section id="migration-naming">
## Migration File Naming

Pattern: `{timestamp}.{action}_{table}_{description}.sql`

Examples:

- `1736000001.create_services_table.sql`
- `1736000002.create_service_categories_table.sql`
- `1736000003.add_parent_id_to_service_categories.sql`
- `2736000001.insert_default_service_types.sql`
- `3736000001.create_service_summary_view.sql`

Prefix meanings:

- `1xxx...` - Schema creation/modification
- `2xxx...` - Data seeding
- `3xxx...` - Views and procedures
</section>
