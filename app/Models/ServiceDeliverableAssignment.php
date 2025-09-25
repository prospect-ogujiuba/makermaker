<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceDeliverableAssignment extends Model
{
    protected $resource = 'srvc_service_deliverable_assignments';

    protected $fillable = [
        'service_id',
        'deliverable_id'
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
        'deliverable'
    ];

    /** ServiceDeliverableAssignment belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliverableAssignment belongs to a ServiceDeliverable */
    public function deliverable()
    {
        return $this->belongsTo(ServiceDeliverable::class, 'deliverable_id');
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
