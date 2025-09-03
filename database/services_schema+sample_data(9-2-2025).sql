-- Adminer 5.3.0 MariaDB 11.8.2-MariaDB-ubu2404 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `wp_srvc_attribute_definitions`;
CREATE TABLE `wp_srvc_attribute_definitions` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_type_id` bigint(20) NOT NULL,
  `code` varchar(64) NOT NULL,
  `label` varchar(128) NOT NULL,
  `data_type` enum('int','decimal','bool','text','enum') NOT NULL,
  `enum_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Valid options for enum data_type as JSON array' CHECK (json_valid(`enum_options`)),
  `unit` varchar(32) DEFAULT NULL COMMENT 'Unit of measurement: users, sites, ft, etc.',
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_attribute_definition__service_type_code` (`service_type_id`,`code`),
  KEY `idx_attribute_definition__service_type_id` (`service_type_id`),
  KEY `idx_attribute_definition__code` (`code`),
  KEY `idx_attribute_definition__deleted_at` (`deleted_at`),
  CONSTRAINT `fk_attribute_definition__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `wp_srvc_service_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Parametric attribute definitions per service type';

INSERT INTO `wp_srvc_attribute_definitions` (`id`, `service_type_id`, `code`, `label`, `data_type`, `enum_options`, `unit`, `required`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	1,	'user_count',	'Number of Users',	'int',	NULL,	'users',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(2,	1,	'concurrent_calls',	'Concurrent Calls',	'int',	NULL,	'calls',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(3,	1,	'sip_trunks',	'SIP Trunk Lines',	'int',	NULL,	'lines',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(4,	1,	'auto_attendant',	'Auto Attendant Required',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(5,	1,	'voicemail_to_email',	'Voicemail to Email',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(6,	1,	'call_recording',	'Call Recording Required',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(7,	2,	'cable_runs',	'Number of Cable Runs',	'int',	NULL,	'runs',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(8,	2,	'cable_length',	'Total Cable Length',	'int',	NULL,	'feet',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(9,	2,	'outlet_count',	'Network Outlets',	'int',	NULL,	'outlets',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(10,	2,	'cable_category',	'Cable Category',	'enum',	'[\"Cat5e\", \"Cat6\", \"Cat6A\", \"Cat7\", \"Fiber\"]',	NULL,	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(11,	2,	'fiber_required',	'Fiber Optic Required',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(12,	2,	'wireless_coverage',	'Wireless Coverage Area',	'int',	NULL,	'sq_ft',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(13,	2,	'access_points',	'Number of Access Points',	'int',	NULL,	'devices',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(14,	3,	'camera_count',	'Number of Cameras',	'int',	NULL,	'cameras',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(15,	3,	'recording_days',	'Recording Retention',	'int',	NULL,	'days',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(16,	3,	'night_vision',	'Night Vision Required',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(17,	3,	'outdoor_cameras',	'Outdoor Cameras',	'int',	NULL,	'cameras',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(18,	3,	'ptz_cameras',	'PTZ Cameras',	'int',	NULL,	'cameras',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(19,	3,	'coverage_area',	'Coverage Area',	'int',	NULL,	'sq_ft',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(20,	4,	'door_count',	'Number of Doors',	'int',	NULL,	'doors',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(21,	4,	'user_capacity',	'User Capacity',	'int',	NULL,	'users',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(22,	4,	'card_technology',	'Card Technology',	'enum',	'[\"Proximity\", \"Mifare\", \"iClass\", \"Mobile\", \"Biometric\"]',	NULL,	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(23,	4,	'integration_required',	'System Integration',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(24,	4,	'time_attendance',	'Time & Attendance',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(25,	5,	'install_duration',	'Installation Duration',	'int',	NULL,	'days',	1,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(26,	5,	'crew_size',	'Installation Crew Size',	'int',	NULL,	'people',	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(27,	5,	'after_hours',	'After Hours Installation',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL),
(28,	5,	'weekend_work',	'Weekend Work Required',	'bool',	NULL,	NULL,	0,	'2025-09-02 22:57:30',	'2025-09-02 22:57:30',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_bundles`;
CREATE TABLE `wp_srvc_bundles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bundle__slug` (`slug`),
  KEY `idx_bundle__is_active` (`is_active`),
  KEY `idx_bundle__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service bundles for grouped offerings';

INSERT INTO `wp_srvc_bundles` (`id`, `name`, `slug`, `short_desc`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Small Office Complete',	'small-office-complete',	'Complete IT solution for small offices (5-15 employees)',	1,	'2025-08-25 23:57:07',	'2025-09-02 15:01:56',	NULL,	NULL,	NULL),
(2,	'Medium Business Package',	'medium-business-package',	'Comprehensive IT infrastructure for growing businesses (15-50 employees)',	1,	'2025-08-25 23:57:07',	'2025-09-02 15:13:41',	NULL,	NULL,	NULL),
(3,	'Enterprise Security Suite',	'enterprise-security-suite',	'Complete security solution with cameras, access control, and monitoring',	1,	'2025-08-25 23:57:07',	'2025-09-02 07:02:39',	NULL,	NULL,	NULL),
(4,	'Network Infrastructure Bundle',	'network-infrastructure-bundle',	'Complete network setup with cabling, equipment, and wireless',	1,	'2025-08-25 23:57:07',	'2025-09-02 13:32:13',	NULL,	NULL,	NULL),
(5,	'Communication Package',	'communication-package',	'VoIP system with professional installation and training',	1,	'2025-08-25 23:57:07',	'2025-09-02 22:33:07',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_bundle_items`;
CREATE TABLE `wp_srvc_bundle_items` (
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
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `wp_srvc_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Services included in bundles with quantities and discounts';

INSERT INTO `wp_srvc_bundle_items` (`bundle_id`, `service_id`, `quantity`, `discount_pct`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-09-02 15:26:48',	NULL,	NULL),
(1,	5,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-09-02 11:47:47',	NULL,	NULL),
(1,	8,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-09-02 12:38:34',	NULL,	NULL),
(1,	11,	1.000,	8.00,	'2025-08-25 23:57:07',	'2025-09-02 03:49:30',	NULL,	NULL),
(1,	14,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-09-02 05:11:48',	NULL,	NULL),
(2,	2,	1.000,	18.00,	'2025-08-25 23:57:07',	'2025-09-02 14:30:32',	NULL,	NULL),
(2,	6,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-09-02 08:57:15',	NULL,	NULL),
(2,	9,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-09-02 01:14:38',	NULL,	NULL),
(2,	12,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-09-02 03:21:29',	NULL,	NULL),
(2,	15,	1.000,	25.00,	'2025-08-25 23:57:07',	'2025-09-02 13:03:28',	NULL,	NULL),
(3,	10,	1.000,	8.00,	'2025-08-25 23:57:07',	'2025-09-02 07:12:55',	NULL,	NULL),
(3,	13,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-09-02 20:54:11',	NULL,	NULL),
(3,	15,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-09-02 10:52:12',	NULL,	NULL),
(3,	17,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-09-02 15:38:25',	NULL,	NULL),
(4,	6,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-09-02 21:35:31',	NULL,	NULL),
(4,	7,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-09-02 13:02:19',	NULL,	NULL),
(4,	16,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-09-02 00:25:04',	NULL,	NULL),
(5,	2,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-09-02 10:58:24',	NULL,	NULL),
(5,	4,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-09-02 05:36:49',	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_categories`;
CREATE TABLE `wp_srvc_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `path` varchar(512) DEFAULT NULL COMMENT 'Materialized path for fast tree queries (e.g., /root/parent/child)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category__slug` (`slug`),
  KEY `idx_category__parent_id` (`parent_id`),
  KEY `idx_category__path` (`path`),
  KEY `idx_category__deleted_at` (`deleted_at`),
  CONSTRAINT `fk_category__parent` FOREIGN KEY (`parent_id`) REFERENCES `wp_srvc_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Hierarchical service categorization';

INSERT INTO `wp_srvc_categories` (`id`, `parent_id`, `name`, `slug`, `path`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	NULL,	'Telecommunications',	'telecommunications',	'/telecommunications',	'2025-08-25 23:57:07',	'2025-09-02 08:43:25',	NULL,	NULL,	NULL),
(2,	NULL,	'Network & Infrastructure',	'network-infrastructure',	'/network-infrastructure',	'2025-08-25 23:57:07',	'2025-09-02 19:14:03',	NULL,	NULL,	NULL),
(3,	NULL,	'Security Systems',	'security-systems',	'/security-systems',	'2025-08-25 23:57:07',	'2025-09-02 22:00:00',	NULL,	NULL,	NULL),
(4,	NULL,	'Business Solutions',	'business-solutions',	'/business-solutions',	'2025-08-25 23:57:07',	'2025-09-02 04:17:51',	NULL,	NULL,	NULL),
(5,	1,	'VoIP Systems',	'voip-systems',	'/telecommunications/voip-systems',	'2025-08-25 23:57:07',	'2025-09-02 03:29:17',	NULL,	NULL,	NULL),
(6,	1,	'Phone Systems',	'phone-systems',	'/telecommunications/phone-systems',	'2025-08-25 23:57:07',	'2025-09-02 04:32:53',	NULL,	NULL,	NULL),
(7,	1,	'Unified Communications',	'unified-communications',	'/telecommunications/unified-communications',	'2025-08-25 23:57:07',	'2025-09-02 12:16:32',	NULL,	NULL,	NULL),
(8,	1,	'Video Conferencing',	'video-conferencing',	'/telecommunications/video-conferencing',	'2025-08-25 23:57:07',	'2025-09-02 23:44:02',	NULL,	NULL,	NULL),
(9,	2,	'Network Cabling',	'network-cabling',	'/network-infrastructure/network-cabling',	'2025-08-25 23:57:07',	'2025-09-02 09:50:33',	NULL,	NULL,	NULL),
(10,	2,	'Wireless Networks',	'wireless-networks',	'/network-infrastructure/wireless-networks',	'2025-08-25 23:57:07',	'2025-09-02 02:00:41',	NULL,	NULL,	NULL),
(11,	2,	'Network Equipment',	'network-equipment',	'/network-infrastructure/network-equipment',	'2025-08-25 23:57:07',	'2025-09-02 04:31:48',	NULL,	NULL,	NULL),
(12,	2,	'Fiber Optic Solutions',	'fiber-optic',	'/network-infrastructure/fiber-optic',	'2025-08-25 23:57:07',	'2025-09-02 16:36:58',	NULL,	NULL,	NULL),
(13,	3,	'Camera Systems',	'camera-systems',	'/security-systems/camera-systems',	'2025-08-25 23:57:07',	'2025-09-02 21:29:27',	NULL,	NULL,	NULL),
(14,	3,	'Access Control',	'access-control',	'/security-systems/access-control',	'2025-08-25 23:57:07',	'2025-09-02 09:36:22',	NULL,	NULL,	NULL),
(15,	3,	'Alarm Systems',	'alarm-systems',	'/security-systems/alarm-systems',	'2025-08-25 23:57:07',	'2025-09-02 07:33:30',	NULL,	NULL,	NULL),
(16,	3,	'Integrated Security',	'integrated-security',	'/security-systems/integrated-security',	'2025-08-25 23:57:07',	'2025-09-02 08:58:24',	NULL,	NULL,	NULL),
(17,	4,	'IT Support',	'it-support',	'/business-solutions/it-support',	'2025-08-25 23:57:07',	'2025-09-02 22:11:30',	NULL,	NULL,	NULL),
(18,	4,	'Consulting Services',	'consulting',	'/business-solutions/consulting',	'2025-08-25 23:57:07',	'2025-09-02 12:02:20',	NULL,	NULL,	NULL),
(19,	4,	'Training',	'training',	'/business-solutions/training',	'2025-08-25 23:57:07',	'2025-09-02 17:37:08',	NULL,	NULL,	NULL),
(20,	4,	'Maintenance Plans',	'maintenance',	'/business-solutions/maintenance',	'2025-08-25 23:57:07',	'2025-09-02 03:58:40',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_complexities`;
CREATE TABLE `wp_srvc_complexities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `level` int(11) NOT NULL,
  `price_multiplier` decimal(8,4) NOT NULL DEFAULT 1.0000,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_complexity__name` (`name`),
  UNIQUE KEY `uq_complexity__level` (`level`),
  KEY `idx_complexity__level` (`level`),
  KEY `idx_complexity__deleted_at` (`deleted_at`),
  KEY `idx_complexity__created_by` (`created_by`),
  KEY `idx_complexity__updated_by` (`updated_by`),
  CONSTRAINT `fk_complexity__created_by` FOREIGN KEY (`created_by`) REFERENCES `wp_users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_complexity__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `wp_users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service complexity levels with associated pricing multipliers';

INSERT INTO `wp_srvc_complexities` (`id`, `name`, `level`, `price_multiplier`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Basic',	1,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 08:47:26',	NULL,	1,	2),
(2,	'Standard',	2,	1.3000,	'2025-08-25 23:57:07',	'2025-09-02 02:29:19',	NULL,	1,	2),
(3,	'Professional',	3,	1.6000,	'2025-08-25 23:57:07',	'2025-09-02 10:04:17',	NULL,	1,	2),
(4,	'Enterprise',	4,	2.2000,	'2025-08-25 23:57:07',	'2025-09-02 18:53:30',	NULL,	1,	2),
(5,	'Custom/Complexes',	9,	3.0000,	'2025-08-25 23:57:07',	'2025-09-02 23:45:41',	NULL,	1,	1);

DROP TABLE IF EXISTS `wp_srvc_coverage_areas`;
CREATE TABLE `wp_srvc_coverage_areas` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Geographic coverage areas for service delivery';

INSERT INTO `wp_srvc_coverage_areas` (`id`, `code`, `name`, `geo`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'GTA',	'Greater Toronto Area',	'{\"type\": \"Polygon\", \"coordinates\": [[[-79.6, 43.5], [-79.6, 43.9], [-79.1, 43.9], [-79.1, 43.5], [-79.6, 43.5]]]}',	'2025-08-25 23:57:07',	'2025-09-02 01:42:13',	NULL,	NULL,	NULL),
(2,	'ON-SW',	'Southwest Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-82.5, 42.0], [-82.5, 44.5], [-80.0, 44.5], [-80.0, 42.0], [-82.5, 42.0]]]}',	'2025-08-25 23:57:07',	'2025-09-02 10:49:44',	NULL,	NULL,	NULL),
(3,	'ON-CENTRAL',	'Central Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-80.0, 43.0], [-80.0, 45.5], [-77.0, 45.5], [-77.0, 43.0], [-80.0, 43.0]]]}',	'2025-08-25 23:57:07',	'2025-09-02 01:01:59',	NULL,	NULL,	NULL),
(4,	'ON-EAST',	'Eastern Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-77.0, 44.0], [-77.0, 46.0], [-74.5, 46.0], [-74.5, 44.0], [-77.0, 44.0]]]}',	'2025-08-25 23:57:07',	'2025-09-02 20:40:45',	NULL,	NULL,	NULL),
(5,	'ON-NORTH',	'Northern Ontario',	'{\"type\": \"Polygon\", \"coordinates\": [[[-90.0, 46.0], [-90.0, 52.0], [-79.0, 52.0], [-79.0, 46.0], [-90.0, 46.0]]]}',	'2025-08-25 23:57:07',	'2025-09-02 04:17:46',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_deliverables`;
CREATE TABLE `wp_srvc_deliverables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  KEY `idx_deliverable__name` (`name`),
  KEY `idx_deliverable__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Deliverable items that can be associated with services';

INSERT INTO `wp_srvc_deliverables` (`id`, `name`, `description`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'System Design Document',	'Detailed technical design and architecture documentation',	'2025-08-25 23:57:07',	'2025-09-02 07:26:38',	NULL,	NULL,	NULL),
(2,	'Installation Plan',	'Step-by-step installation and deployment plan',	'2025-08-25 23:57:07',	'2025-09-02 00:19:51',	NULL,	NULL,	NULL),
(3,	'Network Diagram',	'Complete network topology and connection diagrams',	'2025-08-25 23:57:07',	'2025-09-02 03:19:20',	NULL,	NULL,	NULL),
(4,	'Equipment List',	'Detailed bill of materials and equipment specifications',	'2025-08-25 23:57:07',	'2025-09-02 15:37:11',	NULL,	NULL,	NULL),
(5,	'Configuration Documentation',	'System configuration settings and parameters',	'2025-08-25 23:57:07',	'2025-09-02 20:07:52',	NULL,	NULL,	NULL),
(6,	'Testing Report',	'Comprehensive system testing and validation results',	'2025-08-25 23:57:07',	'2025-09-02 05:47:49',	NULL,	NULL,	NULL),
(7,	'User Manual',	'End-user operation and maintenance documentation',	'2025-08-25 23:57:07',	'2025-09-02 16:35:29',	NULL,	NULL,	NULL),
(8,	'Training Materials',	'Training guides, videos, and reference materials',	'2025-08-25 23:57:07',	'2025-09-02 17:33:59',	NULL,	NULL,	NULL),
(9,	'Warranty Documentation',	'Equipment warranties and service agreements',	'2025-08-25 23:57:07',	'2025-09-02 14:03:30',	NULL,	NULL,	NULL),
(10,	'As-Built Documentation',	'Final installation documentation with actual configurations',	'2025-08-25 23:57:07',	'2025-09-02 17:35:33',	NULL,	NULL,	NULL),
(11,	'Performance Baseline',	'Initial system performance metrics and benchmarks',	'2025-08-25 23:57:07',	'2025-09-02 21:47:14',	NULL,	NULL,	NULL),
(12,	'Security Assessment',	'Security configuration review and recommendations',	'2025-08-25 23:57:07',	'2025-09-02 08:09:30',	NULL,	NULL,	NULL),
(13,	'Compliance Certificate',	'Industry compliance and certification documentation',	'2025-08-25 23:57:07',	'2025-09-02 23:25:51',	NULL,	NULL,	NULL),
(14,	'Maintenance Schedule',	'Recommended maintenance tasks and schedules',	'2025-08-25 23:57:07',	'2025-09-02 20:40:44',	NULL,	NULL,	NULL),
(15,	'Emergency Procedures',	'Troubleshooting and emergency contact procedures',	'2025-08-25 23:57:07',	'2025-09-02 09:06:08',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_delivery_methods`;
CREATE TABLE `wp_srvc_delivery_methods` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_delivery_method__name` (`name`),
  UNIQUE KEY `uq_delivery_method__code` (`code`),
  KEY `idx_delivery_method__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Delivery methods: On-site, Remote, Ship, Pickup, etc.';

INSERT INTO `wp_srvc_delivery_methods` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'On-Site Installation',	'ONSITE',	'2025-08-25 23:57:07',	'2025-09-02 09:03:39',	NULL,	NULL,	NULL),
(2,	'Remote Configuration',	'REMOTE',	'2025-08-25 23:57:07',	'2025-09-02 02:19:08',	NULL,	NULL,	NULL),
(3,	'Hybrid (On-site + Remote)',	'HYBRID',	'2025-08-25 23:57:07',	'2025-09-02 08:24:42',	NULL,	NULL,	NULL),
(4,	'Client Self-Install',	'SELFINSTALL',	'2025-08-25 23:57:07',	'2025-09-02 11:06:05',	NULL,	NULL,	NULL),
(5,	'White Glove Service',	'WHITEGLOVE',	'2025-08-25 23:57:07',	'2025-09-02 06:16:22',	NULL,	NULL,	NULL),
(6,	'Pickup',	'PICKUP',	'2025-08-25 23:57:07',	'2025-09-02 22:03:36',	NULL,	NULL,	NULL),
(7,	'Shipping/Delivery',	'SHIPPING',	'2025-08-25 23:57:07',	'2025-09-02 19:28:55',	NULL,	NULL,	NULL),
(8,	'In-Store Service',	'INSTORE',	'2025-08-25 23:57:07',	'2025-09-02 07:13:48',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_equipment`;
CREATE TABLE `wp_srvc_equipment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `manufacturer` varchar(128) DEFAULT NULL,
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Equipment specifications as JSON object' CHECK (json_valid(`specs`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_equipment__sku` (`sku`),
  KEY `idx_equipment__name` (`name`),
  KEY `idx_equipment__manufacturer` (`manufacturer`),
  KEY `idx_equipment__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Equipment catalog items';

INSERT INTO `wp_srvc_equipment` (`id`, `sku`, `name`, `manufacturer`, `specs`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'VOIP-PBX-001',	'VoIP PBX System',	'Cisco',	'{\"model\": \"UC560\", \"ports\": 24, \"users\": 50, \"features\": [\"SIP\", \"H.323\", \"PoE\"]}',	'2025-08-25 23:57:07',	'2025-09-02 07:28:29',	NULL,	NULL,	NULL),
(2,	'VOIP-PHONE-001',	'IP Desk Phone',	'Cisco',	'{\"model\": \"CP-7841\", \"lines\": 4, \"display\": \"3.5 inch\", \"power\": \"PoE\"}',	'2025-08-25 23:57:07',	'2025-09-02 10:04:03',	NULL,	NULL,	NULL),
(3,	'VOIP-PHONE-002',	'Executive IP Phone',	'Cisco',	'{\"model\": \"CP-8851\", \"lines\": 5, \"display\": \"5 inch color\", \"power\": \"PoE+\"}',	'2025-08-25 23:57:07',	'2025-09-02 03:54:47',	NULL,	NULL,	NULL),
(4,	'VOIP-PHONE-003',	'Cordless IP Phone',	'Cisco',	'{\"model\": \"CP-8821\", \"type\": \"wireless\", \"battery\": \"Li-Ion\", \"range\": \"150m\"}',	'2025-08-25 23:57:07',	'2025-09-02 13:21:45',	NULL,	NULL,	NULL),
(5,	'VOIP-GW-001',	'VoIP Gateway',	'AudioCodes',	'{\"model\": \"MP-114\", \"fxs_ports\": 4, \"protocols\": [\"SIP\", \"H.323\"]}',	'2025-08-25 23:57:07',	'2025-09-02 07:04:27',	NULL,	NULL,	NULL),
(6,	'NET-SW-001',	'Managed Switch 24-Port',	'Cisco',	'{\"model\": \"SG350-28\", \"ports\": 24, \"uplinks\": 4, \"poe\": true, \"power\": \"185W\"}',	'2025-08-25 23:57:07',	'2025-09-02 19:17:00',	NULL,	NULL,	NULL),
(7,	'NET-SW-002',	'Core Switch 48-Port',	'Cisco',	'{\"model\": \"SG500-52P\", \"ports\": 48, \"uplinks\": 4, \"poe_plus\": true, \"power\": \"370W\"}',	'2025-08-25 23:57:07',	'2025-09-02 03:11:41',	NULL,	NULL,	NULL),
(8,	'NET-RT-001',	'Business Router',	'Cisco',	'{\"model\": \"RV340\", \"wan_ports\": 2, \"lan_ports\": 4, \"vpn\": \"50 tunnels\", \"throughput\": \"500Mbps\"}',	'2025-08-25 23:57:07',	'2025-09-02 06:07:25',	NULL,	NULL,	NULL),
(9,	'NET-AP-001',	'Wireless Access Point',	'Cisco',	'{\"model\": \"WAP371\", \"standard\": \"802.11ac\", \"antennas\": 4, \"power\": \"PoE+\"}',	'2025-08-25 23:57:07',	'2025-09-02 21:02:01',	NULL,	NULL,	NULL),
(10,	'NET-AP-002',	'High-Density Access Point',	'Cisco',	'{\"model\": \"WAP581\", \"standard\": \"802.11ac Wave 2\", \"mu_mimo\": true, \"power\": \"PoE++\"}',	'2025-08-25 23:57:07',	'2025-09-02 14:47:53',	NULL,	NULL,	NULL),
(11,	'CBL-CAT6-001',	'Cat6 Cable - 1000ft',	'Belden',	'{\"category\": \"Cat6\", \"length\": \"1000ft\", \"conductor\": \"23AWG\", \"jacket\": \"CMR\"}',	'2025-08-25 23:57:07',	'2025-09-02 10:53:22',	NULL,	NULL,	NULL),
(12,	'CBL-CAT6A-001',	'Cat6A Cable - 1000ft',	'Belden',	'{\"category\": \"Cat6A\", \"length\": \"1000ft\", \"conductor\": \"23AWG\", \"jacket\": \"CMR\", \"shielded\": true}',	'2025-08-25 23:57:07',	'2025-09-02 10:03:12',	NULL,	NULL,	NULL),
(13,	'CBL-FIBER-001',	'Fiber Optic Cable SM',	'Corning',	'{\"type\": \"Single Mode\", \"count\": 12, \"length\": \"1000ft\", \"jacket\": \"OFNR\"}',	'2025-08-25 23:57:07',	'2025-09-02 17:35:56',	NULL,	NULL,	NULL),
(14,	'CBL-PATCH-001',	'Cat6 Patch Cable',	'Panduit',	'{\"category\": \"Cat6\", \"length\": \"3ft\", \"color\": \"blue\", \"connectors\": \"RJ45\"}',	'2025-08-25 23:57:07',	'2025-09-02 09:50:01',	NULL,	NULL,	NULL),
(15,	'SEC-CAM-001',	'IP Security Camera',	'Axis',	'{\"model\": \"P3245-LVE\", \"resolution\": \"1920x1080\", \"night_vision\": true, \"poe_plus\": true}',	'2025-08-25 23:57:07',	'2025-09-02 20:22:21',	NULL,	NULL,	NULL),
(16,	'SEC-CAM-002',	'PTZ Security Camera',	'Axis',	'{\"model\": \"P5635-E\", \"resolution\": \"1920x1080\", \"zoom\": \"32x\", \"outdoor\": true}',	'2025-08-25 23:57:07',	'2025-09-02 00:21:39',	NULL,	NULL,	NULL),
(17,	'SEC-NVR-001',	'16-Channel NVR',	'Axis',	'{\"model\": \"S2216\", \"channels\": 16, \"storage\": \"16TB\", \"recording\": \"H.264/H.265\"}',	'2025-08-25 23:57:07',	'2025-09-02 12:41:14',	NULL,	NULL,	NULL),
(18,	'SEC-AC-001',	'Access Control Panel',	'HID',	'{\"model\": \"VertX V2000\", \"doors\": 2, \"readers\": 4, \"users\": 50000}',	'2025-08-25 23:57:07',	'2025-09-02 14:21:12',	NULL,	NULL,	NULL),
(19,	'SEC-AC-002',	'Card Reader',	'HID',	'{\"model\": \"R40\", \"technology\": \"125kHz\", \"format\": \"Wiegand\", \"led\": true}',	'2025-08-25 23:57:07',	'2025-09-02 09:42:20',	NULL,	NULL,	NULL),
(20,	'SEC-AC-003',	'Keypad Reader',	'HID',	'{\"model\": \"RPK40\", \"technology\": \"125kHz + PIN\", \"keys\": 12, \"backlit\": true}',	'2025-08-25 23:57:07',	'2025-09-02 05:28:05',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_pricing_models`;
CREATE TABLE `wp_srvc_pricing_models` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_model__name` (`name`),
  UNIQUE KEY `uq_pricing_model__code` (`code`),
  KEY `idx_pricing_model__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Pricing models: fixed, hourly, per_unit, subscription, tiered, volume';

INSERT INTO `wp_srvc_pricing_models` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Fixed Project',	'FIXED',	'2025-08-25 23:57:07',	'2025-09-02 00:32:46',	NULL,	NULL,	NULL),
(2,	'Hourly Rate',	'HOURLY',	'2025-08-25 23:57:07',	'2025-09-02 01:59:54',	NULL,	NULL,	NULL),
(3,	'Per Unit/Device',	'UNIT',	'2025-08-25 23:57:07',	'2025-09-02 08:21:12',	NULL,	NULL,	NULL),
(4,	'Monthly Subscription',	'MONTHLY',	'2025-08-25 23:57:07',	'2025-09-02 11:46:20',	NULL,	NULL,	NULL),
(5,	'Annual Contract',	'ANNUAL',	'2025-08-25 23:57:07',	'2025-09-02 09:48:05',	NULL,	NULL,	NULL),
(6,	'Per Square Foot',	'SQ_FT',	'2025-08-25 23:57:07',	'2025-09-02 13:41:26',	NULL,	NULL,	NULL),
(7,	'Tiered Pricing',	'TIERED',	'2025-08-25 23:57:07',	'2025-09-02 15:02:53',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_pricing_tiers`;
CREATE TABLE `wp_srvc_pricing_tiers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_tier__name` (`name`),
  UNIQUE KEY `uq_pricing_tier__code` (`code`),
  KEY `idx_pricing_tier__sort_order` (`sort_order`),
  KEY `idx_pricing_tier__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Pricing tiers: Retail, Partner, Enterprise, etc.';

INSERT INTO `wp_srvc_pricing_tiers` (`id`, `name`, `code`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Small Business',	'SMB',	1,	'2025-08-25 23:57:07',	'2025-09-02 10:10:07',	NULL,	NULL,	NULL),
(2,	'Mid-Market',	'MID',	2,	'2025-08-25 23:57:07',	'2025-09-02 05:41:58',	NULL,	NULL,	NULL),
(3,	'Enterprise',	'ENT',	3,	'2025-08-25 23:57:07',	'2025-09-02 21:59:31',	NULL,	NULL,	NULL),
(4,	'Government',	'GOV',	4,	'2025-08-25 23:57:07',	'2025-09-02 20:51:39',	NULL,	NULL,	NULL),
(5,	'Non-Profit',	'NPO',	5,	'2025-08-25 23:57:07',	'2025-09-02 14:19:43',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_services`;
CREATE TABLE `wp_srvc_services` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) DEFAULT NULL,
  `slug` varchar(128) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `long_desc` text DEFAULT NULL,
  `category_id` bigint(20) NOT NULL,
  `service_type_id` bigint(20) NOT NULL,
  `complexity_id` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_addon` tinyint(1) NOT NULL DEFAULT 0,
  `default_unit` varchar(32) DEFAULT NULL COMMENT 'Default pricing unit: hour, user, site, device',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional service configuration and properties' CHECK (json_valid(`metadata`)),
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service__slug` (`slug`),
  UNIQUE KEY `uq_service__sku` (`sku`),
  KEY `idx_service__category_id` (`category_id`),
  KEY `idx_service__service_type_id` (`service_type_id`),
  KEY `idx_service__complexity_id` (`complexity_id`),
  KEY `idx_service__is_active` (`is_active`),
  KEY `idx_service__is_addon` (`is_addon`),
  KEY `idx_service__deleted_at` (`deleted_at`),
  KEY `idx_service__sku` (`sku`),
  KEY `idx_service__slug` (`slug`),
  CONSTRAINT `fk_service__category` FOREIGN KEY (`category_id`) REFERENCES `wp_srvc_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__complexity` FOREIGN KEY (`complexity_id`) REFERENCES `wp_srvc_complexities` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `wp_srvc_service_types` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Core service catalog entries';

INSERT INTO `wp_srvc_services` (`id`, `sku`, `slug`, `name`, `short_desc`, `long_desc`, `category_id`, `service_type_id`, `complexity_id`, `is_active`, `is_addon`, `default_unit`, `metadata`, `version`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'VOIP-SYS-BASIC',	'voip-system-basic',	'Basic VoIP System',	'Entry-level VoIP phone system for small offices',	'Complete VoIP phone system including IP PBX, desk phones, and basic features like voicemail, call forwarding, and auto-attendant. Perfect for small businesses with up to 25 users.',	5,	1,	1,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 00:08:59',	NULL,	NULL,	NULL),
(2,	'VOIP-SYS-PROF',	'voip-system-professional',	'Professional VoIP System',	'Advanced VoIP solution for growing businesses',	'Comprehensive VoIP system with advanced features including call recording, unified messaging, mobile integration, and detailed reporting. Suitable for businesses with 25-100 users.',	5,	1,	3,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 05:05:31',	NULL,	NULL,	NULL),
(3,	'VOIP-SYS-ENT',	'voip-system-enterprise',	'Enterprise VoIP System',	'Full-featured enterprise communication platform',	'Enterprise-grade unified communications platform with advanced call routing, conference bridging, CRM integration, and redundancy. Supports 100+ users with 99.99% uptime SLA.',	5,	1,	4,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:00:40',	NULL,	NULL,	NULL),
(4,	'VOIP-INST-001',	'voip-installation',	'VoIP System Installation',	'Professional VoIP system installation service',	'Complete installation and configuration of VoIP phone systems including equipment mounting, network configuration, user setup, and training.',	5,	5,	2,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:46:49',	NULL,	NULL,	NULL),
(5,	'CBL-INST-001',	'network-cabling-cat6',	'Cat6 Network Cabling',	'Cat6 structured cabling installation',	'Professional installation of Cat6 structured cabling including cable runs, patch panels, outlets, and testing. Includes 25-year manufacturer warranty.',	9,	2,	2,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 17:52:04',	NULL,	NULL,	NULL),
(6,	'CBL-INST-002',	'network-cabling-cat6a',	'Cat6A Network Cabling',	'Cat6A high-performance cabling installation',	'Premium Cat6A shielded cabling installation for high-speed networks up to 10Gbps. Includes professional termination, testing, and certification.',	9,	2,	3,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:59:56',	NULL,	NULL,	NULL),
(7,	'CBL-INST-003',	'fiber-optic-installation',	'Fiber Optic Installation',	'Single/multi-mode fiber optic cabling',	'Professional fiber optic cable installation including fusion splicing, testing, and documentation. Supports long-distance and high-bandwidth applications.',	12,	2,	4,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 18:23:26',	NULL,	NULL,	NULL),
(8,	'CAM-SYS-001',	'ip-camera-system-basic',	'Basic IP Camera System',	'Entry-level IP camera surveillance system',	'Complete IP camera system with 4-8 cameras, network video recorder, and basic monitoring software. Includes mobile app access and 30-day recording.',	13,	3,	2,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:57:23',	NULL,	NULL,	NULL),
(9,	'CAM-SYS-002',	'ip-camera-system-pro',	'Professional Camera System',	'Advanced IP camera system with analytics',	'Professional IP camera system with intelligent video analytics, motion detection, facial recognition, and advanced search capabilities. Suitable for medium businesses.',	13,	3,	3,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:36:37',	NULL,	NULL,	NULL),
(10,	'CAM-SYS-003',	'ip-camera-system-ent',	'Enterprise Camera System',	'Large-scale enterprise surveillance solution',	'Enterprise-grade IP camera system with redundant NVRs, advanced analytics, integration APIs, and centralized management. Supports 100+ cameras with scalable architecture.',	13,	3,	4,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:10:58',	NULL,	NULL,	NULL),
(11,	'AC-SYS-001',	'access-control-basic',	'Basic Access Control',	'Single-door access control system',	'Basic access control system for single door with card readers, electronic lock, and simple user management. Includes 100 proximity cards.',	14,	4,	1,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 10:05:01',	NULL,	NULL,	NULL),
(12,	'AC-SYS-002',	'access-control-multi',	'Multi-Door Access Control',	'Multi-door access control system',	'Comprehensive access control system for multiple doors with centralized management, time/attendance tracking, and detailed reporting.',	14,	4,	3,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:52:09',	NULL,	NULL,	NULL),
(13,	'AC-SYS-003',	'access-control-enterprise',	'Enterprise Access Control',	'Large-scale enterprise access control',	'Enterprise access control platform with biometric integration, visitor management, emergency lockdown, and integration with HR systems.',	14,	4,	4,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 06:05:42',	NULL,	NULL,	NULL),
(14,	'SUP-001',	'technical-support-basic',	'Basic Technical Support',	'Business hours phone and email support',	'Standard technical support during business hours (8AM-6PM) via phone and email. Includes remote diagnostics and basic troubleshooting.',	17,	6,	1,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:52:05',	NULL,	NULL,	NULL),
(15,	'SUP-002',	'technical-support-premium',	'Premium Technical Support',	'24/7 technical support with priority response',	'Premium support with 24/7 availability, 2-hour response time, on-site service, and dedicated account manager. Includes proactive monitoring.',	17,	6,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:03:20',	NULL,	NULL,	NULL),
(16,	'CONS-001',	'network-assessment',	'Network Assessment',	'Comprehensive network infrastructure assessment',	'Professional assessment of existing network infrastructure including performance analysis, security review, and improvement recommendations.',	18,	7,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 17:40:27',	NULL,	NULL,	NULL),
(17,	'CONS-002',	'security-audit',	'Security System Audit',	'Security system evaluation and recommendations',	'Comprehensive security audit including physical security assessment, system vulnerabilities, and compliance review with detailed recommendations.',	18,	7,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:12:14',	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_service_addons`;
CREATE TABLE `wp_srvc_service_addons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `addon_service_id` bigint(20) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `min_qty` decimal(12,3) NOT NULL DEFAULT 0.000,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `price_delta` decimal(12,2) DEFAULT NULL,
  `multiplier` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_addon` (`service_id`,`addon_service_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_addon_service_id` (`addon_service_id`),
  CONSTRAINT `fk_service_addon__addon_service` FOREIGN KEY (`addon_service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_addon__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service add-on relationships with pricing overrides';

INSERT INTO `wp_srvc_service_addons` (`id`, `service_id`, `addon_service_id`, `required`, `min_qty`, `max_qty`, `price_delta`, `multiplier`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	4,	1,	1.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 06:04:35',	NULL,	NULL),
(2,	1,	14,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 21:47:49',	NULL,	NULL),
(3,	2,	4,	1,	1.000,	1.000,	NULL,	1.2000,	'2025-08-25 23:57:07',	'2025-09-02 18:45:20',	NULL,	NULL),
(4,	2,	15,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 04:23:15',	NULL,	NULL),
(5,	3,	4,	1,	1.000,	1.000,	NULL,	1.5000,	'2025-08-25 23:57:07',	'2025-09-02 13:40:13',	NULL,	NULL),
(6,	3,	15,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 07:11:23',	NULL,	NULL),
(7,	8,	5,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 18:56:17',	NULL,	NULL),
(8,	9,	6,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 01:07:16',	NULL,	NULL),
(9,	10,	7,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 20:47:29',	NULL,	NULL),
(10,	11,	16,	0,	0.000,	1.000,	-10.00,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 04:35:39',	NULL,	NULL),
(11,	12,	17,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 08:35:47',	NULL,	NULL),
(12,	13,	17,	1,	1.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-09-02 05:11:57',	NULL,	NULL);

DELIMITER ;;

CREATE TRIGGER `tr_service_addon_no_self_ref` BEFORE INSERT ON `wp_srvc_service_addons` FOR EACH ROW
BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot be an addon to itself'; END IF; END;;

CREATE TRIGGER `tr_service_addon_no_self_ref_update` BEFORE UPDATE ON `wp_srvc_service_addons` FOR EACH ROW
BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot be an addon to itself';END IF; END;;

DELIMITER ;

DROP TABLE IF EXISTS `wp_srvc_service_attribute_values`;
CREATE TABLE `wp_srvc_service_attribute_values` (
  `service_id` bigint(20) NOT NULL,
  `attribute_definition_id` bigint(20) NOT NULL,
  `int_val` bigint(20) DEFAULT NULL,
  `decimal_val` decimal(18,6) DEFAULT NULL,
  `bool_val` tinyint(1) DEFAULT NULL,
  `text_val` text DEFAULT NULL,
  `enum_val` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`attribute_definition_id`),
  KEY `idx_service_attribute_value__attribute_definition_id` (`attribute_definition_id`),
  CONSTRAINT `fk_service_attribute_value__attribute_definition` FOREIGN KEY (`attribute_definition_id`) REFERENCES `wp_srvc_attribute_definitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_attribute_value__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Attribute values for services based on their type definitions';

INSERT INTO `wp_srvc_service_attribute_values` (`service_id`, `attribute_definition_id`, `int_val`, `decimal_val`, `bool_val`, `text_val`, `enum_val`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	25,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 00:12:26',	NULL,	NULL),
(1,	2,	10,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 09:26:20',	NULL,	NULL),
(1,	3,	5,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:34:24',	NULL,	NULL),
(1,	4,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 12:32:59',	NULL,	NULL),
(1,	5,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 19:01:45',	NULL,	NULL),
(1,	6,	NULL,	NULL,	0,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 09:29:49',	NULL,	NULL),
(2,	1,	100,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 14:23:48',	NULL,	NULL),
(2,	2,	40,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 19:29:33',	NULL,	NULL),
(2,	3,	20,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 06:16:21',	NULL,	NULL),
(2,	4,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 20:53:08',	NULL,	NULL),
(2,	5,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 13:36:39',	NULL,	NULL),
(2,	6,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 05:23:50',	NULL,	NULL),
(3,	1,	500,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 10:09:16',	NULL,	NULL),
(3,	2,	200,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 10:34:47',	NULL,	NULL),
(3,	3,	100,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:26:11',	NULL,	NULL),
(3,	4,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 08:26:31',	NULL,	NULL),
(3,	5,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:54:06',	NULL,	NULL),
(3,	6,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 17:10:55',	NULL,	NULL),
(5,	7,	50,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 17:12:20',	NULL,	NULL),
(5,	8,	2500,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 10:28:54',	NULL,	NULL),
(5,	9,	50,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 00:47:32',	NULL,	NULL),
(5,	10,	NULL,	NULL,	NULL,	NULL,	'Cat6',	'2025-08-25 23:57:07',	'2025-09-02 20:30:56',	NULL,	NULL),
(5,	11,	NULL,	NULL,	0,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 04:12:06',	NULL,	NULL),
(6,	7,	75,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 07:27:44',	NULL,	NULL),
(6,	8,	4000,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 00:42:22',	NULL,	NULL),
(6,	9,	75,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 05:08:39',	NULL,	NULL),
(6,	10,	NULL,	NULL,	NULL,	NULL,	'Cat6A',	'2025-08-25 23:57:07',	'2025-09-02 23:36:08',	NULL,	NULL),
(6,	11,	NULL,	NULL,	0,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 06:34:42',	NULL,	NULL),
(7,	7,	12,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 10:05:10',	NULL,	NULL),
(7,	8,	1500,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 06:41:41',	NULL,	NULL),
(7,	9,	12,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 03:12:57',	NULL,	NULL),
(7,	10,	NULL,	NULL,	NULL,	NULL,	'Fiber',	'2025-08-25 23:57:07',	'2025-09-02 19:59:42',	NULL,	NULL),
(7,	11,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 18:19:41',	NULL,	NULL),
(8,	14,	8,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 07:39:20',	NULL,	NULL),
(8,	15,	30,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 07:17:38',	NULL,	NULL),
(8,	16,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 13:30:08',	NULL,	NULL),
(8,	17,	4,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 21:37:49',	NULL,	NULL),
(8,	18,	0,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 19:38:42',	NULL,	NULL),
(8,	19,	5000,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 09:20:00',	NULL,	NULL),
(9,	14,	32,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 11:43:58',	NULL,	NULL),
(9,	15,	90,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 06:39:48',	NULL,	NULL),
(9,	16,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:07:06',	NULL,	NULL),
(9,	17,	16,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 18:36:09',	NULL,	NULL),
(9,	18,	4,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 02:39:28',	NULL,	NULL),
(9,	19,	25000,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 05:28:54',	NULL,	NULL),
(10,	14,	128,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 19:26:04',	NULL,	NULL),
(10,	15,	365,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 08:43:40',	NULL,	NULL),
(10,	16,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 09:20:07',	NULL,	NULL),
(10,	17,	64,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 20:29:36',	NULL,	NULL),
(10,	18,	16,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 02:27:42',	NULL,	NULL),
(10,	19,	100000,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:49:40',	NULL,	NULL),
(11,	20,	1,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 10:45:14',	NULL,	NULL),
(11,	21,	100,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 09:17:10',	NULL,	NULL),
(11,	22,	NULL,	NULL,	NULL,	NULL,	'Proximity',	'2025-08-25 23:57:07',	'2025-09-02 14:10:08',	NULL,	NULL),
(11,	23,	NULL,	NULL,	0,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 18:59:13',	NULL,	NULL),
(11,	24,	NULL,	NULL,	0,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 04:25:42',	NULL,	NULL),
(12,	20,	8,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 13:10:50',	NULL,	NULL),
(12,	21,	500,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 04:37:05',	NULL,	NULL),
(12,	22,	NULL,	NULL,	NULL,	NULL,	'Mifare',	'2025-08-25 23:57:07',	'2025-09-02 07:32:55',	NULL,	NULL),
(12,	23,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 23:53:20',	NULL,	NULL),
(12,	24,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 00:47:58',	NULL,	NULL),
(13,	20,	50,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 04:19:49',	NULL,	NULL),
(13,	21,	10000,	NULL,	NULL,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 19:15:10',	NULL,	NULL),
(13,	22,	NULL,	NULL,	NULL,	NULL,	'iClass',	'2025-08-25 23:57:07',	'2025-09-02 11:16:23',	NULL,	NULL),
(13,	23,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 22:36:27',	NULL,	NULL),
(13,	24,	NULL,	NULL,	1,	NULL,	NULL,	'2025-08-25 23:57:07',	'2025-09-02 07:13:05',	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_service_coverage`;
CREATE TABLE `wp_srvc_service_coverage` (
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
  CONSTRAINT `fk_service_coverage__coverage_area` FOREIGN KEY (`coverage_area_id`) REFERENCES `wp_srvc_coverage_areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_coverage__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service availability in coverage areas';

INSERT INTO `wp_srvc_service_coverage` (`id`, `service_id`, `coverage_area_id`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:16:05',	NULL,	NULL),
(2,	1,	2,	'2025-08-25 23:57:07',	'2025-09-02 11:41:13',	NULL,	NULL),
(3,	1,	3,	'2025-08-25 23:57:07',	'2025-09-02 09:37:48',	NULL,	NULL),
(4,	2,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:05:23',	NULL,	NULL),
(5,	2,	3,	'2025-08-25 23:57:07',	'2025-09-02 12:33:32',	NULL,	NULL),
(6,	3,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:31:29',	NULL,	NULL),
(7,	3,	2,	'2025-08-25 23:57:07',	'2025-09-02 07:56:50',	NULL,	NULL),
(8,	3,	3,	'2025-08-25 23:57:07',	'2025-09-02 17:09:45',	NULL,	NULL),
(9,	3,	4,	'2025-08-25 23:57:07',	'2025-09-02 13:58:17',	NULL,	NULL),
(10,	3,	5,	'2025-08-25 23:57:07',	'2025-09-02 18:22:09',	NULL,	NULL),
(11,	4,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:55:53',	NULL,	NULL),
(12,	4,	2,	'2025-08-25 23:57:07',	'2025-09-02 02:33:00',	NULL,	NULL),
(13,	4,	3,	'2025-08-25 23:57:07',	'2025-09-02 06:57:22',	NULL,	NULL),
(14,	5,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:07:50',	NULL,	NULL),
(15,	5,	2,	'2025-08-25 23:57:07',	'2025-09-02 18:47:06',	NULL,	NULL),
(16,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 12:31:58',	NULL,	NULL),
(17,	6,	1,	'2025-08-25 23:57:07',	'2025-09-02 06:18:34',	NULL,	NULL),
(18,	6,	3,	'2025-08-25 23:57:07',	'2025-09-02 17:56:55',	NULL,	NULL),
(19,	7,	1,	'2025-08-25 23:57:07',	'2025-09-02 22:48:56',	NULL,	NULL),
(20,	8,	1,	'2025-08-25 23:57:07',	'2025-09-02 12:13:54',	NULL,	NULL),
(21,	8,	2,	'2025-08-25 23:57:07',	'2025-09-02 16:42:41',	NULL,	NULL),
(22,	8,	3,	'2025-08-25 23:57:07',	'2025-09-02 22:51:44',	NULL,	NULL),
(23,	9,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:10:36',	NULL,	NULL),
(24,	10,	1,	'2025-08-25 23:57:07',	'2025-09-02 12:17:51',	NULL,	NULL),
(25,	10,	2,	'2025-08-25 23:57:07',	'2025-09-02 12:57:27',	NULL,	NULL),
(26,	10,	3,	'2025-08-25 23:57:07',	'2025-09-02 03:53:43',	NULL,	NULL),
(27,	10,	4,	'2025-08-25 23:57:07',	'2025-09-02 04:36:12',	NULL,	NULL),
(28,	11,	1,	'2025-08-25 23:57:07',	'2025-09-02 11:19:55',	NULL,	NULL),
(29,	11,	3,	'2025-08-25 23:57:07',	'2025-09-02 18:50:58',	NULL,	NULL),
(30,	12,	1,	'2025-08-25 23:57:07',	'2025-09-02 12:15:04',	NULL,	NULL),
(31,	13,	1,	'2025-08-25 23:57:07',	'2025-09-02 04:42:26',	NULL,	NULL),
(32,	13,	2,	'2025-08-25 23:57:07',	'2025-09-02 10:47:01',	NULL,	NULL),
(33,	13,	3,	'2025-08-25 23:57:07',	'2025-09-02 15:47:45',	NULL,	NULL),
(34,	13,	4,	'2025-08-25 23:57:07',	'2025-09-02 22:37:44',	NULL,	NULL),
(35,	14,	1,	'2025-08-25 23:57:07',	'2025-09-02 17:45:26',	NULL,	NULL),
(36,	14,	2,	'2025-08-25 23:57:07',	'2025-09-02 20:53:59',	NULL,	NULL),
(37,	14,	3,	'2025-08-25 23:57:07',	'2025-09-02 03:13:37',	NULL,	NULL),
(38,	15,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:26:08',	NULL,	NULL),
(39,	15,	2,	'2025-08-25 23:57:07',	'2025-09-02 21:29:49',	NULL,	NULL),
(40,	15,	3,	'2025-08-25 23:57:07',	'2025-09-02 07:10:44',	NULL,	NULL),
(41,	15,	4,	'2025-08-25 23:57:07',	'2025-09-02 19:24:10',	NULL,	NULL),
(42,	15,	5,	'2025-08-25 23:57:07',	'2025-09-02 03:28:41',	NULL,	NULL),
(43,	16,	1,	'2025-08-25 23:57:07',	'2025-09-02 07:10:53',	NULL,	NULL),
(44,	16,	2,	'2025-08-25 23:57:07',	'2025-09-02 01:28:23',	NULL,	NULL),
(45,	16,	3,	'2025-08-25 23:57:07',	'2025-09-02 09:49:17',	NULL,	NULL),
(46,	17,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:41:16',	NULL,	NULL),
(47,	17,	3,	'2025-08-25 23:57:07',	'2025-09-02 01:58:29',	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_service_deliverable_assignments`;
CREATE TABLE `wp_srvc_service_deliverable_assignments` (
  `service_id` bigint(20) NOT NULL,
  `deliverable_id` bigint(20) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`deliverable_id`),
  KEY `idx_service_deliverable__deliverable_id` (`deliverable_id`),
  KEY `idx_service_deliverable__sort_order` (`sort_order`),
  CONSTRAINT `fk_service_deliverable__deliverable` FOREIGN KEY (`deliverable_id`) REFERENCES `wp_srvc_deliverables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Deliverables associated with services';

INSERT INTO `wp_srvc_service_deliverable_assignments` (`service_id`, `deliverable_id`, `sort_order`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:48:38',	NULL,	NULL),
(1,	3,	2,	'2025-08-25 23:57:07',	'2025-09-02 21:07:44',	NULL,	NULL),
(1,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 22:12:46',	NULL,	NULL),
(1,	7,	4,	'2025-08-25 23:57:07',	'2025-09-02 23:40:39',	NULL,	NULL),
(1,	9,	5,	'2025-08-25 23:57:07',	'2025-09-02 03:44:59',	NULL,	NULL),
(2,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:42:55',	NULL,	NULL),
(2,	3,	2,	'2025-08-25 23:57:07',	'2025-09-02 15:19:41',	NULL,	NULL),
(2,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 17:29:40',	NULL,	NULL),
(2,	7,	5,	'2025-08-25 23:57:07',	'2025-09-02 17:29:16',	NULL,	NULL),
(2,	8,	4,	'2025-08-25 23:57:07',	'2025-09-02 10:57:23',	NULL,	NULL),
(2,	9,	6,	'2025-08-25 23:57:07',	'2025-09-02 02:19:05',	NULL,	NULL),
(2,	11,	7,	'2025-08-25 23:57:07',	'2025-09-02 02:43:18',	NULL,	NULL),
(3,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 06:39:14',	NULL,	NULL),
(3,	2,	2,	'2025-08-25 23:57:07',	'2025-09-02 01:06:18',	NULL,	NULL),
(3,	3,	3,	'2025-08-25 23:57:07',	'2025-09-02 09:33:48',	NULL,	NULL),
(3,	5,	4,	'2025-08-25 23:57:07',	'2025-09-02 20:30:08',	NULL,	NULL),
(3,	7,	6,	'2025-08-25 23:57:07',	'2025-09-02 01:49:14',	NULL,	NULL),
(3,	8,	5,	'2025-08-25 23:57:07',	'2025-09-02 19:35:48',	NULL,	NULL),
(3,	9,	7,	'2025-08-25 23:57:07',	'2025-09-02 20:31:20',	NULL,	NULL),
(3,	10,	8,	'2025-08-25 23:57:07',	'2025-09-02 19:49:16',	NULL,	NULL),
(3,	11,	9,	'2025-08-25 23:57:07',	'2025-09-02 13:32:18',	NULL,	NULL),
(3,	14,	10,	'2025-08-25 23:57:07',	'2025-09-02 08:13:44',	NULL,	NULL),
(5,	2,	1,	'2025-08-25 23:57:07',	'2025-09-02 00:31:47',	NULL,	NULL),
(5,	3,	2,	'2025-08-25 23:57:07',	'2025-09-02 01:57:42',	NULL,	NULL),
(5,	4,	3,	'2025-08-25 23:57:07',	'2025-09-02 08:13:10',	NULL,	NULL),
(5,	6,	4,	'2025-08-25 23:57:07',	'2025-09-02 11:12:43',	NULL,	NULL),
(5,	9,	5,	'2025-08-25 23:57:07',	'2025-09-02 07:24:08',	NULL,	NULL),
(5,	10,	6,	'2025-08-25 23:57:07',	'2025-09-02 03:22:30',	NULL,	NULL),
(6,	2,	1,	'2025-08-25 23:57:07',	'2025-09-02 18:40:07',	NULL,	NULL),
(6,	3,	2,	'2025-08-25 23:57:07',	'2025-09-02 11:13:07',	NULL,	NULL),
(6,	4,	3,	'2025-08-25 23:57:07',	'2025-09-02 00:05:12',	NULL,	NULL),
(6,	6,	4,	'2025-08-25 23:57:07',	'2025-09-02 14:46:39',	NULL,	NULL),
(6,	9,	5,	'2025-08-25 23:57:07',	'2025-09-02 01:37:40',	NULL,	NULL),
(6,	10,	6,	'2025-08-25 23:57:07',	'2025-09-02 11:48:23',	NULL,	NULL),
(6,	13,	7,	'2025-08-25 23:57:07',	'2025-09-02 06:08:57',	NULL,	NULL),
(7,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:19:38',	NULL,	NULL),
(7,	2,	2,	'2025-08-25 23:57:07',	'2025-09-02 06:11:16',	NULL,	NULL),
(7,	3,	3,	'2025-08-25 23:57:07',	'2025-09-02 20:57:28',	NULL,	NULL),
(7,	4,	4,	'2025-08-25 23:57:07',	'2025-09-02 14:13:31',	NULL,	NULL),
(7,	6,	5,	'2025-08-25 23:57:07',	'2025-09-02 08:15:13',	NULL,	NULL),
(7,	9,	6,	'2025-08-25 23:57:07',	'2025-09-02 22:35:31',	NULL,	NULL),
(7,	10,	7,	'2025-08-25 23:57:07',	'2025-09-02 16:11:58',	NULL,	NULL),
(7,	13,	8,	'2025-08-25 23:57:07',	'2025-09-02 13:13:16',	NULL,	NULL),
(8,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 17:30:27',	NULL,	NULL),
(8,	5,	2,	'2025-08-25 23:57:07',	'2025-09-02 23:52:26',	NULL,	NULL),
(8,	7,	3,	'2025-08-25 23:57:07',	'2025-09-02 18:50:50',	NULL,	NULL),
(8,	9,	4,	'2025-08-25 23:57:07',	'2025-09-02 22:36:52',	NULL,	NULL),
(9,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 08:31:52',	NULL,	NULL),
(9,	2,	2,	'2025-08-25 23:57:07',	'2025-09-02 22:48:46',	NULL,	NULL),
(9,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 16:28:13',	NULL,	NULL),
(9,	7,	5,	'2025-08-25 23:57:07',	'2025-09-02 13:54:48',	NULL,	NULL),
(9,	8,	4,	'2025-08-25 23:57:07',	'2025-09-02 20:09:21',	NULL,	NULL),
(9,	9,	6,	'2025-08-25 23:57:07',	'2025-09-02 11:02:21',	NULL,	NULL),
(9,	12,	7,	'2025-08-25 23:57:07',	'2025-09-02 18:43:43',	NULL,	NULL),
(10,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 12:31:34',	NULL,	NULL),
(10,	2,	2,	'2025-08-25 23:57:07',	'2025-09-02 06:26:39',	NULL,	NULL),
(10,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 18:38:34',	NULL,	NULL),
(10,	7,	5,	'2025-08-25 23:57:07',	'2025-09-02 01:52:56',	NULL,	NULL),
(10,	8,	4,	'2025-08-25 23:57:07',	'2025-09-02 01:28:55',	NULL,	NULL),
(10,	9,	6,	'2025-08-25 23:57:07',	'2025-09-02 01:45:49',	NULL,	NULL),
(10,	10,	7,	'2025-08-25 23:57:07',	'2025-09-02 04:22:22',	NULL,	NULL),
(10,	12,	8,	'2025-08-25 23:57:07',	'2025-09-02 16:34:20',	NULL,	NULL),
(10,	14,	9,	'2025-08-25 23:57:07',	'2025-09-02 21:44:38',	NULL,	NULL),
(10,	15,	10,	'2025-08-25 23:57:07',	'2025-09-02 11:00:09',	NULL,	NULL),
(11,	5,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:46:52',	NULL,	NULL),
(11,	7,	2,	'2025-08-25 23:57:07',	'2025-09-02 11:53:55',	NULL,	NULL),
(11,	9,	3,	'2025-08-25 23:57:07',	'2025-09-02 18:08:58',	NULL,	NULL),
(12,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 07:03:07',	NULL,	NULL),
(12,	5,	2,	'2025-08-25 23:57:07',	'2025-09-02 04:48:41',	NULL,	NULL),
(12,	7,	4,	'2025-08-25 23:57:07',	'2025-09-02 02:54:06',	NULL,	NULL),
(12,	8,	3,	'2025-08-25 23:57:07',	'2025-09-02 00:04:25',	NULL,	NULL),
(12,	9,	5,	'2025-08-25 23:57:07',	'2025-09-02 15:39:50',	NULL,	NULL),
(12,	12,	6,	'2025-08-25 23:57:07',	'2025-09-02 06:05:53',	NULL,	NULL),
(13,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 07:29:55',	NULL,	NULL),
(13,	2,	2,	'2025-08-25 23:57:07',	'2025-09-02 19:11:59',	NULL,	NULL),
(13,	5,	3,	'2025-08-25 23:57:07',	'2025-09-02 01:30:07',	NULL,	NULL),
(13,	7,	5,	'2025-08-25 23:57:07',	'2025-09-02 21:54:42',	NULL,	NULL),
(13,	8,	4,	'2025-08-25 23:57:07',	'2025-09-02 09:03:10',	NULL,	NULL),
(13,	9,	6,	'2025-08-25 23:57:07',	'2025-09-02 03:31:42',	NULL,	NULL),
(13,	10,	7,	'2025-08-25 23:57:07',	'2025-09-02 14:29:03',	NULL,	NULL),
(13,	12,	8,	'2025-08-25 23:57:07',	'2025-09-02 13:50:09',	NULL,	NULL),
(13,	13,	9,	'2025-08-25 23:57:07',	'2025-09-02 01:43:38',	NULL,	NULL),
(13,	14,	10,	'2025-08-25 23:57:07',	'2025-09-02 15:07:40',	NULL,	NULL),
(13,	15,	11,	'2025-08-25 23:57:07',	'2025-09-02 22:27:29',	NULL,	NULL),
(16,	1,	1,	'2025-08-25 23:57:07',	'2025-09-02 18:54:25',	NULL,	NULL),
(16,	11,	2,	'2025-08-25 23:57:07',	'2025-09-02 03:09:38',	NULL,	NULL),
(16,	12,	3,	'2025-08-25 23:57:07',	'2025-09-02 07:04:58',	NULL,	NULL),
(17,	12,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:55:54',	NULL,	NULL),
(17,	13,	2,	'2025-08-25 23:57:07',	'2025-09-02 12:24:36',	NULL,	NULL),
(17,	15,	3,	'2025-08-25 23:57:07',	'2025-09-02 08:15:18',	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_service_delivery_method_assignments`;
CREATE TABLE `wp_srvc_service_delivery_method_assignments` (
  `service_id` bigint(20) NOT NULL,
  `delivery_method_id` bigint(20) NOT NULL,
  `lead_time_days` int(11) NOT NULL DEFAULT 0,
  `sla_hours` int(11) DEFAULT NULL,
  `surcharge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`delivery_method_id`),
  KEY `idx_service_delivery_method__delivery_method_id` (`delivery_method_id`),
  CONSTRAINT `fk_service_delivery_method__delivery_method` FOREIGN KEY (`delivery_method_id`) REFERENCES `wp_srvc_delivery_methods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Delivery methods available for services with lead times and surcharges';

INSERT INTO `wp_srvc_service_delivery_method_assignments` (`service_id`, `delivery_method_id`, `lead_time_days`, `sla_hours`, `surcharge`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	5,	48,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 04:02:45',	NULL,	NULL),
(1,	3,	7,	72,	200.00,	'2025-08-25 23:57:07',	'2025-09-02 19:27:49',	NULL,	NULL),
(2,	1,	7,	72,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 13:10:53',	NULL,	NULL),
(2,	3,	10,	96,	500.00,	'2025-08-25 23:57:07',	'2025-09-02 07:30:59',	NULL,	NULL),
(3,	1,	14,	120,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 22:02:15',	NULL,	NULL),
(3,	5,	21,	168,	2000.00,	'2025-08-25 23:57:07',	'2025-09-02 15:38:19',	NULL,	NULL),
(4,	1,	2,	24,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 12:04:52',	NULL,	NULL),
(4,	5,	3,	48,	300.00,	'2025-08-25 23:57:07',	'2025-09-02 13:29:24',	NULL,	NULL),
(5,	1,	3,	48,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 07:12:22',	NULL,	NULL),
(6,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 19:33:40',	NULL,	NULL),
(7,	1,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 04:11:14',	NULL,	NULL),
(7,	5,	14,	168,	1000.00,	'2025-08-25 23:57:07',	'2025-09-02 10:15:12',	NULL,	NULL),
(8,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 14:42:18',	NULL,	NULL),
(9,	1,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 18:45:52',	NULL,	NULL),
(9,	5,	14,	168,	800.00,	'2025-08-25 23:57:07',	'2025-09-02 01:42:29',	NULL,	NULL),
(10,	1,	21,	240,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 00:14:49',	NULL,	NULL),
(10,	5,	28,	336,	2500.00,	'2025-08-25 23:57:07',	'2025-09-02 20:06:38',	NULL,	NULL),
(11,	1,	3,	48,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 03:48:44',	NULL,	NULL),
(12,	1,	7,	96,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 06:43:47',	NULL,	NULL),
(13,	1,	14,	168,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 22:12:41',	NULL,	NULL),
(13,	5,	21,	240,	1500.00,	'2025-08-25 23:57:07',	'2025-09-02 18:52:07',	NULL,	NULL),
(14,	1,	1,	24,	50.00,	'2025-08-25 23:57:07',	'2025-09-02 03:42:32',	NULL,	NULL),
(14,	2,	0,	4,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 09:56:17',	NULL,	NULL),
(15,	1,	0,	4,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 14:33:53',	NULL,	NULL),
(15,	2,	0,	2,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 19:00:31',	NULL,	NULL),
(15,	5,	0,	1,	100.00,	'2025-08-25 23:57:07',	'2025-09-02 03:21:00',	NULL,	NULL),
(16,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 07:43:26',	NULL,	NULL),
(16,	2,	2,	48,	-25.00,	'2025-08-25 23:57:07',	'2025-09-02 04:34:09',	NULL,	NULL),
(16,	3,	3,	56,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 23:40:29',	NULL,	NULL),
(17,	1,	7,	96,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 08:39:57',	NULL,	NULL),
(17,	3,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-09-02 20:18:21',	NULL,	NULL);

DROP TABLE IF EXISTS `wp_srvc_service_equipment_assignments`;
CREATE TABLE `wp_srvc_service_equipment_assignments` (
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
  CONSTRAINT `fk_service_equipment__equipment` FOREIGN KEY (`equipment_id`) REFERENCES `wp_srvc_equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_equipment__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Equipment requirements for services';


DROP TABLE IF EXISTS `wp_srvc_service_prices`;
CREATE TABLE `wp_srvc_service_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `pricing_tier_id` bigint(20) NOT NULL,
  `pricing_model_id` bigint(20) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `amount` decimal(12,2) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT 'Pricing unit: hour, user, device, site, month',
  `min_qty` decimal(12,3) NOT NULL DEFAULT 1.000,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `setup_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(512) DEFAULT NULL,
  `effective_from` date NOT NULL DEFAULT curdate(),
  `effective_to` date DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_price__scope_effective` (`service_id`,`pricing_tier_id`,`pricing_model_id`,`effective_from`),
  KEY `idx_service_price__service_id` (`service_id`),
  KEY `idx_service_price__pricing_tier_id` (`pricing_tier_id`),
  KEY `idx_service_price__pricing_model_id` (`pricing_model_id`),
  KEY `idx_service_price__effective_dates` (`effective_from`,`effective_to`),
  KEY `idx_service_price__currency` (`currency`),
  CONSTRAINT `fk_service_price__pricing_model` FOREIGN KEY (`pricing_model_id`) REFERENCES `wp_srvc_pricing_models` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__pricing_tier` FOREIGN KEY (`pricing_tier_id`) REFERENCES `wp_srvc_pricing_tiers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service pricing with effective date ranges';

INSERT INTO `wp_srvc_service_prices` (`id`, `service_id`, `pricing_tier_id`, `pricing_model_id`, `currency`, `amount`, `unit`, `min_qty`, `max_qty`, `setup_fee`, `notes`, `effective_from`, `effective_to`, `version`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1,	4,	'CAD',	45.00,	'user/month',	5.000,	25.000,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 12:59:49',	NULL,	NULL),
(2,	1,	2,	4,	'CAD',	42.00,	'user/month',	5.000,	25.000,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:22:23',	NULL,	NULL),
(3,	1,	3,	5,	'CAD',	480.00,	'user/year',	10.000,	50.000,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 15:52:28',	NULL,	NULL),
(4,	2,	1,	4,	'CAD',	65.00,	'user/month',	10.000,	100.000,	1500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:15:10',	NULL,	NULL),
(5,	2,	2,	4,	'CAD',	60.00,	'user/month',	10.000,	100.000,	1200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:38:29',	NULL,	NULL),
(6,	2,	3,	5,	'CAD',	650.00,	'user/year',	25.000,	200.000,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:26:57',	NULL,	NULL),
(7,	3,	2,	4,	'CAD',	85.00,	'user/month',	25.000,	NULL,	5000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 05:19:16',	NULL,	NULL),
(8,	3,	3,	5,	'CAD',	900.00,	'user/year',	50.000,	NULL,	10000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 22:15:29',	NULL,	NULL),
(9,	3,	4,	5,	'CAD',	850.00,	'user/year',	100.000,	NULL,	8000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:19:36',	NULL,	NULL),
(10,	4,	1,	2,	'CAD',	125.00,	'hour',	4.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 01:51:37',	NULL,	NULL),
(11,	4,	2,	2,	'CAD',	120.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 11:19:16',	NULL,	NULL),
(12,	4,	3,	2,	'CAD',	115.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:01:29',	NULL,	NULL),
(13,	5,	1,	3,	'CAD',	185.00,	'run',	1.000,	NULL,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 05:09:37',	NULL,	NULL),
(14,	5,	2,	3,	'CAD',	175.00,	'run',	10.000,	NULL,	200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:43:38',	NULL,	NULL),
(15,	5,	3,	3,	'CAD',	165.00,	'run',	25.000,	NULL,	150.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:09:18',	NULL,	NULL),
(16,	6,	1,	3,	'CAD',	235.00,	'run',	1.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 02:35:39',	NULL,	NULL),
(17,	6,	2,	3,	'CAD',	220.00,	'run',	10.000,	NULL,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 00:30:21',	NULL,	NULL),
(18,	6,	3,	3,	'CAD',	205.00,	'run',	25.000,	NULL,	200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 18:44:46',	NULL,	NULL),
(19,	7,	1,	3,	'CAD',	450.00,	'run',	1.000,	NULL,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:12:51',	NULL,	NULL),
(20,	7,	2,	3,	'CAD',	420.00,	'run',	5.000,	NULL,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:49:57',	NULL,	NULL),
(21,	7,	3,	3,	'CAD',	390.00,	'run',	10.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 19:31:11',	NULL,	NULL),
(22,	8,	1,	3,	'CAD',	850.00,	'camera',	4.000,	8.000,	800.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 11:06:05',	NULL,	NULL),
(23,	8,	2,	3,	'CAD',	800.00,	'camera',	4.000,	16.000,	600.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 20:56:51',	NULL,	NULL),
(24,	9,	1,	3,	'CAD',	1250.00,	'camera',	8.000,	32.000,	1500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:26:02',	NULL,	NULL),
(25,	9,	2,	3,	'CAD',	1150.00,	'camera',	8.000,	64.000,	1200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 06:19:39',	NULL,	NULL),
(26,	9,	3,	3,	'CAD',	1050.00,	'camera',	16.000,	NULL,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 09:20:06',	NULL,	NULL),
(27,	10,	2,	1,	'CAD',	75000.00,	'system',	1.000,	NULL,	5000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:41:36',	NULL,	NULL),
(28,	10,	3,	1,	'CAD',	70000.00,	'system',	1.000,	NULL,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 14:27:41',	NULL,	NULL),
(29,	11,	1,	3,	'CAD',	1200.00,	'door',	1.000,	1.000,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:13:38',	NULL,	NULL),
(30,	11,	2,	3,	'CAD',	1100.00,	'door',	1.000,	1.000,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 22:45:06',	NULL,	NULL),
(31,	12,	1,	3,	'CAD',	1800.00,	'door',	2.000,	16.000,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 02:04:37',	NULL,	NULL),
(32,	12,	2,	3,	'CAD',	1650.00,	'door',	2.000,	32.000,	800.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 14:07:47',	NULL,	NULL),
(33,	12,	3,	3,	'CAD',	1500.00,	'door',	4.000,	NULL,	600.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 16:25:04',	NULL,	NULL),
(34,	13,	2,	1,	'CAD',	25000.00,	'system',	1.000,	NULL,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 15:41:58',	NULL,	NULL),
(35,	13,	3,	1,	'CAD',	22000.00,	'system',	1.000,	NULL,	2000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 05:14:41',	NULL,	NULL),
(36,	14,	1,	2,	'CAD',	95.00,	'hour',	1.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:07:30',	NULL,	NULL),
(37,	14,	2,	2,	'CAD',	90.00,	'hour',	1.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:53:28',	NULL,	NULL),
(38,	15,	1,	4,	'CAD',	250.00,	'user/month',	10.000,	NULL,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 14:04:50',	NULL,	NULL),
(39,	15,	2,	4,	'CAD',	225.00,	'user/month',	25.000,	NULL,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 22:43:48',	NULL,	NULL),
(40,	15,	3,	4,	'CAD',	200.00,	'user/month',	50.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 23:24:31',	NULL,	NULL),
(41,	16,	1,	2,	'CAD',	175.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 00:51:11',	NULL,	NULL),
(42,	16,	2,	2,	'CAD',	165.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 06:02:20',	NULL,	NULL),
(43,	16,	3,	2,	'CAD',	155.00,	'hour',	24.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 03:38:10',	NULL,	NULL),
(44,	17,	1,	2,	'CAD',	185.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 00:03:49',	NULL,	NULL),
(45,	17,	2,	2,	'CAD',	175.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 13:24:38',	NULL,	NULL),
(46,	17,	3,	2,	'CAD',	165.00,	'hour',	24.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-09-02 18:51:42',	NULL,	NULL);

DELIMITER ;;

CREATE TRIGGER `tr_service_price_overlap_check` BEFORE INSERT ON `wp_srvc_service_prices` FOR EACH ROW
BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `wp_srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;;

CREATE TRIGGER `tr_service_price_overlap_check_update` BEFORE UPDATE ON `wp_srvc_service_prices` FOR EACH ROW
BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `wp_srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND sp.id <> NEW.id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;;

DELIMITER ;

DROP TABLE IF EXISTS `wp_srvc_service_relationships`;
CREATE TABLE `wp_srvc_service_relationships` (
  `service_id` bigint(20) NOT NULL,
  `related_service_id` bigint(20) NOT NULL,
  `relation_type` enum('prerequisite','dependency','incompatible_with','substitute_for') NOT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`related_service_id`,`relation_type`),
  KEY `idx_service_relation__related_service_id` (`related_service_id`),
  KEY `idx_service_relation__relation_type` (`relation_type`),
  CONSTRAINT `fk_service_relation__related_service` FOREIGN KEY (`related_service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relation__service` FOREIGN KEY (`service_id`) REFERENCES `wp_srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Service relationships: prerequisites, dependencies, incompatibilities, substitutions';

INSERT INTO `wp_srvc_service_relationships` (`service_id`, `related_service_id`, `relation_type`, `notes`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	2,	'substitute_for',	'Professional VoIP can substitute for Basic VoIP with additional features',	'2025-08-25 23:57:12',	'2025-09-02 03:31:51',	NULL,	NULL),
(1,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-09-02 04:44:16',	NULL,	NULL),
(1,	5,	'prerequisite',	'Cat6 cabling required for VoIP phone connectivity',	'2025-08-25 23:57:12',	'2025-09-02 13:05:45',	NULL,	NULL),
(2,	3,	'substitute_for',	'Enterprise VoIP can substitute for Professional VoIP in large deployments',	'2025-08-25 23:57:12',	'2025-09-02 03:16:00',	NULL,	NULL),
(2,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-09-02 01:02:43',	NULL,	NULL),
(2,	6,	'prerequisite',	'Cat6A cabling recommended for professional VoIP systems',	'2025-08-25 23:57:12',	'2025-09-02 19:25:35',	NULL,	NULL),
(2,	15,	'dependency',	'Professional VoIP systems typically require premium support',	'2025-08-25 23:57:12',	'2025-09-02 21:59:45',	NULL,	NULL),
(3,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-09-02 03:42:04',	NULL,	NULL),
(3,	7,	'prerequisite',	'Fiber backbone required for enterprise VoIP redundancy',	'2025-08-25 23:57:12',	'2025-09-02 00:31:05',	NULL,	NULL),
(3,	15,	'prerequisite',	'Enterprise VoIP requires premium support contract',	'2025-08-25 23:57:12',	'2025-09-02 15:29:13',	NULL,	NULL),
(5,	6,	'incompatible_with',	'Cat6 and Cat6A cabling are alternative solutions',	'2025-08-25 23:57:12',	'2025-09-02 03:52:52',	NULL,	NULL),
(6,	7,	'incompatible_with',	'Cat6A and Fiber are typically alternative backbone solutions',	'2025-08-25 23:57:12',	'2025-09-02 20:56:38',	NULL,	NULL),
(8,	9,	'substitute_for',	'Professional cameras can substitute for Basic cameras',	'2025-08-25 23:57:12',	'2025-09-02 21:04:38',	NULL,	NULL),
(9,	6,	'prerequisite',	'Cat6A cabling required for professional camera systems',	'2025-08-25 23:57:12',	'2025-09-02 18:33:15',	NULL,	NULL),
(9,	10,	'substitute_for',	'Enterprise cameras can substitute for Professional cameras',	'2025-08-25 23:57:12',	'2025-09-02 05:32:21',	NULL,	NULL),
(10,	7,	'prerequisite',	'Fiber backbone required for enterprise camera systems',	'2025-08-25 23:57:12',	'2025-09-02 20:02:03',	NULL,	NULL),
(10,	15,	'prerequisite',	'Enterprise camera systems require premium support',	'2025-08-25 23:57:12',	'2025-09-02 11:33:10',	NULL,	NULL),
(10,	17,	'prerequisite',	'Security audit recommended for enterprise camera systems',	'2025-08-25 23:57:12',	'2025-09-02 21:39:43',	NULL,	NULL),
(11,	12,	'substitute_for',	'Multi-door access can substitute for Basic access control',	'2025-08-25 23:57:12',	'2025-09-02 01:39:05',	NULL,	NULL),
(12,	13,	'substitute_for',	'Enterprise access can substitute for Multi-door access control',	'2025-08-25 23:57:12',	'2025-09-02 15:16:18',	NULL,	NULL),
(13,	15,	'prerequisite',	'Enterprise access control requires premium support',	'2025-08-25 23:57:12',	'2025-09-02 23:24:14',	NULL,	NULL),
(13,	17,	'prerequisite',	'Security audit required before enterprise access control implementation',	'2025-08-25 23:57:12',	'2025-09-02 23:12:17',	NULL,	NULL),
(14,	15,	'incompatible_with',	'Basic and Premium support are mutually exclusive',	'2025-08-25 23:57:12',	'2025-09-02 21:48:44',	NULL,	NULL);

DELIMITER ;;

CREATE TRIGGER `tr_service_relation_no_self_ref` BEFORE INSERT ON `wp_srvc_service_relationships` FOR EACH ROW
BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;;

CREATE TRIGGER `tr_service_relation_no_self_ref_update` BEFORE UPDATE ON `wp_srvc_service_relationships` FOR EACH ROW
BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;;

DELIMITER ;

DROP TABLE IF EXISTS `wp_srvc_service_types`;
CREATE TABLE `wp_srvc_service_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_type__name` (`name`),
  UNIQUE KEY `uq_service_type__code` (`code`),
  KEY `idx_service_type__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='Defines types of services offered (e.g., consulting, hosting, support)';

INSERT INTO `wp_srvc_service_types` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Installation Services',	'INSTALL',	'2025-08-25 23:57:07',	'2025-09-02 22:13:27',	NULL,	NULL,	NULL),
(2,	'Maintenance & Support',	'SUPPORT',	'2025-08-25 23:57:07',	'2025-09-02 22:42:59',	NULL,	NULL,	NULL),
(3,	'Consulting Services',	'CONSULT',	'2025-08-25 23:57:07',	'2025-09-02 22:54:34',	NULL,	NULL,	NULL),
(4,	'Training Services',	'TRAINING',	'2025-08-25 23:57:07',	'2025-09-02 22:23:56',	NULL,	NULL,	NULL),
(5,	'Configuration Services',	'CONFIG',	'2025-08-25 23:57:07',	'2025-09-02 19:15:56',	NULL,	NULL,	NULL),
(6,	'Repair Services',	'REPAIR',	'2025-08-25 23:57:07',	'2025-09-02 05:07:54',	NULL,	NULL,	NULL),
(7,	'Upgrade Services',	'UPGRADE',	'2025-08-25 23:57:07',	'2025-09-02 15:51:40',	NULL,	NULL,	NULL),
(8,	'Design & Planning',	'DESIGN',	'2025-08-25 23:57:07',	'2025-09-02 15:54:40',	NULL,	NULL,	NULL),
(9,	'Monitoring Services',	'MONITOR',	'2025-08-25 23:57:07',	'2025-09-02 07:58:20',	NULL,	NULL,	NULL),
(10,	'Emergency Services',	'EMERGENCY',	'2025-08-25 23:57:07',	'2025-09-02 16:07:41',	NULL,	NULL,	NULL);

-- 2025-09-03 00:05:19 UTC
