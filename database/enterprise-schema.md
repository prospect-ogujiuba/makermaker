# Complete Target Schema - Service System Database

## Core Lookup Tables

### 1. Service Complexities
```sql
-- 1756191393.create_service_complexities_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_complexities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `level` tinyint unsigned NOT NULL DEFAULT 0,
  `price_multiplier` decimal(8,2) NOT NULL DEFAULT 1.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_complexity__name` (`name`),
  UNIQUE KEY `uq_complexity__level` (`level`),
  KEY `idx_complexity__level` (`level`),
  KEY `idx_complexity__deleted_at` (`deleted_at`),
  KEY `idx_complexity__created_by` (`created_by`),
  KEY `idx_complexity__updated_by` (`updated_by`),
  CONSTRAINT `chk_complexity__positive_multiplier` CHECK (`price_multiplier` > 0 AND `price_multiplier` <= 10.00),
  CONSTRAINT `chk_complexity__valid_level` CHECK (`level` >= 0 AND `level` <= 20),
  CONSTRAINT `fk_complexity__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_complexity__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service complexity levels with price multipliers - CHECK constraints added for valid ranges';
```

### 2. Pricing Models
```sql
-- 1756230789.create_service_pricing_models_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_pricing_models` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_time_based` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_model__name` (`name`),
  UNIQUE KEY `uq_pricing_model__code` (`code`),
  KEY `idx_pricing_model__deleted_at` (`deleted_at`),
  KEY `idx_pricing_model__is_time_based` (`is_time_based`),
  KEY `idx_pricing_model__created_by` (`created_by`),
  KEY `idx_pricing_model__updated_by` (`updated_by`),
  CONSTRAINT `fk_pricing_model__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pricing_model__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pricing models with time-based flag and descriptions';
```

### 3. Pricing Tiers
```sql
-- 1756251629.create_service_pricing_tiers_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_pricing_tiers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT 0,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `min_volume` int unsigned DEFAULT NULL,
  `max_volume` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_tier__name` (`name`),
  UNIQUE KEY `uq_pricing_tier__code` (`code`),
  KEY `idx_pricing_tier__sort_order` (`sort_order`),
  KEY `idx_pricing_tier__deleted_at` (`deleted_at`),
  KEY `idx_pricing_tier__created_by` (`created_by`),
  KEY `idx_pricing_tier__updated_by` (`updated_by`),
  CONSTRAINT `chk_pricing_tier__valid_discount` CHECK (`discount_pct` >= 0 AND `discount_pct` <= 100.00),
  CONSTRAINT `chk_pricing_tier__volume_range` CHECK (`min_volume` IS NULL OR `max_volume` IS NULL OR `min_volume` <= `max_volume`),
  CONSTRAINT `fk_pricing_tier__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pricing_tier__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pricing tiers with volume ranges and default discounts';
```

### 4. Currency Exchange Rates (NEW)
```sql
-- NEW TABLE: Currency support for multi-currency pricing
CREATE TABLE `{!!prefix!!}srvc_currency_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `from_currency` char(3) NOT NULL,
  `to_currency` char(3) NOT NULL,
  `exchange_rate` decimal(10,6) NOT NULL,
  `effective_date` date NOT NULL,
  `source` varchar(64) DEFAULT 'manual',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_currency_rate__currencies_date` (`from_currency`, `to_currency`, `effective_date`),
  KEY `idx_currency_rate__from_currency` (`from_currency`),
  KEY `idx_currency_rate__to_currency` (`to_currency`),
  KEY `idx_currency_rate__effective_date` (`effective_date`),
  CONSTRAINT `chk_currency_rate__valid_from_currency` CHECK (`from_currency` REGEXP '^[A-Z]{3}$'),
  CONSTRAINT `chk_currency_rate__valid_to_currency` CHECK (`to_currency` REGEXP '^[A-Z]{3}$'),
  CONSTRAINT `chk_currency_rate__positive_rate` CHECK (`exchange_rate` > 0),
  CONSTRAINT `chk_currency_rate__no_same_currency` CHECK (`from_currency` != `to_currency`),
  CONSTRAINT `fk_currency_rate__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_currency_rate__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Currency exchange rates with effective dating';
