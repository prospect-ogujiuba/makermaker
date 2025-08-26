-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_model`;
CREATE TABLE `{!!prefix!!}srvc_pricing_model` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Pricing models: fixed, hourly, per_unit, subscription, tiered, volume';

INSERT INTO `{!!prefix!!}srvc_pricing_model` (`id`, `name`, `code`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Fixed Project',	'FIXED',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'Hourly Rate',	'HOURLY',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'Per Unit/Device',	'UNIT',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'Monthly Subscription',	'MONTHLY',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'Annual Contract',	'ANNUAL',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(6,	'Per Square Foot',	'SQ_FT',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(7,	'Tiered Pricing',	'TIERED',	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_model`;
