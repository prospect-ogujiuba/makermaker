-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_coverage_areas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `country_code` char(2) DEFAULT NULL,
  `region_type` enum('city','province','state','country','continent','global') DEFAULT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `postal_code_pattern` varchar(32) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coverage_area__code` (`code`),
  KEY `idx_coverage_area__name` (`name`),
  KEY `idx_coverage_area__country_code` (`country_code`),
  KEY `idx_coverage_area__region_type` (`region_type`),
  KEY `idx_coverage_area__deleted_at` (`deleted_at`),
  KEY `idx_coverage_area__created_by` (`created_by`),
  KEY `idx_coverage_area__updated_by` (`updated_by`),
  CONSTRAINT `chk_coverage_area__valid_country_code` CHECK (`country_code` IS NULL OR `country_code` REGEXP '^[A-Z]{2}$'),
  CONSTRAINT `fk_coverage_area__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_coverage_area__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Geographic coverage areas with enhanced location data';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_coverage_areas`;