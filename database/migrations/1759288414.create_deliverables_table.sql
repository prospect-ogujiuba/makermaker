-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_deliverables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `deliverable_type` enum('document','software','hardware','service','training','report') NOT NULL DEFAULT 'document',
  `template_path` varchar(255) DEFAULT NULL,
  `estimated_effort_hours` decimal(6,2) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_deliverable__name` (`name`),
  KEY `idx_deliverable__deliverable_type` (`deliverable_type`),
  KEY `idx_deliverable__requires_approval` (`requires_approval`),
  KEY `idx_deliverable__deleted_at` (`deleted_at`),
  KEY `idx_deliverable__created_at` (`created_at`),
  KEY `idx_deliverable__updated_at` (`updated_at`),
  CONSTRAINT `chk_deliverable__positive_effort` CHECK (`estimated_effort_hours` IS NULL OR `estimated_effort_hours` > 0),
  CONSTRAINT `fk_deliverable__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_deliverable__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service deliverables with type classification and effort tracking';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_deliverables`;