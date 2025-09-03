<?php

// ARTIFACT: app/Models/ServiceType.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceType has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'service_type_id');
    }

    /** ServiceType has many AttributeDefinitions */
    public function attributeDefinitions()
    {
        return $this->hasMany(ServiceAttributeDefinition::class, 'service_type_id');
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