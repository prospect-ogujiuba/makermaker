<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Service extends Model
{
    protected $resource = 'b2bcnc_services';
    
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'short_description',
        'long_description',
        'service_type',
        'delivery_method',
        'pricing_model',
        'base_price',
        'hourly_rate',
        'estimated_duration_hours',
        'complexity_level',
        'requires_site_visit',
        'supports_remote_delivery',
        'is_active',
        'is_featured',
        'requires_assessment',
        'min_notice_days',
        'meta_title',
        'meta_description',
        'featured_image'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'estimated_duration_hours' => 'integer',
        'min_notice_days' => 'integer',
        'requires_site_visit' => 'boolean',
        'supports_remote_delivery' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'requires_assessment' => 'boolean'
    ];

    // ==========================================
    // RELATIONSHIPS - Based on Database Schema
    // ==========================================

    /**
     * Relationship to service category
     * wp_b2bcnc_services.category_id -> wp_b2bcnc_service_categories.id
     */
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    /**
     * Service pricing tiers
     * wp_b2bcnc_service_pricing_tiers.service_id -> wp_b2bcnc_services.id
     */
    public function pricingTiers()
    {
        return $this->hasMany(ServicePricingTier::class, 'service_id');
    }

    /**
     * Service addons
     * wp_b2bcnc_service_addons.service_id -> wp_b2bcnc_services.id
     */
    public function addons()
    {
        return $this->hasMany(ServiceAddon::class, 'service_id');
    }

    /**
     * Service equipment
     * wp_b2bcnc_service_equipment.service_id -> wp_b2bcnc_services.id
     */
    public function equipment()
    {
        return $this->hasMany(ServiceEquipment::class, 'service_id');
    }

    /**
     * Service coverage areas
     * wp_b2bcnc_service_coverage_areas.service_id -> wp_b2bcnc_services.id
     */
    public function coverageAreas()
    {
        return $this->hasMany(ServiceCoverageArea::class, 'service_id');
    }

    /**
     * Service dependencies - services this service depends on
     * wp_b2bcnc_service_dependencies.primary_service_id -> wp_b2bcnc_services.id
     */
    public function dependencies()
    {
        return $this->hasMany(ServiceDependency::class, 'primary_service_id');
    }

    /**
     * Services that depend on this service
     * wp_b2bcnc_service_dependencies.dependent_service_id -> wp_b2bcnc_services.id
     */
    public function dependents()
    {
        return $this->hasMany(ServiceDependency::class, 'dependent_service_id');
    }

    /**
     * Service prerequisites
     * wp_b2bcnc_service_prerequisites.service_id -> wp_b2bcnc_services.id
     */
    public function prerequisites()
    {
        return $this->hasMany(ServicePrerequisite::class, 'service_id');
    }

    /**
     * Service deliverables
     * wp_b2bcnc_service_deliverables.service_id -> wp_b2bcnc_services.id
     */
    public function deliverables()
    {
        return $this->hasMany(ServiceDeliverable::class, 'service_id');
    }

    /**
     * Service attributes
     * wp_b2bcnc_service_attributes.service_id -> wp_b2bcnc_services.id
     */
    public function attributes()
    {
        return $this->hasMany(ServiceAttribute::class, 'service_id');
    }

    /**
     * Bundle relationships - bundles that include this service
     * Many-to-many through wp_b2bcnc_bundle_services
     */
    public function bundles()
    {
        return $this->belongsToMany(
            ServiceBundle::class,
            'wp_b2bcnc_bundle_services',
            'service_id',
            'bundle_id'
        );
    }

    /**
     * If this service IS a bundle, get the services it includes
     * This assumes bundles are also stored in the services table with a special type
     */
    public function bundleServices()
    {
        return $this->belongsToMany(
            self::class,
            'wp_b2bcnc_bundle_services',
            'bundle_id',
            'service_id'
        );
    }

    // ==========================================
    // SCOPES - Query filtering methods
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeByServiceType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeByDeliveryMethod($query, $method)
    {
        return $query->where('delivery_method', $method);
    }

    public function scopeByPricingModel($query, $model)
    {
        return $query->where('pricing_model', $model);
    }

    public function scopeByComplexity($query, $level)
    {
        return $query->where('complexity_level', $level);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeRequiresSiteVisit($query)
    {
        return $query->where('requires_site_visit', 1);
    }

    public function scopeSupportsRemote($query)
    {
        return $query->where('supports_remote_delivery', 1);
    }

    public function scopeRequiresAssessment($query)
    {
        return $query->where('requires_assessment', 1);
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('base_price', '>=', $min);
        }
        if ($max) {
            $query->where('base_price', '<=', $max);
        }
        return $query;
    }

    public function scopeHourlyRateRange($query, $min = null, $max = null)
    {
        if ($min) {
            $query->where('hourly_rate', '>=', $min);
        }
        if ($max) {
            $query->where('hourly_rate', '<=', $max);
        }
        return $query;
    }

    // Search scope using fulltext index
    public function scopeSearch($query, $term)
    {
        return $query->whereRaw("MATCH(name, short_description, long_description) AGAINST(? IN BOOLEAN MODE)", [$term]);
    }

    // ==========================================
    // ACCESSORS - Formatted display methods
    // ==========================================

    // Accessor for formatted base price
    public function getFormattedBasePriceAttribute()
    {
        return $this->base_price ? '$' . number_format($this->base_price, 2) : null;
    }

    // Accessor for formatted hourly rate
    public function getFormattedHourlyRateAttribute()
    {
        return $this->hourly_rate ? '$' . number_format($this->hourly_rate, 2) . '/hr' : null;
    }

    // Get service type display name
    public function getServiceTypeDisplayAttribute()
    {
        $types = [
            'installation' => 'Installation',
            'maintenance' => 'Maintenance',
            'hosting' => 'Hosting',
            'consulting' => 'Consulting',
            'support' => 'Support',
            'hybrid' => 'Hybrid'
        ];
        return $types[$this->service_type] ?? ucfirst($this->service_type);
    }

    // Get delivery method display name
    public function getDeliveryMethodDisplayAttribute()
    {
        $methods = [
            'onsite' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            'hosted' => 'Hosted'
        ];
        return $methods[$this->delivery_method] ?? ucfirst($this->delivery_method);
    }

    // Get pricing model display name
    public function getPricingModelDisplayAttribute()
    {
        $models = [
            'fixed' => 'Fixed Price',
            'hourly' => 'Hourly Rate',
            'monthly' => 'Monthly',
            'project' => 'Project-based',
            'tiered' => 'Tiered',
            'custom' => 'Custom Quote'
        ];
        return $models[$this->pricing_model] ?? ucfirst($this->pricing_model);
    }

    // Get complexity level display name
    public function getComplexityLevelDisplayAttribute()
    {
        $levels = [
            'basic' => 'Basic',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert'
        ];
        return $levels[$this->complexity_level] ?? ucfirst($this->complexity_level);
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    // Method to get estimated duration in human readable format
    public function getEstimatedDurationFormatted()
    {
        if (!$this->estimated_duration_hours) {
            return null;
        }
        
        $hours = $this->estimated_duration_hours;
        $days = floor($hours / 8);
        $remainingHours = $hours % 8;
        
        if ($days > 0 && $remainingHours > 0) {
            return "{$days} day" . ($days > 1 ? 's' : '') . ", {$remainingHours} hour" . ($remainingHours > 1 ? 's' : '');
        } elseif ($days > 0) {
            return "{$days} day" . ($days > 1 ? 's' : '');
        } else {
            return "{$hours} hour" . ($hours > 1 ? 's' : '');
        }
    }

    // Check if service supports remote delivery
    public function canBeDeliveredRemotely()
    {
        return $this->supports_remote_delivery || in_array($this->delivery_method, ['remote', 'hybrid', 'hosted']);
    }

    // Check if service requires on-site visit
    public function requiresOnsiteVisit()
    {
        return $this->requires_site_visit || in_array($this->delivery_method, ['onsite', 'hybrid']);
    }

    // Get all active pricing tiers ordered by min quantity
    public function getActivePricingTiers()
    {
        return $this->pricingTiers()
            ->where('is_active', 1)
            ->orderBy('min_quantity', 'ASC')
            ->get();
    }

    // Get all active addons ordered by sort order
    public function getActiveAddons()
    {
        return $this->addons()
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();
    }

    // Get required equipment
    public function getRequiredEquipment()
    {
        return $this->equipment()
            ->where('is_customer_provided', 0)
            ->orderBy('equipment_type', 'ASC')
            ->get();
    }

    // Get customer-provided equipment
    public function getCustomerEquipment()
    {
        return $this->equipment()
            ->where('is_customer_provided', 1)
            ->orderBy('equipment_type', 'ASC')
            ->get();
    }

    // Get active coverage areas
    public function getActiveCoverageAreas()
    {
        return $this->coverageAreas()
            ->where('is_active', 1)
            ->orderBy('area_type', 'ASC')
            ->orderBy('area_value', 'ASC')
            ->get();
    }

    // Get required dependencies
    public function getRequiredDependencies()
    {
        return $this->dependencies()
            ->where('dependency_type', 'required')
            ->get();
    }

    // Get required prerequisites
    public function getRequiredPrerequisites()
    {
        return $this->prerequisites()
            ->where('is_required', 1)
            ->get();
    }

    // Get included deliverables
    public function getIncludedDeliverables()
    {
        return $this->deliverables()
            ->where('is_included', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();
    }

    // Get configurable attributes
    public function getConfigurableAttributes()
    {
        return $this->attributes()
            ->where('is_configurable', 1)
            ->orderBy('display_order', 'ASC')
            ->get();
    }

    // Calculate total cost including addons
    // public function getTotalCostWithAddons($selectedAddons = [])
    // {
    //     $total = $this->base_price ?? 0;
        
    //     if (!empty($selectedAddons)) {
    //         $addons = $this->addons()->whereIn('id', $selectedAddons)->get();
    //         foreach ($addons as $addon) {
    //             $total += $addon->price;
    //         }
    //     }
        
    //     return $total;
    // }

    // Check if service is available in coverage area
    // public function isAvailableInArea($areaType, $areaValue)
    // {
    //     return $this->coverageAreas()
    //         ->where('is_active', 1)
    //         ->where('area_type', $areaType)
    //         ->where('area_value', 'LIKE', "%{$areaValue}%")
    //         ->exists();
    // }

    // Get service status badge class for UI
    public function getStatusBadgeClass()
    {
        if (!$this->is_active) {
            return 'badge-inactive';
        }
        if ($this->is_featured) {
            return 'badge-featured';
        }
        return 'badge-active';
    }

    // Get service status display text
    public function getStatusDisplayText()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        if ($this->is_featured) {
            return 'Featured';
        }
        return 'Active';
    }

    // ==========================================
    // SAVE METHODS FOR RELATED DATA
    // ==========================================

    /**
     * Save pricing tiers data
     */
    public function savePricingTiers($tiersData)
    {
        if (!is_array($tiersData)) {
            return;
        }

        // Delete existing tiers
        $this->pricingTiers()->delete();

        // Save new tiers
        foreach ($tiersData as $tierData) {
            if (empty($tierData['tier_name']) || empty($tierData['price'])) {
                continue;
            }
            
            $tierData['service_id'] = $this->id;
            ServicePricingTier::new()->create($tierData);
        }
    }

    /**
     * Save service addons data
     */
    public function saveAddons($addonsData)
    {
        if (!is_array($addonsData)) {
            return;
        }

        // Delete existing addons
        $this->addons()->delete();

        // Save new addons
        foreach ($addonsData as $addonData) {
            if (empty($addonData['addon_name']) || empty($addonData['price'])) {
                continue;
            }
            
            $addonData['service_id'] = $this->id;
            ServiceAddon::new()->create($addonData);
        }
    }

    /**
     * Save service equipment data
     */
    public function saveEquipment($equipmentData)
    {
        if (!is_array($equipmentData)) {
            return;
        }

        // Delete existing equipment
        $this->equipment()->delete();

        // Save new equipment
        foreach ($equipmentData as $equip) {
            if (empty($equip['equipment_name'])) {
                continue;
            }
            
            $equip['service_id'] = $this->id;
            ServiceEquipment::new()->create($equip);
        }
    }

    /**
     * Save coverage areas data
     */
    public function saveCoverageAreas($areasData)
    {
        if (!is_array($areasData)) {
            return;
        }

        // Delete existing coverage areas
        $this->coverageAreas()->delete();

        // Save new coverage areas
        foreach ($areasData as $area) {
            if (empty($area['area_type']) || empty($area['area_value'])) {
                continue;
            }
            
            $area['service_id'] = $this->id;
            ServiceCoverageArea::new()->create($area);
        }
    }

    /**
     * Save service dependencies
     */
    public function saveDependencies($dependenciesData)
    {
        if (!is_array($dependenciesData)) {
            return;
        }

        // Delete existing dependencies
        $this->dependencies()->delete();

        // Save new dependencies
        foreach ($dependenciesData as $dep) {
            if (empty($dep['dependent_service_id'])) {
                continue;
            }
            
            $dep['primary_service_id'] = $this->id;
            ServiceDependency::new()->create($dep);
        }
    }

    /**
     * Save service prerequisites
     */
    public function savePrerequisites($prerequisitesData)
    {
        if (!is_array($prerequisitesData)) {
            return;
        }

        // Delete existing prerequisites
        $this->prerequisites()->delete();

        // Save new prerequisites
        foreach ($prerequisitesData as $prereq) {
            if (empty($prereq['prerequisite_description'])) {
                continue;
            }
            
            $prereq['service_id'] = $this->id;
            ServicePrerequisite::new()->create($prereq);
        }
    }

    /**
     * Save service deliverables
     */
    public function saveDeliverables($deliverablesData)
    {
        if (!is_array($deliverablesData)) {
            return;
        }

        // Delete existing deliverables
        $this->deliverables()->delete();

        // Save new deliverables
        foreach ($deliverablesData as $deliverable) {
            if (empty($deliverable['deliverable_name'])) {
                continue;
            }
            
            $deliverable['service_id'] = $this->id;
            ServiceDeliverable::new()->create($deliverable);
        }
    }

    /**
     * Save service attributes
     */
    public function saveAttributes($attributesData)
    {
        if (!is_array($attributesData)) {
            return;
        }

        // Delete existing attributes
        $this->attributes()->delete();

        // Save new attributes
        foreach ($attributesData as $attr) {
            if (empty($attr['attribute_name'])) {
                continue;
            }
            
            $attr['service_id'] = $this->id;
            ServiceAttribute::new()->create($attr);
        }
    }

    /**
     * Save all related data at once
     */
    public function saveRelatedData($data)
    {
        if (isset($data['pricing_tiers'])) {
            $this->savePricingTiers($data['pricing_tiers']);
        }

        if (isset($data['service_addons'])) {
            $this->saveAddons($data['service_addons']);
        }

        if (isset($data['service_equipment'])) {
            $this->saveEquipment($data['service_equipment']);
        }

        if (isset($data['service_coverage_areas'])) {
            $this->saveCoverageAreas($data['service_coverage_areas']);
        }

        if (isset($data['service_dependencies'])) {
            $this->saveDependencies($data['service_dependencies']);
        }

        if (isset($data['service_prerequisites'])) {
            $this->savePrerequisites($data['service_prerequisites']);
        }

        if (isset($data['service_deliverables'])) {
            $this->saveDeliverables($data['service_deliverables']);
        }

        if (isset($data['service_attributes'])) {
            $this->saveAttributes($data['service_attributes']);
        }

        // Handle bundle relationships
        if (isset($data['bundle_memberships']) && is_array($data['bundle_memberships'])) {
            $this->bundles()->sync($data['bundle_memberships']);
        }

        if (isset($data['bundle_services']) && is_array($data['bundle_services'])) {
            $this->bundleServices()->sync($data['bundle_services']);
        }
    }
}