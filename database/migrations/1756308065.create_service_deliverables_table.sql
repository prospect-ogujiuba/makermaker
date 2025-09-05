-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_deliverables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  KEY `idx_deliverable__name` (`name`),
  KEY `idx_deliverable__deleted_at` (`deleted_at`),
  KEY `idx_deliverable__created_at` (`created_at`),
  KEY `idx_deliverable__updated_at` (`updated_at`),
  CONSTRAINT `fk_deliverable__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_deliverable__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Deliverable items that can be associated with services';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_deliverables`;
