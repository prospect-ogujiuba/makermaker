<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceDeliveryMethodAssignment extends Model
{
    protected $resource = 'srvc_service_delivery_method_assignments';

    protected $fillable = [
        'service_id',
        'delivery_method_id',
        'lead_time_days',
        'sla_hours',
        'surcharge'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    /** ServiceDeliveryMethodAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliveryMethodAssignment belongs to a ServiceDeliveryMethod */
    public function deliveryMethod()
    {
        return $this->belongsTo(ServiceDeliveryMethod::class, 'delivery_method_id');
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
