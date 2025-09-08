-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_service_attribute_values` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `service_id` bigint(20) NOT NULL,
  `attribute_definition_id` bigint(20) NOT NULL,
  `int_val` bigint(20) DEFAULT NULL,
  `decimal_val` decimal(18,6) DEFAULT NULL,
  `bool_val` tinyint(1) DEFAULT NULL,
  `text_val` text DEFAULT NULL,
  `enum_val` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL COMMENT 'Fk to user table',
  `updated_by` bigint(20) unsigned NOT NULL COMMENT 'Fk to user table',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_service_attribute_value` (`service_id`,`attribute_definition_id`),
  KEY `idx_service_attribute_value__service_id` (`service_id`),
  KEY `idx_service_attribute_value__attribute_definition_id` (`attribute_definition_id`),
  KEY `idx_service_attribute_value__deleted_at` (`deleted_at`),
  KEY `idx_service_attribute_value__created_by` (`created_by`),
  KEY `idx_service_attribute_value__updated_by` (`updated_by`),
  CONSTRAINT `fk_service_attribute_value__attribute_definition` FOREIGN KEY (`attribute_definition_id`) REFERENCES `{!!prefix!!}srvc_attribute_definitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_attribute_value__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_attribute_value__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_service_attribute_value__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Attribute values for services based on their type definitions';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_attribute_values`;