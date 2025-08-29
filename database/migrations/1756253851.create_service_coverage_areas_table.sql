-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_coverage_areas`;
CREATE TABLE `{!!prefix!!}srvc_coverage_areas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `geo` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'GeoJSON representation of coverage area boundaries' CHECK (json_valid(`geo`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coverage_area__code` (`code`),
  KEY `idx_coverage_area__name` (`name`),
  KEY `idx_coverage_area__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Geographic coverage areas for service delivery';

INSERT INTO `{!!prefix!!}srvc_coverage_areas` (`id`, `code`, `name`, `geo`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'GTA',	'Greater Toronto Area',	'{\"type\": \"Polygon\", \"coordinates\": [[[-79.6, 43.5], [-79.6, 43.9], [-79.1, 43.9], [-79.1, 43.5], [-79.6, 43.5]]]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'ON-SW',	'Southwest Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-82.5, 42.0], [-82.5, 44.5], [-80.0, 44.5], [-80.0, 42.0], [-82.5, 42.0]]]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'ON-CENTRAL',	'Central Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-80.0, 43.0], [-80.0, 45.5], [-77.0, 45.5], [-77.0, 43.0], [-80.0, 43.0]]]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'ON-EAST',	'Eastern Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-77.0, 44.0], [-77.0, 46.0], [-74.5, 46.0], [-74.5, 44.0], [-77.0, 44.0]]]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'ON-NORTH',	'Northern Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-90.0, 46.0], [-90.0, 52.0], [-79.0, 52.0], [-79.0, 46.0], [-90.0, 46.0]]]}',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_coverage_areas`;
