-- Description:
-- >>> Up >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_attribute_values`;
CREATE TABLE `{!!prefix!!}srvc_service_attribute_values` (
  `service_id` bigint(20) NOT NULL,
  `attribute_definition_id` bigint(20) NOT NULL,
  `int_val` bigint(20) DEFAULT NULL,
  `decimal_val` decimal(18,6) DEFAULT NULL,
  `bool_val` tinyint(1) DEFAULT NULL,
  `text_val` text DEFAULT NULL,
  `enum_val` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  `updated_by` bigint(20) DEFAULT NULL COMMENT 'Future FK to user table',
  PRIMARY KEY (`service_id`,`attribute_definition_id`),
  KEY `idx_service_attribute_value__attribute_definition_id` (`attribute_definition_id`),
  CONSTRAINT `fk_service_attribute_value__attribute_definition` FOREIGN KEY (`attribute_definition_id`) REFERENCES `{!!prefix!!}srvc_attribute_definitions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_service_attribute_value__service` FOREIGN KEY (`service_id`) REFERENCES `{!!prefix!!}srvc_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Attribute values for services based on their type definitions';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_service_attribute_values`;
