<?php

// ARTIFACT: app/Models/ServiceEquipmentAssignment.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceEquipmentAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceEquipmentAssignment belongs to a ServiceEquipment */
    public function equipment()
    {
        return $this->belongsTo(ServiceEquipment::class, 'equipment_id');
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