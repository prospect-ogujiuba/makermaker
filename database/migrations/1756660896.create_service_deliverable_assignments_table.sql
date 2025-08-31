-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_deliverable_assignments`;
CREATE TABLE `{!!prefix!!}srvc_service_deliverable_assignments` (
  `service_id` bigint(20) NOT NULL,
  `deliverable_id` bigint(20) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`deliverable_id`),
  KEY `idx_service_deliverable__deliverable_id` (`deliverable_id`),
  KEY `idx_service_deliverable__sort_order` (`sort_order`),
  CONSTRAINT `fk_service_deliverable__deliverable` FOREIGN KEY (`deliverable_id`) REFERENCES `{!!prefix!!}srvc_deliverables` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_deliverable__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Deliverables associated with services';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_deliverable_assignments`;
