<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceDeliveryMethod extends Model
{
    protected $resource = 'srvc_delivery_methods';

    protected $fillable = [
        'name',
        'code',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Service prices using this pricing tier
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_delivery_method_assignments', 'delivery_method_id', 'service_id');
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
}