-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_types` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_site_visit` tinyint(1) NOT NULL DEFAULT 0,
  `supports_remote` tinyint(1) NOT NULL DEFAULT 0,
  `estimated_duration_hours` decimal(6,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_type__name` (`name`),
  UNIQUE KEY `uq_service_type__code` (`code`),
  KEY `idx_service_type__requires_site_visit` (`requires_site_visit`),
  KEY `idx_service_type__supports_remote` (`supports_remote`),
  KEY `idx_service_type__deleted_at` (`deleted_at`),
  KEY `idx_service_type__created_by` (`created_by`),
  KEY `idx_service_type__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_type__positive_duration` CHECK (`estimated_duration_hours` IS NULL OR `estimated_duration_hours` >= 0),
  CONSTRAINT `fk_service_type__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_type__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service types with delivery characteristics';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_types`;