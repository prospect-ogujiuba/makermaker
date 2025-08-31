-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_relationships`;
CREATE TABLE `{!!prefix!!}srvc_service_relationships` (
  `service_id` bigint(20) NOT NULL,
  `related_service_id` bigint(20) NOT NULL,
  `relation_type` enum('prerequisite','dependency','incompatible_with','substitute_for') NOT NULL,
  `notes` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`related_service_id`,`relation_type`),
  KEY `idx_service_relation__related_service_id` (`related_service_id`),
  KEY `idx_service_relation__relation_type` (`relation_type`),
  CONSTRAINT `fk_service_relation__related_service` FOREIGN KEY (`related_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_relation__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Service relationships: prerequisites, dependencies, incompatibilities, substitutions';

DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref`;
CREATE TRIGGER `tr_service_relation_no_self_ref` BEFORE INSERT ON `{!!prefix!!}srvc_service_relationships` FOR EACH ROW BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;

DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref_update`;
CREATE TRIGGER `tr_service_relation_no_self_ref_update` BEFORE UPDATE ON `{!!prefix!!}srvc_service_relationships` FOR EACH ROW BEGIN IF NEW.service_id = NEW.related_service_id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Service cannot have a relation to itself'; END IF; END;

INSERT INTO `{!!prefix!!}srvc_service_relationships` (`service_id`, `related_service_id`, `relation_type`, `notes`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	2,	'substitute_for',	'Professional VoIP can substitute for Basic VoIP with additional features',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(1,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(1,	5,	'prerequisite',	'Cat6 cabling required for VoIP phone connectivity',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(2,	3,	'substitute_for',	'Enterprise VoIP can substitute for Professional VoIP in large deployments',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(2,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(2,	6,	'prerequisite',	'Cat6A cabling recommended for professional VoIP systems',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(2,	15,	'dependency',	'Professional VoIP systems typically require premium support',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(3,	4,	'prerequisite',	'VoIP installation required for system deployment',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(3,	7,	'prerequisite',	'Fiber backbone required for enterprise VoIP redundancy',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(3,	15,	'prerequisite',	'Enterprise VoIP requires premium support contract',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(5,	6,	'incompatible_with',	'Cat6 and Cat6A cabling are alternative solutions',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(6,	7,	'incompatible_with',	'Cat6A and Fiber are typically alternative backbone solutions',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(8,	9,	'substitute_for',	'Professional cameras can substitute for Basic cameras',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(9,	6,	'prerequisite',	'Cat6A cabling required for professional camera systems',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(9,	10,	'substitute_for',	'Enterprise cameras can substitute for Professional cameras',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(10,	7,	'prerequisite',	'Fiber backbone required for enterprise camera systems',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(10,	15,	'prerequisite',	'Enterprise camera systems require premium support',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(10,	17,	'prerequisite',	'Security audit recommended for enterprise camera systems',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(11,	12,	'substitute_for',	'Multi-door access can substitute for Basic access control',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(12,	13,	'substitute_for',	'Enterprise access can substitute for Multi-door access control',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(13,	15,	'prerequisite',	'Enterprise access control requires premium support',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(13,	17,	'prerequisite',	'Security audit required before enterprise access control implementation',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL),
(14,	15,	'incompatible_with',	'Basic and Premium support are mutually exclusive',	'2025-08-25 23:57:12',	'2025-08-25 23:57:12',	NULL,	NULL);

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref`;
DROP TRIGGER IF EXISTS `tr_service_relation_no_self_ref_update`;
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_relationships`;
