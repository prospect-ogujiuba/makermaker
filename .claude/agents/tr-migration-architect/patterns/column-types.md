# Column Type Mapping

## Text Types

| Purpose | SQL Type |
|---------|----------|
| Short text (name, code) | `varchar(64)` |
| Medium text (title) | `varchar(128)` |
| Long text (description) | `varchar(512)` or `text` |
| Rich content | `text` or `longtext` |
| SKU/Slug | `varchar(64)` |
| URL | `varchar(512)` |
| Email | `varchar(255)` |

## Numeric Types

| Purpose | SQL Type |
|---------|----------|
| Primary key | `bigint(20) NOT NULL AUTO_INCREMENT` |
| Foreign key | `bigint(20) NOT NULL` or `bigint(20) unsigned NOT NULL` |
| Integer | `int(11)` |
| Small integer | `smallint` |
| Boolean | `tinyint(1) NOT NULL DEFAULT 0` |
| Currency | `decimal(12,2)` |
| Quantity | `decimal(12,3)` |
| Percentage | `decimal(5,2)` |

## Date/Time Types

| Purpose | SQL Type |
|---------|----------|
| Timestamp | `datetime NOT NULL DEFAULT current_timestamp()` |
| Auto-update | `datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()` |
| Nullable date | `datetime DEFAULT NULL` |
| Date only | `date` |
| Time only | `time` |

## Special Types

| Purpose | SQL Type |
|---------|----------|
| ENUM | `enum('value1','value2','value3')` |
| JSON | `json DEFAULT NULL` |
| Currency code | `char(3) NOT NULL DEFAULT 'CAD'` |

## Examples

```sql
-- Short required text
`name` varchar(64) NOT NULL,

-- Long optional text
`description` text DEFAULT NULL,

-- SKU with unique
`sku` varchar(64) DEFAULT NULL,

-- Boolean flag
`is_active` tinyint(1) NOT NULL DEFAULT 1,

-- Currency
`price` decimal(12,2) DEFAULT NULL,

-- Enum status
`status` enum('draft','active','archived') NOT NULL DEFAULT 'draft',

-- JSON metadata
`metadata` json DEFAULT NULL COMMENT 'Additional attributes',
```
