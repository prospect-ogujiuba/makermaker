-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_coverage` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `coverage_area_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  KEY `idx_service_coverage__coverage_area_id` (`coverage_area_id`),
  KEY `idx_service_coverage__service_id` (`service_id`),
  CONSTRAINT `fk_service_coverage__coverage_area` FOREIGN KEY (`coverage_area_id`) REFERENCES `{!!prefix!!}srvc_coverage_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service availability in coverage areas';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_coverage`;