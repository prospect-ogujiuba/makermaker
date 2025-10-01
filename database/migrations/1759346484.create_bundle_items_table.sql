-- Description:
-- >>> Up >>>
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
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `{!!prefix!!}srvc_service_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bundle items with optional flag and ordering';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;