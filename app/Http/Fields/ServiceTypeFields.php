<?php

namespace MakerMaker\Http\Fields;

use TypeRocket\Http\Fields;
use TypeRocket\Http\Request;

class ServiceTypeFields extends Fields
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

        $rules['name'] = "unique:name:{$wpdb_prefix}srvc_service_types@id:{$id}|required|max:64";
        $rules['code'] = "unique:code:{$wpdb_prefix}srvc_service_types@id:{$id}|?required|max:64";
        $rules['description'] = "max:2000";
        $rules['requires_site_visit'] = "numeric|callback:checkIntRange:0:1";
        $rules['supports_remote'] = "numeric|callback:checkIntRange:0:1";
        $rules['estimated_duration_hours'] = "numeric|callback:checkIntRange:0:9999.99";

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
