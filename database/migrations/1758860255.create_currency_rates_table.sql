-- Description:
-- >>> Up >>>
CREATE TABLE `{!!prefix!!}srvc_currency_rates` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `from_currency` char(3) NOT NULL,
  `to_currency` char(3) NOT NULL,
  `exchange_rate` decimal(10,6) NOT NULL,
  `effective_date` date NOT NULL,
  `source` varchar(64) DEFAULT 'manual',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) unsigned NOT NULL,
  `updated_by` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_currency_rate__currencies_date` (`from_currency`, `to_currency`, `effective_date`),
  KEY `idx_currency_rate__from_currency` (`from_currency`),
  KEY `idx_currency_rate__to_currency` (`to_currency`),
  KEY `idx_currency_rate__effective_date` (`effective_date`),
  CONSTRAINT `chk_currency_rate__valid_from_currency` CHECK (`from_currency` REGEXP '^[A-Z]{3}$'),
  CONSTRAINT `chk_currency_rate__valid_to_currency` CHECK (`to_currency` REGEXP '^[A-Z]{3}$'),
  CONSTRAINT `chk_currency_rate__positive_rate` CHECK (`exchange_rate` > 0),
  CONSTRAINT `chk_currency_rate__no_same_currency` CHECK (`from_currency` != `to_currency`),
  CONSTRAINT `fk_currency_rate__created_by` FOREIGN KEY (`created_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_currency_rate__updated_by` FOREIGN KEY (`updated_by`) REFERENCES `{!!prefix!!}users` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Currency exchange rates with effective dating';

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}srvc_currency_rates`;