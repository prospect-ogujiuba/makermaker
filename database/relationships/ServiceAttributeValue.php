<?php

// ARTIFACT: app/Models/ServiceAttributeValue.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceAttributeValue belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAttributeValue belongs to a ServiceAttributeDefinition */
    public function attributeDefinition()
    {
        return $this->belongsTo(ServiceAttributeDefinition::class, 'attribute_definition_id');
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