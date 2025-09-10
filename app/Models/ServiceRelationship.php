<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceRelationship extends Model
{
    protected $resource = 'srvc_service_relationships';


    protected $fillable = [
        'service_id',
        'related_service_id',
        'relation_type',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServiceRelationship belongs to a Service (the primary service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceRelationship belongs to a Service (the related service) */
    public function relatedService()
    {
        return $this->belongsTo(Service::class, 'related_service_id');
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
