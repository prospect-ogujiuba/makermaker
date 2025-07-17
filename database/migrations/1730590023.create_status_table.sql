-- SET SQL_MODE = 'STRICT_ALL_ENGINES';

-- Description: Create statuses table
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}makermaker_statuses` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(20) NOT NULL,
    `description` varchar(100) NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET={!!charset!!} COLLATE={!!collate!!};

INSERT IGNORE INTO `{!!prefix!!}makermaker_statuses` (`code`, `description`) VALUES
(1, 'Applied'),
(2, 'Attended Info Session'),
(3, 'Info Session No Show'),
(4, 'On Training'),
(10, 'Applicant Is Hired'),
(95, 'Failed Course'),
(96, 'Failed Course Interview'),
(97, 'Applicant Is Visitor'),
(98, 'Applicant Not Interested'),
(99, 'Applicant Not Ontario Resident');

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}makermaker_statuses`;