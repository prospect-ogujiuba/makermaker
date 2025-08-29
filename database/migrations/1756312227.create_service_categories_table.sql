-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_categories`;
CREATE TABLE `{!!prefix!!}srvc_categories` (
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
  CONSTRAINT `fk_category__parent` FOREIGN KEY (`parent_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hierarchical service categorization';

INSERT INTO `{!!prefix!!}srvc_categories` (`id`, `parent_id`, `name`, `slug`, `path`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, NULL, 'Telecommunications', 'telecommunications', '/telecommunications', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(2, NULL, 'Network & Infrastructure', 'network-infrastructure', '/network-infrastructure', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(3, NULL, 'Security Systems', 'security-systems', '/security-systems', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(4, NULL, 'Business Solutions', 'business-solutions', '/business-solutions', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(5, 1, 'VoIP Systems', 'voip-systems', '/telecommunications/voip-systems', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(6, 1, 'Phone Systems', 'phone-systems', '/telecommunications/phone-systems', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(7, 1, 'Unified Communications', 'unified-communications', '/telecommunications/unified-communications', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(8, 1, 'Video Conferencing', 'video-conferencing', '/telecommunications/video-conferencing', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(9, 2, 'Network Cabling', 'network-cabling', '/network-infrastructure/network-cabling', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(10, 2, 'Wireless Networks', 'wireless-networks', '/network-infrastructure/wireless-networks', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(11, 2, 'Network Equipment', 'network-equipment', '/network-infrastructure/network-equipment', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(12, 2, 'Fiber Optic Solutions', 'fiber-optic', '/network-infrastructure/fiber-optic', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(13, 3, 'Camera Systems', 'camera-systems', '/security-systems/camera-systems', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(14, 3, 'Access Control', 'access-control', '/security-systems/access-control', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(15, 3, 'Alarm Systems', 'alarm-systems', '/security-systems/alarm-systems', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(16, 3, 'Integrated Security', 'integrated-security', '/security-systems/integrated-security', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(17, 4, 'IT Support', 'it-support', '/business-solutions/it-support', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(18, 4, 'Consulting Services', 'consulting', '/business-solutions/consulting', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(19, 4, 'Training', 'training', '/business-solutions/training', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(20, 4, 'Maintenance Plans', 'maintenance', '/business-solutions/maintenance', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_categories`;