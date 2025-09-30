-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_equipment` (
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
  UNIQUE KEY `uq_service_equipment` (`service_id`,`equipment_id`),
  KEY `idx_service_equipment__equipment_id` (`equipment_id`),
  KEY `idx_service_equipment__required` (`required`),
  KEY `idx_service_equipment__deleted_at` (`deleted_at`),
  KEY `idx_service_equipment__created_by` (`created_by`),
  KEY `idx_service_equipment__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_equipment__positive_quantity` CHECK (`quantity` > 0 AND `quantity` <= 10000),
  CONSTRAINT `fk_service_equipment__equipment` FOREIGN KEY (`equipment_id`) REFERENCES `{!!prefix!!}srvc_equipment` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Equipment requirements for services with quantity validation and cost tracking';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_equipment`;