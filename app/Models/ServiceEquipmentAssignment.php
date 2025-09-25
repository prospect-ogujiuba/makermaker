<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceEquipmentAssignment extends Model
{
    protected $resource = 'srvc_service_equipment_assignments';

    protected $fillable = [
        'service_id',
        'equipment_id',
        'required',
        'quantity',
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
        'equipment'
    ];

    /** ServiceEquipmentAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceEquipmentAssignment belongs to a ServiceEquipment */
    public function equipment()
    {
        return $this->belongsTo(ServiceEquipment::class, 'equipment_id');
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
}
