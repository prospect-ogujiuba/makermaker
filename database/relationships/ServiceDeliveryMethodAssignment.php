<?php

// ARTIFACT: app/Models/ServiceDeliveryMethodAssignment.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceDeliveryMethodAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliveryMethodAssignment belongs to a ServiceDeliveryMethod */
    public function deliveryMethod()
    {
        return $this->belongsTo(ServiceDeliveryMethod::class, 'delivery_method_id');
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