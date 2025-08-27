-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_complexities`;
CREATE TABLE `{!!prefix!!}srvc_complexities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `level` int(11) NOT NULL,
  `price_multiplier` decimal(8,4) NOT NULL DEFAULT 1.0000,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_complexity__name` (`name`),
  UNIQUE KEY `uq_complexity__level` (`level`),
  KEY `idx_complexity__level` (`level`),
  KEY `idx_complexity__deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Service complexity levels with associated pricing multipliers';

INSERT INTO `{!!prefix!!}srvc_complexities` (`id`, `name`, `level`, `price_multiplier`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
(1,	'Basic',	1,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(2,	'Standard',	2,	1.3000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(3,	'Professional',	3,	1.6000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(4,	'Enterprise',	4,	2.2000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL),
(5,	'Custom/Complex',	5,	3.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_complexities`;
