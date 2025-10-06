<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceAddon extends Model
{
    protected $resource = 'srvc_service_addons';

    protected $fillable = [
        'service_id',
        'addon_service_id',
        'required',
        'min_qty',
        'max_qty',
        'default_qty',
        'sort_order'
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
        'addonService'
    ];

    protected $format = [
        'metadata' => 'json_encode',
        'min_qty' => 'convertEmptyToNull',
        'max_qty' => 'convertEmptyToNull',
        'default_qty' => 'convertEmptyToNull',
    ];

    /** ServiceAddon belongs to a Service (the main service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAddon belongs to a Service (the addon service) */
    public function addonService()
    {
        return $this->belongsTo(Service::class, 'addon_service_id');
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
     * Get all active addons
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getActive()
    {
        return static::new()->where('status', 'active')->get();
    }

    /**
     * Get all addons for a service
     * 
     * @param int $serviceId
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getByService($serviceId)
    {
        if (empty($serviceId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('service_id', $serviceId)->get();
    }

    /**
     * Get active addons for a service
     * 
     * @param int $serviceId
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getActiveByService($serviceId)
    {
        if (empty($serviceId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()
            ->where('service_id', $serviceId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Find addon by slug
     * 
     * @param string $slug
     * @return static|null
     */
    public static function findBySlug($slug)
    {
        if (empty($slug)) {
            return null;
        }

        return static::new()->where('slug', $slug)->first();
    }

    /**
     * Find addon by service and slug
     * 
     * @param int $serviceId
     * @param string $slug
     * @return static|null
     */
    public static function findByServiceAndSlug($serviceId, $slug)
    {
        if (empty($serviceId) || empty($slug)) {
            return null;
        }

        return static::new()
            ->where('service_id', $serviceId)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get the related Service model
     * 
     * @return Service|null
     */
    public function getService()
    {
        if (empty($this->service_id)) {
            return null;
        }

        return Service::new()->findById($this->service_id);
    }

    /**
     * Get addon price
     * 
     * @return float|null
     */
    public function getPrice()
    {
        return isset($this->price) ? (float)$this->price : null;
    }

    /**
     * Get formatted addon price
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedPrice($currencySymbol = '$')
    {
        $price = $this->getPrice();

        if ($price === null) {
            return 'N/A';
        }

        if ($price == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($price, 2);
    }

    /**
     * Check if addon is free
     * 
     * @return bool
     */
    public function isFree()
    {
        $price = $this->getPrice();
        return $price !== null && $price == 0;
    }

    /**
     * Check if addon is recurring
     * 
     * @return bool
     */
    public function isRecurring()
    {
        return !empty($this->is_recurring) && (bool)$this->is_recurring;
    }

    /**
     * Check if addon is one-time
     * 
     * @return bool
     */
    public function isOneTime()
    {
        return !$this->isRecurring();
    }

    /**
     * Get recurring interval (monthly, yearly, etc.)
     * 
     * @return string|null
     */
    public function getRecurringInterval()
    {
        if (!$this->isRecurring()) {
            return null;
        }

        return $this->recurring_interval ?? null;
    }

    /**
     * Get formatted price with interval
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedPriceWithInterval($currencySymbol = '$')
    {
        $price = $this->getFormattedPrice($currencySymbol);

        if ($this->isRecurring() && !empty($this->recurring_interval)) {
            $price .= '/' . $this->recurring_interval;
        }

        return $price;
    }

    /**
     * Check if addon is required
     * 
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->is_required) && (bool)$this->is_required;
    }

    /**
     * Check if addon is optional
     * 
     * @return bool
     */
    public function isOptional()
    {
        return !$this->isRequired();
    }

    /**
     * Get maximum quantity allowed
     * 
     * @return int|null
     */
    public function getMaxQuantity()
    {
        return isset($this->max_quantity) ? (int)$this->max_quantity : null;
    }

    /**
     * Check if quantity is unlimited
     * 
     * @return bool
     */
    public function isUnlimitedQuantity()
    {
        return $this->getMaxQuantity() === null;
    }

    /**
     * Validate quantity
     * 
     * @param int $quantity
     * @return bool
     */
    public function isValidQuantity($quantity)
    {
        if ($quantity < 1) {
            return false;
        }

        $maxQty = $this->getMaxQuantity();

        if ($maxQty !== null && $quantity > $maxQty) {
            return false;
        }

        return true;
    }

    /**
     * Get addon priority/sort order
     * 
     * @return int
     */
    public function getPriority()
    {
        return isset($this->priority) ? (int)$this->priority : 999;
    }

    /**
     * Calculate price for quantity
     * 
     * @param int $quantity
     * @return float|null
     */
    public function calculatePrice($quantity = 1)
    {
        $price = $this->getPrice();

        if ($price === null) {
            return null;
        }

        if (!$this->isValidQuantity($quantity)) {
            return null;
        }

        return $price * $quantity;
    }

    /**
     * Get formatted price for quantity
     * 
     * @param int $quantity
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedPriceForQuantity($quantity, $currencySymbol = '$')
    {
        $total = $this->calculatePrice($quantity);

        if ($total === null) {
            return 'N/A';
        }

        if ($total == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($total, 2);
    }

    /**
     * Validate addon data
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->service_id)) {
            $errors[] = 'Service ID is required';
        }

        if (empty($this->name)) {
            $errors[] = 'Addon name is required';
        }

        if (empty($this->slug)) {
            $errors[] = 'Addon slug is required';
        } elseif ($this->slug !== sanitize_title($this->slug)) {
            $errors[] = 'Addon slug contains invalid characters';
        }

        if (isset($this->price) && $this->price < 0) {
            $errors[] = 'Addon price cannot be negative';
        }

        if ($this->isRecurring() && empty($this->recurring_interval)) {
            $errors[] = 'Recurring interval is required for recurring addons';
        }

        return $errors;
    }
}
