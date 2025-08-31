-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_categories`;
CREATE TABLE `{!!prefix!!}srvc_categories` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `slug` varchar(128) NOT NULL,
  `path` varchar(512) DEFAULT NULL COMMENT 'Materialized path for fast tree queries (e.g., /root/parent/child)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category__slug` (`slug`),
  KEY `idx_category__parent_id` (`parent_id`),
  KEY `idx_category__path` (`path`),
  KEY `idx_category__deleted_at` (`deleted_at`),
  CONSTRAINT `fk_category__parent` FOREIGN KEY (`parent_id`) REFERENCES `{!!prefix!!}srvc_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hierarchical service categorization';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_categories`;