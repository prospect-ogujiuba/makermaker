<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

/**
 * ServiceDeliverable Model
 * 
 * Represents deliverables that clients receive upon service completion
 * Table: wp_b2bcnc_service_deliverables
 */
class ServiceDeliverable extends Model
{
    protected $resource = 'b2bcnc_service_deliverables';
    
    protected $fillable = [
        'service_id',
        'deliverable_name',
        'deliverable_description',
        'deliverable_type',
        'is_included',
        'quantity',
        'unit_of_measure',
        'additional_cost',
        'delivery_timeframe',
        'sort_order'
    ];

    protected $casts = [
        'service_id' => 'integer',
        'is_included' => 'boolean',
        'quantity' => 'integer',
        'additional_cost' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Relationship to the parent service
     * wp_b2bcnc_service_deliverables.service_id -> wp_b2bcnc_services.id
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    // ==========================================
    // QUERY SCOPES
    // ==========================================

    /**
     * Get only included deliverables (part of base service)
     */
    public function scopeIncluded($query)
    {
        return $query->where('is_included', 1);
    }

    /**
     * Get only additional deliverables (extra cost)
     */
    public function scopeAdditional($query)
    {
        return $query->where('is_included', 0);
    }

    /**
     * Get deliverables by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('deliverable_type', $type);
    }

    /**
     * Get deliverables for a specific service
     */
    public function scopeForService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Order by sort order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'ASC')->orderBy('deliverable_name', 'ASC');
    }

    /**
     * Get deliverables with additional cost
     */
    public function scopeWithCost($query)
    {
        return $query->where('additional_cost', '>', 0);
    }

    /**
     * Search by deliverable name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('deliverable_name', 'LIKE', '%' . $name . '%');
    }

    // ==========================================
    // UTILITY METHODS
    // ==========================================

    /**
     * Get deliverable type display text
     */
    public function getDeliverableTypeText()
    {
        $types = [
            'equipment' => 'Equipment',
            'software' => 'Software',
            'documentation' => 'Documentation',
            'training' => 'Training',
            'access' => 'Access/Credentials',
            'support' => 'Support Package'
        ];
        
        return $types[$this->deliverable_type] ?? ucfirst(str_replace('_', ' ', $this->deliverable_type));
    }

    /**
     * Get formatted additional cost
     */
    public function getFormattedCost()
    {
        if (!$this->additional_cost || $this->additional_cost <= 0) {
            return $this->is_included ? 'Included' : 'No additional cost';
        }
        
        return '$' . number_format($this->additional_cost, 2);
    }

    /**
     * Get total cost (quantity × additional_cost)
     */
    public function getTotalCost()
    {
        if (!$this->additional_cost || $this->additional_cost <= 0) {
            return 0;
        }
        
        return $this->additional_cost * $this->quantity;
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedTotalCost()
    {
        $total = $this->getTotalCost();
        
        if ($total <= 0) {
            return $this->is_included ? 'Included' : 'No charge';
        }
        
        return '$' . number_format($total, 2);
    }

    /**
     * Get quantity with unit of measure
     */
    public function getQuantityWithUnit()
    {
        $quantity = $this->quantity ?: 1;
        $unit = $this->unit_of_measure ?: 'item';
        
        if ($quantity === 1) {
            return '1 ' . $unit;
        }
        
        // Handle pluralization
        $pluralUnit = $this->pluralizeUnit($unit);
        return $quantity . ' ' . $pluralUnit;
    }

    /**
     * Simple pluralization for common units
     */
    private function pluralizeUnit($unit)
    {
        $unit = strtolower($unit);
        
        $irregulars = [
            'license' => 'licenses',
            'access' => 'access',
            'training' => 'training',
            'support' => 'support'
        ];
        
        if (isset($irregulars[$unit])) {
            return $irregulars[$unit];
        }
        
        // Simple pluralization rules
        if (substr($unit, -1) === 's' || substr($unit, -1) === 'x') {
            return $unit . 'es';
        } elseif (substr($unit, -1) === 'y') {
            return substr($unit, 0, -1) . 'ies';
        } else {
            return $unit . 's';
        }
    }

    /**
     * Get delivery timeframe display text
     */
    public function getDeliveryTimeframeText()
    {
        if (empty($this->delivery_timeframe)) {
            return 'Upon service completion';
        }
        
        return $this->delivery_timeframe;
    }

    /**
     * Check if this is a premium deliverable (has additional cost)
     */
    public function isPremium()
    {
        return !$this->is_included && $this->additional_cost > 0;
    }

    /**
     * Check if this is a standard deliverable (included in base service)
     */
    public function isStandard()
    {
        return $this->is_included;
    }

    /**
     * Get status display text
     */
    public function getStatusText()
    {
        if ($this->is_included) {
            return 'Standard (Included)';
        } elseif ($this->additional_cost > 0) {
            return 'Premium (Additional Cost)';
        } else {
            return 'Optional (No Charge)';
        }
    }

    /**
     * Get display name with quantity and type
     */
    public function getDisplayNameWithDetails()
    {
        return $this->deliverable_name . ' (' . $this->getQuantityWithUnit() . ', ' . $this->getDeliverableTypeText() . ')';
    }

    /**
     * Check if this deliverable can be deleted safely
     */
    public function canBeDeleted()
    {
        // Add business logic if certain deliverables should be protected
        // For example, required deliverables for certain service types
        return true;
    }

    /**
     * Get the next sort order value for a service
     */
    public static function getNextSortOrder($serviceId)
    {
        $maxOrder = static::where('service_id', $serviceId)->max('sort_order');
        return ($maxOrder ? $maxOrder + 1 : 1);
    }

    /**
     * Check if deliverable value is reasonable
     */
    public function hasReasonableValues()
    {
        $issues = [];
        
        // Check quantity
        if ($this->quantity <= 0) {
            $issues[] = 'Quantity must be greater than 0';
        }
        
        // Check cost logic
        if ($this->is_included && $this->additional_cost > 0) {
            $issues[] = 'Included deliverables should not have additional cost';
        }
        
        // Check very high costs
        if ($this->additional_cost > 10000) {
            $issues[] = 'Additional cost seems unusually high';
        }
        
        // Check quantity vs cost for certain types
        if ($this->deliverable_type === 'training' && $this->quantity > 100) {
            $issues[] = 'Training quantity seems unusually high';
        }
        
        return empty($issues) ? true : $issues;
    }

    /**
     * Get estimated delivery date based on timeframe
     */
    public function getEstimatedDeliveryDate($serviceStartDate = null)
    {
        if (!$serviceStartDate) {
            $serviceStartDate = new \DateTime();
        } elseif (is_string($serviceStartDate)) {
            $serviceStartDate = new \DateTime($serviceStartDate);
        }
        
        $timeframe = strtolower($this->delivery_timeframe ?: '');
        
        // Parse common timeframe patterns
        if (preg_match('/(\d+)\s*(day|week|month)s?/', $timeframe, $matches)) {
            $amount = (int)$matches[1];
            $unit = $matches[2];
            
            $interval = new \DateInterval('P' . $amount . strtoupper(substr($unit, 0, 1)));
            $deliveryDate = clone $serviceStartDate;
            $deliveryDate->add($interval);
            
            return $deliveryDate;
        }
        
        // Default: same day as service completion
        return $serviceStartDate;
    }

    /**
     * Auto-set sort order before saving if not provided
     */
    protected function beforeSave()
    {
        if (empty($this->sort_order) && $this->service_id) {
            $this->sort_order = static::getNextSortOrder($this->service_id);
        }
        
        // Default quantity if not set
        if (empty($this->quantity)) {
            $this->quantity = 1;
        }
        
        parent::beforeSave();
    }
}