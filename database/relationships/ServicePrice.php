<?php

// ARTIFACT: app/Models/ServicePrice.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServicePrice belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServicePrice belongs to a ServicePricingTier */
    public function pricingTier()
    {
        return $this->belongsTo(ServicePricingTier::class, 'pricing_tier_id');
    }

    /** ServicePrice belongs to a ServicePricingModel */
    public function pricingModel()
    {
        return $this->belongsTo(ServicePricingModel::class, 'pricing_model_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'updated_by');
    }

    // === Relationships:END ===