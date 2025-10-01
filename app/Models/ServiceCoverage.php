<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceCoverage extends Model
{
    protected $resource = 'srvc_service_coverage';

    protected $fillable = [
        'service_id',
        'coverage_area_id',
        'delivery_surcharge',
        'lead_time_adjustment_days'
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
        'coverageArea',
    ];

    /** ServiceCoverage belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceCoverage belongs to a CoverageArea */
    public function coverageArea()
    {
        return $this->belongsTo(CoverageArea::class, 'coverage_area_id');
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
