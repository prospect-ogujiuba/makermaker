<?php

// ARTIFACT: app/Models/ServiceAddon.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceAddon belongs to a Service (the main service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAddon belongs to a Service (the addon service) */
    public function addonService()
    {
        return $this->belongsTo(Service::class, 'addon_service_id');
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