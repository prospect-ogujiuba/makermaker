-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_services`;
CREATE TABLE `{!!prefix!!}srvc_services` (
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
  CONSTRAINT `fk_service__category` FOREIGN KEY (`category_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__complexity` FOREIGN KEY (`complexity_id`) REFERENCES `{!!prefix!!}srvc_complexities` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `{!!prefix!!}srvc_service_types` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Core service catalog entries';

INSERT INTO `{!!prefix!!}srvc_services` (`id`, `sku`, `slug`, `name`, `short_desc`, `long_desc`, `category_id`, `service_type_id`, `complexity_id`, `is_active`, `is_addon`, `default_unit`, `metadata`, `version`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'VOIP-SYS-BASIC',	'voip-system-basic',	'Basic VoIP System',	'Entry-level VoIP phone system for small offices',	'Complete VoIP phone system including IP PBX, desk phones, and basic features like voicemail, call forwarding, and auto-attendant. Perfect for small businesses with up to 25 users.',	5,	1,	1,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'VOIP-SYS-PROF',	'voip-system-professional',	'Professional VoIP System',	'Advanced VoIP solution for growing businesses',	'Comprehensive VoIP system with advanced features including call recording, unified messaging, mobile integration, and detailed reporting. Suitable for businesses with 25-100 users.',	5,	1,	3,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'VOIP-SYS-ENT',	'voip-system-enterprise',	'Enterprise VoIP System',	'Full-featured enterprise communication platform',	'Enterprise-grade unified communications platform with advanced call routing, conference bridging, CRM integration, and redundancy. Supports 100+ users with 99.99% uptime SLA.',	5,	1,	4,	1,	0,	'users',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'VOIP-INST-001',	'voip-installation',	'VoIP System Installation',	'Professional VoIP system installation service',	'Complete installation and configuration of VoIP phone systems including equipment mounting, network configuration, user setup, and training.',	5,	5,	2,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'CBL-INST-001',	'network-cabling-cat6',	'Cat6 Network Cabling',	'Cat6 structured cabling installation',	'Professional installation of Cat6 structured cabling including cable runs, patch panels, outlets, and testing. Includes 25-year manufacturer warranty.',	9,	2,	2,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(6,	'CBL-INST-002',	'network-cabling-cat6a',	'Cat6A Network Cabling',	'Cat6A high-performance cabling installation',	'Premium Cat6A shielded cabling installation for high-speed networks up to 10Gbps. Includes professional termination, testing, and certification.',	9,	2,	3,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(7,	'CBL-INST-003',	'fiber-optic-installation',	'Fiber Optic Installation',	'Single/multi-mode fiber optic cabling',	'Professional fiber optic cable installation including fusion splicing, testing, and documentation. Supports long-distance and high-bandwidth applications.',	12,	2,	4,	1,	0,	'runs',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(8,	'CAM-SYS-001',	'ip-camera-system-basic',	'Basic IP Camera System',	'Entry-level IP camera surveillance system',	'Complete IP camera system with 4-8 cameras, network video recorder, and basic monitoring software. Includes mobile app access and 30-day recording.',	13,	3,	2,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(9,	'CAM-SYS-002',	'ip-camera-system-pro',	'Professional Camera System',	'Advanced IP camera system with analytics',	'Professional IP camera system with intelligent video analytics, motion detection, facial recognition, and advanced search capabilities. Suitable for medium businesses.',	13,	3,	3,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(10,	'CAM-SYS-003',	'ip-camera-system-ent',	'Enterprise Camera System',	'Large-scale enterprise surveillance solution',	'Enterprise-grade IP camera system with redundant NVRs, advanced analytics, integration APIs, and centralized management. Supports 100+ cameras with scalable architecture.',	13,	3,	4,	1,	0,	'cameras',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(11,	'AC-SYS-001',	'access-control-basic',	'Basic Access Control',	'Single-door access control system',	'Basic access control system for single door with card readers, electronic lock, and simple user management. Includes 100 proximity cards.',	14,	4,	1,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(12,	'AC-SYS-002',	'access-control-multi',	'Multi-Door Access Control',	'Multi-door access control system',	'Comprehensive access control system for multiple doors with centralized management, time/attendance tracking, and detailed reporting.',	14,	4,	3,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(13,	'AC-SYS-003',	'access-control-enterprise',	'Enterprise Access Control',	'Large-scale enterprise access control',	'Enterprise access control platform with biometric integration, visitor management, emergency lockdown, and integration with HR systems.',	14,	4,	4,	1,	0,	'doors',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(14,	'SUP-001',	'technical-support-basic',	'Basic Technical Support',	'Business hours phone and email support',	'Standard technical support during business hours (8AM-6PM) via phone and email. Includes remote diagnostics and basic troubleshooting.',	17,	6,	1,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(15,	'SUP-002',	'technical-support-premium',	'Premium Technical Support',	'24/7 technical support with priority response',	'Premium support with 24/7 availability, 2-hour response time, on-site service, and dedicated account manager. Includes proactive monitoring.',	17,	6,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(16,	'CONS-001',	'network-assessment',	'Network Assessment',	'Comprehensive network infrastructure assessment',	'Professional assessment of existing network infrastructure including performance analysis, security review, and improvement recommendations.',	18,	7,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(17,	'CONS-002',	'security-audit',	'Security System Audit',	'Security system evaluation and recommendations',	'Comprehensive security audit including physical security assessment, system vulnerabilities, and compliance review with detailed recommendations.',	18,	7,	3,	1,	0,	'hours',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_services`;