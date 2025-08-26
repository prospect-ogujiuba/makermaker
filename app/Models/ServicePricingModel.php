<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServicePricingModel extends Model
{
    protected $resource = 'srvc_pricing_model';

    protected $format = [];

    protected $cast = [];

    protected $fillable = [
        'name',
        'code',
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
