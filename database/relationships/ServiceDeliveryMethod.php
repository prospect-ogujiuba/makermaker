<?php

// ARTIFACT: app/Models/ServiceDeliveryMethod.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceDeliveryMethod belongs to many Services */
    public function services()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_delivery_method_assignments', 'delivery_method_id', 'service_id');
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