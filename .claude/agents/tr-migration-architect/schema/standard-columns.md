# Standard Columns

## Required in Every Table

### Primary Key
```sql
`id` bigint(20) NOT NULL AUTO_INCREMENT,
```

### Optimistic Locking
```sql
`version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
```

### Timestamps
```sql
`created_at` datetime NOT NULL DEFAULT current_timestamp(),
`updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
`deleted_at` datetime DEFAULT NULL,
```

### Audit Users
```sql
`created_by` bigint(20) unsigned NOT NULL,
`updated_by` bigint(20) unsigned NOT NULL,
```

## Standard Indexes

```sql
KEY `idx_{table}__deleted_at` (`deleted_at`),
KEY `idx_{table}__created_by` (`created_by`),
KEY `idx_{table}__updated_by` (`updated_by`),
```

## Standard Foreign Keys

```sql
CONSTRAINT `fk_{table}__created_by` FOREIGN KEY (`created_by`)
  REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
CONSTRAINT `fk_{table}__updated_by` FOREIGN KEY (`updated_by`)
  REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
```

## Complete Template

```sql
`id` bigint(20) NOT NULL AUTO_INCREMENT,

-- Custom columns here --

`version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
`created_at` datetime NOT NULL DEFAULT current_timestamp(),
`updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
`deleted_at` datetime DEFAULT NULL,
`created_by` bigint(20) unsigned NOT NULL,
`updated_by` bigint(20) unsigned NOT NULL,
PRIMARY KEY (`id`),
```
