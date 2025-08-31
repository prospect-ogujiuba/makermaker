-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_delivery_method_assignments`;
CREATE TABLE `{!!prefix!!}srvc_service_delivery_method_assignments` (
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
  CONSTRAINT `fk_service_delivery_method__delivery_method` FOREIGN KEY (`delivery_method_id`) REFERENCES `{!!prefix!!}srvc_delivery_methods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_delivery_method__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Delivery methods available for services with lead times and surcharges';

INSERT INTO `{!!prefix!!}srvc_service_delivery_method_assignments` (`service_id`, `delivery_method_id`, `lead_time_days`, `sla_hours`, `surcharge`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	5,	48,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	3,	7,	72,	200.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	1,	7,	72,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	3,	10,	96,	500.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	1,	14,	120,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	5,	21,	168,	2000.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	1,	2,	24,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	5,	3,	48,	300.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(5,	1,	3,	48,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(6,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(7,	1,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(7,	5,	14,	168,	1000.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(8,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(9,	1,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(9,	5,	14,	168,	800.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(10,	1,	21,	240,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(10,	5,	28,	336,	2500.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(11,	1,	3,	48,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(12,	1,	7,	96,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(13,	1,	14,	168,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(13,	5,	21,	240,	1500.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(14,	1,	1,	24,	50.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(14,	2,	0,	4,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(15,	1,	0,	4,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(15,	2,	0,	2,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(15,	5,	0,	1,	100.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(16,	1,	5,	72,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(16,	2,	2,	48,	-25.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(16,	3,	3,	56,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(17,	1,	7,	96,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(17,	3,	10,	120,	0.00,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_delivery_method_assignments`;
