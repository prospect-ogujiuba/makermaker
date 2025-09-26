-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_pricing_tiers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT 0,
  `discount_pct` decimal(5,2) NOT NULL DEFAULT 0.00,
  `min_volume` int unsigned DEFAULT NULL,
  `max_volume` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_tier__name` (`name`),
  UNIQUE KEY `uq_pricing_tier__code` (`code`),
  KEY `idx_pricing_tier__sort_order` (`sort_order`),
  KEY `idx_pricing_tier__deleted_at` (`deleted_at`),
  KEY `idx_pricing_tier__created_by` (`created_by`),
  KEY `idx_pricing_tier__updated_by` (`updated_by`),
  CONSTRAINT `chk_pricing_tier__valid_discount` CHECK (`discount_pct` >= 0 AND `discount_pct` <= 100.00),
  CONSTRAINT `chk_pricing_tier__volume_range` CHECK (`min_volume` IS NULL OR `max_volume` IS NULL OR `min_volume` <= `max_volume`),
  CONSTRAINT `fk_pricing_tier__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pricing_tier__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pricing tiers with volume ranges and default discounts';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_tiers`;