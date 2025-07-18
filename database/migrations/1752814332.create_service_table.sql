-- SET SQL_MODE = 'STRICT_ALL_ENGINES';

-- Description: Create IT services table for B2BCNC web application
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}it_services` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(50) NOT NULL,
    `name` varchar(100) NOT NULL,
    `description` text,
    `category` varchar(50) NOT NULL,
    `base_price` decimal(10,2) DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `requires_quote` tinyint(1) NOT NULL DEFAULT 1,
    `allows_file_upload` tinyint(1) NOT NULL DEFAULT 1,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_category` (`category`),
    KEY `idx_active` (`is_active`),
    KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET={!!charset!!} COLLATE={!!collate!!};

INSERT IGNORE INTO `{!!prefix!!}it_services` (`code`, `name`, `description`, `category`, `base_price`, `requires_quote`, `allows_file_upload`) VALUES
('voip_hosting', 'VoIP Phone System - Hosting', 'Cloud-based VoIP phone system hosting and management', 'telecommunications', NULL, 1, 1),
('voip_installation', 'VoIP Phone System - Installation', 'On-site VoIP phone system installation and setup', 'telecommunications', NULL, 1, 1),
('voip_maintenance', 'VoIP Phone System - Maintenance', 'Ongoing VoIP system maintenance and support', 'telecommunications', NULL, 1, 0),
('camera_system', 'Camera System', 'Security camera system installation and configuration', 'security', NULL, 1, 1),
('card_access', 'Card Access System', 'Access control system installation and management', 'security', NULL, 1, 1),
('network_cabling', 'Network Cabling', 'Structured network cabling installation and testing', 'networking', NULL, 1, 1),
('managed_services', 'Managed IT Services', 'Ongoing IT infrastructure management and support', 'support', NULL, 1, 1),
('it_consulting', 'IT Consulting', 'General IT consulting and advisory services', 'consulting', NULL, 1, 1),
('network_setup', 'Network Setup', 'Network infrastructure design and implementation', 'networking', NULL, 1, 1),
('server_setup', 'Server Setup', 'Server installation and configuration', 'infrastructure', NULL, 1, 1),
('backup_solutions', 'Backup Solutions', 'Data backup and recovery system implementation', 'infrastructure', NULL, 1, 0),
('other_it', 'Other IT Services', 'Custom IT services not listed above', 'other', NULL, 1, 1);

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}it_services`;