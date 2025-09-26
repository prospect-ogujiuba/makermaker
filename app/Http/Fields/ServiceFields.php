<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceFields extends Fields
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

        $rules['sku'] = "unique:sku:{$wpdb_prefix}srvc_services@id:{$id}|?required|max:64";
        $rules['slug'] = "unique:slug:{$wpdb_prefix}srvc_services@id:{$id}|?required|max:64";
        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_services@id:{$id}|required|max:64";
        $rules['short_desc'] = 'required|max:512';
        $rules['long_desc'] = '?required';
        $rules['category_id'] = 'required|numeric';
        $rules['service_type_id'] = 'required|numeric';
        $rules['complexity_id'] = 'required|numeric';
        $rules['is_active'] = '?numeric|callback:checkIntRange:0:1';
        $rules['is_featured'] = "?numeric|callback:checkIntRange:0:1";
        $rules['minimum_quantity'] = "?numeric";
        $rules['maximum_quantity'] = "?numeric|callback:checkIntRange:1:999999.99";
        $rules['estimated_hours'] = "?numeric|callback:checkIntRange:0:99999.99";
        $rules['metadata'] = '?required';

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
