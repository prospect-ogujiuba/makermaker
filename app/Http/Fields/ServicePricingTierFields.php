<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServicePricingTierFields extends Fields
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

        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_pricing_tiers@id:{$id}|required|max:64";
        $rules['code'] = "unique:code:{$wpdb_prefix}srvc_pricing_tiers@id:{$id}|?required|max:64";
        $rules['sort_order'] = "?numeric|?required|min:1|max:3|callback:checkIntRange:0:255";

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
