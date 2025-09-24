-- Description: Service Attribute Definitions
-- >>> Up >>>
CREATE TABLE
  `{!!prefix!!}srvc_attribute_definitions` (
    `id` bigint (20) NOT NULL AUTO_INCREMENT,
    `service_type_id` bigint (20) NOT NULL,
    `code` varchar(64) NOT NULL,
    `label` varchar(64) NOT NULL,
    `data_type` enum (
      'int',
      'decimal',
      'bool',
      'text',
      'enum',
      'date',
      'datetime',
      'json',
      'url',
      'email'
    ) NOT NULL,
    `enum_options` longtext CHARACTER
    SET
      utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Valid options for enum data_type as JSON array' CHECK (json_valid (`enum_options`)),
      `unit` varchar(32) DEFAULT NULL COMMENT 'Unit of measurement: users, sites, ft, etc.',
      `required` tinyint (1) NOT NULL DEFAULT 0,
      `created_at` datetime NOT NULL DEFAULT current_timestamp(),
      `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `deleted_at` datetime DEFAULT NULL,
      `created_by` bigint (20) unsigned NOT NULL COMMENT,
      `updated_by` bigint (20) unsigned NOT NULL COMMENT,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_attribute_definition__service_type_code` (`service_type_id`, `code`),
      KEY `idx_attribute_definition__service_type_id` (`service_type_id`),
      KEY `idx_attribute_definition__code` (`code`),
      KEY `idx_attribute_definition__data_type` (`data_type`),
      KEY `idx_attribute_definition__deleted_at` (`deleted_at`),
      KEY `idx_attribute_definition__created_by` (`created_by`),
      KEY `idx_attribute_definition__updated_by` (`updated_by`),
      CONSTRAINT `fk_attribute_definition__service_type` FOREIGN KEY (`service_type_id`) REFERENCES `{!!prefix!!}srvc_service_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `fk_attribute_definition__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
      CONSTRAINT `fk_attribute_definition__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = 'Parametric attribute definitions per service type';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_attribute_definitions`;