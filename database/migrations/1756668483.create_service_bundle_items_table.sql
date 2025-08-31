-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;
CREATE TABLE `{!!prefix!!}srvc_bundle_items` (
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
  CONSTRAINT `fk_bundle_item__bundle` FOREIGN KEY (`bundle_id`) REFERENCES `{!!prefix!!}srvc_bundles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle_item__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Services included in bundles with quantities and discounts';

INSERT INTO `{!!prefix!!}srvc_bundle_items` (`bundle_id`, `service_id`, `quantity`, `discount_pct`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	5,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	8,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	11,	1.000,	8.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	14,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	2,	1.000,	18.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	6,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	9,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	12,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	15,	1.000,	25.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	10,	1.000,	8.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	13,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	15,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	17,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	6,	1.000,	15.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	7,	1.000,	10.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	16,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(5,	2,	1.000,	12.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(5,	4,	1.000,	20.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundle_items`;