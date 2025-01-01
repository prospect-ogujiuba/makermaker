<?php
namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Status extends Model
{
    protected $resource = 'makermaker_statuses';

    // Remove primaryKey override as it should use 'id'
    // protected $primaryKey = 'code';

    protected $fillable = [
        'id',
        'code',
        'description'
    ];

    protected $rules = [
        'code' => 'required|string|max:20|unique',
        'description' => 'required|string|max:100'
    ];

}