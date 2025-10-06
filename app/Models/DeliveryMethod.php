<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class DeliveryMethod extends Model
{
    protected $resource = 'srvc_delivery_methods';

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_site_access',
        'supports_remote',
        'default_lead_time_days',
        'default_sla_hours'
    ];

    protected $cast = [
        'requires_site_access' => 'bool',
        'supports_remote' => 'bool',
        'default_lead_time_days' => 'int',
        'default_sla_hours' => 'int'
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

    // Service prices using this pricing tier
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_delivery', 'delivery_method_id', 'service_id');
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
     * Get all active delivery methods
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getActive()
    {
        return static::new()->where('status', 'active')->get();
    }

    /**
     * Find delivery method by slug
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
     * Find delivery methods by type
     * 
     * @param string $type
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function findByType($type)
    {
        if (empty($type)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('type', $type)->get();
    }

    /**
     * Get digital delivery methods
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getDigital()
    {
        return static::findByType('digital');
    }

    /**
     * Get physical delivery methods
     * 
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getPhysical()
    {
        return static::findByType('physical');
    }

    /**
     * Check if delivery method is digital
     * 
     * @return bool
     */
    public function isDigital()
    {
        return !empty($this->type) && strtolower($this->type) === 'digital';
    }

    /**
     * Check if delivery method is physical
     * 
     * @return bool
     */
    public function isPhysical()
    {
        return !empty($this->type) && strtolower($this->type) === 'physical';
    }

    /**
     * Check if delivery method requires shipping
     * 
     * @return bool
     */
    public function requiresShipping()
    {
        return $this->isPhysical();
    }

    /**
     * Get estimated delivery time (in days)
     * 
     * @return int|null
     */
    public function getEstimatedDays()
    {
        return isset($this->estimated_days) ? (int)$this->estimated_days : null;
    }

    /**
     * Get formatted estimated delivery time
     * 
     * @return string
     */
    public function getFormattedEstimate()
    {
        $days = $this->getEstimatedDays();

        if ($days === null) {
            return 'Not specified';
        }

        if ($days === 0) {
            return 'Instant';
        }

        if ($days === 1) {
            return '1 day';
        }

        if ($days <= 7) {
            return $days . ' days';
        }

        $weeks = ceil($days / 7);
        return $weeks . ($weeks === 1 ? ' week' : ' weeks');
    }

    /**
     * Get delivery cost (if applicable)
     * 
     * @return float|null
     */
    public function getCost()
    {
        return isset($this->cost) ? (float)$this->cost : null;
    }

    /**
     * Get formatted delivery cost
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedCost($currencySymbol = '$')
    {
        $cost = $this->getCost();

        if ($cost === null) {
            return 'N/A';
        }

        if ($cost == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($cost, 2);
    }

    /**
     * Check if delivery method is free
     * 
     * @return bool
     */
    public function isFree()
    {
        $cost = $this->getCost();
        return $cost !== null && $cost == 0;
    }

    /**
     * Get all services using this delivery method
     * 
     * @return \TypeRocket\Models\Results|Service[]
     */
    public function getServices()
    {
        $serviceDeliveries = ServiceDelivery::new()
            ->where('delivery_method_id', $this->getID())
            ->get();

        if ($serviceDeliveries->isEmpty()) {
            return Service::new()->where('1', '0')->get();
        }

        $serviceIds = array_filter(array_column($serviceDeliveries->toArray(), 'service_id'));

        if (empty($serviceIds)) {
            return Service::new()->where('1', '0')->get();
        }

        return Service::new()->where('id', 'in', $serviceIds)->get();
    }

    /**
     * Get formatted delivery method name with type
     * 
     * @return string
     */
    public function getFormattedName()
    {
        $name = $this->name ?? 'Unnamed Method';
        $type = !empty($this->type) ? ' (' . ucfirst($this->type) . ')' : '';

        return $name . $type;
    }

    /**
     * Validate delivery method data
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->name)) {
            $errors[] = 'Delivery method name is required';
        }

        if (empty($this->type)) {
            $errors[] = 'Delivery method type is required';
        } elseif (!in_array(strtolower($this->type), ['digital', 'physical'])) {
            $errors[] = 'Delivery method type must be digital or physical';
        }

        if (empty($this->slug)) {
            $errors[] = 'Delivery method slug is required';
        } elseif ($this->slug !== sanitize_title($this->slug)) {
            $errors[] = 'Delivery method slug contains invalid characters';
        }

        return $errors;
    }
}
