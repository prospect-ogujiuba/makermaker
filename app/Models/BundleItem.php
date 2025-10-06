<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class BundleItem extends Model
{
    protected $resource = 'srvc_bundle_items';

protected $fillable = [
        'bundle_id',
        'service_id',
        'quantity',
        'discount_pct',
        'is_optional',
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
        'bundle',
        'service'
    ];

    /** BundleItem belongs to a ServiceBundle */
    public function bundle()
    {
        return $this->belongsTo(ServiceBundle::class, 'bundle_id');
    }

    /** BundleItem belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
 * Get all items in a bundle
 * 
 * @param int $bundleId
 * @return \TypeRocket\Models\Results|static[]
 */
public static function getByBundle($bundleId)
{
    if (empty($bundleId)) {
        return static::new()->where('1', '0')->get();
    }
    
    return static::new()
        ->where('bundle_id', $bundleId)
        ->orderBy('priority', 'ASC')
        ->get();
}

/**
 * Get all bundles containing a service
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
 * Find specific bundle-service relationship
 * 
 * @param int $bundleId
 * @param int $serviceId
 * @return static|null
 */
public static function findByBundleAndService($bundleId, $serviceId)
{
    if (empty($bundleId) || empty($serviceId)) {
        return null;
    }
    
    return static::new()
        ->where('bundle_id', $bundleId)
        ->where('service_id', $serviceId)
        ->first();
}

/**
 * Get the related ServiceBundle model
 * 
 * @return ServiceBundle|null
 */
public function getBundle()
{
    if (empty($this->bundle_id)) {
        return null;
    }
    
    return ServiceBundle::new()->findById($this->bundle_id);
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
 * Get item quantity
 * 
 * @return int
 */
public function getQuantity()
{
    return isset($this->quantity) ? (int)$this->quantity : 1;
}

/**
 * Get item price (individual service price in bundle context)
 * 
 * @return float|null
 */
public function getPrice()
{
    return isset($this->price) ? (float)$this->price : null;
}

/**
 * Get formatted item price
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
        return 'Included';
    }
    
    return $currencySymbol . number_format($price, 2);
}

/**
 * Calculate total price for this item (price × quantity)
 * 
 * @return float|null
 */
public function calculateTotal()
{
    $price = $this->getPrice();
    
    if ($price === null) {
        return null;
    }
    
    return $price * $this->getQuantity();
}

/**
 * Get formatted total price
 * 
 * @param string $currencySymbol
 * @return string
 */
public function getFormattedTotal($currencySymbol = '$')
{
    $total = $this->calculateTotal();
    
    if ($total === null) {
        return 'N/A';
    }
    
    if ($total == 0) {
        return 'Included';
    }
    
    return $currencySymbol . number_format($total, 2);
}

/**
 * Check if item is required in the bundle
 * 
 * @return bool
 */
public function isRequired()
{
    return !empty($this->is_required) && (bool)$this->is_required;
}

/**
 * Check if item is optional in the bundle
 * 
 * @return bool
 */
public function isOptional()
{
    return !$this->isRequired();
}

/**
 * Get item priority/sort order
 * 
 * @return int
 */
public function getPriority()
{
    return isset($this->priority) ? (int)$this->priority : 999;
}

/**
 * Check if quantity is customizable
 * 
 * @return bool
 */
public function isQuantityCustomizable()
{
    return !empty($this->is_quantity_customizable) && (bool)$this->is_quantity_customizable;
}

/**
 * Get minimum quantity allowed
 * 
 * @return int
 */
public function getMinQuantity()
{
    return isset($this->min_quantity) ? (int)$this->min_quantity : 1;
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
    if ($quantity < $this->getMinQuantity()) {
        return false;
    }
    
    $maxQty = $this->getMaxQuantity();
    
    if ($maxQty !== null && $quantity > $maxQty) {
        return false;
    }
    
    return true;
}

/**
 * Get formatted quantity display
 * 
 * @return string
 */
public function getFormattedQuantity()
{
    $qty = $this->getQuantity();
    
    if ($qty === 1) {
        return '1 service';
    }
    
    return $qty . ' services';
}

/**
 * Get discount applied to this item (if different from bundle discount)
 * 
 * @return float|null
 */
public function getItemDiscount()
{
    return isset($this->discount_percentage) ? (float)$this->discount_percentage : null;
}

/**
 * Check if item has individual discount
 * 
 * @return bool
 */
public function hasItemDiscount()
{
    $discount = $this->getItemDiscount();
    return $discount !== null && $discount > 0;
}

/**
 * Validate bundle item data
 * 
 * @return array Array of error messages (empty if valid)
 */
public function validate()
{
    $errors = [];
    
    if (empty($this->bundle_id)) {
        $errors[] = 'Bundle ID is required';
    }
    
    if (empty($this->service_id)) {
        $errors[] = 'Service ID is required';
    }
    
    if (!empty($this->bundle_id) && !empty($this->service_id)) {
        $existing = static::findByBundleAndService($this->bundle_id, $this->service_id);
        if ($existing && $existing->getID() !== $this->getID()) {
            $errors[] = 'This service is already in the bundle';
        }
    }
    
    $quantity = $this->getQuantity();
    if ($quantity < 1) {
        $errors[] = 'Quantity must be at least 1';
    }
    
    if (isset($this->price) && $this->price < 0) {
        $errors[] = 'Item price cannot be negative';
    }
    
    if (isset($this->discount_percentage)) {
        $discount = (float)$this->discount_percentage;
        if ($discount < 0 || $discount > 100) {
            $errors[] = 'Discount percentage must be between 0 and 100';
        }
    }
    
    if (isset($this->min_quantity) && isset($this->max_quantity)) {
        if ($this->min_quantity > $this->max_quantity) {
            $errors[] = 'Minimum quantity cannot exceed maximum quantity';
        }
    }
    
    return $errors;
}
}
