-- Description:
-- >>> Up >>>
CREATE OR REPLACE TABLE `{!!prefix!!}b2bcnc_services` (
	`id` INT NOT NULL AUTO_INCREMENT UNIQUE,
	`code` VARCHAR(255) NOT NULL UNIQUE, -- Added UNIQUE constraint
	`name` VARCHAR(100) NOT NULL,
	`description` TEXT NOT NULL,
	`base_price` DECIMAL(10,2) NOT NULL, -- Changed from INT to DECIMAL for currency
	`icon` VARCHAR(100) NOT NULL,
	`active` BOOLEAN NULL DEFAULT TRUE, -- Added default value
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, -- Auto-update timestamp
	`deleted_at` TIMESTAMP NULL, -- Explicitly allow NULL for soft deletes
	PRIMARY KEY(`id`)
);

-- Indexes for optimal performance
CREATE INDEX `idx_services_code` ON `{!!prefix!!}b2bcnc_services` (`code`);
CREATE INDEX `idx_services_active` ON `{!!prefix!!}b2bcnc_services` (`active`);
CREATE INDEX `idx_services_deleted_at` ON `{!!prefix!!}b2bcnc_services` (`deleted_at`);
CREATE INDEX `idx_services_active_deleted` ON `{!!prefix!!}b2bcnc_services` (`active`, `deleted_at`);


-- >>> Down >>>
DROP TABLE IF EXISTS {!!prefix!!}b2bcnc_services;