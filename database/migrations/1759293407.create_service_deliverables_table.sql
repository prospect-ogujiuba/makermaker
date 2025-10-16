-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_service_deliverables` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,  
  `service_id` bigint(20) NOT NULL,
  `deliverable_id` bigint(20) NOT NULL,
  `is_optional` tinyint(1) NOT NULL DEFAULT 0,
  `sequence_order` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_deliverable` (`service_id`,`deliverable_id`),
  KEY `idx_service_deliverable__deliverable_id` (`deliverable_id`),
  KEY `idx_service_deliverable__is_optional` (`is_optional`),
  KEY `idx_service_deliverable__sequence_order` (`sequence_order`),
  KEY `idx_service_deliverable__deleted_at` (`deleted_at`),
  KEY `idx_service_deliverable__created_by` (`created_by`),
  KEY `idx_service_deliverable__updated_by` (`updated_by`),
  CONSTRAINT `fk_service_deliverable__deliverable` FOREIGN KEY (`deliverable_id`) REFERENCES `{!!prefix!!}srvc_deliverables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service deliverable with sequencing';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_deliverables`;