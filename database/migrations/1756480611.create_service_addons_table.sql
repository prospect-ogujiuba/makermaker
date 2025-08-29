-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_addons`;
CREATE TABLE `{!!prefix!!}srvc_service_addons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `addon_service_id` bigint(20) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `min_qty` decimal(12,3) NOT NULL DEFAULT 0.000,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `price_delta` decimal(12,2) DEFAULT NULL,
  `multiplier` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_addon` (`service_id`, `addon_service_id`),
  KEY `idx_service_id` (`service_id`),
  KEY `idx_addon_service_id` (`addon_service_id`),
  CONSTRAINT `fk_service_addon__addon_service` FOREIGN KEY (`addon_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_addon__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service add-on relationships with pricing overrides';

DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref`;
CREATE TRIGGER `tr_service_addon_no_self_ref` BEFORE INSERT ON `{!!prefix!!}srvc_service_addons` FOR EACH ROW BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot be an addon to itself'; END IF; END;

DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref_update`;
CREATE TRIGGER `tr_service_addon_no_self_ref_update` BEFORE UPDATE ON `{!!prefix!!}srvc_service_addons` FOR EACH ROW BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot be an addon to itself';END IF; END;

INSERT INTO `{!!prefix!!}srvc_service_addons` (`service_id`, `addon_service_id`, `required`, `min_qty`, `max_qty`, `price_delta`, `multiplier`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	4,	1,	1.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(1,	14,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	4,	1,	1.000,	1.000,	NULL,	1.2000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	15,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	4,	1,	1.000,	1.000,	NULL,	1.5000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	15,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(8,	5,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(9,	6,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(10,	7,	0,	0.000,	NULL,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(11,	16,	0,	0.000,	1.000,	-10.00,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(12,	17,	0,	0.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(13,	17,	1,	1.000,	1.000,	NULL,	1.0000,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL);

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref`;
DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref_update`;
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_addons`;