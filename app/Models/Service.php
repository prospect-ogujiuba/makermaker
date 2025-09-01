<?php

namespace MakerMaker\Models;

use App\Models\User;
use TypeRocket\Models\Model;

class Service extends Model
{
    protected $resource = 'srvc_services';

    protected $fillable = [
        'sku',
        'slug',
        'name',
        'short_desc',
        'long_desc',
        'category_id',
        'service_type_id',
        'complexity_id',  // Add this for the relationship
        'is_active',
        'is_addon',
        'default_unit',
        'metadata'
    ];

    protected $guard = [
        'id',
        'version',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the complexity level for this service
     * 
     * Based on the FK relationship: srvc_services.complexity_id -> srvc_complexities.id
     */
    public function complexity()
    {
        return $this->belongsTo(\MakerMaker\Models\ServiceComplexity::class, 'complexity_id');
    }

    /**
     * Get the price multiplier from the complexity level
     */
    public function getPriceMultiplier()
    {
        return $this->complexity ? $this->complexity->price_multiplier : 1.0;
    }

    /**
     * Get the complexity level number
     */
    public function getComplexityLevel()
    {
        return $this->complexity ? $this->complexity->level : 1;
    }

    /**
     * Get the complexity name
     */
    public function getComplexityName()
    {
        return $this->complexity ? $this->complexity->name : 'Basic';
    }
}
