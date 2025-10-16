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

    protected $with = [
        'serviceType',
        'category.parentCategory',
        'complexity',
        'prices.pricingTier',
        'prices.pricingModel',
        'coverages.coverageArea',
        'addonServices',
        'parentServices',
        'relationships.relatedService',
        'equipment',
        'deliverables',
        'deliveryMethods',
        'bundles.services',
    ];

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

    /** Service has many Service Relationships */
    public function relationships()
    {
        return $this->hasMany(ServiceRelationship::class, 'service_id');
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
        return $this->belongsToMany(Deliverable::class, GLOBAL_WPDB_PREFIX . 'srvc_service_deliverables', 'service_id', 'deliverable_id');
    }

    /** Service belongs to many DeliveryMethods */
    public function deliveryMethods()
    {
        return $this->belongsToMany(DeliveryMethod::class, GLOBAL_WPDB_PREFIX . 'srvc_service_delivery', 'service_id', 'delivery_method_id');
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


    /**
     * Get all active services
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get all featured services
     */
    public function getFeatured()
    {
        return $this->where('is_featured', 1)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get services by category
     */
    public function getByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get services by type
     */
    public function getByType($typeId)
    {
        return $this->where('service_type_id', $typeId)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get services by complexity level
     */
    public function getByComplexity($complexityId)
    {
        return $this->where('complexity_id', $complexityId)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get services by skill level
     */
    public function getBySkillLevel($skillLevel)
    {
        $validLevels = ['entry', 'intermediate', 'advanced', 'expert', 'specialist'];

        if (!in_array($skillLevel, $validLevels)) {
            throw new \InvalidArgumentException("Invalid skill level: {$skillLevel}");
        }

        return $this->where('skill_level', $skillLevel)
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Search services by keyword (searches name, short_desc, long_desc)
     */
    public function search($keyword)
    {
        return $this->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('short_desc', 'LIKE', "%{$keyword}%")
            ->orWhere('long_desc', 'LIKE', "%{$keyword}%")
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Find service by SKU
     */
    public function findBySku($sku)
    {
        return $this->where('sku', $sku)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Find service by slug
     */
    public function findBySlug($slug)
    {
        return $this->where('slug', $slug)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get services with current pricing
     */
    public function getWithCurrentPricing($currency = 'CAD', $tierCode = null)
    {
        $this->where('is_active', 1)
            ->where('deleted_at', 'IS', null);

        $services = $this->findAll()->get();

        if (!$services) {
            return null;
        }

        // Filter services that have current pricing
        $results = [];
        foreach ($services as $service) {
            $prices = $service->prices;
            if ($prices) {
                foreach ($prices as $price) {
                    $matchesCurrency = $price->currency === $currency;
                    $matchesTier = $tierCode ? ($price->pricingTier->code ?? null) === $tierCode : true;
                    $isCurrent = $price->is_current == 1;
                    $isApproved = $price->approval_status === 'approved';

                    if ($matchesCurrency && $matchesTier && $isCurrent && $isApproved) {
                        $service->current_price = $price;
                        $results[] = $service;
                        break;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Get service with calculated total price (includes complexity multiplier)
     */
    public function getCalculatedPrice($serviceId, $currency = 'CAD', $tierCode = 'small_business')
    {
        $service = $this->findById($serviceId);

        if (!$service) {
            return null;
        }

        $complexity = $service->complexity;
        $multiplier = $complexity->price_multiplier ?? 1.0;

        $prices = $service->prices;
        if ($prices) {
            foreach ($prices as $price) {
                $matchesCurrency = $price->currency === $currency;
                $matchesTier = ($price->pricingTier->code ?? null) === $tierCode;
                $isCurrent = $price->is_current == 1;
                $isApproved = $price->approval_status === 'approved';

                if ($matchesCurrency && $matchesTier && $isCurrent && $isApproved) {
                    return [
                        'service' => $service,
                        'base_amount' => $price->amount,
                        'setup_fee' => $price->setup_fee,
                        'multiplier' => $multiplier,
                        'adjusted_amount' => $price->amount * $multiplier,
                        'total_with_setup' => ($price->amount * $multiplier) + ($price->setup_fee ?? 0),
                        'currency' => $price->currency,
                        'unit' => $price->unit,
                        'pricing_model' => $price->pricingModel->name ?? null
                    ];
                }
            }
        }

        return null;
    }

    /**
     * Check if service requires specific quantity
     */
    public function hasQuantityConstraints()
    {
        return $this->minimum_quantity !== null || $this->maximum_quantity !== null;
    }

    /**
     * Validate quantity against service constraints
     */
    public function validateQuantity($quantity)
    {
        $errors = [];

        if ($this->minimum_quantity !== null && $quantity < $this->minimum_quantity) {
            $errors[] = "Quantity must be at least {$this->minimum_quantity}";
        }

        if ($this->maximum_quantity !== null && $quantity > $this->maximum_quantity) {
            $errors[] = "Quantity cannot exceed {$this->maximum_quantity}";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Get total estimated effort including deliverables
     */
    public function getTotalEstimatedEffort()
    {
        $serviceHours = $this->estimated_hours ?? 0;
        $deliverableHours = 0;

        $deliverables = $this->deliverables;
        if ($deliverables) {
            foreach ($deliverables as $deliverable) {
                $deliverableHours += $deliverable->estimated_effort_hours ?? 0;
            }
        }

        return $serviceHours + $deliverableHours;
    }

    /**
     * Check if service is available in a specific coverage area
     */
    public function isAvailableInArea($coverageAreaId)
    {
        $coverages = $this->coverages;

        if (!$coverages) {
            return false;
        }

        foreach ($coverages as $coverage) {
            if ($coverage->coverage_area_id == $coverageAreaId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all prerequisites for this service
     */
    public function getPrerequisites()
    {
        $relationships = $this->relationships;
        $prerequisites = [];

        if ($relationships) {
            foreach ($relationships as $rel) {
                if ($rel->relation_type === 'prerequisite') {
                    $prerequisites[] = $rel->relatedService;
                }
            }
        }

        return $prerequisites;
    }

    /**
     * Get all incompatible services
     */
    public function getIncompatibleServices()
    {
        $relationships = $this->relationships;
        $incompatible = [];

        if ($relationships) {
            foreach ($relationships as $rel) {
                if ($rel->relation_type === 'incompatible_with') {
                    $incompatible[] = $rel->relatedService;
                }
            }
        }

        return $incompatible;
    }

    /**
     * Check if this service conflicts with another service
     */
    public function conflictsWith($serviceId)
    {
        $incompatible = $this->getIncompatibleServices();

        foreach ($incompatible as $service) {
            if ($service->id == $serviceId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get required equipment total cost
     */
    public function getRequiredEquipmentCost()
    {
        $equipment = $this->equipment;
        $totalCost = 0;

        if (!$equipment) {
            return 0;
        }

        // Get pivot data through junction
        $serviceEquipment = ServiceEquipment::new()
            ->where('service_id', $this->id)
            ->where('required', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        if ($serviceEquipment) {
            foreach ($serviceEquipment as $se) {
                $equip = $se->equipment;
                if ($equip && $se->cost_included) {
                    $totalCost += ($equip->unit_cost ?? 0) * ($se->quantity ?? 1);
                }
            }
        }

        return $totalCost;
    }

    /**
     * Get all required addons
     */
    public function getRequiredAddons()
    {
        $addons = ServiceAddon::new()
            ->where('service_id', $this->id)
            ->where('required', 1)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        $results = [];
        if ($addons) {
            foreach ($addons as $addon) {
                $results[] = $addon->addonService;
            }
        }

        return $results;
    }

    /**
     * Increment version for optimistic locking
     */
    public function incrementVersion()
    {
        $this->version = ($this->version ?? 1) + 1;
        return $this;
    }

    /**
     * Soft delete
     */
    public function softDelete()
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->update(['deleted_at']);
    }

    /**
     * Restore from soft delete
     */
    public function restore()
    {
        $this->deleted_at = null;
        return $this->update(['deleted_at']);
    }

    /**
     * Toggle active status
     */
    public function toggleActive()
    {
        $this->is_active = !$this->is_active;
        return $this->update(['is_active']);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured()
    {
        $this->is_featured = !$this->is_featured;
        return $this->update(['is_featured']);
    }

    /**
     * Get metadata value by key
     */
    public function getMetadataValue($key, $default = null)
    {
        $metadata = $this->metadata;

        if (!is_array($metadata)) {
            return $default;
        }

        return $metadata[$key] ?? $default;
    }
}
