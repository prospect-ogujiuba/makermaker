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

    // Service prices using this pricing tier
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_service_delivery_method', 'delivery_method_id', 'service_id');
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
