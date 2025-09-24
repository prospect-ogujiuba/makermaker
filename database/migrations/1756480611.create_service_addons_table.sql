-- Description:
-- >>> Up >>>
CREATE TABLE
  `{!!prefix!!}srvc_service_addons` (
    `id` bigint (20) NOT NULL AUTO_INCREMENT,
    `service_id` bigint (20) NOT NULL,
    `addon_service_id` bigint (20) NOT NULL,
    `required` tinyint (1) NOT NULL DEFAULT 0,
    `min_qty` decimal(12, 3) NOT NULL DEFAULT 0.000,
    `max_qty` decimal(12, 3) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `deleted_at` datetime DEFAULT NULL,
    `created_by` bigint (20) unsigned NOT NULL COMMENT,
    `updated_by` bigint (20) unsigned NOT NULL COMMENT,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_service_addon` (`service_id`, `addon_service_id`),
    KEY `idx_service_id` (`service_id`),
    KEY `idx_addon_service_id` (`addon_service_id`),
    KEY `idx_addon_service__deleted_at` (`deleted_at`),
    KEY `idx_addon_service__created_by` (`created_by`),
    KEY `idx_addon_service__updated_by` (`updated_by`),
    CONSTRAINT `fk_service_addon__addon_service` FOREIGN KEY (`addon_service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_service_addon__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_service_addon__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
    CONSTRAINT `fk_service_addon__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = 'Service add-on relationships with pricing overrides';

DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref`;

CREATE TRIGGER `tr_service_addon_no_self_ref` BEFORE INSERT ON `{!!prefix!!}srvc_service_addons` FOR EACH ROW BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000'
SET
  MESSAGE_TEXT = 'Service cannot be an addon to itself';

END IF;

END;

DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref_update`;

CREATE TRIGGER `tr_service_addon_no_self_ref_update` BEFORE
UPDATE ON `{!!prefix!!}srvc_service_addons` FOR EACH ROW BEGIN IF NEW.service_id = NEW.addon_service_id THEN SIGNAL SQLSTATE '45000'
SET
  MESSAGE_TEXT = 'Service cannot be an addon to itself';

END IF;

END;

-- >>> Down >>>
DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref`;

DROP TRIGGER IF EXISTS `tr_service_addon_no_self_ref_update`;

DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_addons`;