<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ServicePrice extends Model
{
    protected $resource = 'srvc_service_prices';
    protected $fillable = [
        'service_id',
        'pricing_tier_id',
        'pricing_model_id',
        'current',
        'amount',
        'unit',
        'setup_fee',
        'notes',
        'effective_from',
        'effective_to',
        'created_by',
        'updated_by',
    ];

    protected $format = [
        'notes' => 'json_encode'
    ];
    protected $cast = [
        'notes' => 'array'
    ];

    protected $guard = [
        'id',
        'version',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /** ServicePrice belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServicePrice belongs to a ServicePricingTier */
    public function pricingTier()
    {
        return $this->belongsTo(ServicePricingTier::class, 'pricing_tier_id');
    }

    /** ServicePrice belongs to a ServicePricingModel */
    public function pricingModel()
    {
        return $this->belongsTo(ServicePricingModel::class, 'pricing_model_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }
}
