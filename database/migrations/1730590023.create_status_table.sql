-- SET SQL_MODE = 'STRICT_ALL_ENGINES';

-- Description: Create universal statuses table
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}b2bcnc_statuses` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `code` varchar(30) NOT NULL,
    `description` varchar(100) NOT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET={!!charset!!} COLLATE={!!collate!!};

-- Seed with reusable statuses
INSERT IGNORE INTO `{!!prefix!!}b2bcnc_statuses` (`code`, `description`) VALUES
-- General Workflow
('NEW', 'New'),
('IN_PROGRESS', 'In Progress'),
('ON_HOLD', 'On Hold'),
('PENDING_CLIENT', 'Pending Client Response'),
('PENDING_INTERNAL', 'Pending Internal Action'),
('COMPLETED', 'Completed'),
('CLOSED', 'Closed'),
('CANCELLED', 'Cancelled'),
('REOPENED', 'Reopened'),

-- Ticket Specific
('ASSIGNED', 'Assigned to Technician'),
('ESCALATED', 'Escalated to Tier 2'),
('AWAITING_PARTS', 'Awaiting Parts'),

-- Project/Job Management
('SCHEDULED', 'Scheduled'),
('DEPLOYED', 'Deployed'),
('READY_FOR_QA', 'Ready for QA'),
('FAILED_QA', 'Failed QA'),
('APPROVED', 'Approved for Release'),

-- Client/Account Lifecycle
('LEAD', 'Lead'),
('PROSPECT', 'Prospect'),
('ACTIVE_CLIENT', 'Active Client'),
('INACTIVE_CLIENT', 'Inactive Client'),
('CHURNED', 'Churned'),

-- Procurement/Asset Handling
('REQUESTED', 'Requested'),
('ORDERED', 'Ordered'),
('RECEIVED', 'Received'),
('STOCKED', 'In Stock'),
('ALLOCATED', 'Allocated to Client/Job'),
('RETIRED', 'Retired Equipment');

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}b2bcnc_statuses`;
