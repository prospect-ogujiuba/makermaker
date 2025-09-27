<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServicePriceFields extends Fields
{
    /**
     * Run On Import
     *
     * Validate and then redirect on failure with errors, immediately
     * when imported by the application container resolver.
     *
     * @var bool
     */
    protected $run = true;

    /**
     * Model Fillable Property Override
     *
     * @return array
     */
    protected function fillable()
    {
        return [];
    }

    /**
     * Validation Rules
     *
     * @return array
     */
    protected function rules()
    {
        $request = Request::new();
        $route_args = $request->getDataGet('route_args');
        $id = $route_args[0] ?? null;
        $wpdb_prefix = GLOBAL_WPDB_PREFIX;

        $rules = [];

        $rules['service_id'] = 'required|numeric';
        $rules['pricing_tier_id'] = 'required|numeric';
        $rules['pricing_model_id'] = 'required|numeric';
        $rules['currency'] = '?required|max:3';
        $rules['amount'] = 'numeric';
        $rules['unit'] = 'max:32';
        $rules['setup_fee'] = '?numeric';
        $rules['valid_from'] = 'required';
        $rules['valid_to'] = '';
        $rules['is_current'] = 'numeric|callback:checkIntRange:0:1';
        $rules['approval_status'] = 'required';
        $rules['approved_by'] = '?numeric';
        $rules['approved_at'] = '';

        return $rules;
    }

    /**
     * Custom Error Messages
     *
     * @return array
     */
    protected function messages()
    {
        return [];
    }
}
