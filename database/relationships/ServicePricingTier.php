<?php

// ARTIFACT: app/Models/ServicePricingTier.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServicePricingTier has many ServicePrices */
    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class, 'pricing_tier_id');
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