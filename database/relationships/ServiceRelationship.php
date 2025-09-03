<?php

// ARTIFACT: app/Models/ServiceRelationship.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceRelationship belongs to a Service (the primary service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceRelationship belongs to a Service (the related service) */
    public function relatedService()
    {
        return $this->belongsTo(Service::class, 'related_service_id');
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