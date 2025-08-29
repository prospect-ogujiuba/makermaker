-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_types`;
CREATE TABLE `{!!prefix!!}srvc_service_types` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Defines types of services offered (e.g., consulting, hosting, support)';

INSERT INTO `{!!prefix!!}srvc_service_types` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1, 'Installation Services', 'INSTALL', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(2, 'Maintenance & Support', 'SUPPORT', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(3, 'Consulting Services', 'CONSULT', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(4, 'Training Services', 'TRAINING', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(5, 'Configuration Services', 'CONFIG', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(6, 'Repair Services', 'REPAIR', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(7, 'Upgrade Services', 'UPGRADE', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(8, 'Design & Planning', 'DESIGN', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(9, 'Monitoring Services', 'MONITOR', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL),
(10, 'Emergency Services', 'EMERGENCY', '2025-08-25 23:57:07', '2025-08-25 23:57:07', NULL, NULL, NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_types`;
