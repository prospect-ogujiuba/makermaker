-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_bundles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `slug` varchar(64) NOT NULL,
  `short_desc` varchar(512) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_bundle__slug` (`slug`),
  KEY `idx_bundle__is_active` (`is_active`),
  KEY `idx_bundle__deleted_at` (`deleted_at`),
  KEY `idx_bundle__created_by` (`created_by`),
  KEY `idx_bundle__updated_by` (`updated_by`),
  CONSTRAINT `fk_bundle__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_bundle__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service bundles for grouped offerings';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_bundles`;
