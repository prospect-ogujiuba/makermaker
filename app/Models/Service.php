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
        'sku',
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
     * Service addons
     * wp_b2bcnc_service_addons.service_id -> wp_b2bcnc_services.id
     */
    public function addons()
    {
        return $this->hasMany(ServiceAddons::class, 'service_id');
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
     * Service coverage areas
     * wp_b2bcnc_service_coverage_areas.service_id -> wp_b2bcnc_services.id
     */
    public function coverageAreas()
    {
        return $this->hasMany(ServiceCoverageArea::class, 'service_id');
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
     * Service dependencies - services that this service depends on
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
        return $this->hasMany(ServiceDeliverables::class, 'service_id');
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
            ServiceBundles::class,
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
    // NEW LOOKUP TABLE RELATIONSHIPS
    // ==========================================

    /**
     * Service Type lookup (if we want to reference the lookup table instead of enum)
     * Note: This would require updating the services table to have service_type_id
     * For now, we keep the enum but add this for future migration
     */
    public function serviceTypeLookup()
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    /**
     * Delivery Method lookup (if we want to reference the lookup table instead of enum)
     * Note: This would require updating the services table to have delivery_method_id
     * For now, we keep the enum but add this for future migration
     */
    public function deliveryMethodLookup()
    {
        return $this->belongsTo(ServiceDeliveryMethod::class, 'delivery_method_id');
    }

    /**
     * Pricing Model lookup (if we want to reference the lookup table instead of enum)
     * Note: This would require updating the services table to have pricing_model_id
     * For now, we keep the enum but add this for future migration
     */
    public function pricingModelLookup()
    {
        return $this->belongsTo(ServicePricingModel::class, 'pricing_model_id');
    }

    /**
     * Complexity Level lookup (if we want to reference the lookup table instead of enum)
     * Note: This would require updating the services table to have complexity_level_id
     * For now, we keep the enum but add this for future migration
     */
    public function complexityLookup()
    {
        return $this->belongsTo(ServiceComplexity::class, 'complexity_level_id');
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

    public function scopeBySku($query, $sku)
    {
        return $query->where('sku', $sku);
    }

    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('base_price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('base_price', '<=', $max);
        }
        return $query;
    }

    public function scopeRequiresVisit($query)
    {
        return $query->where('requires_site_visit', 1);
    }

    public function scopeSupportsRemote($query)
    {
        return $query->where('supports_remote_delivery', 1);
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get formatted base price
     */
    public function getFormattedPrice()
    {
        return $this->base_price ? '$' . number_format($this->base_price, 2) : 'Contact for pricing';
    }

    /**
     * Get formatted hourly rate
     */
    public function getFormattedHourlyRate()
    {
        return $this->hourly_rate ? '$' . number_format($this->hourly_rate, 2) . '/hour' : null;
    }

    /**
     * Get service type display text
     */
    public function getServiceTypeText()
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

    /**
     * Get delivery method display text
     */
    public function getDeliveryMethodText()
    {
        $methods = [
            'onsite' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            'hosted' => 'Hosted'
        ];
        
        return $methods[$this->delivery_method] ?? ucfirst($this->delivery_method);
    }

    /**
     * Get pricing model display text
     */
    public function getPricingModelText()
    {
        $models = [
            'fixed' => 'Fixed Price',
            'hourly' => 'Hourly Rate',
            'monthly' => 'Monthly Subscription',
            'project' => 'Project-based',
            'tiered' => 'Tiered Pricing',
            'custom' => 'Custom Quote'
        ];
        
        return $models[$this->pricing_model] ?? ucfirst($this->pricing_model);
    }

    /**
     * Get complexity level display text
     */
    public function getComplexityText()
    {
        $levels = [
            'basic' => 'Basic',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert'
        ];
        
        return $levels[$this->complexity_level] ?? ucfirst($this->complexity_level);
    }

    /**
     * Generate SKU if not provided
     */
    public function generateSku()
    {
        if (!empty($this->sku)) {
            return $this->sku;
        }

        // Generate SKU based on category and service name
        $categoryCode = $this->category ? strtoupper(substr($this->category->slug, 0, 3)) : 'SVC';
        $serviceCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $this->name), 0, 6));
        $timestamp = substr(time(), -4);
        
        return $categoryCode . '-' . $serviceCode . '-' . $timestamp;
    }

    /**
     * Check if service has active addons
     */
    public function hasActiveAddons()
    {
        return $this->addons()->active()->count() > 0;
    }

    /**
     * Get total active addons count
     */
    public function getActiveAddonsCount()
    {
        return $this->addons()->active()->count();
    }

    /**
     * Check if service is available (active and has no blocking dependencies)
     */
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        // Check if all required dependencies are met
        $requiredDeps = $this->dependencies()->where('dependency_type', 'required')->get();
        foreach ($requiredDeps as $dependency) {
            if (!$dependency->dependentService || !$dependency->dependentService->is_active) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get display name with SKU
     */
    public function getDisplayNameWithSku()
    {
        return $this->name . ' (' . $this->sku . ')';
    }

    // ==========================================
    // RELATIONSHIP DATA SAVING METHODS
    // ==========================================

    /**
     * Save service pricing tiers data
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
            ServiceAddons::new()->create($addonData);
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
            ServiceDeliverables::new()->create($deliverable);
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

    /**
     * Auto-generate SKU before saving if not provided
     */
    protected function beforeSave()
    {
        if (empty($this->sku)) {
            $this->sku = $this->generateSku();
        }
        
        parent::beforeSave();
    }
}