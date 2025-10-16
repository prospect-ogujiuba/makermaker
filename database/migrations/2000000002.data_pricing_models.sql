-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_pricing_models`
(`name`, `code`, `description`, `is_time_based`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('Fixed Project', 'fixed_project', 'One-time fixed price for entire project scope', 0, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Hourly Rate', 'hourly_rate', 'Billing based on time spent working', 1, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Per Unit/Device', 'per_unit_or_device', 'Pricing per individual unit or device', 0, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Monthly Subscription', 'monthly_subscription', 'Recurring monthly payment model', 1, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Annual Contract', 'annual_contract', 'Yearly contract with fixed pricing', 1, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Per Square Foot', 'per_square_foot', 'Pricing based on area coverage', 0, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Tiered Pricing', 'tiered_pricing', 'Volume-based pricing with different tiers', 0, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Usage-Based', 'usage_based', 'Pay based on actual usage or consumption', 1, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Pay-As-You-Go', 'pay_as_you_go', 'Flexible pricing for services as needed', 0, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Hybrid Model', 'hybrid_model', 'Combination of multiple pricing approaches', 0, '2025-08-28 23:57:07', NOW(), NULL, 1, 1);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_pricing_models`;
