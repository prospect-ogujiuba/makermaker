-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_equipment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `manufacturer` varchar(64) DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Equipment specifications as JSON object' CHECK (json_valid(`specs`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment__sku` (`sku`),
  KEY `idx_equipment__name` (`name`),
  KEY `idx_equipment__manufacturer` (`manufacturer`),
  KEY `idx_equipment__deleted_at` (`deleted_at`),
  KEY `idx_equipment__created_at` (`created_at`),
  KEY `idx_equipment__updated_at` (`updated_at`),
  CONSTRAINT `fk_equipment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_equipment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Equipment catalog items';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_equipment`;
