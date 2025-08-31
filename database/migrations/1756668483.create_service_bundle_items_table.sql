-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;
CREATE TABLE `{!!prefix!!}srvc_bundle_items` (
  `bundle_id` bigint(20) NOT NULL,
  `service_id` bigint(20) NOT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`bundle_id`,`service_id`),
  KEY `idx_bundle_item__service_id` (`service_id`),
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `{!!prefix!!}srvc_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Services included in bundles with quantities and discounts';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;