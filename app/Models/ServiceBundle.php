<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceBundle extends Model
{
    protected $resource = 'srvc_service_bundles';

    protected $fillable = [
        'name',
        'slug',
        'short_desc',
        'long_desc',
        'bundle_type',
        'total_discount_pct',
        'is_active',
        'valid_from',
        'valid_to'
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
        'services'
    ];

    /** ServiceBundle belongs to many Services */
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_bundle_items', 'bundle_id', 'service_id');
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
     * Get all active bundles
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getActive()
    {
        return static::new()->where('status', 'active')->get();
    }

    /**
     * Find bundle by slug
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
     * Get bundles available on a specific date
     * 
     * @param string $date Y-m-d format
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getAvailableOnDate($date)
    {
        $query = static::new()->where('status', 'active');

        if (!empty($date)) {
            $query->where(function ($q) use ($date) {
                $q->where('start_date', '<=', $date)
                    ->orWhereNull('start_date');
            })->where(function ($q) use ($date) {
                $q->where('end_date', '>=', $date)
                    ->orWhereNull('end_date');
            });
        }

        return $query->get();
    }

    /**
     * Get currently available bundles
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getCurrentlyAvailable()
    {
        return static::getAvailableOnDate(date('Y-m-d'));
    }

    /**
     * Get bundle base price
     * 
     * @return float|null
     */
    public function getBasePrice()
    {
        return isset($this->price) ? (float)$this->price : null;
    }

    /**
     * Get formatted base price
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedBasePrice($currencySymbol = '$')
    {
        $price = $this->getBasePrice();

        if ($price === null) {
            return 'N/A';
        }

        if ($price == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($price, 2);
    }

    /**
     * Get discount percentage
     * 
     * @return float|null
     */
    public function getDiscountPercentage()
    {
        return isset($this->discount_percentage) ? (float)$this->discount_percentage : null;
    }

    /**
     * Get formatted discount
     * 
     * @return string
     */
    public function getFormattedDiscount()
    {
        $discount = $this->getDiscountPercentage();

        if ($discount === null || $discount == 0) {
            return 'No discount';
        }

        return number_format($discount, 0) . '% off';
    }

    /**
     * Check if bundle has a discount
     * 
     * @return bool
     */
    public function hasDiscount()
    {
        $discount = $this->getDiscountPercentage();
        return $discount !== null && $discount > 0;
    }

    /**
     * Check if bundle is available on a specific date
     * 
     * @param string $date Y-m-d format
     * @return bool
     */
    public function isAvailableOnDate($date)
    {
        if (empty($date)) {
            return false;
        }

        $startDate = $this->start_date;
        $endDate = $this->end_date;

        if (!empty($startDate) && $date < $startDate) {
            return false;
        }

        if (!empty($endDate) && $date > $endDate) {
            return false;
        }

        return true;
    }

    /**
     * Check if bundle is currently available
     * 
     * @return bool
     */
    public function isCurrentlyAvailable()
    {
        return $this->status === 'active' && $this->isAvailableOnDate(date('Y-m-d'));
    }

    /**
     * Get all bundle items
     * 
     * @return \TypeRocket\Models\Results|BundleItem[]
     */
    public function getBundleItems()
    {
        return BundleItem::getByBundle($this->getID());
    }

    /**
     * Get all services in this bundle
     * 
     * @return array
     */
    public function getServices()
    {
        $bundleItems = $this->getBundleItems();

        if ($bundleItems->isEmpty()) {
            return [];
        }

        $services = [];

        foreach ($bundleItems as $item) {
            $service = $item->getService();
            if ($service) {
                $services[] = [
                    'id' => $service->getID(),
                    'name' => $service->name ?? '',
                    'quantity' => $item->getQuantity(),
                    'item_price' => $item->getPrice(),
                    'formatted_item_price' => $item->getFormattedPrice(),
                ];
            }
        }

        return $services;
    }

    /**
     * Calculate total value of all items (before discount)
     * 
     * @return float
     */
    public function calculateItemsTotal()
    {
        $bundleItems = $this->getBundleItems();
        $total = 0;

        foreach ($bundleItems as $item) {
            $itemPrice = $item->getPrice();
            $quantity = $item->getQuantity();

            if ($itemPrice !== null) {
                $total += $itemPrice * $quantity;
            }
        }

        return $total;
    }

    /**
     * Calculate final bundle price (with discount applied)
     * 
     * @return float|null
     */
    public function calculateFinalPrice()
    {
        $basePrice = $this->getBasePrice();

        // If bundle has explicit price, use that
        if ($basePrice !== null) {
            return $basePrice;
        }

        // Otherwise calculate from items with discount
        $itemsTotal = $this->calculateItemsTotal();
        $discount = $this->getDiscountPercentage() ?? 0;

        if ($discount > 0) {
            $itemsTotal = $itemsTotal * (1 - ($discount / 100));
        }

        return $itemsTotal;
    }

    /**
     * Get formatted final price
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedFinalPrice($currencySymbol = '$')
    {
        $price = $this->calculateFinalPrice();

        if ($price === null) {
            return 'N/A';
        }

        if ($price == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($price, 2);
    }

    /**
     * Calculate savings amount
     * 
     * @return float
     */
    public function calculateSavings()
    {
        $itemsTotal = $this->calculateItemsTotal();
        $finalPrice = $this->calculateFinalPrice() ?? 0;

        return max(0, $itemsTotal - $finalPrice);
    }

    /**
     * Get formatted savings
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedSavings($currencySymbol = '$')
    {
        $savings = $this->calculateSavings();

        if ($savings == 0) {
            return 'No savings';
        }

        return 'Save ' . $currencySymbol . number_format($savings, 2);
    }

    /**
     * Get number of services in bundle
     * 
     * @return int
     */
    public function getServiceCount()
    {
        return count($this->getBundleItems());
    }

    /**
     * Check if bundle contains a specific service
     * 
     * @param int $serviceId
     * @return bool
     */
    public function containsService($serviceId)
    {
        if (empty($serviceId)) {
            return false;
        }

        $item = BundleItem::findByBundleAndService($this->getID(), $serviceId);

        return $item !== null;
    }

    /**
     * Validate bundle data
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Bundle name is required';
        }

        if (empty($this->slug)) {
            $errors[] = 'Bundle slug is required';
        } elseif ($this->slug !== sanitize_title($this->slug)) {
            $errors[] = 'Bundle slug contains invalid characters';
        }

        if (isset($this->price) && $this->price < 0) {
            $errors[] = 'Bundle price cannot be negative';
        }

        if (isset($this->discount_percentage)) {
            $discount = (float)$this->discount_percentage;
            if ($discount < 0 || $discount > 100) {
                $errors[] = 'Discount percentage must be between 0 and 100';
            }
        }

        if (!empty($this->start_date) && !empty($this->end_date)) {
            if ($this->start_date > $this->end_date) {
                $errors[] = 'Start date must be before end date';
            }
        }

        return $errors;
    }
}
