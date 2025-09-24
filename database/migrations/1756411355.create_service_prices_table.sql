-- Description:
-- >>> Up >>>
CREATE TABLE
  `{!!prefix!!}srvc_service_prices` (
    `id` bigint (20) NOT NULL AUTO_INCREMENT,
    `service_id` bigint (20) NOT NULL,
    `pricing_tier_id` bigint (20) NOT NULL,
    `pricing_model_id` bigint (20) NOT NULL,
    `currency` char(3) NOT NULL DEFAULT 'CAD',
    `amount` decimal(12, 2) DEFAULT NULL,
    `unit` varchar(32) DEFAULT NULL COMMENT 'Pricing unit: hour, user, device, site, month',
    `setup_fee` decimal(12, 2) NOT NULL DEFAULT 0.00,
    `notes` longtext CHARACTER
    SET
      utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Informational service price configuration and properties' CHECK (json_valid (`notes`)),
      `created_at` datetime NOT NULL DEFAULT current_timestamp(),
      `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `deleted_at` datetime DEFAULT NULL,
      `created_by` bigint (20) unsigned NOT NULL COMMENT,
      `updated_by` bigint (20) unsigned NOT NULL COMMENT,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_service_price__pricing_tier_model` (
        `service_id`,
        `pricing_tier_id`,
        `pricing_model_id`
      ),
      KEY `idx_service_price__service_id` (`service_id`),
      KEY `idx_service_price__pricing_tier_id` (`pricing_tier_id`),
      KEY `idx_service_price__pricing_model_id` (`pricing_model_id`),
      KEY `idx_service_price__currency` (`currency`),
      KEY `idx_service_price__deleted_at` (`deleted_at`),
      KEY `idx_service_price__created_by` (`created_by`),
      KEY `idx_service_price__updated_by` (`updated_by`),
      CONSTRAINT `fk_service_price__pricing_model` FOREIGN KEY (`pricing_model_id`) REFERENCES `{!!prefix!!}srvc_pricing_models` (`id`) ON UPDATE CASCADE,
      CONSTRAINT `fk_service_price__pricing_tier` FOREIGN KEY (`pricing_tier_id`) REFERENCES `{!!prefix!!}srvc_pricing_tiers` (`id`) ON UPDATE CASCADE,
      CONSTRAINT `fk_service_price__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_service_price__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
      CONSTRAINT `fk_service_price__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = 'Service pricing';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_prices`;