<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Service extends Model
{
    protected $resource = 'b2bcnc_services';

    protected $fillable = [
        'code',
        'name',
        'description',
        'base_price',
        'icon',
        'active',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
