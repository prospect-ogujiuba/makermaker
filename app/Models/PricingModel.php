<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class PricingModel extends Model
{
    protected $resource = 'srvc_pricing_models';

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_time_based',
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
        'servicePrices'
    ];

    // Service prices using this pricing model
    public function servicePrices()
    {
        return $this->hasMany(\MakerMaker\Models\ServicePrice::class, 'pricing_model_id');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    /**
     * Get all active pricing models
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Find pricing model by code
     */
    public function findByCode($code)
    {
        return $this->where('code', $code)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get time-based pricing models
     */
    public function getTimeBased()
    {
        return $this->where('is_time_based', 1)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get non-time-based (fixed/unit-based) pricing models
     */
    public function getFixedBased()
    {
        return $this->where('is_time_based', 0)
            ->where('deleted_at', 'IS', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Check if this is a time-based pricing model
     */
    public function isTimeBased()
    {
        return (bool) $this->is_time_based;
    }

    /**
     * Check if this is a fixed/unit-based pricing model
     */
    public function isFixedBased()
    {
        return !$this->is_time_based;
    }

    /**
     * Count service prices using this pricing model
     */
    public function getPriceCount($currentOnly = true, $approvedOnly = true)
    {
        $prices = $this->servicePrices;

        if (!$prices) {
            return 0;
        }

        $count = 0;
        foreach ($prices as $price) {
            $matchesCurrent = !$currentOnly || $price->is_current == 1;
            $matchesApproved = !$approvedOnly || $price->approval_status === 'approved';
            $notDeleted = $price->deleted_at === null;

            if ($matchesCurrent && $matchesApproved && $notDeleted) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get all services using this pricing model
     */
    public function getServicesUsingModel($activeOnly = true)
    {
        $prices = $this->servicePrices;
        $serviceIds = [];
        $services = [];

        if (!$prices) {
            return [];
        }

        foreach ($prices as $price) {
            if (
                $price->is_current == 1 &&
                $price->approval_status === 'approved' &&
                $price->deleted_at === null
            ) {

                if (!in_array($price->service_id, $serviceIds)) {
                    $service = $price->service;

                    if ($service) {
                        $matchesActive = !$activeOnly || ($service->is_active && $service->deleted_at === null);

                        if ($matchesActive) {
                            $serviceIds[] = $service->id;
                            $services[] = $service;
                        }
                    }
                }
            }
        }

        return $services;
    }

    /**
     * Get pricing statistics for this model
     */
    public function getPricingStats($currency = 'CAD')
    {
        $prices = $this->servicePrices;

        if (!$prices) {
            return null;
        }

        $amounts = [];
        $setupFees = [];

        foreach ($prices as $price) {
            if (
                $price->is_current == 1 &&
                $price->approval_status === 'approved' &&
                $price->deleted_at === null &&
                $price->currency === $currency
            ) {

                $amounts[] = $price->amount;
                if ($price->setup_fee !== null) {
                    $setupFees[] = $price->setup_fee;
                }
            }
        }

        if (empty($amounts)) {
            return null;
        }

        return [
            'count' => count($amounts),
            'avg_amount' => round(array_sum($amounts) / count($amounts), 2),
            'min_amount' => min($amounts),
            'max_amount' => max($amounts),
            'avg_setup_fee' => !empty($setupFees) ? round(array_sum($setupFees) / count($setupFees), 2) : null,
            'currency' => $currency
        ];
    }

    /**
     * Get typical units used with this pricing model
     */
    public function getCommonUnits()
    {
        $prices = $this->servicePrices;

        if (!$prices) {
            return [];
        }

        $units = [];

        foreach ($prices as $price) {
            if (
                $price->is_current == 1 &&
                $price->deleted_at === null &&
                $price->unit
            ) {

                if (!isset($units[$price->unit])) {
                    $units[$price->unit] = 0;
                }
                $units[$price->unit]++;
            }
        }

        arsort($units);
        return $units;
    }

    /**
     * Get suggested unit based on pricing model type
     */
    public function getSuggestedUnit()
    {
        $commonUnits = $this->getCommonUnits();

        if (!empty($commonUnits)) {
            return array_key_first($commonUnits);
        }

        // Fallback based on model type
        if ($this->is_time_based) {
            return 'hour';
        }

        // Check code for hints
        $code = strtolower($this->code);

        if (strpos($code, 'unit') !== false || strpos($code, 'device') !== false) {
            return 'unit';
        }

        if (strpos($code, 'user') !== false) {
            return 'user';
        }

        if (strpos($code, 'subscription') !== false || strpos($code, 'monthly') !== false) {
            return 'month';
        }

        if (strpos($code, 'annual') !== false) {
            return 'year';
        }

        return 'each';
    }

    /**
     * Check if pricing model is in use
     */
    public function isInUse()
    {
        return $this->getPriceCount(true, true) > 0;
    }

    /**
     * Soft delete (with validation)
     */
    public function softDelete()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot delete pricing model '{$this->name}' because it is currently in use by active service prices"
            );
        }

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
     * Toggle time-based flag
     */
    public function toggleTimeBased()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot change time-based setting for '{$this->name}' because it is currently in use"
            );
        }

        $this->is_time_based = !$this->is_time_based;
        return $this->update(['is_time_based']);
    }

    /**
     * Search pricing models by name or description
     */
    public static function search($keyword)
    {
        return static::new()
            ->where('name', 'LIKE', "%{$keyword}%")
            ->orWhere('description', 'LIKE', "%{$keyword}%")
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get pricing model display label with type indicator
     */
    public function getDisplayLabel()
    {
        $type = $this->is_time_based ? 'Time-based' : 'Fixed';
        return "{$this->name} ({$type})";
    }

    /**
     * Validate unit compatibility with pricing model
     */
    public function isUnitCompatible($unit)
    {
        $unit = strtolower($unit);

        // Time-based models should use time units
        if ($this->is_time_based) {
            $timeUnits = ['hour', 'day', 'week', 'month', 'year', 'minute'];
            return in_array($unit, $timeUnits);
        }

        // Fixed models shouldn't use time units
        $timeUnits = ['hour', 'day', 'week', 'month', 'year', 'minute'];
        return !in_array($unit, $timeUnits);
    }
}
