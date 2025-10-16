-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_pricing_tiers`
(`name`, `code`, `sort_order`, `discount_pct`, `min_volume`, `max_volume`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('Small Business', 'small_business', 1, 0.00, 1, 10, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Mid-Market', 'mid_market', 2, 5.00, 11, 100, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Enterprise', 'enterprise', 3, 15.00, 101, 1000, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Government', 'government', 4, 10.00, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Non-Profit', 'non_profit', 5, 20.00, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Startups', 'startups', 6, 25.00, 1, 5, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Education', 'education', 7, 15.00, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Healthcare', 'healthcare', 8, 12.00, NULL, NULL, '2025-08-28 23:57:07', NOW(), NULL, 2, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_pricing_tiers`;