<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class BundleItem extends Model
{
    protected $resource = 'srvc_bundle_items';

protected $fillable = [
        'bundle_id',
        'service_id',
        'quantity',
        'discount_pct',
        'is_optional',
        'sort_order'
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
        'bundle',
        'service'
    ];

    /** BundleItem belongs to a ServiceBundle */
    public function bundle()
    {
        return $this->belongsTo(ServiceBundle::class, 'bundle_id');
    }

    /** BundleItem belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
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
