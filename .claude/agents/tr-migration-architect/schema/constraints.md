# CHECK Constraints

## Naming Pattern
`chk_{table}__{rule}`

## Common Patterns

### Positive Values
```sql
CONSTRAINT `chk_{table}__positive_price` CHECK (`price` > 0),
```

### Non-Negative Values
```sql
CONSTRAINT `chk_{table}__non_negative_quantity` CHECK (`quantity` >= 0),
```

### Nullable with Constraint
```sql
CONSTRAINT `chk_{table}__non_negative_cost` CHECK (`unit_cost` IS NULL OR `unit_cost` >= 0),
```

### Range Validation
```sql
CONSTRAINT `chk_{table}__valid_range` CHECK (`max_value` IS NULL OR `max_value` >= `min_value`),
```

### Date Range
```sql
CONSTRAINT `chk_{table}__valid_dates` CHECK (`end_date` IS NULL OR `end_date` > `start_date`),
```

### Bounded Range
```sql
CONSTRAINT `chk_{table}__valid_quantity` CHECK (`quantity` > 0 AND `quantity` <= 10000),
```

### Percentage
```sql
CONSTRAINT `chk_{table}__valid_percent` CHECK (`discount_percent` >= 0 AND `discount_percent` <= 100),
```

## When to Use CHECK

**Good for:**
- Positive/non-negative values
- Valid ranges (min ≤ max)
- Date ordering
- Bounded values

**Prefer ENUM for:**
- Limited value sets (status, type)
- Known categorical values

## Placement

CHECK constraints go after indexes, before foreign keys:
```sql
  -- Indexes
  KEY `idx_...` (...),

  -- Check constraints
  CONSTRAINT `chk_...` CHECK (...),

  -- Foreign keys
  CONSTRAINT `fk_...` FOREIGN KEY ...
```
