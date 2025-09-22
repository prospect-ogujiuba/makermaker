-- Description:
-- >>> Up >>>
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
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional service configuration and properties' CHECK (json_valid(`metadata`)),
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service__slug` (`slug`),
  UNIQUE KEY `uq_service__sku` (`sku`),
  KEY `idx_service__category_id` (`category_id`),
  KEY `idx_service__service_type_id` (`service_type_id`),
  KEY `idx_service__complexity_id` (`complexity_id`),
  KEY `idx_service__is_active` (`is_active`),
  KEY `idx_service__deleted_at` (`deleted_at`),
  KEY `idx_service__created_by` (`created_by`),
  KEY `idx_service__updated_by` (`updated_by`),
  KEY `idx_service__sku` (`sku`),
  KEY `idx_service__slug` (`slug`),
  CONSTRAINT `fk_service__category` FOREIGN KEY (`category_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__complexity` FOREIGN KEY (`complexity_id`) REFERENCES `{!!prefix!!}srvc_complexities` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `{!!prefix!!}srvc_service_types` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Core service catalog entries';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_services`;