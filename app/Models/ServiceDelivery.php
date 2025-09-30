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
}
