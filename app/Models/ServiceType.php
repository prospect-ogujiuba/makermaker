<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceType extends Model
{
    protected $resource = 'srvc_service_types';

    protected $fillable = [
        'name',
        'code',
        'description',
        'requires_site_visit',
        'supports_remote',
        'estimated_duration_hours'
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
        'services'
    ];

    /** ServiceType has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'service_type_id');
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
