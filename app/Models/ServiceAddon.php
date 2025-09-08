<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceAddon extends Model
{
    protected $resource = 'srvc_service_addons';

    protected $fillable = [
        'service_id',
        'addon_service_id',
        'required',
        'min_qty',
        'max_qty',
        'price_delta',
        'multiplier',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServiceAddon belongs to a Service (the main service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceAddon belongs to a Service (the addon service) */
    public function addonService()
    {
        return $this->belongsTo(Service::class, 'addon_service_id');
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
