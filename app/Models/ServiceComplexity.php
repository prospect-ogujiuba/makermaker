<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServiceComplexity extends Model
{
    protected $resource = 'srvc_complexities';

    protected $fillable = [
        'name',
        'level',
        'price_multiplier'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    // Get all services using this complexity level
    public function services()
    {
        return $this->hasMany(Service::class, 'complexity_id');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }
}
