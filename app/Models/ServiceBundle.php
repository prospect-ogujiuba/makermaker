<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceBundle Model
 * 
 * Represents service bundles/packages that group multiple services together
 * Table: wp_b2bcnc_service_bundles
 */
class ServiceBundle extends Model
{
    protected $resource = 'b2bcnc_service_bundles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'bundle_type',
        'base_price',
        'discount_percentage',
        'is_active',
        'min_commitment_months'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'min_commitment_months' => 'integer'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Services included in this bundle
     * Many-to-many through wp_b2bcnc_bundle_services
     */
    public function services()
    {
        return $this->belongsToMany(
            Service::class,
            'wp_b2bcnc_bundle_services',
            'bundle_id',
            'service_id'
        );
    }

    /**
     * Bundle services with pivot data (quantity, optional, sort_order)
     * wp_b2bcnc_bundle_services.bundle_id -> wp_b2bcnc_service_bundles.id
     */
    public function bundleServices()
    {
        return $this->hasMany(BundleService::class, 'bundle_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only active bundles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Get bundles by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('bundle_type', $type);
    }

    /**
     * Get bundles with minimum commitment
     */
    public function scopeWithCommitment($query)
    {
        return $query->where('min_commitment_months', '>', 0);
    }

    /**
     * Get bundles without commitment
     */
    public function scopeNoCommitment($query)
    {
        return $query->where('min_commitment_months', '<=', 0)->orWhereNull('min_commitment_months');
    }

    /**
     * Order by name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name', 'ASC');
    }

    /**
     * Get by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Price range filter
     */
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

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get formatted base price
     */
    public function getFormattedPrice()
    {
        return $this->base_price ? '$' . number_format($this->base_price, 2) : 'Custom pricing';
    }

    /**
     * Get bundle type display text
     */
    public function getBundleTypeText()
    {
        $types = [
            'package' => 'Service Package',
            'addon_group' => 'Addon Group',
            'maintenance_plan' => 'Maintenance Plan',
            'enterprise' => 'Enterprise Solution'
        ];

        return $types[$this->bundle_type] ?? ucfirst(str_replace('_', ' ', $this->bundle_type));
    }

    /**
     * Generate slug from name if not provided
     */
    public function generateSlug()
    {
        if (!empty($this->slug)) {
            return $this->slug;
        }

        $slug = strtolower(trim($this->name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Get services count in this bundle
     */
    public function getServicesCount()
    {
        return $this->services()->count();
    }

    /**
     * Get active services count in this bundle
     */
    public function getActiveServicesCount()
    {
        return $this->services()->active()->count();
    }

    /**
     * Get required services in this bundle
     */
    public function getRequiredServices()
    {
        return $this->bundleServices()->where('is_optional', 0)->get();
    }

    /**
     * Get optional services in this bundle
     */
    public function getOptionalServices()
    {
        return $this->bundleServices()->where('is_optional', 1)->get();
    }

    /**
     * Calculate total value of individual services
     */
    public function getIndividualServicesValue()
    {
        $total = 0;
        foreach ($this->services as $service) {
            if ($service->base_price) {
                // Get quantity from pivot if available
                $quantity = $service->pivot ? $service->pivot->quantity : 1;
                $total += $service->base_price * $quantity;
            }
        }
        return $total;
    }

    /**
     * Calculate actual discount amount
     */
    public function getDiscountAmount()
    {
        if (!$this->discount_percentage || !$this->base_price) {
            return 0;
        }

        $individualValue = $this->getIndividualServicesValue();
        if ($individualValue > 0) {
            return $individualValue - $this->base_price;
        }

        return ($this->base_price * $this->discount_percentage) / 100;
    }

    /**
     * Get actual discount percentage based on individual service prices
     */
    public function getActualDiscountPercentage()
    {
        $individualValue = $this->getIndividualServicesValue();
        if ($individualValue <= 0 || !$this->base_price) {
            return 0;
        }

        return round((($individualValue - $this->base_price) / $individualValue) * 100, 2);
    }

    /**
     * Check if bundle has any services
     */
    public function hasServices()
    {
        return $this->getServicesCount() > 0;
    }

    /**
     * Check if bundle is available (active and has active services)
     */
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        // Must have at least one active service
        return $this->getActiveServicesCount() > 0;
    }

    /**
     * Get commitment text
     */
    public function getCommitmentText()
    {
        if (!$this->min_commitment_months || $this->min_commitment_months <= 0) {
            return 'No commitment';
        }

        if ($this->min_commitment_months === 1) {
            return '1 month minimum';
        }

        if ($this->min_commitment_months === 12) {
            return '1 year minimum';
        }

        return $this->min_commitment_months . ' months minimum';
    }

    /**
     * Get display name with type
     */
    public function getDisplayNameWithType()
    {
        return $this->name . ' (' . $this->getBundleTypeText() . ')';
    }

    /**
     * Check if this bundle can be deleted safely
     */
    public function canBeDeleted()
    {
        // Add any business logic for when bundles can't be deleted
        // For example, if they're part of active orders/quotes
        return true;
    }

    // ==========================================
    // RELATIONSHIP DATA MANAGEMENT
    // ==========================================

    /**
     * Sync services with this bundle
     */
    public function syncServices($serviceIds, $pivotData = [])
    {
        if (!is_array($serviceIds)) {
            return;
        }

        $syncData = [];
        foreach ($serviceIds as $serviceId) {
            $syncData[$serviceId] = $pivotData[$serviceId] ?? [
                'quantity' => 1,
                'is_optional' => 0,
                'sort_order' => 0
            ];
        }

        $this->services()->sync($syncData);
    }

    /**
     * Add service to bundle
     */
    public function addService($serviceId, $quantity = 1, $isOptional = false, $sortOrder = 0)
    {
        $this->services()->attach($serviceId, [
            'quantity' => $quantity,
            'is_optional' => $isOptional ? 1 : 0,
            'sort_order' => $sortOrder
        ]);
    }

    /**
     * Remove service from bundle
     */
    public function removeService($serviceId)
    {
        $this->services()->detach($serviceId);
    }

    /**
     * Update service in bundle
     */
    public function updateService($serviceId, $quantity = 1, $isOptional = false, $sortOrder = 0)
    {
        $this->services()->updateExistingPivot($serviceId, [
            'quantity' => $quantity,
            'is_optional' => $isOptional ? 1 : 0,
            'sort_order' => $sortOrder
        ]);
    }

    /**
     * Auto-generate slug before saving if not provided
     */
    protected function beforeSave()
    {
        if (empty($this->slug)) {
            $this->slug = $this->generateSlug();
        }

        parent::beforeSave();
    }
}
