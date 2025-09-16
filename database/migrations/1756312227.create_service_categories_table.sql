-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT -1,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category__slug` (`slug`),
  KEY `idx_category__parent_id` (`parent_id`),
  KEY `idx_category__deleted_at` (`deleted_at`),
  KEY `idx_category__created_by` (`created_by`),
  KEY `idx_category__updated_by` (`updated_by`),
  CONSTRAINT `fk_category__parent` FOREIGN KEY (`parent_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_category__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_category__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hierarchical service categorization';

DROP TRIGGER IF EXISTS `tr_srvc_categories_no_self`;
CREATE TRIGGER `tr_srvc_categories_no_self` BEFORE INSERT ON `{!!prefix!!}srvc_categories` FOR EACH ROW BEGIN IF NEW.parent_id IS NOT NULL AND NEW.parent_id = NEW.id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'parent_id cannot equal id'; END IF; END;

DROP TRIGGER IF EXISTS `tr_srvc_categories_no_self_update`;
CREATE TRIGGER `tr_srvc_categories_no_self_update` BEFORE UPDATE ON `{!!prefix!!}srvc_categories` FOR EACH ROW BEGIN IF NEW.parent_id IS NOT NULL AND NEW.parent_id = NEW.id THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'parent_id cannot equal id'; END IF; END;

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_srvc_categories_no_self`;
DROP TRIGGER IF EXISTS `tr_srvc_categories_no_self_update`;
DROP TABLE IF EXISTS `{!!prefix!!}srvc_categories`;