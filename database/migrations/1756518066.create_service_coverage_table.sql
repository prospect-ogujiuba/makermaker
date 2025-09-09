-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_coverage` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `coverage_area_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  KEY `idx_service_coverage__coverage_area_id` (`coverage_area_id`),
  KEY `idx_service_coverage__service_id` (`service_id`),
  KEY `idx_service_coverage__deleted_at` (`deleted_at`),
  KEY `idx_service_coverage__created_by` (`created_by`),
  KEY `idx_service_coverage__updated_by` (`updated_by`),
  CONSTRAINT `fk_service_coverage__coverage_area` FOREIGN KEY (`coverage_area_id`) REFERENCES `{!!prefix!!}srvc_coverage_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage_value__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage_value__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service availability in coverage areas';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_coverage`;