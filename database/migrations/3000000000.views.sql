-- Description:
-- >>> Up >>>
CREATE VIEW `{!!prefix!!}srvc_v_current_service_pricing` AS
SELECT
    s.id as service_id,
    s.name as service_name,
    s.slug as service_slug,
    c.name as category_name,
    st.name as service_type_name,
    sc.name as complexity_name,
    pt.name as pricing_tier_name,
    pm.name as pricing_model_name,
    sp.currency,
    sp.amount,
    sp.setup_fee,
    sp.unit,
    (sp.amount * sc.price_multiplier) as adjusted_amount,
    sp.valid_from,
    sp.valid_to,
    sp.approval_status
FROM `{!!prefix!!}srvc_services` s
LEFT JOIN `{!!prefix!!}srvc_categories` c ON s.category_id = c.id
LEFT JOIN `{!!prefix!!}srvc_service_types` st ON s.service_type_id = st.id
LEFT JOIN `{!!prefix!!}srvc_complexity_levels` sc ON s.complexity_id = sc.id
LEFT JOIN `{!!prefix!!}srvc_service_prices` sp ON s.id = sp.service_id AND sp.is_current = 1 AND sp.deleted_at IS NULL
LEFT JOIN `{!!prefix!!}srvc_pricing_tiers` pt ON sp.pricing_tier_id = pt.id
LEFT JOIN `{!!prefix!!}srvc_pricing_models` pm ON sp.pricing_model_id = pm.id
WHERE s.deleted_at IS NULL
  AND s.is_active = 1
  AND (sp.valid_to IS NULL OR sp.valid_to > NOW())
  AND (c.deleted_at IS NULL OR c.deleted_at IS NULL)
  AND (pt.deleted_at IS NULL OR pt.deleted_at IS NULL)
  AND (pm.deleted_at IS NULL OR pm.deleted_at IS NULL);

-- >>> Down >>>
DELETE VIEW `{!!prefix!!}srvc_v_current_service_pricing`;