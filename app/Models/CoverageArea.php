<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class CoverageArea extends Model
{
    protected $resource = 'srvc_coverage_areas';

    protected $fillable = [
        'name',
        'code',
        'country_code',
        'region_type',
        'timezone',
        'postal_code_pattern'
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
        'serviceCoverages.service'
    ];


    public function serviceCoverages()
    {
        return $this->hasMany(ServiceCoverage::class, 'coverage_area_id');
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
