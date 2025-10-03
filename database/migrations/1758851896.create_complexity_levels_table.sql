-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_complexity_levels` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `level` tinyint unsigned NOT NULL DEFAULT 0,
  `price_multiplier` decimal(8,2) NOT NULL DEFAULT 1.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL DEFAULT 1,
  `updated_by` bigint(20) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_complexity__name` (`name`),
  UNIQUE KEY `uq_complexity__level` (`level`),
  KEY `idx_complexity__level` (`level`),
  KEY `idx_complexity__deleted_at` (`deleted_at`),
  KEY `idx_complexity__created_by` (`created_by`),
  KEY `idx_complexity__updated_by` (`updated_by`),
  CONSTRAINT `chk_complexity__positive_multiplier` CHECK (`price_multiplier` >= 0 AND `price_multiplier` <= 99),
  CONSTRAINT `chk_complexity__valid_level` CHECK (`level` >= 0 AND `level` <= 255),
  CONSTRAINT `fk_complexity__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_complexity__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service complexity levels with price multipliers';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_complexity_levels`;