-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundles`;
CREATE TABLE `{!!prefix!!}srvc_bundles` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Service bundles for grouped offerings';

INSERT INTO `{!!prefix!!}srvc_bundles` (`id`, `name`, `slug`, `short_desc`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Small Office Complete',	'small-office-complete',	'Complete IT solution for small offices (5-15 employees)',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'Medium Business Package',	'medium-business-package',	'Comprehensive IT infrastructure for growing businesses (15-50 employees)',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'Enterprise Security Suite',	'enterprise-security-suite',	'Complete security solution with cameras, access control, and monitoring',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'Network Infrastructure Bundle',	'network-infrastructure-bundle',	'Complete network setup with cabling, equipment, and wireless',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'Communication Package',	'communication-package',	'VoIP system with professional installation and training',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundles`;
