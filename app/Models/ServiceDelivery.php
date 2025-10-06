<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceDelivery extends Model
{
    protected $resource = 'srvc_service_delivery';

    protected $fillable = [
        'service_id',
        'delivery_method_id',
        'lead_time_days',
        'sla_hours',
        'surcharge',
        'is_default'
    ];

    protected $cast = [
        'lead_time_days' => 'int',
        'sla_hours' => 'int',
        'surcharge' => 'float',
        'is_default' => 'bool'
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
        'deliveryMethod'
    ];

    /** ServiceDelivery belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDelivery belongs to a DeliveryMethod */
    public function deliveryMethod()
    {
        return $this->belongsTo(DeliveryMethod::class, 'delivery_method_id');
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
     * Get all delivery methods for a service
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
     * Get all services using a delivery method
     * 
     * @param int $deliveryMethodId
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getByDeliveryMethod($deliveryMethodId)
    {
        if (empty($deliveryMethodId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('delivery_method_id', $deliveryMethodId)->get();
    }

    /**
     * Find specific service-delivery method relationship
     * 
     * @param int $serviceId
     * @param int $deliveryMethodId
     * @return static|null
     */
    public static function findRelationship($serviceId, $deliveryMethodId)
    {
        if (empty($serviceId) || empty($deliveryMethodId)) {
            return null;
        }

        return static::new()
            ->where('service_id', $serviceId)
            ->where('delivery_method_id', $deliveryMethodId)
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
     * Get the related DeliveryMethod model
     * 
     * @return DeliveryMethod|null
     */
    public function getDeliveryMethod()
    {
        if (empty($this->delivery_method_id)) {
            return null;
        }

        return DeliveryMethod::new()->findById($this->delivery_method_id);
    }

    /**
     * Check if this is the default delivery method for the service
     * 
     * @return bool
     */
    public function isDefault()
    {
        return !empty($this->is_default) && (bool)$this->is_default;
    }

    /**
     * Check if this delivery method is available for the service
     * 
     * @return bool
     */
    public function isAvailable()
    {
        return !empty($this->is_available) && (bool)$this->is_available;
    }

    /**
     * Get additional cost for this delivery method (beyond base service price)
     * 
     * @return float|null
     */
    public function getAdditionalCost()
    {
        return isset($this->additional_cost) ? (float)$this->additional_cost : null;
    }

    /**
     * Get formatted additional cost
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedAdditionalCost($currencySymbol = '$')
    {
        $cost = $this->getAdditionalCost();

        if ($cost === null) {
            return 'N/A';
        }

        if ($cost == 0) {
            return 'Included';
        }

        return '+ ' . $currencySymbol . number_format($cost, 2);
    }

    /**
     * Check if additional cost is included (zero)
     * 
     * @return bool
     */
    public function isIncluded()
    {
        $cost = $this->getAdditionalCost();
        return $cost !== null && $cost == 0;
    }

    /**
     * Get estimated delivery time in days (overrides delivery method default if set)
     * 
     * @return int|null
     */
    public function getEstimatedDays()
    {
        if (isset($this->estimated_days)) {
            return (int)$this->estimated_days;
        }

        $deliveryMethod = $this->getDeliveryMethod();
        return $deliveryMethod ? $deliveryMethod->getEstimatedDays() : null;
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
     * Get priority/sort order
     * 
     * @return int
     */
    public function getPriority()
    {
        return isset($this->priority) ? (int)$this->priority : 999;
    }

    /**
     * Get total cost (delivery method base cost + additional cost)
     * 
     * @return float|null
     */
    public function getTotalCost()
    {
        $deliveryMethod = $this->getDeliveryMethod();
        $baseCost = $deliveryMethod ? $deliveryMethod->getCost() : null;
        $additionalCost = $this->getAdditionalCost();

        if ($baseCost === null && $additionalCost === null) {
            return null;
        }

        return ($baseCost ?? 0) + ($additionalCost ?? 0);
    }

    /**
     * Get formatted total cost
     * 
     * @param string $currencySymbol
     * @return string
     */
    public function getFormattedTotalCost($currencySymbol = '$')
    {
        $cost = $this->getTotalCost();

        if ($cost === null) {
            return 'N/A';
        }

        if ($cost == 0) {
            return 'Free';
        }

        return $currencySymbol . number_format($cost, 2);
    }

    /**
     * Validate service-delivery relationship
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->service_id)) {
            $errors[] = 'Service ID is required';
        }

        if (empty($this->delivery_method_id)) {
            $errors[] = 'Delivery method ID is required';
        }

        if (!empty($this->service_id) && !empty($this->delivery_method_id)) {
            $existing = static::findRelationship($this->service_id, $this->delivery_method_id);
            if ($existing && $existing->getID() !== $this->getID()) {
                $errors[] = 'This service-delivery method relationship already exists';
            }
        }

        return $errors;
    }
}
