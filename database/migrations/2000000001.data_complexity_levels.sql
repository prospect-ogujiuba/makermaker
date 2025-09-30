-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_complexity_levels`
(`name`, `level`, `price_multiplier`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`) VALUES
('Proof of Concept', 0, 0.89, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Evaluation', 1, 1.09, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Foundational', 2, 1.29, '2025-08-28 23:57:07', NOW(), NULL, 1, 2),
('Standard', 3, 1.49, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),
('Enhanced', 4, 1.69, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),
('Advanced', 5, 1.89, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Professional', 6, 2.19, '2025-08-28 23:57:07', NOW(), NULL, 2, 1),
('Enterprise', 7, 2.59, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Bespoke', 8, 2.89, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),
('Strategic', 9, 3.29, '2025-08-28 23:57:07', NOW(), NULL, 1, 1),
('Transformational', 10, 3.79, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('Mission Critical', 11, 4.29, '2025-08-28 23:57:07', NOW(), NULL, 2, 2),
('R&D Experimental', 12, 4.79, '2025-08-28 23:57:07', NOW(), NULL, 2, 1);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_complexity_levels`;