-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_bundle_items` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `bundle_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bundle_item` (`bundle_id`,`service_id`),
  KEY `idx_bundle_item__service_id` (`service_id`),
  KEY `idx_bundle_item__deleted_at` (`deleted_at`),
  KEY `idx_bundle_item__created_by` (`created_by`),
  KEY `idx_bundle_item__updated_by` (`updated_by`),
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `{!!prefix!!}srvc_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Services included in bundles with quantities and discounts';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;