-- Description:
-- >>> Up >>>
CREATE TABLE
  `{!!prefix!!}srvc_pricing_models` (
    `id` bigint (20) NOT NULL AUTO_INCREMENT,
    `name` varchar(64) NOT NULL,
    `code` varchar(64) NOT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` datetime DEFAULT NULL,
    `created_by` bigint (20) unsigned NOT NULL COMMENT,
    `updated_by` bigint (20) unsigned NOT NULL COMMENT,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pricing_model__name` (`name`),
    UNIQUE KEY `uq_pricing_model__code` (`code`),
    KEY `idx_pricing_model__deleted_at` (`deleted_at`),
    KEY `idx_pricing_model__created_by` (`created_by`),
    KEY `idx_pricing_model__updated_by` (`updated_by`),
    CONSTRAINT `fk_pricing_model__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
    CONSTRAINT `fk_pricing_model__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = 'Pricing models: fixed, hourly, per_unit, subscription, tiered, volume';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_models`;