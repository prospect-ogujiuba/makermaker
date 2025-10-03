-- Description:
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}srvc_service_relationships` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `related_service_id` bigint(20) NOT NULL,
  `relation_type` enum('prerequisite','dependency','incompatible_with','substitute_for','complements','replaces','requires','enables','conflicts_with') NOT NULL,
  `strength` tinyint(2) unsigned NOT NULL DEFAULT 5 COMMENT 'Relationship strength: 1=weak, 10=critical',
  `notes` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_service_relationship` (`service_id`,`related_service_id`,`relation_type`),
  KEY `idx_service_relationship__related_service_id` (`related_service_id`),
  KEY `idx_service_relationship__relation_type` (`relation_type`),
  KEY `idx_service_relationship__strength` (`strength`),
  KEY `idx_service_relationship__deleted_at` (`deleted_at`),
  KEY `idx_service_relationship__created_by` (`created_by`),
  KEY `idx_service_relationship__updated_by` (`updated_by`),
  CONSTRAINT `chk_service_relationship__strength_range` CHECK (`strength` >= 1 AND `strength` <= 10),
  CONSTRAINT `fk_service_relationship__related_service` FOREIGN KEY (`related_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relationship__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Service relationships with strength indicators and CHECK constraint for self-reference';

DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref`;
CREATE TRIGGER `tr_service_relation_no_self_ref` BEFORE INSERT ON `{!!prefix!!}srvc_service_relationships` FOR EACH ROW BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;

DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref_update`;
CREATE TRIGGER `tr_service_relation_no_self_ref_update` BEFORE UPDATE ON `{!!prefix!!}srvc_service_relationships` FOR EACH ROW BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_relationships`;