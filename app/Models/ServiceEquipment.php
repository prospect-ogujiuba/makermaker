<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceEquipment extends Model
{
    protected $resource = 'srvc_service_equipment';

    protected $fillable = [
        'service_id',
        'equipment_id',
        'required',
        'quantity',
        'quantity_unit',
        'cost_included'
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

    /** ServiceEquipment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceEquipment belongs to a Equipment */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
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
