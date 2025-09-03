<?php

// ARTIFACT: app/Models/ServiceComplexity.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceComplexity has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'complexity_id');
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