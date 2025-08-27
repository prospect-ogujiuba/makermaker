-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_attribute_definitions`;
CREATE TABLE `{!!prefix!!}srvc_attribute_definitions` (
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
  CONSTRAINT `fk_attribute_definition__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `{!!prefix!!}srvc_service_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Parametric attribute definitions per service type';

INSERT INTO `{!!prefix!!}srvc_attribute_definitions` (`id`, `service_type_id`, `code`, `label`, `data_type`, `enum_options`, `unit`, `required`, `created_at`, `updated_at`) VALUES
(1,	1,	'user_count',	'Number of Users',	'int',	NULL,	'users',	1,	NOW(),	NOW()),
(2,	1,	'concurrent_calls',	'Concurrent Calls',	'int',	NULL,	'calls',	1,	NOW(),	NOW()),
(3,	1,	'sip_trunks',	'SIP Trunk Lines',	'int',	NULL,	'lines',	0,	NOW(),	NOW()),
(4,	1,	'auto_attendant',	'Auto Attendant Required',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(5,	1,	'voicemail_to_email',	'Voicemail to Email',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(6,	1,	'call_recording',	'Call Recording Required',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(7,	2,	'cable_runs',	'Number of Cable Runs',	'int',	NULL,	'runs',	1,	NOW(),	NOW()),
(8,	2,	'cable_length',	'Total Cable Length',	'int',	NULL,	'feet',	0,	NOW(),	NOW()),
(9,	2,	'outlet_count',	'Network Outlets',	'int',	NULL,	'outlets',	1,	NOW(),	NOW()),
(10,	2,	'cable_category',	'Cable Category',	'enum',	'[\"Cat5e\", \"Cat6\", \"Cat6A\", \"Cat7\", \"Fiber\"]',	NULL,	1,	NOW(),	NOW()),
(11,	2,	'fiber_required',	'Fiber Optic Required',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(12,	2,	'wireless_coverage',	'Wireless Coverage Area',	'int',	NULL,	'sq_ft',	0,	NOW(),	NOW()),
(13,	2,	'access_points',	'Number of Access Points',	'int',	NULL,	'devices',	0,	NOW(),	NOW()),
(14,	3,	'camera_count',	'Number of Cameras',	'int',	NULL,	'cameras',	1,	NOW(),	NOW()),
(15,	3,	'recording_days',	'Recording Retention',	'int',	NULL,	'days',	1,	NOW(),	NOW()),
(16,	3,	'night_vision',	'Night Vision Required',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(17,	3,	'outdoor_cameras',	'Outdoor Cameras',	'int',	NULL,	'cameras',	0,	NOW(),	NOW()),
(18,	3,	'ptz_cameras',	'PTZ Cameras',	'int',	NULL,	'cameras',	0,	NOW(),	NOW()),
(19,	3,	'coverage_area',	'Coverage Area',	'int',	NULL,	'sq_ft',	1,	NOW(),	NOW()),
(20,	4,	'door_count',	'Number of Doors',	'int',	NULL,	'doors',	1,	NOW(),	NOW()),
(21,	4,	'user_capacity',	'User Capacity',	'int',	NULL,	'users',	1,	NOW(),	NOW()),
(22,	4,	'card_technology',	'Card Technology',	'enum',	'[\"Proximity\", \"Mifare\", \"iClass\", \"Mobile\", \"Biometric\"]',	NULL,	1,	NOW(),	NOW()),
(23,	4,	'integration_required',	'System Integration',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(24,	4,	'time_attendance',	'Time & Attendance',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(25,	5,	'install_duration',	'Installation Duration',	'int',	NULL,	'days',	1,	NOW(),	NOW()),
(26,	5,	'crew_size',	'Installation Crew Size',	'int',	NULL,	'people',	0,	NOW(),	NOW()),
(27,	5,	'after_hours',	'After Hours Installation',	'bool',	NULL,	NULL,	0,	NOW(),	NOW()),
(28,	5,	'weekend_work',	'Weekend Work Required',	'bool',	NULL,	NULL,	0,	NOW(),	NOW());

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_attribute_definitions`;
