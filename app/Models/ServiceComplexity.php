<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceComplexity extends Model
{
    protected $resource = 'srvc_complexities';

    protected $format = [];

    protected $cast = [];

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
        'updated_by',
    ];
}
