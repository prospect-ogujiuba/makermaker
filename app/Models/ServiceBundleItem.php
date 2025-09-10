<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceBundleItem extends Model
{
    protected $resource = 'srvc_bundle_items';

    protected $fillable = [
        'bundle_id',
        'service_id',
        'quantity',
        'discount_pct',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServiceBundleItem belongs to a ServiceBundle */
    public function bundle()
    {
        return $this->belongsTo(ServiceBundle::class, 'bundle_id');
    }

    /** ServiceBundleItem belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
