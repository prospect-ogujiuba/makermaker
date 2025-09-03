<?php

// ARTIFACT: app/Models/Service.php (relationships)
    
    // === Relationships:BEGIN ===

    /** Service belongs to a ServiceType */
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    /** Service belongs to a ServiceCategory */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /** Service belongs to a ServiceComplexity */
    public function complexity()
    {
        return $this->belongsTo(ServiceComplexity::class, 'complexity_id');
    }

    /** Service has many ServicePrices */
    public function prices()
    {
        return $this->hasMany(ServicePrice::class, 'service_id');
    }

    /** Service has many ServiceAttributeValues */
    public function attributeValues()
    {
        return $this->hasMany(ServiceAttributeValue::class, 'service_id');
    }

    /** Service has many ServiceCoverages */
    public function coverages()
    {
        return $this->hasMany(ServiceCoverage::class, 'service_id');
    }

    /** Services this Service requires as addons */
    public function addonServices()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_addons', 'service_id', 'addon_service_id');
    }

    /** Services that include THIS service as an addon */
    public function parentServices()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_addons', 'addon_service_id', 'service_id');
    }

    /** Services related to this Service (prerequisites, dependencies, etc.) */
    public function relatedServices()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_relationships', 'service_id', 'related_service_id');
    }

    /** Services that relate TO this Service */
    public function reverseRelatedServices()
    {
        return $this->belongsToMany(Service::class, '{!!prefix!!}srvc_service_relationships', 'related_service_id', 'service_id');
    }

    /** Service belongs to many Equipment */
    public function equipment()
    {
        return $this->belongsToMany(ServiceEquipment::class, '{!!prefix!!}srvc_service_equipment_assignments', 'service_id', 'equipment_id');
    }

    /** Service belongs to many Deliverables */
    public function deliverables()
    {
        return $this->belongsToMany(ServiceDeliverable::class, '{!!prefix!!}srvc_service_deliverable_assignments', 'service_id', 'deliverable_id');
    }

    /** Service belongs to many DeliveryMethods */
    public function deliveryMethods()
    {
        return $this->belongsToMany(ServiceDeliveryMethod::class, '{!!prefix!!}srvc_service_delivery_method_assignments', 'service_id', 'delivery_method_id');
    }

    /** Service belongs to many Bundles */
    public function bundles()
    {
        return $this->belongsToMany(ServiceBundle::class, '{!!prefix!!}srvc_bundle_items', 'service_id', 'bundle_id');
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