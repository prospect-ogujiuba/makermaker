-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_delivery_method_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `delivery_method_id` bigint(20) NOT NULL,
  `lead_time_days` int(11) NOT NULL DEFAULT 0,
  `sla_hours` int(11) DEFAULT NULL,
  `surcharge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_service_delivery_method_assignment` (`service_id`,`delivery_method_id`),
  KEY `idx_service_delivery_method_assignment__delivery_method_id` (`delivery_method_id`),
  KEY `idx_service_delivery_method_assignment__deleted_at` (`deleted_at`),
  KEY `idx_service_delivery_method_assignment__created_by` (`created_by`),
  KEY `idx_service_delivery_method_assignment__updated_by` (`updated_by`),  
  CONSTRAINT `fk_service_delivery_method_assignment__delivery_method` FOREIGN KEY (`delivery_method_id`) REFERENCES `{!!prefix!!}srvc_delivery_methods` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method_assignment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method_assignment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method_assignment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Delivery methods available for services with lead times and surcharges';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_delivery_method_assignments`;
