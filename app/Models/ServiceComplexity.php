<?php

namespace MakerMaker\Models;

use App\Models\User;
use TypeRocket\Models\Model;

class ServiceComplexity extends Model
{
    protected $resource = 'srvc_complexities';

    protected $format = [];

    protected $cast = [];

    protected $fillable = [
        'name',
        'level',
        'price_multiplier',
        'created_by',
        'updated_by',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Get all services using this complexity level
    public function services()
    {
        return $this->hasMany(\MakerMaker\Models\Service::class, 'complexity_id');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
