<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceAddon Model
 * 
 * Represents service add-ons, upgrades, and additional features
 * Table: wp_b2bcnc_service_addons
 */
class ServiceAddon extends Model
{
    protected $resource = 'b2bcnc_service_addons';
    
    protected $fillable = [
        'service_id',
        'addon_name',
        'addon_description',
        'addon_type',
        'price',
        'is_recurring',
        'billing_frequency',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'price' => 'decimal:2',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relationship to the parent service
     * wp_b2bcnc_service_addons.service_id -> wp_b2bcnc_services.id
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only active addons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /**
     * Get addons by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('addon_type', $type);
    }

    /**
     * Get recurring addons only
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', 1);
    }

    /**
     * Get one-time addons only
     */
    public function scopeOneTime($query)
    {
        return $query->where('is_recurring', 0);
    }

    /**
     * Order by sort order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'ASC')->orderBy('addon_name', 'ASC');
    }

    /**
     * Get addons for a specific service
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get formatted price with currency
     */
    public function getFormattedPrice()
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get billing frequency display text
     */
    public function getBillingFrequencyText()
    {
        if (!$this->is_recurring) {
            return 'One-time';
        }

        $frequencies = [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly', 
            'annually' => 'Annually'
        ];

        return $frequencies[$this->billing_frequency] ?? 'Unknown';
    }

    /**
     * Get addon type display text
     */
    public function getAddonTypeText()
    {
        $types = [
            'upgrade' => 'Upgrade',
            'additional' => 'Additional Feature',
            'extended_warranty' => 'Extended Warranty',
            'training' => 'Training',
            'support' => 'Support Package'
        ];

        return $types[$this->addon_type] ?? ucfirst($this->addon_type);
    }

    /**
     * Check if addon is available (active and service is active)
     */
    public function isAvailable()
    {
        return $this->is_active && $this->service && $this->service->is_active;
    }

    /**
     * Get the full price text including billing frequency
     */
    public function getFullPriceText()
    {
        $price = $this->getFormattedPrice();
        
        if ($this->is_recurring) {
            $frequency = strtolower($this->getBillingFrequencyText());
            return "{$price} {$frequency}";
        }
        
        return "{$price} one-time";
    }
}