```

## Service Structure Tables

### 5. Service Categories
```sql
-- 1756312227.create_service_categories_table.sql (IMPROVED - NO TRIGGERS)
CREATE TABLE `{!!prefix!!}srvc_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category__name` (`name`),
  UNIQUE KEY `uq_category__slug` (`slug`),
  KEY `idx_category__parent_id` (`parent_id`),
  KEY `idx_category__sort_order` (`sort_order`),
  KEY `idx_category__is_active` (`is_active`),
  KEY `idx_category__deleted_at` (`deleted_at`),
  KEY `idx_category__created_by` (`created_by`),
  KEY `idx_category__updated_by` (`updated_by`),
  CONSTRAINT `chk_category__no_self_reference` CHECK (`parent_id` IS NULL OR `parent_id` != `id`),
  CONSTRAINT `fk_category__parent` FOREIGN KEY (`parent_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_category__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_category__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hierarchical service categorization - self-reference check via constraint, not trigger';
```

### 6. Service Types
```sql
-- 1756309963.create_service_types_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_service_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_site_visit` tinyint(1) NOT NULL DEFAULT 0,
  `supports_remote` tinyint(1) NOT NULL DEFAULT 1,
  `estimated_duration_hours` decimal(6,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_type__name` (`name`),
  UNIQUE KEY `uq_service_type__code` (`code`),
  KEY `idx_service_type__requires_site_visit` (`requires_site_visit`),
  KEY `idx_service_type__supports_remote` (`supports_remote`),
  KEY `idx_service_type__deleted_at` (`deleted_at`),
  KEY `idx_service_type__created_by` (`created_by`),
  KEY `idx_service_type__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_type__positive_duration` CHECK (`estimated_duration_hours` IS NULL OR `estimated_duration_hours` > 0),
  CONSTRAINT `fk_service_type__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_type__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service types with delivery characteristics';
```

### 7. Services (Core Table)
```sql
-- 1756346166.create_services_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) DEFAULT NULL,
  `slug` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `long_desc` text DEFAULT NULL,
  `category_id` bigint(20) NOT NULL,
  `service_type_id` bigint(20) NOT NULL,
  `complexity_id` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `minimum_quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `maximum_quantity` decimal(12,3) DEFAULT NULL,
  `estimated_hours` decimal(8,2) DEFAULT NULL,
  `skill_level` enum('entry','intermediate','advanced','expert','specialist') DEFAULT NULL,
  `metadata` json DEFAULT NULL COMMENT 'Additional service configuration properties',
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service__slug` (`slug`),
  UNIQUE KEY `uq_service__sku` (`sku`),
  KEY `idx_service__category_id` (`category_id`),
  KEY `idx_service__service_type_id` (`service_type_id`),
  KEY `idx_service__complexity_id` (`complexity_id`),
  KEY `idx_service__is_active` (`is_active`),
  KEY `idx_service__is_featured` (`is_featured`),
  KEY `idx_service__deleted_at` (`deleted_at`),
  KEY `idx_service__created_by` (`created_by`),
  KEY `idx_service__updated_by` (`updated_by`),
  KEY `idx_service__sku` (`sku`),
  KEY `idx_service__slug` (`slug`),
  KEY `idx_service__active_lookup` (`is_active`, `category_id`, `service_type_id`, `deleted_at`),
  CONSTRAINT `chk_service__positive_min_quantity` CHECK (`minimum_quantity` > 0),
  CONSTRAINT `chk_service__valid_quantity_range` CHECK (`maximum_quantity` IS NULL OR `maximum_quantity` >= `minimum_quantity`),
  CONSTRAINT `chk_service__positive_estimated_hours` CHECK (`estimated_hours` IS NULL OR `estimated_hours` > 0),
  CONSTRAINT `fk_service__category` FOREIGN KEY (`category_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__complexity` FOREIGN KEY (`complexity_id`) REFERENCES `{!!prefix!!}srvc_complexities` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `{!!prefix!!}srvc_service_types` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Core service catalog with quantity constraints and promoted metadata fields';
```

## Pricing Tables (With Effective Dating)

### 8. Service Prices (With History Support)
```sql
-- 1756411355.create_service_prices_table.sql (MAJOR IMPROVEMENTS)
CREATE TABLE `{!!prefix!!}srvc_service_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `pricing_tier_id` bigint(20) NOT NULL,
  `pricing_model_id` bigint(20) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'CAD',
  `amount` decimal(12,2) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT 'Pricing unit: hour, user, device, site, month',
  `setup_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `valid_from` datetime NOT NULL DEFAULT current_timestamp(),
  `valid_to` datetime DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 1,
  `approval_status` enum('draft','pending','approved','rejected') NOT NULL DEFAULT 'draft',
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `notes` json DEFAULT NULL COMMENT 'Pricing configuration properties and approval notes',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_price__current_pricing` (`service_id`,`pricing_tier_id`,`pricing_model_id`,`is_current`) USING BTREE,
  KEY `idx_service_price__service_id` (`service_id`),
  KEY `idx_service_price__pricing_tier_id` (`pricing_tier_id`),
  KEY `idx_service_price__pricing_model_id` (`pricing_model_id`),
  KEY `idx_service_price__currency` (`currency`),
  KEY `idx_service_price__validity` (`service_id`, `valid_from`, `valid_to`, `deleted_at`),
  KEY `idx_service_price__approval_status` (`approval_status`),
  KEY `idx_service_price__deleted_at` (`deleted_at`),
  KEY `idx_service_price__created_by` (`created_by`),
  KEY `idx_service_price__updated_by` (`updated_by`),
  KEY `idx_service_price__approved_by` (`approved_by`),
  CONSTRAINT `chk_service_price__valid_currency` CHECK (`currency` REGEXP '^[A-Z]{3}$'),
  CONSTRAINT `chk_service_price__non_negative_amount` CHECK (`amount` IS NULL OR `amount` >= 0),
  CONSTRAINT `chk_service_price__non_negative_setup_fee` CHECK (`setup_fee` >= 0),
  CONSTRAINT `chk_service_price__valid_date_range` CHECK (`valid_to` IS NULL OR `valid_to` > `valid_from`),
  CONSTRAINT `chk_service_price__approved_logic` CHECK ((`approval_status` = 'approved' AND `approved_by` IS NOT NULL) OR (`approval_status` != 'approved')),
  CONSTRAINT `fk_service_price__pricing_model` FOREIGN KEY (`pricing_model_id`) REFERENCES `{!!prefix!!}srvc_pricing_models` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__pricing_tier` FOREIGN KEY (`pricing_tier_id`) REFERENCES `{!!prefix!!}srvc_pricing_tiers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__approved_by` FOREIGN KEY (`approved_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service pricing with effective dating, approval workflow, and validation constraints';
```

### 9. Service Price History (NEW)
```sql
-- NEW TABLE: Track all pricing changes for audit and compliance
CREATE TABLE `{!!prefix!!}srvc_service_price_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_price_id` bigint(20) NOT NULL,
  `change_type` enum('created','amount_changed','dates_changed','status_changed','deleted') NOT NULL,
  `old_amount` decimal(12,2) DEFAULT NULL,
  `new_amount` decimal(12,2) DEFAULT NULL,
  `old_setup_fee` decimal(12,2) DEFAULT NULL,
  `new_setup_fee` decimal(12,2) DEFAULT NULL,
  `old_valid_from` datetime DEFAULT NULL,
  `new_valid_from` datetime DEFAULT NULL,
  `old_valid_to` datetime DEFAULT NULL,
  `new_valid_to` datetime DEFAULT NULL,
  `old_approval_status` enum('draft','pending','approved','rejected') DEFAULT NULL,
  `new_approval_status` enum('draft','pending','approved','rejected') DEFAULT NULL,
  `change_reason` varchar(512) DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  `changed_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_price_history__service_price_id` (`service_price_id`),
  KEY `idx_price_history__change_type` (`change_type`),
  KEY `idx_price_history__changed_at` (`changed_at`),
  KEY `idx_price_history__changed_by` (`changed_by`),
  CONSTRAINT `fk_price_history__service_price` FOREIGN KEY (`service_price_id`) REFERENCES `{!!prefix!!}srvc_service_prices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_price_history__changed_by` FOREIGN KEY (`changed_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Complete audit trail for pricing changes';
```

## Service Relationship Tables

### 10. Service Addons (Improved)
```sql
-- 1756480611.create_service_addons_table.sql (IMPROVED - NO TRIGGERS)
CREATE TABLE `{!!prefix!!}srvc_service_addons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `addon_service_id` bigint(20) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `min_qty` decimal(12,3) NOT NULL DEFAULT 0.000,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `default_qty` decimal(12,3) NOT NULL DEFAULT 1.000,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_addon` (`service_id`, `addon_service_id`),
  KEY `idx_service_addons__service_id` (`service_id`),
  KEY `idx_service_addons__addon_service_id` (`addon_service_id`),
  KEY `idx_service_addons__required` (`required`),
  KEY `idx_service_addons__deleted_at` (`deleted_at`),
  KEY `idx_service_addons__created_by` (`created_by`),
  KEY `idx_service_addons__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_addon__no_self_reference` CHECK (`service_id` != `addon_service_id`),
  CONSTRAINT `chk_service_addon__valid_quantities` CHECK (`min_qty` >= 0 AND (`max_qty` IS NULL OR `max_qty` >= `min_qty`) AND `default_qty` >= `min_qty` AND (`max_qty` IS NULL OR `default_qty` <= `max_qty`)),
  CONSTRAINT `fk_service_addon__addon_service` FOREIGN KEY (`addon_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_addon__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_addon__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_addon__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service addons with quantity validation via CHECK constraints, not triggers';
```

### 11. Service Relationships (Improved)
```sql
-- 1756667496.create_service_relationships_table.sql (IMPROVED - NO TRIGGERS)
CREATE TABLE `{!!prefix!!}srvc_service_relationships` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `related_service_id` bigint(20) NOT NULL,
  `relation_type` enum('prerequisite','dependency','incompatible_with','substitute_for','complements','replaces','requires','enables','conflicts_with') NOT NULL,
  `strength` enum('weak','moderate','strong','critical') NOT NULL DEFAULT 'moderate',
  `notes` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_relationship` (`service_id`,`related_service_id`,`relation_type`),
  KEY `idx_service_relationship__related_service_id` (`related_service_id`),
  KEY `idx_service_relationship__relation_type` (`relation_type`),
  KEY `idx_service_relationship__strength` (`strength`),
  KEY `idx_service_relationship__deleted_at` (`deleted_at`),
  KEY `idx_service_relationship__created_by` (`created_by`),
  KEY `idx_service_relationship__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_relationship__no_self_reference` CHECK (`service_id` != `related_service_id`),
  CONSTRAINT `fk_service_relationship__related_service` FOREIGN KEY (`related_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service relationships with strength indicators and CHECK constraint for self-reference';
```

## Support Tables (Equipment, Coverage, etc.)

### 12. Equipment
```sql
-- 1756309210.create_service_equipment_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_equipment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `manufacturer` varchar(64) NOT NULL,
  `model` varchar(64) DEFAULT NULL,
  `category` varchar(64) DEFAULT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `is_consumable` tinyint(1) NOT NULL DEFAULT 0,
  `specs` json DEFAULT NULL COMMENT 'Equipment specifications as JSON object',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment__name` (`name`),
  UNIQUE KEY `uq_equipment__sku` (`sku`),
  KEY `idx_equipment__manufacturer` (`manufacturer`),
  KEY `idx_equipment__category` (`category`),
  KEY `idx_equipment__is_consumable` (`is_consumable`),
  KEY `idx_equipment__deleted_at` (`deleted_at`),
  KEY `idx_equipment__created_at` (`created_at`),
  KEY `idx_equipment__updated_at` (`updated_at`),
  CONSTRAINT `chk_equipment__positive_unit_cost` CHECK (`unit_cost` IS NULL OR `unit_cost` >= 0),
  CONSTRAINT `fk_equipment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_equipment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Equipment catalog with cost tracking and consumable flag';
```

### 13. Service Equipment Assignments (Improved)
```sql
-- 1756666767.create_service_equipment_assignments_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_service_equipment_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `equipment_id` bigint(20) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 1,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `quantity_unit` varchar(16) DEFAULT 'each',
  `cost_included` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether equipment cost is included in service price',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_equipment_assignment` (`service_id`,`equipment_id`),
  KEY `idx_service_equipment_assignment__equipment_id` (`equipment_id`),
  KEY `idx_service_equipment_assignment__required` (`required`),
  KEY `idx_service_equipment_assignment__deleted_at` (`deleted_at`),
  KEY `idx_service_equipment_assignment__created_by` (`created_by`),
  KEY `idx_service_equipment_assignment__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_equipment_assignment__positive_quantity` CHECK (`quantity` > 0 AND `quantity` <= 10000),
  CONSTRAINT `fk_service_equipment_assignment__equipment` FOREIGN KEY (`equipment_id`) REFERENCES `{!!prefix!!}srvc_equipment` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment_assignment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment_assignment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment_assignment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Equipment requirements for services with quantity validation and cost tracking';
```

### 14. Delivery Methods
```sql
-- 1756252687.create_service_delivery_methods_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_delivery_methods` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_site_access` tinyint(1) NOT NULL DEFAULT 0,
  `supports_remote` tinyint(1) NOT NULL DEFAULT 1,
  `default_lead_time_days` int unsigned DEFAULT 0,
  `default_sla_hours` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_delivery_method__name` (`name`),
  UNIQUE KEY `uq_delivery_method__code` (`code`),
  KEY `idx_delivery_method__requires_site_access` (`requires_site_access`),
  KEY `idx_delivery_method__supports_remote` (`supports_remote`),
  KEY `idx_delivery_method__deleted_at` (`deleted_at`),
  KEY `idx_delivery_method__created_by` (`created_by`),
  KEY `idx_delivery_method__updated_by` (`updated_by`),
  CONSTRAINT `fk_delivery_method__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_method__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Delivery methods with default timing and access requirements';
```

### 15. Service Delivery Method Assignments
```sql
-- 1756666041.create_service_delivery_method_assignments_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_service_delivery_method_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `delivery_method_id` bigint(20) NOT NULL,
  `lead_time_days` int unsigned NOT NULL DEFAULT 0,
  `sla_hours` int unsigned DEFAULT NULL,
  `surcharge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_delivery_method` (`service_id`,`delivery_method_id`),
  KEY `idx_service_delivery_method__delivery_method_id` (`delivery_method_id`),
  KEY `idx_service_delivery_method__is_default` (`is_default`),
  KEY `idx_service_delivery_method__deleted_at` (`deleted_at`),
  KEY `idx_service_delivery_method__created_by` (`created_by`),
  KEY `idx_service_delivery_method__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_delivery_method__non_negative_surcharge` CHECK (`surcharge` >= 0),
  CONSTRAINT `fk_service_delivery_method__delivery_method` FOREIGN KEY (`delivery_method_id`) REFERENCES `{!!prefix!!}srvc_delivery_methods` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Delivery methods for services with timing and cost overrides';
```

### 16. Coverage Areas
```sql
-- 1756253851.create_service_coverage_areas_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_coverage_areas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `country_code` char(2) DEFAULT NULL,
  `region_type` enum('city','province','state','country','continent','global') DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `postal_code_pattern` varchar(32) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coverage_area__code` (`code`),
  KEY `idx_coverage_area__name` (`name`),
  KEY `idx_coverage_area__country_code` (`country_code`),
  KEY `idx_coverage_area__region_type` (`region_type`),
  KEY `idx_coverage_area__deleted_at` (`deleted_at`),
  KEY `idx_coverage_area__created_by` (`created_by`),
  KEY `idx_coverage_area__updated_by` (`updated_by`),
  CONSTRAINT `chk_coverage_area__valid_country_code` CHECK (`country_code` IS NULL OR `country_code` REGEXP '^[A-Z]{2}),
  CONSTRAINT `fk_coverage_area__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_coverage_area__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Geographic coverage areas with enhanced location data';
```

### 17. Service Coverage
```sql
-- 1756518066.create_service_coverage_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_service_coverage` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `coverage_area_id` bigint(20) NOT NULL,
  `delivery_surcharge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `lead_time_adjustment_days` int NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_coverage` (`service_id`,`coverage_area_id`),
  KEY `idx_service_coverage__coverage_area_id` (`coverage_area_id`),
  KEY `idx_service_coverage__service_id` (`service_id`),
  KEY `idx_service_coverage__deleted_at` (`deleted_at`),
  KEY `idx_service_coverage__created_by` (`created_by`),
  KEY `idx_service_coverage__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_coverage__non_negative_surcharge` CHECK (`delivery_surcharge` >= 0),
  CONSTRAINT `fk_service_coverage__coverage_area` FOREIGN KEY (`coverage_area_id`) REFERENCES `{!!prefix!!}srvc_coverage_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service coverage areas with delivery adjustments';
```

### 18. Deliverables
```sql
-- 1756308065.create_service_deliverables_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_deliverables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `deliverable_type` enum('document','software','hardware','service','training','report') NOT NULL DEFAULT 'document',
  `template_path` varchar(255) DEFAULT NULL,
  `estimated_effort_hours` decimal(6,2) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_deliverable__name` (`name`),
  KEY `idx_deliverable__deliverable_type` (`deliverable_type`),
  KEY `idx_deliverable__requires_approval` (`requires_approval`),
  KEY `idx_deliverable__deleted_at` (`deleted_at`),
  KEY `idx_deliverable__created_at` (`created_at`),
  KEY `idx_deliverable__updated_at` (`updated_at`),
  CONSTRAINT `chk_deliverable__positive_effort` CHECK (`estimated_effort_hours` IS NULL OR `estimated_effort_hours` > 0),
  CONSTRAINT `fk_deliverable__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_deliverable__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service deliverables with type classification and effort tracking';
```

### 19. Service Deliverable Assignments
```sql
-- 1756660896.create_service_deliverable_assignments_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_service_deliverable_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,  
  `service_id` bigint(20) NOT NULL,
  `deliverable_id` bigint(20) NOT NULL,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0,
  `sequence_order` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_deliverable_assignment` (`service_id`,`deliverable_id`),
  KEY `idx_service_deliverable_assignment__deliverable_id` (`deliverable_id`),
  KEY `idx_service_deliverable_assignment__is_optional` (`is_optional`),
  KEY `idx_service_deliverable_assignment__sequence_order` (`sequence_order`),
  KEY `idx_service_deliverable_assignment__deleted_at` (`deleted_at`),
  KEY `idx_service_deliverable_assignment__created_by` (`created_by`),
  KEY `idx_service_deliverable_assignment__updated_by` (`updated_by`),
  CONSTRAINT `fk_service_deliverable_assignment__deliverable` FOREIGN KEY (`deliverable_id`) REFERENCES `{!!prefix!!}srvc_deliverables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service deliverable assignments with sequencing';
```

## Bundle Tables

### 20. Service Bundles
```sql
-- 1756345584.create_service_bundles_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_bundles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `long_desc` text DEFAULT NULL,
  `bundle_type` enum('package','collection','suite','solution') NOT NULL DEFAULT 'package',
  `total_discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `min_services` int unsigned DEFAULT NULL,
  `max_services` int unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bundle__slug` (`slug`),
  KEY `idx_bundle__name` (`name`),
  KEY `idx_bundle__bundle_type` (`bundle_type`),
  KEY `idx_bundle__is_active` (`is_active`),
  KEY `idx_bundle__validity` (`valid_from`, `valid_to`),
  KEY `idx_bundle__deleted_at` (`deleted_at`),
  KEY `idx_bundle__created_by` (`created_by`),
  KEY `idx_bundle__updated_by` (`updated_by`),
  CONSTRAINT `chk_bundle__valid_discount` CHECK (`total_discount_pct` >= 0 AND `total_discount_pct` <= 100.00),
  CONSTRAINT `chk_bundle__valid_service_count` CHECK (`min_services` IS NULL OR `max_services` IS NULL OR `min_services` <= `max_services`),
  CONSTRAINT `chk_bundle__valid_date_range` CHECK (`valid_to` IS NULL OR `valid_from` IS NULL OR `valid_to` >= `valid_from`),
  CONSTRAINT `fk_bundle__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service bundles with validity periods and service count constraints';
```

### 21. Bundle Items
```sql
-- 1756668483.create_service_bundle_items_table.sql (IMPROVED)
CREATE TABLE `{!!prefix!!}srvc_bundle_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bundle_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bundle_item` (`bundle_id`,`service_id`),
  KEY `idx_bundle_item__service_id` (`service_id`),
  KEY `idx_bundle_item__is_optional` (`is_optional`),
  KEY `idx_bundle_item__sort_order` (`sort_order`),
  KEY `idx_bundle_item__deleted_at` (`deleted_at`),
  KEY `idx_bundle_item__created_by` (`created_by`),
  KEY `idx_bundle_item__updated_by` (`updated_by`),
  CONSTRAINT `chk_bundle_item__positive_quantity` CHECK (`quantity` > 0),
  CONSTRAINT `chk_bundle_item__valid_discount` CHECK (`discount_pct` >= 0 AND `discount_pct` <= 100.00),
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `{!!prefix!!}srvc_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bundle items with optional flag and ordering';
```

## Summary Views for Performance (Optional)

### 22. Service Pricing Summary View
```sql
-- OPTIONAL: Materialized view for common pricing queries
CREATE VIEW `{!!prefix!!}srvc_v_current_service_pricing` AS
SELECT 
    s.id as service_id,
    s.name as service_name,
    s.slug as service_slug,
    c.name as category_name,
    st.name as service_type_name,
    sc.name as complexity_name,
    pt.name as pricing_tier_name,
    pm.name as pricing_model_name,
    sp.currency,
    sp.amount,
    sp.setup_fee,
    sp.unit,
    (sp.amount * sc.price_multiplier) as adjusted_amount,
    sp.valid_from,
    sp.valid_to,
    sp.approval_status
FROM `{!!prefix!!}srvc_services` s
LEFT JOIN `{!!prefix!!}srvc_categories` c ON s.category_id = c.id
LEFT JOIN `{!!prefix!!}srvc_service_types` st ON s.service_type_id = st.id  
LEFT JOIN `{!!prefix!!}srvc_complexities` sc ON s.complexity_id = sc.id
LEFT JOIN `{!!prefix!!}srvc_service_prices` sp ON s.id = sp.service_id AND sp.is_current = 1 AND sp.deleted_at IS NULL
LEFT JOIN `{!!prefix!!}srvc_pricing_tiers` pt ON sp.pricing_tier_id = pt.id
LEFT JOIN `{!!prefix!!}srvc_pricing_models` pm ON sp.pricing_model_id = pm.id
WHERE s.deleted_at IS NULL 
  AND s.is_active = 1
  AND (sp.valid_to IS NULL OR sp.valid_to > NOW())
  AND (c.deleted_at IS NULL OR c.deleted_at IS NULL)
  AND (pt.deleted_at IS NULL OR pt.deleted_at IS NULL)
  AND (pm.deleted_at IS NULL OR pm.deleted_at IS NULL);
```

## Key Improvements Made

### 1. **Eliminated All Triggers**
- Replaced trigger-based validations with CHECK constraints
- Better performance and SQL standard compliance
- Self-reference prevention via `CHECK (service_id != addon_service_id)`

### 2. **Added Comprehensive Data Validation**
- Currency validation: `CHECK (currency REGEXP '^[A-Z]{3})`
- Positive amount constraints: `CHECK (amount >= 0)`
- Quantity range validation: `CHECK (quantity > 0 AND quantity <= 10000)`
- Discount percentage limits: `CHECK (discount_pct >= 0 AND discount_pct <= 100.00)`

### 3. **Implemented Effective Dating & History**
- `valid_from`/`valid_to` columns on pricing
- `is_current` flag to identify active pricing
- Complete price history table for audit compliance
- Approval workflow with status tracking

### 4. **Enhanced Business Logic**
- Added `skill_level` and `estimated_hours` to services (promoted from JSON)
- Bundle validity periods with `valid_from`/`valid_to`
- Equipment cost tracking and consumable flags  
- Delivery method timing defaults
- Coverage area geographic enhancements

### 5. **Improved Indexing Strategy**
- Covering indexes for common query patterns
- Composite indexes on frequently joined columns
- Performance-optimized indexes for soft-delete aware queries

### 6. **Better Data Types**
- Changed metadata from `longtext` to `json` for better validation
- Added appropriate length constraints
- Consistent use of `unsigned` for counts and IDs

### 7. **Enhanced Foreign Key Relationships**
- Consistent naming convention for all constraints
- Proper CASCADE/SET NULL behaviors
- Added approval user tracking with foreign keys

This schema provides a robust, well-validated, and performance-optimized foundation for your service system while maintaining all the flexibility of the original design.