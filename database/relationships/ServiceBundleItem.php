<?php

// ARTIFACT: app/Models/ServiceBundleItem.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceBundleItem belongs to a ServiceBundle */
    public function bundle()
    {
        return $this->belongsTo(ServiceBundle::class, 'bundle_id');
    }

    /** ServiceBundleItem belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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