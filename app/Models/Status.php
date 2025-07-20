<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Status extends Model
{
    protected $resource = 'b2bcnc_statuses';

    protected $fillable = [
        'code',
        'description'
    ];

    protected $rules = [
        'code' => 'required|string|max:20|unique',
        'description' => 'required|string|max:100'
    ];
}
