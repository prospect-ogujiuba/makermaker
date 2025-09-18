-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_pricing_tiers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pricing_tier__name` (`name`),
  UNIQUE KEY `uq_pricing_tier__code` (`code`),
  KEY `idx_pricing_tier__sort_order` (`sort_order`),
  KEY `idx_pricing_tier__deleted_at` (`deleted_at`),
  KEY `idx_pricing_tier__created_by` (`created_by`),
  KEY `idx_pricing_tier__updated_by` (`updated_by`),
  CONSTRAINT `fk_pricing_tier__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_pricing_tier__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pricing tiers: Retail, Partner, Enterprise, etc.';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_pricing_tiers`;
