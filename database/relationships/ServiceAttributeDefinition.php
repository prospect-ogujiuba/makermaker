<?php

// ARTIFACT: app/Models/ServiceAttributeDefinition.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceAttributeDefinition belongs to a ServiceType */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    /** ServiceAttributeDefinition has many ServiceAttributeValues */
    public function attributeValues()
    {
        return $this->hasMany(ServiceAttributeValue::class, 'attribute_definition_id');
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