-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_equipment_assignments`;
CREATE TABLE `{!!prefix!!}srvc_service_equipment_assignments` (
  `service_id` bigint(20) NOT NULL,
  `equipment_id` bigint(20) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 1,
  `quantity` decimal(12,3) NOT NULL DEFAULT 1.000,
  `substitute_ok` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`equipment_id`),
  KEY `idx_service_equipment__equipment_id` (`equipment_id`),
  CONSTRAINT `fk_service_equipment__equipment` FOREIGN KEY (`equipment_id`) REFERENCES `{!!prefix!!}srvc_equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Equipment requirements for services';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_equipment_assignments`;
