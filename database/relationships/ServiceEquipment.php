<?php

// ARTIFACT: app/Models/ServiceEquipment.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceEquipment belongs to many Services */
    public function services()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_equipment_assignments', 'equipment_id', 'service_id');
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