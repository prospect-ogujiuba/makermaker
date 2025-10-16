<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceBundleFields extends Fields
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

        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_service_bundles@id:{$id}|required|max:64";
        $rules['slug'] = "unique:slug:{$wpdb_prefix}srvc_service_bundles@id:{$id}|?required|max:64";
        $rules['short_desc'] = "max:512|?required";
        $rules['long_desc'] = "?required";
        $rules['bundle_type'] = "";
        $rules['total_discount_pct'] = "?numeric|callback:checkIntRange:0:100";
        $rules['is_active'] = "?required|callback:checkIntRange:0:1";
        $rules['valid_from'] = "?required";
        $rules['valid_to'] = "?required";

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
