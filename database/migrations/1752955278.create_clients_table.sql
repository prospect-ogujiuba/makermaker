-- Description: Create clients table
-- >>> Up >>>
CREATE TABLE IF NOT EXISTS `{!!prefix!!}b2bcnc_clients` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `company_name` varchar(255) NOT NULL,
    `legal_name` varchar(255) DEFAULT NULL,
    `business_number` varchar(50) DEFAULT NULL,
    `contact_firstname` varchar(100) NOT NULL,
    `contact_lastname` varchar(100) NOT NULL,
    `contact_title` varchar(100) DEFAULT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `mobile` varchar(20) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `street` varchar(255) NOT NULL,
    `city` varchar(100) NOT NULL,
    `province` char(2) NOT NULL DEFAULT 'ON',
    `postal_code` varchar(7) NOT NULL,
    `country` varchar(3) NOT NULL DEFAULT 'CA',
    `industry` varchar(100) DEFAULT NULL,
    `company_size` enum('1-10', '11-50', '51-200', '201-500', '500+') DEFAULT NULL,
    `annual_revenue` enum('under-1m', '1m-5m', '5m-25m', '25m-100m', 'over-100m') DEFAULT NULL,
    `billing_street` varchar(255) DEFAULT NULL,
    `billing_city` varchar(100) DEFAULT NULL,
    `billing_province` enum(
        'ON',
        'BC',
        'AB',
        'SK',
        'MB',
        'QC',
        'NB',
        'NS',
        'PE',
        'NL',
        'YT',
        'NT',
        'NU'
    ) DEFAULT NULL,
    `billing_postal_code` varchar(7) DEFAULT NULL,
    `billing_country` varchar(3) DEFAULT 'CA',
    `tax_number` varchar(50) DEFAULT NULL,
    `payment_terms` enum('net-15', 'net-30', 'net-60', 'due-on-receipt') DEFAULT 'net-30',
    `credit_limit` decimal(10,2) DEFAULT NULL,
    `notes` text DEFAULT NULL,
    `status` enum('active', 'inactive', 'suspended', 'prospect') NOT NULL DEFAULT 'prospect',
    `priority` enum('low', 'normal', 'high', 'critical') NOT NULL DEFAULT 'normal',
    `source` enum('website', 'referral', 'cold-call', 'trade-show', 'social-media', 'other') DEFAULT NULL,
    `assigned_to` bigint(20) unsigned DEFAULT NULL COMMENT 'WordPress user ID of assigned account manager',
    `onboarded_at` timestamp NULL DEFAULT NULL,
    `last_contact_at` timestamp NULL DEFAULT NULL,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_email` (`email`),
    KEY `idx_company` (`company_name`),
    KEY `idx_status` (`status`),
    KEY `idx_location` (`province`, `city`),
    KEY `idx_assigned` (`assigned_to`),
    KEY `idx_deleted` (`deleted_at`),
    KEY `idx_contact_name` (`contact_lastname`, `contact_firstname`),
    CONSTRAINT `chk_phone` CHECK (phone REGEXP '^[0-9]{10}$')
) ENGINE=InnoDB DEFAULT CHARSET={!!charset!!} COLLATE={!!collate!!};

INSERT IGNORE INTO `{!!prefix!!}b2bcnc_clients` (
    `company_name`, `legal_name`, `business_number`, `contact_firstname`, `contact_lastname`, 
    `contact_title`, `email`, `phone`, `mobile`, `website`, `street`, `city`, `province`, 
    `postal_code`, `country`, `industry`, `company_size`, `annual_revenue`, `billing_street`, 
    `billing_city`, `billing_province`, `billing_postal_code`, `billing_country`, `tax_number`, 
    `payment_terms`, `credit_limit`, `notes`, `status`, `priority`, `source`, `assigned_to`, 
    `onboarded_at`, `last_contact_at`
) VALUES
-- Technology Companies
('TechFlow Solutions', 'TechFlow Solutions Inc.', '123456789RT0001', 'Sarah', 'Chen', 'CTO', 'sarah.chen@techflow.ca', '4161234567', '4169876543', 'https://techflow.ca', '100 King St W', 'Toronto', 'ON', 'M5X1A1', 'CA', 'Software Development', '51-200', '5m-25m', '100 King St W', 'Toronto', 'ON', 'M5X1A1', 'CA', '123456789RT0001', 'net-30', 50000.00, 'Enterprise software development company specializing in fintech solutions.', 'active', 'high', 'website', 1, '2024-03-15 10:30:00', '2024-07-10 14:20:00'),

('DataMine Analytics', 'DataMine Analytics Corp.', '234567890RT0001', 'Michael', 'Thompson', 'CEO', 'mike.thompson@datamine.ca', '6041234567', '6049876543', 'https://datamineanalytics.com', '1055 W Georgia St', 'Vancouver', 'BC', 'V6E3R5', 'CA', 'Data Analytics', '11-50', '1m-5m', '1055 W Georgia St', 'Vancouver', 'BC', 'V6E3R5', 'CA', '234567890RT0001', 'net-30', 25000.00, 'Specializes in big data analytics and machine learning solutions.', 'active', 'normal', 'referral', 2, '2024-02-20 09:15:00', '2024-07-08 11:30:00'),

('CloudBridge Tech', 'CloudBridge Technologies Ltd.', '345678901RT0001', 'Jennifer', 'Lee', 'VP Operations', 'jennifer.lee@cloudbridge.ca', '4031234567', '4039876543', 'https://cloudbridge.tech', '530 8th Ave SW', 'Calgary', 'AB', 'T2P3S8', 'CA', 'Cloud Services', '201-500', '25m-100m', '530 8th Ave SW', 'Calgary', 'AB', 'T2P3S8', 'CA', '345678901RT0001', 'net-15', 100000.00, 'Leading cloud infrastructure and migration services provider.', 'active', 'critical', 'trade-show', 3, '2024-01-10 13:45:00', '2024-07-15 16:00:00'),

-- Manufacturing Companies
('Precision Manufacturing', 'Precision Manufacturing Inc.', '456789012RT0001', 'Robert', 'Wilson', 'Plant Manager', 'robert.wilson@precisionmfg.ca', '9051234567', '9059876543', 'https://precisionmfg.ca', '2500 Argentia Rd', 'Mississauga', 'ON', 'L5N6C2', 'CA', 'Manufacturing', '51-200', '5m-25m', NULL, NULL, NULL, NULL, 'CA', '456789012RT0001', 'net-30', 75000.00, 'Automotive parts manufacturing with ISO 9001 certification.', 'active', 'normal', 'cold-call', 1, '2024-04-05 08:00:00', '2024-07-12 10:15:00'),

('Atlantic Steel Works', 'Atlantic Steel Works Limited', '567890123RT0001', 'Patricia', 'MacDonald', 'Operations Director', 'patricia.macdonald@atlanticsteel.ca', '9021234567', '9029876543', 'https://atlanticsteel.ca', '1234 Industrial Dr', 'Halifax', 'NS', 'B3K5M2', 'CA', 'Steel Manufacturing', '11-50', '1m-5m', '1234 Industrial Dr', 'Halifax', 'NS', 'B3K5M2', 'CA', '567890123RT0001', 'net-60', 40000.00, 'Custom steel fabrication for marine and construction industries.', 'active', 'normal', 'website', 2, '2024-03-22 11:20:00', '2024-07-05 15:30:00'),

('Prairie Grain Processing', 'Prairie Grain Processing Corp.', '678901234RT0001', 'David', 'Anderson', 'General Manager', 'david.anderson@prairiegrain.ca', '3061234567', '3069876543', 'https://prairiegrain.ca', '789 Railway Ave', 'Saskatoon', 'SK', 'S7K1J8', 'CA', 'Food Processing', '201-500', '25m-100m', '789 Railway Ave', 'Saskatoon', 'SK', 'S7K1J8', 'CA', '678901234RT0001', 'net-30', 150000.00, 'Large-scale grain processing and agricultural products.', 'active', 'high', 'referral', 3, '2024-02-14 07:30:00', '2024-07-18 09:45:00'),

-- Healthcare & Professional Services
('MedTech Solutions', 'MedTech Solutions Inc.', '789012345RT0001', 'Dr. Lisa', 'Patel', 'Medical Director', 'lisa.patel@medtechsol.ca', '5141234567', '5149876543', 'https://medtechsolutions.ca', '1250 René-Lévesque Blvd', 'Montreal', 'QC', 'H3B4W8', 'CA', 'Healthcare Technology', '11-50', '1m-5m', '1250 René-Lévesque Blvd', 'Montreal', 'QC', 'H3B4W8', 'CA', '789012345RT0001', 'net-30', 30000.00, 'Medical device software and hospital management systems.', 'active', 'high', 'website', 1, '2024-05-08 12:00:00', '2024-07-16 14:30:00'),

('Maritime Legal Group', 'Maritime Legal Group LLP', '890123456RT0001', 'John', 'Mitchell', 'Senior Partner', 'john.mitchell@maritimelegal.ca', '5061234567', '5069876543', 'https://maritimelegal.ca', 'Brunswick Square', 'Saint John', 'NB', 'E2L4V1', 'CA', 'Legal Services', '1-10', 'under-1m', 'Brunswick Square', 'Saint John', 'NB', 'E2L4V1', 'CA', '890123456RT0001', 'due-on-receipt', 15000.00, 'Specialized in maritime law and corporate litigation.', 'active', 'normal', 'referral', 2, '2024-06-01 16:15:00', '2024-07-14 11:00:00'),

-- Consulting & Finance
('Northern Consulting', 'Northern Consulting Services Ltd.', '901234567RT0001', 'Amanda', 'Brown', 'Principal Consultant', 'amanda.brown@northconsult.ca', '2041234567', '2049876543', 'https://northernconsulting.ca', '360 Main St', 'Winnipeg', 'MB', 'R3C3Z3', 'CA', 'Management Consulting', '1-10', 'under-1m', '360 Main St', 'Winnipeg', 'MB', 'R3C3Z3', 'CA', '901234567RT0001', 'net-30', 20000.00, 'Strategic planning and organizational development consulting.', 'active', 'normal', 'social-media', 3, '2024-04-18 10:45:00', '2024-07-09 13:20:00'),

('Capital Investment Partners', 'Capital Investment Partners Inc.', '012345678RT0001', 'Thomas', 'O''Brien', 'Investment Director', 'thomas.obrien@capitalip.ca', '4161234568', '4169876544', 'https://capitalip.ca', '181 Bay St', 'Toronto', 'ON', 'M5J2T3', 'CA', 'Financial Services', '51-200', '5m-25m', '181 Bay St', 'Toronto', 'ON', 'M5J2T3', 'CA', '012345678RT0001', 'net-15', 200000.00, 'Private equity and venture capital investment firm.', 'active', 'critical', 'trade-show', 1, '2024-01-25 14:30:00', '2024-07-17 10:00:00'),

-- Construction & Real Estate
('Boreal Construction', 'Boreal Construction Ltd.', '123450987RT0001', 'Mark', 'Lavoie', 'Project Manager', 'mark.lavoie@borealconstruct.ca', '8671234567', '8679876543', 'https://borealconstruct.ca', '4912 49th St', 'Yellowknife', 'NT', 'X1A2N2', 'CA', 'Construction', '11-50', '1m-5m', '4912 49th St', 'Yellowknife', 'NT', 'X1A2N2', 'CA', '123450987RT0001', 'net-30', 45000.00, 'Commercial and residential construction in northern communities.', 'active', 'normal', 'website', 2, '2024-03-30 08:15:00', '2024-07-11 16:45:00'),

('Island Properties', 'Island Properties Development Corp.', '234561098RT0001', 'Catherine', 'Murphy', 'Development Director', 'catherine.murphy@islandprops.ca', '9021234568', '9029876544', 'https://islandproperties.ca', '100 Grafton St', 'Charlottetown', 'PE', 'C1A1K2', 'CA', 'Real Estate Development', '11-50', '5m-25m', '100 Grafton St', 'Charlottetown', 'PE', 'C1A1K2', 'CA', '234561098RT0001', 'net-30', 80000.00, 'Residential and commercial property development across the Maritimes.', 'active', 'high', 'referral', 3, '2024-02-28 11:30:00', '2024-07-13 14:15:00'),

-- Energy & Resources
('Renewable Energy Co.', 'Renewable Energy Solutions Corp.', '345672109RT0001', 'Kevin', 'Sinclair', 'Engineering Manager', 'kevin.sinclair@renewableenergy.ca', '7801234567', '7809876543', 'https://renewableenergy.ca', '10220 103 Ave NW', 'Edmonton', 'AB', 'T5J0K4', 'CA', 'Renewable Energy', '51-200', '25m-100m', '10220 103 Ave NW', 'Edmonton', 'AB', 'T5J0K4', 'CA', '345672109RT0001', 'net-30', 120000.00, 'Solar and wind energy installation and maintenance services.', 'active', 'high', 'trade-show', 1, '2024-01-15 09:00:00', '2024-07-19 12:30:00'),

('Newfoundland Fisheries', 'Newfoundland Fisheries Ltd.', '456783210RT0001', 'Sean', 'O''Connell', 'Operations Manager', 'sean.oconnell@nlfisheries.ca', '7091234567', '7099876543', 'https://nlfisheries.ca', '30 Water St', 'St. John''s', 'NL', 'A1C1A2', 'CA', 'Fisheries', '51-200', '5m-25m', '30 Water St', 'St. John''s', 'NL', 'A1C1A2', 'CA', '456783210RT0001', 'net-30', 60000.00, 'Commercial fishing and seafood processing operations.', 'active', 'normal', 'cold-call', 2, '2024-04-12 07:45:00', '2024-07-06 15:00:00'),

-- Transportation & Logistics
('Trans-Canada Logistics', 'Trans-Canada Logistics Inc.', '567894321RT0001', 'Maria', 'Rodriguez', 'Fleet Manager', 'maria.rodriguez@tclogistics.ca', '4031234568', '4039876544', 'https://tclogistics.ca', '1010 Centre St NE', 'Calgary', 'AB', 'T2E2R2', 'CA', 'Transportation', '201-500', '25m-100m', '1010 Centre St NE', 'Calgary', 'AB', 'T2E2R2', 'CA', '567894321RT0001', 'net-30', 180000.00, 'Cross-country freight and logistics services provider.', 'active', 'critical', 'referral', 3, '2024-03-08 13:15:00', '2024-07-15 11:45:00'),

('Pacific Shipping', 'Pacific Shipping Solutions Ltd.', '678905432RT0001', 'James', 'Wang', 'Port Operations Director', 'james.wang@pacificshipping.ca', '6041234568', '6049876544', 'https://pacificshipping.ca', '999 Canada Pl', 'Vancouver', 'BC', 'V6C3T4', 'CA', 'Shipping & Maritime', '11-50', '1m-5m', '999 Canada Pl', 'Vancouver', 'BC', 'V6C3T4', 'CA', '678905432RT0001', 'net-30', 35000.00, 'International shipping and port services across the Pacific.', 'active', 'normal', 'website', 1, '2024-05-20 10:00:00', '2024-07-07 16:30:00'),

-- Retail & E-commerce
('Northern Retail Chain', 'Northern Retail Chain Inc.', '789016543RT0001', 'Rebecca', 'Johansson', 'Regional Manager', 'rebecca.johansson@northernretail.ca', '8671234568', '8679876544', 'https://northernretail.ca', '5015 49th Ave', 'Yellowknife', 'NT', 'X1A3T2', 'CA', 'Retail', '11-50', '1m-5m', '5015 49th Ave', 'Yellowknife', 'NT', 'X1A3T2', 'CA', '789016543RT0001', 'net-30', 25000.00, 'General merchandise retailer serving northern communities.', 'active', 'normal', 'cold-call', 2, '2024-06-15 14:20:00', '2024-07-04 09:30:00'),

('Maritime E-commerce', 'Maritime E-commerce Solutions Corp.', '890127654RT0001', 'Daniel', 'Leblanc', 'E-commerce Director', 'daniel.leblanc@maritimeecom.ca', '5061234568', '5069876544', 'https://maritimeecommerce.ca', '15 King St', 'Saint John', 'NB', 'E2L1G4', 'CA', 'E-commerce', '1-10', 'under-1m', '15 King St', 'Saint John', 'NB', 'E2L1G4', 'CA', '890127654RT0001', 'net-15', 12000.00, 'Online retail platform development and digital marketing.', 'prospect', 'low', 'social-media', 3, NULL, '2024-07-18 10:15:00'),

-- Agriculture & Food
('Prairie Agriculture', 'Prairie Agriculture Corp.', '901238765RT0001', 'Paul', 'Kowalski', 'Farm Operations Manager', 'paul.kowalski@prairieag.ca', '3061234568', '3069876544', 'https://prairieagriculture.ca', '1245 Central Ave', 'Saskatoon', 'SK', 'S7N2H1', 'CA', 'Agriculture', '201-500', '25m-100m', '1245 Central Ave', 'Saskatoon', 'SK', 'S7N2H1', 'CA', '901238765RT0001', 'net-60', 200000.00, 'Large-scale crop production and agricultural equipment services.', 'active', 'high', 'trade-show', 1, '2024-02-10 06:30:00', '2024-07-12 17:00:00'),

('Organic Food Distributors', 'Organic Food Distributors Ltd.', '012349876RT0001', 'Sophie', 'Dubois', 'Distribution Manager', 'sophie.dubois@organicfood.ca', '5141234568', '5149876544', 'https://organicfooddist.ca', '3456 Rue Saint-Denis', 'Montreal', 'QC', 'H2X3L1', 'CA', 'Food Distribution', '11-50', '5m-25m', '3456 Rue Saint-Denis', 'Montreal', 'QC', 'H2X3L1', 'CA', '012349876RT0001', 'net-30', 55000.00, 'Organic and specialty food distribution to retailers and restaurants.', 'active', 'normal', 'website', 2, '2024-04-25 11:45:00', '2024-07-08 13:50:00'),

-- Education & Training
('Skills Development Institute', 'Skills Development Institute Inc.', '123457890RT0001', 'Dr. Rachel', 'Green', 'Academic Director', 'rachel.green@skillsdev.ca', '4161234569', '4169876545', 'https://skillsdevelopment.ca', '789 Yonge St', 'Toronto', 'ON', 'M4W2G8', 'CA', 'Education & Training', '11-50', '1m-5m', '789 Yonge St', 'Toronto', 'ON', 'M4W2G8', 'CA', '123457890RT0001', 'net-30', 40000.00, 'Professional development and corporate training programs.', 'active', 'normal', 'referral', 3, '2024-03-18 15:30:00', '2024-07-10 12:15:00'),

-- Tourism & Hospitality
('Yukon Adventure Tours', 'Yukon Adventure Tours Ltd.', '234568901RT0001', 'Michelle', 'Taylor', 'Tour Operations Manager', 'michelle.taylor@yukonadventure.ca', '8671234569', '8679876545', 'https://yukonadventure.ca', '212 Main St', 'Whitehorse', 'YT', 'Y1A2B6', 'CA', 'Tourism', '1-10', 'under-1m', '212 Main St', 'Whitehorse', 'YT', 'Y1A2B6', 'CA', '234568901RT0001', 'due-on-receipt', 8000.00, 'Wilderness adventure tours and outdoor recreation services.', 'prospect', 'low', 'website', 1, NULL, '2024-07-15 08:30:00'),

('Arctic Hospitality Group', 'Arctic Hospitality Group Inc.', '345679012RT0001', 'Peter', 'Kanguq', 'General Manager', 'peter.kanguq@arctichospitality.ca', '8671234570', '8679876546', 'https://arctichospitality.ca', '1102 Gjoa Haven', 'Iqaluit', 'NU', 'X0A0H0', 'CA', 'Hospitality', '11-50', '1m-5m', '1102 Gjoa Haven', 'Iqaluit', 'NU', 'X0A0H0', 'CA', '345679012RT0001', 'net-30', 30000.00, 'Hotel and accommodation services in Arctic communities.', 'active', 'normal', 'cold-call', 2, '2024-05-12 12:45:00', '2024-07-14 16:20:00'),

-- Mining & Resources
('Northern Mining Corp.', 'Northern Mining Corporation', '456780123RT0001', 'Andrew', 'Mackenzie', 'Mine Supervisor', 'andrew.mackenzie@northernmining.ca', '7091234568', '7099876545', 'https://northernmining.ca', '567 Topsail Rd', 'St. John''s', 'NL', 'A1E2C5', 'CA', 'Mining', '201-500', 'over-100m', '567 Topsail Rd', 'St. John''s', 'NL', 'A1E2C5', 'CA', '456780123RT0001', 'net-30', 500000.00, 'Iron ore and copper mining operations with multiple sites.', 'active', 'critical', 'trade-show', 3, '2024-01-20 07:00:00', '2024-07-16 18:00:00'),

-- Telecommunications
('Island Communications', 'Island Communications Ltd.', '567891234RT0001', 'Mary', 'Campbell', 'Network Operations Manager', 'mary.campbell@islandcomm.ca', '9021234569', '9029876545', 'https://islandcommunications.ca', '45 University Ave', 'Charlottetown', 'PE', 'C1A4P3', 'CA', 'Telecommunications', '51-200', '5m-25m', '45 University Ave', 'Charlottetown', 'PE', 'C1A4P3', 'CA', '567891234RT0001', 'net-30', 85000.00, 'Regional telecommunications and internet service provider.', 'active', 'high', 'referral', 1, '2024-02-05 10:20:00', '2024-07-11 14:40:00'),

-- Environmental Services
('Green Solutions Canada', 'Green Solutions Canada Inc.', '678902345RT0001', 'Elena', 'Petrov', 'Environmental Consultant', 'elena.petrov@greensolutions.ca', '2041234568', '2049876545', 'https://greensolutions.ca', '456 Portage Ave', 'Winnipeg', 'MB', 'R3C0E6', 'CA', 'Environmental Services', '1-10', 'under-1m', '456 Portage Ave', 'Winnipeg', 'MB', 'R3C0E6', 'CA', '678902345RT0001', 'net-30', 15000.00, 'Environmental assessment and remediation consulting services.', 'prospect', 'normal', 'social-media', 2, NULL, '2024-07-17 09:10:00'),

-- Aviation
('Northern Airways', 'Northern Airways Ltd.', '789013456RT0001', 'Captain Steve', 'Morrison', 'Operations Director', 'steve.morrison@northernairways.ca', '8671234571', '8679876547', 'https://northernairways.ca', '200 Airport Rd', 'Yellowknife', 'NT', 'X1A3T4', 'CA', 'Aviation', '11-50', '5m-25m', '200 Airport Rd', 'Yellowknife', 'NT', 'X1A3T4', 'CA', '789013456RT0001', 'net-15', 75000.00, 'Charter flights and cargo services to remote northern communities.', 'active', 'high', 'website', 3, '2024-04-08 06:15:00', '2024-07-13 19:30:00'),

-- Media & Marketing
('Atlantic Media Group', 'Atlantic Media Group Inc.', '890124567RT0001', 'Jennifer', 'MacLeod', 'Creative Director', 'jennifer.macleod@atlanticmedia.ca', '9021234570', '9029876546', 'https://atlanticmedia.ca', '789 Barrington St', 'Halifax', 'NS', 'B3J1P2', 'CA', 'Media & Marketing', '11-50', '1m-5m', '789 Barrington St', 'Halifax', 'NS', 'B3J1P2', 'CA', '890124567RT0001', 'net-30', 35000.00, 'Full-service marketing agency specializing in digital and traditional media.', 'active', 'normal', 'referral', 1, '2024-05-30 13:00:00', '2024-07-09 15:45:00');

-- >>> Down >>>
DROP TABLE IF EXISTS `{!!prefix!!}b2bcnc_clients`;