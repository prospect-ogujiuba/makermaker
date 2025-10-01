-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_bundles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `long_desc` text DEFAULT NULL,
  `bundle_type` enum('package','collection','suite','solution') NOT NULL DEFAULT 'package',
  `total_discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
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
  CONSTRAINT `chk_bundle__valid_date_range` CHECK (`valid_to` IS NULL OR `valid_from` IS NULL OR `valid_to` >= `valid_from`),
  CONSTRAINT `fk_bundle__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service bundles with validity periods and service count constraints';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_bundles`;