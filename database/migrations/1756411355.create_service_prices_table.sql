-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_prices`;
CREATE TABLE `{!!prefix!!}srvc_service_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `pricing_tier_id` bigint(20) NOT NULL,
  `pricing_model_id` bigint(20) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `amount` decimal(12,2) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL COMMENT 'Pricing unit: hour, user, device, site, month',
  `min_qty` decimal(12,3) NOT NULL DEFAULT 1.000,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `setup_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(512) DEFAULT NULL,
  `effective_from` date NOT NULL DEFAULT curdate(),
  `effective_to` date DEFAULT NULL,
  `version` int(11) NOT NULL DEFAULT 1 COMMENT 'Optimistic locking version',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_price__scope_effective` (`service_id`,`pricing_tier_id`,`pricing_model_id`,`effective_from`),
  KEY `idx_service_price__service_id` (`service_id`),
  KEY `idx_service_price__pricing_tier_id` (`pricing_tier_id`),
  KEY `idx_service_price__pricing_model_id` (`pricing_model_id`),
  KEY `idx_service_price__effective_dates` (`effective_from`,`effective_to`),
  KEY `idx_service_price__currency` (`currency`),
  CONSTRAINT `fk_service_price__pricing_model` FOREIGN KEY (`pricing_model_id`) REFERENCES `{!!prefix!!}srvc_pricing_models` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__pricing_tier` FOREIGN KEY (`pricing_tier_id`) REFERENCES `{!!prefix!!}srvc_pricing_tiers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_price__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Service pricing with effective date ranges';

DROP TRIGGER IF EXISTS `tr_service_price_overlap_check`;
CREATE TRIGGER `tr_service_price_overlap_check` BEFORE INSERT ON `{!!prefix!!}srvc_service_prices` FOR EACH ROW BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `{!!prefix!!}srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;

DROP TRIGGER IF EXISTS `tr_service_price_overlap_check_update`;
CREATE TRIGGER `tr_service_price_overlap_check_update` BEFORE UPDATE ON `{!!prefix!!}srvc_service_prices` FOR EACH ROW BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `{!!prefix!!}srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND sp.id <> NEW.id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;



INSERT INTO `{!!prefix!!}srvc_service_prices` (`id`, `service_id`, `pricing_tier_id`, `pricing_model_id`, `currency`, `amount`, `unit`, `min_qty`, `max_qty`, `setup_fee`, `notes`, `effective_from`, `effective_to`, `version`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	1,	4,	'CAD',	45.00,	'user/month',	5.000,	25.000,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(2,	1,	2,	4,	'CAD',	42.00,	'user/month',	5.000,	25.000,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(3,	1,	3,	5,	'CAD',	480.00,	'user/year',	10.000,	50.000,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(4,	2,	1,	4,	'CAD',	65.00,	'user/month',	10.000,	100.000,	1500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(5,	2,	2,	4,	'CAD',	60.00,	'user/month',	10.000,	100.000,	1200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(6,	2,	3,	5,	'CAD',	650.00,	'user/year',	25.000,	200.000,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(7,	3,	2,	4,	'CAD',	85.00,	'user/month',	25.000,	NULL,	5000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(8,	3,	3,	5,	'CAD',	900.00,	'user/year',	50.000,	NULL,	10000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(9,	3,	4,	5,	'CAD',	850.00,	'user/year',	100.000,	NULL,	8000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(10,	4,	1,	2,	'CAD',	125.00,	'hour',	4.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(11,	4,	2,	2,	'CAD',	120.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(12,	4,	3,	2,	'CAD',	115.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(13,	5,	1,	3,	'CAD',	185.00,	'run',	1.000,	NULL,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(14,	5,	2,	3,	'CAD',	175.00,	'run',	10.000,	NULL,	200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(15,	5,	3,	3,	'CAD',	165.00,	'run',	25.000,	NULL,	150.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(16,	6,	1,	3,	'CAD',	235.00,	'run',	1.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(17,	6,	2,	3,	'CAD',	220.00,	'run',	10.000,	NULL,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(18,	6,	3,	3,	'CAD',	205.00,	'run',	25.000,	NULL,	200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(19,	7,	1,	3,	'CAD',	450.00,	'run',	1.000,	NULL,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(20,	7,	2,	3,	'CAD',	420.00,	'run',	5.000,	NULL,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(21,	7,	3,	3,	'CAD',	390.00,	'run',	10.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(22,	8,	1,	3,	'CAD',	850.00,	'camera',	4.000,	8.000,	800.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(23,	8,	2,	3,	'CAD',	800.00,	'camera',	4.000,	16.000,	600.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(24,	9,	1,	3,	'CAD',	1250.00,	'camera',	8.000,	32.000,	1500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(25,	9,	2,	3,	'CAD',	1150.00,	'camera',	8.000,	64.000,	1200.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(26,	9,	3,	3,	'CAD',	1050.00,	'camera',	16.000,	NULL,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(27,	10,	2,	1,	'CAD',	75000.00,	'system',	1.000,	NULL,	5000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(28,	10,	3,	1,	'CAD',	70000.00,	'system',	1.000,	NULL,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(29,	11,	1,	3,	'CAD',	1200.00,	'door',	1.000,	1.000,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(30,	11,	2,	3,	'CAD',	1100.00,	'door',	1.000,	1.000,	250.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(31,	12,	1,	3,	'CAD',	1800.00,	'door',	2.000,	16.000,	1000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(32,	12,	2,	3,	'CAD',	1650.00,	'door',	2.000,	32.000,	800.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(33,	12,	3,	3,	'CAD',	1500.00,	'door',	4.000,	NULL,	600.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(34,	13,	2,	1,	'CAD',	25000.00,	'system',	1.000,	NULL,	3000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(35,	13,	3,	1,	'CAD',	22000.00,	'system',	1.000,	NULL,	2000.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(36,	14,	1,	2,	'CAD',	95.00,	'hour',	1.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(37,	14,	2,	2,	'CAD',	90.00,	'hour',	1.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(38,	15,	1,	4,	'CAD',	250.00,	'user/month',	10.000,	NULL,	500.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(39,	15,	2,	4,	'CAD',	225.00,	'user/month',	25.000,	NULL,	400.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(40,	15,	3,	4,	'CAD',	200.00,	'user/month',	50.000,	NULL,	300.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(41,	16,	1,	2,	'CAD',	175.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(42,	16,	2,	2,	'CAD',	165.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(43,	16,	3,	2,	'CAD',	155.00,	'hour',	24.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(44,	17,	1,	2,	'CAD',	185.00,	'hour',	8.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(45,	17,	2,	2,	'CAD',	175.00,	'hour',	16.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL),
(46,	17,	3,	2,	'CAD',	165.00,	'hour',	24.000,	NULL,	0.00,	NULL,	'2024-01-01',	NULL,	1,	'2025-08-25 23:57:07',	'2025-08-25 23:57:07',	NULL,	NULL);

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_service_price_overlap_check`;
DROP TRIGGER IF EXISTS `tr_service_price_overlap_check_update`;
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_prices`;
