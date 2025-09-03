<?php

// ARTIFACT: app/Models/ServicePricingModel.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServicePricingModel has many ServicePrices */
    public function servicePrices()
    {
        return $this->hasMany(ServicePrice::class, 'pricing_model_id');
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