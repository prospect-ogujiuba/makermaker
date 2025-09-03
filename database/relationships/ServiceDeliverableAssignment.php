<?php

// ARTIFACT: app/Models/ServiceDeliverableAssignment.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceDeliverableAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliverableAssignment belongs to a ServiceDeliverable */
    public function deliverable()
    {
        return $this->belongsTo(ServiceDeliverable::class, 'deliverable_id');
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