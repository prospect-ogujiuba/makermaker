<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class Service extends Model
{
    protected $resource = 'srvc_services';

    protected $fillable = [
        'sku',
        'slug',
        'name',
        'short_desc',
        'long_desc',
        'category_id',
        'service_type_id',
        'complexity_id',
        'is_active',
        'is_featured',
        'minimum_quantity',
        'maximum_quantity',
        'estimated_hours',
        'skill_level',
        'metadata',
        'version'
    ];


    protected $format = [
        'metadata' => 'json_encode',
        'minimum_quantity' => 'convertEmptyToNull',
        'maximum_quantity' => 'convertEmptyToNull',
        'estimated_hours' => 'convertEmptyToNull',

    ];
    protected $cast = [
        'metadata' => 'array'
    ];

    protected $guard = [
        'id',
        'version',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    // protected $with = [
    //     'serviceType',
    //     'category',
    //     'complexity',
    //     'prices',
    //     'coverages',
    //     'addonServices',
    //     'parentServices',
    //     'relatedServices',
    //     'reverseRelatedServices',
    //     'equipment',
    //     'deliverables',
    //     'deliveryMethods',
    //     'bundles',
    // ];

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

    /** Service belongs to a ComplexityLevel */
    public function complexity()
    {
        return $this->belongsTo(ComplexityLevel::class, 'complexity_id');
    }

    /** Service has many ServicePrices */
    public function prices()
    {
        return $this->hasMany(ServicePrice::class, 'service_id');
    }

    /** Service has many ServiceCoverages */
    public function coverages()
    {
        return $this->hasMany(ServiceCoverage::class, 'service_id');
    }

    /** Services this Service requires as addons */
    public function addonServices()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_addons', 'service_id', 'addon_service_id');
    }

    /** Services that include THIS service as an addon */
    public function parentServices()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_addons', 'addon_service_id', 'service_id');
    }

    /** Services related to this Service (prerequisites, dependencies, etc.) */
    public function relatedServices()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_relationships', 'service_id', 'related_service_id');
    }

    /** Services that relate TO this Service */
    public function reverseRelatedServices()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_relationships', 'related_service_id', 'service_id');
    }

    /** Service belongs to many Equipment */
    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, GLOBAL_WPDB_PREFIX . 'srvc_service_equipment', 'service_id', 'equipment_id');
    }

    /** Service belongs to many Deliverables */
    public function deliverables()
    {
        return $this->belongsToMany(ServiceDeliverable::class, GLOBAL_WPDB_PREFIX . 'srvc_service_deliverable_assignments', 'service_id', 'deliverable_id');
    }

    /** Service belongs to many DeliveryMethods */
    public function deliveryMethods()
    {
        return $this->belongsToMany(DeliveryMethod::class, GLOBAL_WPDB_PREFIX . 'srvc_service_delivery_methods', 'service_id', 'delivery_method_id');
    }

    /** Service belongs to many Bundles */
    public function bundles()
    {
        return $this->belongsToMany(ServiceBundle::class, GLOBAL_WPDB_PREFIX . 'srvc_bundle_items', 'service_id', 'bundle_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    // Simplified attribute handling for timestamp-based metadata structure
    public function getAttribute($key, $default = null)
    {
        $metadata = $this->getMetadataArray();

        foreach ($metadata as $item) {
            if (isset($item['key']) && $item['key'] === $key) {
                return $item['value'] ?? $default;
            }
        }

        return $default;
    }

    public function setAttribute($key, $value)
    {
        $metadata = $this->getMetadataArray();
        $found = false;

        // Update existing attribute
        foreach ($metadata as $timestamp => &$item) {
            if (isset($item['key']) && $item['key'] === $key) {
                $item['value'] = $value;
                $found = true;
                break;
            }
        }

        // Add new attribute with timestamp key
        if (!$found) {
            $timestamp = (string)(microtime(true) * 1000000); // microsecond timestamp
            $metadata[$timestamp] = [
                'key' => $key,
                'value' => $value
            ];
        }

        $this->metadata = json_encode($metadata);
        return $this;
    }

    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    public function getAttributes()
    {
        $metadata = $this->getMetadataArray();
        $result = [];

        foreach ($metadata as $item) {
            if (isset($item['key'], $item['value'])) {
                $result[$item['key']] = $item['value'];
            }
        }

        return $result;
    }

    public function hasAttribute($key)
    {
        $metadata = $this->getMetadataArray();

        foreach ($metadata as $item) {
            if (isset($item['key']) && $item['key'] === $key) {
                return true;
            }
        }

        return false;
    }

    public function removeAttribute($key)
    {
        $metadata = $this->getMetadataArray();

        foreach ($metadata as $timestamp => $item) {
            if (isset($item['key']) && $item['key'] === $key) {
                unset($metadata[$timestamp]);
                break;
            }
        }

        $this->metadata = json_encode($metadata);
        return $this;
    }

    public function getAttributeTimestamps()
    {
        $metadata = $this->getMetadataArray();
        $result = [];

        foreach ($metadata as $timestamp => $item) {
            if (isset($item['key'])) {
                $result[$item['key']] = $timestamp;
            }
        }

        return $result;
    }

    private function getMetadataArray()
    {
        if (empty($this->metadata)) {
            return [];
        }

        $decoded = json_decode($this->metadata, true);
        return is_array($decoded) ? $decoded : [];
    }
}
