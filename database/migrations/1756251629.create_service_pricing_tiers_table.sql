-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_tiers`;
CREATE TABLE `{!!prefix!!}srvc_pricing_tiers` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Pricing tiers: Retail, Partner, Enterprise, etc.';

INSERT INTO `{!!prefix!!}srvc_pricing_tiers` (`id`, `name`, `code`, `sort_order`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Small Business',	'SMB',	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'Mid-Market',	'MID',	2,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'Enterprise',	'ENT',	3,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'Government',	'GOV',	4,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'Non-Profit',	'NPO',	5,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_tiers`;
