-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_complexities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `level` tinyint unsigned NOT NULL DEFAULT 0,
  `price_multiplier` decimal(8,2) NOT NULL DEFAULT 1.00,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_complexity__name` (`name`),
  UNIQUE KEY `uq_complexity__level` (`level`),
  KEY `idx_complexity__level` (`level`),
  KEY `idx_complexity__deleted_at` (`deleted_at`),
  KEY `idx_complexity__created_by` (`created_by`),
  KEY `idx_complexity__updated_by` (`updated_by`),
  CONSTRAINT `fk_complexity__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_complexity__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service complexity levels with associated pricing multipliers';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_complexities`;
