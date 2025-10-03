-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_service_delivery` (
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
  UNIQUE KEY `uq_service_delivery` (`service_id`,`delivery_method_id`),
  KEY `idx_service_delivery__delivery_method_id` (`delivery_method_id`),
  KEY `idx_service_delivery__is_default` (`is_default`),
  KEY `idx_service_delivery__deleted_at` (`deleted_at`),
  KEY `idx_service_delivery__created_by` (`created_by`),
  KEY `idx_service_delivery__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_delivery__non_negative_surcharge` CHECK (`surcharge` >= 0),
  CONSTRAINT `fk_service_delivery__delivery_method` FOREIGN KEY (`delivery_method_id`) REFERENCES `{!!prefix!!}srvc_delivery_methods` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Delivery methods for services with timing and cost overrides';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_delivery`;