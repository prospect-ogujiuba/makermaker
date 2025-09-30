-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_coverage_areas`
(`code`, `name`, `country_code`, `region_type`, `timezone`, `postal_code_pattern`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('greater_toronto_area', 'Greater Toronto Area', 'CA', 'city', 'America/Toronto', '^[LMNP][0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('southwest_ontario', 'Southwest Ontario', 'CA', 'province', 'America/Toronto', '^N[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('central_ontario', 'Central Ontario', 'CA', 'province', 'America/Toronto', '^[KL][0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('eastern_ontario', 'Eastern Ontario', 'CA', 'province', 'America/Toronto', '^K[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('northern_ontario', 'Northern Ontario', 'CA', 'province', 'America/Toronto', '^P[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('province_of_quebec', 'Province of Quebec', 'CA', 'province', 'America/Montreal', '^[GHJ][0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('british_columbia', 'British Columbia', 'CA', 'province', 'America/Vancouver', '^V[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('alberta', 'Alberta', 'CA', 'province', 'America/Edmonton', '^T[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('manitoba', 'Manitoba', 'CA', 'province', 'America/Winnipeg', '^R[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('saskatchewan', 'Saskatchewan', 'CA', 'province', 'America/Regina', '^S[0-9][A-Z] ?[0-9][A-Z][0-9]$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('western_canada', 'Western Canada', 'CA', 'country', 'America/Vancouver', NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('national_coverage', 'National Coverage - Canada', 'CA', 'country', 'America/Toronto', NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('northeast_us', 'Northeast United States', 'US', 'country', 'America/New_York', '^\d{5}(-\d{4})?$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('midwest_us', 'Midwest United States', 'US', 'country', 'America/Chicago', '^\d{5}(-\d{4})?$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('southeast_us', 'Southeast United States', 'US', 'country', 'America/New_York', '^\d{5}(-\d{4})?$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('southwest_us', 'Southwest United States', 'US', 'country', 'America/Denver', '^\d{5}(-\d{4})?$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('west_coast_us', 'West Coast United States', 'US', 'country', 'America/Los_Angeles', '^\d{5}(-\d{4})?$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('new_york_metro', 'New York Metropolitan Area', 'US', 'city', 'America/New_York', '^1[0-1]\d{3}$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('los_angeles_metro', 'Los Angeles Metropolitan Area', 'US', 'city', 'America/Los_Angeles', '^9[0-6]\d{3}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('chicago_metro', 'Chicago Metropolitan Area', 'US', 'city', 'America/Chicago', '^60[0-9]{3}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('united_kingdom', 'United Kingdom', 'GB', 'country', 'Europe/London', '^[A-Z]{1,2}\d[A-Z\d]? ?\d[A-Z]{2}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('germany', 'Germany', 'DE', 'country', 'Europe/Berlin', '^\d{5}$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('france', 'France', 'FR', 'country', 'Europe/Paris', '^\d{5}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('australia', 'Australia', 'AU', 'country', 'Australia/Sydney', '^\d{4}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('japan', 'Japan', 'JP', 'country', 'Asia/Tokyo', '^\d{3}-?\d{4}$', '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('singapore', 'Singapore', 'SG', 'country', 'Asia/Singapore', '^\d{6}$', '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('north_america', 'North America', NULL, 'continent', 'America/New_York', NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('europe', 'Europe', NULL, 'continent', 'Europe/London', NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('asia_pacific_coverage', 'Asia-Pacific Region', NULL, 'continent', 'Asia/Singapore', NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('global_coverage', 'Global Coverage', NULL, 'global', 'UTC', NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('international_coverage', 'International Coverage', NULL, 'global', 'UTC', NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_coverage_areas`;