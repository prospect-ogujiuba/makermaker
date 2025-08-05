<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use MakerMaker\Models\Traits\HasCodeGeneration;
use MakerMaker\Models\Traits\HasSoftDeletes;  
use MakerMaker\Models\Traits\HasActiveStatus;

class Service extends Model
{
    use HasCodeGeneration, HasSoftDeletes, HasActiveStatus;

    protected $resource = 'b2bcnc_services';

    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'icon',
        'active',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at', // System managed - don't allow mass assignment
    ];

    /**
     * Data type casting for model properties
     */
    protected $cast = [
        'id' => 'int',
        'base_price' => 'float',
        'active' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Format data before saving
     */
    protected $format = [
        'code' => 'sanitize_key', // WordPress function for sanitizing keys
        'base_price' => 'static::formatPrice',
    ];

    /**
     * Format price to ensure proper decimal places
     */
    public static function formatPrice($value)
    {
        return number_format((float)$value, 2, '.', '');
    }

    // ================================
    // SCOPES - Enhanced with trait methods
    // ================================

    /**
     * Scope to get available services (active and not deleted)
     * 
     * @return $this
     */
    public function available()
    {
        return $this->active()->withoutDeleted();
    }

    /**
     * Scope to get services by price range
     * 
     * @param float $min Minimum price
     * @param float|null $max Maximum price (optional)
     * @return $this
     */
    public function priceRange(float $min, ?float $max = null)
    {
        $query = $this->where('base_price', '>=', $min);
        
        if ($max !== null) {
            $query = $query->where('base_price', '<=', $max);
        }
        
        return $query;
    }

    // ================================
    // RELATIONSHIPS
    // ================================

    // Add relationships here as needed
    // public function orders()
    // {
    //     return $this->hasMany(Order::class, 'service_id');
    // }

    // ================================  
    // BUSINESS LOGIC METHODS
    // ================================

    /**
     * Get formatted price with currency
     * 
     * @param string $currency Currency symbol
     * @return string Formatted price with currency
     */
    public function getFormattedPrice(string $currency = '$'): string
    {
        return $currency . number_format($this->base_price, 2);
    }

    /**
     * Check if service is available for purchase
     * 
     * @return bool True if available, false otherwise
     */
    public function isAvailable(): bool
    {
        return $this->isActive() && !$this->isDeleted();
    }

    /**
     * Get service icon URL or default
     * 
     * @param string $default Default icon if none set
     * @return string Icon URL or default
     */
    public function getIconUrl(string $default = 'dashicons-admin-generic'): string
    {
        return $this->icon ?: $default;
    }

    /**
     * Get short description (truncated)
     * 
     * @param int $length Maximum length
     * @return string Truncated description
     */
    public function getShortDescription(int $length = 100): string
    {
        if (strlen($this->description) <= $length) {
            return $this->description;
        }
        
        return substr($this->description, 0, $length) . '...';
    }

    // ================================
    // STATIC HELPER METHODS
    // ================================

    /**
     * Get all available services (shorthand)
     * 
     * @return \TypeRocket\Database\Results Collection of available services
     */
    public static function getAvailable()
    {
        return static::new()->available()->get();
    }

    /**
     * Get services by price range (shorthand)
     * 
     * @param float $min Minimum price
     * @param float|null $max Maximum price
     * @return \TypeRocket\Database\Results Collection of services
     */
    public static function getByPriceRange(float $min, ?float $max = null)
    {
        return static::new()->priceRange($min, $max)->get();
    }

    /**
     * Get popular services (you can implement your own logic)
     * 
     * @param int $limit Number of services to return
     * @return \TypeRocket\Database\Results Collection of popular services
     */
    public static function getPopular(int $limit = 5)
    {
        // This is a placeholder - implement based on your business logic
        // For example, you might order by order count, ratings, etc.
        return static::new()->available()->take($limit)->get();
    }
}