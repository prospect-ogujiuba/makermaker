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
        'currency',
        'amount',
        'unit',
        'setup_fee',
        'valid_from',
        'valid_to',
        'is_current',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected $format = [
        'valid_to' => 'convertEmptyToNull',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $with = [
        'service',
        'pricingTier',
        'pricingModel'
    ];

    /** ServicePrice belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServicePrice belongs to a PricingTier */
    public function pricingTier()
    {
        return $this->belongsTo(PricingTier::class, 'pricing_tier_id');
    }

    /** ServicePrice belongs to a PricingModel */
    public function pricingModel()
    {
        return $this->belongsTo(PricingModel::class, 'pricing_model_id');
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
