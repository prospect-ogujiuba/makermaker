<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class PricingTier extends Model
{
    protected $resource = 'srvc_pricing_tiers';

    protected $fillable = [
        'name',
        'code',
        'sort_order',
        'discount_pct',
        'min_volume',
        'max_volume'
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

    // Service prices using this pricing tier
    public function servicePrices()
    {
        return $this->hasMany(\MakerMaker\Models\ServicePrice::class, 'pricing_tier_id');
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
     * Get all active pricing tiers
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Find pricing tier by code
     */
    public function findByCode($code)
    {
        return $this->where('code', $code)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get tiers ordered by discount (highest to lowest)
     */
    public function getByDiscount()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('discount_pct', 'DESC')
            ->findAll();
    }

    /**
     * Get tier applicable for a given volume
     */
    public function getTierForVolume($volume)
    {
        return $this->where('min_volume', '<=', $volume)
            ->where(function ($query) use ($volume) {
                $query->where('max_volume', '>=', $volume)
                    ->orWhere('max_volume', 'IS', null);
            })
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->first();
    }

    /**
     * Check if volume falls within this tier's range
     */
    public function isVolumeInRange($volume)
    {
        $meetsMin = $volume >= $this->min_volume;
        $meetsMax = $this->max_volume === null || $volume <= $this->max_volume;

        return $meetsMin && $meetsMax;
    }

    /**
     * Get volume range as formatted string
     */
    public function getVolumeRangeLabel()
    {
        if ($this->min_volume === null && $this->max_volume === null) {
            return 'Any volume';
        }

        if ($this->max_volume === null) {
            return "{$this->min_volume}+";
        }

        if ($this->min_volume == $this->max_volume) {
            return (string) $this->min_volume;
        }

        return "{$this->min_volume} - {$this->max_volume}";
    }

    /**
     * Get discount as decimal multiplier (e.g., 10% = 0.90)
     */
    public function getDiscountMultiplier()
    {
        return 1 - ($this->discount_pct / 100);
    }

    /**
     * Calculate discounted price
     */
    public function applyDiscount($basePrice)
    {
        return $basePrice * $this->getDiscountMultiplier();
    }

    /**
     * Calculate savings from discount
     */
    public function calculateSavings($basePrice)
    {
        return $basePrice * ($this->discount_pct / 100);
    }

    /**
     * Format discount as percentage string
     */
    public function getFormattedDiscount()
    {
        if ($this->discount_pct == 0) {
            return 'No discount';
        }

        return number_format($this->discount_pct, 2) . '% off';
    }

    /**
     * Get tier display name with discount
     */
    public function getDisplayLabel()
    {
        $label = $this->name;

        if ($this->discount_pct > 0) {
            $label .= " ({$this->getFormattedDiscount()})";
        }

        return $label;
    }

    /**
     * Count service prices using this tier
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
     * Get all services using this tier
     */
    public function getServicesUsingTier($activeOnly = true)
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
     * Get pricing statistics for this tier
     */
    public function getPricingStats($currency = 'CAD')
    {
        $prices = $this->servicePrices;

        if (!$prices) {
            return null;
        }

        $amounts = [];
        $discountedAmounts = [];

        foreach ($prices as $price) {
            if (
                $price->is_current == 1 &&
                $price->approval_status === 'approved' &&
                $price->deleted_at === null &&
                $price->currency === $currency
            ) {

                $amounts[] = $price->amount;
                $discountedAmounts[] = $this->applyDiscount($price->amount);
            }
        }

        if (empty($amounts)) {
            return null;
        }

        return [
            'count' => count($amounts),
            'avg_base_amount' => round(array_sum($amounts) / count($amounts), 2),
            'avg_discounted_amount' => round(array_sum($discountedAmounts) / count($discountedAmounts), 2),
            'total_savings' => round(array_sum($amounts) - array_sum($discountedAmounts), 2),
            'min_amount' => min($amounts),
            'max_amount' => max($amounts),
            'currency' => $currency
        ];
    }

    /**
     * Check if tier is in use
     */
    public function isInUse()
    {
        return $this->getPriceCount(true, true) > 0;
    }

    /**
     * Update sort order
     */
    public function updateSortOrder($newOrder)
    {
        $this->sort_order = $newOrder;
        return $this->update(['sort_order']);
    }

    /**
     * Reorder all tiers
     */
    public static function reorderTiers($tierIds)
    {
        $order = 0;
        foreach ($tierIds as $tierId) {
            $tier = static::new()->findById($tierId);
            if ($tier) {
                $tier->updateSortOrder($order);
                $order++;
            }
        }

        return true;
    }

    /**
     * Validate volume range
     */
    public function validateVolumeRange($minVolume, $maxVolume)
    {
        $errors = [];

        if ($minVolume < 0) {
            $errors[] = "Minimum volume must be non-negative";
        }

        if ($maxVolume !== null && $maxVolume < $minVolume) {
            $errors[] = "Maximum volume must be greater than or equal to minimum volume";
        }

        // Check for overlaps with other tiers
        $overlapping = static::new()
            ->where('id', '!=', $this->id ?? 0)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        if ($overlapping) {
            foreach ($overlapping as $tier) {
                $overlap = false;

                // Check if ranges overlap
                if ($maxVolume === null && $tier->max_volume === null) {
                    $overlap = true; // Both unbounded
                } elseif ($maxVolume === null) {
                    $overlap = $minVolume <= ($tier->max_volume ?? PHP_INT_MAX);
                } elseif ($tier->max_volume === null) {
                    $overlap = ($tier->min_volume ?? 0) <= $maxVolume;
                } else {
                    $overlap = !($maxVolume < ($tier->min_volume ?? 0) || $minVolume > $tier->max_volume);
                }

                if ($overlap) {
                    $errors[] = "Volume range overlaps with tier '{$tier->name}' ({$tier->getVolumeRangeLabel()})";
                }
            }
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Update discount percentage
     */
    public function updateDiscount($discountPct)
    {
        if ($discountPct < 0 || $discountPct > 100) {
            throw new \InvalidArgumentException("Discount must be between 0 and 100");
        }

        $this->discount_pct = $discountPct;
        return $this->update(['discount_pct']);
    }

    /**
     * Soft delete (with validation)
     */
    public function softDelete()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot delete pricing tier '{$this->name}' because it is currently in use by active service prices"
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
     * Search pricing tiers by name
     */
    public static function search($keyword)
    {
        return static::new()
            ->where('name', 'LIKE', "%{$keyword}%")
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    /**
     * Get next available sort order
     */
    public static function getNextSortOrder()
    {
        $maxOrder = static::new()
            ->where('deleted_at', 'IS', null)
            ->orderBy('sort_order', 'DESC')
            ->first();

        return $maxOrder ? $maxOrder->sort_order + 1 : 0;
    }
}
