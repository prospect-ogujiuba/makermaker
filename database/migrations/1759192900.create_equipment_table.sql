-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_equipment` (
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

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_equipment`;