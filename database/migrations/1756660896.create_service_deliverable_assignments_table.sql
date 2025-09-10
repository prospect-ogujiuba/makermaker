-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_deliverable_assignments` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,  
  `service_id` bigint(20) NOT NULL,
  `deliverable_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_service_deliverable_assignment` (`service_id`,`deliverable_id`),
  KEY `idx_service_deliverable_assignment__deliverable_id` (`deliverable_id`),
  KEY `idx_service_deliverable_assignment__deleted_at` (`deleted_at`),
  KEY `idx_service_deliverable_assignment__created_by` (`created_by`),
  KEY `idx_service_deliverable_assignment__updated_by` (`updated_by`),
  CONSTRAINT `fk_service_deliverable_assignment__deliverable` FOREIGN KEY (`deliverable_id`) REFERENCES `{!!prefix!!}srvc_deliverables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable_assignment__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Deliverables associated with services';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_deliverable_assignments`;
