<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceDeliverable extends Model
{
    protected $resource = 'srvc_service_deliverables';

    protected $fillable = [
        'service_id',
        'deliverable_id',
        'is_optional',
        'sequence_order'
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

    /** ServiceDeliverable belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceDeliverable belongs to a Deliverable */
    public function deliverable()
    {
        return $this->belongsTo(Deliverable::class, 'deliverable_id');
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
