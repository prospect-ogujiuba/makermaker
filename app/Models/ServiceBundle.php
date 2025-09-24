<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceBundle extends Model
{
    protected $resource = 'srvc_bundles';

    protected $fillable = [
        'name',
        'slug',
        'short_desc',
        'is_active'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    /** ServiceBundle belongs to many Services */
    public function services()
    {
        return $this->belongsToMany(Service::class, GLOBAL_WPDB_PREFIX . 'srvc_bundle_items', 'bundle_id', 'service_id');
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
