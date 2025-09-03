-- Description:
-- >>> Up >>>
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service pricing with effective date ranges';

DROP TRIGGER IF EXISTS `tr_service_price_overlap_check`;
CREATE TRIGGER `tr_service_price_overlap_check` BEFORE INSERT ON `{!!prefix!!}srvc_service_prices` FOR EACH ROW BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `{!!prefix!!}srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;

DROP TRIGGER IF EXISTS `tr_service_price_overlap_check_update`;
CREATE TRIGGER `tr_service_price_overlap_check_update` BEFORE UPDATE ON `{!!prefix!!}srvc_service_prices` FOR EACH ROW BEGIN DECLARE overlap_count INT DEFAULT 0; SELECT COUNT(*) INTO overlap_count FROM `{!!prefix!!}srvc_service_prices` sp WHERE sp.service_id = NEW.service_id AND sp.pricing_tier_id = NEW.pricing_tier_id AND sp.pricing_model_id = NEW.pricing_model_id AND sp.id <> NEW.id AND ( (NEW.effective_from BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (COALESCE(NEW.effective_to, '9999-12-31') BETWEEN sp.effective_from AND COALESCE(sp.effective_to, '9999-12-31')) OR (sp.effective_from BETWEEN NEW.effective_from AND COALESCE(NEW.effective_to, '9999-12-31')) ); IF overlap_count > 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Overlapping service price ranges not allowed for same service/tier/model combination'; END IF; END;

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_service_price_overlap_check`;
DROP TRIGGER IF EXISTS `tr_service_price_overlap_check_update`;
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_prices`;
