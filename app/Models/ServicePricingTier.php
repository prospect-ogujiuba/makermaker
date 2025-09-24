<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServicePricingTier extends Model
{
    protected $resource = 'srvc_pricing_tiers';

    protected $fillable = [
        'name',
        'code',
        'sort_order'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    // Service prices using this pricing tier
    public function servicePrices()
    {
        return $this->hasMany(\MakerMaker\Models\ServicePrice::class, 'pricing_tier_id');
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
