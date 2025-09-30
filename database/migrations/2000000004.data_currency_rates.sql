-- Description:
-- >>> Up >>>
INSERT INTO `{!!prefix!!}srvc_currency_rates`
(`from_currency`, `to_currency`, `exchange_rate`, `effective_date`, `source`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
('CAD', 'USD', 0.740000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'EUR', 0.670000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'GBP', 0.580000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'AUD', 1.120000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'JPY', 110.500000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'CHF', 0.660000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('CAD', 'MXN', 12.800000, '2025-09-26', 'bank_of_canada', '2025-08-28 23:57:07', NOW(), 1, 1),
('USD', 'CAD', 1.351351, '2025-09-26', 'federal_reserve', '2025-08-28 23:57:07', NOW(), 1, 1),
('USD', 'EUR', 0.905405, '2025-09-26', 'federal_reserve', '2025-08-28 23:57:07', NOW(), 1, 1),
('USD', 'GBP', 0.783784, '2025-09-26', 'federal_reserve', '2025-08-28 23:57:07', NOW(), 1, 1),
('USD', 'AUD', 1.513514, '2025-09-26', 'federal_reserve', '2025-08-28 23:57:07', NOW(), 1, 1),
('USD', 'JPY', 149.324324, '2025-09-26', 'federal_reserve', '2025-08-28 23:57:07', NOW(), 1, 1),
('EUR', 'CAD', 1.492537, '2025-09-26', 'ecb', '2025-08-28 23:57:07', NOW(), 2, 2),
('EUR', 'USD', 1.104478, '2025-09-26', 'ecb', '2025-08-28 23:57:07', NOW(), 2, 2),
('EUR', 'GBP', 0.865672, '2025-09-26', 'ecb', '2025-08-28 23:57:07', NOW(), 2, 2),
('GBP', 'CAD', 1.724138, '2025-09-26', 'bank_of_england', '2025-08-28 23:57:07', NOW(), 2, 2),
('GBP', 'USD', 1.275862, '2025-09-26', 'bank_of_england', '2025-08-28 23:57:07', NOW(), 2, 2),
('GBP', 'EUR', 1.155172, '2025-09-26', 'bank_of_england', '2025-08-28 23:57:07', NOW(), 2, 2);

-- >>> Down >>>
DELETE FROM `{!!prefix!!}srvc_currency_rates`;