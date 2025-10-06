<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceDeliverable extends Model
{
    protected $resource = 'srvc_service_deliverables';

    protected $fillable = [
        'service_id',
        'deliverable_id',
        'is_optional',
        'sequence_order'
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
        'deliverable'
    ];

    /** ServiceDeliverable belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliverable belongs to a Deliverable */
    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class, 'deliverable_id');
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
     * Get all deliverables for a service
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
     * Get all services using a deliverable
     * 
     * @param int $deliverableId
     * @return \TypeRocket\Models\Results|static[]
     */
    public static function getByDeliverable($deliverableId)
    {
        if (empty($deliverableId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('deliverable_id', $deliverableId)->get();
    }

    /**
     * Find specific service-deliverable relationship
     * 
     * @param int $serviceId
     * @param int $deliverableId
     * @return static|null
     */
    public static function findRelationship($serviceId, $deliverableId)
    {
        if (empty($serviceId) || empty($deliverableId)) {
            return null;
        }

        return static::new()
            ->where('service_id', $serviceId)
            ->where('deliverable_id', $deliverableId)
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
     * Get the related Deliverable model
     * 
     * @return Deliverable|null
     */
    public function getDeliverable()
    {
        if (empty($this->deliverable_id)) {
            return null;
        }

        return Deliverable::new()->findById($this->deliverable_id);
    }

    /**
     * Check if this deliverable is required for the service
     * 
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->is_required) && (bool)$this->is_required;
    }

    /**
     * Check if this deliverable is optional for the service
     * 
     * @return bool
     */
    public function isOptional()
    {
        return !$this->isRequired();
    }

    /**
     * Get quantity for this deliverable
     * 
     * @return int
     */
    public function getQuantity()
    {
        return isset($this->quantity) ? (int)$this->quantity : 1;
    }

    /**
     * Get formatted quantity with unit
     * 
     * @return string
     */
    public function getFormattedQuantity()
    {
        $quantity = $this->getQuantity();
        $deliverable = $this->getDeliverable();
        $unit = $deliverable ? $deliverable->getUnitLabel() : 'item';

        return $quantity . ' ' . ($quantity === 1 ? $unit : $unit . 's');
    }

    /**
     * Get estimated delivery timeframe (in days)
     * 
     * @return int|null
     */
    public function getDeliveryDays()
    {
        return isset($this->delivery_days) ? (int)$this->delivery_days : null;
    }

    /**
     * Get formatted delivery timeframe
     * 
     * @return string
     */
    public function getFormattedDeliveryTime()
    {
        $days = $this->getDeliveryDays();

        if ($days === null) {
            return 'Not specified';
        }

        if ($days === 0) {
            return 'Same day';
        }

        if ($days === 1) {
            return '1 day';
        }

        return $days . ' days';
    }

    /**
     * Validate service-deliverable relationship
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->service_id)) {
            $errors[] = 'Service ID is required';
        }

        if (empty($this->deliverable_id)) {
            $errors[] = 'Deliverable ID is required';
        }

        if (!empty($this->service_id) && !empty($this->deliverable_id)) {
            $existing = static::findRelationship($this->service_id, $this->deliverable_id);
            if ($existing && $existing->getID() !== $this->getID()) {
                $errors[] = 'This service-deliverable relationship already exists';
            }
        }

        return $errors;
    }
}
