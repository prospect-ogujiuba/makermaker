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

        // Get the first route arg (the ID)
        $id = $route_args[0] ?? null;

        $rules = [];

        // Correct format: unique:field:table@id_column:id_value
        $rules['name'] = "unique:name:{GLOBAL_WPDB_PREFIX}srvc_pricing_tiers@id:{$id}|required";
        $rules['code'] = "unique:code:{GLOBAL_WPDB_PREFIX}srvc_pricing_tiers@id:{$id}|required";
        $rules['sort_order'] = "numeric|?required";

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
