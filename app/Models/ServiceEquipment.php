<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use MakerMaker\Helpers\ServiceCatalogHelper;

class ServiceEquipment extends Model
{
    protected $resource = 'srvc_service_equipment';

    protected $fillable = [
        'service_id',
        'equipment_id',
        'required',
        'quantity',
        'quantity_unit',
        'cost_included'
    ];
    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $with = [
        'service',
        'equipment'
    ];

    /** ServiceEquipment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceEquipment belongs to a Equipment */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
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

    /**
     * Get all active service equipment associations
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get equipment for a specific service
     */
    public static function getForService($serviceId, $requiredOnly = false)
    {
        $query = static::new()
            ->where('service_id', $serviceId)
            ->where('deleted_at', 'IS', null);

        if ($requiredOnly) {
            $query->where('required', 1);
        }

        return $query->findAll();
    }

    /**
     * Get services using specific equipment
     */
    public static function getServicesUsingEquipment($equipmentId, $requiredOnly = false)
    {
        $query = static::new()
            ->where('equipment_id', $equipmentId)
            ->where('deleted_at', 'IS', null);

        if ($requiredOnly) {
            $query->where('required', 1);
        }

        return $query->findAll();
    }

    /**
     * Get required equipment for service
     */
    public static function getRequiredForService($serviceId)
    {
        return static::getForService($serviceId, true)->get();
    }

    /**
     * Get optional equipment for service
     */
    public static function getOptionalForService($serviceId)
    {
        return static::new()
            ->where('service_id', $serviceId)
            ->where('required', 0)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();
    }

    /**
     * Check if equipment is required for service
     */
    public function isRequired()
    {
        return (bool) $this->required;
    }

    /**
     * Check if cost is included in service price
     */
    public function isCostIncluded()
    {
        return (bool) $this->cost_included;
    }

    /**
     * Calculate total equipment cost
     */
    public function getTotalCost()
    {
        $equipment = $this->equipment;

        if (!$equipment || $equipment->unit_cost === null) {
            return 0;
        }

        return $equipment->unit_cost * $this->quantity;
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedTotalCost($currency = 'CAD')
    {
        return ServiceCatalogHelper::formatCurrency($this->getTotalCost(), $currency);
    }

    /**
     * Get cost impact (0 if included in price, otherwise total cost)
     */
    public function getCostImpact()
    {
        return $this->cost_included ? 0 : $this->getTotalCost();
    }

    /**
     * Get quantity with unit
     */
    public function getFormattedQuantity()
    {
        return number_format($this->quantity, 3) . ' ' . $this->quantity_unit;
    }

    /**
     * Update quantity
     */
    public function updateQuantity($newQuantity)
    {
        if ($newQuantity <= 0 || $newQuantity > 10000) {
            throw new \InvalidArgumentException("Quantity must be between 0 and 10000");
        }

        $this->quantity = $newQuantity;
        return $this->update(['quantity']);
    }

    /**
     * Toggle required status
     */
    public function toggleRequired()
    {
        $this->required = !$this->required;
        return $this->update(['required']);
    }

    /**
     * Toggle cost included status
     */
    public function toggleCostIncluded()
    {
        $this->cost_included = !$this->cost_included;
        return $this->update(['cost_included']);
    }

    /**
     * Get all required equipment costs for a service
     */
    public static function getRequiredCostsForService($serviceId)
    {
        $items = static::getRequiredForService($serviceId);
        $totalCost = 0;
        $includedCost = 0;
        $additionalCost = 0;

        if ($items) {
            foreach ($items as $item) {
                $cost = $item->getTotalCost();
                $totalCost += $cost;

                if ($item->cost_included) {
                    $includedCost += $cost;
                } else {
                    $additionalCost += $cost;
                }
            }
        }

        return [
            'total_cost' => $totalCost,
            'included_cost' => $includedCost,
            'additional_cost' => $additionalCost
        ];
    }

    /**
     * Get all equipment costs for a service
     */
    public static function getAllCostsForService($serviceId)
    {
        $items = static::getForService($serviceId)->get();
        $totalCost = 0;
        $requiredCost = 0;
        $optionalCost = 0;
        $includedCost = 0;
        $additionalCost = 0;

        if ($items) {
            foreach ($items as $item) {
                $cost = $item->getTotalCost();
                $totalCost += $cost;

                if ($item->required) {
                    $requiredCost += $cost;
                } else {
                    $optionalCost += $cost;
                }

                if ($item->cost_included) {
                    $includedCost += $cost;
                } else {
                    $additionalCost += $cost;
                }
            }
        }

        return [
            'total_cost' => $totalCost,
            'required_cost' => $requiredCost,
            'optional_cost' => $optionalCost,
            'included_cost' => $includedCost,
            'additional_cost' => $additionalCost
        ];
    }

    /**
     * Check if association already exists
     */
    public static function exists($serviceId, $equipmentId)
    {
        $existing = static::new()
            ->where('service_id', $serviceId)
            ->where('equipment_id', $equipmentId)
            ->where('deleted_at', 'IS', null)
            ->first();

        return $existing !== null;
    }

    /**
     * Validate quantity
     */
    public function validateQuantity()
    {
        $errors = [];

        if ($this->quantity <= 0) {
            $errors[] = "Quantity must be greater than 0";
        }

        if ($this->quantity > 10000) {
            $errors[] = "Quantity cannot exceed 10000";
        }

        return empty($errors) ? true : $errors;
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
     * Get requirement badge
     */
    public function getRequirementBadge()
    {
        if ($this->required) {
            return '<span class="req-badge required">Required</span>';
        }

        return '<span class="req-badge optional">Optional</span>';
    }

    /**
     * Get cost inclusion badge
     */
    public function getCostBadge()
    {
        if ($this->cost_included) {
            return '<span class="cost-badge included">Cost Included</span>';
        }

        return '<span class="cost-badge additional">Additional Cost</span>';
    }
}
